<!-- admCRUD.php
        PROG8105-1 Systems Project: Controls all requests, check for integrity,
            errors, uses the classes to Create, Read, Update and Delete. Once
            the operation is completed admCRUD re-direct the user for the 
            appropriate screen sending the respective message via URL. 

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.02.01: Created
-->
<?
// Includes
require_once ("includes/dbConnection.php");
require_once("includes/validationFunctions.php");

// Includes the classes
require_once("classes/organizations.class.php");
require_once("classes/branches.class.php");
require_once("classes/locations.class.php");
require_once("classes/users.class.php");

// Get parameters from the URL 
session_start('officeHours');
$organizationId = isset($_SESSION['organizationId'])?$_SESSION['organizationId']:'';
$userName = isset($_SESSION['userName'])?$_SESSION['userName']:'';
$table  = mysql_entities_fix_string((isset($_POST['table']))?$_POST['table']:$_GET['table']);
$action = mysql_entities_fix_string((isset($_POST['action']))?$_POST['action']:$_GET['action']);

$errorMessages = array();

/* This block handle all the requests for organizations table according to the
*  action which also is sent via URL. When the requested is finished the user 
*  has the appropriated message   
*/ 
if($table == 'organizations'):
    // Get parameters from the URL 
    $organizationName = mysql_entities_fix_string((isset($_POST['organizationName']))?$_POST['organizationName']:'');
    $newOrganizationName = mysql_entities_fix_string((isset($_POST['newOrganizationName']))?$_POST['newOrganizationName']:'');
    
    // Creates organization
    if($action == 'create'):
        // Get parameters from the URL 
        $organizationAddress = mysql_entities_fix_string(trim($_POST['organizationAddress']));
        $organizationPhone = mysql_entities_fix_string(trim($_POST['organizationPhone']));
        $adminuserName = mysql_entities_fix_string(trim($_POST['adminUserName']));
        $adminPassword = mysql_entities_fix_string(trim($_POST['adminPassword']));
        $adminFirstName = mysql_entities_fix_string(trim($_POST['adminFirstName']));
        $adminLastName = mysql_entities_fix_string(trim($_POST['adminLastName']));
        $adminEmail = mysql_entities_fix_string(trim($_POST['adminEmail']));
        
        // Handles errors and send it back to the user cancelling the operation 
        if(isEmpty($newOrganizationName)):
            array_push($errorMessages,"Organization Name is required."); 
        endif;        
        if(isEmpty($adminuserName)):
            array_push($errorMessages,"Administrator Username is required."); 
        endif;   
        if(isEmpty($adminPassword)):
            array_push($errorMessages,"Administrator Password is required."); 
        endif;                         
    	$organization = new Organizations('',$newOrganizationName,'','','','','','','','','','','');
	$result = $organization->selectOrganization();    
        if(count($result) > 0 ):
            array_push($errorMessages,"Organization Name already exists in our database, please choose another one."); 
        endif; 
        if(count($errorMessages) > 0 ):
            $errorMessages = implode('*', $errorMessages);
            header("location:index.php?errorMessages=$errorMessages");
            exit();
        endif;
        /* Instantiates the class and execute the operation requested, once it 
        *  has done send the appropriated message back to the user  
        */       
	$organization = new Organizations('',$newOrganizationName, $organizationAddress, $organizationPhone,$organizationName,'#54e4f2','#1d30b9','#FFFFFF','#5f94d8','#FFFFFF','#000000','#000000','#FFFFFF');
	$organization->Insert();
	$organization = new Organizations('',$newOrganizationName,'','','','','','','','','','','');
	$result = $organization->selectOrganization();
        $organizationId = $result[0]["organizationId"];
        $user = new Users($adminuserName,$adminFirstName,$adminLastName,$adminEmail,$adminPassword,'Y',$organizationId);
	$user->Insert();
        session_start('officeHours');
        $_SESSION['userName'] = $adminuserName;
        $_SESSION['organizationId'] = $organizationId; 
        $_SESSION['organizationName'] = $newOrganizationName; 
        
        header ("location:admControlPanel.php?commited=yes");
    endif;

    // Updates organization details
    if($action == 'upd'):
        // Get parameters from the URL  
        //$organizationId = mysql_entities_fix_string((isset($_POST['organizationId']))?$_POST['organizationId']:$_GET['organizationId']);    
        $accessCode = mysql_entities_fix_string((isset($_POST['accessCode']))?$_POST['accessCode']:$_GET['accessCode']);     
        $fontHeadingColor = mysql_entities_fix_string((isset($_POST['fontHeadingColor']))?$_POST['fontHeadingColor']:$_GET['fontHeadingColor']);   
        $backgroundHeadingColor = mysql_entities_fix_string((isset($_POST['backgroundHeadingColor']))?$_POST['backgroundHeadingColor']:$_GET['backgroundHeadingColor']);  
        $fontGridOddColor = mysql_entities_fix_string((isset($_POST['fontGridOddColor']))?$_POST['fontGridOddColor']:$_GET['fontGridOddColor']);  
        $backgroundGridOddColor = mysql_entities_fix_string((isset($_POST['backgroundGridOddColor']))?$_POST['backgroundGridOddColor']:$_GET['backgroundGridOddColor']); 
        $fontGridEvenColor = mysql_entities_fix_string((isset($_POST['fontGridEvenColor']))?$_POST['fontGridEvenColor']:$_GET['fontGridEvenColor']); 
        $backgroundGridEvenColor = mysql_entities_fix_string((isset($_POST['backgroundGridEvenColor']))?$_POST['backgroundGridEvenColor']:$_GET['backgroundGridEvenColor']);
        $fontBottomColor = mysql_entities_fix_string((isset($_POST['fontBottomColor']))?$_POST['fontBottomColor']:$_GET['fontBottomColor']);
        $backgroundBottomColor = mysql_entities_fix_string((isset($_POST['backgroundBottomColor']))?$_POST['backgroundBottomColor']:$_GET['backgroundBottomColor']);
        
        // Handles errors and send it back to the user cancelling the operation
        if(isEmpty($accessCode)):
            array_push($errorMessages,"Access Code is required."); 
        endif;        
        if(isEmpty($fontHeadingColor)):
            array_push($errorMessages,"Heading Font Color is required - Format Hex: XXXXXX"); 
        endif;   
        if(isEmpty($backgroundHeadingColor)):
            array_push($errorMessages,"Heading Background Color is required - Format Hex: XXXXXX"); 
        endif; 
 
        if(isEmpty($fontGridOddColor)):
            array_push($errorMessages,"Board Odd Lines Font Color is required  - Format Hex: XXXXXX"); 
        endif;        

        if(isEmpty($backgroundGridOddColor)):
            array_push($errorMessages,"Board Odd Lines Backgroung Color is required  - Format Hex: XXXXXX"); 
        endif;

        if(isEmpty($fontGridEvenColor)):
            array_push($errorMessages,"Board Even Lines Font Color is required  - Format Hex: XXXXXX"); 
        endif;        

        if(isEmpty($backgroundGridEvenColor)):
            array_push($errorMessages,"Board Even Lines Backgroung Color is required  - Format Hex: XXXXXX"); 
        endif;        
        
        if(isEmpty($fontBottomColor)):
            array_push($errorMessages,"Footer Font Color is required  - Format Hex: XXXXXX"); 
        endif;        

        if(isEmpty($backgroundBottomColor)):
            array_push($errorMessages,"Footer Backgroung Color is required  - Format Hex: XXXXXX"); 
        endif; 
        if(count($errorMessages) > 0 ):
            $errorMessages = implode('*', $errorMessages);
            header ("location:admSettings.php?errorMessages=$errorMessages");
            exit();
        endif;
        /* Instantiates the class and execute the operation requested, once it 
        *  has done send the appropriated message back to the user  
        */        
        $organization = new Organizations($organizationId,'','','',$accessCode,$fontHeadingColor,$backgroundHeadingColor,
                $fontGridOddColor,$backgroundGridOddColor,$fontGridEvenColor,$backgroundGridEvenColor,$fontBottomColor,$backgroundBottomColor);
	$organization->updateSettings();
        header ("location:admSettings.php?commited=yes");
    endif;
    
    // Check log in information
    if($action == 'logIn'):
        // Get parameters from the URL          
        $organizationName = mysql_entities_fix_string(trim($_POST['organizationName']));
        $accessCode = mysql_entities_fix_string(trim($_POST['accessCode']));
        $userName = mysql_entities_fix_string(trim($_POST['userName']));
        $password = mysql_entities_fix_string(trim($_POST['userPassword']));

        
        /* Handles errors and send it back to the user cancelling the operation,
        *  instantiates the class and execute the operation requested, once it 
        *  has done send the appropriated message back to the user  
        */         
        if(isEmpty($organizationName)):
            array_push($errorMessages,"Organization Name is required."); 
        endif; 
        if(isEmpty($accessCode)):
             array_push($errorMessages,"Access Code is required."); 
        endif;
        if(isEmpty($userName)):
             array_push($errorMessages,"Username is required."); 
        endif;        
        if(isEmpty($password)):
             array_push($errorMessages,"Password is required."); 
        endif;
        $organization = new Organizations('',$organizationName,'','','','','','','','','','','');
	$resultOrganization = $organization->selectOrganization(); 
        
        if(count($resultOrganization)==0):
             array_push($errorMessages,"Organization not found.");
        else:
            $organizationId = $resultOrganization[0]["organizationId"];
            $user = new Users($userName,'','','',$password,'',$organizationId);
            $resultUser = $user->checkUserPassword();
            if(count($resultUser)==0):
                 array_push($errorMessages,"Login denied.");
            else:
                $isAdministrator = $resultUser[0]['isAdministrator'];
            endif;             
            if($accessCode != $resultOrganization[0]["accessCode"])
                array_push($errorMessages,"Invalid Access Code.");
        endif;  
        if(count($errorMessages) > 0 ):
            $errorMessages = implode('*', $errorMessages);
            header("location:index.php?errorMessages=$errorMessages");
            exit();
        endif;
        session_start('officeHours');
        $_SESSION['userName'] = $userName;
        $_SESSION['organizationId'] = $organizationId;  
        $_SESSION['organizationName'] = $organizationName;
        
        if($isAdministrator == 'Y'):
            header ("location:admControlPanel.php");
        else:
            header ("location:userControlPanel.php");
        endif;
    endif;

    // Launch the office hours board
    if($action == 'activation'):
        // Get parameter from the URL 
        $accessCode = mysql_entities_fix_string(trim($_POST['accessCode']));
        
        // Handles errors and send it back to the user cancelling the operation
        if(isEmpty($organizationName)):
            array_push($errorMessages,"Organization Name is required."); 
        endif; 
        if(isEmpty($accessCode)):
             array_push($errorMessages,"Access Code is required."); 
        endif;

        $organization = new Organizations('',$organizationName,'','','','','','','','','','','');
	$resultOrganization = $organization->selectOrganization(); 
        
        if(count($resultOrganization)==0):
             array_push($errorMessages,"Organization not found.");
        else:
            $organizationId = $resultOrganization[0]["organizationId"];
            if($accessCode != $resultOrganization[0]["accessCode"])
                array_push($errorMessages,"Invalid Access Code.");
        endif;
 
        $access = '';
        if(count($errorMessages) > 0 ):
            $errorMessages = implode('*', $errorMessages);
            $access = 'denied';
            header("location:boardActivation.php?access=$access&organizationId=$organizationId&errorMessages=$errorMessages");
            exit();
        else:
            $access = 'ok';
            header ("location:boardActivation.php?access=$access&organizationId=$organizationId");   
        endif;
    endif;
