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
import android.util.Log;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.bean.Compra;
import br.com.guidebar.bean.Evento;
import br.com.guidebar.bean.Item;
import br.com.guidebar.classes.AsyncDelegate;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.SessionManager;
import br.com.guidebar.customviews.CustomAdapterItem;

public class ValidateCheckInActivity extends Activity implements AsyncDelegate {
	private LoadDadosQRCode mLoadDadosIngresso;

	private String url = "";
	private Evento evento = new Evento();

	private ListView mLstItemsView;
	private Button btnReadCode;
	private Button btnStopValidation;
	private View mValidateCheckinFormView;
	private View mValidateCheckinStatusView;
	private TextView mValidateCheckinStatusMessageView;
	private SessionManager session;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_validate_check_in);
		loadComponents();
	}

	public void loadComponents() {
		mLstItemsView = (ListView) findViewById(R.id.itemListView);
		btnReadCode = (Button) findViewById(R.id.btnReadCode);
		btnStopValidation = (Button) findViewById(R.id.btnStop);
		mValidateCheckinFormView = findViewById(R.id.validate_checkin_form);
		mValidateCheckinStatusView = findViewById(R.id.status);
		mValidateCheckinStatusMessageView = (TextView) findViewById(R.id.status_message);

		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");

		btnReadCode.setTypeface(tf);
		btnStopValidation.setTypeface(tf);
		Bundle parametros = getIntent().getExtras();
		evento.setId(parametros.getInt("id_evento"));

		session = new SessionManager(ValidateCheckInActivity.this);
	}

	public void stopValidation(View v) {
		finish();
	}

	public void readCode(View v) {
		// Intent intent = new Intent(ValidateCheckInActivity.this,
		// CaptureActivity.class);
		// Intent intent = new
		// Intent("com.google.zxing.client.android.SCAN");
		// intent.putExtra("SCAN_MODE", "QR_CODE_MODE");
		// startActivityForResult(intent, 0);
		// IntentIntegrator integrator = new IntentIntegrator(
		// ValidateCheckInActivity.this);
		// integrator.initiateScan();
		Intent intent = new Intent("com.google.zxing.client.android.SCAN");
		intent.putExtra("SCAN_MODE", "QR_CODE_MODE");
		startActivityForResult(intent, 0);
	}

	@Override
	public void onActivityResult(int requestCode, int resultCode, Intent data) {
		if (data != null) {
			boolean cancel = false;
			try {
				url = data.getStringExtra("SCAN_RESULT");
			} catch (NumberFormatException e) {
				cancel = true;
			}
			if (!cancel) {
				mValidateCheckinStatusMessageView
						.setText(getString(R.string.progress_loading_data));
				showProgress(true);
				mLoadDadosIngresso = new LoadDadosQRCode();
				mLoadDadosIngresso.execute();
			}
		}
	}

	public class LoadDadosQRCode extends AsyncTask<Void, Void, Boolean> {
		private static final String TAG_DESCRICAO = "description";
		private static final String TAG_QUANTIDADE = "quantity";
		private static final String TAG_VALIDADO = "validated";
		private static final String TAG_ITEM_QUANTITY = "item_quantity";
		private static final String TAG_NOME = "name";
		private static final String TAG_PURCHASE = "purchase";
		private static final String TAG_PURCHASE_OBJECT = "Purchase";
		private static final String TAG_ITEM = "Item";
		private static final String TAG_EVENT_PURCHASE = "EventPurchase";
		private static final String TAG_SUBTOTAL = "subtotal";
		private static final String TAG_PRICE = "price";
		private static final String TAG_PRODUCT = "Product";
		private static final String TAG_ID = "id";
		private Compra obj = new Compra();
		Boolean success = false;

		@Override
		protected Boolean doInBackground(Void... params) {
			String urlPost = url;
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));
			String respostaRetornada = "";

			JSONObject objJSON = null;
			String resposta = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				if (!resposta.replaceAll("\\s+", "").equals("[]")) {
					JSONObject json = new JSONObject(resposta);
					try {
						objJSON = json.getJSONObject(TAG_PURCHASE);
						JSONObject purchase = objJSON
								.getJSONObject(TAG_PURCHASE_OBJECT);
						JSONArray items = objJSON.getJSONArray(TAG_ITEM);
						JSONObject event = objJSON
								.getJSONObject(TAG_EVENT_PURCHASE);
						if (event.getInt(TAG_ID) == evento.getId()) {
							success = true;
						} else {
							success = false;
						}
						obj.getEvento().setNome(event.getString(TAG_NOME));
						for (int j = 0; j < items.length(); j++) {
							JSONObject itemJSON = items.getJSONObject(j);
							Item item = new Item();
							item.setId(itemJSON.getInt(TAG_ID));
							item.setQuantity(itemJSON.getInt(TAG_QUANTIDADE));
							item.setValidated(itemJSON.getInt(TAG_VALIDADO));
							item.setPrice(itemJSON.getDouble(TAG_PRICE));
							item.setSubtotal(itemJSON.getDouble(TAG_SUBTOTAL));
							item.getProduto().setDescricao(
									itemJSON.getJSONObject(TAG_PRODUCT)
											.getString(TAG_DESCRICAO));
							item.getProduto().setName(
									itemJSON.getJSONObject(TAG_PRODUCT)
											.getString(TAG_NOME));
							obj.getItems().add(item);
						}
						obj.setItemQuantity(Integer.parseInt(purchase
								.getString(TAG_ITEM_QUANTITY)));
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}
			return !resposta.replaceAll("\\s+", "").equals("[]");
		}

		@Override
		protected void onPostExecute(final Boolean ok) {
			mLoadDadosIngresso = null;
			showProgress(false);
			if (ok && success) {

				ArrayAdapter<Item> aa = new CustomAdapterItem(
						ValidateCheckInActivity.this, R.layout.item_list_item,
						obj.getItems(), ValidateCheckInActivity.this);
				mLstItemsView.setAdapter(aa);
				mLstItemsView.setVisibility(View.VISIBLE);
			} else {
				if (success == false) {
					obj.getItems().removeAll(obj.getItems());
					ArrayAdapter<Item> aa = new CustomAdapterItem(
							ValidateCheckInActivity.this,
							R.layout.item_list_item, obj.getItems(),
							ValidateCheckInActivity.this);
					mLstItemsView.setAdapter(aa);
					mLstItemsView.setVisibility(View.VISIBLE);
					Toast.makeText(ValidateCheckInActivity.this,
							R.string.error_ticket_from_wrong_event,
							Toast.LENGTH_SHORT).show();
				} else {
					Toast.makeText(ValidateCheckInActivity.this,
							R.string.error_load_ticket_validation,
							Toast.LENGTH_SHORT).show();
				}
			}
		}

		@Override
		protected void onCancelled() {
			mLoadDadosIngresso = null;
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

			mValidateCheckinStatusView.setVisibility(View.VISIBLE);
			mValidateCheckinStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mValidateCheckinStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mValidateCheckinFormView.setVisibility(View.VISIBLE);
			mValidateCheckinFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mValidateCheckinFormView
									.setVisibility(show ? View.GONE
											: View.VISIBLE);
						}
					});
		} else {
			// The ViewPropertyAnimator APIs are not available, so simply show
			// and hide the relevant UI components.
			mValidateCheckinStatusView.setVisibility(show ? View.VISIBLE
					: View.GONE);
			mValidateCheckinFormView.setVisibility(show ? View.GONE
					: View.VISIBLE);
		}
	}

	@Override
	public void asyncComplete(boolean success) {
		mLoadDadosIngresso = new LoadDadosQRCode();
		mLoadDadosIngresso.execute();
		((CustomAdapterItem) mLstItemsView.getAdapter()).notifyDataSetChanged();
	}
}
