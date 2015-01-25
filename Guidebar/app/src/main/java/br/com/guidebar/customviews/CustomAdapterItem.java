package br.com.guidebar.customviews;

import java.util.ArrayList;
import java.util.List;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONObject;

import android.content.Context;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.activities.ValidateCheckInActivity;
import br.com.guidebar.bean.Item;
import br.com.guidebar.classes.AsyncDelegate;
import br.com.guidebar.classes.ConexaoHttpClient;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.SessionManager;

public class CustomAdapterItem extends ArrayAdapter<Item> {
	private final LayoutInflater inflater;
	private final int resourceId;
	private final Context context;
	private ValidateTask mValidateTask = null;
	private SessionManager session;
	List<Item> lst;
	private AsyncDelegate delegate;

	public CustomAdapterItem(Context context, int resource, List<Item> objects,
			ValidateCheckInActivity validateCheckInActivity) {
		super(context, resource, objects);
		this.inflater = LayoutInflater.from(context);
		session = new SessionManager(context);
		this.lst = objects;
		this.delegate = validateCheckInActivity;
		this.resourceId = resource;
		this.context = context;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		final View view;
		TextView name;
		final EditText quantity;
		Button btnValidate;
		final Integer position1 = position;
		view = inflater.inflate(resourceId, parent, false);
		name = (TextView) view.findViewById(R.id.txtName);
		quantity = (EditText) view.findViewById(R.id.txtQuantity);
		btnValidate = (Button) view.findViewById(R.id.btnValidate);
		quantity.setError(null);
		Item obj = (Item) getItem(position);
		name.setText(obj.getProduto().getName() + " (" + obj.getValidated()
				+ "/" + obj.getQuantity() + ")");
		Typeface tf = Typeface.createFromAsset(context.getAssets(),
				"fonts/Gotham-Book.otf");
		name.setTypeface(tf);
		if (obj.getQuantity() <= obj.getValidated()) {
			btnValidate.setEnabled(false);
		}
		btnValidate.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				v.setEnabled(false);
				Integer position = position1;
				final Item obj = (Item) getItem(position);
				EditText quantity_edit = (EditText) view
						.findViewById(R.id.txtQuantity);
				Boolean cancel = false;
				String sQuantityValidate = quantity_edit.getText().toString();
				Integer quantityValidate = 0;
				if (sQuantityValidate.length() == 0) {
					quantity_edit.setError(context
							.getString(R.string.error_field_required));
					cancel = true;
				} else {
					quantityValidate = Integer.parseInt(sQuantityValidate);
					if (quantityValidate > (obj.getQuantity() - obj
							.getValidated())) {
						quantity_edit.setError(context
								.getString(R.string.error_invalid_quantity));
						cancel = true;
					}

				}
				if (cancel) {
					v.setEnabled(true);
				} else {
					mValidateTask = new ValidateTask();
					mValidateTask.execute(obj.getId(), quantityValidate);
				}
			}
		});
		return view;
	}

	public class ValidateTask extends AsyncTask<Integer, Void, Boolean> {

		@Override
		protected Boolean doInBackground(Integer... params) {
			String urlPost = Guidebar.serverUrl + "items/validateItem.json";
			ArrayList<NameValuePair> parametrosPost = new ArrayList<NameValuePair>();
			parametrosPost.add(new BasicNameValuePair("data[Item][id]",
					params[0].toString()));
			parametrosPost.add(new BasicNameValuePair(
					"data[Item][quantityToValidate]", params[1].toString()));
			parametrosPost.add(new BasicNameValuePair("email", session
					.getUserDetails().get(SessionManager.KEY_EMAIL)));
			parametrosPost.add(new BasicNameValuePair("token", session
					.getUserDetails().get(SessionManager.KEY_ACCESS_TOKEN)));

			String respostaRetornada = "";
			String resposta = "";
			Boolean success = false;
			try {
				respostaRetornada = ConexaoHttpClient.executaHttpPost(urlPost,
						parametrosPost);
				resposta = respostaRetornada.toString();
				resposta = resposta.replaceAll("\\s+", "");
				success = new JSONObject(resposta).getBoolean("success");
			} catch (Exception erro) {
				Toast.makeText(context, "Erro: " + erro, Toast.LENGTH_LONG)
						.show();
			}
			return success;
		}

		@Override
		protected void onPostExecute(final Boolean success) {
			mValidateTask = null;

			if (success) {
				Toast.makeText(context, R.string.success_validate_item,
						Toast.LENGTH_LONG).show();
				delegate.asyncComplete(true);

			} else {
				Toast.makeText(context, R.string.error_validate_item,
						Toast.LENGTH_LONG).show();
			}
		}

		@Override
		protected void onCancelled() {
			mValidateTask = null;
		}
	}
}
