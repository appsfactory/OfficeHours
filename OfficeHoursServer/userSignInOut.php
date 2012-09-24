<!-- userSignInOut.php
        PROG8105-1 Systems Project: Signs In and Out

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.02.17: Created
-->
<?
//// Checks if the user logged in the application
//$organizationId = (isset($_GET['organizationId']))?$_GET['organizationId']:'';
//$userName = (isset($_GET['userName']))?$_GET['userName']:'';
//if($organizationId == "" || $userName == ""):
//   echo "<h3>You need to log in to accesss this page, please <a href='index.php'>click here to log in</a>.<h3>";
//   exit();
//endif;
session_start('officeHours');
$organizationId = isset($_SESSION['organizationId'])?$_SESSION['organizationId']:'';
$userName = isset($_SESSION['userName'])?$_SESSION['userName']:'';
if($organizationId == "" || $userName == ""):
    echo "<h3>You need to log in to accesss this page, please <a href='index.php'>click here to log in</a>.<h3>";
    exit();
endif;

// Gets all the parameters from the URL 
require_once("includes/dbConnection.php");
$date = date('Y').'-'.date('m').'-'.date('d');
$action = (isset($_GET['action']))?$_GET['action']:'';
$branchId = (isset($_GET['branchId']))?$_GET['branchId']:'';

// Selects the users schedule 
$query = "SELECT B.branchName, S.*
    FROM branches AS B, userschedules AS S  
    WHERE S.organizationId = $organizationId
    AND B.organizationId = S.organizationId
    AND B.branchId = S.branchId
    AND S.userName = '$userName'
    AND S.date = '$date'
    ORDER BY S.date, S.branchId, S.locationCode";
$stm = $db->prepare($query);
$stm->execute();
$userScheduleResult = $stm->fetchAll(PDO::FETCH_BOTH);

// Initializes all variables if action is different of signIn or signOut
if($action == 'signIn' || $action == 'signOut'):
    $branchId = $_GET['branchId'];
    $branchName = $_GET['branchName'];
    $locationCode = $_GET['locationCode'];
    $startingTime = (isset($_GET['startingTime']))?$_GET['startingTime']:'';
    $finishingTime = (isset($_GET['finishingTime']))?$_GET['finishingTime']:'';
else:
    $branchId = '';
    $branchName = '';
    $locationCode = '';
    $startingTime = '';
    $finishingTime = '';
endif;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>User Control Panel</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="css/userControlPanel.css" type="text/css" media="all" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />     
    <script type="text/javascript" src="js/jquery-1.4.4.min.js" ></script>
    <script type="text/javascript" src="js/functions.js"></script> 
    <noscript>
        <meta http-equiv="Refresh" content="1;url=userSignInOut.php?organizationId=<?=$organizationId?>&userName=<?=$userName?>&javascript=no"/> 
    </noscript>    
