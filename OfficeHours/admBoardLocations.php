<!-- admBoardLocations.php
        PROG8105-1 Systems Project: Create, update and delete locations

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.02.05: Created
-->
<?
// Checks if the user logged in the application
session_start('officeHours');
$organizationId = isset($_SESSION['organizationId'])?$_SESSION['organizationId']:'';
$organizationName = isset($_SESSION['organizationName'])?$_SESSION['organizationName']:'';
$userName = isset($_SESSION['userName'])?$_SESSION['userName']:'';
if($organizationId == "" || $userName == ""):
    echo "<h3>You need to log in to accesss this page, please <a href='index.php'>click here to log in</a>.<h3>";
    exit();
endif;

// Includes the classes  
require_once("includes/validationFunctions.php");
require_once("classes/locations.class.php");
require_once("classes/branches.class.php");

// Get all the parameters from the URL  
$action         = mysql_entities_fix_string((isset($_GET['action']))?$_GET['action']:'');
$branchId       = mysql_entities_fix_string((isset($_GET['branchId']))?$_GET['branchId']:'%');
$locationCode   = mysql_entities_fix_string((isset($_GET['locationCode']))?$_GET['locationCode']:'');

// Initialize varables according to the action
if($action == 'upd' || $action == 'del'):
    $location = new Locations($locationCode,'',$branchId,$organizationId);
    $locationResult = $location->selectLocation();
    $locationCode    = $locationResult[0]["locationCode"];
    $locationDetails = $locationResult[0]["locationDetails"];
else:
    $locationCode    = '';
    $locationDetails = '';
endif;

// instantiate classes
$location = new Locations('','',$branchId,$organizationId);
$locationsResult = $location->selectAll();

$branches = new Branches('','','','',$organizationId);
$branchesResult = $branches->selectAll();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Administrator Control Panel</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="css/admControlPanel.css" type="text/css" media="all" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />     
    <script type="text/javascript" src="js/jquery-1.4.4.min.js" ></script>
    <script type="text/javascript" src="js/functions.js"></script>      
    <noscript>
        <meta http-equiv="Refresh" content="1;url=admBoardLocations.php?javascript=no"/>
    </noscript> 
