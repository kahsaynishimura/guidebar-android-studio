package br.com.guidebar.activities;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.entity.mime.HttpMultipartMode;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.entity.mime.content.ByteArrayBody;
import org.apache.http.entity.mime.content.StringBody;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.Bitmap.CompressFormat;
import android.graphics.BitmapFactory;
import android.graphics.Typeface;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.provider.MediaStore;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.GridView;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.bean.Evento;
import br.com.guidebar.bean.Imagem;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.ConnectionDetector;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.customviews.ImageAdapter;

public class AddImageActivity extends Activity {
	private Evento evento = new Evento();
	private LoadImagesTask mLoadImagesTask;
	private View mAddImageFormView;
	private View mAddImageStatusView;
	private TextView mAddImageStatusMessageView;
	private UploadTask mUploadTask;
	private RemoveImageTask mRemoveImageTask;
	private Button btnAddImage;
	String path = "";
	private static final int REQUEST_CHOOSER = 1;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_add_image);
		loadComponents();

	}

	public void concluir(View v) {
		finish();
	}

	private void loadComponents() {

		Bundle parametros = getIntent().getExtras();
		evento.setId(parametros.getInt("id_evento"));
		mAddImageFormView = findViewById(R.id.add_images_form);
		mAddImageStatusView = findViewById(R.id.status);
		mAddImageStatusMessageView = (TextView) findViewById(R.id.status_message);

		btnAddImage = (Button) findViewById(R.id.btnAddImage);
		Button btnConcluir = (Button) findViewById(R.id.btnConcluir);
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		btnAddImage.setTypeface(tf);
		btnConcluir.setTypeface(tf);
		mAddImageStatusMessageView.setTypeface(tf);
		mAddImageStatusMessageView
				.setText(getText(R.string.progress_loading_data));
		loadImages();
	}

	private void loadImages() {
		btnAddImage.setEnabled(false);
		if (mLoadImagesTask == null) {
			mAddImageStatusMessageView
					.setText(getText(R.string.progress_loading_data));
			showProgress(true);

			mLoadImagesTask = new LoadImagesTask();
			mLoadImagesTask.execute();
		}
	}

	public void openChooser(View v) {
		Intent intent = new Intent(Intent.ACTION_PICK,
				android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
		intent.setType("image/*");
		startActivityForResult(intent, REQUEST_CHOOSER);
	}

	public void getPath(Uri uri) {
		String[] filePathColumn = { MediaStore.Images.Media.DATA };
		Cursor cursor = getContentResolver().query(uri, filePathColumn, null,
				null, null);
		cursor.moveToFirst();
		int columnIndex = cursor.getColumnIndex(filePathColumn[0]);
		path = cursor.getString(columnIndex);
		cursor.close();
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		if (resultCode == RESULT_OK) {
			if (requestCode == REQUEST_CHOOSER) {
				Uri selectedImage = data.getData();
				getPath(selectedImage);
				ConnectionDetector cd = new ConnectionDetector(
						getApplicationContext());
				Boolean isInternetPresent = cd.isConnectingToInternet();
				if (isInternetPresent) {
					mAddImageStatusMessageView
							.setText(R.string.progress_saving_data);
					showProgress(true);
					mUploadTask = new UploadTask();
					mUploadTask.execute();
				} else {
					Toast.makeText(AddImageActivity.this,
							R.string.error_network_connection,
							Toast.LENGTH_SHORT).show();
				}
			}
		}
		loadImages();
	}

	private Integer idImagemExcluir = 0;

	private void removeImage() {
		if (mRemoveImageTask == null) {
			mAddImageStatusMessageView.setText(R.string.progress_saving_data);
			showProgress(true);
			mRemoveImageTask = new RemoveImageTask();
			mRemoveImageTask.execute();
		}
	}

	public class RemoveImageTask extends AsyncTask<Void, Void, Boolean> {
		@Override
		protected Boolean doInBackground(Void... arg0) {
			String urlPost = Guidebar.serverUrl + "removerImagemEvento.php";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("id", idImagemExcluir
					.toString()));
			String respostaRetornada = "";
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				resposta = resposta.replaceAll("\\s+", "");
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}
			return resposta.equals("1");
		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mRemoveImageTask = null;
			showProgress(false);
			if (success) {
				Toast.makeText(AddImageActivity.this,
						getString(R.string.success_remove_image),
						Toast.LENGTH_SHORT).show();
				loadImages();
			} else {
				Toast.makeText(AddImageActivity.this,
						getString(R.string.error_remove_image),
						Toast.LENGTH_SHORT).show();
			}
		}

		@Override
		protected void onCancelled() {
			mRemoveImageTask = null;
			showProgress(false);
		}
	}

	public class LoadImagesTask extends
			AsyncTask<Void, Void, ArrayList<Imagem>> {

		// JSON Node names
		private static final String TAG_ID = "id";
		private static final String TAG_CAMINHO = "caminho";
		private static final String TAG_IMAGENS = "imagens";
		JSONArray imagens = null;

		@Override
		protected ArrayList<Imagem> doInBackground(Void... arg0) {
			ArrayList<Imagem> listaImagens = new ArrayList<Imagem>();
			String urlPost = Guidebar.serverUrl + "buscarImagensEvento.php";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("id_evento", evento
					.getId().toString()));
			String respostaRetornada = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				String resposta = respostaRetornada.toString();

				JSONObject json = new JSONObject(resposta);
				try {
					imagens = json.getJSONArray(TAG_IMAGENS);
					for (int i = 0; i < imagens.length(); i++) {

						Imagem obj = new Imagem();
						JSONObject c = imagens.getJSONObject(i);
						obj.setId(Integer.parseInt(c.getString(TAG_ID)));
						obj.setCaminho(c.getString(TAG_CAMINHO));
						listaImagens.add(obj);
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}

			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}

			return listaImagens;
		}

		@Override
		protected void onPostExecute(final ArrayList<Imagem> listaImagens) {
			mLoadImagesTask = null;
			showProgress(false);

			GridView gridview = (GridView) findViewById(R.id.gridView);
			gridview.setAdapter(new ImageAdapter(AddImageActivity.this,
					listaImagens));

			gridview.setOnItemClickListener(new AdapterView.OnItemClickListener() {
				public void onItemClick(AdapterView<?> parent, View v,
						int position, long id) {
					Imagem i = listaImagens.get(position);

					idImagemExcluir = i.getId();

					AlertDialog.Builder confirmDeleteImage = new AlertDialog.Builder(
							AddImageActivity.this);
					confirmDeleteImage
							.setTitle(getString(R.string.confirm_delete_image));
					confirmDeleteImage
							.setMessage(getString(R.string.confirm_delete_image_msg));
					confirmDeleteImage.setPositiveButton(
							getString(R.string.yes),
							new DialogInterface.OnClickListener() {

								@Override
								public void onClick(DialogInterface dialog,
										int which) {
									removeImage();
								}

							});

					confirmDeleteImage.setNegativeButton(
							getString(R.string.no),
							new DialogInterface.OnClickListener() {

								@Override
								public void onClick(DialogInterface dialog,
										int which) {
									// dialog.cancel();
								}
							});
					confirmDeleteImage.show();

				}
			});
			btnAddImage.setEnabled(true);
		}

		@Override
		protected void onCancelled() {
			mLoadImagesTask = null;
			showProgress(false);
			btnAddImage.setEnabled(true);
		}
	}

	public class UploadTask extends AsyncTask<Void, Void, Boolean> {
		@Override
		protected Boolean doInBackground(Void... params) {
			String resposta = "";
			File sourceFile = new File(path);
			if (!sourceFile.isFile()) {
				return null;
			} else {
				Bitmap bm = BitmapFactory.decodeFile(path);
				ByteArrayOutputStream bos = new ByteArrayOutputStream();
				bm.compress(CompressFormat.JPEG, 75, bos);
				byte[] data = bos.toByteArray();
				ByteArrayBody bab = new ByteArrayBody(data, evento.getId()
						+ ".jpg");
				try {
					String urlPost = Guidebar.serverUrl
							+ "uploadImagemEvento.php";
					MultipartEntity entity = new MultipartEntity(
							HttpMultipartMode.BROWSER_COMPATIBLE);
					entity.addPart("id_evento", new StringBody(evento.getId()
							.toString()));
					entity.addPart("uploaded_file", bab);
					// entity.addPart("myAudioFile", new FileBody(audioFile));
					String respostaRetornada = "";
					respostaRetornada = ConexaoHttpClient
							.executaHttpPostMultipart(urlPost, entity);
					resposta = respostaRetornada.toString();
					resposta = resposta.replaceAll("\\s+", "");
				} catch (Exception erro) {
					Toast.makeText(AddImageActivity.this, "Erro: " + erro,
							Toast.LENGTH_SHORT).show();
				}
			} // End else block
			return Integer.parseInt(resposta) == 1 ? true : false;
		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mUploadTask = null;
			showProgress(false);
			if (success) {
				runOnUiThread(new Runnable() {
					public void run() {
						Toast.makeText(AddImageActivity.this,
								getString(R.string.success_upload_image),
								Toast.LENGTH_SHORT).show();
					}
				});
			} else {
				Toast.makeText(AddImageActivity.this,
						R.string.error_upload_image, Toast.LENGTH_SHORT).show();
			}
		}

		@Override
		protected void onCancelled() {
			mUploadTask = null;
			showProgress(false);
		}
	}

	@TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
	private void showProgress(final boolean show) {
		// On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
		// for very easy animations. If available, use these APIs to fade-in
		// the progress spinner.
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
			int shortAnimTime = getResources().getInteger(
					android.R.integer.config_shortAnimTime);

			mAddImageStatusView.setVisibility(View.VISIBLE);
			mAddImageStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mAddImageStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mAddImageFormView.setVisibility(View.VISIBLE);
			mAddImageFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mAddImageFormView.setVisibility(show ? View.GONE
									: View.VISIBLE);
						}
					});
		} else {
			mAddImageStatusView.setVisibility(show ? View.VISIBLE : View.GONE);
			mAddImageFormView.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}
}
