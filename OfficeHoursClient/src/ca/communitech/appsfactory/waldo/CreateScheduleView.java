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
import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.Menu;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnTouchListener;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

public class CreateScheduleView extends Activity {

	private int height;
	private int width;
	private int screenDensity;
	private int DptoPixel;
	private String startingTime;
	private String finishingTime;
	private Map<String, String> daySelected; 
	private Typeface metro;
	private String[] startend;
	private Boolean saving;
	
	 @Override
	    public void onCreate(Bundle savedInstanceState) {
		 super.onCreate(savedInstanceState);
	        setContentView(R.layout.activity_create_schedule_view);
	       
	       //set the interactable areas of the draggable time slider
	       View topscroll = findViewById(R.id.topscroll);
	       topscroll.setOnTouchListener(topTouch);
	       View bottomscroll = findViewById(R.id.bottomscroll);
	       bottomscroll.setOnTouchListener(bottomTouch);
	       

	       //initialize map of days to be added
	       daySelected = new HashMap<String, String>(5);
	       daySelected.put("Monday", "false");
	       daySelected.put("Tuesday", "false");
	       daySelected.put("Wednesday", "false");
	       daySelected.put("Thursday", "false");
	       daySelected.put("Friday", "false");
	       
	       //grab the start/end times from the intent, and set them if they exist
	       String[] intentextra = this.getIntent().getStringArrayExtra("ca.communitech.appsfactory.waldo.startEnd");
    	   LinearLayout daybuttons = (LinearLayout) findViewById(R.id.daybuttons);
	       if (intentextra != null) {
	    	   startend = intentextra;
	    	   for (int i=0;i < daybuttons.getChildCount(); i++){
	    		   TextView daybutton = (TextView) daybuttons.getChildAt(i);
	    		   daybutton.setClickable(false);
	    		   //daybutton.setBackgroundColor(Color.argb(255, 200, 55, 55));

	    		   if (String.valueOf(daybutton.getHint()).equals(startend[3])) {
	    			   daybutton.setBackgroundColor(Color.argb(255, 44, 132, 183));

	    		   }
	    	   }
	    	   //TextView addheader = (TextView) findViewById(R.id.addheader);
	    	   //TextView updateheader = (TextView) findViewById(R.id.updateheader);
	    	   //addheader.setVisibility(View.INVISIBLE);
	    	  // updateheader.setVisibility(View.VISIBLE);
	    	   
	       }else{
	    	   for (int i=0;i < daybuttons.getChildCount(); i++){
	    		   TextView daybutton = (TextView) daybuttons.getChildAt(i);
	    		   daybutton.setClickable(true);
	    	   }
	    	   startend = new String[4];
	    	   startend[0] = "f";
	    	   startend[1] = "false";
	    	   startend[2] = "false";
	    	   startend[3] = "false";
	       }
	       //used for calculating pixels from dp
	       DisplayMetrics displaymetrics = new DisplayMetrics();
	       getWindowManager().getDefaultDisplay().getMetrics(displaymetrics);
	      
	       height = displaymetrics.heightPixels;
	       width = displaymetrics.widthPixels;
	       screenDensity = displaymetrics.densityDpi;
	       DptoPixel = screenDensity/160;
	       
	       startingTime = "12:00";
	       finishingTime = "14:00";
	    
	      //set font
	       metro = Typeface.createFromAsset(getAssets(), "HelveticaCY.dfont");
	       
	       LinearLayout sidebar = (LinearLayout) findViewById(R.id.sidebar);
	       RelativeLayout relv = (RelativeLayout) findViewById(R.id.savecancel);
	       
	       Utils.setFont(sidebar, metro);
	       Utils.setFont(daybuttons, metro);
	       Utils.setFont(relv, metro);
	       saving = false;
	       
	     
	    }
	 	@Override
	 	public void onWindowFocusChanged(boolean hasFocus) {
	 		super.onWindowFocusChanged(hasFocus);
	 		RelativeLayout blue = (RelativeLayout) findViewById(R.id.bluebox);
	 		if (startend[0] != "f" && saving == false){
	    	   updateBlue(blue);
	 		}
	 		
	 	}
	    @Override
	    public void onStop(){
	    	super.onStop();
	    	finish();
	    }

	    @Override
	    public boolean onCreateOptionsMenu(Menu menu) {
	        getMenuInflater().inflate(R.menu.activity_create_schedule_view, menu);
	        return true;
	    }
	
