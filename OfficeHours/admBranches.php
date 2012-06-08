<!-- admBranches.php
        PROG8105-1 Systems Project: Create, update and delete branches

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.02.01: Created
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

// Includes branches class
require_once("classes/branches.class.php");

// Get all the parameters from the URL  
$action         = (isset($_GET['action']))?$_GET['action']:'';
$branchId       = (isset($_GET['branchId']))?$_GET['branchId']:'';

// Initialize varables according to the action
if($action == 'upd' || $action == 'del'):
    $branch = new Branches($branchId,'','','',$organizationId);
    $result = $branch->selectBranch();
    $branchName     = $result[0]["branchName"];
    $branchAddress  = $result[0]["branchAddress"];
    $branchPhone    = $result[0]["branchPhone"];
else:
    $branchName     = '';
    $branchAddress  = '';
    $branchPhone    = '';
endif;

// instantiate branches class
$branches = new Branches('','','','',$organizationId);
$result = $branches->selectAll();
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
    <script type="text/javascript" src="js/jqueryTools.js"></script> 
    <noscript>
        <meta http-equiv="Refresh" content="1;url=admBranches.php?javascript=no"/>
    </noscript>
</head>
<body onunload="showBranchDetails(false)">
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
            <li id="currentPage" rel="Create, Update and Delete Branches">
                <img src="images/ico_branch.png" 
                     style="vertical-align:middle;"/> Branches</li>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="admBoardLocations.php" 
               target="_self"><li rel="Create, Update and Delete Locations">
                    <img src="images/ico_location.png" 
                         style="vertical-align:middle;"/> Locations</li>
            </a>
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
            <a href="help/officeHoursHelp.html" target="_new" >
                <li rel="Web site Help">
                    <img src="images/ico_help.png" 
                         style="vertical-align:middle;"/> Help</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="index.php" target="_self" ><li rel="Log Out">
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
                <a href='admBranches.php' 
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
        <div id="branchesTableDiv">
            <table id="branchesTable" width="900px" align="center" cellspacing="0" border="1">
                <tr id="branchesTableHeader" height="25">
                    <th width="20%">Name</th>
                    <th width="50%">Address</th>
                    <th width="15%">Phone</th>
                    <th width="15%">Actions</th>
                </tr>
                <tbody width="800px" id="branchesTableGrid">
                <?
                for ($i=0;$i<count($result);$i++):
                ?>
                    <tr id="branchesTableRow" height="25">
                        <td align="left"><?=$result[$i]["branchName"]?></td>
                        <td align="left"><?=$result[$i]["branchAddress"]?></td>
                        <td align="left"><?=$result[$i]["branchPhone"]?></td>
                        <td align="center">
                            <a href="admBranches.php?branchId=<?=$result[$i]["branchId"]?>&action=upd">
                                <img border="0" src="images/ico_search.png" rel="Edit branch information" />
                            </a>
                            <img border="0" src="images/ico_delete.png" 
                                 class="iconAction" rel="Delete branch and associated locations"
                                 onclick="if(confirm('Delete branch <?=$result[$i]['branchName']?> and associated locations?')) 
                                 window.location='admCRUD.php?branchId=<?=$result[$i]['branchId']?>&table=branches&action=del'"/>
                        </td>
                    </tr>
                <?
                endfor;
                ?>
                <tr id="branchesTableRow" height="25">
                    <td colspan="3" align="center">
                        <img border="0" src="images/ico_search.png" style="vertical-align: top;"/> Edit branch information &nbsp;&bull;
                        <img border="0" src="images/ico_delete.png" style="vertical-align: top;"/> Delete branch and associated locations
                    </td>
                    <td align="center">
                        <a href="admBranches.php?action=add">
                            <input type="button" value="Add" rel="Add branch"/>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="branchFormDiv">
            <?
            // Changes the dialog box header according to the action
            switch ($action):
                case 'upd':    
                    $formHeader = 'EDIT BRANCH INFORMATION';
                    break;
                case 'add':
                    $formHeader = 'ADD BRANCH';
                    break;
                default:
                    $formHeader = '';
            endswitch;                   
            ?>
            <div class="detailsFormHeaderDiv"><?=$formHeader?></div>
            <form name="entryForm" id="entryForm" method="POST" action="admCRUD.php" 
                  enctype="multipart/form-data" onsubmit="return validateBranch()">
                <table id="detailsTable">
                    <tr>
                        <td align="right">Branch Name:</td>
                        <td><input id="branchName" type="text" name="txt_branchName" 
                                   size="20" <?=($action == 'upd')?'disabled':''?> 
                                   value="<?=$branchName?>" rel='Branch name'/> *</td>
                    </tr>
                    <tr>
                        <td align="right">Address:</td>
                        <td><input id="branchAddress" type="text" name="txt_branchAddress" 
                                   size="50" maxlenth="50" value="<?=$branchAddress?>" 
                                   rel='Branch address'/></td>
                    </tr>
                    <tr>
                        <td align="right">Phone:</td>
                        <td><input id="branchPhone" type="text" name="txt_branchPhone" 
                                   size="20" maxlenth="15" value="<?=$branchPhone?>" 
                                   rel='Branch phone'/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <?
                            switch ($action):
                                case 'upd':
                                    echo "<input type='submit' value='Save' rel='Update branch Information'>";
                                    break;
                                case 'add':
                                    echo "<input type='submit' value='Add' rel='Add branch' >";
                                    break;                                    
                            endswitch;
                            echo "<input type='reset' value='Cancel' onclick='showBranchDetails(false)' rel='Cancel and close'/>";
                            ?>    
                        </td>    
                    </tr>
                </table>
                <input type="hidden" name="action" value="<?=$action?>" />
                <input type="hidden" name="table" value="branches" />
                <input type="hidden" name="branchId" value="<?=$branchId?>"/>
                <?
                if($action == 'upd'):
                    echo "<input type='hidden' name='txt_branchName' value='".$branchName."'/>";
                endif;  
                ?>                    
            </form>
        </div>            
        <?
          if($action != '') echo "<script>showBranchDetails(true)</script>";  
        ?>    
    </div>
    <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