endif;

/* This block handle all the requests for branches table according to the
*  action which also is sent via URL. When the requested is finished the user 
*  has the appropriated message   
*/ 
// Get parameters from the URL 
//$organizationId = mysql_entities_fix_string((isset($_POST['organizationId']))?$_POST['organizationId']:$_GET['organizationId']);
$branchId = mysql_entities_fix_string((isset($_POST['branchId']))?$_POST['branchId']:$_GET['branchId']);
//$userName = mysql_entities_fix_string((isset($_POST['userName']))?$_POST['userName']:$_GET['userName']);

if($table == 'branches'):
    // Get parameters from the URL 
    $branchName = mysql_entities_fix_string(trim($_POST['txt_branchName']));
    $branchAddress = mysql_entities_fix_string(trim($_POST['txt_branchAddress']));
    $branchPhone = mysql_entities_fix_string(trim($_POST['txt_branchPhone']));

    // Add branch
    if($action == 'add'):
        /* Handles errors and send it back to the user cancelling the operation,
        *  instantiates the class and execute the operation requested, once it 
        *  has done send the appropriated message back to the user  
        */  
        if(isEmpty($branchName))
            array_push($errorMessages,"Branch Name is required."); 
        
        $branch = new Branches('',$branchName,'','',$organizationId);
        $result = $branch->selectBranchByName();  
        if(count($result) > 0 ):
            array_push($errorMessages,"Branch Name already exists."); 
        endif; 
        if(count($errorMessages) > 0 ):
            $errorMessages = implode('*', $errorMessages);
            header ("location: admBranches.php?errorMessages=$errorMessages");
            exit();
        endif;        
        $branch = new Branches($branchId,$branchName,$branchAddress,$branchPhone,$organizationId);
        $branch->Insert();
    endif;
    
    // Update branch information
    if($action == 'upd'):
        $branch = new Branches($branchId,$branchName,$branchAddress,$branchPhone,$organizationId);
        $branch->Update();
    endif;
    
    // Delete branch
    if($action == 'del'):
        $branch = new Branches($branchId,'','','',$organizationId);
        $branch->Delete();
        
        $location = new Locations('','',$branchId,$organizationId);
        $location->DeleteAllLocations();
        
        $query = "DELETE FROM userschedules
                    WHERE organizationId = $organizationId
                    AND branchId = $branchId";
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: DELETE query failed.");
    endif;
    header ("location: admBranches.php?commited=yes");
