package br.com.guidebar.activities;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import br.com.guidebar.bean.Evento;
import br.com.guidebar.classes.SessionManager;

public class DetailActivity extends Activity {
	private String urlFB = "";
	public Evento evento = new Evento();
	private SessionManager sessionGuidebar;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		Uri targetUri = getIntent().getData();
		sessionGuidebar = new SessionManager(DetailActivity.this);
		if (sessionGuidebar.isLoggedIn() && (targetUri != null)) {
			urlFB = targetUri.toString();
			evento.setId(Integer.parseInt((urlFB.substring((urlFB.lastIndexOf('/')+1),urlFB.length()))));
			Intent i = new Intent(DetailActivity.this, ViewEventActivity.class);
			i.putExtra("id", evento.getId());
			startActivity(i);
			finish();
		} else {
			Intent i = new Intent(DetailActivity.this, LoginActivity.class);
			startActivity(i);
			finish();
		}
	}

}
