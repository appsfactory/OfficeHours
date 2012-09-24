<!-- userScheduleMaker.php
        PROG8105-1 Systems Project: Creates user schedule

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.02.17: Created
-->
<?
// Checks if the user logged in the application
session_start('officeHours');
$organizationId = isset($_SESSION['organizationId'])?$_SESSION['organizationId']:'';
$userName = isset($_SESSION['userName'])?$_SESSION['userName']:'';
if($organizationId == "" || $userName == ""):
    echo "<h3>You need to log in to accesss this page, please <a href='index.php'>click here to log in</a>.<h3>";
    exit();
endif;

// Gets all the parameters from the URL 
require_once("includes/dbConnection.php");
$fromDate = isset($_GET['fromDate'])?$_GET['fromDate']:'';
$toDate = isset($_GET['toDate'])?$_GET['toDate']:''; 

// Selects all onganization branches 
$query = "SELECT * FROM branches WHERE organizationId = $organizationId ORDER BY branchName";
$executeQuery = $db->prepare($query);
$executeQuery->execute();
$branchesResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);

// Selects all onganization locations
$query = "SELECT locationCode FROM boardlocations WHERE locationOrganizationId = $organizationId ORDER BY locationCode";
$executeQuery = $db->prepare($query);
$executeQuery->execute() or exit("Error: SELECT query failed.");
$boardLocationsResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);

