package br.com.guidebar.customviews;

import java.util.Calendar;
import java.util.List;

import android.content.Context;
import android.content.Intent;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.RatingBar;
import android.widget.TextView;
import br.com.guidebar.R;
import br.com.guidebar.activities.ListEventsActivity;
import br.com.guidebar.activities.ViewEventActivity;
import br.com.guidebar.bean.Evento;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.ImageDownloaderTask;

public class CustomAdapterEvent extends ArrayAdapter<Evento> {

	private final Context context;

	private LayoutInflater layoutInflater;

	private List<Evento> listData;

	public CustomAdapterEvent(Context context, int resource,
			List<Evento> objects) {
		super(context, resource, objects);
		// this.inflater = LayoutInflater.from(context);
		this.context = context;
		this.listData = objects;
		layoutInflater = LayoutInflater.from(context);
	}

	static class ViewHolder {
		TextView nameView;
		TextView descriptionView;
		TextView dateView;
		ImageView imageView;
		// Button btnBuyTicket;
		RatingBar rtbEvaluation;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		ViewHolder holder;
		if (convertView == null) {
			convertView = layoutInflater
					.inflate(R.layout.event_list_item, null);
			holder = new ViewHolder();

			holder.nameView = (TextView) convertView
					.findViewById(R.id.lblEventName);
			holder.descriptionView = (TextView) convertView
					.findViewById(R.id.lblEventDescription);
			holder.dateView = (TextView) convertView
					.findViewById(R.id.lblEventDate);
			// holder.btnBuyTicket = (Button) convertView
			// .findViewById(R.id.btnBuyTicket);
			holder.rtbEvaluation = (RatingBar) convertView
					.findViewById(R.id.rtbEvaluation);
			holder.imageView = (ImageView) convertView
					.findViewById(R.id.imgEventFolder);
			convertView.setTag(holder);
		} else {
			holder = (ViewHolder) convertView.getTag();
		}

		final Evento evento = (Evento) listData.get(position);
		evento.setThumbnail(Guidebar.serverUrl + "img/" + evento.getFilename());
		holder.rtbEvaluation.setClickable(false);
		holder.rtbEvaluation.setRating(evento.getAvaliacaoMedia());

		holder.nameView.setText(evento.getNome());
		holder.descriptionView.setText(evento.getDescricao());
		holder.dateView.setText(new DateDisplayPicker(getContext())
				.getDateForPresenting(evento.getDataInicio().substring(0, 10)));
		Typeface tf = Typeface.createFromAsset(context.getAssets(),
				"fonts/Gotham-Book.otf");
		holder.nameView.setTypeface(tf);
		holder.descriptionView.setTypeface(tf);
		holder.dateView.setTypeface(tf);
		// holder.btnBuyTicket.setTypeface(tf);
		Calendar c = Calendar.getInstance();

		c.set(Calendar.HOUR_OF_DAY, 0);
		c.set(Calendar.MINUTE, 5);
		String data = getDateForPresenting(evento.getDataInicio().substring(0,
				10));

		c.set(Calendar.YEAR, DateDisplayPicker.getYear(data, "/"));
		c.set(Calendar.DAY_OF_MONTH, DateDisplayPicker.getDay(data, "/"));
		c.set(Calendar.MONTH, ((DateDisplayPicker.getMonth(data, "/")) - 1));
		Calendar atual = Calendar.getInstance();
		atual.set(Calendar.HOUR_OF_DAY, 0);
		atual.set(Calendar.MINUTE, 1);

		// TODO: ver se vai ficar assim mesmo a compra de ingresso
		// holder.btnBuyTicket.setVisibility(View.VISIBLE);

		// holder.btnBuyTicket.setOnClickListener(new View.OnClickListener() {
		//
		// @Override
		// public void onClick(View v) {
		// v.setEnabled(false);
		// Intent i = new Intent(context, TicketActivity.class);
		// i.putExtra("id_evento", evento.getId());
		// i.putExtra("id_promotor", evento.getPromotor().getId());
		// context.startActivity(i);
		// // v.setEnabled(true);
		// }
		// });
		final Intent ITelaVisualizacao = new Intent(context,
				ViewEventActivity.class);
		convertView.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
//				v.setEnabled(false);
//				v.setBackgroundColor(context.getResources().getColor(
//						R.color.light_grey));
				
				ITelaVisualizacao.putExtra("id", evento.getId());
				((ListEventsActivity) context).startActivity(
						ITelaVisualizacao);
				// v.setEnabled(true);
			}
		});

		if (holder.imageView != null) {
			new ImageDownloaderTask(holder.imageView).execute(evento
					.getThumbnail());
		}

		return convertView;

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

}