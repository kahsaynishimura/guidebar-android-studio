package br.com.guidebar.activities;

import android.app.Activity;

public class UploadToServerEventActivity extends Activity {
//	private static final int REQUEST_CHOOSER = 1;
//	private Evento evento = new Evento();
//	private Button btnDeleteImage;
//
//	private View mUploadEventFormView;
//	private View mUploadEventStatusView;
//	private TextView mUploadEventStatusMessageView;
//
//	private UploadImageEventTask mUploadImageEventTask = null;
//	// private UpdateImageTask mUpdateImageTask;
//
//	private RemoveImageTask mRemoveImageTask;
//	String path = "";
//
//	@Override
//	protected void onCreate(Bundle savedInstanceState) {
//		super.onCreate(savedInstanceState);
//		setContentView(R.layout.activity_upload_to_server_event);
//		loadComponents();
//	}
//
//	private void loadComponents() {
//		// txtUpload = (TextView) findViewById(R.id.txtUpload);
//		btnDeleteImage = (Button) findViewById(R.id.btnDeleteImage);
//		Bundle parametros = getIntent().getExtras();
//		evento.setId(parametros.getInt("id_evento"));
//		mUploadEventFormView = findViewById(R.id.upload_event_form);
//		mUploadEventStatusView = findViewById(R.id.status);
//		mUploadEventStatusMessageView = (TextView) findViewById(R.id.status_message);
//		Typeface tf = Typeface.createFromAsset(getAssets(),
//				"fonts/Gotham-Book.otf");
//		mUploadEventStatusMessageView.setTypeface(tf);
//		((Button) findViewById(R.id.btnAddImage)).setTypeface(tf);
//		((Button) findViewById(R.id.btnNext)).setTypeface(tf);
//		btnDeleteImage.setTypeface(tf);
//		final ImageView mFolderView = (ImageView) findViewById(R.id.imgCamera);
//
//		evento.setThumbnail(Guidebar.serverUrl + "uploads/evento/"
//				+ evento.getId() + ".jpg");
//		if (mFolderView != null) {
//			new ImageDownloaderTask(mFolderView).execute(evento.getThumbnail());
//		}
//	}
//
//	public class UploadImageEventTask extends AsyncTask<Void, Void, Boolean> {
//		@Override
//		protected Boolean doInBackground(Void... params) {
//			String resposta = "";
//			File sourceFile = new File(path);
//			if (!sourceFile.isFile()) {
//				return null;
//			} else {
//				try {
//					Bitmap bm = BitmapFactory.decodeFile(path);
//					ByteArrayOutputStream bos = new ByteArrayOutputStream();
//					bm.compress(CompressFormat.JPEG, 100, bos);
//					byte[] data = bos.toByteArray();
//					ByteArrayBody bab = new ByteArrayBody(data, evento.getId()
//							+ ".jpg");
//					String urlPost = Guidebar.serverUrl
//							+ "uploadToServerEvento.php";
//					MultipartEntity entity = new MultipartEntity(
//							HttpMultipartMode.BROWSER_COMPATIBLE);
//					entity.addPart("id_evento", new StringBody(evento.getId()
//							.toString()));
//					entity.addPart("uploaded_file", bab);
//					// entity.addPart("myAudioFile", new FileBody(audioFile));
//					String respostaRetornada = "";
//					respostaRetornada = ConexaoHttpClient
//							.executaHttpPostMultipart(urlPost, entity);
//					resposta = respostaRetornada.toString();
//					resposta = resposta.replaceAll("\\s+", "");
//
//				} catch (Exception erro) {
//					Toast.makeText(UploadToServerEventActivity.this,
//							"Erro: " + erro, Toast.LENGTH_SHORT).show();
//				}
//			} // End else block
//			return Integer.parseInt(resposta) == 1 ? true : false;
//		}
//
//		@Override
//		protected void onPostExecute(final Boolean success) {
//			mUploadImageEventTask = null;
//			showProgress(false);
//			if (success) {
//				runOnUiThread(new Runnable() {
//					public void run() {
//						Toast.makeText(UploadToServerEventActivity.this,
//								getString(R.string.success_upload_image),
//								Toast.LENGTH_SHORT).show();
//					}
//				});
//
//				final ImageView mFolderView = (ImageView) findViewById(R.id.imgCamera);
//
//				evento.setThumbnail(Guidebar.serverUrl + "uploads/evento/"
//						+ evento.getId() + ".jpg");
//				if (mFolderView != null) {
//					new ImageDownloaderTask(mFolderView).execute(evento
//							.getThumbnail());
//				}
//			} else {
//				Toast.makeText(UploadToServerEventActivity.this,
//						R.string.error_invalid_user, Toast.LENGTH_SHORT).show();
//			}
//		}
//
//		@Override
//		protected void onCancelled() {
//			mUploadImageEventTask = null;
//			showProgress(false);
//		}
//	}
//
//	public class UpdateImageTask extends AsyncTask<Void, Void, Void> {
//
//		@Override
//		protected Void doInBackground(Void... params) {
//
//			String urlPost = "https://graph.facebook.com";
//			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
//			parametrosPost
//					.add(new BasicNameValuePair("id", "1407661312804479"));
//			parametrosPost.add(new BasicNameValuePair("scrape", "true"));
//			try {
//				ConexaoHttpClient.executaHttpPost(urlPost, parametrosPost);
//			} catch (Exception e) {
//				e.printStackTrace();
//			}
//			return null;
//		}
//
//		@Override
//		protected void onCancelled() {
//			showProgress(false);
//			// mUpdateImageTask = null;
//		}
//
//		@Override
//		protected void onPostExecute(Void result) {
//
//			// mUpdateImageTask = null;
//			showProgress(false);
//			// Intent i = new Intent(UploadToServerEventActivity.this,
//			// AddImageActivity.class);
//			// i.putExtra("id_evento", evento.getId());
//			// startActivity(i);
//			// finish();
//		}
//
//	}
//
//	public void removeImage(View v) {
//		AlertDialog.Builder confirmDeleteImage = new AlertDialog.Builder(
//				UploadToServerEventActivity.this);
//		confirmDeleteImage.setTitle(getString(R.string.confirm_delete_image));
//		confirmDeleteImage
//				.setMessage(getString(R.string.confirm_delete_image_msg));
//		confirmDeleteImage.setPositiveButton(getString(R.string.yes),
//				new DialogInterface.OnClickListener() {
//
//					@Override
//					public void onClick(DialogInterface dialog, int which) {
//						if (mRemoveImageTask == null) {
//							mUploadEventStatusMessageView
//									.setText(R.string.progress_saving_data);
//							showProgress(true);
//							mRemoveImageTask = new RemoveImageTask();
//							mRemoveImageTask.execute();
//						}
//					}
//
//				});
//
//		confirmDeleteImage.setNegativeButton(getString(R.string.no),
//				new DialogInterface.OnClickListener() {
//
//					@Override
//					public void onClick(DialogInterface dialog, int which) {
//						// dialog.cancel();
//					}
//				});
//		confirmDeleteImage.show();
//
//	}
//
//	public class RemoveImageTask extends AsyncTask<Void, Void, Boolean> {
//		@Override
//		protected Boolean doInBackground(Void... arg0) {
//			String urlPost = Guidebar.serverUrl + "removerFolder.php";
//			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
//			parametrosPost.add(new BasicNameValuePair("id", evento.getId()
//					.toString()));
//			String respostaRetornada = "";
//			String resposta = "";
//			try {
//				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
//						parametrosPost);
//				resposta = respostaRetornada.toString();
//				resposta = resposta.replaceAll("\\s+", "");
//			} catch (Exception erro) {
//				Log.i("erro", "erro = " + erro);
//			}
//			return resposta.equals("1");
//		}
//
//		@Override
//		protected void onPostExecute(final Boolean success) {
//			mRemoveImageTask = null;
//			showProgress(false);
//			if (success) {
//				Toast.makeText(UploadToServerEventActivity.this,
//						getString(R.string.success_remove_image),
//						Toast.LENGTH_SHORT).show();
//				((ImageView) findViewById(R.id.imgCamera))
//						.setImageResource(R.drawable.camera);
//			} else {
//				Toast.makeText(UploadToServerEventActivity.this,
//						getString(R.string.error_no_image), Toast.LENGTH_SHORT)
//						.show();
//			}
//		}
//
//		@Override
//		protected void onCancelled() {
//			mRemoveImageTask = null;
//			showProgress(false);
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
//	public void next(View v) {
//		Intent i = new Intent(UploadToServerEventActivity.this,
//				AddImageActivity.class);
//		i.putExtra("id_evento", evento.getId());
//		startActivity(i);
//		finish();
//	}
//
//	@Override
//	public boolean onOptionsItemSelected(MenuItem item) {
//		switch (item.getItemId()) {
//		case android.R.id.home:
//			// This ID represents the Home or Up button. In the case of this
//			// activity, the Up button is shown. Use NavUtils to allow users
//			// to navigate up one level in the application structure. For
//			// more details, see the Navigation pattern on Android Design:
//			//
//			// http://developer.android.com/design/patterns/navigation.html#up-vs-back
//			//
//			NavUtils.navigateUpFromSameTask(this);
//			return true;
//		}
//		return super.onOptionsItemSelected(item);
//	}
//
//	@Override
//	public void onActivityResult(int requestCode, int resultCode, Intent data) {
//		if (resultCode == RESULT_OK) {
//			if (requestCode == REQUEST_CHOOSER) {
//				Uri selectedImage = data.getData();
//				getPath(selectedImage);
//				// txtUpload.setText(path);
//				ConnectionDetector cd = new ConnectionDetector(
//						getApplicationContext());
//				Boolean isInternetPresent = cd.isConnectingToInternet();
//				if (isInternetPresent) {
//					mUploadEventStatusMessageView
//							.setText(R.string.progress_loading_data);
//					showProgress(true);
//					mUploadImageEventTask = new UploadImageEventTask();
//					mUploadImageEventTask.execute();
//				} else {
//					Toast.makeText(UploadToServerEventActivity.this,
//							R.string.error_network_connection,
//							Toast.LENGTH_SHORT).show();
//				}
//			}
//		}
//	}
//
//	@TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
//	private void showProgress(final boolean show) {
//		// On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
//		// for very easy animations. If available, use these APIs to fade-in
//		// the progress spinner.
//		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
//			int shortAnimTime = getResources().getInteger(
//					android.R.integer.config_shortAnimTime);
//
//			mUploadEventStatusView.setVisibility(View.VISIBLE);
//			mUploadEventStatusView.animate().setDuration(shortAnimTime)
//					.alpha(show ? 1 : 0)
//					.setListener(new AnimatorListenerAdapter() {
//						@Override
//						public void onAnimationEnd(Animator animation) {
//							mUploadEventStatusView
//									.setVisibility(show ? View.VISIBLE
//											: View.GONE);
//						}
//					});
//
//			mUploadEventFormView.setVisibility(View.VISIBLE);
//			mUploadEventFormView.animate().setDuration(shortAnimTime)
//					.alpha(show ? 0 : 1)
//					.setListener(new AnimatorListenerAdapter() {
//						@Override
//						public void onAnimationEnd(Animator animation) {
//							mUploadEventFormView.setVisibility(show ? View.GONE
//									: View.VISIBLE);
//						}
//					});
//		} else {
//			// The ViewPropertyAnimator APIs are not available, so simply show
//			// and hide the relevant UI components.
//			mUploadEventStatusView.setVisibility(show ? View.VISIBLE
//					: View.GONE);
//			mUploadEventFormView.setVisibility(show ? View.GONE : View.VISIBLE);
//		}
//	}
}
