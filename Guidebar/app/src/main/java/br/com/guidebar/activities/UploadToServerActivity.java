package br.com.guidebar.activities;

import android.app.Activity;

public class UploadToServerActivity extends Activity {
//	private static final int REQUEST_CHOOSER = 1;
//	private Usuario usuario = new Usuario();
//
//	private UploadImageTask mUploadImageTask = null;
//	String path = "";
//
//	private View mUploadUserFormView;
//	private View mUploadUserStatusView;
//	private TextView mUploadUserStatusMessageView;
//	private Button btnAddImage;
//
//	@Override
//	protected void onCreate(Bundle savedInstanceState) {
//		super.onCreate(savedInstanceState);
//		setContentView(R.layout.activity_upload_to_server);
//		loadComponents();
//	}
//
//	private void loadComponents() {
//		Bundle parametros = getIntent().getExtras();
//		usuario.setId(parametros.getInt("id_usuario"));
//		Typeface tf = Typeface.createFromAsset(getAssets(),
//				"fonts/Gotham-Book.otf");
//		TextView tv = (TextView) findViewById(R.id.txtUpload);
//		btnAddImage = (Button) findViewById(R.id.btnAddImage);
//		tv.setTypeface(tf);
//		btnAddImage.setTypeface(tf);
//		mUploadUserFormView = findViewById(R.id.upload_user_form);
//		mUploadUserStatusView = findViewById(R.id.status);
//		mUploadUserStatusMessageView = (TextView) findViewById(R.id.status_message);
//	}
//
//	@Override
//	public void onActivityResult(int requestCode, int resultCode, Intent data) {
//
//		if (resultCode == RESULT_OK) {
//			if (requestCode == REQUEST_CHOOSER) {
//				Uri selectedImage = data.getData();
//				getPath(selectedImage);
//				ConnectionDetector cd = new ConnectionDetector(
//						getApplicationContext());
//				Boolean isInternetPresent = cd.isConnectingToInternet();
//				if (isInternetPresent) {
//					mUploadUserStatusMessageView
//							.setText(R.string.progress_saving_data);
//					showProgress(true);
//					mUploadImageTask = new UploadImageTask();
//					mUploadImageTask.execute();
//				} else {
//					Toast.makeText(UploadToServerActivity.this,
//							R.string.error_network_connection,
//							Toast.LENGTH_SHORT).show();
//				}
//			}
//		}
//	}
//
//	public void openChooser(View v) {
//		Intent intent = new Intent(Intent.ACTION_PICK,
//				android.provider.MediaStore.Images.Media.EXTERNAL_CONTENT_URI);
//		intent.setType("image/*");
//		startActivityForResult(intent, REQUEST_CHOOSER);
//	}
//
//	public void getPath(Uri uri) {
//
//		String[] filePathColumn = { MediaStore.Images.Media.DATA };
//
//		Cursor cursor = getContentResolver().query(uri, filePathColumn, null,
//				null, null);
//		cursor.moveToFirst();
//
//		int columnIndex = cursor.getColumnIndex(filePathColumn[0]);
//		path = cursor.getString(columnIndex);
//		cursor.close();
//	}
//
//	public class UploadImageTask extends AsyncTask<Void, Void, Boolean> {
//		@Override
//		protected Boolean doInBackground(Void... params) {
//			String resposta = "";
//			File sourceFile = new File(path);
//			if (!sourceFile.isFile()) {
//				return null;
//			} else {
//				Bitmap bm = BitmapFactory.decodeFile(path);
//				ByteArrayOutputStream bos = new ByteArrayOutputStream();
//				bm.compress(CompressFormat.JPEG, 75, bos);
//				byte[] data = bos.toByteArray();
//				ByteArrayBody bab = new ByteArrayBody(data, usuario.getId()
//						+ ".jpg");
//				try {
//					String urlPost = Guidebar.serverUrl + "uploadToServer.php";
//					MultipartEntity entity = new MultipartEntity(
//							HttpMultipartMode.BROWSER_COMPATIBLE);
//					entity.addPart("id_usuario", new StringBody(usuario.getId()
//							.toString()));
//					entity.addPart("uploaded_file", bab);
//					// entity.addPart("myAudioFile", new FileBody(audioFile));
//					String respostaRetornada = "";
//					respostaRetornada = ConexaoHttpClient
//							.executaHttpPostMultipart(urlPost, entity);
//					resposta = respostaRetornada.toString();
//					resposta = resposta.replaceAll("\\s+", "");
//				} catch (Exception erro) {
//					Toast.makeText(UploadToServerActivity.this,
//							"Erro: " + erro, Toast.LENGTH_SHORT).show();
//				}
//			} // End else block
//			return Integer.parseInt(resposta) == 1 ? true : false;
//		}
//
//		@Override
//		protected void onPostExecute(final Boolean success) {
//			mUploadImageTask = null;
//			showProgress(false);
//
//			if (success) {
//
//				runOnUiThread(new Runnable() {
//					public void run() {
//						Toast.makeText(UploadToServerActivity.this,
//								getString(R.string.success_upload_image),
//								Toast.LENGTH_SHORT).show();
//					}
//				});
//				finish();
//			} else {
//				// TODO: mensagem
//				Toast.makeText(UploadToServerActivity.this,
//						R.string.error_invalid_user, Toast.LENGTH_SHORT).show();
//			}
//		}
//
//		@Override
//		protected void onCancelled() {
//			mUploadImageTask = null;
//			showProgress(false);
//		}
//	}
//
//	/**
//	 * Shows the progress UI and hides the login form.
//	 */
//	@TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
//	private void showProgress(final boolean show) {
//		// On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
//		// for very easy animations. If available, use these APIs to fade-in
//		// the progress spinner.
//		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
//			int shortAnimTime = getResources().getInteger(
//					android.R.integer.config_shortAnimTime);
//
//			mUploadUserStatusView.setVisibility(View.VISIBLE);
//			mUploadUserStatusView.animate().setDuration(shortAnimTime)
//					.alpha(show ? 1 : 0)
//					.setListener(new AnimatorListenerAdapter() {
//						@Override
//						public void onAnimationEnd(Animator animation) {
//							mUploadUserStatusView
//									.setVisibility(show ? View.VISIBLE
//											: View.GONE);
//						}
//					});
//
//			mUploadUserFormView.setVisibility(View.VISIBLE);
//			mUploadUserFormView.animate().setDuration(shortAnimTime)
//					.alpha(show ? 0 : 1)
//					.setListener(new AnimatorListenerAdapter() {
//						@Override
//						public void onAnimationEnd(Animator animation) {
//							mUploadUserFormView.setVisibility(show ? View.GONE
//									: View.VISIBLE);
//						}
//					});
//		} else {
//			// The ViewPropertyAnimator APIs are not available, so simply show
//			// and hide the relevant UI components.
//			mUploadUserStatusView
//					.setVisibility(show ? View.VISIBLE : View.GONE);
//			mUploadUserFormView.setVisibility(show ? View.GONE : View.VISIBLE);
//		}
//	}

}
