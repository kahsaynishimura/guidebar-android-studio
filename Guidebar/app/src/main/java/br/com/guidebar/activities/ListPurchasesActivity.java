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
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.bean.Compra;
import br.com.guidebar.bean.Item;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.SessionManager;
import br.com.guidebar.customviews.CustomAdapterPurchase;

public class ListPurchasesActivity extends Activity {
	private ListPurchaseTask mListPurchaseTask;
	private ListView mLstPurchasesView;
	private View mListPurchasesFormView;
	private View mListPurchasesStatusView;
	private TextView mListPurchasesStatusMessageView;
	public SessionManager session;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_list_purchases);
		loadComponents();
	}

	private void loadComponents() {
		session = new SessionManager(ListPurchasesActivity.this);
		mLstPurchasesView = (ListView) findViewById(R.id.lstPurchases);
		mListPurchasesFormView = findViewById(R.id.list_purchases_form);
		mListPurchasesStatusView = findViewById(R.id.status);
		mListPurchasesStatusMessageView = (TextView) findViewById(R.id.status_message);
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		mListPurchasesStatusMessageView.setTypeface(tf);

		mListPurchasesStatusMessageView.setText(R.string.progress_loading_data);
		showProgress(true);

		mListPurchaseTask = new ListPurchaseTask();
		mListPurchaseTask.execute();
	}

	class ListPurchaseTask extends AsyncTask<Void, Void, ArrayList<Compra>> {

		// JSON Node names
		private static final String TAG_QUANTIDADE = "quantity";
		private static final String TAG_URL_PAGAMENTO = "payment_url";
		private static final String TAG_PRICE = "price";
		private static final String TAG_DESCRICAO = "description";
		private static final String TAG_CODIGO_TRANSACAO = "transaction_code";
		private static final String TAG_NOME = "name";
		private static final String TAG_SUBTOTAL = "subtotal";
		private static final String TAG_ITEM_QUANTITY = "item_quantity";
		private static final String TAG_VALIDADO = "validated";
		private static final String TAG_COMPRAS = "purchases";
		private static final String TAG_ID = "id";
		private static final String TAG_PRODUCT = "Product";
		private static final String TAG_ITEM = "Item";
		private static final String TAG_PURCHASE = "Purchase";
		private static final String TAG_QR_CODE = "qr_code";
		private static final String TAG_EVENT_PURCHASE = "EventPurchase";

		JSONArray array = null;

		@Override
		protected ArrayList<Compra> doInBackground(Void... params) {
			ArrayList<Compra> listaCompras = new ArrayList<Compra>();
			String url = Guidebar.serverUrl + "purchases/myPurchases.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
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
					array = json.getJSONArray(TAG_COMPRAS);

					for (int i = 0; i < array.length(); i++) {
						JSONObject c = array.getJSONObject(i);
						Compra obj = new Compra();

						JSONArray items = c.getJSONArray(TAG_ITEM);
						JSONObject purchase = c.getJSONObject(TAG_PURCHASE);
						JSONObject event = c.getJSONObject(TAG_EVENT_PURCHASE);
						obj.getEvento().setNome(event.getString(TAG_NOME));
						for (int j = 0; j < items.length(); j++) {
							JSONObject itemJSON = items.getJSONObject(j);
							Item item = new Item();
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
						try {
							obj.setPaymentURL(purchase
									.getString(TAG_URL_PAGAMENTO) + "");
						} catch (Exception e) {
							e.printStackTrace();
							obj.setPaymentURL("");
						}
						try {
							obj.setTransactionCode(purchase
									.getString(TAG_CODIGO_TRANSACAO));
						} catch (Exception e) {
							obj.setTransactionCode("");
						}
						obj.setItemQuantity(Integer.parseInt(purchase
								.getString(TAG_ITEM_QUANTITY)));
						try {
							obj.setQrCode(purchase.getString(TAG_QR_CODE));
						} catch (Exception e) {
							obj.setQrCode("");
						}
						obj.setId(purchase.getInt(TAG_ID));
						listaCompras.add(obj);
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}
			return listaCompras;
		}

		@Override
		protected void onPostExecute(final ArrayList<Compra> listaCompras) {
			mListPurchaseTask = null;

			ArrayAdapter<Compra> aa = new CustomAdapterPurchase(
					ListPurchasesActivity.this, R.layout.purchase_list_item,
					listaCompras);
			if (listaCompras.isEmpty()) {
				Toast.makeText(ListPurchasesActivity.this,
						getText(R.string.no_purchases), Toast.LENGTH_SHORT)
						.show();
			}
			mLstPurchasesView.setAdapter(aa);

			showProgress(false);
		}

		@Override
		protected void onCancelled() {
			mListPurchaseTask = null;
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

			mListPurchasesStatusView.setVisibility(View.VISIBLE);
			mListPurchasesStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mListPurchasesStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mListPurchasesFormView.setVisibility(View.VISIBLE);
			mListPurchasesFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mListPurchasesFormView
									.setVisibility(show ? View.GONE
											: View.VISIBLE);
						}
					});
		} else {
			mListPurchasesStatusView.setVisibility(show ? View.VISIBLE
					: View.GONE);
			mListPurchasesFormView.setVisibility(show ? View.GONE
					: View.VISIBLE);
		}
	}
}