endif;

if($table == 'locations'):
    // Get parameters from the URL     
    $locationCode = mysql_entities_fix_string((isset($_POST['locationCode']))?$_POST['locationCode']:$_GET['locationCode']);
    $locationDetails = mysql_entities_fix_string((isset($_POST['locationDetails']))?$_POST['locationDetails']:$_GET['locationDetails']);
    
    // Add location
    if($action == 'add'):
        /* Handles errors and send it back to the user cancelling the operation,
        *  instantiates the class and execute the operation requested, once it 
        *  has done send the appropriated message back to the user  
        */  
        if(isEmpty($branchId))
            array_push($errorMessages,"Branch is required.");         
        if(isEmpty($locationCode))
            array_push($errorMessages,"Location Code is required."); 
        
        $location = new Locations($locationCode,'',$branchId,$organizationId);
        $result = $location->selectLocation();  
        if(count($result) > 0 ):
            array_push($errorMessages,"Location Code already exists in this branch."); 
        endif; 
        if(count($errorMessages) > 0 ):
            $errorMessages = implode('*', $errorMessages);
            header ("location: admBoardLocations.php?errorMessages=$errorMessages");
            exit();
        endif;          
        $location = new Locations($locationCode,$locationDetails,$branchId,$organizationId);
        $location->Insert();	
    endif;
    
    // Update location information
    if($action == 'upd'):
        $location = new Locations($locationCode,$locationDetails,$branchId,$organizationId);
        $location->Update();
    endif;
    
    // Delete location
    if($action == 'del'):
        $location = new Locations($locationCode,'',$branchId,$organizationId);
        $location->Delete();

        $query = "DELETE FROM userschedules
                    WHERE organizationId = $organizationId
                    AND branchId = $branchId
                    AND locationCode = '$locationCode'";
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: DELETE query failed.");        
    endif;
    
    // Populate locations dropbox
    if($action == 'populate'):
        $branchId = mysql_entities_fix_string($_POST['branchId']);
        $query = "SELECT locationCode 
                    FROM boardLocations 
                    WHERE locationBranchId = $branchId
                    AND locationOrganizationId = $organizationId";
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $locationsResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        for ($i=0;$i<count($locationsResult);$i++):
            echo "<option value=".$locationsResult[$i]['locationCode'].">".$locationsResult[$i]['locationCode']."</option>";    
        endfor;    
    endif;   
    header ("location: admBoardLocations.php?commited=yes");
