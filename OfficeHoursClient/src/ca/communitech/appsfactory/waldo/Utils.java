package ca.communitech.appsfactory.waldo;

import android.content.Context;
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
}
