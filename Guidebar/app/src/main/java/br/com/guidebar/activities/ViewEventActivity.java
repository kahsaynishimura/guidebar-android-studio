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
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.support.v4.app.FragmentActivity;
import android.support.v4.app.FragmentManager;
import android.support.v4.app.FragmentTransaction;
import android.text.Html;
import android.text.TextUtils;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RatingBar;
import android.widget.RatingBar.OnRatingBarChangeListener;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.CommentsActivity;
import br.com.guidebar.R;
import br.com.guidebar.bean.Avaliacao;
import br.com.guidebar.bean.Categoria;
import br.com.guidebar.bean.Endereco;
import br.com.guidebar.bean.Evento;
import br.com.guidebar.bean.Favorito;
import br.com.guidebar.bean.Participacao;
import br.com.guidebar.bean.Usuario;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.ConnectionDetector;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.ImageDownloaderTask;
import br.com.guidebar.classes.SessionManager;
import br.com.guidebar.customviews.CustomAdapterAttendee;
import br.com.guidebar.customviews.DateDisplayPicker;

import com.facebook.UiLifecycleHelper;
import com.facebook.widget.FacebookDialog;

public class ViewEventActivity extends FragmentActivity {
	private TextView mNameView;
	private ImageView mEventFolder;
	private TextView lblEventDate;
	private Button btnDescription;
	private Button btnViewAttendees;
	// private Button btnShare;
	private SessionManager session;
	private Evento evento = new Evento();
	private Participacao participacao = new Participacao();
	private Favorito favorito = new Favorito();
	private Avaliacao avaliacao = new Avaliacao();
	private JoinEventTask mJoinEventTask;
	private SetActiveEventTask mSetActiveEventTask = null;
	private UnJoinEventTask mUnJoinEventTask = null;
	private RssEventTask mRssEventTask = null;
	private LeaveRssEventTask mLeaveRssEventTask = null;
	private DenunciateEventTask mDenunciateEventTask = null;
	private ViewEventTask mViewEventTask = null;
	private EvaluateEventTask mEvaluateEventTask = null;
	private View mViewEventFormView;
	private View mViewEventStatusView;
	private TextView mViewEventStatusMessageView;
	private static final int DESCRIPTION = 0;
	private static final int ATTENDEES = 1;
	private static final int FRAGMENT_COUNT = ATTENDEES + 1;
	private Fragment[] fragments = new Fragment[FRAGMENT_COUNT];

	private UiLifecycleHelper uiHelper;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		setContentView(R.layout.activity_view_event);

		loadComponents();

		uiHelper = new UiLifecycleHelper(this, null);
		uiHelper.onCreate(savedInstanceState);