	/**
	 * listens for a touch event at the top edge of the box and drags accordingly
	 * 
	 */
	private OnTouchListener topTouch = new OnTouchListener() {
		@Override
		public boolean onTouch(View v, MotionEvent event){
			View bar = findViewById(R.id.bar);
			View blue = findViewById(R.id.bluebox);
			RelativeLayout relscreen = (RelativeLayout) findViewById(R.id.relscreen);
			
			int Y = (int) event.getY() + blue.getTop() - 15;
			
			if (Y < blue.getBottom() - dp(30) && Y > bar.getTop() + dp(5)){
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
	
	/**
	 * Listens for a touch event at the bottom edge of the box and drags accordingly
	 */
	private OnTouchListener bottomTouch = new OnTouchListener() {
		@Override
		public boolean onTouch(View v, MotionEvent event) {
				View bar = findViewById(R.id.bar);
				View blue = (View) v.getParent();
				RelativeLayout relscreen = (RelativeLayout) findViewById(R.id.relscreen);
				
				int Y = (int) event.getY() + blue.getBottom() - 45;

				if (Y - blue.getTop() > dp(30) && Y < bar.getBottom() - dp(5)){
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
    
	
	
   
    /**
     * grab info from UI and pass to the ScheduleView activity that will populate the screen
     * @param view
     */
    public void Save(View view){
    	boolean dayChosen = false;
    	if (startend[3] != "false"){
    		daySelected.put(startend[3], "true");
    	}
    	for ( String key : daySelected.keySet() ) {
        	if (daySelected.get(key) == "true") {
        		new SaveScheduleTask().execute(key);
        		dayChosen = true;
        	}
    	}
		if (!dayChosen) {
			Utils.errormessage("You must select day(s) (on the right) to add schedule to!", getApplicationContext());
			 
		} else {
			saving = true;
	    	Intent intent = new Intent(this, ScheduleView.class);
	    	intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
	    	startActivity(intent);
		}
    }
    /**
     * resets back to the ScheduleView activity
     * @param v
     */
    public void Cancel (View v){
    	Intent intent = new Intent(this, ScheduleView.class);
    	intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
    	startActivity(intent);
    }
    
    
    
    public void dayTextClick(View view){
    	
    	TextView text = (TextView) view;
    	
    	if (daySelected.get((String) text.getHint()) == "false"){
        	//text.setBackgroundColor(Color.argb(255, 0, 102, 200));
        	text.setBackgroundColor(Color.argb(255, 44, 132, 183));
        	daySelected.put((String) text.getHint(), "true");
    	} else {
    		text.setBackgroundColor(Color.argb(0,0,0,0));
    		daySelected.put((String) text.getHint(), "false");
    	}
    }
    
    
    
    private void helperText(RelativeLayout view, int Y, int width, String location){
    	TextView helper = new TextView(getBaseContext());
    	View bar = findViewById(R.id.bar);
    	View rhelper;
    	if (location == "top") {
    		rhelper = findViewById(555);
    	} else {
    		rhelper = findViewById(556);
    	}
    	view.removeView(rhelper);


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
						if (String.valueOf(M).length() < 2) { 
							Mstring = "0" + String.valueOf(M);
						} else { 
							Mstring = String.valueOf(M);
						}
					break;
					}
				}
				break;
			}
		}
		//Set helper text parameters
    	helper.setText(String.valueOf(H) + ":" + Mstring);
		helper.setTextSize(bar.getWidth() / 6);
		if (location == "top"){
			helper.setId(555);
		} else {
			helper.setId(556);
		}
		helper.setTypeface(metro, Typeface.NORMAL);
		RelativeLayout.LayoutParams parms = new RelativeLayout.LayoutParams(width, dp(70));
			parms.topMargin = Y - dp(25);
			parms.leftMargin = bar.getLeft() + bar.getWidth() + dp(5);
			parms.width = RelativeLayout.LayoutParams.WRAP_CONTENT;
		helper.setLayoutParams(parms);
		view.addView(helper);
		
		//Set start or finish time (whichever applies) to calculated time value
		if (location=="top") {
			startingTime = (String) helper.getText();
		} else if (location == "bottom") {
			finishingTime = (String) helper.getText();
		}
    }
    
    
    
    private void databaseConnectionErrorMessage() {
    	new Thread(new Runnable() {
			@Override
			public void run() {
				Utils.errormessage("Error connecting to database. Please try again in a few moments", getBaseContext());
			}
		}).start();
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
	        	if (startend[2] != "false" ) {
	        	request.put("referenceId", startend[2]);
	        	}
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
    }

    
    private void updateBlue (RelativeLayout blue){
    	String start = startend[0];
    	String end = startend[1];
    	View bar = findViewById(R.id.bar);
    	int H=Integer.parseInt(start.substring(0, 2));
    	int M=Integer.parseInt(start.substring(3,5));
    	//calculate height of bar
		int top= (bar.getHeight()/40)*((M/15 + 1 + 4*(H-7)));
    	Log.i("H", String.valueOf(top));
		//re-use H and M variables because why not
    	H=Integer.parseInt(end.substring(0, 2));
    	M=Integer.parseInt(end.substring(3,5)); 
    	int bottom= bar.getHeight()/40*(M/15 + 1 + 4*(H-7));
    	RelativeLayout relscreen = (RelativeLayout) findViewById(R.id.relscreen);
    	RelativeLayout.LayoutParams parms=new RelativeLayout.LayoutParams(blue.getWidth(),blue.getHeight());
			parms.topMargin = (int) top;
			parms.leftMargin = blue.getLeft();
			parms.height = (int) bottom - (int) top;
		blue.setLayoutParams(parms);
		helperText(relscreen, (int) top, blue.getWidth(), "top");
    	helperText(relscreen, (int) bottom, blue.getWidth(), "bottom");
	}
    
    private int dp(int dp){
    	return dp * DptoPixel;
    }
   
}





	
