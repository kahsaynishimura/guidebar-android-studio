package br.com.guidebar.activities;

import java.util.ArrayList;
import java.util.Calendar;

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
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.bean.Categoria;
import br.com.guidebar.bean.Cidade;
import br.com.guidebar.bean.Estado;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.ConnectionDetector;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.SessionManager;
import br.com.guidebar.customviews.DateDisplayPicker;
import br.com.guidebar.enums.EnumCategoria.ECategoria;

public class FiltersActivity extends Activity {
	private Spinner spnState;
	private Spinner spnCategory;
	private CheckBox chkOnlyOpenBar;
	private AutoCompleteTextView txtCity;
	private DateDisplayPicker txtStartDate;
	private View mFiltersFormView;
	private View mFiltersStatusView;
	private TextView mFiltersStatusMessageView;
	private LoadStatesTask mLoadStatesTask;
	private LoadCitiesTask mLoadCitiesTask;
	private ArrayList<Cidade> listaCidadesPorEstado = new ArrayList<Cidade>();
	Cidade cidadeEscolhida = new Cidade();

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_filters);
		loadComponents();
	}

	private void loadComponents() {
		mFiltersFormView = findViewById(R.id.filters_form);
		mFiltersStatusView = findViewById(R.id.status);
		mFiltersStatusMessageView = (TextView) findViewById(R.id.status_message);
		TextView lblCity = (TextView) findViewById(R.id.lblCity);
		TextView lblState = (TextView) findViewById(R.id.lblState);
		TextView lblDate = (TextView) findViewById(R.id.lblDate);
		spnState = (Spinner) findViewById(R.id.spnState);
		spnCategory = (Spinner) findViewById(R.id.spnCategory);
		chkOnlyOpenBar = (CheckBox) findViewById(R.id.chkOnlyOpenBar);
		txtStartDate = (DateDisplayPicker) findViewById(R.id.txtStartDate);
		txtCity = (AutoCompleteTextView) findViewById(R.id.txtCity);
		Button btnSearch = (Button) findViewById(R.id.btnSearch);
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		lblCity.setTypeface(tf);
		lblState.setTypeface(tf);
		lblDate.setTypeface(tf);
		btnSearch.setTypeface(tf);
		chkOnlyOpenBar.setTypeface(tf);
		txtStartDate.setTypeface(tf);
		txtCity.setTypeface(tf);
		mFiltersStatusMessageView.setTypeface(tf);

		setCurrentDateOnView();

		spnState.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {

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

		loadStates();
	}

	// set current date into textview
	private void setCurrentDateOnView() {
		int year, month, day;
		final Calendar c = Calendar.getInstance();
		year = c.get(Calendar.YEAR);
		month = c.get(Calendar.MONTH);
		day = c.get(Calendar.DAY_OF_MONTH);

		txtStartDate.setText(new StringBuilder()
				// Month is 0 based, just add 1
				.append(String.format("%02d", day)).append("/")
				.append(String.format("%02d", month + 1)).append("/")
				.append(year));

	}

	private void loadCities(int position) {
		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent) {
			mFiltersStatusMessageView.setText(R.string.progress_loading_cities);
			showProgress(true);
			mLoadCitiesTask = new LoadCitiesTask();
			mLoadCitiesTask.execute(position);
		} else {
			Toast.makeText(FiltersActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}
	}

	private void loadStates() {
		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent) {
			mFiltersStatusMessageView.setText(R.string.progress_loading_data);
			showProgress(true);
			mLoadStatesTask = new LoadStatesTask();
			mLoadStatesTask.execute();
		} else {
			Toast.makeText(FiltersActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}
	}

	public class LoadStatesTask extends
			AsyncTask<Void, Void, ArrayList<Estado>> {

		// JSON Node names
		private static final String TAG_ID = "id";
		private static final String TAG_NOME = "name";
		private static final String TAG_ESTADOS = "states";
		private static final String TAG_STATE = "State";

		@Override
		protected ArrayList<Estado> doInBackground(Void... params) {
			ArrayList<Estado> listaEstados = new ArrayList<Estado>();
			listaEstados.add(new Estado(0, "[TODOS]"));
			String url = Guidebar.serverUrl + "states/index.json";

			String respostaRetornada = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpGet(url);
				String resposta = respostaRetornada.toString();

				JSONObject json = new JSONObject(resposta);
				try {
					JSONArray array = json.getJSONArray(TAG_ESTADOS);
					for (int i = 0; i < array.length(); i++) {

						JSONObject state = array.getJSONObject(i);
						JSONObject c = state.getJSONObject(TAG_STATE);

						int id = Integer.parseInt(c.getString(TAG_ID));
						String nome = c.getString(TAG_NOME);
						listaEstados.add(new Estado(id, nome));
					}
				} catch (JSONException e) {
					e.printStackTrace();
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
					FiltersActivity.this, android.R.layout.simple_list_item_1,
					listaEstados);
			spnState.setAdapter(aa);
			spnState.setSelection(16);
		}
	}

	public void filter(View v) {
		boolean valid = false;
		for (Cidade obj : listaCidadesPorEstado) {
			if ((txtCity.getText().toString()).equals(obj.getNome())) {
				valid = true;
				cidadeEscolhida.setId(obj.getId());
			}
		}
		if (valid) {
			Intent i = new Intent(FiltersActivity.this,
					ListEventsActivity.class);
			i.putExtra("state_id", ((Estado) spnState.getSelectedItem())
					.getId().toString());

			i.putExtra("city_id", cidadeEscolhida.getId().toString());

			i.putExtra("is_open_bar", chkOnlyOpenBar.isChecked() ? "1" : "-1");
			i.putExtra("category_id", ((Categoria) spnCategory
					.getSelectedItem()).getId().toString());
			i.putExtra("start_date", txtStartDate
					.getDateForPersistence(txtStartDate.getText().toString()));

			setResult(RESULT_OK, i);
			finish();
		} else {
			txtCity.setError(getString(R.string.error_invalid_city));
		}
	}

	public class LoadCitiesTask extends
			AsyncTask<Integer, Void, ArrayList<Cidade>> {
		// JSON Node names
		private static final String TAG_ID = "id";
		private static final String TAG_ID_ESTADO = "state_id";
		private static final String TAG_NOME = "name";
		private static final String TAG_CIDADES = "cities";
		private static final String TAG_CITY = "City";
		JSONArray cidades = null;

		@Override
		protected ArrayList<Cidade> doInBackground(Integer... params) {
			ArrayList<Cidade> listaCidades = new ArrayList<Cidade>();
			String url = Guidebar.serverUrl + "cities/getByStateSearch.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("data[Event][state_id]",
					params[0].toString()));
			SessionManager session = new SessionManager(FiltersActivity.this);
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			String respostaRetornada = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
						parametrosPost);
				String resposta = respostaRetornada.toString();

				JSONObject json = new JSONObject(resposta);
				try {
					cidades = json.getJSONArray(TAG_CIDADES);
					for (int i = 0; i < cidades.length(); i++) {
						JSONObject city = cidades.getJSONObject(i);
						JSONObject c = city.getJSONObject(TAG_CITY);
						Cidade obj = new Cidade();

						obj.setId(Integer.parseInt(c.getString(TAG_ID)));
						obj.setNome(c.getString(TAG_NOME));
						obj.getEstado().setId(
								Integer.parseInt(c.getString(TAG_ID_ESTADO)));
						listaCidades.add(obj);
					}
				} catch (JSONException e) {
					e.printStackTrace();
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
					FiltersActivity.this,
					android.R.layout.simple_dropdown_item_1line, listaCidades);

			txtCity.setAdapter(aa);

			loadCategories();
		}

	}

	private void loadCategories() {
		ArrayList<Categoria> lstCategoria = new ArrayList<Categoria>();
		lstCategoria.add(new Categoria(0, "[TODAS]"));

		for (ECategoria item : ECategoria.values()) {
			Categoria obj = new Categoria();
			obj.setNome(item.getNome());
			obj.setId(item.getId());
			lstCategoria.add(obj);
		}
		ArrayAdapter<Categoria> aa = new ArrayAdapter<Categoria>(
				FiltersActivity.this, android.R.layout.simple_list_item_1,
				lstCategoria);
		spnCategory.setAdapter(aa);
		spnCategory.setSelection(0);
	}

	@TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
	private void showProgress(final boolean show) {
		// On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
		// for very easy animations. If available, use these APIs to fade-in
		// the progress spinner.
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
			int shortAnimTime = getResources().getInteger(
					android.R.integer.config_shortAnimTime);

			mFiltersStatusView.setVisibility(View.VISIBLE);
			mFiltersStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mFiltersStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mFiltersFormView.setVisibility(View.VISIBLE);
			mFiltersFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mFiltersFormView.setVisibility(show ? View.GONE
									: View.VISIBLE);
						}
					});
		} else {
			mFiltersStatusView.setVisibility(show ? View.VISIBLE : View.GONE);
			mFiltersFormView.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}
}
