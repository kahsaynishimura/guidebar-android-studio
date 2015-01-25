package br.com.guidebar.customviews;

import java.util.Calendar;

import android.app.DatePickerDialog;
import android.content.Context;
import android.util.AttributeSet;
import android.view.View;
import android.widget.DatePicker;
import android.widget.TextView;

public class DateDisplayPicker extends TextView implements
		DatePickerDialog.OnDateSetListener {

	private Context _context;

	public DateDisplayPicker(Context context, AttributeSet attrs, int defStyle) {
		super(context, attrs, defStyle);
		_context = context;
	}

	public DateDisplayPicker(Context context, AttributeSet attrs) {
		super(context, attrs);
		_context = context;
		setAttributes();
	}

	public DateDisplayPicker(Context context) {
		super(context);
		_context = context;
		setAttributes();
	}

	private void setAttributes() {
		setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				showDateDialog();
			}
		});
	}

	private void showDateDialog() {
		final Calendar c = Calendar.getInstance();
		DatePickerDialog dp = new DatePickerDialog(_context, this,
				c.get(Calendar.YEAR), c.get(Calendar.MONTH),
				c.get(Calendar.DAY_OF_MONTH));
		dp.show();
	}

	@Override
	public void onDateSet(DatePicker view, int year, int monthOfYear,
			int dayOfMonth) {
		setText(String.format("%02d/%02d/%02d", dayOfMonth, monthOfYear + 1,
				year));
	}

	public String getDateForPersistence(String str) {
		if (str != null) {
			String[] date = str.split("/");
			String date2 = date[2] + "/" + date[1] + "/" + date[0];
			return date2;
		} else {
			return "";
		}
	}

	public String getDateForPresenting(String str) {
		if (str != null) {
			String[] date = str.split("-");
			String date2 = date[2] + "/" + date[1] + "/" + date[0];
			return date2;
		} else {
			return "";
		}
	}

	public static int getDay(String str, String separator) {
		if (str != null) {
			String[] date = str.split(separator);
			Integer data = Integer.parseInt(date[0]);
			return data;
		} else {
			return 0;
		}
	}

	public static int getMonth(String str, String separator) {
		if (str != null) {
			String[] date = str.split(separator);
			Integer data = Integer.parseInt(date[1]);
			return data;
		} else {
			return 0;
		}
	}

	public static int getYear(String str, String separator) {
		if (str != null) {
			String[] date = str.split(separator);
			Integer data = Integer.parseInt(date[2]);
			return data;
		} else {
			return 0;
		}
	}
}