endif;

/* This block handle all the requests for users table according to the
*  action which also is sent via URL. When the requested is finished the user 
*  has the appropriated message   
*/ 
if($table == 'users'):
    // Get parameters from the URL 
    $firstName = mysql_entities_fix_string((isset($_POST['firstName']))?$_POST['firstName']:$_GET['firstName']);
    $lastName = mysql_entities_fix_string((isset($_POST['lastName']))?$_POST['lastName']:$_GET['lastName']);   
    $email = mysql_entities_fix_string((isset($_POST['email']))?$_POST['email']:$_GET['email']);     
    $password = mysql_entities_fix_string((isset($_POST['password']))?$_POST['password']:$_GET['password']); 
    $newPassword = mysql_entities_fix_string((isset($_POST['newPassword']))?$_POST['newPassword']:$_GET['newPassword']); 
    $confirmPassword = mysql_entities_fix_string((isset($_POST['confirmPassword']))?$_POST['confirmPassword']:$_GET['confirmPassword']); 
    $isAdministrator = mysql_entities_fix_string((isset($_POST['isAdministrator']))?$_POST['isAdministrator']:$_GET['isAdministrator']); 
    if($action == 'add'):       
        // Get parameters from the URL 
        $userName = mysql_entities_fix_string((isset($_POST['userName']))?$_POST['userName']:$_GET['userName']);
        if(isEmpty($userName))
            array_push($errorMessages,"Username is required.");    
        $user = new Users($userName,'','','','','',$organizationId);
        $result = $user->selectUser();
        if(count($result) > 0 ):
            array_push($errorMessages,"Username already exists."); 
        endif; 
        if(isEmpty($firstName))
            array_push($errorMessages,"First Name is required."); 
        if(isEmpty($lastName))
            array_push($errorMessages,"Last Name is required.");  
        if(isEmpty($email))
            array_push($errorMessages,"E-mail is required.");        
        if(isEmpty($password))
            array_push($errorMessages,"Password is required.");        
        if(count($errorMessages) > 0 ):
            $errorMessages = implode('*', $errorMessages);
            header ("location: admUsers.php?errorMessages=$errorMessages"); 
            exit();
        endif;         
	$user = new Users($userName,$firstName,$lastName,$email,$password,$isAdministrator,$organizationId);
	$user->Insert();	
    endif;
    /* This block handle all the requests for users table according to the
    *  action which also is sent via URL. When the requested is finished the user 
    *  has the appropriated message   
    */ 
    // Updates password
    if($action == 'upd'):
        $userName = mysql_entities_fix_string((isset($_POST['userName']))?$_POST['userName']:$_GET['userName']);
        if(isEmpty($firstName))
            array_push($errorMessages,"First Name is required."); 
        if(isEmpty($lastName))
            array_push($errorMessages,"Last Name is required.");  
        if(isEmpty($email))
            array_push($errorMessages,"E-mail is required.");        
        if(isEmpty($password))
            array_push($errorMessages,"Password is required.");        
        if(count($errorMessages) > 0 ):
            $errorMessages = implode('*', $errorMessages);
            header ("location: admUsers.php?errorMessages=$errorMessages"); 
            exit();
        endif;      
        $user = new Users($userName,$firstName,$lastName,$email,$password,$isAdministrator,$organizationId);
	$user->Update();
    endif;
    
    // Updates password
    if($action == 'updPassword'):
        // Handles errors and send it back to the user cancelling the operation
        if(isEmpty($newPassword))
            array_push($errorMessages,"New Password is required."); 
        if(isEmpty($confirmPassword))
            array_push($errorMessages,"Password Confirmation is required.");  
        if($newPassword != $confirmPassword)
            array_push($errorMessages,"Password and Confirmation must be the same.");        
        if(count($errorMessages) > 0 ):
            $errorMessages = implode('*', $errorMessages);
            header ("location: userPassword.php?errorMessages=$errorMessages"); 
            exit();
        endif;        
        $user = new Users($userName,'','','',$newPassword,'',$organizationId);
	$user->updateUserPassword();
    endif;
    if($action == 'resetPassword'):  
        $userName = mysql_entities_fix_string((isset($_POST['userName']))?$_POST['userName']:$_GET['userName']);
        $user = new Users($userName,'','','',$userName.'123','',$organizationId);
	$user->updateUserPassword();
    endif;
    if($action == 'del'):  
        $userName = mysql_entities_fix_string((isset($_POST['userName']))?$_POST['userName']:$_GET['userName']);
        $user = new Users($userName,'','','','','',$organizationId);
        $user->Delete();
    endif;
    if($action == 'updPassword'):
        header ("location: userPassword.php?commited=yes");   
    else:
        header ("location: admUsers.php?commited=yes");   
    endif;
