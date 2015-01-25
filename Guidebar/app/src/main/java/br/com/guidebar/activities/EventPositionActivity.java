package br.com.guidebar.activities;

import android.support.v4.app.FragmentActivity;

public class EventPositionActivity extends FragmentActivity {
//implements
//// OnMapClickListener,
//		OnMapLongClickListener {

//	SupportMapFragment fragment;
//	GPSTracker gps;
//	private GoogleMap mMap;
//	private String latitude = "";
//	private String longitude = "";
//	private Evento evento = new Evento();
//	private EventPositionTask mEventPositionTask;
//	private CarregarLocalizacaoTask mCarregarLocalizacaoTask;
//
//	@Override
//	protected void onCreate(Bundle savedInstanceState) {
//		super.onCreate(savedInstanceState);
//		setContentView(R.layout.activity_event_position);
//
//		loadComponents();
//	}
//
//	private void loadComponents() {
//		Bundle parametros = getIntent().getExtras();
//
//		evento.setId(parametros.getInt("id_evento"));
//		fragment = (SupportMapFragment) getSupportFragmentManager()
//				.findFragmentById(R.id.map);
//		mMap = fragment.getMap();
//
//		// mMap.setOnMapClickListener(this);
//		mMap.setOnMapLongClickListener(this);
//		TextView txtTapText = (TextView) findViewById(R.id.tapText);
//		Typeface tf = Typeface.createFromAsset(getAssets(),
//				"fonts/Gotham-Book.otf");
//		txtTapText.setTypeface(tf);
//		mCarregarLocalizacaoTask = new CarregarLocalizacaoTask();
//		mCarregarLocalizacaoTask.execute();
//	}
//
//	// @Override
//	// public void onMapClick(LatLng point) {
//	//
//	// latitude = point.latitude + "";
//	// longitude = point.longitude + "";
//	// attemptSave();
//	//
//	// }
//
//	public class CarregarLocalizacaoTask extends AsyncTask<Void, Void, Void> {
//
//		// JSON Node names
//		private static final String TAG_LATITUDE = "latitude";
//		private static final String TAG_LONGITUDE = "longitude";
//		private static final String TAG_NOME = "nome";
//		private static final String TAG_DATA_INICIO = "data_inicio";
//		private static final String TAG_EVENTOS = "eventos";
//		JSONArray eventos = null;
//
//		@Override
//		protected Void doInBackground(Void... params) {
//			String url = Guidebar.serverUrl + "buscarLocalizacao.php";
//
//			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
//			parametrosPost.add(new BasicNameValuePair("id_evento", evento
//					.getId().toString()));
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
//						evento.setLatitude(!c.getString(TAG_LATITUDE).equals("null")?c.getString(TAG_LATITUDE):"");
//						evento.setLongitude(!c.getString(TAG_LONGITUDE).equals("null")?c.getString(TAG_LONGITUDE):"");
//						evento.setNome(c.getString(TAG_NOME));
//						evento.setDataInicio(c.getString(TAG_DATA_INICIO)
//								.substring(0, 10));
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
//		protected void onCancelled() {
//			mCarregarLocalizacaoTask = null;
//		}
//
//		@Override
//		protected void onPostExecute(Void obj) {
//			mCarregarLocalizacaoTask = null;
//			gps = new GPSTracker(EventPositionActivity.this);
//			LatLng latLng;
//			if (TextUtils.isEmpty(evento.getLatitude())) {
//				latLng = getUserLocation();
//			} else {
//				latLng = new LatLng(Double.parseDouble(evento.getLatitude()),
//						Double.parseDouble(evento.getLongitude()));
//			}
//			if (latLng != null) {
//				mMap.animateCamera(CameraUpdateFactory.newLatLngZoom(latLng,
//						12.0f));
//
//				if (!TextUtils.isEmpty(evento.getLatitude())) {
//					mMap.addMarker(new MarkerOptions().position(latLng)
//					// .icon(BitmapDescriptorFactory
//					// .fromResource(R.drawable.icon))
//							.title(evento.getNome())
//							.snippet(
//									new DateDisplayPicker(
//											EventPositionActivity.this)
//											.getDateForPresenting(evento
//													.getDataInicio())));
//				}
//				configuraPosicao(mMap, latLng);
//			}
//		}
//
//		public LatLng getUserLocation() {
//			LatLng ll = null;
//			// check if GPS enabled
//			if (gps.canGetLocation()) {
//				double latitude = gps.getLatitude();
//				double longitude = gps.getLongitude();
//
//				ll = new LatLng(latitude, longitude);
//				// \n is for new line
//				Toast.makeText(
//						getApplicationContext(),
//						"Sua posição é - \nLat: " + latitude + "\nLong: "
//								+ longitude, Toast.LENGTH_LONG).show();
//			} else {
//				// can't get location
//				// GPS or Network is not enabled
//				// Ask user to enable GPS/network in settings
//				gps.showSettingsAlert();
//			}
//			return ll;
//		}
//	}
//
//	private void configuraPosicao(GoogleMap map, LatLng latLng) {
//		map.moveCamera(CameraUpdateFactory.newLatLngZoom(latLng, 15));
//		map.animateCamera(CameraUpdateFactory.zoomTo(10), 2000, null);
//
//		CameraPosition cameraPosition = new CameraPosition.Builder()
//				.target(latLng).zoom(17).bearing(90).tilt(45).build();
//		map.animateCamera(CameraUpdateFactory.newCameraPosition(cameraPosition));
//	}
//
//	public void attemptSave() {
//		if (mEventPositionTask != null) {
//			return;
//		}
//
//		mEventPositionTask = new EventPositionTask();
//		mEventPositionTask.execute();
//
//	}
//
//	public class EventPositionTask extends AsyncTask<Void, Void, Boolean> {
//
//		@Override
//		protected Boolean doInBackground(Void... params) {
//			String urlPost = Guidebar.serverUrl + "gravarLocalizacao.php";
//			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
//
//			parametrosPost.add(new BasicNameValuePair("id_evento", evento
//					.getId().toString()));
//			parametrosPost.add(new BasicNameValuePair("latitude", latitude));
//			parametrosPost.add(new BasicNameValuePair("longitude", longitude));
//			String respostaRetornada = "";
//
//			String resposta = "";
//			try {
//				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
//						parametrosPost);
//				resposta = respostaRetornada.toString();
//				resposta = resposta.replaceAll("\\s+", "");
//			} catch (Exception erro) {
//				Log.i("EventPositionActivity", "Erro: " + erro);
//			}
//			return resposta.equals("1");
//		}
//
//		@Override
//		protected void onCancelled() {
//			mEventPositionTask = null;
//		}
//
//		@Override
//		protected void onPostExecute(Boolean success) {
//			mEventPositionTask = null;
//			if (success) {
//				Intent intent = new Intent(EventPositionActivity.this,
//						AddTicketsActivity.class);
//				intent.putExtra("id_evento", evento.getId());
//				startActivity(intent);
//				finish();
//			} else {
//				Toast.makeText(EventPositionActivity.this,
//						getString(R.string.error_save_address),
//						Toast.LENGTH_SHORT).show();
//			}
//		}
//	}
//
//	public void next(View v) {
//		Intent intent = new Intent(EventPositionActivity.this,
//				AddTicketsActivity.class);
//		intent.putExtra("id_evento", evento.getId());
//		startActivity(intent);
//		finish();
//	}
//
//	@Override
//	public void onMapLongClick(LatLng point) {
//
//		latitude = point.latitude + "";
//		longitude = point.longitude + "";
//		attemptSave();
//	}
//
//	// @Override
//	// public void onMapClick(LatLng arg0) {
//	//
//	//
//	// }
}