package br.com.guidebar.activities;

import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.NoHttpResponseException;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.accounts.Account;
import android.accounts.AccountManager;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.text.TextUtils;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.bean.Usuario;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.ConnectionDetector;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.SessionManager;
import br.com.guidebar.fragments.FacebookFragment;

public class LoginActivity extends FragmentActivity {
	Usuario usuario = new Usuario();
	ProgressDialog dialog;
	private UserLoginTask mAuthTask = null;
	private SessionManager session;
	private String mEmail;
	private String mPassword;
	private AutoCompleteTextView mEmailView;
	private EditText mPasswordView;
	private FacebookFragment facebookFragment;

	@Override
	protected void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_login);
		
		if (savedInstanceState == null) {
	        // Add the fragment on initial activity setup
			facebookFragment = new FacebookFragment();
	        getSupportFragmentManager()
	        .beginTransaction()
	        .add(R.id.login_form_linear, facebookFragment)
	        .commit();
	    } else {
	        // Or set the fragment from restored state info
	    	facebookFragment = (FacebookFragment) getSupportFragmentManager()
	        .findFragmentById(R.id.login_form_linear);
	    }
		

		loadComponents();

	}

	private void loadComponents() {
		session = new SessionManager(getApplicationContext());
		if (session.isLoggedIn()) {
			startActivity(new Intent(LoginActivity.this,
					ListEventsActivity.class));
			finish();
		}
		ArrayAdapter<String> aa = new ArrayAdapter<String>(this,
				android.R.layout.simple_dropdown_item_1line, getUserEmails());
		mEmailView = (AutoCompleteTextView) findViewById(R.id.email);
		mEmailView.setAdapter(aa);

		mPasswordView = (EditText) findViewById(R.id.password);
		mPasswordView
				.setOnEditorActionListener(new TextView.OnEditorActionListener() {
					@Override
					public boolean onEditorAction(TextView textView, int id,
							KeyEvent keyEvent) {
						if (id == R.id.login || id == EditorInfo.IME_NULL) {
							attemptLogin();
							return true;
						}
						return false;
					}
				});

		Button btnLogin = (Button) findViewById(R.id.sign_in_button);
		Button btnRegister = (Button) findViewById(R.id.register_button);
		Button btnRecover = (Button) findViewById(R.id.btnRecoverPass);
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		mEmailView.setTypeface(tf);
		mPasswordView.setTypeface(tf);
		btnLogin.setTypeface(tf);
		btnRegister.setTypeface(tf);
		btnRecover.setTypeface(tf);
	}

	public void logar(View v) {
		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent) {
			attemptLogin();
		} else {
			Toast.makeText(LoginActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}
	}

	public void recoverPass(View v) {
		Intent i = new Intent(LoginActivity.this, RecoverPassActivity.class);
		startActivity(i);

	}

	public void attemptLogin() {
		if (mAuthTask != null) {
			return;
		}

		mEmailView.setError(null);
		mPasswordView.setError(null);

		mEmail = mEmailView.getText().toString();
		mPassword = mPasswordView.getText().toString();

		boolean cancel = false;
		View focusView = null;

		if (TextUtils.isEmpty(mPassword)) {
			mPasswordView.setError(getString(R.string.error_field_required));
			focusView = mPasswordView;
			cancel = true;
		} else if (mPassword.length() < 4) {
			mPasswordView.setError(getString(R.string.error_invalid_password));
			focusView = mPasswordView;
			cancel = true;
		}

		if (TextUtils.isEmpty(mEmail)) {
			mEmailView.setError(getString(R.string.error_field_required));
			focusView = mEmailView;
			cancel = true;
		} else if (!mEmail.contains("@")) {
			mEmailView.setError(getString(R.string.error_invalid_email));
			focusView = mEmailView;
			cancel = true;
		}

		if (cancel) {
			focusView.requestFocus();
		} else {
			mAuthTask = new UserLoginTask();
			mAuthTask.execute((Void) null);
		}
	}

	private static final String TAG_ID = "id";
	private static final String TAG_EMAIL = "email";
	private static final String TAG_IS_ACTIVE = "is_active";
	private static final String TAG_ID_FACEBOOK = "id_facebook";
	private static final String TAG_TOKEN_PAGSEGURO = "token_pagseguro";
	private static final String TAG_EMAIL_PAGSEGURO = "email_pagseguro";
	private static final String TAG_ACCESS_TOKEN = "access_token";

	public class UserLoginTask extends AsyncTask<Void, Void, Boolean> {

		String resposta = "";
		boolean errorProxy = false;

		JSONArray array = null;

		@Override
		protected Boolean doInBackground(Void... params) {
			String urlPost = Guidebar.serverUrl + "users/login.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("data[User][email]",
					mEmail));
			parametrosPost.add(new BasicNameValuePair("data[User][password]",
					mPassword));
			String respostaRetornada = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();

				if (!(resposta.equals("null\n"))) {
					try {
						JSONObject c = new JSONObject(resposta);
						usuario.setId(Integer.parseInt(c.getString(TAG_ID)));
						usuario.setTokenPagSeguro(c
								.getString(TAG_TOKEN_PAGSEGURO));
						usuario.setEmail(c.getString(TAG_EMAIL));
						usuario.setIsConfirmado(Boolean.parseBoolean(c
								.getString(TAG_IS_ACTIVE)));
						usuario.setEmailPagSeguro(c
								.getString(TAG_EMAIL_PAGSEGURO));
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
			mAuthTask = null;
			if (success) {
				if (usuario.getIsConfirmado()) {
					session.createLoginSession(
							usuario.getEmail(),
							usuario.getId().toString(),
							usuario.getEmailPagSeguro() == null ? "" : usuario
									.getEmailPagSeguro(),
							usuario.getTokenPagSeguro() == null ? "" : usuario
									.getTokenPagSeguro(), usuario
									.getIdFacebook(), usuario.getAccessToken());

					startActivity(new Intent(LoginActivity.this,
							ListEventsActivity.class));
					finish();
				} else {
					mEmailView.setError(null);
					mEmailView
							.setError(getText(R.string.error_user_confirmation));
					mEmailView.requestFocus();
				}

			} else {
				if (resposta.equals("null\n")) {
					mEmailView.setError(null);
					mEmailView.setError(getString(R.string.error_empty_user));
					mEmailView.requestFocus();
				} else if (errorProxy) {
					Toast.makeText(LoginActivity.this,
							getText(R.string.error_proxy), Toast.LENGTH_SHORT)
							.show();
				}
			}
		}

		@Override
		protected void onCancelled() {
			mAuthTask = null;
		}
	}

	public void registerUser(View v) {
		startActivity(new Intent(LoginActivity.this, NewUserActivity.class));
	}

	private ArrayList<String> getUserEmails() {
		ArrayList<String> lstEmails = new ArrayList<String>();
		AccountManager am = AccountManager.get(getApplicationContext());
		// Facebook
		Account[] accounts = am.getAccountsByType("com.facebook.auth.login");
		ArrayList<Account> arrayAccounts = new ArrayList<Account>();
		for (Account account : accounts) {
			lstEmails.add(account.name);
			arrayAccounts.add(account);
		}
		// Google
		accounts = am.getAccountsByType("com.google");
		for (Account account : accounts) {
			lstEmails.add(account.name);
			arrayAccounts.add(account);
		}
		return lstEmails;
	}

	@Override
	public void onResume() {
		super.onResume();
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);
	}

	@Override
	public void onPause() {
		super.onPause();
	}

	@Override
	public void onDestroy() {
		super.onDestroy();
	}

	@Override
	public void onSaveInstanceState(Bundle outState) {
		super.onSaveInstanceState(outState);
	}
}
