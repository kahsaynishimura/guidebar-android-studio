package br.com.guidebar.activities;

import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONObject;

import android.accounts.Account;
import android.accounts.AccountManager;
import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.app.Activity;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.Guidebar;

public class RecoverPassActivity extends Activity {
	private AutoCompleteTextView mEmailView;
	private String mEmail;
	private SendEmailTask mSendEmailTask;
	private View mRecoveryPassFormView;
	private View mRecoveryPassStatusView;
	private TextView mRecoveryPassStatusMessageView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_recover_pass);
		loadComoponents();
	}

	private void loadComoponents() {
		ArrayAdapter<String> aa = new ArrayAdapter<String>(this,
				android.R.layout.simple_dropdown_item_1line, getUserEmails());
		mEmailView = (AutoCompleteTextView) findViewById(R.id.txtEmailRecover);
		mEmailView.setAdapter(aa);
		Button btnSend = (Button) findViewById(R.id.btnSend);
		mRecoveryPassFormView = findViewById(R.id.recover_pass_form);
		mRecoveryPassStatusView = findViewById(R.id.status);
		mRecoveryPassStatusMessageView = (TextView) findViewById(R.id.status_message);

		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		btnSend.setTypeface(tf);
		mEmailView.setTypeface(tf);
		mRecoveryPassStatusMessageView.setTypeface(tf);
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

	public void send(View v) {
		if (mSendEmailTask != null) {
			return;
		}
		mEmailView.setError(null);
		mEmail = mEmailView.getText().toString();
		boolean cancel = false;
		View focusView = null;
		if (TextUtils.isEmpty(mEmail)) {
			mEmailView.setError(getString(R.string.error_field_required));
			focusView = mEmailView;
			cancel = true;
		}
		if (cancel) {
			focusView.requestFocus();
		} else {
			mRecoveryPassStatusMessageView
					.setText(R.string.progress_saving_data);
			showProgress(true);
			mSendEmailTask = new SendEmailTask();
			mSendEmailTask.execute();
		}

	}

	public class SendEmailTask extends AsyncTask<Void, Void, Boolean> {
		private String dados="";

		@Override
		protected Boolean doInBackground(Void... params) {
			Boolean success = false;
			String url = Guidebar.serverUrl + "users/recover_password.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();

			parametrosPost.add(new BasicNameValuePair("data[User][email]",
					mEmail));
			String resposta = "";

			try {
				String respostaRetornada = ConexaoHttpClient.executaHttpPost(
						url, parametrosPost);
				resposta = respostaRetornada.toString();
				JSONObject json = new JSONObject(resposta);
				success=json.getBoolean("success");
				dados=json.getString("data");
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}

			return success;
		}

		@Override
		protected void onPostExecute(Boolean success) {
			showProgress(false);
			mSendEmailTask = null;
			if (success) {
				Toast.makeText(RecoverPassActivity.this,dados,
						Toast.LENGTH_LONG).show();
				finish();
			} else {
				mEmailView
						.setError(dados);
				mEmailView.requestFocus();
			}
		}

		@Override
		protected void onCancelled() {
			showProgress(false);
			mSendEmailTask = null;
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

			mRecoveryPassStatusView.setVisibility(View.VISIBLE);
			mRecoveryPassStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mRecoveryPassStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mRecoveryPassFormView.setVisibility(View.VISIBLE);
			mRecoveryPassFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mRecoveryPassFormView
									.setVisibility(show ? View.GONE
											: View.VISIBLE);
						}
					});
		} else {
			mRecoveryPassStatusView.setVisibility(show ? View.VISIBLE
					: View.GONE);
			mRecoveryPassFormView
					.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}
}
