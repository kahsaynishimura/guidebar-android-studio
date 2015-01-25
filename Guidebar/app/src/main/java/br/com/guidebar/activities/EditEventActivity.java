package br.com.guidebar.activities;

import android.app.Activity;

public class EditEventActivity extends Activity {

//	private LoadEventTask mLoadEventTask;
//	private EditEventTask mEditEventTask;
//	private SendEmailTask mSendEmailTask;
//
//	// Evento a ser editado
//	Evento evento = new Evento();
//
//	// String values
//	private String mName;
//	private String mDescription;
//	private String mStartDate;
//	private String mEndDate;
//	private String mStartTime;
//	private String mEndTime;
//	private String mMinAge;
//	private Integer mCategory;
//	private Integer mOpenBar;
//
//	// UI references.
//
//	private View mEditEventFormView;
//	private View mEditEventStatusView;
//	private TextView mEditEventStatusMessageView;
//
//	private EditText mNameView;
//	private EditText mDescriptionView;
//	private DateDisplayPicker mStartDateView;
//	private DateDisplayPicker mEndDateView;
//	private TimeDisplayPicker mStartTimeView;
//	private TimeDisplayPicker mEndTimeView;
//	private EditText mMinAgeView;
//	private Spinner mCategoryView;
//	private CheckBox mOpenBarView;
//	private SessionManager session;
//
//	private ArrayList<String> lstAssinantes = new ArrayList<String>();
//
//	@Override
//	protected void onCreate(Bundle savedInstanceState) {
//		super.onCreate(savedInstanceState);
//
//		setContentView(R.layout.activity_edit_event);
//		loadComponents();
//	}
//
//	private void loadComponents() {
//		// Session class instance
//
//		session = new SessionManager(getApplicationContext());
//		session.checkLogin();
//		mNameView = (EditText) findViewById(R.id.txtName);
//		mDescriptionView = (EditText) findViewById(R.id.txtDescription);
//		mStartDateView = (DateDisplayPicker) findViewById(R.id.txtStartDate);
//		mStartTimeView = (TimeDisplayPicker) findViewById(R.id.txtStartTime);
//		mEndDateView = (DateDisplayPicker) findViewById(R.id.txtEndDate);
//		mEndTimeView = (TimeDisplayPicker) findViewById(R.id.txtEndTime);
//		mMinAgeView = (EditText) findViewById(R.id.txtMinAge);
//		mOpenBarView = (CheckBox) findViewById(R.id.chkOpenBar);
//		mCategoryView = (Spinner) findViewById(R.id.spnCategory);
//		mEditEventFormView = findViewById(R.id.edit_event_form);
//		mEditEventStatusView = findViewById(R.id.status);
//		mEditEventStatusMessageView = (TextView) findViewById(R.id.status_message);
//
//		TextView txtCategoryView = (TextView) findViewById(R.id.lblCategory);
//		TextView txtNameView = (TextView) findViewById(R.id.lblName);
//		TextView txtDescriptionView = (TextView) findViewById(R.id.lblDescription);
//		TextView txtFromView = (TextView) findViewById(R.id.lblFrom);
//		TextView txtToView = (TextView) findViewById(R.id.lblTo);
//		TextView txtMinAgeView = (TextView) findViewById(R.id.lblMinAge);
//		TextView txtNewEvent = (TextView) findViewById(R.id.lblEditEvent);
//		Button btnSave = (Button) findViewById(R.id.btnSave);
//		Button btnNext = (Button) findViewById(R.id.btnNext);
//		Typeface tf = Typeface.createFromAsset(getAssets(),
//				"fonts/Gotham-Book.otf");
//		mNameView.setTypeface(tf);
//		mDescriptionView.setTypeface(tf);
//		mStartDateView.setTypeface(tf);
//		mEndDateView.setTypeface(tf);
//		mStartTimeView.setTypeface(tf);
//		mEndTimeView.setTypeface(tf);
//		mMinAgeView.setTypeface(tf);
//		mOpenBarView.setTypeface(tf);
//		mEditEventStatusMessageView.setTypeface(tf);
//		txtCategoryView.setTypeface(tf);
//		txtNameView.setTypeface(tf);
//		txtDescriptionView.setTypeface(tf);
//		txtFromView.setTypeface(tf);
//		txtToView.setTypeface(tf);
//		txtMinAgeView.setTypeface(tf);
//		txtNewEvent.setTypeface(tf);
//		btnSave.setTypeface(tf);
//		btnNext.setTypeface(tf);
//
//		findViewById(R.id.btnSave).setOnClickListener(
//				new View.OnClickListener() {
//					@Override
//					public void onClick(View view) {
//						ConnectionDetector cd = new ConnectionDetector(
//								getApplicationContext());
//						Boolean isInternetPresent = cd.isConnectingToInternet();
//						if (isInternetPresent) {
//							attemptSave();
//						} else {
//							Toast.makeText(EditEventActivity.this,
//									R.string.error_network_connection,
//									Toast.LENGTH_SHORT).show();
//						}
//					}
//				});
//		Bundle parametros = getIntent().getExtras();
//		evento.setId(parametros.getInt("id"));
//		evento.setCategoria(new Categoria());
//		evento.setPromotor(new Usuario());
//
//		mEditEventStatusMessageView.setText(R.string.progress_loading_data);
//		showProgress(true);
//		mLoadEventTask = new LoadEventTask();
//		mLoadEventTask.execute((Void) null);
//	}
//
//	public class SendEmailTask extends AsyncTask<Void, Void, Void> {
//		@Override
//		protected Void doInBackground(Void... params) {
//
//			String url = Guidebar.serverUrl
//					+ "buscarAssinantesParticipantes.php";
//
//			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
//			parametrosPost.add(new BasicNameValuePair("id_evento", evento
//					.getId().toString()));
//
//			String respostaRetornada = "";
//			String resposta = "";
//			try {
//				respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
//						parametrosPost);
//				resposta = respostaRetornada.toString();
//
//				char character_lido = resposta.charAt(0);
//				String str = "";
//
//				for (int i = 0; character_lido != '^'; i++) {
//					character_lido = resposta.charAt(i);
//					if (character_lido != '#') {
//						str += character_lido;
//					} else {
//						lstAssinantes.add(str);
//						str = "";
//					}
//				}
//			} catch (Exception erro) {
//				Log.i("erro", "erro = " + erro);
//			}
//			if (lstAssinantes.size() > 0) {
//				String bodyText = String
//						.format("O evento \"%1$s\" foi alterado. \n\nDescrição do evento: %2$s. \n Acesse o aplicativo GuideBar para visualizar este evento. ",
//								evento.getNome(), evento.getDescricao());
//				url = Guidebar.serverUrl + "mail.php";
//				parametrosPost = new ArrayList<NameValuePair>();
//				parametrosPost.add(new BasicNameValuePair("to", lstAssinantes
//						.toString()));
//				parametrosPost.add(new BasicNameValuePair("subject",
//						getString(R.string.email_edit_event_subject)));
//				parametrosPost
//						.add(new BasicNameValuePair("bodyText", bodyText));
//				try {
//					respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
//							parametrosPost);
//					resposta = respostaRetornada.toString();
//					resposta = resposta.replaceAll("\\s+", "");
//
//				} catch (Exception erro) {
//					Log.i("erro", "erro = " + erro);
//				}
//
//			}
//			return null;
//		}
//
//		@Override
//		protected void onPostExecute(Void params) {
//			mSendEmailTask = null;
//		}
//
//		@Override
//		protected void onCancelled() {
//			mSendEmailTask = null;
//		}
//	}
//
//	class LoadEventTask extends AsyncTask<Void, Void, Void> {
//		// JSON Node names
//		private static final String TAG_ID = "id";
//		private static final String TAG_EVENTOS = "events";
//		private static final String TAG_NOME = "name";
//		private static final String TAG_DESCRICAO = "description";
//		private static final String TAG_DATA_INICIO = "start_date";
//		private static final String TAG_DATA_FIM = "end_date";
//		private static final String TAG_IDADE_MINIMA = "minimum_age";
//		private static final String TAG_VISUALIZACOES = "views";
//		private static final String TAG_ID_CATEGORIA = "category_id";
//		private static final String TAG_ID_PROMOTOR = "user_is";
//		private static final String TAG_LATITUDE = "latitude";
//		private static final String TAG_LONGITUDE = "longitude";
//		private static final String TAG_IS_OPEN_BAR = "is_open_bar";
//		JSONArray eventos = null;
//
//		@Override
//		protected Void doInBackground(Void... params) {
//			String url = Guidebar.serverUrl + "buscarEditarEvento.php";
//
//			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
//			parametrosPost.add(new BasicNameValuePair("id", evento.getId()
//					.toString()));
//
//			String respostaRetornada = "";
//			try {
//				respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
//						parametrosPost);
//				String resposta = respostaRetornada.toString();
//				JSONObject json = new JSONObject(resposta);
//				try {
//					eventos = json.getJSONArray(TAG_EVENTOS);
//					for (int i = 0; i < eventos.length(); i++) {
//						JSONObject c = eventos.getJSONObject(i);
//						evento.setId(Integer.parseInt(c.getString(TAG_ID)));
//						evento.setNome(c.getString(TAG_NOME));
//						evento.setDescricao(c.getString(TAG_DESCRICAO));
//						evento.setDataInicio(c.getString(TAG_DATA_INICIO));
//						evento.setDataFim(c.getString(TAG_DATA_FIM));
//						evento.setIdadeMinima(Integer.parseInt(c
//								.getString(TAG_IDADE_MINIMA)));
//						evento.setVisualizacoes(Integer.parseInt(c
//								.getString(TAG_VISUALIZACOES)));
//						evento.setCategoria(new Categoria(Integer.parseInt(c
//								.getString(TAG_ID_CATEGORIA))));
//						evento.setPromotor(new Usuario(Integer.parseInt(c
//								.getString(TAG_ID_PROMOTOR))));
//						evento.setLatitude(c.getString(TAG_LATITUDE));
//						evento.setLongitude(c.getString(TAG_LONGITUDE));
////						evento.setIsOpenBar(Integer.parseInt(c
////								.getString(TAG_OPEN_BAR)));
//					}
//				} catch (JSONException e) {
//					e.printStackTrace();
//				}
//
//			} catch (Exception erro) {
//				Log.i("erro", "erro = " + erro);
//			}
//			return null;
//		}
//
//		@Override
//		protected void onPostExecute(final Void a) {
//			mLoadEventTask = null;
//			showProgress(false);
//			mNameView.setText(evento.getNome());
//			mDescriptionView.setText(evento.getDescricao());
//			if (evento.getDataInicio() != null) {
//				mStartDateView.setText(mStartDateView
//						.getDateForPresenting(evento.getDataInicio().substring(
//								0, 10)));
//				mEndDateView.setText(mEndDateView.getDateForPresenting(evento
//						.getDataFim().substring(0, 10)));
//				mStartTimeView
//						.setText(evento.getDataInicio().substring(11, 16));
//				mEndTimeView.setText(evento.getDataFim().substring(11, 16));
//				mMinAgeView.setText(evento.getIdadeMinima().toString());
////				mOpenBarView.setChecked(evento.getIsOpenBar() == 1 ? true
////						: false);
//			}
//
//			ArrayAdapter<ECategoria> aa = new ArrayAdapter<ECategoria>(
//					EditEventActivity.this,
//					android.R.layout.simple_list_item_1, ECategoria.values());
//			mCategoryView.setAdapter(aa);
//			mCategoryView.setSelection(evento.getCategoria().getId() - 1);
//		}
//
//		@Override
//		protected void onCancelled() {
//			mLoadEventTask = null;
//			showProgress(false);
//		}
//	}
//
//	public void attemptSave() {
//		if (mEditEventTask != null) {
//			return;
//		}
//
//		// Reset errors.
//		mNameView.setError(null);
//		mDescriptionView.setError(null);
//		mStartDateView.setError(null);
//		mEndDateView.setError(null);
//		mStartTimeView.setError(null);
//		mEndTimeView.setError(null);
//		mMinAgeView.setError(null);
//
//		// Store values at the time of the login attempt.
//
//		mName = mNameView.getText().toString();
//		mDescription = mDescriptionView.getText().toString();
//		mStartDate = mStartDateView.getText().toString();
//		mEndDate = mEndDateView.getText().toString();
//		mStartTime = mStartTimeView.getText().toString();
//		mEndTime = mEndTimeView.getText().toString();
//		mMinAge = mMinAgeView.getText().toString();
//		mOpenBar = mOpenBarView.isChecked() ? 1 : 0;
//		mCategory = ((ECategoria) mCategoryView.getSelectedItem()).getId();
//
//		Calendar startDate = Calendar.getInstance();
//		startDate
//				.set(Calendar.YEAR, DateDisplayPicker.getYear(mStartDate, "/"));
//		startDate.set(Calendar.DAY_OF_MONTH,
//				DateDisplayPicker.getDay(mStartDate, "/"));
//		startDate.set(Calendar.MONTH,
//				((DateDisplayPicker.getMonth(mStartDate, "/")) - 1));
//		startDate.set(Calendar.HOUR_OF_DAY,
//				TimeDisplayPicker.getHour(mStartTime));
//		startDate.set(Calendar.MINUTE, TimeDisplayPicker.getMinute(mStartTime));
//
//		Calendar endDate = Calendar.getInstance();
//		endDate.set(Calendar.YEAR, DateDisplayPicker.getYear(mEndDate, "/"));
//		endDate.set(Calendar.DAY_OF_MONTH,
//				DateDisplayPicker.getDay(mEndDate, "/"));
//		endDate.set(Calendar.MONTH,
//				((DateDisplayPicker.getMonth(mEndDate, "/")) - 1));
//		endDate.set(Calendar.HOUR_OF_DAY, TimeDisplayPicker.getHour(mEndTime));
//		endDate.set(Calendar.MINUTE, TimeDisplayPicker.getMinute(mEndTime));
//		boolean cancel = false;
//		View focusView = null;
//
//		// Check for a valid name.
//		if (TextUtils.isEmpty(mName)) {
//			mNameView.setError(getString(R.string.error_field_required));
//			focusView = mNameView;
//			cancel = true;
//		}
//
//		// Check for a valid description.
//		if (TextUtils.isEmpty(mDescription)) {
//			mDescriptionView.setError(getString(R.string.error_field_required));
//			focusView = mDescriptionView;
//			cancel = true;
//		}
//
//		// Check for a valid start date
//		if (TextUtils.isEmpty(mStartDate)) {
//			mStartDateView.setError(getString(R.string.error_field_required));
//			focusView = mStartDateView;
//			cancel = true;
//		} else if (startDate.before(Calendar.getInstance())) {
//			mStartDateView
//					.setError(getString(R.string.error_invalid_start_date));
//			focusView = mStartDateView;
//			cancel = true;
//		}
//		// Check for a valid end date.
//		if (TextUtils.isEmpty(mEndDate)) {
//			mEndDateView.setError(getString(R.string.error_field_required));
//			focusView = mEndDateView;
//			cancel = true;
//		} else if (endDate.before(startDate)) {
//			mEndDateView.setError(getString(R.string.error_invalid_end_date));
//			focusView = mEndDateView;
//			cancel = true;
//		}
//
//		// Check for a valid start time
//		if (TextUtils.isEmpty(mStartTime)) {
//			mStartDateView.setError(getString(R.string.error_field_required));
//			focusView = mStartDateView;
//			cancel = true;
//		}
//		// Check for a valid end time.
//		if (TextUtils.isEmpty(mEndTime)) {
//			mEndDateView.setError(getString(R.string.error_field_required));
//			focusView = mEndDateView;
//			cancel = true;
//		}
//
//		// Check for a valid end date.
//		if (TextUtils.isEmpty(mMinAge)) {
//			mMinAgeView.setError(getString(R.string.error_field_required));
//			focusView = mMinAgeView;
//			cancel = true;
//		}
//
//		if (cancel) {
//			focusView.requestFocus();
//		} else {
//			mEditEventStatusMessageView.setText(R.string.progress_saving_data);
//			showProgress(true);
//			mEditEventTask = new EditEventTask();
//			mEditEventTask.execute((Void) null);
//		}
//	}
//
//	public class EditEventTask extends AsyncTask<Void, Void, Boolean> {
//
//		@Override
//		protected Boolean doInBackground(Void... params) {
//			final String SUCCESSFUL = "1";
//			String urlPost = Guidebar.serverUrl + "alterarEvento.php";
//			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
//			String startDate = mStartDateView.getDateForPersistence(mStartDate)
//					+ " " + mStartTimeView.getTimeForPersistence(mStartTime);
//			String endDate = mEndDateView.getDateForPersistence(mEndDate) + " "
//					+ mEndTimeView.getTimeForPersistence(mEndTime);
//
//			parametrosPost.add(new BasicNameValuePair("id", evento.getId()
//					.toString()));
//			parametrosPost.add(new BasicNameValuePair("nome", mName));
//			parametrosPost
//					.add(new BasicNameValuePair("descricao", mDescription));
//			parametrosPost
//					.add(new BasicNameValuePair("data_inicio", startDate));
//			parametrosPost.add(new BasicNameValuePair("data_fim", endDate));
//			parametrosPost.add(new BasicNameValuePair("idade_minima", mMinAge));
//			parametrosPost.add(new BasicNameValuePair("open_bar", mOpenBar
//					.toString()));
//			parametrosPost.add(new BasicNameValuePair("id_categoria", mCategory
//					.toString()));
//
//			String respostaRetornada = "";
//
//			String resposta = "";
//			try {
//				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
//						parametrosPost);
//				resposta = respostaRetornada.toString();
//				resposta = resposta.replaceAll("\\s+", "");
//			} catch (Exception erro) {
//				Toast.makeText(EditEventActivity.this, "Erro: " + erro,
//						Toast.LENGTH_SHORT).show();
//			}
//			return resposta.equals(SUCCESSFUL);
//		}
//
//		@Override
//		protected void onPostExecute(final Boolean success) {
//			mEditEventTask = null;
//
//			if (success) {
//
//				showProgress(false);
//				Toast.makeText(EditEventActivity.this,
//						R.string.success_save_event, Toast.LENGTH_SHORT).show();
//
//				Intent i = new Intent(EditEventActivity.this,
//						AddAddressActivity.class);
//				i.putExtra("id_evento", evento.getId());
//				i.putExtra("editar_evento", true);
//				startActivity(i);
//				finish();
//
//				mSendEmailTask = new SendEmailTask();
//				mSendEmailTask.execute();
//
//			} else {
//				showProgress(false);
//				Toast.makeText(EditEventActivity.this,
//						R.string.error_save_event, Toast.LENGTH_SHORT).show();
//				mNameView.requestFocus();
//			}
//		}
//
//		@Override
//		protected void onCancelled() {
//			mEditEventTask = null;
//			showProgress(false);
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
//			mEditEventStatusView.setVisibility(View.VISIBLE);
//			mEditEventStatusView.animate().setDuration(shortAnimTime)
//					.alpha(show ? 1 : 0)
//					.setListener(new AnimatorListenerAdapter() {
//						@Override
//						public void onAnimationEnd(Animator animation) {
//							mEditEventStatusView
//									.setVisibility(show ? View.VISIBLE
//											: View.GONE);
//						}
//					});
//
//			mEditEventFormView.setVisibility(View.VISIBLE);
//			mEditEventFormView.animate().setDuration(shortAnimTime)
//					.alpha(show ? 0 : 1)
//					.setListener(new AnimatorListenerAdapter() {
//						@Override
//						public void onAnimationEnd(Animator animation) {
//							mEditEventFormView.setVisibility(show ? View.GONE
//									: View.VISIBLE);
//						}
//					});
//		} else {
//			mEditEventStatusView.setVisibility(show ? View.VISIBLE : View.GONE);
//			mEditEventFormView.setVisibility(show ? View.GONE : View.VISIBLE);
//		}
//	}
//
//	public void next(View v) {
//		Intent intent = new Intent(EditEventActivity.this,
//				AddAddressActivity.class);
//		intent.putExtra("id_evento", evento.getId());
//		intent.putExtra("editar_evento", true);
//		startActivity(intent);
//		finish();
//	}

}
