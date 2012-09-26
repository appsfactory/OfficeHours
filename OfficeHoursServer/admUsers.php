<!-- admUsers.php
        PROG8105-1 Systems Project: Create, update and delete users

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma
            Vandana Sharma, 2012.02.01: Created
-->
<?
// Checks if the user logged in the application
session_start('officeHours');
$organizationId = isset($_SESSION['organizationId'])?$_SESSION['organizationId']:'';
$organizationName = isset($_SESSION['organizationName'])?$_SESSION['organizationName']:'';
$userNameHeader = isset($_SESSION['userName'])?$_SESSION['userName']:'';
if($organizationId == "" || $userNameHeader == ""):
    echo "<h3>You need to log in to accesss this page, please <a href='index.php'>click here to log in</a>.<h3>";
    exit();
endif;

// Includes users class
require_once("classes/users.class.php");

// Get all the parameters from the URL 
$userName = isset($_GET['userName'])?$_GET['userName']:'';
$action = (isset($_GET['action']))?$_GET['action']:'';

// Initialize varables according to the action
if($action == 'upd' || $action == 'del'):
    $user = new Users($userName,'','','','','',$organizationId);
    $result = $user->selectUser();
    $userName = $result[0]["userName"];
    $firstName = $result[0]["firstName"];
    $lastName = $result[0]["lastName"];
    $email = $result[0]["email"];
    $password = $result[0]["password"];
    $isAdministrator = $result[0]["isAdministrator"];
else:
    $userName = '';
    $firstName = '';
    $lastName = '';
    $email = '';
    $password = '';
    $isAdministrator = '';
endif;

// instantiate users class
$users = new Users('','','','','','',$organizationId);
$result = $users->selectAll();
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
        <meta http-equiv="Refresh" content="1;url=admUsers.php?javascript=no"/>
    </noscript>   
