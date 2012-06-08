<!-- userMySchedule.php
        PROG8105-1 Systems Project: Create, update and delete schedule

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.02.17: Created
-->
<?
//// Checks if the user logged in the application
//$organizationId = $_GET['organizationId'];
//$userName = $_GET['userName'];
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
$branchIdSelect = (isset($_GET['branchIdSelect']))?$_GET['branchIdSelect']:'%';
$locationCodeSelect = (isset($_GET['locationCodeSelect']))?$_GET['locationCodeSelect']:'%';
$fromDate = isset($_GET['fromDate'])?$_GET['fromDate']:date('m').'/'.date('d').'/'.date('Y');
$toDate = isset($_GET['toDate'])?$_GET['toDate']:date("m/d/Y", strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00')))); 
$action = (isset($_GET['action']))?$_GET['action']:'';

// Selects all onganization branches 
$query = "SELECT * FROM branches WHERE organizationId = $organizationId ORDER BY branchName";
$stm = $db->prepare($query);
$stm->execute();
$branchesResult = $stm->fetchAll(PDO::FETCH_BOTH);

// Selects all onganization locations
$query = "SELECT locationCode FROM boardlocations WHERE locationOrganizationId = $organizationId ORDER BY locationCode";
$stm = $db->prepare($query);
$stm->execute();
$boardLocationsResult = $stm->fetchAll(PDO::FETCH_BOTH);

// Selects the users schedule 
$query = "SELECT B.branchName, S.*
    FROM branches AS B, userschedules AS S  
    WHERE S.organizationId = $organizationId
    AND S.branchId LIKE '$branchIdSelect'
    AND S.locationCode LIKE '$locationCodeSelect'
    AND B.organizationId = S.organizationId
    AND B.branchId = S.branchId
    AND S.userName = '$userName'
    AND S.date BETWEEN '".date('Y-m-d',strtotime($fromDate))."' AND '".date('Y-m-d',strtotime($toDate))."'
    ORDER BY S.date, S.branchId, S.locationCode";
$stm = $db->prepare($query);
$stm->execute();
$userScheduleResult = $stm->fetchAll(PDO::FETCH_BOTH);

// Initializes all variables if add a schedule or gets values from the url
if($action == 'upd'):
    $date = date('m/d/Y',strtotime($_GET['date']));
    $branchId = $_GET['branchId'];
    $locationCode = $_GET['locationCode'];
    $startingTime = $_GET['startingTime'];
    $signedIn = $_GET['signedIn'];
    $finishingTime = $_GET['finishingTime'];
    $signedOut = $_GET['signedOut'];
    $referenceId = $_GET['referenceId'];
else:
    $branchId = '';
    $locationCode = '';
    $date = date("m/d/Y");
    $startingTime = '';
    $signedIn = '';
    $finishingTime = '';
    $signedOut = '';
    $referenceId = '';
endif;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>User Control Panel</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="css/userControlPanel.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/datePicker.css" type="text/css" media="all" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />     
    <script type="text/javascript" src="js/jquery-1.4.4.min.js" ></script>
    <script type="text/javascript" src="js/date.js"></script>
    <script type="text/javascript" src="js/jquery.datePicker.js"></script>
    <script type="text/javascript" src="js/functions.js"></script>  
    <noscript>
        <meta http-equiv="Refresh" 
              content="1;url=userMySchedule.php?organizationId=<?=$organizationId?>&userName=<?=$userName?>&javascript=no"/>  
    </noscript>    
    <script type="text/javascript" charset="utf-8">
        //Populate locations dropbox using Ajax in the search form        
        $(document).ready(function(){
            $('select[name=branchIdSelect]').change(function(){
                $('select[name=locationCodeSelect]').html('<option value="0">Loading...</option>');

            $.post('ajaxLocations.php', 
            {branchId:$(this).val(),organizationId:<?=$organizationId?>},
                 function(value){
                      $('select[name=locationCodeSelect]').html(value);}
             )
            })
        });
        
        //Populate locations dropbox using Ajax in the search form
        $(document).ready(function(){
            $('select[name=branchId]').change(function(){
                $('select[name=txt_locationCode]').html('<option value="0">Loading...</option>');

            $.post('ajaxLocations.php', 
            {branchId:$(this).val(),organizationId:<?=$organizationId?>},
                 function(value){
                      $('select[name=txt_locationCode]').html(value);}
             )
            })
        });
        
        //Activates the calendar
        $(function(){
          $('.date-pick').datePicker({autoFocusNextInput: true});
        });
    </script>
</head>
<body onunload="showScheduleDetails(false)">
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
            <li id="currentPage" rel="Create, Update and Delete Schedule">
                <img src="images/ico_schedule.png" 
                     style="vertical-align:middle;"/> My Schedule</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="userSignInOut.php" 
               target="_self"><li rel="Sign In/Out">
                    <img src="images/ico_signIn.png" 
                         style="vertical-align:middle;"/> Sign In/Out</li></a>
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
            <a href="index.php" 
               target="_self"><li rel="Log Out">
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
                <a href='userMySchedule.php'
                   style="color: yellow">reload the page.
                </a></b>            
            </div>
        <?
        endif;
        $errorMessages  = (isset($_GET['errorMessages']))?$_GET['errorMessages']:'';
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
        <div id="myScheduleSelectionDiv">
            <table>
            <tr>
            <td>    
            <form name="selectBranchForm" id="selectBranchForm" method="GET" 
                  action="userMySchedule.php" enctype="multipart/form-data">
                <span style="float: left; margin-top: 2px">From:&nbsp;</span>
                <input type="text" name="fromDate" id="txt_fromDate" 
                       value="<?=$fromDate?>" size="8" class="date-pick" 
                       readonly="yes" onchange="return compareFromAndToDates()" 
                       rel="Select from date" />
                <span style="float: left; margin-left: 20px; margin-top: 2px">To:&nbsp;</span>
                <input type="text" name="toDate" id="txt_toDate" value="<?=$toDate?>" 
                       size="8" class="date-pick" readonly="yes" 
                       onchange="return compareFromAndToDates()" rel="Select to date" />                 
                &nbsp; 
                <select name="branchIdSelect" id="branchIdSearch" 
                        style="width:240px; font-size: 14px; padding: 3px;" 
                        rel="Select branch">
                    <option value="%">&laquo;&nbsp; All Branches &nbsp;&raquo;</option>
                    <?	
                    for ($i=0;$i<count($branchesResult);$i++):
                        if ($branchId == $branchesResult[$i]['branchId']):
                            echo "<option value='".$branchesResult[$i]['branchId']."' selected='selected'>".$branchesResult[$i]['branchName']."</option>";
                        else:    
                            echo "<option value='".$branchesResult[$i]['branchId']."'>".$branchesResult[$i]['branchName']."</option>";
                        endif;
                    endfor;
                    ?>
                </select>
                &nbsp;
                <select name="locationCodeSelect" id="txt_locationCode" 
                        style="width:240px; font-size: 14px; padding: 3px;" 
                        rel="Select location">
                    <option value="%">&laquo;&nbsp; All Locations &nbsp;&raquo;</option>
                    <?	
                    for ($i=0;$i<count($boardLocationsResult);$i++):
                        if($locationCode == $boardLocationsResult[$i]['locationCode']):
                            echo "<option value='".$boardLocationsResult[$i]['locationCode']."' selected='selected'>".$boardLocationsResult[$i]['locationCode']."</option>";
                        else:    
                            echo "<option value='".$boardLocationsResult[$i]['locationCode']."'>".$boardLocationsResult[$i]['locationCode']."</option>";
                        endif;                        
                    endfor;
                    ?>
                </select>
                </td>
                <td>
                &nbsp;<input type="submit" value="&nbsp; Select &nbsp;" 
                             rel="Select"/>
                <input type="text" style="display: none" id="organizationId" 
                       name="organizationId" value="<?=$organizationId?>"/>
                <input type="hidden" name="userName" value="<?=$userName?>"/>
                </td>
            </form>
            </tr>
            </table>
        </div>
       <div id="scheduleTableDiv">
            <table id="scheduleTable" width="900px" align="center" 
                   cellspacing="0" border="1">
                <tr id="scheduleTableHeader" height="25">
                    <th width="10%">Date</th>
                    <th width="20%">Branch</th>
                    <th width="20%">Location</th>
                    <th width="10%">Starting Time</th>
                    <th width="10%">Signed In</th>
                    <th width="10%">Finishing Time</th>
                    <th width="10%">Signed Out</th>
                    <th width="10%">Actions</th>
                </tr>
                <tbody width="900px" id="scheduleTableGrid">
                <?
                // Shows the schedule in a table
                for ($i=0;$i<count($userScheduleResult);$i++):
                ?>
                    <tr id="scheduleTableRow" height="25">
                        <td align="center"><?=date('m/d/Y',strtotime($userScheduleResult[$i]["date"]))?></td>
                        <td align="left"><?=$userScheduleResult[$i]["branchName"]?></td>
                        <td align="left"><?=$userScheduleResult[$i]["locationCode"]?></td>
                        <td align="center"><?=substr($userScheduleResult[$i]["startingTime"],0,5)?></td>
                        <td align="center"><b><?=($userScheduleResult[$i]["signedIn"] == '00:00:00')?'-':substr($userScheduleResult[$i]["signedIn"],0,5)?></b></td>
                        <td align="center"><?=substr($userScheduleResult[$i]["finishingTime"],0,5)?></td>
                        <td align="center"><b><?=($userScheduleResult[$i]["signedOut"] == '00:00:00')?'-':substr($userScheduleResult[$i]["signedOut"],0,5)?></b></td>    
                        <td align="center">
                            <a href="userMySchedule.php?organizationId=<?=$organizationId?>&userName=<?=$userName?>&branchId=<?=$userScheduleResult[$i]["branchId"]?>&locationCode=<?=$userScheduleResult[$i]["locationCode"]?>&date=<?=$userScheduleResult[$i]["date"]?>&startingTime=<?=substr($userScheduleResult[$i]["startingTime"],0,5)?>&signedIn=<?=substr($userScheduleResult[$i]["signedIn"],0,5)?>&finishingTime=<?=substr($userScheduleResult[$i]["finishingTime"],0,5)?>&signedOut=<?=substr($userScheduleResult[$i]["signedOut"],0,5)?>&referenceId=<?=substr($userScheduleResult[$i]["referenceId"],0,5)?>&branchIdSelect=<?=$branchIdSelect?>&locationCodeSelect=<?=$locationCodeSelect?>&action=upd">
                                <img border="0" src="images/ico_search.png" rel="Edit day schedule information"/>
                            </a>

                            <img border="0" class="iconAction" 
                                 src="images/ico_delete.png" rel="Delete day schedule"
                                 onclick="if(confirm('Delete the following day schedule? \n'
                                    +'- Date: <?=date('m/d/Y',strtotime($userScheduleResult[$i]["date"]))?> \n'
                                    +'- Branch: <?=$userScheduleResult[$i]["branchName"]?> \n' 
                                    +'- Location: <?=$userScheduleResult[$i]["locationCode"]?> \n'
                                    +'- Starting: <?=$userScheduleResult[$i]["startingTime"]?> \n'
                                    +'- Finishing: <?=$userScheduleResult[$i]["finishingTime"]?> \n'))
                                 window.location='admCRUD.php?organizationId=<?=$organizationId?>&userName=<?=$userName?>&referenceId=<?=$userScheduleResult[$i]["referenceId"]?>&table=userschedules&action=del'"/>
                         </td>                            
                    </tr>
                <?
                endfor;
                ?>
                <tr id="branchesTableRow" height="25">
                    <td colspan="7" align="center">
                        <img border="0" src="images/ico_search.png"
                             style="vertical-align: top;"/> Edit day schedule information &nbsp;&bull;
                        <img border="0" src="images/ico_delete.png" 
                             style="vertical-align: top;"/> Delete day schedule &nbsp;
                    </td>
                    <td colspan="1" align="center">
                        <a href="userMySchedule.php?action=add">
                            <input type="button" value="Add Day" rel="Add day schedule"/>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="myScheduleUpdateFormDiv">
            <?
            // Changes the dialog box header according to the action
            switch ($action):
                case 'upd':    
                    $formHeader = 'UPDATE DAY SCHEDULE';
                    break;
                case 'add':
                    $formHeader = 'ADD DAY SCHEDULE';
                    break;
                default:
                    $formHeader = '';
            endswitch;                   
            ?>
            <div class="detailsFormHeaderDiv"><?=$formHeader?></div>
            <form name="myScheduleUpdateForm" id="myScheduleUpdateForM" 
                  method="POST" action="admCRUD.php" enctype="multipart/form-data" 
                  onsubmit="return validateMySchedule()">
                <table id="detailsTable" border ="0">
                    <tr>
                        <td align="right">Date:</td>
                        <td colspan="3">
                            <input id="txt_date" type="text" name="txt_date" 
                                   size="10" value="<?=$date?>" readonly="yes" 
                                   class="date-pick" rel="Schedule date"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Branch:</td>
                        <td colspan="3">
                            <select id="branchIdForm" name="branchId" 
                                    style="width:250px; height: 25px; font-size: 14px;" 
                                    rel="Select branch">
                                <option value=""></option>
                                <?	
                                for ($i=0;$i<count($branchesResult);$i++):
                                    echo"<script>alert($branchId.'-'.$branchesResult[$i]['branchId'])</script>";
                                    if($branchId == $branchesResult[$i]['branchId'] ):
                                        echo "<option value='".$branchesResult[$i]['branchId']."' selected='selected'>".$branchesResult[$i]['branchName']."</option>";
                                    else:    
                                        echo "<option value='".$branchesResult[$i]['branchId']."'>".$branchesResult[$i]['branchName']."</option>";
                                    endif;
                                endfor;
                                ?>
                            </select>
                         </td>   
                    </tr>
                    <tr>
                        <td align="right">Location:</td>
                        <td colspan="3">
                            <select id="txt_locationCodeForm" name="txt_locationCode" 
                                    style="width:250px; height: 25px; font-size: 14px;" 
                                    rel="Select location">
                                <option value=""></option>
                                <?	
                                for ($i=0;$i<count($boardLocationsResult);$i++):
                                    if($locationCode == $boardLocationsResult[$i]['locationCode'] ):
                                        echo "<option value='".$boardLocationsResult[$i]['locationCode']."' selected='selected'>".$boardLocationsResult[$i]['locationCode']."</option>";
                                    else:
                                        echo "<option value='".$boardLocationsResult[$i]['locationCode']."'>".$boardLocationsResult[$i]['locationCode']."</option>";
                                    endif;
                                endfor;
                                ?>
                            </select>                                
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Starting Time:</td>
                        <td>
                            <select id="startingTimeHH" name="txt_startingHH" 
                                    style="width:50px; height: 25px; font-size: 14px;" 
                                    onchange="return compareStartingAndFinishingTime()" 
                                    rel="Starting hour">
                                <?
                                for ($i=0;$i<=24;$i++):
                                    if(substr($startingTime,0,2) == str_pad($i,2,0,STR_PAD_LEFT)):
                                        echo "<option value='".str_pad($i,2,0,STR_PAD_LEFT)."' selected='selected'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
                                    else:
                                        echo "<option value='".str_pad($i,2,0,STR_PAD_LEFT)."'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
                                    endif;
                                endfor;
                                ?>
                            </select>
                            &nbsp;:

                            <select id="startingTimeMM" name="txt_startingMM" 
                                    style="width:50px; height: 25px; font-size: 14px;" 
                                    onchange="return compareStartingAndFinishingTime()" 
                                    rel="Starting minute">
                                <?
                                for ($i=0;$i<=59;$i=$i+5):
                                    if(substr($startingTime,3,2) == str_pad($i,2,0,STR_PAD_LEFT)):
                                        echo "<option value='".str_pad($i,2,0,STR_PAD_LEFT)."' selected='selected'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
                                    else:
                                        echo "<option value='".str_pad($i,2,0,STR_PAD_LEFT)."'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
                                    endif;
                                endfor;
                                ?>
                            </select>                                
                        </td>  
                        <?if($action != 'add'):?>
                        <td align="right">Signed In:</td>   
                        <td>
                            <input id="txt_signedIn" type="text" 
                                   name="txt_signedIn" size="5" maxlenth="5" 
                                   disabled value="<?=$signedIn?>" 
                                   rel="Time signed in"/>
                        </td>
                        <?endif;?>
                    </tr>
                    <tr>
                        <td align="right">Finishing Time:</td>
                        <td>
                            <select id="finishingTimeHH" name="txt_finishingHH" 
                                    style="width:50px; height: 25px; font-size: 14px;" 
                                    onchange="return compareStartingAndFinishingTime()"
                                    rel="Finishing hour">
                                <?
                                for ($i=0;$i<=24;$i++):
                                    if(substr($finishingTime,0,2) == str_pad($i,2,0,STR_PAD_LEFT)):
                                        echo "<option value='".str_pad($i,2,0,STR_PAD_LEFT)."' selected='selected'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
                                    else:
                                        echo "<option value='".str_pad($i,2,0,STR_PAD_LEFT)."'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
                                    endif;
                                endfor;
                                ?>
                            </select>
                            &nbsp;:
                            <select id="finishingTimeMM" name="txt_finishingMM" 
                                    style="width:50px; height: 25px; font-size: 14px;" 
                                    onchange="return compareStartingAndFinishingTime()" 
                                    rel="Finishing minute">
                                <?
                                for ($i=0;$i<=59;$i=$i+5):
                                    if(substr($finishingTime,3,2) == str_pad($i,2,0,STR_PAD_LEFT)):
                                        echo "<option value='".str_pad($i,2,0,STR_PAD_LEFT)."' selected='selected'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
                                    else:
                                        echo "<option value='".str_pad($i,2,0,STR_PAD_LEFT)."'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
                                    endif;
                                endfor;
                                ?>
                            </select>  
                        </td>
                        <?if($action != 'add'):?>
                        <td align="right">Signed Out:</td>             
                        <td>
                            <input id="txt_signedOut" type="text" name="txt_signedOut" 
                                   size="5" maxlenth="5" disabled value="<?=$signedOut?>" 
                                   rel="Time signed out"/>
                        </td>
                        <?endif;?>
                    </tr>
                    <tr>
                        <td colspan="4" align="center">
                            <?
                            switch ($action):
                                case 'add':    
                                    echo "<input type='submit' value='&nbsp;Add&nbsp;' rel='Add day schedule'/>";
                                    break;
                                case 'upd':
                                    echo "<input type='submit' value='&nbsp;Save&nbsp;' rel='Update day schedule information'/>";
                                    break;
                            endswitch;
                            ?>    
                            <input type='reset' value='Cancel' 
                                   onclick="showScheduleDetails(false)" 
                                   rel='Cancel and close'/>
                        </td>    
                    </tr>
                </table>
                <input type="hidden" name="action" value="<?=$action?>" />
                <input type="hidden" name="makingOption" value="anyDateAndTime" />
                <input type="hidden" name="table" value="userschedules" />
                <input type="hidden" name="referenceId" value="<?=$referenceId?>"/>
                <input type="hidden" name="txt_signedIn" value="<?=$signedIn?>"/>
                <input type="hidden" name="txt_signedOut" value="<?=$signedOut?>"/>
            </form>
        </div>            
        <?
        if($action == 'add' || $action == 'upd') echo "<script>showScheduleDetails(true)</script>";  
        ?>             
    </div>
   <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
