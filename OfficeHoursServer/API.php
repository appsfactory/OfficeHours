<?php

require_once ("includes/dbConnection.php");
require_once("includes/validationFunctions.php");


// Includes the classes
require_once("classes/organizations.class.php");
require_once("classes/branches.class.php");
require_once("classes/locations.class.php");
require_once("classes/users.class.php");

//if ($_SERVER["REQUEST_METHOD"] != "POST") {
	//echo('<script type="text/javascript"> console.log("called")</script>');
	//$this->response ('', '406');
//}

header("Content-type: application/json");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
//echo json_encode(count($_POST));
$json = json_decode(file_get_contents("php://input"));
//echo $json -> organizationName;
//echo json_encode('blah');
/*$peniscock = array('hello' => 'yes', 'cocknballs' => false);
echo json_encode($peniscock);
//session_start('officeHours');*/
$errorMessages = array();
function checkBlank($keyname) {
	if ($keyname == "") {
		array_push($errorMessages, $keyname . " is Blank");
		return "";
	}
	return $keyname;
}
	$organizationName = checkBlank($json -> organizationName);
	$branchId = checkBlank($json -> branchId);
	$locationCode = checkBlank($json -> locationCode);
	$accessCode = checkBlank($json -> accessCode);
	$userName = checkBlank($json -> userName);
	$password = checkBlank($json -> password);
	$action = checkBlank($json -> action);
	$organizationId = checkBlank($json -> organizationId);
	$fromDate = checkBlank($json -> fromDate);
	$toDate = checkBlank($json -> toDate);
	$selectedDate = date('Y-m-d', strtotime(strtolower($json-> selectedDate) . "this week"));
	$date = date('Y-m-d');
	$repeating = checkBlank($json -> repeating);
	$startingTime = str_pad($json -> startingTime,5,0,STR_PAD_LEFT);
	$finishingTime = str_pad($json -> finishingTime,5,0,STR_PAD_LEFT);
	/*$startingHH = str_pad($json -> startingH,2,0,STR_PAD_LEFT);
	$startingMM = str_pad($json -> startingM,2,0,STR_PAD_LEFT);
	$finishingHH = str_pad($json -> finishingH,2,0,STR_PAD_LEFT);
	$finishingMM = str_pad($json -> finishingM,2,0,STR_PAD_LEFT);
	$startingTime = $startingHH  .":". $startingMM;
	$finishingTime = $finishingHH .":". $finishingMM;*/
	
if ($action == "logIn"){
	$organization = new Organizations('',$organizationName,'','','','','','','','','','','');
	$resultOrganization = $organization->selectOrganization();
	//echo "boop";
	//$resultAdministrator = "false";
	if(count($resultOrganization)==0){
		array_push($errorMessages,"Organization not found.");
		//echo json_encode("no organs");		
	} else {
		$organizationId = $resultOrganization[0]["organizationId"];
		$user = new Users($userName,'','','',$password,'',$organizationId);
		$resultUser = $user->checkUserPassword();
		if(count($resultUser)==0){
			array_push($errorMessages,"Login denied.");
			//echo json_encode("no logs");
		} 
		if($accessCode != $resultOrganization[0]["accessCode"]){
			array_push($errorMessages,"Invalid Access Code.");
			//echo json_encode("no access");				
		}
	}
	if(count($errorMessages) > 0 ){
		//$errorMessages = implode('*', $errorMessages);
		//header("location:index.php?errorMessages=$errorMessages");
		//exit();
		header('HTTP', true, 500);
		echo json_encode($errorMessages);
	} else {
		session_start('officeHours');
		$_SESSION['userName'] = $userName;
		$_SESSION['organizationId'] = $organizationId;
		$_SESSION['organizationName'] = $organizationName;
		header('HTTP', true, 200);
		echo json_encode(array('message'=>'success', 'organizationId' => $organizationId));
		
		
	}	
}

