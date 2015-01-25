package br.com.guidebar.activities;

import java.util.ArrayList;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.annotation.TargetApi;
import android.app.Activity;
import android.content.Intent;
import android.graphics.Typeface;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.TextView;
import br.com.guidebar.R;
import br.com.guidebar.bean.Categoria;
import br.com.guidebar.customviews.CustomAdapterCategory;
import br.com.guidebar.enums.EnumCategoria.ECategoria;

public class ListCategoriesActivity extends Activity {

	private ListView mLstCategoriesView;
	private View mListCategoriesFormView;
	private View mListCategoriesStatusView;
	private TextView mListCategoriesStatusMessageView;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_list_categories);
		loadComponents();
	}

	private void loadComponents() {
		mLstCategoriesView = (ListView) findViewById(R.id.lstCategories);
		mListCategoriesFormView = findViewById(R.id.list_categories_form);
		mListCategoriesStatusView = findViewById(R.id.status);
		mListCategoriesStatusMessageView = (TextView) findViewById(R.id.status_message);
		Typeface tf = Typeface.createFromAsset(getAssets(),
				"fonts/Gotham-Book.otf");
		mListCategoriesStatusMessageView.setTypeface(tf);
		mLstCategoriesView
				.setOnItemClickListener(new AdapterView.OnItemClickListener() {

					@Override
					public void onItemClick(AdapterView<?> a, View arg1,
							int position, long arg3) {

						Categoria obj = (Categoria) a
								.getItemAtPosition(position);
						Intent i = new Intent();
						i.putExtra("category_id", obj.getId().toString());

						setResult(RESULT_OK, i);
						finish();
					}
				});

		ArrayList<Categoria> listaCategorias = new ArrayList<Categoria>();
		listaCategorias.add(new Categoria(0, "Todas as Categorias"));

		for (ECategoria item : ECategoria.values()) {
			Categoria obj = new Categoria();
			obj.setId(item.getId());
			obj.setNome(item.getNome());
			listaCategorias.add(obj);
		}
		ArrayAdapter<Categoria> aa = new CustomAdapterCategory(
				ListCategoriesActivity.this, R.layout.category_list_item,
				listaCategorias);
		mLstCategoriesView.setAdapter(aa);

	}

	@TargetApi(Build.VERSION_CODES.HONEYCOMB_MR2)
	private void showProgress(final boolean show) {
		// On Honeycomb MR2 we have the ViewPropertyAnimator APIs, which allow
		// for very easy animations. If available, use these APIs to fade-in
		// the progress spinner.
		if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.HONEYCOMB_MR2) {
			int shortAnimTime = getResources().getInteger(
					android.R.integer.config_shortAnimTime);

			mListCategoriesStatusView.setVisibility(View.VISIBLE);
			mListCategoriesStatusView.animate().setDuration(shortAnimTime)
					.alpha(show ? 1 : 0)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mListCategoriesStatusView
									.setVisibility(show ? View.VISIBLE
											: View.GONE);
						}
					});

			mListCategoriesFormView.setVisibility(View.VISIBLE);
			mListCategoriesFormView.animate().setDuration(shortAnimTime)
					.alpha(show ? 0 : 1)
					.setListener(new AnimatorListenerAdapter() {
						@Override
						public void onAnimationEnd(Animator animation) {
							mListCategoriesFormView
									.setVisibility(show ? View.GONE
											: View.VISIBLE);
						}
					});
		} else {
			mListCategoriesStatusView.setVisibility(show ? View.VISIBLE
					: View.GONE);
			mListCategoriesFormView.setVisibility(show ? View.GONE
					: View.VISIBLE);
		}
	}
}
