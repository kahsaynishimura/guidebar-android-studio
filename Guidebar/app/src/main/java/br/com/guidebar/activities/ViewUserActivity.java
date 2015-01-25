package br.com.guidebar.activities;

import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import com.google.gson.Gson;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Intent;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.text.Html;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.bean.Usuario;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.ConnectionDetector;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.ImageDownloaderTask;
import br.com.guidebar.classes.SessionManager;

public class ViewUserActivity extends Activity {

	private SessionManager session;
	private Usuario usuario = new Usuario();
	private TextView mNameView;
	private TextView mEmailView;
	private TextView mAgeView;
	private ImageView mProfileImage;
	private Button btnEditUser;
	private ViewUserTask mViewUserTask;
	private View mViewUserFormView;
	private View mViewUserStatusView;
	private TextView mViewUserStatusMessageView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_view_user);
		session = new SessionManager(getApplicationContext());
		session.checkLogin();
		loadComponents();
	}

	private void loadComponents() {

		mNameView = (TextView) findViewById(R.id.txtName);
		mEmailView = (TextView) findViewById(R.id.txtEmail);
		mAgeView = (TextView) findViewById(R.id.txtAge);
		btnEditUser = (Button) findViewById(R.id.btnEditUser);
		mViewUserFormView = findViewById(R.id.view_user_form);
		mViewUserStatusView = findViewById(R.id.status);
		mViewUserStatusMessageView = (TextView) findViewById(R.id.status_message);
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		mViewUserStatusMessageView.setTypeface(tf);
		mEmailView.setTypeface(tf);
		mAgeView.setTypeface(tf);
		btnEditUser.setTypeface(tf);
		TextView txtUserProfile = (TextView) findViewById(R.id.txtUserProfile);
		txtUserProfile.setTypeface(tf);
		tf = Typeface.createFromAsset(getAssets(), "fonts/Gotham-Bold.otf");
		mNameView.setTypeface(tf);

		Bundle parametros = getIntent().getExtras();
		usuario.setId(Integer.parseInt(parametros.getString("id")));
		if (usuario.getId().toString()
				.equals(session.getUserDetails().get(SessionManager.KEY_ID))) {
			btnEditUser.setVisibility(View.VISIBLE);
		} else {
			btnEditUser.setVisibility(View.GONE);
		}
		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent) {
			search();
		} else {
			Toast.makeText(ViewUserActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}
	}

	private void search() {
		mViewUserStatusMessageView.setText(R.string.progress_loading_data);
		showProgress(true);
		mViewUserTask = new ViewUserTask();
		mViewUserTask.execute((Void) null);

	}

	public void editUser(View v) {
		Intent i = new Intent(new Intent(ViewUserActivity.this,
				EditUserActivity.class));
		Gson gson = new Gson();
		String userJSON = gson.toJson(usuario);
		i.putExtra("user", userJSON);

		startActivityForResult(i, 1);
	}

	class ViewUserTask extends AsyncTask<Void, Void, Boolean> {
		private static final String TAG_NOME = "name";
		private static final String TAG_EMAIL = "email";
		private static final String TAG_DATA_NASCIMENTO = "date_of_birth";
		private static final String TAG_ID = "id";
		private static final String TAG_USER = "User";
		private static final String TAG_GENDER = "gender";
		private static final String TAG_FILENAME = "filename";

		@Override
		protected Boolean doInBackground(Void... params) {
			String url = Guidebar.serverUrl + "users/details/"
					+ usuario.getId() + ".json";

			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();

			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			String respostaRetornada = "";
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
						parametrosPost);
				resposta = respostaRetornada.toString();
				if (!resposta.replaceAll("\\s+", "").equals("[]")) {
					JSONObject json = new JSONObject(resposta);
					try {
						JSONObject c = json.getJSONObject(TAG_USER);
						usuario = new Usuario(usuario.getId());
						usuario.setNome(c.getString(TAG_NOME));
						usuario.setEmail(c.getString(TAG_EMAIL));
						usuario.setGender(Integer.parseInt(c
								.getString(TAG_GENDER)));
						usuario.setDataNascimento(c
								.getString(TAG_DATA_NASCIMENTO));
						usuario.setId(Integer.parseInt(c.getString(TAG_ID)));
						if (c.getString(TAG_FILENAME).contains(
								"http://")||c.getString(TAG_FILENAME).contains(
										"https://")) {
							usuario.setThumbnail(c.getString(TAG_FILENAME));
						} else {
							usuario.setThumbnail(Guidebar.serverUrl + "img/"
									+ c.getString(TAG_FILENAME));
						}
						usuario.setEmailPagSeguro("");
						usuario.setTokenPagSeguro("");
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}

			return !resposta.replaceAll("\\s+", "").equals("[]");
		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mViewUserTask = null;
			showProgress(false);
			if (success) {
				mNameView.setText(Html.fromHtml(usuario.getNome()));
				mEmailView.setText(Html.fromHtml(usuario.getEmail()));
				mAgeView.setText(Html.fromHtml(usuario.getDataNascimento()));
				mProfileImage = (ImageView) findViewById(R.id.imgUserProfile);

				if (mProfileImage != null) {
					new ImageDownloaderTask(mProfileImage).executeOnExecutor(
							THREAD_POOL_EXECUTOR, usuario.getThumbnail());
				}
				btnEditUser.setVisibility(View.VISIBLE);
			} else {
				btnEditUser.setVisibility(View.GONE);
				logout();
			}
		}

		@Override
		protected void onCancelled() {
			mViewUserTask = null;
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

			mViewUserStatusView.setVisibility(View.VISIBLE);
			mViewUserStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mViewUserStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mViewUserFormView.setVisibility(View.VISIBLE);
			mViewUserFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mViewUserFormView.setVisibility(show ? View.GONE
									: View.VISIBLE);
						}
					});
		} else {
			mViewUserStatusView.setVisibility(show ? View.VISIBLE : View.GONE);
			mViewUserFormView.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		search();
	}

	public void logout() {
		session.logoutUser();
		finish();
	}
}
