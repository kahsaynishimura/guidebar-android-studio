package br.com.guidebar.customviews;

import java.util.List;

import android.content.Context;
import android.content.Intent;
import android.graphics.Typeface;
import android.net.Uri;
import android.text.Html;
import android.text.SpannableStringBuilder;
import android.text.Spanned;
import android.text.style.URLSpan;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import br.com.guidebar.R;
import br.com.guidebar.bean.Compra;
import br.com.guidebar.bean.Item;
import br.com.guidebar.classes.ImageDownloaderTask;
import br.com.guidebar.enums.EnumEstadoPagamento.EstadoPagamento;

public class CustomAdapterPurchase extends ArrayAdapter<Compra> {
	private final LayoutInflater inflater;
	private final int resourceId;
	private final Context context;

	public CustomAdapterPurchase(Context context, int resource,
			List<Compra> objects) {
		super(context, resource, objects);
		this.inflater = LayoutInflater.from(context);
		this.resourceId = resource;
		this.context = context;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View view;
		view = inflater.inflate(resourceId, parent, false);

		TextView eventName = (TextView) view.findViewById(R.id.txtEventName);
		TextView description = (TextView) view
				.findViewById(R.id.txtDescription);
		TextView value = (TextView) view.findViewById(R.id.txtTicketValue);
		TextView quantity = (TextView) view.findViewById(R.id.txtQuantity);
		TextView urlPayment = (TextView) view.findViewById(R.id.txtUrlPayment);
		TextView lblUrlPayment = (TextView) view
				.findViewById(R.id.lblUrlPayment);
		TextView mStatusPaymentView = (TextView) view
				.findViewById(R.id.lblStatusPayment);
		Button btnSeeMore = (Button) view.findViewById(R.id.btnSeeMore);

		final ImageView image;
		final View formDetails = (View) view.findViewById(R.id.form_details);

		final Compra obj = (Compra) getItem(position);
		eventName.setText(Html.fromHtml("<b>" + obj.getEvento().getNome()
				+ "</b>"));

		for (Item item : obj.getItems()) {

			description.setText(description.getText().toString()
					+ Html.fromHtml(item.getQuantity() + "x "
							+ item.getProduto().getName() + " a R$"
							+ item.getPrice() + "= <b>R$" + item.getSubtotal()
							+ "</b><br />"));
		}
		try {
			image = (ImageView) view.findViewById(R.id.imgQRCode);
		} catch (ClassCastException e) {
			Log.e("Error",
					"Your layout must provide an image and a text view with ID's icon and text.",
					e);
			throw e;
		}
		if (obj.getItemQuantity() != 3 && obj.getItemQuantity() != 4) {
			EstadoPagamento estadoPagamento = EstadoPagamento
					.getEstadoPagamentoPorId(obj.getItemQuantity());
			mStatusPaymentView.setText(Html
					.fromHtml("Estado do pagamento:<br/> <b>"
							+ estadoPagamento.getNome() + "</b>"));
			mStatusPaymentView.setVisibility(View.VISIBLE);
			urlPayment.setVisibility(View.VISIBLE);
			lblUrlPayment.setVisibility(View.VISIBLE);
			image.setVisibility(View.GONE);
			urlPayment.setText(Html.fromHtml("<b>" + obj.getPaymentURL()
					+ "</b>"));
		} else {
			String url = "https://chart.googleapis.com/chart?cht=qr&chs=150x150&choe=UTF-8&chl=http://guidebar.com.br/purchases/processCode?code="
					+ obj.getQrCode();

			if (image != null) {
				new ImageDownloaderTask(image).execute(url);
			}
			image.setVisibility(View.VISIBLE);
			lblUrlPayment.setVisibility(View.GONE);
			mStatusPaymentView.setVisibility(View.GONE);
			urlPayment.setVisibility(View.GONE);
		}
		Typeface tf = Typeface.createFromAsset(context.getAssets(),
				"fonts/Gotham-Book.otf");
		eventName.setTypeface(tf);
		description.setTypeface(tf);
		value.setTypeface(tf);
		quantity.setTypeface(tf);
		lblUrlPayment.setTypeface(tf);
		urlPayment.setTypeface(tf);
		btnSeeMore.setTypeface(tf);
		mStatusPaymentView.setTypeface(tf);
		makeTextViewHyperlink(urlPayment);

		btnSeeMore.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				if (formDetails.getVisibility() == View.VISIBLE) {
					formDetails.setVisibility(View.GONE);
					((Button) v).setText(R.string.action_see_more);
				} else {
					formDetails.setVisibility(View.VISIBLE);
					((Button) v).setText(R.string.action_hide);
				}
			}

		});
		urlPayment.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				Intent viewIntent = new Intent("android.intent.action.VIEW",
						Uri.parse(obj.getPaymentURL()));
				context.startActivity(viewIntent);
			}
		});

		return view;
	}

	private void makeTextViewHyperlink(TextView tv) {
		SpannableStringBuilder ssb = new SpannableStringBuilder();
		ssb.append(tv.getText());
		ssb.setSpan(new URLSpan("#"), 0, ssb.length(),
				Spanned.SPAN_EXCLUSIVE_EXCLUSIVE);
		tv.setText(ssb, TextView.BufferType.SPANNABLE);
	}
}
