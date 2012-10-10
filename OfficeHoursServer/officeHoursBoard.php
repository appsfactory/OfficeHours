<!-- officeHoursBoard.php
        PROG8105-1 Systems Project: Displays people schedules

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.02.05: Created
-->
<?
// Includes connection and organizations class
require_once("includes/dbConnection.php");
require_once("classes/organizations.class.php");

// Get all the parameters from the URL 
$organizationId = $_GET['organizationId'];
$branchId = $_GET['branchId'];
$locationCode = $_GET['locationCode'];

// instantiates organizations class
$organization = new Organizations($organizationId,'','','','','','','','','','','','');
$resultOrganization = $organization->selectSettings();

// Initialize varables 
$fontHeadingColor = $resultOrganization[0]["fontHeadingColor"];
$backgroundHeadingColor = $resultOrganization[0]["backgroundHeadingColor"];
$fontGridOddColor = $resultOrganization[0]["fontGridOddColor"];
$backgroundGridOddColor = $resultOrganization[0]["backgroundGridOddColor"];
$fontGridEvenColor = $resultOrganization[0]["fontGridEvenColor"];
$backgroundGridEvenColor = $resultOrganization[0]["backgroundGridEvenColor"];
$fontBottomColor = $resultOrganization[0]["fontBottomColor"];
$backgroundBottomColor = $resultOrganization[0]["backgroundBottomColor"];

// Loads the stylesheet for the board screen
$cssFile = 'screen1920Resolution.css';

// Gets the todays date 
$today = date('m/d/Y');
$dateSearch = date('Y-m-d');

// Get parameters from the URL to control the refresh 
$nextPage = isset($_GET['nextPage'])?$_GET['nextPage']:0;
$totalRecords = isset($_GET['totalRecords'])?$_GET['totalRecords']:0;
$linesPerPage = 10;
$secondsToRefresh = 1;

if($nextPage == 0):
    $query = "SELECT COUNT(*)
                FROM userschedules
                WHERE date = '$dateSearch'
                AND organizationId = $organizationId
                AND branchId LIKE '$branchId' 
                AND locationCode LIKE '$locationCode' 
                ORDER BY startingTime";
    $executeQuery = $db->prepare($query);
    $executeQuery->execute() or exit("Error: SELECT query failed.");
    $totalRecords = $executeQuery->fetchColumn(0);
endif;

// Controls the users that will show up on the screen 
if($totalRecords == 0):
   $numberOfPages = 0;
   $linesToShow = 0;
else:
   $numberOfPages = ceil($totalRecords/$linesPerPage);
   if($totalRecords <= $linesPerPage):
        $numberOfPages = 1;
        $nextPage = 0;
        $nextRecord = 0;
    else:
        $nextRecord = ($nextPage*$linesPerPage);
        $nextPage++;
    endif;
endif;

// If there is any user to be shown, selects all schedule information   
if($totalRecords > 0):
    $query = "SELECT U.firstName AS firstName, U.lastName AS lastName, B.branchName AS branchName, S.*
                FROM users AS U,userschedules AS S, branches AS B
                WHERE U.userName = S.userName
                AND S.organizationId = $organizationId
                AND S.branchId LIKE '$branchId'
                AND S.locationCode LIKE '$locationCode'
                AND S.date = '$dateSearch'
                AND S.branchId = B.branchId 
                ORDER BY S.startingTime LIMIT $nextRecord,$linesPerPage";
    $executeQuery = $db->prepare($query);
    $executeQuery->execute() or exit("Error: SELECT query failed.");
    $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
    $numberLines = count($result);
else:
    $numberLines = 0;
endif;

