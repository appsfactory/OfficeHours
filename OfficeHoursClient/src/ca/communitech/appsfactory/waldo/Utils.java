package ca.communitech.appsfactory.waldo;

import android.content.Context;
import android.graphics.Typeface;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import android.widget.Toast;

public class Utils {
	static Toast currenttoast;// = Toast.makeText(null, "", Toast.LENGTH_SHORT);
	public static void errormessage(String message, Context context){
		CharSequence errormessage = message;
		int duration = Toast.LENGTH_SHORT;
		if (currenttoast != null){
			currenttoast.cancel();
		}
		//return that user messed up
		Toast toastiness = Toast.makeText(context, errormessage, duration);
		currenttoast = toastiness;
		toastiness.show();
	}
	public static void setFont(View view, Typeface metro) {
		ViewGroup v = (ViewGroup) view;
		for (int i=0; i < v.getChildCount(); i++){
    	   TextView text = (TextView) v.getChildAt(i);
    		text.setTypeface(metro, Typeface.NORMAL);
       }
	}
}
