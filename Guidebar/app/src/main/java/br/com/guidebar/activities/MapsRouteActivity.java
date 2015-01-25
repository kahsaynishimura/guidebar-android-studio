package br.com.guidebar.activities;

import android.os.Bundle;
import android.support.v4.app.FragmentActivity;
import android.widget.Toast;
import br.com.guidebar.R;
import br.com.guidebar.classes.GPSTracker;
import br.com.guidebar.classes.RotaAsyncTask;

import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.LatLng;

public class MapsRouteActivity extends FragmentActivity {

	// GPSTracker class
	GPSTracker gps;
	SupportMapFragment fragment;
	GoogleMap map;
	Double latitudeEvento;
	Double longitudeEvento;

	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_maps_route);

		Bundle bundle = getIntent().getExtras();
		this.latitudeEvento = Double.parseDouble(bundle.getString("latitude"));
		this.longitudeEvento = Double
				.parseDouble(bundle.getString("longitude"));
		fragment = (SupportMapFragment) getSupportFragmentManager()
				.findFragmentById(R.id.map);
		map = fragment.getMap();

		// create class object
		gps = new GPSTracker(MapsRouteActivity.this);
		LatLng latLng = getUserLocation();
		if (latLng != null) {
			map.animateCamera(CameraUpdateFactory.newLatLngZoom(latLng, 17.0f));

			new RotaAsyncTask(MapsRouteActivity.this, map).execute(
			// Latitude, Logintude de Origem
					latLng.latitude, latLng.longitude,
					// Latitude, Longitude de Destino
					latitudeEvento, longitudeEvento);
		}

		/*
		 * SupportMapFragment fragment = (SupportMapFragment)
		 * getSupportFragmentManager() .findFragmentById(R.id.map); GoogleMap
		 * map = fragment.getMap();
		 * 
		 * LatLng latLng = new LatLng(-23.561706, -46.655981); map.addMarker(new
		 * MarkerOptions() .position(latLng) .icon(BitmapDescriptorFactory
		 * .fromResource(R.drawable.ic_launcher))
		 * .title("Av. Paulista").snippet("São Paulo"));
		 * 
		 * configuraPosicao(map, latLng);
		 */
	}

	@Override
	protected void onDestroy() {
		super.onDestroy();
		gps.stopUsingGPS();
	}

	@Override
	protected void onResume() {
		super.onResume();
		gps = new GPSTracker(MapsRouteActivity.this);
		// check if GPS enabled
		if (gps.canGetLocation()) {
			double latitude = gps.getLatitude();
			double longitude = gps.getLongitude();

			LatLng ll = new LatLng(latitude, longitude);

			new RotaAsyncTask(MapsRouteActivity.this, map).execute(
			// Latitude, Logintude de Origem
					ll.latitude, ll.longitude,
					// Latitude, Longitude de Destino
					latitudeEvento, longitudeEvento);
		}
	}

	public LatLng getUserLocation() {
		LatLng ll = null;
		// check if GPS enabled
		if (gps.canGetLocation()) {
			double latitude = gps.getLatitude();
			double longitude = gps.getLongitude();

			ll = new LatLng(latitude, longitude);
			// \n is for new line
			Toast.makeText(
					getApplicationContext(),
					"Sua posição é - \nLat: " + latitude + "\nLong: "
							+ longitude, Toast.LENGTH_LONG).show();
		} else {
			// can't get location
			// GPS or Network is not enabled
			// Ask user to enable GPS/network in settings
			gps.showSettingsAlert();
		}
		return ll;
	}


}
