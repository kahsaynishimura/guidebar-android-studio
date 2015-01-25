package br.com.guidebar.classes;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URI;
import java.util.ArrayList;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.conn.params.ConnManagerParams;
import org.apache.http.entity.mime.MultipartEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.params.HttpConnectionParams;
import org.apache.http.params.HttpParams;
import org.apache.http.protocol.HTTP;

import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;

public class ConexaoHttpClient {
	public static final int HTTP_TIMEOUT = 30 * 1000;
	private static HttpClient httpClient;

	private static HttpClient getHttpClient() {
		if (httpClient == null) {
			httpClient = new DefaultHttpClient();
			final HttpParams httpParamns = httpClient.getParams();
			HttpConnectionParams
					.setConnectionTimeout(httpParamns, HTTP_TIMEOUT);
			HttpConnectionParams.setSoTimeout(httpParamns, HTTP_TIMEOUT);
			ConnManagerParams.setTimeout(httpParamns, HTTP_TIMEOUT);

		}
		return httpClient;
	}

	public static String executaHttpPost(String url,
			ArrayList<NameValuePair> parametrosPost) throws Exception {
		BufferedReader bufferedReader = null;
		try {
			HttpClient client = getHttpClient();
			HttpPost httpPost = new HttpPost(url);

			UrlEncodedFormEntity formEntity = new UrlEncodedFormEntity(
					parametrosPost,HTTP.UTF_8);

			httpPost.setEntity(formEntity);
			HttpResponse httpResponse = client.execute(httpPost);
			bufferedReader = new BufferedReader(new InputStreamReader(
					httpResponse.getEntity().getContent(), HTTP.UTF_8), 8);

			StringBuffer stringBuffer = new StringBuffer("");
			String line = "";
			String LS = System.getProperty("line.separator");

			while ((line = bufferedReader.readLine()) != null) {
				stringBuffer.append(line + LS);
			}
			bufferedReader.close();
			
			String resultado = stringBuffer.toString();
			byte[] bytes = resultado.getBytes("UTF-8"); 
			String texto = new String(bytes);
			return texto;
		} finally {
			httpClient.getConnectionManager().closeExpiredConnections();
			if (bufferedReader != null) {
				try {
					bufferedReader.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
		}
	}

	public static String executaHttpGet(String url) throws Exception {
		BufferedReader bufferedReader = null;
		try {
			HttpClient client = getHttpClient();
			HttpGet httpGet = new HttpGet(url);
			httpGet.setURI(new URI(url));
			HttpResponse httpResponse = client.execute(httpGet);
			bufferedReader = new BufferedReader(new InputStreamReader(
					httpResponse.getEntity().getContent()));

			StringBuffer stringBuffer = new StringBuffer("");
			String line = "";
			String LS = System.getProperty("line.separator");

			while ((line = bufferedReader.readLine()) != null) {
				stringBuffer.append(line + LS);
			}
			bufferedReader.close();
			String resultado = stringBuffer.toString();
			return resultado;
		} finally {
			httpClient.getConnectionManager().closeExpiredConnections();
			if (bufferedReader != null) {
				try {
					bufferedReader.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
		}

	}

	public static String executaHttpPostMultipart(String url,
			MultipartEntity entity) throws Exception {
		BufferedReader bufferedReader = null;
		try {
			HttpClient client = getHttpClient();
			HttpPost httpPost = new HttpPost(url);
			httpPost.setEntity(entity);
			HttpResponse httpResponse = client.execute(httpPost);
			bufferedReader = new BufferedReader(new InputStreamReader(
					httpResponse.getEntity().getContent()));

			StringBuffer stringBuffer = new StringBuffer("");
			String line = "";
			String LS = System.getProperty("line.separator");

			while ((line = bufferedReader.readLine()) != null) {
				stringBuffer.append(line + LS);
			}
			bufferedReader.close();
			String resultado = stringBuffer.toString();
			return resultado;
		} finally {
			if (bufferedReader != null) {
				try {
					bufferedReader.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
		}

	}

	public static String executaHttpPostPagSeguro(String url,
			ArrayList<NameValuePair> parametrosPost) throws Exception {
		BufferedReader bufferedReader = null;
		try {
			HttpClient client = getHttpClient();
			HttpPost httpPost = new HttpPost(url);

			UrlEncodedFormEntity formEntity = new UrlEncodedFormEntity(
					parametrosPost);
			formEntity.setContentEncoding(HTTP.ISO_8859_1);
			formEntity.setContentType("application/x-www-form-urlencoded");

			httpPost.setEntity(formEntity);
			HttpResponse httpResponse = client.execute(httpPost);
			bufferedReader = new BufferedReader(new InputStreamReader(
					httpResponse.getEntity().getContent(), "UTF-8"), 8);

			StringBuffer stringBuffer = new StringBuffer("");
			String line = "";
			String LS = System.getProperty("line.separator");

			while ((line = bufferedReader.readLine()) != null) {
				stringBuffer.append(line + LS);
			}
			bufferedReader.close();
			String resultado = stringBuffer.toString();
			return resultado;
		} finally {
			if (bufferedReader != null) {
				try {
					bufferedReader.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
		}
	}

	public static boolean VerificaConexao(Context contexto) {

		ConnectivityManager cm = (ConnectivityManager) contexto
				.getSystemService(Context.CONNECTIVITY_SERVICE);
		// Pego a
		// conectividade
		// do contexto o
		// qual o metodo
		// foi chamado

		NetworkInfo netInfo = cm.getActiveNetworkInfo();
		// Crio o objeto netInfo
		// que recebe as
		// informacoes da
		// NEtwork

		System.out.println("NETWORK INFO: " + netInfo.getSubtypeName());

		if ((netInfo != null) && (netInfo.isConnectedOrConnecting())
				&& (netInfo.isAvailable()))
			// Se o objeto for nulo ou nao tem
			// conectividade retorna false
			return true;
		else
			return false;

	}
}
