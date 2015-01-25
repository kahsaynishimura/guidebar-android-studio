package br.com.guidebar.classes;

import java.util.HashMap;

import br.com.guidebar.activities.LoginActivity;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;

public class SessionManager {
	// Shared Preferences
	SharedPreferences pref;

	// Editor for Shared preferences
	Editor editor;

	// Context
	Context _context;

	// Shared pref mode
	int PRIVATE_MODE = 0;

	// Sharedpref file name
	private static final String PREF_NAME = "SharedPreferences";

	// All Shared Preferences Keys
	private static final String IS_LOGIN = "IsLoggedIn";

	// User id (make variable public to access from outside)
	public static final String KEY_ID = "id";

	// Email address (make variable public to access from outside)
	public static final String KEY_EMAIL = "email";

	// Email address pagseguro (make variable public to access from outside)
	public static final String KEY_EMAIL_PAGSEGURO = "email_pagseguro";

	// Token pagseguro (make variable public to access from outside)
	public static final String KEY_TOKEN_PAGSEGURO = "token_pagseguro";

	// Token pagseguro (make variable public to access from outside)
	public static final String KEY_ID_FACEBOOK = "id_facebook";

	// Token pagseguro (make variable public to access from outside)
	public static final String KEY_ACCESS_TOKEN = "access_token";
	// Constructor
	@SuppressLint("CommitPrefEdits")
	public SessionManager(Context context) {
		this._context = context;
		pref = _context.getSharedPreferences(PREF_NAME, PRIVATE_MODE);
		editor = pref.edit();
	}

	/**
	 * Create login session
	 * */
	public void createLoginSession(String email, String id,
			String emailPagSeguro, String tokenPagSeguro, String idFacebook, String accessToken) {
		// Storing login value as TRUE
		editor.putBoolean(IS_LOGIN, true);

		// Storing email in pref
		editor.putString(KEY_EMAIL, email);

		// Storing user id in pref
		editor.putString(KEY_ID, id);

		// Storing email pagseguro in pref
		editor.putString(KEY_EMAIL_PAGSEGURO, emailPagSeguro);

		// Storing token pagseguro in pref
		editor.putString(KEY_TOKEN_PAGSEGURO, tokenPagSeguro);

		// Storing facebook id in pref
		editor.putString(KEY_ID_FACEBOOK, idFacebook);
		
		// Storing facebook id in pref
		editor.putString(KEY_ACCESS_TOKEN, accessToken);

		// commit changes
		editor.commit();
	}

	/**
	 * Check login method wil check user login status If false it will redirect
	 * user to login page Else won't do anything
	 * */
	public void checkLogin() {
		// Check login status
		if (!this.isLoggedIn()) {
			// user is not logged in redirect him to Login Activity
			Intent i = new Intent(_context, LoginActivity.class);
			// Closing all the Activities
			i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);

			// Add new Flag to start new Activity
			i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);

			// Staring Login Activity
			_context.startActivity(i);
		}

	}

	/**
	 * Get stored session data
	 * */
	public HashMap<String, String> getUserDetails() {
		HashMap<String, String> user = new HashMap<String, String>();
		// user id
		user.put(KEY_ID, pref.getString(KEY_ID, null));

		// user email
		user.put(KEY_EMAIL, pref.getString(KEY_EMAIL, null));

		// user email pagseguro
		user.put(KEY_EMAIL_PAGSEGURO, pref.getString(KEY_EMAIL_PAGSEGURO, null));

		// user token pagseguro
		user.put(KEY_TOKEN_PAGSEGURO, pref.getString(KEY_TOKEN_PAGSEGURO, null));

		// user token pagseguro
		user.put(KEY_ID_FACEBOOK, pref.getString(KEY_ID_FACEBOOK, null));
		
		// user token pagseguro
		user.put(KEY_ACCESS_TOKEN, pref.getString(KEY_ACCESS_TOKEN, null));

		// return user
		return user;
	}

	/**
	 * Clear session details
	 * */
	public void logoutUser() {
		// Clearing all data from Shared Preferences
		editor.clear();
		editor.commit();

		// After logout redirect user to Login Activity
		Intent i = new Intent(_context, LoginActivity.class);
		// Closing all the Activities
		i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);

		// Add new Flag to start new Activity
		i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);

		// Staring Login Activity
		_context.startActivity(i);
	}

	/**
	 * Quick check for login
	 * **/
	// Get Login State
	public boolean isLoggedIn() {
		return pref.getBoolean(IS_LOGIN, false);
	}
}
