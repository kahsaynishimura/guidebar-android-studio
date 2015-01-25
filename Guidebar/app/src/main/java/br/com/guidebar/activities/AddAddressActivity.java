package br.com.guidebar.activities;

import java.util.ArrayList;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Intent;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.bean.Cidade;
import br.com.guidebar.bean.Endereco;
import br.com.guidebar.bean.Estado;
import br.com.guidebar.bean.Evento;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.Guidebar;

public class AddAddressActivity extends Activity {
	private Evento evento = new Evento();
	private View mAddAddressFormView;
	private View mAddAddressStatusView;
	private TextView mAddAddressStatusMessageView;
	private TextView lblStreet;
	private TextView lblNumero;
	private TextView lblBairro;
	private TextView lblComplemento;
	private TextView lblCEP;
	private TextView lblState;
	private TextView lblCity;
	private EditText txtStreet;
	private EditText txtNumero;
	private EditText txtBairro;
	private EditText txtComplemento;
	private EditText txtCEP;
	private Spinner txtState;
	private ArrayList<Cidade> listaCidadesPorEstado = new ArrayList<Cidade>();
	private AutoCompleteTextView txtCity;
	private LoadCitiesTask mLoadCitiesTask;
	private LoadStatesTask mLoadStatesTask;
	private AddAddressTask mAddAddressTask;
	private CarregarEnderecoTask mCarregarEnderecoTask;

