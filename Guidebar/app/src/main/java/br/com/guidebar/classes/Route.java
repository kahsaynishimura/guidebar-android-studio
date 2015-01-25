package br.com.guidebar.classes;

import java.util.ArrayList;
import java.util.List;

import com.google.android.gms.maps.model.LatLng;

public class Route {
	private final List<LatLng> points;
	private String polyline;

	public Route() {
		points = new ArrayList<LatLng>();
	}

	public void addPoints(final List<LatLng> points) {
		this.points.addAll(points);
	}

	public List<LatLng> getPoints() {
		return points;
	}

	public void setPolyline(String polyline) {
		this.polyline = polyline;
	}

	public String getPolyline() {
		return polyline;
	}

}