if ($action == 'signIn') {
	// Get parameters from the URL
	$time = date('H:i:s',time());
	$date = date('Y-m-d');
	$actualTime = date("H:i:s");
	
	$query = "UPDATE userschedules
	SET signedIn = '$actualTime'
	WHERE  organizationId = $organizationId
	AND branchId = $branchId
	AND locationCode = '$locationCode'
	AND date = '$date'
	AND userName = '$userName'";
	$executeQuery = $db->prepare($query);
	$executeQuery->execute() or exit("Error: UPDATE query failed.");
	//header('HTTP', true, 200);
	echo json_encode("signin success");
}
	
	// Register Sign Out
if($action == 'signOut'){
	// Get parameters from the URL
	$time = $_POST['txt_time'];
	$actualTime = date("H:i:s");
	
	$query = "UPDATE userschedules
	SET signedOut = '$actualTime'
	WHERE  organizationId = $organizationId
	AND branchId = $branchId
	AND locationCode = '$locationCode'
	AND date = '$date'
	AND userName = '$userName'";
	
	$executeQuery = $db->prepare($query);
	$executeQuery->execute() or exit("Error: UPDATE query failed.");
	//header('HTTP', true, 200);
	echo json_encode("signout success");
}

if ($action == "showSchedule"){
	//$branchId = checkBlank($json -> brandId);
	//$locationCode = checkBlank($json -> locationCode);
	
	//$fromDate = isset($_GET['fromDate'])?$_GET['fromDate']:date('m').'/'.date('d').'/'.date('Y');
	//$toDate = isset($_GET['toDate'])?$_GET['toDate']:date("m/d/Y", strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
	/*
	// Selects all organization branches
	$query = "SELECT * FROM branches WHERE organizationId = $organizationId ORDER BY branchName";
	$stm = $db->prepare($query);
	$stm->execute();
	$branchesResult = $stm->fetchAll(PDO::FETCH_BOTH);
	
	// Selects all organization locations
	$query = "SELECT locationCode FROM boardlocations WHERE locationOrganizationId = $organizationId ORDER BY locationCode";
	$stm = $db->prepare($query);
	$stm->execute();
	$boardLocationsResult = $stm->fetchAll(PDO::FETCH_BOTH);
	*/
	// Selects the users schedule
	$query = "SELECT B.branchName, S.*
	FROM branches AS B, userschedules AS S
	WHERE S.organizationId = $organizationId
	AND S.branchId LIKE '$branchId'
	AND S.locationCode LIKE '$locationCode'
	AND B.organizationId = S.organizationId
	AND B.branchId = S.branchId
	AND S.userName = '$userName'
	AND S.date BETWEEN '".date('Y-m-d',strtotime('monday this week'))."' AND '".date('Y-m-d',strtotime('friday this week'))."'
	ORDER BY S.date, S.branchId, S.locationCode";
	$stm = $db->prepare($query);
	$stm->execute();
	$userScheduleResult = $stm->fetchAll(PDO::FETCH_BOTH);
	
	//echo json_encode(var_dump($userScheduleResult));
	//echo json_encode($userScheduleResult[4]['startingTime']);
	$schedules = Array();
	for ($i = 0; $i < count($userScheduleResult); $i++) {
		array_push($schedules, Array('startTime' => $userScheduleResult[$i]['startingTime'], 'endTime' => $userScheduleResult[$i]['finishingTime'], 'date' => $userScheduleResult[$i]['date'], 
				'weekday' => date('l', strtotime($userScheduleResult[$i]['date']))));
	}
	echo json_encode($schedules);
}