		// btnShare = (Button) findViewById(R.id.btnShareOnFB);
	}

	public void shareOnFB(View v) {

		FacebookDialog shareDialog = new FacebookDialog.ShareDialogBuilder(this)
				.setLink(Guidebar.serverUrl + "events/view/" + evento.getId())

				.build();
		uiHelper.trackPendingDialogCall(shareDialog.present());
	}

	private void loadComponents() {
		session = new SessionManager(getApplicationContext());
		session.checkLogin();
		FragmentManager fm = getSupportFragmentManager();
		fragments[DESCRIPTION] = fm.findFragmentById(R.id.descriptionFragment);
		fragments[ATTENDEES] = fm.findFragmentById(R.id.attendeesFragment);

		FragmentTransaction transaction = fm.beginTransaction();
		for (int i = 0; i < fragments.length; i++) {
			transaction.hide(fragments[i]);
		}
		transaction.commit();

		showFragment(DESCRIPTION, false);

		btnDescription = (Button) findViewById(R.id.btnDescripion);
		btnViewAttendees = (Button) findViewById(R.id.btnViewAttendees);
		setSelectedTabColor(DESCRIPTION);

		mViewEventFormView = findViewById(R.id.view_event_form);
		mViewEventStatusView = findViewById(R.id.status);
		mViewEventStatusMessageView = (TextView) findViewById(R.id.status_message);
		mViewEventStatusMessageView.setText(R.string.progress_loading_data);
		showProgress(true);
		mEventFolder = (ImageView) findViewById(R.id.imgEventFolder);
		mNameView = (TextView) findViewById(R.id.txtName);
		// btnBuyTicket = (Button) findViewById(R.id.btnBuyTicket);
		Bundle parametros = getIntent().getExtras();
		evento = new Evento();
		evento.setId(parametros.getInt("id"));
		evento.setCategoria(new Categoria());
		evento.setPromotor(new Usuario());
		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = false;
		isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent) {
			if (mViewEventTask == null) {
				mViewEventTask = new ViewEventTask();
				mViewEventTask.execute();
			}
		} else {
			Toast.makeText(ViewEventActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}
		// participacao.setEstadoParticipacao(new EstadoParticipacao());
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		mViewEventStatusMessageView.setTypeface(tf);
		lblEventDate = (TextView) findViewById(R.id.lblEventDate);
		lblEventDate.setTypeface(tf);
		mNameView.setTypeface(tf);
		// btnBuyTicket.setTypeface(tf);
		btnDescription.setTypeface(tf);
		btnViewAttendees.setTypeface(tf);
		btnDescription.setTypeface(tf);
		btnViewAttendees.setTypeface(tf);

	}

	private void showFragment(int fragmentIndex, boolean addToBackStack) {
		FragmentManager fm = getSupportFragmentManager();
		FragmentTransaction transaction = fm.beginTransaction();
		for (int i = 0; i < fragments.length; i++) {
			if (i == fragmentIndex) {
				transaction.show(fragments[i]);
			} else {
				transaction.hide(fragments[i]);
			}
		}
		if (addToBackStack) {
			transaction.addToBackStack(null);
		}
		transaction.commit();
	}

	public void fixButtonText() {
		MenuItem mnuJoin = (MenuItem) menu.findItem(R.id.mnu_join);
		if (participacao.getId() == 0) {
			mnuJoin.setTitle(R.string.action_join);
		} else {
			mnuJoin.setTitle(R.string.action_cancel_participation);
		}
	}

	private void joinEvent() {
		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = false;
		isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent) {
			mViewEventStatusMessageView.setText(R.string.progress_saving_data);
			showProgress(true);
			if (participacao.getId() != 0 && mUnJoinEventTask == null) {
				mUnJoinEventTask = new UnJoinEventTask();
				mUnJoinEventTask.execute();
			} else if (mJoinEventTask == null) {
				mJoinEventTask = new JoinEventTask();
				mJoinEventTask.execute();
			}
		} else {
			Toast.makeText(ViewEventActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}
	}

	public void rssEvent() {

		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = false;
		isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent) {
			if (favorito.getAssinou()) {

				mViewEventStatusMessageView
						.setText(R.string.progress_saving_data);
				showProgress(true);
				if (mLeaveRssEventTask == null && favorito.getId() != 0) {
					mLeaveRssEventTask = new LeaveRssEventTask();
					mLeaveRssEventTask.execute((Void) null);
				} else {
					showProgress(false);
				}
			} else {

				mViewEventStatusMessageView
						.setText(R.string.progress_saving_data);
				showProgress(true);
				if (mRssEventTask == null) {
					mRssEventTask = new RssEventTask();
					mRssEventTask.execute((Void) null);
				} else {
					showProgress(false);
				}
			}
		} else {
			Toast.makeText(ViewEventActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}
	}

	// public void viewComments(View v) {
	// Intent i = new Intent(ViewEventActivity.this,
	// ListCommentsActivity.class);
	// // i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
	// i.putExtra("id_evento", evento.getId());
	// startActivity(i);
	// }

	private void denunciateEvent() {

		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = false;
		isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent) {
			mViewEventStatusMessageView.setText(R.string.progress_saving_data);
			showProgress(true);
			if (mDenunciateEventTask == null) {
				mDenunciateEventTask = new DenunciateEventTask();
				mDenunciateEventTask.execute((Void) null);
			}
		} else {
			Toast.makeText(ViewEventActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}
	}

	private void route() {
		Intent i = new Intent(ViewEventActivity.this, MapsRouteActivity.class);
		i.putExtra("latitude", evento.getLatitude());
		i.putExtra("longitude", evento.getLongitude());
		startActivity(i);
	}

	public void viewProducts(View v) {
		Intent i = new Intent(ViewEventActivity.this,
				ListProductsActivity.class);
		i.putExtra("id_evento", evento.getId());
		startActivity(i);
	}

	public void loadTabAttendees(View v) {
		showFragment(ATTENDEES, false);
		setSelectedTabColor(ATTENDEES);

	}

	public void loadTabDescription(View v) {
		showFragment(DESCRIPTION, false);
		setSelectedTabColor(DESCRIPTION);
	}

	public void loadTabComments(View v) {
		// showFragment(COMMENTS, false);
		//
		// setSelectedTabColor(COMMENTS);
	}

	private void setSelectedTabColor(int selectedTab) {
		switch (selectedTab) {
		case DESCRIPTION:
			btnDescription.setEnabled(false);
			btnViewAttendees.setEnabled(true);
			btnDescription.setBackgroundColor(getResources().getColor(
					R.color.button_selected_dark_grey));
			btnDescription.setTextColor(getResources().getColor(R.color.white));

			btnViewAttendees.setBackgroundColor(getResources().getColor(
					R.color.white));
			btnViewAttendees.setTextColor(getResources()
					.getColor(R.color.black));

			break;
		case ATTENDEES:

			btnDescription.setEnabled(true);
			btnViewAttendees.setEnabled(false);
			btnViewAttendees.setBackgroundColor(getResources().getColor(
					R.color.button_selected_dark_grey));
			btnViewAttendees.setTextColor(getResources()
					.getColor(R.color.white));

			btnDescription.setBackgroundColor(getResources().getColor(
					R.color.white));
			btnDescription.setTextColor(getResources().getColor(R.color.black));

			break;
		// case COMMENTS:
		//
		// btnDescription.setEnabled(true);
		// btnViewAttendees.setEnabled(true);
		// btnViewComments.setEnabled(false);
		// btnViewComments.setBackgroundColor(getResources().getColor(
		// R.color.button_selected_dark_grey));
		// btnViewComments
		// .setTextColor(getResources().getColor(R.color.white));
		//
		// btnDescription.setBackgroundColor(getResources().getColor(
		// R.color.white));
		// btnDescription.setTextColor(getResources().getColor(R.color.black));
		//
		// btnViewAttendees.setBackgroundColor(getResources().getColor(
		// R.color.white));
		// btnViewAttendees.setTextColor(getResources()
		// .getColor(R.color.black));
		// break;

		}
	}

	class ViewEventTask extends AsyncTask<Integer, Void, Boolean> {

		private static final String TAG_NOME = "name";
		private static final String TAG_THUMB = "thumb";
		private static final String TAG_DESCRICAO = "description";
		private static final String TAG_DATA_INICIO = "start_date";
		private static final String TAG_DATA_FIM = "end_date";
		private static final String TAG_IDADE_MINIMA = "minimum_age";
		private static final String TAG_VISUALIZACOES = "views";
		private static final String TAG_EMAIL = "email";
		private static final String TAG_ID_PROMOTOR = "user_id";
		private static final String TAG_AVALIACAO_MEDIA = "average_rating";
		private static final String TAG_LATITUDE = "latitude";
		private static final String TAG_LONGITUDE = "longitude";
		private static final String TAG_RUA = "street";
		private static final String TAG_NUMERO = "street_number";
		private static final String TAG_BAIRRO = "neighborhood";
		private static final String TAG_COMPLEMENTO = "complement";
		private static final String TAG_CEP = "zip_code";
		private static final String TAG_IS_OPEN_BAR = "is_open_bar";
		private static final String TAG_IS_ATIVO = "is_active";
		private static final String TAG_ID = "id";
		private static final String TAG_EVENTOS = "event";
		private static final String TAG_EVENT = "Event";
		private static final String TAG_CAMINHO_IMAGEM = "filename";
		private static final String TAG_CATEGORY = "Category";
		private static final String TAG_EVALUATION = "Evaluation";
		private static final String TAG_ATTENDANCE = "Attendance";
		private static final String TAG_ADDRESS = "Address";
		private static final String TAG_ATTENDEES = "Attendees";
		private static final String TAG_BOOKMARK = "Bookmark";
		private static final String TAG_USER = "User";
		private static final String TAG_CITY = "City";
		private static final String TAG_RATING = "rating";
		private static final String TAG_STATE = "State";
		private static final String TAG_PRODUCT = "Product";

		JSONObject event = null;
		Boolean hasProducts = false;

		@Override
		protected Boolean doInBackground(Integer... params) {
			String url = Guidebar.serverUrl + "events/view/" + evento.getId()
					+ ".json";

			String respostaRetornada = "";
			String resposta = "";
			try {
				ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
				parametrosPost.add(new BasicNameValuePair("email", session
						.getUserDetails().get(SessionManager.KEY_EMAIL)));
				parametrosPost
						.add(new BasicNameValuePair("token", session
								.getUserDetails().get(
										SessionManager.KEY_ACCESS_TOKEN)));

				respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
						parametrosPost);
				resposta = respostaRetornada.toString();
				if ((resposta) != null) {
					JSONObject json = new JSONObject(resposta);

					try {
						event = json.getJSONObject(TAG_EVENTOS);
						JSONObject c = event.getJSONObject(TAG_EVENT);
						JSONObject category = event.getJSONObject(TAG_CATEGORY);
						JSONArray evaluation = event
								.getJSONArray(TAG_EVALUATION);
						JSONArray attendees = event.getJSONArray(TAG_ATTENDEES);
						if (event.getJSONArray(TAG_PRODUCT).length() > 0) {
							hasProducts = true;
						} else {
							hasProducts = false;
						}
						if (event.getJSONArray(TAG_ATTENDANCE).length() > 0) {
							participacao.setId(event
									.getJSONArray(TAG_ATTENDANCE)
									.getJSONObject(0).getInt("id"));
							participacao.setParticipando(true);
						} else {
							participacao.setId(0);
							participacao.setParticipando(false);
						}
						ArrayList<Participacao> listaParticipantes = new ArrayList<Participacao>();
						for (int i = 0; i < attendees.length(); i++) {

							Participacao obj = new Participacao();

							JSONObject attendanceJSON = attendees
									.getJSONObject(i);
							obj.setUsuario(new Usuario(Integer
									.parseInt(attendanceJSON.getJSONObject(
											TAG_USER).getString(TAG_ID))));
							obj.getUsuario().setNome(
									attendanceJSON.getJSONObject(TAG_USER)
											.getString(TAG_NOME));
							obj.getUsuario().setThumbnail(
									attendanceJSON.getJSONObject(TAG_USER)
											.getString(TAG_THUMB));

							listaParticipantes.add(obj);
						}
						ArrayAdapter<Participacao> aa = new CustomAdapterAttendee(
								ViewEventActivity.this,
								R.layout.user_list_item, listaParticipantes);

						ListView mLstAttendees = (ListView) findViewById(R.id.lstAttendees);
						mLstAttendees.setAdapter(aa);

						JSONObject promotor = event.getJSONObject(TAG_USER);
						JSONArray bookmark = event.getJSONArray(TAG_BOOKMARK);
						if (bookmark.length() > 0) {
							favorito.setId(bookmark.getJSONObject(0).getInt(
									TAG_ID));
							favorito.setAssinou(true);
						} else {
							favorito.setId(0);
							favorito.setAssinou(false);
						}
						if (evaluation.length() > 0) {
							avaliacao.setValor(Float.parseFloat(""
									+ evaluation.getJSONObject(0).get(
											TAG_RATING)));
						} else {
							avaliacao.setValor(0f);
						}
						JSONObject address = event.getJSONObject(TAG_ADDRESS);
						evento.setNome(c.getString(TAG_NOME));
						evento.setFilename(c.getString(TAG_THUMB));
						evento.setDescricao(c.getString(TAG_DESCRICAO));
						evento.setDataInicio(c.getString(TAG_DATA_INICIO));
						evento.setDataFim(c.getString(TAG_DATA_FIM));
						evento.setIdadeMinima(Integer.parseInt(c
								.getString(TAG_IDADE_MINIMA)));
						evento.setVisualizacoes(Integer.parseInt(c
								.getString(TAG_VISUALIZACOES)));
						evento.setCategoria(new Categoria(category
								.getString(TAG_ID)));
						evento.getCategoria().setNome(
								category.getString(TAG_NOME));
						evento.setThumbnail(c.getString(TAG_CAMINHO_IMAGEM));
						evento.getPromotor().setId(
								Integer.parseInt(c.getString(TAG_ID_PROMOTOR)));
						evento.setAvaliacaoMedia(Float.parseFloat(c
								.getString(TAG_AVALIACAO_MEDIA)));
						evento.getEndereco().setRua(address.getString(TAG_RUA));
						evento.getEndereco().setNumero(
								address.getString(TAG_NUMERO));
						evento.getEndereco().setBairro(
								address.getString(TAG_BAIRRO));
						evento.getEndereco().setComplemento(
								address.getString(TAG_COMPLEMENTO));
						evento.getEndereco().setCep(address.getString(TAG_CEP));
						evento.setIsOpenBar(Boolean.parseBoolean(c
								.getString(TAG_IS_OPEN_BAR)));
						evento.setIsAtivo(Boolean.parseBoolean(c
								.getString(TAG_IS_ATIVO)));
						evento.setLatitude(address.getString(TAG_LATITUDE));
						evento.setLongitude(address.getString(TAG_LONGITUDE));
						evento.setId(Integer.parseInt(c.getString(TAG_ID)));
						evento.getPromotor().setNome(
								promotor.getString(TAG_NOME));
						evento.getPromotor().setEmail(
								promotor.getString(TAG_EMAIL));
						evento.getEndereco()
								.getCidade()
								.setNome(
										address.getJSONObject(TAG_CITY)
												.getString(TAG_NOME));
						evento.getEndereco()
								.getEstado()
								.setNome(
										address.getJSONObject(TAG_CITY)
												.getJSONObject(TAG_STATE)
												.getString(TAG_NOME));
					} catch (JSONException e) {
						e.printStackTrace();
					}

				}
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}
			return evento.getNome() != null;
		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mViewEventTask = null;
			showProgress(false);
			if (success) {
				runOnUiThread(new Runnable() {
					public void run() {
						TextView mDescriptionView = (TextView) findViewById(R.id.txtDescription);
						TextView mStartDateView = (TextView) findViewById(R.id.txtStartDate);
						TextView mEndDateView = (TextView) findViewById(R.id.txtEndDate);
						TextView mMinAgeView = (TextView) findViewById(R.id.txtMinAge);
						TextView mNumViewsView = (TextView) findViewById(R.id.txtNumViews);
						TextView mAddressView = (TextView) findViewById(R.id.txtAddress);
						TextView mBairroView = (TextView) findViewById(R.id.txtBairro);
						TextView mComplementoView = (TextView) findViewById(R.id.txtComplemento);
						TextView mCEPView = (TextView) findViewById(R.id.txtCEP);
						TextView mCityView = (TextView) findViewById(R.id.txtCity);
						TextView mStateView = (TextView) findViewById(R.id.txtState);

						TextView mCategoryView = (TextView) findViewById(R.id.txtCategory);
						TextView lblCategoryView = (TextView) findViewById(R.id.lblCategory);
						TextView mPromoterView = (TextView) findViewById(R.id.txtPromoter);
						TextView mOpenBarView = (TextView) findViewById(R.id.txtOpenBar);
						RatingBar rtbEvaluation = (RatingBar) findViewById(R.id.rtbEvaluationEventUser);
						RatingBar rtbEvaluationAvarage = (RatingBar) findViewById(R.id.rtbEvaluation);

						Button btnViewProducts = (Button) findViewById(R.id.btnViewProducts);
						rtbEvaluationAvarage.setRating(evento
								.getAvaliacaoMedia());
						rtbEvaluationAvarage.setClickable(false);
						rtbEvaluation.setRating(Float.parseFloat(String
								.valueOf(avaliacao.getValor())));
						TextView lblEvaluation = (TextView) findViewById(R.id.lblEvaluation);
						rtbEvaluation
								.setOnRatingBarChangeListener(ratingChangeListener);
						Typeface tf = Typeface.createFromAsset(getAssets(),
								"fonts/Gotham-Book.otf");
						mDescriptionView.setTypeface(tf);
						mStartDateView.setTypeface(tf);
						mEndDateView.setTypeface(tf);
						mMinAgeView.setTypeface(tf);
						mNumViewsView.setTypeface(tf);
						mAddressView.setTypeface(tf);
						mBairroView.setTypeface(tf);
						mComplementoView.setTypeface(tf);
						mCEPView.setTypeface(tf);
						mCityView.setTypeface(tf);
						mStateView.setTypeface(tf);
						mCategoryView.setTypeface(tf);
						mPromoterView.setTypeface(tf);
						lblEvaluation.setTypeface(tf);
						lblCategoryView.setTypeface(tf);
						mOpenBarView.setTypeface(tf);
						btnViewProducts.setTypeface(tf);
						if (hasProducts) {
							btnViewProducts.setVisibility(View.VISIBLE);
						} else {
							btnViewProducts.setVisibility(View.GONE);
						}
						mNameView.setText(Html.fromHtml("<b>"
								+ evento.getNome() + "</b>"));

						mDescriptionView.setText(evento.getDescricao());
						mStartDateView.setText(Html
								.fromHtml(getString(R.string.prompt_start_date)
										+ ": <b>"
										+

										new DateDisplayPicker(
												ViewEventActivity.this)
												.getDateForPresenting(evento
														.getDataInicio()
														.substring(0, 10))

										+ evento.getDataInicio().substring(10,
												16) + "</b>"));
						mEndDateView.setText(Html
								.fromHtml(getString(R.string.prompt_end_date)
										+ ": <b>"
										+ new DateDisplayPicker(
												ViewEventActivity.this)
												.getDateForPresenting(evento
														.getDataFim()
														.substring(0, 10))
										+ evento.getDataFim().substring(10, 16)
										+ "</b>"));
						mMinAgeView.setText(Html
								.fromHtml(getString(R.string.prompt_min_age)
										+ ": <b>" + evento.getIdadeMinima()
										+ " ano(s)</b>"));
						mNumViewsView.setText(Html
								.fromHtml(getString(R.string.prompt_num_views)
										+ ": <b>" + evento.getVisualizacoes()
										+ "</b>"));
						mPromoterView.setText(Html
								.fromHtml(getString(R.string.prompt_promoter)
										+ ": <b>"
										+ evento.getPromotor().getNome()
										+ "</b>"));
						mCategoryView.setText(Html
								.fromHtml(getString(R.string.prompt_category)
										+ ": <b>"
										+ evento.getCategoria().getNome()
										+ "</b>"));
						lblCategoryView.setText(Html
								.fromHtml(getString(R.string.prompt_category)
										+ ": <b>"
										+ evento.getCategoria().getNome()
										+ "</b>"));
						mOpenBarView.setText(Html
								.fromHtml(getString(R.string.prompt_open_bar)
										+ ": <b>"
										+ (evento.getIsOpenBar() ? "Sim"
												: "Não") + "</b>"));
						Endereco endereco = evento.getEndereco();
						if (TextUtils.isEmpty(endereco.getRua())) {
							mAddressView.setVisibility(View.GONE);
							mBairroView.setVisibility(View.GONE);
							mCEPView.setVisibility(View.GONE);
							mCityView.setVisibility(View.GONE);
							mStateView.setVisibility(View.GONE);
						} else {
							mAddressView.setVisibility(View.VISIBLE);
							mBairroView.setVisibility(View.VISIBLE);
							mCEPView.setVisibility(View.VISIBLE);
							mCityView.setVisibility(View.VISIBLE);
							mStateView.setVisibility(View.VISIBLE);

							mAddressView
									.setText(Html
											.fromHtml(getString(R.string.prompt_address)
													+ ": <b>"
													+ endereco.getRua()
													+ ", "
													+ endereco.getNumero()
													+ " " + "</b>"));
							mBairroView.setText(Html
									.fromHtml(getString(R.string.prompt_bairro)
											+ ": <b>" + endereco.getBairro()
											+ "</b>"));
							mCEPView.setText(Html
									.fromHtml(getString(R.string.prompt_cep)
											+ ": <b>" + endereco.getCep()
											+ "</b>"));
							mCityView.setText(Html
									.fromHtml(getString(R.string.prompt_city)
											+ ": <b>"
											+ endereco.getCidade().getNome()
											+ "</b>"));
							mStateView.setText(Html
									.fromHtml(getString(R.string.prompt_state)
											+ ": <b>"
											+ endereco.getEstado().getNome()
											+ "</b>"));
						}
						if (TextUtils.isEmpty(endereco.getComplemento())) {
							mComplementoView.setVisibility(View.GONE);
						} else {
							mComplementoView.setVisibility(View.VISIBLE);
							mComplementoView
									.setText(Html
											.fromHtml(getString(R.string.prompt_complemento)
													+ ": <b>"
													+ endereco.getComplemento()
													+ "</b>"));

						}

						lblEventDate.setText(new DateDisplayPicker(
								ViewEventActivity.this)
								.getDateForPresenting(evento.getDataInicio()
										.substring(0, 10)));
						fixButtonText();
						rtbEvaluation.setRating(avaliacao.getValor());
						if (evento.getLatitude() == ""
								|| evento.getLongitude() == ""
								|| evento.getLongitude() == null
								|| evento.getLatitude() == null) {
							menu.removeItem(R.id.mnu_route);
						}
						if (evento.getPromotor().getId() != Integer
								.parseInt(session.getUserDetails().get(
										SessionManager.KEY_ID))) {
							menu.removeItem(R.id.mnu_validate_checkin);
							// menu.removeItem(R.id.mnu_edit_event);
							menu.removeItem(R.id.mnu_deactivate_event);
						} else {
							MenuItem mnuActivate = (MenuItem) menu
									.findItem(R.id.mnu_deactivate_event);
							if (evento.getIsAtivo()) {
								mnuActivate.setTitle(R.string.menu_deactivate);
							} else {
								mnuActivate.setTitle(R.string.menu_activate);
							}
						}
						Calendar c = Calendar.getInstance();

						c.set(Calendar.HOUR_OF_DAY, 0);
						c.set(Calendar.MINUTE, 5);
						String data = getDateForPresenting(evento
								.getDataInicio().substring(0, 10));

						c.set(Calendar.YEAR,
								DateDisplayPicker.getYear(data, "/"));
						c.set(Calendar.DAY_OF_MONTH,
								DateDisplayPicker.getDay(data, "/"));
						c.set(Calendar.MONTH,
								((DateDisplayPicker.getMonth(data, "/")) - 1));
						Calendar atual = Calendar.getInstance();
						atual.set(Calendar.HOUR_OF_DAY, 0);
						atual.set(Calendar.MINUTE, 1);

						// TODO: ver se vai ficar assim a venda de ingresso
						// btnBuyTicket.setVisibility(View.VISIBLE);
						MenuItem mnuFavorite = (MenuItem) menu
								.findItem(R.id.mnu_favorite);
						if (favorito.getAssinou()) {
							mnuFavorite
									.setTitle(R.string.action_remove_from_favorite);
						} else {
							mnuFavorite
									.setTitle(R.string.action_mark_as_favorite);
						}
						mEventFolder = (ImageView) findViewById(R.id.imgEventFolder);

						evento.setThumbnail(Guidebar.serverUrl + "img/"
								+ evento.getFilename());
						if (mEventFolder != null) {
							new ImageDownloaderTask(mEventFolder)
									.execute(evento.getThumbnail());
						}
					}
				});
			} else {
				Toast.makeText(ViewEventActivity.this,
						getText(R.string.error_invalid_event),
						Toast.LENGTH_SHORT).show();
				finish();
			}
		}

		@Override
		protected void onCancelled() {
			showProgress(false);
		}
	}

	@Override
	public void onResume() {
		super.onResume();

		uiHelper.onResume();
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		super.onActivityResult(requestCode, resultCode, data);

		uiHelper.onActivityResult(requestCode, resultCode, data,
				new FacebookDialog.Callback() {
					@Override
					public void onError(FacebookDialog.PendingCall pendingCall,
							Exception error, Bundle data) {
						Log.e("Activity",
								String.format("Error: %s", error.toString()));
					}

					@Override
					public void onComplete(
							FacebookDialog.PendingCall pendingCall, Bundle data) {
						Log.i("Activity", "Success!");
					}
				});
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

	public String getDateForPresenting(String str) {
		if (str != null) {
			String[] date = str.split("-");
			String date2 = date[2] + "/" + date[1] + "/" + date[0];
			return date2;
		} else {
			return "";
		}
	}

	public class LeaveRssEventTask extends AsyncTask<Void, Void, Boolean> {

		@Override
		protected Boolean doInBackground(Void... params) {
			Boolean success = false;
			String urlPost = Guidebar.serverUrl + "bookmarks/delete/"
					+ favorito.getId() + ".json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			String respostaRetornada = "";

			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				JSONObject json = new JSONObject(resposta);
				success = json.getBoolean("success");
			} catch (Exception erro) {
				Toast.makeText(ViewEventActivity.this, "Erro: " + erro,
						Toast.LENGTH_SHORT).show();
			}
			return success;

		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mLeaveRssEventTask = null;
			showProgress(false);
			if (success) {
				Toast.makeText(ViewEventActivity.this,
						R.string.success_leave_rss, Toast.LENGTH_SHORT).show();
				favorito.setAssinou(false);

				MenuItem mnuFavorite = (MenuItem) menu
						.findItem(R.id.mnu_favorite);
				mnuFavorite.setTitle(R.string.action_mark_as_favorite);

			} else {
				Toast.makeText(ViewEventActivity.this,
						R.string.error_leave_rss, Toast.LENGTH_SHORT).show();
			}
		}

		@Override
		protected void onCancelled() {
			mLeaveRssEventTask = null;
			showProgress(false);
		}
	}

	public class RssEventTask extends AsyncTask<Void, Void, Boolean> {
		@Override
		protected Boolean doInBackground(Void... params) {
			Boolean success = false;
			String urlPost = Guidebar.serverUrl + "bookmarks/add.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			parametrosPost.add(new BasicNameValuePair(
					"data[Bookmark][event_id]", evento.getId().toString()));
			String respostaRetornada = "";

			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				JSONObject json = new JSONObject(resposta);
				success = json.getBoolean("success");
				favorito.setId(json.getInt("id"));
			} catch (Exception erro) {
				Toast.makeText(ViewEventActivity.this, "Erro: " + erro,
						Toast.LENGTH_SHORT).show();
			}
			return success;

		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mRssEventTask = null;
			showProgress(false);

			if (success) {
				Toast.makeText(ViewEventActivity.this,
						R.string.success_save_rss, Toast.LENGTH_SHORT).show();
				favorito.setAssinou(true);
				MenuItem mnuFavorite = (MenuItem) menu
						.findItem(R.id.mnu_favorite);
				mnuFavorite.setTitle(R.string.action_remove_from_favorite);
			} else {
				Toast.makeText(ViewEventActivity.this, R.string.error_save_rss,
						Toast.LENGTH_SHORT).show();
			}
		}

		@Override
		protected void onCancelled() {
			mRssEventTask = null;
			showProgress(false);
		}
	}

	public class JoinEventTask extends AsyncTask<Void, Void, Boolean> {
		ArrayList<Participacao> listaParticipantes = new ArrayList<Participacao>();
		private static final String TAG_ID = "id";
		private static final String TAG_NOME = "name";
		private static final String TAG_ATTENDANCES = "attendances";
		private static final String TAG_FILENAME = "filename";
		private static final String TAG_USER = "User";
		private static final String TAG_RESPONSE = "response";

		@Override
		protected Boolean doInBackground(Void... params) {
			Boolean success = false;
			String urlPost = Guidebar.serverUrl + "attendances/add.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			parametrosPost.add(new BasicNameValuePair(
					"data[Attendance][event_id]", evento.getId().toString()));
			String respostaRetornada = "";

			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				JSONObject json = new JSONObject(resposta);
				JSONObject response = new JSONObject(
						json.getString(TAG_RESPONSE));
				JSONArray attendances = json.getJSONArray(TAG_ATTENDANCES);
				success = response.getBoolean("success");
				participacao.setId(response.getInt("id"));
				for (int i = 0; i < attendances.length(); i++) {
					Participacao obj = new Participacao();

					JSONObject c = attendances.getJSONObject(i);
					obj.setUsuario(new Usuario(Integer.parseInt(c
							.getJSONObject(TAG_USER).getString(TAG_ID))));
					obj.getUsuario().setNome(
							c.getJSONObject(TAG_USER).getString(TAG_NOME));
					obj.getUsuario().setThumbnail(
							c.getJSONObject(TAG_USER).getString(TAG_FILENAME));

					listaParticipantes.add(obj);
				}
			} catch (Exception erro) {
				Toast.makeText(ViewEventActivity.this, "Erro: " + erro,
						Toast.LENGTH_SHORT).show();
			}
			return success;

		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mJoinEventTask = null;
			showProgress(false);

			if (success) {
				Toast.makeText(ViewEventActivity.this,
						R.string.success_save_participation, Toast.LENGTH_SHORT)
						.show();
				ArrayAdapter<Participacao> aa = new CustomAdapterAttendee(
						ViewEventActivity.this, R.layout.user_list_item,
						listaParticipantes);

				ListView mLstAttendees = (ListView) findViewById(R.id.lstAttendees);
				mLstAttendees.setAdapter(aa);
				fixButtonText();
			} else {
				Toast.makeText(ViewEventActivity.this,
						R.string.error_save_participation, Toast.LENGTH_SHORT)
						.show();
				mNameView.requestFocus();
			}
		}

		@Override
		protected void onCancelled() {
			mJoinEventTask = null;
			showProgress(false);
		}
	}

	private Menu menu;

	@Override
	public boolean onPrepareOptionsMenu(Menu menu) {
		this.menu = menu;
		return super.onPrepareOptionsMenu(menu);
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.activity_view_event, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {
		case R.id.mnu_join:
			joinEvent();
			break;
		case R.id.mnu_denunciate:
			denunciateEvent();
			break;
		case R.id.mnu_route:
			route();
			break;
		case R.id.mnu_facebook_comment:
			Intent intent = new Intent(ViewEventActivity.this,
					CommentsActivity.class);
			intent.putExtra("id_evento", evento.getId());
			startActivity(intent);
			break;
		case R.id.mnu_validate_checkin:
			Intent i = new Intent(ViewEventActivity.this,
					ValidateCheckInActivity.class);
			i.putExtra("id_evento", evento.getId());
			startActivity(i);
			break;
		// case R.id.mnu_edit_event:
		// if (!evento
		// .getPromotor()
		// .getId()
		// .toString()
		// .equals(session.getUserDetails().get(SessionManager.KEY_ID))) {
		// Toast.makeText(ViewEventActivity.this,
		// getText(R.string.error_edit_event), Toast.LENGTH_SHORT)
		// .show();
		// } else {
		// editEvent(evento.getId());
		// }
		// break;
		case R.id.mnu_deactivate_event:
			if (evento
					.getPromotor()
					.getId()
					.toString()
					.equals(session.getUserDetails().get(SessionManager.KEY_ID))) {
				setActiveEventConfirmation(evento.getId());
			} else {
				Toast.makeText(ViewEventActivity.this,
						getText(R.string.error_deactivate_event),
						Toast.LENGTH_SHORT).show();
			}
			break;
		case R.id.mnu_favorite:
			rssEvent();
			break;
		default:
			return super.onOptionsItemSelected(item);
		}
		return true;
	}

	// protected void editEvent(Integer id) {
	//
	// Intent ITelaEdicao = new Intent(ViewEventActivity.this,
	// EditEventActivity.class);
	// ITelaEdicao.putExtra("id", id);
	//
	// startActivity(ITelaEdicao);
	// finish();
	// }

	protected void setActiveEventConfirmation(final Integer id) {

		AlertDialog.Builder comfirmDelete = new AlertDialog.Builder(this);
		if (evento.getIsAtivo()) {
			comfirmDelete.setTitle("Desativar evento");
			comfirmDelete.setMessage("Desativar o evento?");
		} else {
			comfirmDelete.setTitle("Ativar evento");
			comfirmDelete.setMessage("Deseja ativar o evento?");

		}
		comfirmDelete.setPositiveButton("SIM",
				new DialogInterface.OnClickListener() {

					@Override
					public void onClick(DialogInterface dialog, int which) {
						setActiveEvent();
					}
				});

		comfirmDelete.setNegativeButton("NÃO",
				new DialogInterface.OnClickListener() {

					@Override
					public void onClick(DialogInterface dialog, int which) {
						// dialog.cancel();
					}
				});
		comfirmDelete.show();
	}

	protected void setActiveEvent() {
		ConnectionDetector cd = new ConnectionDetector(getApplicationContext());
		Boolean isInternetPresent = cd.isConnectingToInternet();
		if (isInternetPresent && mSetActiveEventTask == null) {
			if (evento.getIsAtivo()) {

				mSetActiveEventTask = new SetActiveEventTask();
				mSetActiveEventTask.execute("events/deactivate/"
						+ evento.getId() + ".json");
			} else {
				mSetActiveEventTask = new SetActiveEventTask();
				mSetActiveEventTask.execute("events/activate/" + evento.getId()
						+ ".json");
			}
		} else {
			Toast.makeText(ViewEventActivity.this,
					R.string.error_network_connection, Toast.LENGTH_SHORT)
					.show();
		}

	}

	class SetActiveEventTask extends AsyncTask<String, Void, Boolean> {

		@Override
		protected Boolean doInBackground(String... params) {
			Boolean success = false;
			String urlPost = Guidebar.serverUrl + params[0];
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));
			String respostaRetornada = "";
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				JSONObject json = new JSONObject(resposta);
				success = json.getBoolean("success");
			} catch (Exception erro) {
				erro.printStackTrace();
			}
			return success;
		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mSetActiveEventTask = null;
			MenuItem mnuActivate = (MenuItem) menu
					.findItem(R.id.mnu_deactivate_event);
			if (success) {
				if (evento.getIsAtivo()) {
					evento.setIsAtivo(false);
					mnuActivate.setTitle(R.string.menu_activate);
					Toast.makeText(ViewEventActivity.this,
							R.string.success_deactivate_event,
							Toast.LENGTH_SHORT).show();
				} else {
					evento.setIsAtivo(true);
					mnuActivate.setTitle(R.string.menu_deactivate);
					Toast.makeText(ViewEventActivity.this,
							R.string.success_activate_event, Toast.LENGTH_SHORT)
							.show();
				}
			} else {
				Toast.makeText(ViewEventActivity.this,
						R.string.error_deactivate_event, Toast.LENGTH_SHORT)
						.show();
			}
		}

		@Override
		protected void onCancelled() {
			mSetActiveEventTask = null;
		}

	}

	public class UnJoinEventTask extends AsyncTask<Void, Void, Boolean> {

		ArrayList<Participacao> listaParticipantes = new ArrayList<Participacao>();
		private static final String TAG_RESPONSE = "response";
		private static final String TAG_ATTENDANCES = "attendances";
		private static final String TAG_ID = "id";
		private static final String TAG_NOME = "name";
		private static final String TAG_FILENAME = "filename";

		@Override
		protected Boolean doInBackground(Void... params) {
			Boolean success = false;
			String urlPost = Guidebar.serverUrl + "attendances/delete/"
					+ participacao.getId() + ".json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			String respostaRetornada = "";

			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				JSONObject json = new JSONObject(resposta);
				JSONObject response = new JSONObject(
						json.getString(TAG_RESPONSE));
				JSONArray attendances = json.getJSONArray(TAG_ATTENDANCES);
				success = response.getBoolean("success");
				for (int i = 0; i < attendances.length(); i++) {
					Participacao obj = new Participacao();

					JSONObject c = attendances.getJSONObject(i);
					obj.setUsuario(new Usuario(Integer.parseInt(c
							.getJSONObject("User").getString(TAG_ID))));
					obj.getUsuario().setNome(
							c.getJSONObject("User").getString(TAG_NOME));
					obj.getUsuario().setThumbnail(
							c.getJSONObject("User").getString(TAG_FILENAME));

					listaParticipantes.add(obj);
				}
			} catch (Exception erro) {
				Toast.makeText(ViewEventActivity.this, "Erro: " + erro,
						Toast.LENGTH_SHORT).show();
			}
			return success;
		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mUnJoinEventTask = null;
			showProgress(false);

			if (success) {
				Toast.makeText(ViewEventActivity.this,
						R.string.success_cancel_participation,
						Toast.LENGTH_SHORT).show();
				ArrayAdapter<Participacao> aa = new CustomAdapterAttendee(
						ViewEventActivity.this, R.layout.user_list_item,
						listaParticipantes);

				ListView mLstAttendees = (ListView) findViewById(R.id.lstAttendees);
				mLstAttendees.setAdapter(aa);
				participacao.setId(0);
				fixButtonText();
			} else {
				Toast.makeText(ViewEventActivity.this,
						R.string.error_cancel_participation, Toast.LENGTH_SHORT)
						.show();
			}
		}

		@Override
		protected void onCancelled() {
			mUnJoinEventTask = null;
			showProgress(false);
		}

	}

	public class DenunciateEventTask extends AsyncTask<Void, Void, Boolean> {
		private String dados = "";

		@Override
		protected Boolean doInBackground(Void... params) {
			Boolean success = false;
			String urlPost = Guidebar.serverUrl + "complaints/add.json";

			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			parametrosPost.add(new BasicNameValuePair(
					"data[Complaint][event_id]", evento.getId().toString()));

			String respostaRetornada = "";

			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				JSONObject json = new JSONObject(resposta);

				success = json.getBoolean("success");
				dados = json.getString("data");
			} catch (Exception e) {
				e.printStackTrace();
			}
			return success;

		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mDenunciateEventTask = null;
			showProgress(false);

			if (success) {

				Toast.makeText(ViewEventActivity.this, dados,
						Toast.LENGTH_SHORT).show();
			} else {
				Toast.makeText(ViewEventActivity.this,
						R.string.error_denunciate_event, Toast.LENGTH_SHORT)
						.show();

			}
		}

		@Override
		protected void onCancelled() {
			mDenunciateEventTask = null;
			showProgress(false);
		}
	}

	OnRatingBarChangeListener ratingChangeListener = new OnRatingBarChangeListener() {
		public void onRatingChanged(RatingBar ratingBar, final float rating,
				boolean fromUser) {
			if (fromUser) {
				mViewEventStatusMessageView
						.setText(R.string.progress_saving_data);
				showProgress(true);
				mEvaluateEventTask = new EvaluateEventTask();
				mEvaluateEventTask.execute(rating);
			}
		}
	};

	public class EvaluateEventTask extends AsyncTask<Float, Void, Boolean> {
		@Override
		protected Boolean doInBackground(Float... params) {
			Boolean success = false;
			String urlPost = Guidebar.serverUrl + "evaluations/add.json";

			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			parametrosPost.add(new BasicNameValuePair(
					"data[Evaluation][event_id]", evento.getId().toString()));
			parametrosPost.add(new BasicNameValuePair(
					"data[Evaluation][rating]", params[0].toString()));

			String respostaRetornada = "";
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				JSONObject json = new JSONObject(resposta);
				success = json.getBoolean("success");
			} catch (Exception erro) {
				Toast.makeText(ViewEventActivity.this, "Erro: " + erro,
						Toast.LENGTH_SHORT).show();
			}
			return success;

		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mEvaluateEventTask = null;
			showProgress(false);

			if (success) {
				Toast.makeText(ViewEventActivity.this,
						R.string.success_evaluate_event, Toast.LENGTH_SHORT)
						.show();
			} else {
				Toast.makeText(ViewEventActivity.this,
						R.string.error_evaluate_event, Toast.LENGTH_SHORT)
						.show();
			}
		}

		@Override
		protected void onCancelled() {
			mEvaluateEventTask = null;
			showProgress(false);
		}
	}

	// private void hideSoftKeyboard() {
	// if (getCurrentFocus() != null && getCurrentFocus() instanceof EditText) {
	// InputMethodManager imm = (InputMethodManager)
	// getSystemService(Context.INPUT_METHOD_SERVICE);
	// imm.hideSoftInputFromWindow(mCommentView.getWindowToken(), 0);
	// }
	// }

	@TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
	private void showProgress(final boolean show) {
		// On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
		// for very easy animations. If available, use these APIs to fade-in
		// the progress spinner.
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
			int shortAnimTime = getResources().getInteger(
					android.R.integer.config_shortAnimTime);

			mViewEventStatusView.setVisibility(View.VISIBLE);
			mViewEventStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mViewEventStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mViewEventFormView.setVisibility(View.VISIBLE);
			mViewEventFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mViewEventFormView.setVisibility(show ? View.GONE
									: View.VISIBLE);
						}
					});
		} else {
			// The ViewPropertyAnimator APIs are not available, so simply show
			// and hide the relevant UI components.
			mViewEventStatusView.setVisibility(show ? View.VISIBLE : View.GONE);
			mViewEventFormView.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}
}
