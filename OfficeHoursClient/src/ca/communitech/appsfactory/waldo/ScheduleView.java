package ca.communitech.appsfactory.waldo;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.View;
import android.view.View.OnLongClickListener;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

public class ScheduleView extends Activity {
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_schedule_view);
        new ChangeHeaderColor().execute();
        new PopulateScheduleTask().execute();
    }
    public void addSchedule(View view) {
		Intent intent = new Intent(this, CreateScheduleView.class);
		startActivity(intent);
   	}
    /**refresh the schedule*/
    public void refreshSchedule(View view) {
    	
    	//Remove all views from the columns
    	RelativeLayout column = (RelativeLayout) findViewById(R.id.moncolumn);
    	column.removeAllViewsInLayout();
    	column = (RelativeLayout) findViewById(R.id.tuecolumn);
    	column.removeAllViewsInLayout();
    	column = (RelativeLayout) findViewById(R.id.wedcolumn);
    	column.removeAllViewsInLayout();
    	column = (RelativeLayout) findViewById(R.id.thucolumn);
    	column.removeAllViewsInLayout();
    	column = (RelativeLayout) findViewById(R.id.fricolumn);
    	column.removeAllViewsInLayout();
    	
    	
    	//Change header colors back
    	TextView header = (TextView) findViewById(R.id.mon_header);
		header.setTextColor(Color.GRAY);
		header.setBackgroundColor(Color.BLACK);
		header = (TextView) findViewById(R.id.tue_header);
		header.setTextColor(Color.GRAY);
		header.setBackgroundColor(Color.BLACK);
		header = (TextView) findViewById(R.id.wed_header);
		header.setTextColor(Color.GRAY);
		header.setBackgroundColor(Color.BLACK);
		header = (TextView) findViewById(R.id.thu_header);
		header.setTextColor(Color.GRAY);
		header.setBackgroundColor(Color.BLACK);
		header = (TextView) findViewById(R.id.fri_header);
		header.setTextColor(Color.GRAY);
		header.setBackgroundColor(Color.BLACK);
		
		
		//repopulate
		new ChangeHeaderColor().execute();
		new PopulateScheduleTask().execute();
		return;
    }

    /** get the current date from the server*/
	private HttpResponse getDate() {
		HttpClient client = new DefaultHttpClient();
        HttpPost post = new HttpPost(Constants.POST_URL);
    	JSONObject daterequest = new JSONObject();
    	
    	try {
        	daterequest.put("action", "currentDate");
        	StringEntity data_string = new StringEntity(daterequest.toString());
    		post.setEntity(data_string);
    		post.setHeader("dataType", "json");
    		
    		HttpResponse response = client.execute(post);
    		if (response.getStatusLine().getStatusCode() == 200) {
    			return response;
    		}
    		else {
    			databaseConnectionErrorMessage();
    			return null;
    		}
    	} catch (Exception e){
    		databaseConnectionErrorMessage();
    		return null;
    	}
	}
	
	/**Toasts a generic database error message */
	private void databaseConnectionErrorMessage() {
		Context context = getApplicationContext();
		CharSequence errormessage = "Error connecting to database. Please try again in a few moments.";
		int duration = Toast.LENGTH_SHORT;
		
		//return that user messed up
		Toast toastiness = Toast.makeText(context, errormessage, duration);
		toastiness.show();
	}

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.activity_schedule_view, menu);
        return true;
    }
    private class ChangeHeaderColor extends AsyncTask<Void, Integer, HttpResponse> {

		@Override
		protected HttpResponse doInBackground(Void... params) {
			return getDate();
		}
		
		@Override
		protected void onPostExecute(HttpResponse response) {
			try {
				BufferedReader reader = new BufferedReader(new InputStreamReader(response.getEntity().getContent(), "UTF-8"));
				String json = reader.readLine();
				JSONTokener tokener = new JSONTokener(json);
				JSONObject jsonobject = new JSONObject(tokener);
				TextView header;
				switch (Constants.DAYS.valueOf(jsonobject.getString("weekday"))) {
				case Monday:
					header = (TextView) findViewById(R.id.mon_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
					break;
				case Tuesday:
					header = (TextView) findViewById(R.id.tue_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
					break;
				case Wednesday:
					header = (TextView) findViewById(R.id.wed_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
					break;
				case Thursday:
					header = (TextView) findViewById(R.id.thu_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
					break;
				case Friday:
					header = (TextView) findViewById(R.id.fri_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
					break;

				default:
					//well shit
					break;
				}				
			} catch (IllegalStateException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (JSONException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
    
    }
    private class PopulateScheduleTask extends AsyncTask<Void, Integer, HttpResponse> {

		@Override
		protected HttpResponse doInBackground(Void... params) {
			HttpClient client = new DefaultHttpClient();
	        HttpPost post = new HttpPost(Constants.POST_URL);
	    	JSONObject request = new JSONObject();
	    	SharedPreferences auth_stuff = getSharedPreferences(Constants.SHARED_PREFS_FILE, MODE_PRIVATE);
	        String string = auth_stuff.getString("authstring", " " + Constants.AUTH_SPLITTER + " ");
	        string = string.split(Constants.AUTH_SPLITTER)[0];
	    	try {
	        	request.put("action", "showSchedule");
	        	request.put("branchId", Constants.BRANCH_ID);
	        	request.put("organizationId", Constants.ORGANIZATION_ID);
	        	request.put("locationCode", Constants.LOCATION);
	        	request.put("userName", string);
	        	StringEntity data_string = new StringEntity(request.toString());
	    		post.setEntity(data_string);
	    		post.setHeader("dataType", "json");
	    		
	    		HttpResponse response = client.execute(post);
	    		if (response.getStatusLine().getStatusCode() == 200) {
	    			return response;
	    		}
	    		else {
	    			databaseConnectionErrorMessage();
	    			return null;
	    		}
	    	} catch (Exception e){
	    		databaseConnectionErrorMessage();
	    		return null;
	    	}
		}
		
		@Override
		protected void onPostExecute(HttpResponse response) {
				try {
					BufferedReader reader = new BufferedReader(new InputStreamReader(response.getEntity().getContent(), "UTF-8"));
					String json = reader.readLine();
					JSONTokener tokener = new JSONTokener(json);
					JSONArray jsonarray = new JSONArray(tokener);
					if (jsonarray.length() == 0) {
						Context context = getApplicationContext();
						CharSequence errormessage = "There don't seem to be any scheduled events in our database.";
						int duration = Toast.LENGTH_SHORT;
						
						//return that user messed up
						Toast toastiness = Toast.makeText(context, errormessage, duration);
						toastiness.show();
					}
					else{
						//Iterate through the array of JSONObjects, populate for each
						for(int i=0; i < jsonarray.length(); i++){
							String startTime_s = jsonarray.getJSONObject(i).getString("startTime");
							String endTime_s = jsonarray.getJSONObject(i).getString("endTime");
							
							switch (Constants.DAYS.valueOf(jsonarray.getJSONObject(i).getString("weekday"))) {
							case Monday:
								addScheduleEvent(startTime_s, endTime_s,
										R.id.moncolumn);
								break;
							case Tuesday:
								addScheduleEvent(startTime_s, endTime_s,
										R.id.tuecolumn);
								break;
							case Wednesday:
								addScheduleEvent(startTime_s, endTime_s,
										R.id.wedcolumn);
								break;
							case Thursday:
								addScheduleEvent(startTime_s, endTime_s,
										R.id.thucolumn);
								break;
							case Friday:
								addScheduleEvent(startTime_s, endTime_s,
										R.id.fricolumn);
								break;
							default:
								//well shit
								databaseConnectionErrorMessage();
								break;
							}
						}							
					}					
				} catch (ParseException e) {
					// TODO Auto-generated catch block
					Log.i("CATCH HIT: ", e.getMessage());
				} catch (JSONException e) {
					// TODO Auto-generated catch block
					Log.i("CATCH HIT: ", e.getMessage());
				} catch (UnsupportedEncodingException e) {
					// TODO Auto-generated catch block
					Log.i("CATCH HIT: ", e.getMessage());
				} catch (IllegalStateException e) {
					// TODO Auto-generated catch block
					Log.i("CATCH HIT: ", e.getMessage());
				} catch (IOException e) {
					// TODO Auto-generated catch block
					Log.i("CATCH HIT: ", e.getMessage());
				}
		}

		private String cleanTimeString(String string) {
			if (string.charAt(0) == '0')
				string = string.substring(1, 5);
			else
				string = string.substring(0,5);
			return string;
		}

		private void addScheduleEvent(String startTime_s, String endTime_s,
				int columnId) throws ParseException {
			ViewGroup col = (ViewGroup) findViewById(columnId);        
			
			//Create a layout to hold the event
			LayoutInflater inflater = (LayoutInflater)getBaseContext().getSystemService (Context.LAYOUT_INFLATER_SERVICE);
			RelativeLayout layout = (RelativeLayout) inflater.inflate(R.layout.timeboxborder, null, false);
			//Add the TextView to the layout, but the function returns layout so...
			View toptime = getLayoutInflater().inflate(R.layout.timeboxtexttop, layout);
			//I have to use this ugly hack to get a reference to the actual TextView
			TextView toptimetextview = (TextView) layout.getChildAt(layout.getChildCount()-1);
			//Which lets me set the text
			toptimetextview.setText(cleanTimeString(startTime_s));
			
			//Now to do it for the bottom:
			View bottomtime = getLayoutInflater().inflate(R.layout.timeboxtextbottom, layout);
			TextView bottomtimetextview = (TextView) layout.getChildAt(layout.getChildCount()-1);
			bottomtimetextview.setText(cleanTimeString(endTime_s));
			
			
			//Set margins and height
				//First strings to actual time objects
			SimpleDateFormat df = new SimpleDateFormat("HH:mm:ss");
			Date startTime = df.parse(startTime_s);
			Date endTime = df.parse(endTime_s);
			
				//do math and set params!
			double topmargin = startTime.getTime() - 46800000.0;
			topmargin *= Constants.MS_TO_DP;
			topmargin += 3;
			topmargin *= (getResources().getDisplayMetrics().density);
			topmargin += 0.5f;
			
			double height = endTime.getTime() - startTime.getTime() + 0.0;
			height *= Constants.MS_TO_DP;
			height -= 5.0;
			height *= (getResources().getDisplayMetrics().density);
			height += 0.5f;
			layout.setOnLongClickListener(new OnLongClickListener() {
				
				@Override
				public boolean onLongClick(final View v) {
					new AlertDialog.Builder(v.getContext())
					.setTitle("Delete Event")
					.setMessage("Do you really want to delete this event?")
					.setPositiveButton("You Bet!", new DialogInterface.OnClickListener() {
						
						@Override
						public void onClick(DialogInterface dialog, int which) {
	    	    			RelativeLayout relv = (RelativeLayout) v;
	    	    			final TextView starttime = (TextView) relv.getChildAt(1);
	    	    			final TextView endtime = (TextView)relv.getChildAt(2);
	    	    			View parent = (View) relv.getParent();
	    	    			final String date;
	    	    			int id = parent.getId();
	    	    			switch (id) {
							case R.id.moncolumn:
								date = "Monday";
								break;
							case R.id.tuecolumn:
								date = "Tuesday";
								break;
							case R.id.wedcolumn:
								date = "Wednesday";
								break;
							case R.id.thucolumn:
								date = "Thursday";
								break;
							case R.id.fricolumn:
								date = "Friday";
								break;

							default:
								date = null;
								break;
							}
				    	    new Thread(new Runnable() {
								
								@Override
								public void run() {
									HttpClient client = new DefaultHttpClient();
									HttpPost post = new HttpPost (Constants.POST_URL);        	
									try {
										JSONObject data_json = new JSONObject();
										data_json.put("userName", getIntent().getExtras().getString(Log_In.USERNAME));
										data_json.put("organizationId", Constants.ORGANIZATION_ID);
										data_json.put("locationCode", Constants.LOCATION);
										data_json.put("branchId", Constants.BRANCH_ID);
										data_json.put("action", "deleteSchedule");
										data_json.put("startingTime", starttime.getText().toString());
										data_json.put("finishingTime", endtime.getText().toString());
										data_json.put("selectedDate", date);
										StringEntity data_string = new StringEntity(data_json.toString());
										post.setEntity(data_string);
										post.setHeader("dataType", "json");
										HttpResponse response = client.execute(post);
									} catch(Exception e) {
										//TODO: handle error
									}
								}
							}).start();
				    	    refreshSchedule(v);
							return;
						}
					})
					.setNegativeButton("Nope!", null)
					.show();
					return true;
				}
			});
			LinearLayout.LayoutParams lparams = new LinearLayout.LayoutParams(LinearLayout.LayoutParams.FILL_PARENT, (int)height);
			col.addView(layout, lparams);
			RelativeLayout.LayoutParams legitparams = (android.widget.RelativeLayout.LayoutParams) layout.getLayoutParams();
			legitparams.topMargin = (int)topmargin;
			legitparams.leftMargin = (int)(1*getResources().getDisplayMetrics().density + 0.5f);
			legitparams.rightMargin = (int)(1*getResources().getDisplayMetrics().density + 0.5f);
			return;
		}
    }
   
}
