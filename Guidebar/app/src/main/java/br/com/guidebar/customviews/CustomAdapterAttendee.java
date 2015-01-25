package br.com.guidebar.customviews;

import java.util.List;

import android.content.Context;
import android.graphics.Typeface;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;
import br.com.guidebar.R;
import br.com.guidebar.bean.Participacao;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.ImageDownloaderTask;

public class CustomAdapterAttendee extends ArrayAdapter<Participacao> {
	private final LayoutInflater inflater;
	private final static String TAG = "CustomAdapterAttendee";
	private final int resourceId;
	private Context context;

	public CustomAdapterAttendee(Context context, int resource,
			List<Participacao> objects) {
		super(context, resource, objects);
		this.inflater = LayoutInflater.from(context);
		this.resourceId = resource;
		this.context = context;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View view;
		TextView userName;
		final ImageView image;
		view = inflater.inflate(resourceId, parent, false);
		try {
			image = (ImageView) view.findViewById(R.id.imgUserProfile);
			userName = (TextView) view.findViewById(R.id.lblUserName);

		} catch (ClassCastException e) {
			Log.e(TAG,
					"Your layout must provide an image and a text view with ID's icon and text.",
					e);
			throw e;
		}

		Typeface tf = Typeface.createFromAsset(context.getAssets(),
				"fonts/Gotham-Book.otf");
		userName.setTypeface(tf);
		Participacao obj = getItem(position);
		if (obj.getUsuario().getThumbnail().contains("http") == false) {
			String thumb = Guidebar.serverUrl + "img/"
					+ obj.getUsuario().getThumbnail();
			obj.getUsuario().setThumbnail(thumb);
		}
		if (image != null) {
			new ImageDownloaderTask(image).execute(obj.getUsuario()
					.getThumbnail());
		}

		userName.setText(obj.getUsuario().getNome());
		return view;
	}

}