//UPDATE RECURRING RECORDS//--------------------------------------------------
//Get an array of each unique username and loop through
$userQuery = "SELECT DISTINCT userName FROM userschedules";
$executeQuery = $db -> prepare($userQuery);
$executeQuery->execute() or exit ("Error: SELECT Needed Updates Query Failed.");
$userResult = $executeQuery->fetchAll(PDO::FETCH_COLUMN, 0);
$numberUserLines = count($userResult);
for ($i = 0; $i < $numberUserLines; $i++){
	//find entries which are over a week old and still set to 'recurring'
	$selectQuery = "SELECT * FROM userschedules
					WHERE cast('".date('Y-m-d', strtotime('monday this week'))."' as date) > date AND status = 'y' AND userName = '$userResult[$i]';";
	$executeQuery = $db -> prepare($selectQuery);
	$executeQuery->execute() or exit ("Error: SELECT Needed Updates Query Failed.");
	$selectResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);
	$numberSelectLines = count($selectResult);
	//Update the recurring status of all the old entries to not recur
	$updateQuery = "UPDATE userschedules SET status = ' ' WHERE cast('".date('Y-m-d', strtotime('monday this week'))."' as date) > date AND status = 'y' AND userName = '$userResult[$i]';";
	$executeQuery = $db->prepare($updateQuery);
	$executeQuery ->execute() or exit("Error: UPDATE old recurring query failed.");
	for ($i = 0; $i < $numberSelectLines; $i++){
		//Check if there are more up-to-date recurring entries sent from phone 
		$query = "SELECT userName FROM userschedules WHERE date = DATE_ADD(".$selectResult[$i]['date'].", INTERVAL 7 DAY) and status = 'y' AND userName = '$userResult[$i]';";
		$executeQuery = $db -> prepare($query);
		$executeQuery->execute() or exit ("Error: SELECT Skippable Updates Query Failed.");
		$checkResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);
		//If not, create a new entry for the recurring schedule
		if (count($checkResult) == 0){
			$query = "INSERT INTO userschedules (userName, organizationId, branchId, locationCode, date, startingTime, signedIn, finishingTime, signedOut, status) VALUES
					('".$selectResult[$i]['userName']."', $organizationId, $branchId,'$locationCode', DATE_ADD('".$selectResult[$i]['date']."', INTERVAL 7 DAY),'". $selectResult[$i]['startingTime']."', 
					'00:00','".$selectResult[$i]['finishingTime']."','00:00', 'y');";
			$executeQuery = $db -> prepare($query);
			$executeQuery->execute() or exit ("Error: INSERT Updated Recurring Query Failed.");
		}
	}
}
/////---------------------------------------------------------------------------



if($nextPage == $numberOfPages) $nextPage = 0;
$parameters = "nextPage=$nextPage&totalRecords=$totalRecords&organizationId=$organizationId&branchId=$branchId&locationCode=$locationCode";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
    <meta http-equiv="Refresh" content="<?=$secondsToRefresh?>;URL=officeHoursBoard.php?<?=$parameters?>"></meta>
    <link href="css/<?=$cssFile?>" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />     
    <script type="text/javascript" src="js/jquery-1.4.4.min.js" ></script>
    <script type="text/javascript" src="js/functions.js" ></script>
    <noscript>
        <meta http-equiv="Refresh" content="1;url=officeHoursBoard.php?<?=$parameters?>&javascript=no"/>
    </noscript>   
    <title>Office Hours Board</title>
    <style type="text/css">
        /* Gets from the organization color settings to show on the board */
        #boardSchedule thead{
            color: <?=$fontHeadingColor?>;
            background-color: <?=$backgroundHeadingColor?>;
        }

        .oddRow{
            color: <?=$fontGridOddColor?>;
            background-color: <?=$backgroundGridOddColor?>;
        }

        .evenRow{
            color: <?=$fontGridEvenColor?>;
            background-color: <?=$backgroundGridEvenColor?>;
        }

        #boardFooterDiv
        {
            color: <?=$fontBottomColor?>;
            background-color: <?=$backgroundBottomColor?>;
        }
    </style>  