</head>
<body onunload="showUserDetails(false)">
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
        Organization: <b><?=$_SESSION['organizationName']?></b> - User: <b><?=$userNameHeader?> (Administrator)</b>
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
            <a href="admBoardLocations.php" 
               target="_self" ><li rel="Create, Update and Delete Locations">
                    <img src="images/ico_location.png" 
                         style="vertical-align:middle;"/> Locations</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <li id="currentPage" rel="Create, Update and Delete Users">
                <img src="images/ico_users.png" 
                     style="vertical-align:middle;"/> Users</li>
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
                    <a href='admUsers.php' 
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
                    <th width="15%">Username</th>
                    <th width="15%">First Name</th>
                    <th width="15%">Last Name</th>
                    <th width="30%">E-mail</th>
                    <th width="8%">Is Admin?</th>
                    <th width="10%">Actions</th>
                </tr>
                <tbody width="800px" id="branchesTableGrid">
                <?
                for ($i=0;$i<count($result);$i++):
                ?>
                    <tr id="branchesTableRow" height="25">
                        <td align="left"><?=$result[$i]["userName"]?></td>
                        <td align="left"><?=$result[$i]["firstName"]?></td>
                        <td align="left"><?=$result[$i]["lastName"]?></td>
                        <td align="left"><?=$result[$i]["email"]?></td>
                        <td align="center"><?=($result[$i]["isAdministrator"] == 'Y')?'Yes':''?></td>                            
                        <td align="center">
                            <a href="admUsers.php?userName=<?=$result[$i]["userName"]?>&action=upd">
                                <img border="0" src="images/ico_search.png" rel="Edit user information"/>
                            </a>
                            <img border="0" class="iconAction" src="images/ico_key.png" rel="Reset user password"
                                 onclick="if(confirm('Reset the <?=$result[$i]['userName']?> password? \n The new password will be <?=$result[$i]['userName']?>123.')) 
                                 window.location='admCRUD.php?userName=<?=$result[$i]["userName"]?>&newPassword=<?="@"?>&table=users&action=resetPassword'"/>  
                            <img border="0" class="iconAction" src="images/ico_delete.png" rel="Delete user and associated schedules"
                                 onclick="if(confirm('Delete user <?=$result[$i]['userName']?> and associated schedules?')) 
                                 window.location='admCRUD.php?userName=<?=$result[$i]["userName"]?>&table=users&action=del'"/>   
                        </td>
                    </tr>
                <?
                endfor;
                ?>
                <tr id="branchesTableRow" height="25">
                    <td colspan="5" align="center">
                        <img border="0" src="images/ico_search.png" style="vertical-align: top;"/> Edit user information &nbsp;&bull;
                        <img border="0" src="images/ico_key.png" style="vertical-align: top;"/> Reset user password &nbsp;&bull;
                        <img border="0" src="images/ico_delete.png" style="vertical-align: top;"/> Delete user and associated schedules
                    </td>
                    <td align="center">
                        <a href="admUsers.php?action=add">
                            <input type="button"  value="Add" rel="Add User"/>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="userFormDiv">
            <?
            // Changes the dialog box header according to the action
            switch ($action):
                case 'upd':    
                    $formHeader = 'EDIT USER INFORMATION';
                    break;
                case 'add':
                    $formHeader = 'ADD USER';
                    break;    
                default:
                    $formHeader = '';
            endswitch;                   
            ?>
            <div class="detailsFormHeaderDiv"><?=$formHeader?></div>
            <form name="entryForm" id="entryForm" method="POST" action="admCRUD.php" 
                  enctype="multipart/form-data" onsubmit="return validateUser()">
                <table id="detailsTable">
                    <tr>
                        <td align="right" width="40%">Username:</td>
                        <td width="60%">
                            <input id="userName" type="text" name="userName" 
                                   rel="Username" size="30" <?=($action == 'add')?'':'disabled'?> 
                                   value="<?=$userName?>"/> *
                        </td>
                    </tr>
                    <tr>
                        <td align="right">First Name:</td>
                        <td>
                            <input id="firstName" type="text" name="firstName" 
                                   rel="First name" size="30" <?=($action != 'add' && $action != 'upd')?'readonly':''?> 
                                   maxlenth="30" value="<?=$firstName?>"/> *
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Last Name:</td>
                        <td>
                            <input id="lastName" type="text" name="lastName" rel="Last Name" 
                                   size="30" <?=($action != 'add' && $action != 'upd')?'readonly':''?> 
                                   maxlenth="30" value="<?=$lastName?>"/> *
                        </td>
                    </tr>
                    <tr>
                        <td align="right">E-mail:</td>
                        <td>
                            <input id="email" type="text" name="email" rel="Email" 
                                   size="30" <?=($action != 'add' && $action != 'upd')?'readonly':''?> 
                                   maxlenth="30" value="<?=$email?>"/> *
                        </td>
                    </tr>
                    <tr>
                        <td align="right">  <?=($action == 'add')?'Temporary':''?> Password:</td>
                        <td>
                            <input id="password" type="<?=($action == 'add')?'text':'password'?>" 
                                   name="password" rel="Set a new temporary password" size="30" 
                                   <?=($action != 'add')?'readonly':''?>
                                   maxlenth="30" value="<?=$password?>"/> *
                        </td>
                    </tr>                        
                    <tr>
                        <td align="right">Administrator:</td>
                        <td>
                            No <input type="radio" value="" name="isAdministrator" 
                                      id="isAdministrator" rel="User is not administrator" <?=($isAdministrator == '')?'checked':''?> />
                            Yes <input type="radio" value="Y" name="isAdministrator" 
                                       id="isAdministrator" rel="User is administrator" <?=($isAdministrator == 'Y')?'checked':''?> />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <?
                            // Changes the button according to the action
                            switch ($action):
                                case 'upd':
                                    echo "<input type='submit' value='Save' rel='Update user information'>";
                                    break;
                                case 'add':
                                    echo "<input type='submit' value='Add' rel='Add location'>";
                                    break;                                    
                            endswitch;
                            echo "<input type='reset' value='Cancel' rel='Cancel and close' onclick='showUserDetails(false)'/>";
                            ?>                                 
                        </td>    
                    </tr>
                </table>
                <input type="hidden" name="action" value="<?=$action?>" />
                <input type="hidden" name="table" value="users" />
                <?
                if($action != 'add'):
                    echo "<input type='hidden' name='userName' value='".$userName."'/>";
                endif;  
                ?>                    
            </form>
        </div>            
        <?
          if($action != '') echo "<script>showUserDetails(true)</script>";  
        ?>    
    </div>
    <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
