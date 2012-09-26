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
		echo json_encode(array('message'=>'failure'));
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
	$date = date('Y-m-d');
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