package br.com.guidebar.customviews;

import java.util.ArrayList;
import java.util.List;

import android.app.Activity;
import android.content.Context;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Spinner;
import android.widget.TextView;
import br.com.guidebar.R;
import br.com.guidebar.bean.Produto;

public class CustomAdapterProduct extends ArrayAdapter<Produto> {
	private final int resourceId;
	private final Context context;

	public String[] quantity_values;
	public String[] available_quantity_values;

	@Override
	public Produto getItem(int position) {
		// TODO Auto-generated method stub
		return super.getItem(position);
	}

	private List<Produto> items;

	public CustomAdapterProduct(Context context, int resource,
			List<Produto> items) {
		super(context, resource, items);
		this.resourceId = resource;
		this.context = context;
		this.items = items;
		quantity_values = new String[items.size()];
		available_quantity_values = new String[items.size()];

	}

	public static class ProductHolder {
		Produto product;
		TextView name;
		TextView price;
		Spinner spnQuantity;
		int ref;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		View row = convertView;
		final ProductHolder holder = new ProductHolder();

		LayoutInflater inflater = ((Activity) context).getLayoutInflater();
		row = inflater.inflate(resourceId, parent, false);

		holder.product = items.get(position);

		holder.name = (TextView) row.findViewById(R.id.txtProductName);
		holder.price = (TextView) row.findViewById(R.id.txtProductPrice);
		holder.spnQuantity = (Spinner) row.findViewById(R.id.spnQuantity);
		ArrayList<Integer> values = new ArrayList<Integer>();
		for (int i = 0; i <= holder.product.getAvailableQuantity(); i++) {
			values.add(i);
		}
		holder.spnQuantity.setAdapter(new ArrayAdapter<Integer>(context,
				android.R.layout.simple_list_item_1, values));
		holder.spnQuantity
				.setOnItemSelectedListener(new OnItemSelectedListener() {

					@Override
					public void onItemSelected(AdapterView<?> parentView,
							View selectedItemView, int position, long id) {
						quantity_values[holder.ref] = position + "";
					}

					@Override
					public void onNothingSelected(AdapterView<?> arg0) {
						// TODO Auto-generated method stub

					}
				});

		holder.ref = position;
		row.setTag(holder);

		setupItem(holder);
		return row;
	}

	private void setupItem(ProductHolder holder) {
		Typeface tf = Typeface.createFromAsset(context.getAssets(),
				"fonts/Gotham-Book.otf");
		holder.name.setTypeface(tf);
		holder.price.setTypeface(tf);
		holder.name.setText(holder.product.getName());
		holder.price.setText("R$ "
				+ String.format("%1.2f", holder.product.getPrice()));
	}

}