	private String mStreet;
	private String mNumero;
	private String mBairro;
	private String mComplemento;
	private String mCEP;
	private String mCity;
	private String mState;
	private Endereco endereco = new Endereco();
	Cidade cidadeEscolhida = new Cidade();
	private boolean editarEvento = false;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_add_address);
		lodComponents();
	}

	private void lodComponents() {
		Bundle parametros = getIntent().getExtras();
		evento.setId(parametros.getInt("id_evento"));
		editarEvento = parametros.getBoolean("editar_evento");
		mAddAddressFormView = findViewById(R.id.add_address_form);
		mAddAddressStatusView = findViewById(R.id.status);
		mAddAddressStatusMessageView = (TextView) findViewById(R.id.status_message);
		lblStreet = (TextView) findViewById(R.id.lblStreet);
		lblNumero = (TextView) findViewById(R.id.lblNumero);
		lblBairro = (TextView) findViewById(R.id.lblBairro);
		lblComplemento = (TextView) findViewById(R.id.lblComplemento);
		lblCEP = (TextView) findViewById(R.id.lblCEP);
		lblState = (TextView) findViewById(R.id.lblState);
		lblCity = (TextView) findViewById(R.id.lblCity);
		txtStreet = (EditText) findViewById(R.id.txtStreet);
		txtNumero = (EditText) findViewById(R.id.txtNumero);
		txtBairro = (EditText) findViewById(R.id.txtBairro);
		txtComplemento = (EditText) findViewById(R.id.txtComplemento);
		txtCEP = (EditText) findViewById(R.id.txtCEP);
		txtState = (Spinner) findViewById(R.id.txtState);
		txtCity = (AutoCompleteTextView) findViewById(R.id.txtCity);
		Button btnSave = (Button) findViewById(R.id.btnSave);
		Button btnNext = (Button) findViewById(R.id.btnNext);
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		lblStreet.setTypeface(tf);
		lblNumero.setTypeface(tf);
		lblBairro.setTypeface(tf);
		lblComplemento.setTypeface(tf);
		lblCEP.setTypeface(tf);
		lblState.setTypeface(tf);
		lblCity.setTypeface(tf);
		txtStreet.setTypeface(tf);
		txtNumero.setTypeface(tf);
		txtBairro.setTypeface(tf);
		txtComplemento.setTypeface(tf);
		txtCEP.setTypeface(tf);
		// TODO txtState.setTypeface(tf);
		txtCity.setTypeface(tf);

		btnSave.setTypeface(tf);
		btnNext.setTypeface(tf);
		mAddAddressStatusMessageView.setTypeface(tf);
		txtState.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {

			@Override
			public void onItemSelected(AdapterView<?> parent, View view,
					int position, long id) {
				Estado estado = (Estado) parent.getItemAtPosition(position);
				loadCities(estado.getId());
			}

			@Override
			public void onNothingSelected(AdapterView<?> arg0) {

			}
		});

		loadAddress();
	}

	private void loadAddress() {
		mAddAddressStatusMessageView.setText(R.string.progress_loading_data);
		showProgress(true);
		if (editarEvento) {
			mCarregarEnderecoTask = new CarregarEnderecoTask();
			mCarregarEnderecoTask.execute();
		} else {
			mLoadStatesTask = new LoadStatesTask();
			mLoadStatesTask.execute();
		}
	}

	private void loadCities(int position) {
		mAddAddressStatusMessageView.setText(R.string.progress_loading_cities);
		showProgress(true);
		mLoadCitiesTask = new LoadCitiesTask();
		mLoadCitiesTask.execute(position);
	}

	public void attemptSave(View v) {
		if (mAddAddressTask != null) {
			return;
		}
		txtStreet.setError(null);
		txtNumero.setError(null);
		txtBairro.setError(null);
		txtComplemento.setError(null);
		txtCEP.setError(null);
		txtCity.setError(null);

		mStreet = txtStreet.getText().toString();
		mNumero = txtNumero.getText().toString();
		mComplemento = txtComplemento.getText().toString();
		mBairro = txtBairro.getText().toString();
		mCEP = txtCEP.getText().toString();
		mCity = txtCity.getText().toString();
		mState = ((Estado) txtState.getSelectedItem()).getId().toString();

		boolean cancel = false;
		View focusView = null;
		boolean valid = false;
		for (Cidade obj : listaCidadesPorEstado) {
			if (mCity.equals(obj.getNome())) {
				valid = true;
				cidadeEscolhida.setId(obj.getId());
			}
		}
		if (!valid) {
			txtCity.setError(getString(R.string.error_invalid_city));
			focusView = txtCity;
			cancel = true;
		}
		if (TextUtils.isEmpty(mStreet)) {
			txtStreet.setError(getString(R.string.error_field_required));
			focusView = txtStreet;
			cancel = true;
		}
		if (TextUtils.isEmpty(mNumero)) {
			txtNumero.setError(getString(R.string.error_field_required));
			focusView = txtNumero;
			cancel = true;
		}
		if (TextUtils.isEmpty(mBairro)) {
			txtBairro.setError(getString(R.string.error_field_required));
			focusView = txtBairro;
			cancel = true;
		}
		if (TextUtils.isEmpty(mCEP)) {
			txtCEP.setError(getString(R.string.error_field_required));
			focusView = txtCEP;
			cancel = true;
		} else if (mCEP.length() != 8) {
			txtCEP.setError(getString(R.string.error_invalid_cep));
			focusView = txtCEP;
			cancel = true;
		} else {
			try {
				Integer.parseInt(mCEP);
			} catch (NumberFormatException e) {
				txtCEP.setError(getString(R.string.error_number_required));
				focusView = txtCEP;
				cancel = true;
			}
		}

		if (cancel) {
			focusView.requestFocus();
		} else {
			mAddAddressStatusMessageView.setText(R.string.progress_saving_data);
			showProgress(true);
			mAddAddressTask = new AddAddressTask();
			mAddAddressTask.execute();
		}
	}

	public void next(View v) {
		Intent intent = new Intent(AddAddressActivity.this,
				EventPositionActivity.class);
		intent.putExtra("id_evento", evento.getId());
		startActivity(intent);
		finish();
	}

	public class CarregarEnderecoTask extends AsyncTask<Void, Void, Boolean> {

		// JSON Node names
		private static final String TAG_RUA = "rua";
		private static final String TAG_ENDERECO = "endereco";
		private static final String TAG_NUMERO = "numero";
		private static final String TAG_BAIRRO = "bairro";
		private static final String TAG_COMPLEMENTO = "complemento";
		private static final String TAG_CEP = "cep";
		private static final String TAG_ID_CIDADE = "id_cidade";
		private static final String TAG_ID_ESTADO = "estado";
		private static final String TAG_NOME_CIDADE = "nome_cidade";
		private static final String TAG_ID_ENDE = "id_ende";

		@Override
		protected Boolean doInBackground(Void... params) {
			String url = Guidebar.serverUrl + "buscarEndereco.php";

			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("id_evento", evento
					.getId().toString()));

			String respostaRetornada = "";
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
						parametrosPost);
				resposta = respostaRetornada.toString();
				if (!resposta.replaceAll("\\s+", "").equals("[]")) {
					JSONObject json = new JSONObject(resposta);
					try {
						JSONArray enderecoArray = json
								.getJSONArray(TAG_ENDERECO);
						for (int i = 0; i < enderecoArray.length(); i++) {
							JSONObject c = enderecoArray.getJSONObject(i);

							endereco.setId(Integer.parseInt(c
									.getString(TAG_ID_ENDE)));
							endereco.setRua(c.getString(TAG_RUA));
							endereco.setNumero(c.getString(TAG_NUMERO));
							endereco.setComplemento(c
									.getString(TAG_COMPLEMENTO) == "null" ? ""
									: c.getString(TAG_COMPLEMENTO));
							endereco.setBairro(c.getString(TAG_BAIRRO));
							endereco.setCep(c.getString(TAG_CEP));
							endereco.getCidade()
									.setId(Integer.parseInt(c
											.getString(TAG_ID_CIDADE)));
							endereco.getCidade().setNome(
									c.getString(TAG_NOME_CIDADE));
							endereco.getCidade()
									.getEstado()
									.setId(Integer.parseInt(c
											.getString(TAG_ID_ESTADO)));
						}
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}
			return (!resposta.replaceAll("\\s+", "").equals("[]"));
		}

		@Override
		protected void onCancelled() {
			mCarregarEnderecoTask = null;
			showProgress(false);
		}

		@Override
		protected void onPostExecute(Boolean success) {
			mCarregarEnderecoTask = null;
			showProgress(false);
			if (success) {
				txtStreet.setText(endereco.getRua());
				txtNumero.setText(endereco.getNumero());
				txtBairro.setText(endereco.getBairro());
				txtComplemento.setText(endereco.getComplemento());
				txtCEP.setText(endereco.getCep());
			}
			mLoadStatesTask = new LoadStatesTask();
			mLoadStatesTask.execute();
		}

	}

	public class AddAddressTask extends AsyncTask<Void, Void, Boolean> {

		@Override
		protected Boolean doInBackground(Void... params) {
			String urlPost = Guidebar.serverUrl + "gravarEndereco.php";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();

			parametrosPost.add(new BasicNameValuePair("id_evento", evento
					.getId().toString()));
			parametrosPost.add(new BasicNameValuePair("rua", mStreet));
			parametrosPost.add(new BasicNameValuePair("numero", mNumero));
			parametrosPost.add(new BasicNameValuePair("complemento",
					mComplemento));
			parametrosPost.add(new BasicNameValuePair("bairro", mBairro));
			parametrosPost.add(new BasicNameValuePair("cep", mCEP));
			parametrosPost.add(new BasicNameValuePair("id_cidade",
					cidadeEscolhida.getId().toString()));
			parametrosPost.add(new BasicNameValuePair("id_estado", mState));

			String respostaRetornada = "";

			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				resposta = resposta.replaceAll("\\s+", "");
			} catch (Exception erro) {
				Log.i("AddAddressActivity", "Erro: " + erro);
			}
			return resposta.equals("1");
		}

		@Override
		protected void onCancelled() {
			mAddAddressTask = null;
			showProgress(false);
		}

		@Override
		protected void onPostExecute(Boolean success) {
			mAddAddressTask = null;
			showProgress(false);
			if (success) {
				Intent intent = new Intent(AddAddressActivity.this,
						EventPositionActivity.class);
				intent.putExtra("id_evento", evento.getId());
				startActivity(intent);
				finish();
			} else {
				Toast.makeText(AddAddressActivity.this,
						getString(R.string.error_save_address),
						Toast.LENGTH_SHORT).show();
			}
		}
	}

	public class LoadStatesTask extends
			AsyncTask<Void, Void, ArrayList<Estado>> {
		// JSON Node names
		private static final String TAG_ID = "id";
		private static final String TAG_NOME = "nome";
		private static final String TAG_ESTADOS = "estados";

		@Override
		protected ArrayList<Estado> doInBackground(Void... params) {
			ArrayList<Estado> listaEstados = new ArrayList<Estado>();
			String url = Guidebar.serverUrl + "listarEstados.php";

			String respostaRetornada = "";
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpGet(url);
				resposta = respostaRetornada.toString();
				if (!resposta.replaceAll("\\s+", "").equals("[]")) {
					JSONObject json = new JSONObject(resposta);
					try {
						JSONArray array = json.getJSONArray(TAG_ESTADOS);
						for (int i = 0; i < array.length(); i++) {
							JSONObject c = array.getJSONObject(i);
							int id = Integer.parseInt(c.getString(TAG_ID));
							String nome = c.getString(TAG_NOME);
							listaEstados.add(new Estado(id, nome));
						}
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}

			return listaEstados;
		}

		@Override
		protected void onCancelled() {
			mLoadStatesTask = null;
			showProgress(false);
		}

		@Override
		protected void onPostExecute(ArrayList<Estado> listaEstados) {
			mLoadStatesTask = null;
			showProgress(false);
			ArrayAdapter<Estado> aa = new ArrayAdapter<Estado>(
					AddAddressActivity.this,
					android.R.layout.simple_list_item_1, listaEstados);

			txtState.setAdapter(aa);
			mAddAddressStatusMessageView
					.setText(getString(R.string.progress_loading_data));
			if (endereco.getCidade().getEstado().getId() != null
					&& editarEvento) {
				txtState.setSelection(Estado.getPositionById(
						txtState.getAdapter(), endereco.getCidade().getEstado()
								.getId()));
			} else {
				txtState.setSelection(15);
			}
		}
	}

	public class LoadCitiesTask extends
			AsyncTask<Integer, Void, ArrayList<Cidade>> {

		// JSON Node names
		private static final String TAG_ID = "id";
		private static final String TAG_ID_ESTADO = "estado";
		private static final String TAG_NOME = "nome";
		private static final String TAG_CIDADES = "cidades";
		JSONArray cidades = null;

		@Override
		protected ArrayList<Cidade> doInBackground(Integer... params) {
			ArrayList<Cidade> listaCidades = new ArrayList<Cidade>();
			String url = Guidebar.serverUrl + "listarCidadesPorEstado.php";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();

			parametrosPost.add(new BasicNameValuePair("id_estado", params[0]
					.toString()));

			String respostaRetornada = "";
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
						parametrosPost);
				resposta = respostaRetornada.toString();
				if (!resposta.replaceAll("\\s+", "").equals("[]")) {
					JSONObject json = new JSONObject(resposta);
					try {
						cidades = json.getJSONArray(TAG_CIDADES);
						for (int i = 0; i < cidades.length(); i++) {
							JSONObject c = cidades.getJSONObject(i);
							Cidade obj = new Cidade();

							obj.setId(Integer.parseInt(c.getString(TAG_ID)));
							obj.setNome(c.getString(TAG_NOME));
							obj.getEstado()
									.setId(Integer.parseInt(c
											.getString(TAG_ID_ESTADO)));
							listaCidades.add(obj);
						}
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}

			return listaCidades;
		}

		@Override
		protected void onCancelled() {
			mLoadCitiesTask = null;
			showProgress(false);
		}

		@Override
		protected void onPostExecute(ArrayList<Cidade> listaCidades) {
			mLoadCitiesTask = null;
			showProgress(false);

			listaCidadesPorEstado = listaCidades;
			ArrayAdapter<Cidade> aa = new ArrayAdapter<Cidade>(
					AddAddressActivity.this,
					android.R.layout.simple_dropdown_item_1line, listaCidades);
			txtCity.setAdapter(aa);
			if (endereco.getCidade().getId() != null && editarEvento) {
				txtCity.setText(endereco.getCidade().getNome());
				editarEvento = false;
			} else {
				txtCity.setText("");
			}
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

			mAddAddressStatusView.setVisibility(View.VISIBLE);
			mAddAddressStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mAddAddressStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mAddAddressFormView.setVisibility(View.VISIBLE);
			mAddAddressFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mAddAddressFormView.setVisibility(show ? View.GONE
									: View.VISIBLE);
						}
					});
		} else {
			mAddAddressStatusView
					.setVisibility(show ? View.VISIBLE : View.GONE);
			mAddAddressFormView.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}
}
