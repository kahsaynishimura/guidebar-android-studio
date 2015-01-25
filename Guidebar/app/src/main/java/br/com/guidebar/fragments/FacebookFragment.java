package br.com.guidebar.fragments;

import java.util.ArrayList;
import java.util.Arrays;

import org.apache.http.NameValuePair;
import org.apache.http.NoHttpResponseException;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.activities.ListEventsActivity;
import br.com.guidebar.bean.Usuario;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.SessionManager;

import com.facebook.HttpMethod;
import com.facebook.Request;
import com.facebook.Response;
import com.facebook.Session;
import com.facebook.SessionState;
import com.facebook.UiLifecycleHelper;
import com.facebook.model.GraphUser;
import com.facebook.widget.LoginButton;

public class FacebookFragment extends Fragment {
	private UiLifecycleHelper uiHelper;
	private LoginFBTask mLoginFBTask = new LoginFBTask();
	private Usuario usuario = new Usuario();
	SessionManager sessionm;
	Session session;

	@Override 
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		sessionm = new SessionManager(getActivity());
		uiHelper = new UiLifecycleHelper(getActivity(), callback);
		uiHelper.onCreate(savedInstanceState);
		session = Session.getActiveSession();
		if (session != null && session.isOpened()) {
			// Get the user's data
			makeMeRequest(session);
		}
	}

	@Override
	public void onResume() {
		super.onResume();
		Session session = Session.getActiveSession();
		if (session != null && (session.isOpened() || session.isClosed())) {
			onSessionStateChange(session, session.getState(), null);
		}

		uiHelper.onResume();
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
		uiHelper.onActivityResult(requestCode, resultCode, data);
	}

	@Override
	public void onPause() {
		super.onPause();
		uiHelper.onPause();
	}

	@Override
	public void onDestroy() {
		super.onDestroy();
		uiHelper.onDestroy();
	}

	@Override
	public void onSaveInstanceState(Bundle outState) {
		super.onSaveInstanceState(outState);
		uiHelper.onSaveInstanceState(outState);
	}

	private Session.StatusCallback callback = new Session.StatusCallback() {
		@Override
		public void call(Session session, SessionState state,
				Exception exception) {
			onSessionStateChange(session, state, exception);
		}
	};

	private void onSessionStateChange(Session session, SessionState state,
			Exception exception) {
		if (state.isOpened()) {
			Log.i("MainFragment", "Logged in...");
			makeMeRequest(session);
		} else if (state.isClosed()) {
			Log.i("MainFragment", "Logged out...");
		}
	}

	private void makeMeRequest(final Session session) {
		// Make an API call to get user data and define a
		// new callback to handle the response.
		Request request = Request.newMeRequest(session,
				new Request.GraphUserCallback() {
					@Override
					public void onCompleted(GraphUser user, Response response) {
						// If the response is successful
						if (session == Session.getActiveSession()) {
							if (user != null) {
								usuario.setIdFacebook(user.getId());
								usuario.setEmail(user.getProperty("email")
										.toString());
								usuario.setNome(user.getName());
								usuario.setDataNascimento(user.getProperty(
										"birthday").toString());
								usuario.setGender(user.getProperty("gender")
										.toString() == "female" ? 1 : 2);
								usuario.setThumbnail("http://graph.facebook.com/"
										+ usuario.getIdFacebook() + "/picture?fields=picture");
								new Request(
									    session,
									    "/me?fields=picture",
									    null,
									    HttpMethod.GET,
									    new Request.Callback() {
									        public void onCompleted(Response response) {
									            Log.i("tag", response.toString());
									        }
									    }
									).executeAsync();
								if (mLoginFBTask != null) {
									mLoginFBTask = new LoginFBTask();
									mLoginFBTask.execute();
								}
							}
						}
						if (response.getError() != null) {
							// Handle errors, will do so later.
						}
					}
				});
		request.executeAsync();
	}

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container,
			Bundle savedInstanceState) {
		View view = inflater.inflate(R.layout.facebook_login, container, false);
		LoginButton authButton = (LoginButton) view
				.findViewById(R.id.authButton);
		authButton.setFragment(this);
		authButton.setReadPermissions(Arrays.asList("email", "user_birthday"));

		return view;
	}

	private static final String TAG_ID = "id";
	private static final String TAG_EMAIL = "email";
	private static final String TAG_IS_ACTIVE = "is_active";
	private static final String TAG_ID_FACEBOOK = "id_facebook";
	private static final String TAG_TOKEN_PAGSEGURO = "token_pagseguro";
	private static final String TAG_EMAIL_PAGSEGURO = "email_pagseguro";
	private static final String TAG_ACCESS_TOKEN = "access_token";

	public class LoginFBTask extends AsyncTask<Void, Void, Boolean> {

		String resposta = "";
		boolean errorProxy = false;

		JSONArray array = null;

		@Override
		protected Boolean doInBackground(Void... params) {
			String urlPost = Guidebar.serverUrl + "users/loginfb.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("data[email]", usuario
					.getEmail()));
			parametrosPost.add(new BasicNameValuePair("data[id]", usuario
					.getIdFacebook()));
			parametrosPost.add(new BasicNameValuePair("data[birthday]", usuario
					.getDataNascimento()));
			parametrosPost.add(new BasicNameValuePair("data[name]", usuario
					.getNome()));
			parametrosPost.add(new BasicNameValuePair("data[gender]", usuario
					.getGender().toString()));
			parametrosPost.add(new BasicNameValuePair("data[filename]", usuario
					.getThumbnail()));

			String respostaRetornada = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				// respostaRetornada =
				// ConexaoHttpClient.executaHttpGet(urlPost);
				resposta = respostaRetornada.toString();

				if (!(resposta.equals("null\n"))) {
					try {
						JSONObject c = new JSONObject(resposta);
						usuario.setId(Integer.parseInt(c.getString(TAG_ID)));
						usuario.setEmail(c.getString(TAG_EMAIL));
						usuario.setIsConfirmado(Boolean.parseBoolean(c
								.getString(TAG_IS_ACTIVE)));
						usuario.setEmailPagSeguro(c
								.getString(TAG_EMAIL_PAGSEGURO)==null?"":c
										.getString(TAG_EMAIL_PAGSEGURO));
						usuario.setTokenPagSeguro(c
								.getString(TAG_TOKEN_PAGSEGURO)==null?"":c
										.getString(TAG_TOKEN_PAGSEGURO));
						usuario.setIdFacebook(c.getString(TAG_ID_FACEBOOK));
						usuario.setAccessToken(c.getString(TAG_ACCESS_TOKEN));

					} catch (JSONException e) {
						e.printStackTrace();
					}
				}
			} catch (NoHttpResponseException erro) {
				Log.i("Erro", getString(R.string.error_proxy));
				errorProxy = true;
			} catch (Exception e) {
				Log.i("Erro LoginActivity", "Houve um erro no login");
			}
			return (!(resposta.equals("null\n"))) && !errorProxy;
		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mLoginFBTask = null;
			if (success) {
				if (usuario.getIsConfirmado()) {
					sessionm.createLoginSession(
							usuario.getEmail(),
							usuario.getId().toString(),
							usuario.getEmailPagSeguro() == null ? "" : usuario
									.getEmailPagSeguro(),
							usuario.getTokenPagSeguro() == null ? "" : usuario
									.getTokenPagSeguro(), usuario
									.getIdFacebook(), usuario.getAccessToken());

					startActivity(new Intent(getActivity(),
							ListEventsActivity.class));
//					getActivity().finish();
				}
			} else {
				Toast.makeText(getActivity(),
						getText(R.string.error_network_connection),
						Toast.LENGTH_SHORT).show();
			}
		}

		@Override
		protected void onCancelled() {
			mLoginFBTask = null;
		}
	}

}
