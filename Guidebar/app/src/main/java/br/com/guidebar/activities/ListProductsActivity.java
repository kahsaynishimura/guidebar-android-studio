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
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.bean.Evento;
import br.com.guidebar.bean.Produto;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.SessionManager;
import br.com.guidebar.customviews.CustomAdapterProduct;

import com.google.gson.Gson;

public class ListProductsActivity extends Activity {
	private ListProductsTask mListProductsTask = null;
	private AddPurchaseTask mAddPurchaseTask = null;
	private ListView mLstProductsView;
	private Button btnBuy;
	private CustomAdapterProduct adapter;
	private View mListProductsFormView;
	private View mListProductsStatusView;
	private TextView mListProductsStatusMessageView;
	public SessionManager session;
	private Evento evento = new Evento();
	private ArrayList<Produto> boughtProducts = new ArrayList<Produto>();
	private ArrayList<Produto> listaProdutos = new ArrayList<Produto>();

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_list_products);
		loadComponents();
	}

	private void loadComponents() {
		session = new SessionManager(ListProductsActivity.this);
		mListProductsFormView = findViewById(R.id.list_products_form);
		mListProductsStatusView = findViewById(R.id.status);
		mListProductsStatusMessageView = (TextView) findViewById(R.id.status_message);
		mLstProductsView = (ListView) findViewById(R.id.lstProducts);
		btnBuy = (Button) findViewById(R.id.btnBuy);

		Bundle bundle = getIntent().getExtras();
		evento.setId(bundle.getInt("id_evento"));

		btnBuy.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View view) {
				boughtProducts = new ArrayList<Produto>();
				for (int i = 0; i < adapter.getCount(); i++) {
					if (adapter.quantity_values[i] == null
							|| adapter.quantity_values[i].isEmpty()
							|| adapter.quantity_values[i].equals("0")) {
						adapter.quantity_values[i] = "0";
					} else {
						Produto obj = listaProdutos.get(i);
						obj.setEvento(null);
						obj.setQuantity(Integer
								.parseInt(adapter.quantity_values[i]));
						obj.setIdEvento(evento.getId());
						boughtProducts.add(obj);
					}
				}
				if (mAddPurchaseTask == null) {
					mAddPurchaseTask = new AddPurchaseTask();
					mAddPurchaseTask.execute();
				}
			}
		});

		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		mListProductsStatusMessageView.setTypeface(tf);

		mListProductsStatusMessageView.setText(R.string.progress_loading_data);
		showProgress(true);

		mListProductsTask = new ListProductsTask();
		mListProductsTask.execute();
	}

	class ListProductsTask extends AsyncTask<Void, Void, Void> {

		// JSON Node names
		private static final String TAG_PRICE = "price";
		private static final String TAG_NOME = "name";
		private static final String TAG_PRODUTOS = "products";
		private static final String TAG_ID = "id";
		private static final String TAG_QUANTITY_AVAILABLE = "quantity_available";
		private static final String TAG_PRODUCT = "Product";

		JSONArray array = null;

		@Override
		protected Void doInBackground(Void... params) {
			listaProdutos = new ArrayList<Produto>();
			String url = Guidebar.serverUrl + "products/index/"
					+ evento.getId() + ".json";
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
					array = json.getJSONArray(TAG_PRODUTOS);

					for (int i = 0; i < array.length(); i++) {
						JSONObject c = array.getJSONObject(i);
						Produto obj = new Produto();

						JSONObject product = c.getJSONObject(TAG_PRODUCT);
						obj.setName(product.getString(TAG_NOME));
						obj.setPrice(product.getDouble(TAG_PRICE));
						obj.setId(product.getInt(TAG_ID));
						obj.setAvailableQuantity(product.getInt(TAG_QUANTITY_AVAILABLE));
						listaProdutos.add(obj);
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}
			return null;
		}

		@Override
		protected void onPostExecute(Void a) {
			mListProductsTask = null;

			adapter = new CustomAdapterProduct(ListProductsActivity.this,
					R.layout.product_list_item, listaProdutos);
			if (listaProdutos.isEmpty()) {
				Toast.makeText(ListProductsActivity.this,
						getText(R.string.no_purchases), Toast.LENGTH_SHORT)
						.show();
			}
			mLstProductsView.setAdapter(adapter);

			showProgress(false);
		}

		@Override
		protected void onCancelled() {
			mListProductsTask = null;
			showProgress(false);
		}
	}

	class AddPurchaseTask extends AsyncTask<Void, Void, Boolean> {

		Gson gson = new Gson();
		String json = gson.toJson(boughtProducts);
		String urlPagSeguro = "";
		JSONArray array = null;

		@Override
		protected Boolean doInBackground(Void... params) {
			Boolean success = false;
			String url = Guidebar.serverUrl + "items/addItems.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			parametrosPost.add(new BasicNameValuePair("items", json));
			String respostaRetornada = "";
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(url,
						parametrosPost);
				String resposta = respostaRetornada.toString();
				JSONObject json = new JSONObject(resposta);
				success = json.getBoolean("success");
				urlPagSeguro = json.getString("url");
			} catch (Exception erro) {
				Log.i("erro", "erro = " + erro);
			}
			return success;
		}

		@Override
		protected void onPostExecute(Boolean success) {
			mAddPurchaseTask = null;
			if (success) {
				//TODO: confirm dialog: você será encaminhado para a pagina de pagamento. se quiser pagar depois, acesse o menu minhas compras na tela principal
				Intent viewIntent = new Intent("android.intent.action.VIEW", Uri.parse(urlPagSeguro));
				startActivity(viewIntent);  
				finish();
			}
			showProgress(false);
		}

		@Override
		protected void onCancelled() {
			mAddPurchaseTask = null;
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

			mListProductsStatusView.setVisibility(View.VISIBLE);
			mListProductsStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mListProductsStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mListProductsFormView.setVisibility(View.VISIBLE);
			mListProductsFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mListProductsFormView
									.setVisibility(show ? View.GONE
											: View.VISIBLE);
						}
					});
		} else {
			mListProductsStatusView.setVisibility(show ? View.VISIBLE
					: View.GONE);
			mListProductsFormView
					.setVisibility(show ? View.GONE : View.VISIBLE);
		}
	}
}
