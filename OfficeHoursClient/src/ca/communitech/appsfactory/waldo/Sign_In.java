package ca.communitech.appsfactory.waldo;

import java.io.IOException;
import java.io.UnsupportedEncodingException;

import org.apache.http.HttpResponse;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.Menu;
import android.view.View;
import android.widget.ToggleButton;

public class Sign_In extends Activity {

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sign__in);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.activity_sign__in, menu);
        return true;
    }
    
    /** Called when user presses the sign in/out button
     * 
     * @param view
     */
    public void toggleSignIn (View view) {
    	ToggleButton togglebutton = (ToggleButton) findViewById(R.id.toggleButton1);
    	if (togglebutton.isChecked()){
    		new Thread(new Runnable() {
    	        public void run() {
    	            String action = "signIn";
    	    		postSignInorOut(action);
    	    		return;
    	        }
    	    }).start(); 

    	}
    	else {
    		new Thread(new Runnable() {
    	        public void run() {
    	            String action = "signOut";
    	    		postSignInorOut(action);
    	    		return;
    	        }
    	    }).start(); 
    	}
    }
    
    /** Communicates to the server, setting the user's signed in/out status
     * 
     * @param action either 'signIn' or 'signOut'
     */
	private void postSignInorOut(String action) {
		//Create Http client and header
		HttpClient client = new DefaultHttpClient();
		HttpPost post = new HttpPost (Constants.POST_URL);        	
		try {
			JSONObject data_json = new JSONObject();
			data_json.put("userName", getIntent().getExtras().getString(Log_In.USERNAME));
			data_json.put("organizationId", Constants.ORGANIZATION_ID);
			data_json.put("locationCode", Constants.LOCATION);
			data_json.put("branchId", Constants.BRANCH_ID);
			data_json.put("action", action);
			StringEntity data_string = new StringEntity(data_json.toString());
			post.setEntity(data_string);
			post.setHeader("dataType", "json");
			
			HttpResponse response = client.execute(post);

		} catch(JSONException e) {
			//TODO: handle error
		} catch(UnsupportedEncodingException e) {
			//TODO: handle this errorage
		} catch (ClientProtocolException e) {
			// TODO Auto-generated catch block
		} catch (IOException e) {
			// TODO Auto-generated catch block
		}
	}
    
    /** Called when user presses the My Schedule button */
    public void viewSchedule(View view){
    	Intent my_schedule_intent = new Intent(this, ScheduleView.class);
    	my_schedule_intent.putExtra(Log_In.USERNAME, getIntent().getExtras().getString(Log_In.USERNAME));
		startActivity(my_schedule_intent);
    }
    
    /** signs the user out, resets the saved username/password, and pops to the log in screen
     * 
     * @param view
     */
    public void logOut(View view) {
    	new Thread(new Runnable() {
	        public void run() {
	        	String action="signOut";
	    		postSignInorOut(action);
	    		return;
	        }
	    }).start(); 

		SharedPreferences auth_stuff = getSharedPreferences(Constants.SHARED_PREFS_FILE, MODE_PRIVATE);
		SharedPreferences.Editor editor = auth_stuff.edit();
		editor.putString("authstring", (" " + Constants.AUTH_SPLITTER + " "));
		editor.commit();
		Intent logged_in_intent = new Intent(this, Log_In.class);
		logged_in_intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
		startActivity(logged_in_intent);
    }
}
