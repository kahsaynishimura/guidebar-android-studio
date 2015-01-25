package br.com.guidebar;

import android.app.Activity;
import android.os.Bundle;
import android.os.Message;
import android.util.Log;
import android.view.View;
import android.view.ViewGroup.LayoutParams;
import android.view.Window;
import android.webkit.WebChromeClient;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.LinearLayout;
import br.com.guidebar.classes.Guidebar;

public class CommentsActivity extends Activity {
	private String requestUrl = "";
	private WebView webView, childView = null;
	private LinearLayout parentLayout;
	private Activity MyActivity;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		this.getWindow().requestFeature(Window.FEATURE_PROGRESS);

		setContentView(R.layout.activity_comments);

		getWindow().setFeatureInt(Window.FEATURE_PROGRESS,
				Window.PROGRESS_VISIBILITY_ON);
		parentLayout = (LinearLayout) findViewById(R.id.parentLayout);
		Integer id = getIntent().getExtras().getInt("id_evento");

		requestUrl = Guidebar.serverUrl + "events/facebookComments/" + id;

		MyActivity = this;

		webView = new WebView(this);
		webView.setLayoutParams(getLayoutParams());

		webView.setWebViewClient(new FaceBookClient());
		webView.setWebChromeClient(new MyChromeClient());
		webView.getSettings().setJavaScriptEnabled(true);
		webView.getSettings().setAppCacheEnabled(true);
		webView.getSettings().setJavaScriptCanOpenWindowsAutomatically(true);
		webView.getSettings().setSupportMultipleWindows(true);
		webView.getSettings().setSupportZoom(true);
		webView.getSettings().setBuiltInZoomControls(true);

		parentLayout.addView(webView);
		webView.loadUrl(requestUrl);

	}

	private LinearLayout.LayoutParams getLayoutParams() {
		return new LinearLayout.LayoutParams(LayoutParams.MATCH_PARENT,
				LayoutParams.MATCH_PARENT);
	}

	final class MyChromeClient extends WebChromeClient {
		@Override
		public boolean onCreateWindow(WebView view, boolean dialog,
				boolean userGesture, Message resultMsg) {
			childView = new WebView(CommentsActivity.this);
			childView.getSettings().setJavaScriptEnabled(true);
			childView.getSettings().setSupportZoom(true);
			childView.getSettings().setBuiltInZoomControls(true);
			childView.setWebViewClient(new FaceBookClient());
			childView.setWebChromeClient(this);
			childView.setLayoutParams(new LinearLayout.LayoutParams(
					LayoutParams.MATCH_PARENT, LayoutParams.MATCH_PARENT));

			parentLayout.addView(childView);

			childView.requestFocus();
			webView.setVisibility(View.GONE);

			/*
			 * I think this is the main part which handles all the log in
			 * session
			 */
			WebView.WebViewTransport transport = (WebView.WebViewTransport) resultMsg.obj;
			transport.setWebView(childView);
			resultMsg.sendToTarget();
			return true;
		}

		@Override
		public void onProgressChanged(WebView view, int newProgress) {
			MyActivity.setProgress(newProgress * 100);
		}

		@Override
		public void onCloseWindow(WebView window) {
			parentLayout.removeViewAt(parentLayout.getChildCount() - 1);
			childView = null;
			webView.setVisibility(View.VISIBLE);
			webView.requestFocus();
		}
	}

	private class FaceBookClient extends WebViewClient {
		@Override
		public boolean shouldOverrideUrlLoading(WebView view, String url) {
			Log.i("REQUEST URL", url);
			return false;
		}
	}

	@Override
	public void onBackPressed() {
		if (childView != null && parentLayout.getChildCount() == 2) {
			childView.stopLoading();
			parentLayout.removeViewAt(parentLayout.getChildCount() - 1);
			if (webView.getVisibility() == View.GONE)
				webView.setVisibility(View.VISIBLE);
		} else {
			super.onBackPressed();
		}
	}
}
