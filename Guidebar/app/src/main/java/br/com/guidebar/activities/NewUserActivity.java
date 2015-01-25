package br.com.guidebar.activities;

import java.util.ArrayList;
import java.util.Calendar;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.accounts.Account;
import android.accounts.AccountManager;
import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.app.Activity;
import android.app.AlertDialog;
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
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.bean.Usuario;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.ConnectionDetector;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.customviews.DateDisplayPicker;

public class NewUserActivity extends Activity {

	private Usuario usuario = new Usuario();
	private EditText mNameView;
	private AutoCompleteTextView mEmailView;
	private EditText mPasswordView;
	private DateDisplayPicker mBirthDateView;
	private Spinner mGenderView;
	private NewUserTask mNewUserTask = null;
	private View mNewUserFormView;
	private View mNewUserStatusView;
	private TextView mNewUserStatusMessageView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_new_user);
		loadComponents();

	}

	private void loadComponents() {
		mNameView = (EditText) findViewById(R.id.txtName);

		ArrayAdapter<String> aa = new ArrayAdapter<String>(this,
				android.R.layout.simple_dropdown_item_1line, getUserEmails());
		mEmailView = (AutoCompleteTextView) findViewById(R.id.txtEmail);
		mEmailView.setAdapter(aa);

		mPasswordView = (EditText) findViewById(R.id.txtPassword);
		mBirthDateView = (DateDisplayPicker) findViewById(R.id.txtBirthDate);
		mGenderView = (Spinner) findViewById(R.id.txtGender);
		ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(
				NewUserActivity.this, R.array.gender_array,
				android.R.layout.simple_spinner_item);
		adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		mGenderView.setAdapter(adapter);
		mNewUserFormView = findViewById(R.id.new_user_form);
		mNewUserStatusView = findViewById(R.id.status);
		mNewUserStatusMessageView = (TextView) findViewById(R.id.status_message);
		TextView txtWelcomeView = (TextView) findViewById(R.id.txtWelcome);
		Button btnSave = (Button) findViewById(R.id.btnNextStep);
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		txtWelcomeView.setTypeface(tf);
		mNameView.setTypeface(tf);
		mEmailView.setTypeface(tf);
		mBirthDateView.setTypeface(tf);
		mPasswordView.setTypeface(tf);
		btnSave.setTypeface(tf);
		mNewUserStatusMessageView.setTypeface(tf);
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

	public void attemptSave() {
		if (mNewUserTask != null) {
			return;
		}
		mNameView.setError(null);
		mEmailView.setError(null);
		mPasswordView.setError(null);
		mBirthDateView.setError(null);
		usuario.setNome(mNameView.getText().toString());
		usuario.setEmail(mEmailView.getText().toString());
		usuario.setSenha(mPasswordView.getText().toString());
		usuario.setDataNascimento(mBirthDateView.getText().toString());
		usuario.setGender(Integer.parseInt((mGenderView
				.getSelectedItemPosition() + 1) + ""));
		Calendar dateOfBirth = Calendar.getInstance();
		String data = usuario.getDataNascimento();
		dateOfBirth.set(Calendar.YEAR, DateDisplayPicker.getYear(data, "/"));
		dateOfBirth.set(Calendar.DAY_OF_MONTH,
				DateDisplayPicker.getDay(data, "/"));
		dateOfBirth.set(Calendar.MONTH,
				((DateDisplayPicker.getMonth(data, "/")) - 1));

		boolean cancel = false;
		View focusView = null;

		if (TextUtils.isEmpty(usuario.getNome())) {
			mNameView.setError(getString(R.string.error_field_required));
			focusView = mNameView;
			cancel = true;
		}
		if (TextUtils.isEmpty(usuario.getSenha())) {
			mPasswordView.setError(getString(R.string.error_field_required));
			focusView = mPasswordView;
			cancel = true;
		} else if (usuario.getSenha().length() < 4) {
			mPasswordView.setError(getString(R.string.error_invalid_password));
			focusView = mPasswordView;
			cancel = true;
		}
		if (TextUtils.isEmpty(usuario.getEmail())) {
			mEmailView.setError(getString(R.string.error_field_required));
			focusView = mEmailView;
			cancel = true;
		} else if (!usuario.getEmail().contains("@")) {
			mEmailView.setError(getString(R.string.error_invalid_email));
			focusView = mEmailView;
			cancel = true;
		}
		if (TextUtils.isEmpty(usuario.getDataNascimento())) {
			mBirthDateView.setError(getString(R.string.error_field_required));
			focusView = mBirthDateView;
			cancel = true;
		} else if (dateOfBirth.after(Calendar.getInstance())) {
			mBirthDateView
					.setError(getString(R.string.error_invalid_birth_date));
			focusView = mBirthDateView;
			cancel = true;
		}

		if (cancel) {
			focusView.requestFocus();
		} else {
			mNewUserStatusMessageView.setText(R.string.progress_saving_data);
			showProgress(true);
			mNewUserTask = new NewUserTask();
			mNewUserTask.execute((Void) null);
		}
	}

	public void nextStep(View v) {
		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent) {
			attemptSave();
		} else {
			Toast.makeText(NewUserActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}
	}

	public class NewUserTask extends AsyncTask<Void, Void, Boolean> {

		private static final String TAG_ID = "id";
		private static final String TAG_ERRORS = "errors";
		private static final String TAG_SUCCESS = "success";
		private String erros = "";

		@Override
		protected Boolean doInBackground(Void... params) {
			Boolean success = false;
			String urlPost = Guidebar.serverUrl + "users/add.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("data[User][name]",
					usuario.getNome()));
			parametrosPost.add(new BasicNameValuePair("data[User][email]",
					usuario.getEmail()));
			parametrosPost.add(new BasicNameValuePair("data[User][password]",
					usuario.getSenha()));
			parametrosPost
					.add(new BasicNameValuePair("data[User][date_of_birth]",
							mBirthDateView.getDateForPersistence(usuario
									.getDataNascimento())));
			parametrosPost.add(new BasicNameValuePair("data[User][gender]",
					usuario.getGender() + ""));

			String respostaRetornada = "";
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				JSONObject response = new JSONObject(resposta);
				success = Boolean.parseBoolean(response.getString(TAG_SUCCESS));
				if (success == false) {
					erros = response.getString(TAG_ERRORS);
				}
				usuario.setId(Integer.parseInt(response.getString(TAG_ID)));
			} catch (Exception erro) {
				Log.i("Erro", erro + "");
			}
			return success;

		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mNewUserTask = null;
			showProgress(false);
			if (success) {
				Toast.makeText(NewUserActivity.this,
						R.string.success_save_user, Toast.LENGTH_LONG).show();

				// Intent i = new Intent(NewUserActivity.this,
				// UploadToServerActivity.class);
				// i.putExtra("id_usuario", usuario.getId());
				// startActivity(i);

				finish();
			} else {
				try {
					JSONObject errors = new JSONObject(erros);
					mEmailView.setError(errors.getString("email"));
					mEmailView.requestFocus();
				} catch (JSONException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
		}

		@Override
		protected void onCancelled() {
			mNewUserTask = null;
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

			mNewUserStatusView.setVisibility(View.VISIBLE);
			mNewUserStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mNewUserStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mNewUserFormView.setVisibility(View.VISIBLE);
			mNewUserFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mNewUserFormView.setVisibility(show ? View.GONE
									: View.VISIBLE);
						}
					});
		} else {
			mNewUserStatusView.setVisibility(show ? View.VISIBLE : View.GONE);
			mNewUserFormView.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}

	public void mensagemExibir(String titulo, String texto) {
		AlertDialog.Builder mensagem = new AlertDialog.Builder(
				NewUserActivity.this);
		mensagem.setTitle(titulo);
		mensagem.setMessage(texto);
		mensagem.setNeutralButton("OK", null);
		mensagem.show();
	}

}