// Selects the users schedule 
$query = "SELECT B.branchName, S.*
    FROM branches AS B, userschedules AS S  
    WHERE S.organizationId = $organizationId
    AND B.organizationId = S.organizationId
    AND B.branchId = S.branchId
    AND S.userName = '$userName'
    AND S.date BETWEEN '$fromDate' AND '$toDate'
    ORDER BY S.date, S.branchId, S.locationCode";
    $executeQuery = $db->prepare($query);
    $executeQuery->execute();
    $userScheduleResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);
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
              content="1;url=userScheduleMaker.php?javascript=no"/>        
    </noscript>  
    <script type="text/javascript" charset="utf-8">
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
<body>
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
            <li id="currentPage" rel="Create schedule">
                <img src="images/ico_makeSchedule.png" 
                     style="vertical-align:middle;"/> Schedule Maker</li>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="userMySchedule.php" 
               target="_self"><li rel="Create, Update and Delete Schedule">
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
                <a href='userScheduleMaker.php?'
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
        <div id="scheduleMakerDiv">
            <div id="scheduleDetailsHeaderDiv">SCHEDULE BUILDER</div>
            <form name="scheduleForm" id="scheduleForm" method="POST" 
                  action="admCRUD.php" enctype="multipart/form-data" 
                  onsubmit="return validateMakeSchedule()">
                <table id="scheduleDetailsTable" width="100%" 
                       height="100%" border="0">
                    <tr>
                        <td align="center">
                            <span style="float: left; margin-top: 2px">From:&nbsp;</span>
                            <input type="text" name="txt_fromDate" id="txt_fromDate" 
                                   size="10" readonly="yes" class="date-pick" 
                                   onchange="return compareFromAndToDates()" 
                                   rel="Starting date"/>
                            <span style="float: left; margin-left: 20px; margin-top: 2px;">To:&nbsp;</span>
                            <input type="text" name="txt_toDate" id="txt_toDate" size="10" readonly="yes" 
                                   class="date-pick" onchange="return compareFromAndToDates()" 
                                   rel="Finishing date"/>
                        </td>
                        <td align="right">
                            Start:
                        </td>
                        <td>
                            <select id="startingTimeHH" name="txt_startingHH" 
                                    style="width:50px; height: 25px; font-size: 14px;" 
                                    onchange="return compareStartingAndFinishingTime()" 
                                    rel="Starting hour">
                                <?
                                for ($i=0;$i<=24;$i++):
                                ?>
                                    <option value="<?=$i?>"><?=str_pad($i,2,0,STR_PAD_LEFT)?></option>
                                <?
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
                                ?>
                                    <option value="<?=$i?>"><?=str_pad($i,2,0,STR_PAD_LEFT)?></option>
                                <? 
                                endfor;
                                ?>
                            </select>
                        </td>

                    </tr>
                    <tr height="20px">
                        <td>
                            <input type="radio" name="makingOption" value="MonFri" 
                                   checked="checked" id="monFriRadio" 
                                   rel="Select Monday to Friday"/> Monday to Friday
                        </td>
                        <td align="right">
                            Finish:
                        </td>
                        <td>
                            <select id="finishingTimeHH" name="txt_finishingHH" 
                                    style="width:50px; height: 25px; font-size: 14px;" 
                                    onchange="return compareStartingAndFinishingTime()" 
                                    rel="Finishing hour">
                                <?
                                for ($i=0;$i<=24;$i++):
                                ?>
                                    <option value="<?=$i?>"><?=str_pad($i,2,0,STR_PAD_LEFT)?></option>
                                <?
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
                                ?>
                                    <option value="<?=$i?>"><?=str_pad($i,2,0,STR_PAD_LEFT)?></option>
                                <? 
                                endfor;
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr height="20px">
                        <td>
                            <input type="radio"    name="makingOption" value="days"  
                                   id="daysWeekRadio" 
                                   rel="Select the weekdays"/> Days: &nbsp; &nbsp;
                            <input type="checkbox" name="checkDays[]"  value="Mon"  
                                   class="daysCheckBox" /> Mon &nbsp;
                            <input type="checkbox" name="checkDays[]"  value="Tue"  
                                   class="daysCheckBox" /> Tue &nbsp;
                            <input type="checkbox" name="checkDays[]"  value="Wed"  
                                   class="daysCheckBox" /> Wed &nbsp;
                            <input type="checkbox" name="checkDays[]"  value="Thu"  
                                   class="daysCheckBox" /> Thu &nbsp;
                            <input type="checkbox" name="checkDays[]"  value="Fri"  
                                   class="daysCheckBox" /> Fri &nbsp;
                            <input type="checkbox" name="checkDays[]"  value="Sat"  
                                   class="daysCheckBox" /> Sat &nbsp;
                            <input type="checkbox" name="checkDays[]"  value="Sun"  
                                   class="daysCheckBox" /> Sun
                        </td>
                        <td colspan="2" align="left">
                            Branches:<br/>
                            <select name="branchId" id="branchId" 
                                    style="width:250px; height: 25px; font-size: 14px;" 
                                    rel="Select branch">
                                <option value=""></option>
                                <?	
                                for ($i=0;$i<count($branchesResult);$i++):
                                    echo "<option value=".$branchesResult[$i]['branchId'].">".$branchesResult[$i]['branchName']."</option>";
                                endfor;
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr height="20px">
                        <td>* Please, be aware that all duplicated schedules will be discarded.</td>
                        <td colspan="2" align="left">
                            Location:<br/>
                            <select name="txt_locationCode" id="txt_locationCode" 
                                    style="width:250px; height: 25px; font-size: 14px;" 
                                    rel="Select location">
                                <option value=""></option>
                                <?	
                                for ($i=0;$i<count($boardLocationsResult);$i++):
                                    echo "<option value='".$boardLocationsResult[$i]['locationCode']."'>".$boardLocationsResult[$i]['locationCode']."</option>";
                                endfor;
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr height="20px">
                        <td align="center" colspan="3">
                            <input type='submit' value='Create' 
                                   rel='Create schedule'/>&nbsp;
                            <input type='reset' value='Cancel' 
                                   onclick="window.location.href ='userControlPanel.php?'" 
                                   rel='Cancel and close'/> 
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="add" />
                <input type="hidden" name="table" value="userschedules" />
            </form>
        </div>          
       <div id="scheduleTableDiv">
            <table id="scheduleTable" width="900px" align="center" 
                   cellspacing="0" border="1">
                <tr id="scheduleTableHeader" height="25">
                    <th width="20%">Date</th>
                    <th width="25%">Branch</th>
                    <th width="25%">Location</th>
                    <th width="15%">Starting Time</th>
                    <th width="15%">Finishing Time</th>
                </tr>
                <tbody width="900px" id="scheduleTableGrid">
                <?
                for ($i=0;$i<count($userScheduleResult);$i++){
                ?>
                    <tr id="scheduleTableRow" height="25">
                        <td align="center"><?=date('m/d/Y',strtotime($userScheduleResult[$i]["date"]))?></td>
                        <td align="left"><?=$userScheduleResult[$i]["branchName"]?></td>
                        <td align="left"><?=$userScheduleResult[$i]["locationCode"]?></td>
                        <td align="center"><?=substr($userScheduleResult[$i]["startingTime"],0,5)?></td>
                        <td align="center"><?=substr($userScheduleResult[$i]["finishingTime"],0,5)?></td>
                    </tr>
                <?
                }
                ?>
                <tr id="branchesTableRow" height="25">
                    <td colspan="5" align="center">
                        <b>Go to <a href="userMySchedule.php" 
                                    target="_self" style="color:red;" 
                                    rel="Create, Update and Delete Schedule" >My Schedule</a> for changes.</b>  
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