</head>
<body onunload="showSignInOutDetails(false)">
<div id="mainDiv">
    <div id="topDiv">
        <div id="headerDiv">
            <span>OFFICE <img src="images/boardImage.png" 
                              style="vertical-align:middle;"/> HOURS</span>
        </div>
        <div id="sitePanelNameDiv">
            <span>User Control Panel</span>                
        </div>
    </div>
    <span style="margin-left: 10px">
        Organization: <b><?=$_SESSION['organizationName']?></b> - User: <b><?=$userName?></b>
    </span>
    <div id="menuDiv">
        <ul style="margin-left: 5px;">
            <li><img src="images/menuDivider.gif" 
                     style="vertical-align:middle;"/></li>   
            <a href="userControlPanel.php" 
               target="_self"><li rel="Home page">
                    <img src="images/ico_home.png" 
                         style="vertical-align:middle;"/> Home</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="userScheduleMaker.php" 
               target="_self"><li rel="Create schedule">
                    <img src="images/ico_makeSchedule.png" 
                         style="vertical-align:middle;"/> Schedule Maker</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="userMySchedule.php" 
               target="_self"><li rel="Create, Update and Delete Schedule">
                    <img src="images/ico_schedule.png" 
                         style="vertical-align:middle;"/> My Schedule</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <li id="currentPage" rel="Sign In/Out">
                <img src="images/ico_signIn.png" 
                     style="vertical-align:middle;"/> Sign In/Out</li>
            <li><img src="images/menuDivider.gif"/></li>                  
            <a href="userPassword.php" 
               target="_self"><li rel="Change password">
                    <img src="images/ico_password.png" 
                         style="vertical-align:middle;"/> Change Password</li></a>            
            <li><img src="images/menuDivider.gif"/></li>
            <a href="help/officeHoursHelp.html" 
               target="_new"><li rel="Web site Help">
                    <img src="images/ico_help.png" 
                         style="vertical-align:middle;"/> Help</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="index.php" target="_self">
                <li rel="Log Out">
                    <img src="images/ico_logout.png" 
                         style="vertical-align:middle;"/> Log Out</li></a>
            <li><img src="images/menuDivider.gif"/></li>                 
        </ul>
    </div>
     <div id="errorMessage">
        <?
        /* This PHP block checks if JavaSript is activated and also
        *  checks for erros or susscess messages and displays accordling.  
        */         
        echo "<script>$('#errorMessage').css('display','none');</script>";
        $javascript = (isset($_GET['javascript']))?$_GET['javascript']:'';
        if($javascript == 'no'):
            echo "<script>$('#errorMessage').css('display','block');</script>";
        ?>
            <div id="noScriptMessage">
                <strong>JavaScript is disabled.</strong><br/>
                The Office Hours Web site needs the JavaScript Enabled to fully work. 
                Please turn it on and <b>
                    <a href='userSignInOut.php?organizationId=<?=$organizationId?>&userName=<?=$userName?>'
                       style="color: yellow">reload the page.
                    </a></b>            
            </div>
        <?
        endif;
        $errorMessages = (isset($_GET['errorMessages']))?$_GET['errorMessages']:'';
        if($errorMessages != ''):
            $errorMessages = explode('*', $errorMessages);
            echo "<p style='text-align:center;'><b>ERRORS FOUND</b></p>";
            echo "<p>";
            echo "<br/>";
            for($i=0;$i<count($errorMessages);$i++):
                echo "&bull; $errorMessages[$i]<br/>";
            endfor;
            echo "</p>";
            echo "<br/>";
            echo "<p style='text-align:center;'><input type='button' id='errorButton' value='Close'></p>";
            echo "<script>$('#errorMessage').css('display','block');</script>";
        endif;
        $commited  = (isset($_GET['commited']))?$_GET['commited']:'';
        if($commited == 'yes'):            
        echo "<b>Operation executed successfully</b>";
        //echo "<span style='float:right'><input type='button' id='errorButton' value='Close'></span>";
        echo "<script>";
        echo "$('#errorMessage').css('text-align','center');";
        echo "$('#errorMessage').css('padding-top','4px');";
        echo "$('#errorMessage').css('background-color','green');";              
        echo "$('#errorMessage').css('width','980px');";    
        echo "$('#errorMessage').css('height','16px');";              
        echo "$('#errorMessage').css('position','absolute');";  
        echo "$('#errorMessage').css('top','210px');";  
        echo "$('#errorMessage').css('z-index','5');";  
        echo "$('#errorMessage').css('display','block').fadeOut(3000)";   
        echo "</script>";              
        endif;            
        ?>
    </div>         
    <div id="middleDiv">
       <div id="scheduleTableDiv">
            <table id="scheduleTable" width="900px" 
                   align="center" cellspacing="0" border="1">
                <tr id="scheduleTableHeader" height="25">
                    <th width="8%">Status</th>
                    <th width="10%">Date</th>
                    <th width="15%">Branch</th>
                    <th width="17%">Location</th>
                    <th width="10%">Starting Time</th>
                    <th width="10%">Signed In</th>
                    <th width="10%">Finishing Time</th>
                    <th width="10%">Signed Out</th>
                    <th width="10%">Actions</th>
                </tr>
                <tbody width="900px" id="scheduleTableGrid">
                <?
                for ($i=0;$i<count($userScheduleResult);$i++):
                ?>
                    <tr id="scheduleTableRow" height="25">
                        <?
                        // Shows in the table the user schedule
                        $timeNow = date('H:i:s');
                        $status = "red";
                        if($timeNow < $userScheduleResult[$i]["startingTime"]) $status = "red";
                        if($timeNow > $userScheduleResult[$i]["finishingTime"] && ($userScheduleResult[$i]["signedIn"] == "00:00:00" && $userScheduleResult[$i]["signedIn"] == "00:00:00")) $status = "red";
                        if(($timeNow >= $userScheduleResult[$i]["startingTime"] && $timeNow < $userScheduleResult[$i]["finishingTime"]) && $userScheduleResult[$i]["signedIn"] == "00:00:00") $status = "yellow";
                        if($timeNow > $userScheduleResult[$i]["finishingTime"] && $userScheduleResult[$i]["signedIn"] != "00:00:00" && $userScheduleResult[$i]["signedOut"] == "00:00:00") $status = "yellow";
                        if($timeNow <= $userScheduleResult[$i]["finishingTime"] && $userScheduleResult[$i]["signedIn"] != "00:00:00") $status = "green";

                        // Shows the colored light according to the status
                        switch($status):
                            case 'red':
                                $image = "images/ico_redlightSmall.png";
                                break;
                            case 'green':
                                $image = "images/ico_greenlightSmall.png";
                                break;
                            case 'yellow':
                                $image = "images/ico_yellowlightSmall.png";
                                break;
                        endswitch;
                        echo "<td align='center'> <img border='0' src='".$image."'/> </td>";
                        ?>
                        <td align="center"><?=date('m/d/Y',strtotime($userScheduleResult[$i]["date"]))?></td>
                        <td align="left"><?=$userScheduleResult[$i]["branchName"]?></td>
                        <td align="left"><?=$userScheduleResult[$i]["locationCode"]?></td>
                        <td align="center"><?=substr($userScheduleResult[$i]["startingTime"],0,5)?></td>
                        <td align="center"><b><?=($userScheduleResult[$i]["signedIn"] == '00:00:00')?'-':substr($userScheduleResult[$i]["signedIn"],0,5)?></b></td>
                        <td align="center"><?=substr($userScheduleResult[$i]["finishingTime"],0,5)?></td>
                        <td align="center"><b><?=($userScheduleResult[$i]["signedOut"] == '00:00:00')?'-':substr($userScheduleResult[$i]["signedOut"],0,5)?></b></td>    
                        <td align="center">
                            <?
                            // Disables/enables the sign In/Out icon
                            if($userScheduleResult[$i]["signedIn"] == '00:00:00'):
                                $parameter = "userSignInOut.php?branchId=".$userScheduleResult[$i]['branchId']
                                    ."&locationCode=".$userScheduleResult[$i]['locationCode']."&branchName=".$userScheduleResult[$i]['branchName']
                                    ."&startingTime=".$userScheduleResult[$i]['startingTime']."&action=signIn";
                                echo "<a href='".$parameter."'><img border='0' src='images/ico_in.png' rel='Sign In'/></a>";
                            else:
                                echo "<img border='0' src='images/ico_In_disabled.png'/>";
                            endif;
                            echo "&nbsp;&nbsp;";
                            if($userScheduleResult[$i]["signedOut"] == '00:00:00' && $userScheduleResult[$i]["signedIn"] != '00:00:00'):
                                $parameter = "userSignInOut.php?branchId=".$userScheduleResult[$i]['branchId']
                                    ."&locationCode=".$userScheduleResult[$i]['locationCode']."&branchName=".$userScheduleResult[$i]['branchName']
                                    ."&finishingTime=".$userScheduleResult[$i]['finishingTime']."&action=signOut";
                                echo "<a href='".$parameter."'><img border='0' src='images/ico_Out.png' rel='Sign Out'/></a>";                                    
                            else:
                                echo "<img border='0' src='images/ico_out_disabled.png'/>";
                            endif;
                            ?>                                
                        </td>                            
                    </tr>
                <?
                endfor;
                ?>
                <tr id="branchesTableRow" height="25">
                    <td colspan="9" align="center">
                        <img border="0" src="images/ico_greenlightSmall.png"
                             style="vertical-align: top;"/> In Office &nbsp;&bull;
                        <img border="0" src="images/ico_yellowlightSmall.png"
                             style="vertical-align: top;"/> Not Signed In/Out &nbsp;&bull;
                        <img border="0" src="images/ico_redlightSmall.png" 
                             style="vertical-align: top;"/> Not Currently Scheduled &nbsp;&bull;                            
                        <img border="0" src="images/ico_in.png"   
                             style="vertical-align: top;"/> Sign In &nbsp;&bull;
                        <img border="0" src="images/ico_out.png" 
                             style="vertical-align: top;"/> Sign Out
                    </td>
                </tr>
                <tr id="branchesTableRow" height="25">
                    <td colspan="9" align="center">
                        <b>Go to 
                        <a href="userMySchedule.php" 
                        target="_self" 
                        style="color:red;" 
                        rel="Create, Update and Delete Schedule" >My Schedule
                        </a> for changes.</b>
                    </td>
                </tr>                        
                </tbody>
            </table>
        </div>
        <div id="signInOutFormDiv">
            <div class="detailsFormHeaderDiv">SIGN <?=($action=='signIn')?'IN':'OUT'?></div>
            <form name="signInOutForm" id="signInOutForm" 
                  method="POST" action="admCRUD.php" enctype="multipart/form-data">
                <table id="detailsTable">
                    <tr>
                        <td align="right">Branch:</td>
                        <td><input id="branchName" 
                                   type="text" name="txt_branchName" 
                                   size="20" disabled value="<?=$branchName?>" 
                                   rel="Branch name"/></td>
                    </tr>
                    <tr>
                        <td align="right">Location:</td>
                        <td><input id="locationCode" type="text"
                                   name="txt_locationCode" size="50"
                                   maxlenth="50" disabled value="<?=$locationCode?>" 
                                   rel="Location code"/></td>
                    </tr>
                    <tr>
                        <td align="right"><?=($action=='signIn')?'Starting':'Finishing'?> Time:</td>
                        <td>
                            <input id="startingTime" type="text" name="txt_time" 
                                   size="5" maxlenth="5" disabled 
                                   value="<?=($action=='signIn')?substr($startingTime,0,5):substr($finishingTime,0,5)?>" 
                                   rel="<?=($action=='signIn')?'Starting ':'Finishing '?>time"/>
                        </td>               
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <?
                            if($action == 'signIn'):
                                echo "<input type='submit' value='&nbsp;Sign In&nbsp;' rel='Sign In'/>";
                            else:
                                echo "<input type='submit' value='&nbsp;Sign Out&nbsp;' rel='Sign Out'>";
                            endif;
                            ?>    
                            <input type='reset' value='Cancel' 
                                   onclick='showSignInOutDetails(false)' 
                                   rel='Cancel and close'/>
                        </td>    
                    </tr>
                </table>
                <input type="hidden" name="action" value="<?=$action?>" />
                <input type="hidden" name="table" value="userschedules" />
                <input type="hidden" name="branchId" value="<?=$branchId?>"/>
                <input type="hidden" name="txt_locationCode" value="<?=$locationCode?>"/>
                <input type="hidden" name="date" value="<?=$date?>"/>
                <input type="hidden" name="txt_time" 
                       value="<?=($action=='signIn')?substr($startingTime,0,5):substr($finishingTime,0,5)?>"/>                    
            </form>
        </div>            
        <? if($action != '') echo "<script>showSignInOutDetails(true)</script>"; ?>                         
    </div>
    <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
