package ca.communitech.appsfactory.waldo;

import java.util.HashMap;
import java.util.Map;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.DisplayMetrics;
import android.view.Menu;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnTouchListener;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

public class CreateScheduleView extends Activity {

	private int height;
	private int width ;
	private int screenDensity;
	private int DptoPixel;
	private String startingTime;
	private String finishingTime;
	private Map<String, String> daySelected; 
	private Typeface metro;
	
	private OnTouchListener topTouch = new OnTouchListener() {
		@Override
		public boolean onTouch(View v, MotionEvent event){
			View bar = findViewById(R.id.bar);
			View blue = findViewById(R.id.bluebox);
			RelativeLayout relscreen = (RelativeLayout) findViewById(R.id.relscreen);
			
			int Y = (int) event.getY() + blue.getTop();
			
			if (Y < blue.getBottom() - dp(30) && Y > bar.getTop()){
				RelativeLayout.LayoutParams parms=new RelativeLayout.LayoutParams(blue.getWidth(),blue.getHeight());
					parms.topMargin = Y;
					parms.leftMargin = blue.getLeft();
					parms.height = blue.getBottom() - Y;
				blue.setLayoutParams(parms);
				helperText(relscreen, Y, blue.getWidth(), "top");
			}
			return true;
		}
	};
//	@Override
//    public void onStop() {
////		super.onStop();
////    	finish();
//    }
	
