package ca.communitech.appsfactory.waldo;

import java.io.ByteArrayOutputStream;
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

import android.os.Bundle;
import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.view.Menu;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;
import ca.communitech.appsfactory.waldo.R;

public class Log_In extends Activity {
	public static final String USERNAME = "ca.communitech.appsfactory.waldo.USERNAME";
	
	
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_log__in);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.activity_log__in, menu);
        return true;
    }
    
    /** Called when user clicks the "Log In" Button */
    public void logIn(View view){
    	EditText username_field = (EditText) findViewById(R.id.login_username);
    	String username = username_field.getText().toString();
    	EditText pw_field = (EditText) findViewById(R.id.login_pw);
    	String password = pw_field.getText().toString();
    	
    	if (logInPost(username, password)){
			SharedPreferences auth_stuff = getSharedPreferences(Constants.SHARED_PREFS_FILE, MODE_PRIVATE);
			SharedPreferences.Editor editor = auth_stuff.edit();
			editor.putString("authstring", (username + Constants.AUTH_SPLITTER + password));
			editor.commit();
    		Intent logged_in_intent = new Intent(this, Sign_In.class);
    		logged_in_intent.putExtra(USERNAME, username);
    		startActivity(logged_in_intent);
    	}
    	else {
    		Context context = getApplicationContext();
    		CharSequence errormessage = "Error Signing In. Please Try Again.";
    		int duration = Toast.LENGTH_SHORT;
    		
    		Toast toastiness = Toast.makeText(context, errormessage, duration);
    		toastiness.show();
    	}
	
    }
    
    /** Handles the actual logging in of a user */
    public static boolean logInPost(String un, String pw) {
    	//Create Http client and header
    	HttpClient client = new DefaultHttpClient();
    	HttpPost post = new HttpPost (Constants.POST_URL);
    	
    	try {
    		JSONObject data_json = new JSONObject();
    		data_json.put("userName", un);
    		data_json.put("password", pw);
    		data_json.put("accessCode", Constants.ACCESS_CODE);
    		data_json.put("organizationName", Constants.ORGANIZATION_NAME);
    		data_json.put("action", "logIn");
    		StringEntity data_string = new StringEntity(data_json.toString());
    		post.setEntity(data_string);
    		post.setHeader("dataType", "json");
    		
    		HttpResponse response = client.execute(post);
    		
    		ByteArrayOutputStream outstream = new ByteArrayOutputStream();
			response.getEntity().writeTo(outstream);
			
    		if (response.getStatusLine().getStatusCode() == 200) {
    			return true;
    		}
    		else {
    			return false;
    		}
    	} catch(JSONException e) {
    		//TODO: handle error
    		e.printStackTrace();
    		return false;
    	} catch(UnsupportedEncodingException e) {
    		//TODO: handle this errorage
    		e.printStackTrace();
    		return false;
    	} catch (ClientProtocolException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return false;
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return false;
		}
    }
}
