package ca.communitech.appsfactory.waldo;

import android.os.Bundle;
import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.view.Menu;

public class SignInChecker extends Activity {

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sign_in_checker);
        
        SharedPreferences auth_stuff = getSharedPreferences(Constants.SHARED_PREFS_FILE, MODE_PRIVATE);
        String unsplit_auth_string = auth_stuff.getString("authstring", " " + Constants.AUTH_SPLITTER + " ");
        String[] auth_data = unsplit_auth_string.split(Constants.AUTH_SPLITTER);
        if (Log_In.logInPost(auth_data[0], auth_data[1])){
        	 Intent logged_in_intent = new Intent(this, Sign_In.class);
        	 logged_in_intent.putExtra(Log_In.USERNAME, auth_data[0]);
        	 startActivity(logged_in_intent);
        	 finish();
        }
        else {
        	Intent not_logged_in = new Intent(this, Log_In.class);
	       	 startActivity(not_logged_in);
	       	 finish();
        }
        
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.activity_sign_in_checker, menu);
        return true;
    }
}