	private OnTouchListener bottomTouch = new OnTouchListener() {
		@Override
		public boolean onTouch(View v, MotionEvent event) {
				View bar = findViewById(R.id.bar);
				View blue = (View) v.getParent();
				RelativeLayout relscreen = (RelativeLayout) findViewById(R.id.relscreen);
				
				int Y = (int) event.getY() + blue.getBottom();

				if (Y - blue.getTop() > dp(30) && Y < bar.getBottom()){
					RelativeLayout.LayoutParams parms=new RelativeLayout.LayoutParams(blue.getWidth(),blue.getHeight());
						parms.topMargin = blue.getTop();
						parms.leftMargin = blue.getLeft();
						parms.height = Y - blue.getTop();
					blue.setLayoutParams(parms);
					helperText(relscreen, Y, blue.getWidth(), "bottom");
				}
				return true;
			}
    };
    
	
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_create_schedule_view);
       
        View topscroll = findViewById(R.id.topscroll);
       topscroll.setOnTouchListener(topTouch);
       View bottomscroll = findViewById(R.id.bottomscroll);
       bottomscroll.setOnTouchListener(bottomTouch);
      
       DisplayMetrics displaymetrics = new DisplayMetrics();
       getWindowManager().getDefaultDisplay().getMetrics(displaymetrics);
       height = displaymetrics.heightPixels;
       width = displaymetrics.widthPixels;
       screenDensity = displaymetrics.densityDpi;
       DptoPixel = screenDensity/160;
       startingTime = "12:00";
       finishingTime = "14:00";
       daySelected = new HashMap<String, String>(5);
       daySelected.put("Monday", "false");
       daySelected.put("Tuesday", "false");
       daySelected.put("Wednesday", "false");
       daySelected.put("Thursday", "false");
       daySelected.put("Friday", "false");
       metro = Typeface.createFromAsset(getAssets(), "Segoe UI.ttf");
       TextView daytext = (TextView) findViewById(R.id.daybutton);
       daytext.setTypeface(metro, Typeface.NORMAL);

    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.activity_create_schedule_view, menu);
        return true;
    }
    public void Save(View view){
	 for ( String key : daySelected.keySet() ) {
        	if (daySelected.get(key) == "true") {
        		new SaveScheduleTask().execute(key);
        	}
	 }
    	Intent intent = new Intent(this, ScheduleView.class);
    	startActivity(intent);
    	//ViewGroup.LayoutParams params = new ViewGroup.LayoutParams(20,50);
    	//view.setLayoutParams(params);
    	//Intent intent = new Intent(this, DisplayMessageActivity.class);
    	//EditText text = (EditText) findViewById(R.id.edit_message);
    	//String message = text.getText().toString();
    	//intent.putExtra("com.communitech.testapp.message", message);
    	//startActivity(intent);
    }
    public void dayTextClick(View view){
    	
    	TextView text = (TextView) view;
    	
    	if (daySelected.get((String) text.getHint()) == "false"){
        	text.setBackgroundColor(Color.argb(255, 0, 102, 200));
        	daySelected.put((String) text.getHint(), "true");
    	} else {
    		text.setBackgroundColor(Color.BLACK);
    		daySelected.put((String) text.getHint(), "false");
    	}
    }
    
    private void helperText(RelativeLayout view, int Y, int width, String location){
    	View rhelper = findViewById(555);
    	View bar = findViewById(R.id.bar);
    	view.removeView(rhelper);
    	TextView helper = new TextView(getBaseContext());
    	int timeY = Y - bar.getTop();
    	int H=1;
    	int M=1;
    	String Mstring = "";
		for (int i=1;i<=11;i++){
			if (timeY <= bar.getHeight()/10*i){
				H = i + 7;
				for (int a=1;a<=40;a++){
					if (timeY <= bar.getHeight()/40*a){
						M = 15*(a - (i-1)*4 - 1);
						if (String.valueOf(M).length() < 2) { Mstring = "0" + String.valueOf(M);} else { Mstring = String.valueOf(M);}
					break;
					}
				}
				break;
			}
		}
    	helper.setText(String.valueOf(H) + ":" + Mstring);
		helper.setTextSize(bar.getWidth() / 5);
		helper.setId(555);
		helper.setTypeface(metro, Typeface.NORMAL);
		RelativeLayout.LayoutParams parms = new RelativeLayout.LayoutParams(width, dp(70));
			parms.topMargin = Y - dp(50);
			parms.leftMargin = bar.getLeft() + bar.getWidth() + dp(5);
			parms.width = RelativeLayout.LayoutParams.WRAP_CONTENT;
		helper.setLayoutParams(parms);
		view.addView(helper);
		if (location=="top") {
			startingTime = (String) helper.getText();
		} else if (location == "bottom") {
			finishingTime = (String) helper.getText();
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
    private class SaveScheduleTask extends AsyncTask<String, Integer, HttpResponse> {

		@Override
		protected HttpResponse doInBackground(String... params) {
			HttpClient client = new DefaultHttpClient();
	        HttpPost post = new HttpPost(Constants.POST_URL);
	    	JSONObject request = new JSONObject();
	    	SharedPreferences auth_stuff = getSharedPreferences(Constants.SHARED_PREFS_FILE, MODE_PRIVATE);
	        String string = auth_stuff.getString("authstring", " " + Constants.AUTH_SPLITTER + " ");
	        string = string.split(Constants.AUTH_SPLITTER)[0]; 
	    	try {
	        	request.put("action", "updateSchedule");
	        	request.put("branchId", Constants.BRANCH_ID);
	        	request.put("organizationId", Constants.ORGANIZATION_ID);
	        	request.put("locationCode", Constants.LOCATION);
	        	request.put("userName", string);
	        	request.put("startingTime", startingTime);
	        	request.put("finishingTime", finishingTime);
	        	request.put("selectedDate", params[0]);
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
	      

		
		/*@Override
		protected void onPostExecute(HttpResponse response) {
				try {
					BufferedReader reader = new BufferedReader(new InputStreamReader(response.getEntity().getContent(), "UTF-8"));
					String json = reader.readLine();
					JSONTokener tokener = new JSONTokener(json);
					JSONArray jsonarray = new JSONArray(tokener);
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
				}*/
		}
    }
    private int dp(int dp){
    	return dp * DptoPixel;
    }
   
}





	
