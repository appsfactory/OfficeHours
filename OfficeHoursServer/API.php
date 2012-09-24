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

header("Content-Type: application/json");
/*$peniscock = array('hello' => 'yes', 'cocknballs' => false);
echo json_encode($peniscock);*/
//session_start('officeHours');
$errorMessages = array();

function checkBlank($keyname) {
	$temp = mysql_entities_fix_string((isset($_POST[$keyname]))?trim($_POST[$keyname]):'');
	if ($temp == "") {
		array_push($errorMessages, $keyname . " is Blank");
		return "";
	}
	return $temp;
}

	$organizationName = $_REQUEST['userName'];
	$accessCode = checkBlank('accessCode');
	$userName = checkBlank('userName');
	$password = checkBlank('password');
	$action = checkBlank('action');

	
if ($action == "logIn"){
	$organization = new Organizations('',$organizationName,'','','','','','','','','','','');
	$resultOrganization = $organization->selectOrganization();
	if(count($resultOrganization)==0){
		array_push($errorMessages,"Organization not found.");
	} else {
		$organizationId = $resultOrganization[0]["organizationId"];
		$user = new Users($userName,'','','',$password,'',$organizationId);
		$resultUser = $user->checkUserPassword();
		if(count($resultUser)==0){
			array_push($errorMessages,"Login denied.");
		} else {
			$isAdministrator = $resultUser[0]['isAdministrator'];
		}
		if($accessCode != $resultOrganization[0]["accessCode"]){
			array_push($errorMessages,"Invalid Access Code.");
		}
		if(count($errorMessages) > 0 ){
			//$errorMessages = implode('*', $errorMessages);
			//header("location:index.php?errorMessages=$errorMessages");
			//exit();
			echo json_encode(array('message'=>'failure'));
		} else {
			echo json_encode(array('message'=>'success'));
				
		}
		session_start('officeHours');
		$_SESSION['userName'] = $userName;
		$_SESSION['organizationId'] = $organizationId;
		$_SESSION['organizationName'] = $organizationName;
	}	
}
if ($action = 'signIn') {
	// Get parameters from the URL
	$locationCode = $_POST['locationCode'];
	$time = $_POST['time'];
	$date = date('Y-m-d',strtotime($_POST['date']));
	$actualTime = date("H:i:s");
	
	$query = "UPDATE userschedules
	SET signedIn = '$actualTime'
	WHERE  organizationId = $organizationId
	AND branchId = $branchId
	AND locationCode = '$locationCode'
	AND date = '$date'
	AND startingTime =  '$time'
	AND userName = '$userName'";
	
	$executeQuery = $db->prepare($query);
	$executeQuery->execute() or exit("Error: UPDATE query failed.");
};
	
	// Register Sign Out
if($action == 'signOut'){
	// Get parameters from the URL
	$locationCode = $_POST['txt_locationCode'];
	$time = $_POST['txt_time'];
	$date = date('Y-m-d',strtotime($_POST['date']));
	$actualTime = date("H:i:s");
	
	$query = "UPDATE userschedules
	SET signedOut = '$actualTime'
	WHERE  organizationId = $organizationId
	AND branchId = $branchId
	AND locationCode = '$locationCode'
	AND date = '$date'
	AND finishingTime = '$time'
	AND userName = '$userName'";
	
	$executeQuery = $db->prepare($query);
	$executeQuery->execute() or exit("Error: UPDATE query failed.");
}


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