if ($action == "updateSchedule"){
		// Get parameters from the URL
/*$query = "DELETE FROM userschedules
	WHERE organizationId = '$organizationId',
	AND userName = '$userName',
	AND date BETWEEN '".date('Y-m-d',strtotime($fromDate))."' AND '".date('Y-m-d',strtotime($toDate))."'";
	$executeQuery = $db->prepare($query);
	$executeQuery->execute() or exit("Error: DELETE query failed.");*/
	//$makingOption = mysql_entities_fix_string($_POST['makingOption']);
	//if($makingOption != "anyDateAndTime"){
		// Get parameters from the URL
		//$fromDate = strtotime($_POST['txt_fromDate']);
		//$toDate = strtotime($_POST['txt_toDate']);
	
	//$locationCode = $_POST['txt_locationCode'];
	$checkDays = checkBlank($json -> checkDays);
	
	//}
	
	// Creates Monday to friday schedule
	//if($makingOption == "MonFri"){
	$values = "";
	//$date = date('Y-m-d', strtotime($fromDate));
	//$comma = false;
	//while($date<=$toDate){
	//	if((date("D",$date) != 'Sat') && (date("D",$date) != 'Sun')){
	$checkQuery = "SELECT * FROM userschedules
	WHERE userName = '$userName'
	AND organizationId = $organizationId
	AND branchId = $branchId
	AND locationCode = '$locationCode'
	AND date = '".date("Y-m-d", strtotime($fromDate))."'
	AND (startingTime = '$startingTime'
	OR finishingTime = '$finishingTime')";
	$executeQuery = $db->prepare($checkQuery);
	$executeQuery->execute() or exit("Error: INSERT query failed.");
	$scheduleResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);
	if(count($scheduleResult) <= 0 ){
		$query = "INSERT INTO userschedules (userName, organizationId, branchId, locationCode, date, startingTime, signedIn, finishingTime, signedOut, status) VALUES ";	
		//$values .= ($comma)?',':'';
		$values .= "('$userName',$organizationId,$branchId,'$locationCode','".date("Y-m-d", strtotime($fromDate))."','$startingTime','00:00','$finishingTime','00:00','y')";
		//$comma = true;
	} else {
		echo $finishingTime;
		$query = "UPDATE userschedules 
				SET finishingTime = '$finishingTime',
					startingTime = '$startingTime'
				WHERE userName = '$userName'
				AND organizationId = $organizationId
				AND branchId = $branchId
				AND locationCode = '$locationCode'
				AND date = '".date("Y-m-d", strtotime($fromDate))."'
				AND (startingTime = '$startingTime'
				OR finishingTime = '$finishingTime')";
		//(userName, organizationId, branchId, locationCode, date, startingTime, signedIn, finishingTime, signedOut, status) VALUES ;	
	}
		//	}
			//$date = strtotime(date("Y-m-d", $date) . "+1 day");
		//}
		//$dateFrom = date("Y-m-d", $fromDate);
		//$dateTo = date("Y-m-d", $toDate);
		//if($values != ""){
			//if($action == 'add'):
	$executeQuery = $db->prepare($query.$values);
	$executeQuery->execute() or exit("Error: INSERT query failed.");
	header('HTTP', true, 200);
	//echo $finishingTime;
	
			//endif;
			//header ("location: userScheduleMaker.php?fromDate=$dateFrom&toDate=$dateTo&commited=yes");
		//}else{
		//	array_push($errorMessages,"There is no selected days between the dates".date("m/d/Y",$dateFrom)." and ".date("m/d/Y",$dateTo)." or duplicated schedule was found.");
		//	$errorMessages = implode('*', $errorMessages);
		//}
	//}
}

if ($action == "deleteSchedule") {
		
	$query = "DELETE FROM userschedules
	WHERE organizationId = '$organizationId'
	AND branchId = '$branchId'
	AND locationCode = '$locationCode'
	AND userName = '$userName'
	AND startingTime = '$startingTime'
	AND finishingTime = '$finishingTime'
	AND date = '$selectedDate';";
	$executeQuery = $db -> prepare($query);
	$executeQuery->execute() or exit("Error: DELETE query failed.");
	header('HTTP', true, 200);		
}
if ($action == "currentDate") {
	echo json_encode(Array("date" => date('Y/m/d'), "weekday" => date('l')));
}
//if($action != "signIn" && $action != "signOut" && $action != "logIn"){
	//header('HTTP', true, 404);
	//echo $action;
//}
//MAKE THIS WORK
/*
if(count($errorMessages) > 0 ):
$errorMessages = implode('*', $errorMessages);
header("location:index.php?errorMessages=$errorMessages");
exit();
endif;*/

//signout/signin
//view schedule
//edit schedule
?>