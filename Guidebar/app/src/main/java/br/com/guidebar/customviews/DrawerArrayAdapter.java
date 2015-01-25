package br.com.guidebar.customviews;

import android.content.Context;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import br.com.guidebar.R;

public class DrawerArrayAdapter extends ArrayAdapter<String> {
	private final LayoutInflater inflater;
	private final int resourceId;
	private final Context context;

	public DrawerArrayAdapter(Context context, int resource, String[] objects) {
		super(context, resource, objects);
		this.inflater = LayoutInflater.from(context);
		this.resourceId = resource;
		this.context = context;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View view;
		TextView mnuName;
		view = inflater.inflate(resourceId, parent, false);
		mnuName = (TextView) view.findViewById(R.id.txt);
		mnuName.setText(getItem(position));
		Typeface tf = Typeface.createFromAsset(context.getAssets(),
				"fonts/Gotham-Book.otf");
		mnuName.setTypeface(tf);

		return view;
	}
}
