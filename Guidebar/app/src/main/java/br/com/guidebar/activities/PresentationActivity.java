 package br.com.guidebar.activities;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import br.com.guidebar.R;
import br.com.guidebar.classes.SessionManager;

public class PresentationActivity extends Activity {

	SessionManager session;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_presentation);
		
		startActivity(new Intent(PresentationActivity.this, LoginActivity.class));
		finish();
	}

	@Override
	protected void onResume() {
		super.onResume();
		startActivity(new Intent(PresentationActivity.this, LoginActivity.class));
		finish();
	}

}
