package br.com.guidebar.customviews;

import java.util.ArrayList;

import android.content.Context;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.GridView;
import android.widget.ImageView;
import br.com.guidebar.bean.Imagem;
import br.com.guidebar.classes.Guidebar;
import br.com.guidebar.classes.ImageDownloaderTask;

public class ImageAdapter extends BaseAdapter {
	private Context mContext;
	private ArrayList<Imagem> mThumbPaths = new ArrayList<Imagem>();

	public ImageAdapter(Context context, ArrayList<Imagem> listaCaminhos) {
		mContext = context;
		mThumbPaths = listaCaminhos;
	}

	public int getCount() {
		return mThumbPaths.size();
	}

	public Object getItem(int position) {
		return null;
	}

	public long getItemId(int position) {
		return 0;
	}

	// create a new ImageView for each item referenced by the Adapter
	public View getView(int position, View convertView, ViewGroup parent) {
		ImageView imageView;
		if (convertView == null) { // if it's not recycled, initialize some
									// attributes
			imageView = new ImageView(mContext);
			imageView.setLayoutParams(new GridView.LayoutParams(100, 100));
			imageView.setScaleType(ImageView.ScaleType.CENTER_CROP);
			imageView.setPadding(8, 8, 8, 8);
		} else {
			imageView = (ImageView) convertView;
		}

		Imagem obj = (Imagem) mThumbPaths.get(position);

		String caminho = Guidebar.serverUrl + "" + obj.getCaminho();

		if (imageView != null) {
			new ImageDownloaderTask(imageView).execute(caminho);
		}
		return imageView;
	}

}