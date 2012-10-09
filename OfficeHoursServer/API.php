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
	$referenceId = checkBlank($json -> referenceId);
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
	
	
	$schedules = Array();
	for ($i = 0; $i < count($userScheduleResult); $i++) {
		array_push($schedules, Array('referenceId' => $userScheduleResult[$i]['referenceId'], 'startTime' => $userScheduleResult[$i]['startingTime'], 'endTime' => $userScheduleResult[$i]['finishingTime'], 'date' => $userScheduleResult[$i]['date'], 
				'weekday' => date('l', strtotime($userScheduleResult[$i]['date']))));
	}
	echo json_encode($schedules);
}

if ($action == "updateSchedule"){
		
	$checkDays = checkBlank($json -> checkDays);
	
	
	$values = "";
	$overlap = false;
	////OVERLAP CHECKER///////////////////////////////////////////
	$checkQuery = "SELECT * FROM userschedules
	WHERE userName = '$userName'
	AND organizationId = $organizationId
	AND branchId = $branchId
	AND locationCode = '$locationCode'
	AND date = '".date("Y-m-d", strtotime($selectedDate))."';";
	$executeQuery = $db->prepare($checkQuery);
	$executeQuery->execute() or exit("Error: INSERT query failed.");
	$scheduleResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);
	for ($i = 0; $i < count($scheduleResult); $i++) {
		if (strtotime($startingTime) > strtotime($scheduleResult[$i]['startingTime']) && strtotime($finishingTime) < strtotime($scheduleResult[$i]['finishingTime']) && $referenceId != $scheduleResult[$i]['referenceId']){
				$overlap = true;
				break;
		} elseif (strtotime($startingTime) > strtotime($scheduleResult[$i]['startingTime']) && strtotime($startingTime) < strtotime($scheduleResult[$i]['finishingTime']) && $referenceId != $scheduleResult[$i]['referenceId']) {
				$overlap=true;
				
				$query = "UPDATE userschedules
				SET finishingTime = '$finishingTime'
				WHERE userName = '$userName'
				AND organizationId = $organizationId
				AND branchId = $branchId
				AND locationCode = '$locationCode'
				AND date = '".date("Y-m-d", strtotime($selectedDate))."'
				AND startingTime = '".$scheduleResult[$i]['startingTime']."';";
				$executeQuery = $db->prepare($query);
				$executeQuery->execute() or exit("Error: INSERT query failed.");
				break;
		} else if (strtotime($finishingTime) < strtotime($scheduleResult[$i]['finishingTime']) && strtotime($finishingTime) > strtotime($scheduleResult[$i]['startingTime']) && $referenceId != $scheduleResult[$i]['referenceId']) {
				$overlap=true;
				$query = "UPDATE userschedules
				SET startingTime = '$startingTime'
				WHERE userName = '$userName'
				AND organizationId = $organizationId
				AND branchId = $branchId
				AND locationCode = '$locationCode'
				AND date = '".date("Y-m-d", strtotime($selectedDate))."'
				AND finishingTime = '".$scheduleResult[$i]['finishingTime']."';";
				$executeQuery = $db->prepare($query);
				$executeQuery->execute() or exit("Error: INSERT query failed.");
				break;
		} else if (strtotime($finishingTime) > strtotime($scheduleResult[$i]['finishingTime']) && strtotime($startingTime) < strtotime($scheduleResult[$i]['startingTime']) && $referenceId != $scheduleResult[$i]['referenceId']){
				$overlap=true;
				$query = "UPDATE userschedules
				SET startingTime = '$startingTime',
				finishingTime = '$finishingTime'
				WHERE userName = '$userName'
				AND organizationId = $organizationId
				AND branchId = $branchId
				AND locationCode = '$locationCode'
				AND date = '".date("Y-m-d", strtotime($selectedDate))."';";
				//AND startingTime = '".$scheduleResult[$i]['startingTime']."'
				//AND finishingTime = '".$scheduleResult[$i]['finishingTime']."';";
				$executeQuery = $db->prepare($query);
				$executeQuery->execute() or exit("Error: INSERT query failed.");
				break;
		}
	}
	///////\\\\\\\OVERLAP CHECKER///////////\\\\\\\\\\\\\\\\\\\\\
	if ($overlap == true && $referenceId != "") {
		$deleteQuery = "DELETE FROM userschedules
			WHERE organizationId = '$organizationId'
			AND branchId = '$branchId'
			AND locationCode = '$locationCode'
			AND userName = '$userName'
			AND referenceId = '$referenceId'
			AND date = '$selectedDate';";
		$executeQuery = $db->prepare($deleteQuery);
		$executeQuery->execute() or exit("Error: DELETE  query failed.");
	}
	if (!$overlap){
		$checkQuery = "SELECT * FROM userschedules
		WHERE userName = '$userName'
		AND organizationId = $organizationId
		AND branchId = $branchId
		AND locationCode = '$locationCode'
		AND date = '".date("Y-m-d", strtotime($selectedDate))."'
		AND referenceId = '$referenceId';";
		$executeQuery = $db->prepare($checkQuery);
		$executeQuery->execute() or exit("Error: INSERT query failed.");
		$scheduleResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);
		if(count($scheduleResult) <= 0 ){
			$query = "INSERT INTO userschedules (userName, organizationId, branchId, locationCode, date, startingTime, signedIn, finishingTime, signedOut, status) VALUES ";	
			$values .= "('$userName',$organizationId,$branchId,'$locationCode','".date("Y-m-d", strtotime($selectedDate))."','$startingTime','00:00','$finishingTime','00:00','y')";
		} else {
			echo $finishingTime;
			$query = "UPDATE userschedules 
					SET finishingTime = '$finishingTime',
						startingTime = '$startingTime'
					WHERE userName = '$userName'
					AND organizationId = $organizationId
					AND branchId = $branchId
					AND locationCode = '$locationCode'
					AND date = '".date("Y-m-d", strtotime($selectedDate))."'
					AND referenceId = '$referenceId';";
		}
		
		$executeQuery = $db->prepare($query.$values);
		$executeQuery->execute() or exit("Error: INSERT query failed.");
		header('HTTP', true, 200);
	}
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

function checkOverlap(){
	
	return overlap;
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