</head>
<body onload="LoadFadeInBody()" onunload="LoadFadeOutBody()">
<div id="boardMasterDiv">

    <div id="boardHeaderDiv">
        <div id="boardLogoDiv"></div>
        <div id="boardTitleDiv">
            <label>Office Hours Board</label>
        </div>
    </div>                       
    <div id="errorMessage">
        <?
        // This PHP block checks if JavaSript is activated
        echo "<script>$('#errorMessage').css('display','none');</script>";
        $javascript = (isset($_GET['javascript']))?$_GET['javascript']:'';
        if($javascript == 'no'):
            echo "<script>$('#errorMessage').css('display','block');</script>";
        ?>
            <div id="noScriptMessage">
                <strong>JavaScript is disabled.</strong><br/>
                The Office Hours Web site needs the JavaScript Enabled to fully work. 
                Please turn it on and <b>
                    <a href='officeHoursBoard.php?organizationId=<?=$organizationId?>&branchId=<?=$branchId?>&locationCode=<?=$locationCode?>&javascript=no'
                       style="color: yellow">reload the page.
                    </a></b>            
            </div>
        <?
            echo "<script>$('#boardMasterDiv').css('background-color','#FFFFFF');</script>";
        endif;
        ?>
    </div>               
    <div id="boardContentDiv">
        <table id="boardSchedule">
            <thead id="boardHeader">
                <td id="boardStatusIcon"></td>
                <td id="boardFirstName">Name</td>
                <td id="boardOfficeHour">Office Hours</td>
                <!--<td id="boardBranch">Branch</td>
                	<td id="boardLocation">Location</td>-->
            </thead>
            <tbody id="boardGrid">
                <?
                // Shows the users and respective schedule information
                $lastEntry = "";
                for ($i=0;$i<10;$i++):
                    if($i<$numberLines):
                        $timeNow = date('H:i:s');
                        $status = "red";
                        if(strtotime($result[$i]["signedIn"]) > strtotime($result[$i]["signedOut"])) $status = "green";
                        elseif($timenow <= $result[$i]["finishingTime"]) $status = "yellow";
                        elseif($timeNow < $result[$i]["startingTime"] && $result[$i]["firstName"] != $lastEntry) $status = "red";
                        //if($timeNow > $result[$i]["finishingTime"] && ($result[$i]["signedIn"] == "00:00:00" && $result[$i]["signedOut"] == "00:00:00")) $status = "red";
                        elseif($timeNow > $result[$i]["finishingTime"] && $result[$i]["firstName"] != $lastEntry) $status = "red";
                        //if(($timeNow >= $result[$i]["startingTime"] && $timeNow < $result[$i]["finishingTime"]) && $result[$i]["signedIn"] == "00:00:00") $status = "yellow";
                        //if($timeNow > $result[$i]["finishingTime"] && $result[$i]["signedIn"] != "00:00:00" && $result[$i]["signedOut"] == "00:00:00") $status = "yellow";
                       
                        echo "<tr id='boardGridRow'>";
                        switch($status):
                            case 'red':
                                $image = "images/redLight.png";
                                break;
                            case 'green':
                                $image = "images/greenLight.png";
                                break;
                            case 'yellow':
                                $image = "images/yellowLight.png";
                                break;
                            default:
                                $image = "images/redLight.png";
                        endswitch;
                        if ($lastEntry != $result[$i]["firstName"]) {
                        echo "<td align='center'> <img src='".$image."'/> </td>";
                        echo "<td align='center'>"."  ".$result[$i]["firstName"]." ".$result[$i]["lastName"]."</td>";
                        } else {
                        	echo "<td align='center'></td>";
                        	echo "<td align='center'></td>";
                        	                        	 
                        }
                        echo "<td align='center'>".substr($result[$i]["startingTime"],0,5)." - ".substr($result[$i]["finishingTime"],0,5)."</td>";
                        //echo "<td align='center'>".$result[$i]["branchName"]."</td>";
                        //echo "<td align='center'>".$result[$i]["locationCode"]."</td>";
                        echo "</tr>";
                    else:
                        echo "<tr id='boardGridRow'><td colspan='7'></td></tr>";    
                    endif;
                    $lastEntry = $result[$i]["firstName"];
                endfor;
                ?>
             </tbody>
        </table>    
    </div>
    <div id="boardFooterDiv">
        <div id="legendDiv">
            <label><img src="images/greenLight.png"/> In Office.</label><br/>
            <label><img src="images/yellowLight.png"/> Away.</label><br/>
            <label><img src="images/redLight.png"/> Not Currently Scheduled.</label>
        </div>
        <div id="clockDiv">
            <label id="hours"></label>
            <label id="point">:</label>
            <label id="min"></label>
        </div>
        <div id='widgetDiv'>
            <!--<script src='http://netweather.accuweather.com/adcbin/netweather_v2/netweatherV2ex.asp?partner=netweather&tStyle=whteYell&logo=0&zipcode=NAM|CA|ON|CAMBRIDGE|&lang=eng&size=8&theme=blue&metric=1&target=_self'></script>-->
        </div>
        <div id="todaysDateDiv"><?=date('D M j\, Y')?></div>
    </div>
</div>
</body>
</html>

