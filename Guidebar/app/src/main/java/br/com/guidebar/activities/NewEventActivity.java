package br.com.guidebar.activities;

import android.app.Activity;

public class NewEventActivity extends Activity {
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
//	private String idPromoter;
//
//	// UI references.
//	private EditText mNameView;
//	private EditText mDescriptionView;
//	private DateDisplayPicker mStartDateView;
//	private DateDisplayPicker mEndDateView;
//	private TimeDisplayPicker mStartTimeView;
//	private TimeDisplayPicker mEndTimeView;
//	private EditText mMinAgeView;
//	private Spinner mCategoryView;
//	private CheckBox mOpenBarView;
//
//	private View mNewEventFormView;
//	private View mNewEventStatusView;
//	private TextView mNewEventStatusMessageView;
//
//	private NewEventTask mNewEventTask = null;
//	private SessionManager session;
//	private Evento evento = new Evento();
//
//	@Override
//	protected void onCreate(Bundle savedInstanceState) {
//		super.onCreate(savedInstanceState);
//		setContentView(R.layout.activity_new_event);
//		loadComponents();
//	}
//
//	private void loadComponents() {
//
//		// Session class instance
//		session = new SessionManager(getApplicationContext());
//		session.checkLogin();
//		mNameView = (EditText) findViewById(R.id.txtName);
//		mDescriptionView = (EditText) findViewById(R.id.txtDescription);
//		mStartDateView = (DateDisplayPicker) findViewById(R.id.txtStartDate);
//		mEndDateView = (DateDisplayPicker) findViewById(R.id.txtEndDate);
//		mStartTimeView = (TimeDisplayPicker) findViewById(R.id.txtStartTime);
//		mEndTimeView = (TimeDisplayPicker) findViewById(R.id.txtEndTime);
//		mMinAgeView = (EditText) findViewById(R.id.txtMinAge);
//		mCategoryView = (Spinner) findViewById(R.id.spnCategory);
//		mOpenBarView = (CheckBox) findViewById(R.id.chkOpenBar);
//		HashMap<String, String> user = session.getUserDetails();
//		idPromoter = user.get(SessionManager.KEY_ID);
//		mNewEventFormView = findViewById(R.id.new_event_form);
//		mNewEventStatusView = findViewById(R.id.status);
//		mNewEventStatusMessageView = (TextView) findViewById(R.id.status_message);
//		TextView txtCategoryView = (TextView) findViewById(R.id.lblCategory);
//		TextView txtNameView = (TextView) findViewById(R.id.lblName);
//		TextView txtDescriptionView = (TextView) findViewById(R.id.lblDescription);
//		TextView txtFromView = (TextView) findViewById(R.id.lblFrom);
//		TextView txtToView = (TextView) findViewById(R.id.lblTo);
//		TextView txtMinAgeView = (TextView) findViewById(R.id.lblMinAge);
//		TextView txtNewEvent = (TextView) findViewById(R.id.lblNewEvent);
//		Button btnSave = (Button) findViewById(R.id.btnSave);
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
//		// mInvitationPermissionView.setTypeface(tf);
//		// mVisibilityView.setTypeface(tf);
//		// mCategoryView.setTypeface(tf);
//		mNewEventStatusMessageView.setTypeface(tf);
//		txtCategoryView.setTypeface(tf);
//		txtNameView.setTypeface(tf);
//		txtDescriptionView.setTypeface(tf);
//		txtFromView.setTypeface(tf);
//		txtToView.setTypeface(tf);
//		txtMinAgeView.setTypeface(tf);
//		txtNewEvent.setTypeface(tf);
//		btnSave.setTypeface(tf);
//
//		loadCategories();
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
//							Toast.makeText(NewEventActivity.this,
//									R.string.error_network_connection,
//									Toast.LENGTH_SHORT).show();
//						}
//					}
//				});
//		setCurrentDateOnView();
//		setCurrentTimeOnView();
//
//	}
//
//	private void loadCategories() {
//		ArrayAdapter<ECategoria> aa = new ArrayAdapter<ECategoria>(
//				NewEventActivity.this, android.R.layout.simple_list_item_1,
//				ECategoria.values());
//		mCategoryView.setAdapter(aa);
//	}
//
//	// set current date into textview
//	private void setCurrentDateOnView() {
//
//		int year, month, day;
//		final Calendar c = Calendar.getInstance();
//		year = c.get(Calendar.YEAR);
//		month = c.get(Calendar.MONTH);
//		day = c.get(Calendar.DAY_OF_MONTH);
//
//		mStartDateView.setText(new StringBuilder()
//				// Month is 0 based, just add 1
//				.append(String.format("%02d", day)).append("/")
//				.append(String.format("%02d", month + 1)).append("/")
//				.append(year));
//		mEndDateView.setText(new StringBuilder()
//				.append(String.format("%02d", day)).append("/")
//				.append(String.format("%02d", month + 1)).append("/")
//				.append(year));
//
//	}
//
//	// set current time into textview
//	private void setCurrentTimeOnView() {
//
//		final Calendar c = Calendar.getInstance();
//		c.add(Calendar.MINUTE, 10);
//		int hour = c.get(Calendar.HOUR_OF_DAY);
//		int minute = c.get(Calendar.MINUTE);
//		mStartTimeView.setText(new StringBuilder()
//				.append(String.format("%02d", hour)).append(":")
//				.append(String.format("%02d", minute)));
//
//		c.add(Calendar.MINUTE, 5);
//		hour = c.get(Calendar.HOUR_OF_DAY);
//		minute = c.get(Calendar.MINUTE);
//		mEndTimeView.setText(new StringBuilder()
//				.append(String.format("%02d", hour)).append(":")
//				.append(String.format("%02d", minute)));
//
//	}
//
//	public void attemptSave() {
//		if (mNewEventTask != null) {
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
//		// Store values at the time of the saving attempt.
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
//			// There was an error; don't attempt NewEvent and focus the first
//			// form field with an error.
//			focusView.requestFocus();
//		} else {
//			mNewEventStatusMessageView.setText(R.string.progress_saving_data);
//			showProgress(true);
//			mNewEventTask = new NewEventTask();
//			mNewEventTask.execute((Void) null);
//		}
//	}
//
//	public class NewEventTask extends AsyncTask<Void, Void, Boolean> {
//
//		@Override
//		protected Boolean doInBackground(Void... params) {
//			String urlPost = Guidebar.serverUrl + "gravarEvento.php";
//			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
//			parametrosPost.add(new BasicNameValuePair("nome", mName));
//			parametrosPost
//					.add(new BasicNameValuePair("descricao", mDescription));
//			String startDate = mStartDateView.getDateForPersistence(mStartDate)
//					+ " " + mStartTimeView.getTimeForPersistence(mStartTime);
//			parametrosPost
//					.add(new BasicNameValuePair("data_inicio", startDate));
//			String endDate = mEndDateView.getDateForPersistence(mEndDate) + " "
//					+ mEndTimeView.getTimeForPersistence(mEndTime);
//			parametrosPost.add(new BasicNameValuePair("data_fim", endDate));
//			parametrosPost.add(new BasicNameValuePair("idade_minima", mMinAge));
//			parametrosPost.add(new BasicNameValuePair("id_categoria", mCategory
//					.toString()));
//			parametrosPost.add(new BasicNameValuePair("id_promotor", idPromoter
//					.toString()));
//			parametrosPost.add(new BasicNameValuePair("open_bar", mOpenBar
//					.toString()));
//			String respostaRetornada = "";
//
//			String resposta = "";
//			try {
//				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
//						parametrosPost);
//				resposta = respostaRetornada.toString();
//				resposta = resposta.replaceAll("\\s+", "");
//			} catch (Exception erro) {
//				Toast.makeText(NewEventActivity.this, "Erro: " + erro,
//						Toast.LENGTH_SHORT).show();
//			}
//			evento.setId(Integer.parseInt(resposta));
//			return !resposta.equals("0");
//		}
//
//		@Override
//		protected void onPostExecute(final Boolean success) {
//			mNewEventTask = null;
//
//			if (success) {
//
//				showProgress(false);
//				Toast.makeText(NewEventActivity.this,
//						R.string.success_save_event, Toast.LENGTH_SHORT).show();
//
//				Intent i = new Intent(NewEventActivity.this,
//						AddAddressActivity.class);
//				i.putExtra("id_evento", evento.getId());
//				i.putExtra("editar_evento", false);
//				startActivity(i);
//				finish();
//
//			} else {
//				showProgress(false);
//				Toast.makeText(NewEventActivity.this,
//						R.string.error_save_event, Toast.LENGTH_SHORT).show();
//				mNameView.requestFocus();
//			}
//		}
//
//		@Override
//		protected void onCancelled() {
//			mNewEventTask = null;
//			showProgress(false);
//		}
//	}
//
//	/**
//	 * Shows the progress UI and hides the login form.
//	 */
//	@TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
//	private void showProgress(final boolean show) {
//		// On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
//		// for very easy animations. If available, use these APIs to fade-in
//		// the progress spinner.
//		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
//			int shortAnimTime = getResources().getInteger(
//					android.R.integer.config_shortAnimTime);
//
//			mNewEventStatusView.setVisibility(View.VISIBLE);
//			mNewEventStatusView.animate().setDuration(shortAnimTime)
//					.alpha(show ? 1 : 0)
//					.setListener(new AnimatorListenerAdapter() {
//						@Override
//						public void onAnimationEnd(Animator animation) {
//							mNewEventStatusView
//									.setVisibility(show ? View.VISIBLE
//											: View.GONE);
//						}
//					});
//
//			mNewEventFormView.setVisibility(View.VISIBLE);
//			mNewEventFormView.animate().setDuration(shortAnimTime)
//					.alpha(show ? 0 : 1)
//					.setListener(new AnimatorListenerAdapter() {
//						@Override
//						public void onAnimationEnd(Animator animation) {
//							mNewEventFormView.setVisibility(show ? View.GONE
//									: View.VISIBLE);
//						}
//					});
//		} else {
//			// The ViewPropertyAnimator APIs are not available, so simply show
//			// and hide the relevant UI components.
//			mNewEventStatusView.setVisibility(show ? View.VISIBLE : View.GONE);
//			mNewEventFormView.setVisibility(show ? View.GONE : View.VISIBLE);
//		}
//	}

}
