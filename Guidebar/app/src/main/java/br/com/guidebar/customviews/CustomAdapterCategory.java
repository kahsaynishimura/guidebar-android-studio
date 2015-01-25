package br.com.guidebar.customviews;

import java.util.List;

import android.content.Context;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import br.com.guidebar.R;
import br.com.guidebar.bean.Categoria;

public class CustomAdapterCategory extends ArrayAdapter<Categoria> {
	private final LayoutInflater inflater;
	private final int resourceId;
	private final Context context;

	public CustomAdapterCategory(Context context, int resource,
			List<Categoria> objects) {
		super(context, resource, objects);
		this.inflater = LayoutInflater.from(context);
		this.resourceId = resource;
		this.context = context;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View view;
		TextView name;
		view = inflater.inflate(resourceId, parent, false);
		name = (TextView) view.findViewById(R.id.txtName);
		Categoria obj = (Categoria) getItem(position);
		name.setText(obj.getNome());
		Typeface tf = Typeface.createFromAsset(context.getAssets(),
				"fonts/Gotham-Book.otf");
		name.setTypeface(tf);
	
		return view;
	}

}