</head>
<body onunload="showLocationDetails(false)">
<div id="mainDiv">
    <div id="topDiv">
        <div id="headerDiv">
            <span>OFFICE <img src="images/boardImage.png" 
                              style="vertical-align:middle;"/> HOURS</span>
        </div>
        <div id="sitePanelNameDiv">
            <span>Administrator Control Panel</span>                
        </div>
    </div>
    <span style="margin-left: 10px">
        Organization: <b><?=$_SESSION['organizationName']?></b> - User: <b><?=$userName?> (Administrator)</b>
    </span>
    <div id="menuDiv">
        <ul style="margin-left: 90px;">
            <li><img src="images/menuDivider.gif" 
                     style="vertical-align:middle;"/></li>               
            <a href="admControlPanel.php" 
               target="_self"><li rel="Home page">
                    <img src="images/ico_home.png" 
                         style="vertical-align:middle;"/> Home</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="admBranches.php" 
               target="_self"><li rel="Create, Update and Delete Branches">
                    <img src="images/ico_branch.png" 
                         style="vertical-align:middle;"/> Branches</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <li id="currentPage" rel="Create, Update and Delete Locations">
                <img src="images/ico_location.png" 
                     style="vertical-align:middle;"/> Locations</li>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="admUsers.php" 
               target="_self" ><li rel="Create, Update and Delete Users">
                    <img src="images/ico_users.png" 
                         style="vertical-align:middle;"/> Users</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="admSettings.php" 
               target="_self" ><li rel="Web site Settings">
                    <img src="images/ico_settings.png" 
                         style="vertical-align:middle;"/> Settings</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="help/officeHoursHelp.html" 
               target="_new" ><li rel="Web site Help">
                    <img src="images/ico_help.png" 
                         style="vertical-align:middle;"/> Help</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="index.php" 
               target="_self" ><li rel="Log Out">
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
                Please turn it on and <b><a href='admBoardLocations.php' 
                                            style="color: yellow">reload the page.</a></b>            
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
        <div id="branchSelectionDiv">
            <form name="selectBranchForm" id="selectBranchForm" method="GET" 
                  action="admBoardLocations.php" enctype="multipart/form-data">
            Select a Branch:&nbsp; 
            <select name="branchId" 
                    style="width:250px; font-size: 14px; padding: 3px;" rel="Select branch">
                <option value="%">&laquo;&nbsp; All Branches &nbsp;&raquo;</option>
                <?	
                for ($i=0;$i<count($branchesResult);$i++):
                    if($branchId == $branchesResult[$i]['branchId'] ):
                        echo "<option value=".$branchesResult[$i]['branchId']." selected='selected'>".$branchesResult[$i]['branchName']."</option>";
                    else:    
                        echo "<option value=".$branchesResult[$i]['branchId'].">".$branchesResult[$i]['branchName']."</option>";
                    endif;
                endfor;
                ?>
            </select>
            &nbsp;<input type="submit" value="&nbsp;OK&nbsp;"/>
            </form>    
        </div>
        <div id="branchesTableDiv">
            <table id="branchesTable" width="900px" 
                   align="center" cellspacing="0" border="1">
                <tr id="branchesTableHeader" height="25">
                    <th width="20%">Branch</th>
                    <th width="15%">Location Code</th>
                    <th width="50%">Location Details</th>
                    <th width="15%">Actions</th>
                </tr>
                <tbody width="800px" id="branchesTableGrid">
                <?
                for ($i=0;$i<count($locationsResult);$i++):
                ?>
                    <tr id="branchesTableRow" height="25">
                        <td align="left"><?=$locationsResult[$i]["branchName"]?></td>
                        <td align="left"><?=$locationsResult[$i]["locationCode"]?></td>
                        <td align="left"><?=$locationsResult[$i]["locationDetails"]?></td>
                        <td align="center">
                            <a href="admBoardLocations.php?branchId=<?=$locationsResult[$i]["locationBranchId"]?>&locationCode=<?=$locationsResult[$i]["locationCode"]?>&action=upd">
                                <img border="0" src="images/ico_search.png" 
                                     rel="Edit location information"/>
                            </a>
                            <img border="0" class="iconAction" src="images/ico_delete.png" 
                                 rel="Delete location and associated schedules" 
                                 onclick="if(confirm('Delete location <?=$locationsResult[$i]['locationCode']?> and associated schedules?')) 
                                 window.location='admCRUD.php?branchId=<?=$locationsResult[$i]["locationBranchId"]?>&locationCode=<?=$locationsResult[$i]["locationCode"]?>&table=locations&action=del'"/>   
                        </td>
                    </tr>
                <?
                endfor;
                ?>
                <tr id="branchesTableRow" height="25">
                    <td colspan="3" align="center">
                        <img border="0" src="images/ico_search.png" 
                             style="vertical-align: top;"/> Edit location information &nbsp;&bull;
                        <img border="0" src="images/ico_delete.png" 
                             style="vertical-align: top;"/> Delete location and associated schedules
                    </td>
                    <td align="center">
                        <a href="admBoardLocations.php?action=add">
                            <input type="button" value="Add" rel="Add location"/>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="locationFormDiv">
           <?
                switch ($action):
                    case 'upd':    
                        $formHeader = 'EDIT LOCATION DETAILS';
                        break;
                    case 'add':
                        $formHeader = 'ADD LOCATION';
                        break;
                    default:
                        $formHeader = '';
                endswitch;                   
            ?>
            <div class="detailsLocationFormHeaderDiv"><?=$formHeader?></div>                
            <form name="entryForm" id="entryForm" method="POST" action="admCRUD.php" 
                  enctype="multipart/form-data" onsubmit="return validateLocation()">
                <table id="detailsTable">
                    <tr>
                        <td align="right">Branch:</td>
                        <td>
                            <select id="branchId" name="branchId" <?=($action != 'add')?'disabled':''?> 
                                    style="width:145px; padding: 2px;" <?=($action != 'add')?'':"rel='Select branch'"?>>
                                <option value=""></option>
                                <?	
                                for ($i=0;$i<count($branchesResult);$i++):
                                    if($branchId == $branchesResult[$i]['branchId'] ):
                                        echo "<option value=".$branchesResult[$i]['branchId']." selected='selected'>".$branchesResult[$i]['branchName']."</option>";
                                    else:    
                                        echo "<option value=".$branchesResult[$i]['branchId'].">".$branchesResult[$i]['branchName']."</option>";
                                    endif;
                                endfor;
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Location Code:</td>
                        <td><input id="locationCode" type="text" name="locationCode" 
                                   size="20" maxlenth="35" <?=($action != 'add')?'disabled':''?> value="<?=$locationCode?>" <?=($action != 'add')?'':"rel='Select branch'"?> /> *</td>
                    </tr>
                    <tr>
                        <td align="right">Location Details:</td>
                        <td><input id="locationDetails" type="text" name="locationDetails" 
                                   rel="Location details" size="50" 
                                   maxlenth="50" <?=($action != 'add' && $action != 'upd')?'readonly':''?> 
                                   value="<?=$locationDetails?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <?
                            switch ($action):
                                case 'upd':
                                    echo "<input type='submit' value='Save' rel='Update location information'>";
                                    break;
                                case 'add':
                                    echo "<input type='submit' value='Add' rel='Add location'>";
                                    break;                                    
                            endswitch;
                            echo "<input type='reset' value='Cancel' rel='Cancel and close' onclick='showLocationDetails(false)'/>";
                            ?>   
                        </td>    
                    </tr>
                </table>
                <input type="hidden" name="action" value="<?=$action?>" />
                <input type="hidden" name="table" value="locations" />
                <?
                if($action != 'add'):
                    echo "<input type='hidden' name='branchId' value='".$branchId."'/>";
                    echo "<input type='hidden' name='locationCode' value='".$locationCode."'/>";
                endif;  
                ?>
            </form>
        </div>            
        <? if($action != '') echo "<script>showLocationDetails(true)</script>"; ?>    
    </div>
    <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
