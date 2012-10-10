package ca.communitech.appsfactory.waldo;

import android.app.Activity;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Typeface;
import android.os.Bundle;
import android.view.Menu;

public class SignInChecker extends Activity {
	public static Typeface metro;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sign_in_checker);
        metro = Typeface.createFromAsset(getAssets(), "HelveticaCY.dfont");
        //grab usernamesaltpassword from local storage and disseminate
        SharedPreferences auth_stuff = getSharedPreferences(Constants.SHARED_PREFS_FILE, MODE_PRIVATE);
        String unsplit_auth_string = auth_stuff.getString("authstring", " " + Constants.AUTH_SPLITTER + " ");
        final String[] auth_data = unsplit_auth_string.split(Constants.AUTH_SPLITTER);
        
        new Thread(new Runnable() {
            public void run() {
            	//check login data, if correct go to the sign_in screen
            	if (Log_In.logInPost(auth_data[0], auth_data[1])){
               	 Intent logged_in_intent = new Intent(getBaseContext(), Sign_In.class);
               	 logged_in_intent.putExtra(Log_In.USERNAME, auth_data[0]);
               	 startActivity(logged_in_intent);
               	 finish();
               }
               //if not go to login
               else {
               	Intent not_logged_in = new Intent(getBaseContext(), Log_In.class);
       	       	 startActivity(not_logged_in);
       	       	 finish();
               }
            return;
            }
        }).start();

        
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.activity_sign_in_checker, menu);
        return true;
    }
}
