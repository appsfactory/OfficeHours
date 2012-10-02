package ca.communitech.appsfactory.waldo;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONException;
import org.json.JSONObject;
import org.json.JSONTokener;

import android.app.Activity;
import android.content.Context;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.view.ViewGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

public class ScheduleView extends Activity {
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_schedule_view);
        new ChangeHeaderColor().execute();
        
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
    			return null;
    		}
    	} catch (Exception e){
    		databaseConnectionErrorMessage();
    		return null;
    	}
	}

	private void databaseConnectionErrorMessage() {
		Context context = getApplicationContext();
		CharSequence errormessage = "Error connecting to database. Please try again in a few moments.";
		int duration = Toast.LENGTH_SHORT;
		
		//return that user messed up
		Toast toastiness = Toast.makeText(context, errormessage, duration);
		toastiness.show();
	}

	private void createEventBox() {
		ViewGroup moncol = (ViewGroup) findViewById(R.id.moncolumn);        
        
         //Create a layout to hold the event
         RelativeLayout layout = (RelativeLayout) RelativeLayout.inflate(getBaseContext(), R.layout.timeboxborder, moncol);
         //Add the TextView to the layout, but the function returns layout so...
         View toptime = getLayoutInflater().inflate(R.layout.timeboxtexttop, layout);
         //I have to use this ugly hack to get a reference to the actual TextView
         TextView toptimetextview = (TextView) layout.getChildAt(layout.getChildCount()-1);
         //Which lets me set the text
         toptimetextview.setText("bacon");
         
         //Now to do it for the bottom:
         View bottomtime = getLayoutInflater().inflate(R.layout.timeboxtextbottom, layout);
         TextView bottomtimetextview = (TextView) layout.getChildAt(layout.getChildCount()-1);
         bottomtimetextview.setText("isafruit");
         
         ViewGroup.MarginLayoutParams lparams = (ViewGroup.MarginLayoutParams)layout.getLayoutParams();
         lparams.topMargin = 500;
         layout.setLayoutParams(lparams);
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
				if(jsonobject.get("weekday").toString() == "Monday") {
					TextView header = (TextView) findViewById(R.id.mon_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
				} else if(jsonobject.get("weekday").toString().equals("Tuesday")) {
					TextView header = (TextView) findViewById(R.id.tue_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
				} else if(jsonobject.get("weekday").toString() == "Wednesday") {
					TextView header = (TextView) findViewById(R.id.wed_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
				} else if(jsonobject.get("weekday").toString() == "Thursday") {
					TextView header = (TextView) findViewById(R.id.thu_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
				} else if(jsonobject.get("weekday").toString() == "Friday") {
					TextView header = (TextView) findViewById(R.id.fri_header);
					header.setTextColor(Color.WHITE);
					header.setBackgroundColor(Color.GRAY);
				} else {
					
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
			
			return null;
		}
		
		@Override
		protected void onPostExecute(HttpResponse response) {
			
		}
    
    }
}