endif;

/* This block handle all the requests for userschedules table according to the
*  action which also is sent via URL. When the requested is finished the user 
*  has the appropriated message   
*/ 
if($table == 'userschedules'):
    // Add schedule 
    if($action == 'add'):
        $makingOption = mysql_entities_fix_string($_POST['makingOption']);
        if($makingOption != "anyDateAndTime"):
            // Get parameters from the URL 
            $fromDate = strtotime($_POST['txt_fromDate']);
            $toDate = strtotime($_POST['txt_toDate']);
            $startingHH = str_pad($_POST['txt_startingHH'],2,0,STR_PAD_LEFT);
            $startingMM = str_pad($_POST['txt_startingMM'],2,0,STR_PAD_LEFT);
            $finishingHH = str_pad($_POST['txt_finishingHH'],2,0,STR_PAD_LEFT);
            $finishingMM = str_pad($_POST['txt_finishingMM'],2,0,STR_PAD_LEFT);
            $locationCode = $_POST['txt_locationCode'];
            $checkDays = (isset($_POST['checkDays']))?$_POST['checkDays']:'';
            $startingTime = $startingHH  .":". $startingMM;
            $finishingTime = $finishingHH .":". $finishingMM;
        endif;

        // Creates Monday to friday schedule
        if($makingOption == "MonFri"):             
            $query = "INSERT INTO userschedules (userName, organizationId, branchId, locationCode, date, startingTime, signedIn, finishingTime, signedOut, status) VALUES ";
            $values = "";
            $date = $fromDate;
            $comma = false; 
            while($date<=$toDate):
                if((date("D",$date) != 'Sat') && (date("D",$date) != 'Sun')):
                    $checkQuery = "SELECT * FROM userschedules
                        WHERE userName = '$userName'
                        AND organizationId = $organizationId
                        AND branchId = $branchId
                        AND locationCode = '$locationCode'
                        AND date = '".date("Y-m-d", $date)."'
                        AND startingTime = '$startingTime'";
                    $executeQuery = $db->prepare($checkQuery);
                    $executeQuery->execute() or exit("Error: INSERT query failed."); 
                    $scheduleResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);   
                    if(count($scheduleResult) <= 0 ):
                        $values .= ($comma)?',':'';
                        $values .= "('$userName',$organizationId,$branchId,'$locationCode','".date("Y-m-d", $date)."','$startingTime','00:00','$finishingTime','00:00','')";
                        $comma = true;
                    endif;                     
                endif;
                $date = strtotime(date("Y-m-d", $date) . "+1 day");
            endwhile;
            $dateFrom = date("Y-m-d", $fromDate);
            $dateTo = date("Y-m-d", $toDate);            
            if($values != ""):
                if($action == 'add'):
                    $executeQuery = $db->prepare($query.$values);
                    $executeQuery->execute() or exit("Error: INSERT query failed.");
                endif; 
                header ("location: userScheduleMaker.php?fromDate=$dateFrom&toDate=$dateTo&commited=yes");
            else:
                array_push($errorMessages,"There is no selected days between the dates".date("m/d/Y",$dateFrom)." and ".date("m/d/Y",$dateTo)." or duplicated schedule was found."); 
                $errorMessages = implode('*', $errorMessages);
                header ("location: userScheduleMaker.php?fromDate=$dateFrom&toDate=$dateTo&errorMessages=$errorMessages");
            endif;    
        endif;
        
        // Creates selected days schedule
        if($makingOption == "days"):             
            $query = "INSERT INTO userschedules (userName, organizationId, branchId, locationCode, date, startingTime, signedIn, finishingTime, signedOut, status) VALUES ";
            $values = "";
            $date = $fromDate;
            $comma = false; 
            while($date<=$toDate):
                if(in_array(date("D",$date), $checkDays)):
                    $checkQuery = "SELECT * FROM userschedules
                        WHERE userName = '$userName'
                        AND organizationId = $organizationId
                        AND branchId = $branchId
                        AND locationCode = '$locationCode'
                        AND date = '".date("Y-m-d", $date)."'
                        AND startingTime = '$startingTime'";
                    $executeQuery = $db->prepare($checkQuery);
                    $executeQuery->execute() or exit("Error: INSERT query failed."); 
                    $scheduleResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);   
                    if(count($scheduleResult) <= 0 ):
                        $values .= ($comma)?',':'';
                        $values .= "('$userName',$organizationId,$branchId,'$locationCode','".date("Y-m-d", $date)."','$startingTime','00:00','$finishingTime','00:00','')";
                        $comma = true;
                    endif;                     
                endif;
                $date = strtotime(date("Y-m-d", $date) . "+1 day");
            endwhile;
            $dateFrom = date("Y-m-d", $fromDate);
            $dateTo = date("Y-m-d", $toDate);            
            if($values != ""):
                if($action == 'add'):
                    $executeQuery = $db->prepare($query.$values);
                    $executeQuery->execute() or exit("Error: INSERT query failed.");
                endif; 
                header ("location: userScheduleMaker.php?fromDate=$dateFrom&toDate=$dateTo&commited=yes");
            else:
                array_push($errorMessages,"There is no selected days between the dates".date("m/d/Y",$dateFrom)." and ".date("m/d/Y",$dateTo)." or duplicated schedule was found."); 
                $errorMessages = implode('*', $errorMessages);
                header ("location: userScheduleMaker.php?fromDate=$dateFrom&toDate=$dateTo&errorMessages=$errorMessages");
            endif;    
        endif;        
         
        // Creates general schedule (for further use)
        if($makingOption == "anyDateAndTime"):
            // Get parameters from the URL 
            $locationCode = $_POST['txt_locationCode'];
            $date = date('Y-m-d',strtotime($_POST['txt_date']));
            $startingHH = str_pad($_POST['txt_startingHH'],2,0,STR_PAD_LEFT);
            $startingMM = str_pad($_POST['txt_startingMM'],2,0,STR_PAD_LEFT);
            $finishingHH = str_pad($_POST['txt_finishingHH'],2,0,STR_PAD_LEFT);
            $finishingMM = str_pad($_POST['txt_finishingMM'],2,0,STR_PAD_LEFT);
            $startingTime = $startingHH  .":". $startingMM;
            $finishingTime = $finishingHH .":". $finishingMM;
            
            $checkQuery = "SELECT * FROM userschedules
                WHERE userName = '$userName'
                AND organizationId = $organizationId
                AND branchId = $branchId
                AND locationCode = '$locationCode'
                AND date = '$date'
                AND startingTime = '$startingTime'";
            $executeQuery = $db->prepare($checkQuery);
            $executeQuery->execute() or exit("Error: INSERT query failed."); 
            $scheduleResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);   
            if(count($scheduleResult) <= 0 ):
                $query = "INSERT INTO userschedules (userName, organizationId, branchId, locationCode, date, startingTime, signedIn, finishingTime, signedOut, status)
                            VALUES ('$userName',$organizationId,$branchId,'$locationCode','$date','$startingTime','00:00:00','$finishingTime','00:00:00','')";
                $executeQuery = $db->prepare($query);
                $executeQuery->execute() or exit("Error: INSERT query failed.");
                header ("location: userMySchedule.php?commited=yes"); 
            else:
                array_push($errorMessages,"The day schedule already exists."); 
                $errorMessages = implode('*', $errorMessages);
                header ("location: userMySchedule.php?errorMessages=$errorMessages");
            endif;            
        endif;
    endif;

    // Updates a day schedule
    if($action == 'upd'):
        // Get parameters from the URL 
        $locationCode = $_POST['txt_locationCode'];
        $date = date('Y-m-d',strtotime($_POST['txt_date']));
        $time = date("H:i:s");
        $referenceId = $_POST['referenceId'];
        $startingHH = str_pad($_POST['txt_startingHH'],2,0,STR_PAD_LEFT);
        $startingMM = str_pad($_POST['txt_startingMM'],2,0,STR_PAD_LEFT);
        $finishingHH = str_pad($_POST['txt_finishingHH'],2,0,STR_PAD_LEFT);
        $finishingMM = str_pad($_POST['txt_finishingMM'],2,0,STR_PAD_LEFT);
        $startingTime = $startingHH  .":". $startingMM;
        $finishingTime = $finishingHH .":". $finishingMM;

        $checkQuery = "SELECT * FROM userschedules
            WHERE userName = '$userName'
            AND organizationId = $organizationId
            AND branchId = $branchId
            AND locationCode = '$locationCode'
            AND date = '$date'
            AND startingTime = '$startingTime'";
        $executeQuery = $db->prepare($checkQuery);
        $executeQuery->execute() or exit("Error: INSERT query failed."); 
        $scheduleResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);   
        if(count($scheduleResult) <= 0 ):
            $query = "UPDATE userschedules
                        SET date = '$date',
                        branchId = $branchId,
                        locationCode = '$locationCode',
                        startingTime = '$startingTime',
                        finishingTime = '$finishingTime'
                        WHERE  referenceId = $referenceId";
            $executeQuery = $db->prepare($query);
            $executeQuery->execute() or exit("Error: UPDATE query failed.");
            header ("location: userMySchedule.php?commited=yes");
        else:
            array_push($errorMessages,"The day schedule already exists."); 
            $errorMessages = implode('*', $errorMessages);
            header ("location: userMySchedule.php?errorMessages=$errorMessages");
        endif; 
    endif;     

    // Deletes a day schedule
    if($action == 'del'):
        // Get parameters from the URL 
        $referenceId = (isset($_POST['referenceId']))?$_POST['referenceId']:$_GET['referenceId'];
        $query = "DELETE FROM userschedules
                    WHERE organizationId = $organizationId
                    AND referenceId = $referenceId";
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: DELETE query failed.");
        header ("location: userMySchedule.php?commited=yes");
    endif; 
    
    // Register Sign In 
    if($action == 'signIn'):
        // Get parameters from the URL 
        $locationCode = $_POST['txt_locationCode'];
        $time = $_POST['txt_time'];
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
        header ("location: userSignInOut.php?commited=yes");
    endif;   
    
    // Register Sign Out
    if($action == 'signOut'):
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
        header ("location: userSignInOut.php?commited=yes");
    endif;       
endif;
?>