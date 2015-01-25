package br.com.guidebar.activities;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.HashMap;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.app.Activity;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.ArrayAdapter;
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
import br.com.guidebar.classes.SessionManager;
import br.com.guidebar.customviews.DateDisplayPicker;

public class EditUserActivity extends Activity {
	// String values
	private String mName;
	private String mBirthDate;
	private String mEmailPagSeguro;
	private String mTokenPagSeguro;

	// Session Manager Class
	SessionManager session;

	// UI references.

	private EditText mNameView;
	private DateDisplayPicker mBirthDateView;
	private Spinner mGenderView;	
	private EditText mEmailPagSeguroView;
	private EditText mTokenPagSeguroView;

	private EditUserTask mEditUserTask = null;
	private Usuario usuario = new Usuario();

	private View mEditUserFormView;
	private View mEditUserStatusView;
	private TextView mEditUserStatusMessageView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_edit_user);
		loadComponents();
	}

	private void loadComponents() {
		// Session class instance
		session = new SessionManager(getApplicationContext());
		session.checkLogin();
		usuario.setId(Integer.parseInt(session.getUserDetails().get(
				SessionManager.KEY_ID)));
		mNameView = (EditText) findViewById(R.id.txtName);
		mBirthDateView = (DateDisplayPicker) findViewById(R.id.txtBirthDate);
		mGenderView = (Spinner) findViewById(R.id.txtGender);
		ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(
				EditUserActivity.this, R.array.gender_array,
				android.R.layout.simple_spinner_item);
		adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		mGenderView.setAdapter(adapter);
		mEmailPagSeguroView = (EditText) findViewById(R.id.txtEmailPagseguro);
		mTokenPagSeguroView = (EditText) findViewById(R.id.txtTokenPagseguro);
		Button btnSave = (Button) findViewById(R.id.btnSave);
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		mNameView.setTypeface(tf);
		mBirthDateView.setTypeface(tf);

		mEmailPagSeguroView.setTypeface(tf);
		mTokenPagSeguroView.setTypeface(tf);
		try {

			String TAG_ID = "id";
			String TAG_NOME = "nome";
			String TAG_TOKEN_PAGSEGURO = "tokenPagSeguro";
			String TAG_EMAIL_PAGSEGURO = "emailPagSeguro";
			String TAG_DATA_NASCIMENTO = "dataNascimento";
			String TAG_GENDER = "gender";
			String TAG_EMAIL = "email";
			String user = getIntent().getExtras().getString("user");
			JSONObject c = new JSONObject(user);
			usuario.setId(Integer.parseInt(c.getString(TAG_ID)));
			usuario.setNome(c.getString(TAG_NOME));
			usuario.setEmail(c.getString(TAG_EMAIL));
			usuario.setDataNascimento(c
					.getString(TAG_DATA_NASCIMENTO));
			usuario.setEmailPagSeguro(c
					.getString(TAG_EMAIL_PAGSEGURO));
			usuario.setTokenPagSeguro(c
					.getString(TAG_TOKEN_PAGSEGURO));
			usuario.setGender(Integer.parseInt(c
					.getString(TAG_GENDER)));
			mNameView.setText(usuario.getNome());
			mEmailPagSeguroView.setText(usuario.getEmailPagSeguro().toString()
					.equals("null") ? "" : usuario.getEmailPagSeguro());
			mTokenPagSeguroView.setText(usuario.getTokenPagSeguro().toString()
					.equals("null") ? "" : usuario.getTokenPagSeguro());
			mBirthDateView.setText(mBirthDateView.getDateForPresenting(usuario
					.getDataNascimento()));
			mGenderView.setSelection(usuario.getGender()-1);
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		// user.setId(Integer.parseInt(getIntent().get.getStringExtra("user")));
		btnSave.setTypeface(tf);
		findViewById(R.id.btnSave).setOnClickListener(
				new View.OnClickListener() {
					@Override
					public void onClick(View view) {
						ConnectionDetector cd = new ConnectionDetector(
								getApplicationContext());
						Boolean isInternetPresent = cd.isConnectingToInternet();
						if (isInternetPresent) {
							attemptSave();
						} else {
							Toast.makeText(EditUserActivity.this,
									R.string.error_network_connection,
									Toast.LENGTH_SHORT).show();
						}
					}
				});
		mEditUserFormView = findViewById(R.id.edit_user_form);
		mEditUserStatusView = findViewById(R.id.status);
		mEditUserStatusMessageView = (TextView) findViewById(R.id.status_message);
		mEditUserStatusMessageView.setTypeface(tf);
		mEditUserStatusMessageView.setText(R.string.progress_loading_data);

	}


	public void attemptSave() {
		if (mEditUserTask != null ) {
			return;
		}
		// Reset errors.
		mNameView.setError(null);
		mBirthDateView.setError(null);
		mEmailPagSeguroView.setError(null);
		mTokenPagSeguroView.setError(null);

		// Store values at the time of the saving attempt.

		mName = mNameView.getText().toString();
		mBirthDate = mBirthDateView.getText().toString();
		mEmailPagSeguro = mEmailPagSeguroView.getText().toString();
		mTokenPagSeguro = mTokenPagSeguroView.getText().toString();
		usuario.setGender(Integer.parseInt((mGenderView
				.getSelectedItemPosition() + 1) + ""));
	
		Calendar dateOfBirth = Calendar.getInstance();
		dateOfBirth.set(Calendar.YEAR,
				DateDisplayPicker.getYear(mBirthDate, "/"));
		dateOfBirth.set(Calendar.DAY_OF_MONTH,
				DateDisplayPicker.getDay(mBirthDate, "/"));
		dateOfBirth.set(Calendar.MONTH,
				((DateDisplayPicker.getMonth(mBirthDate, "/")) - 1));
		boolean cancel = false;
		View focusView = null;

		if (TextUtils.isEmpty(mName)) {
			mNameView.setError(getString(R.string.error_field_required));
			focusView = mNameView;
			cancel = true;
		}
		if (TextUtils.isEmpty(mBirthDate)) {
			mBirthDateView.setError(getString(R.string.error_field_required));
			focusView = mBirthDateView;
			cancel = true;
		} else if (dateOfBirth.after(Calendar.getInstance())) {
			mBirthDateView
					.setError(getString(R.string.error_invalid_birth_date));
			focusView = mBirthDateView;
			cancel = true;
		}
		if (mTokenPagSeguro.length() != 0 && mTokenPagSeguro.length() != 32) {
			mTokenPagSeguroView
					.setError(getString(R.string.error_invalid_token));
			focusView = mTokenPagSeguroView;
			cancel = true;
		}
		if (cancel) {
			focusView.requestFocus();
		} else {
			mEditUserStatusMessageView.setText(R.string.progress_saving_data);
			showProgress(true);
			mEditUserTask = new EditUserTask();
			mEditUserTask.execute((Void) null);
		}
	}

	public class EditUserTask extends AsyncTask<Void, Void, Boolean> {

		private static final String TAG_ID = "id";
		private static final String TAG_ERRORS = "errors";
		private static final String TAG_SUCCESS = "success";
		private String erros = "";
		
		@Override
		protected Boolean doInBackground(Void... params) {
			Boolean success = false;
			
			String urlPost = Guidebar.serverUrl + "users/"+usuario.getId()+".json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			parametrosPost.add(new BasicNameValuePair("data[User][name]", mName));
			parametrosPost.add(new BasicNameValuePair("data[User][date_of_birth]",
					mBirthDateView.getDateForPersistence(mBirthDate)));
			parametrosPost.add(new BasicNameValuePair("data[User][email_pagseguro]",
					mEmailPagSeguro));
			parametrosPost.add(new BasicNameValuePair("data[User][token_pagseguro]",
					mTokenPagSeguro));
			parametrosPost.add(new BasicNameValuePair("data[User][gender]", usuario
					.getGender().toString()));parametrosPost.add(new BasicNameValuePair("data[User][id]", usuario
							.getId().toString()));
			
			String respostaRetornada = "";
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();

				JSONObject response = new JSONObject(resposta);
				erros = response.getString(TAG_ERRORS);
				success = Boolean.parseBoolean(response.getString(TAG_SUCCESS));
				usuario.setId(Integer.parseInt(response.getString(TAG_ID)));
			} catch (Exception erro) {
				Toast.makeText(EditUserActivity.this, "Erro: " + erro,
						Toast.LENGTH_SHORT).show();
			}

			return success;

		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mEditUserTask = null;
			showProgress(false);

			if (success) {
				HashMap<String, String> usuarioLogado = session
						.getUserDetails();
				session.logoutUser();
				// Alterar dados da sessao para pegar novo token do promotor
				session.createLoginSession(
						usuarioLogado.get(SessionManager.KEY_EMAIL),
						usuarioLogado.get(SessionManager.KEY_ID),
						mEmailPagSeguro, mTokenPagSeguro,
						usuarioLogado.get(SessionManager.KEY_ID_FACEBOOK),
						usuarioLogado.get(SessionManager.KEY_ACCESS_TOKEN));

//				Intent i = new Intent(EditUserActivity.this,
//						UploadToServerActivity.class);
//				i.putExtra("id_usuario", usuario.getId());
//				startActivity(i);
				finish();
			} else {
				try {
					JSONObject errors = new JSONObject(erros);
					mNameView.setError(errors.getString("name"));
					mNameView.requestFocus();
				} catch (JSONException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
		}

		@Override
		protected void onCancelled() {
			mEditUserTask = null;
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

			mEditUserStatusView.setVisibility(View.VISIBLE);
			mEditUserStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mEditUserStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mEditUserFormView.setVisibility(View.VISIBLE);
			mEditUserFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mEditUserFormView.setVisibility(show ? View.GONE
									: View.VISIBLE);
						}
					});
		} else {
			mEditUserStatusView.setVisibility(show ? View.VISIBLE : View.GONE);
			mEditUserFormView.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}
}
