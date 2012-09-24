<!-- userPassword.php
        PROG8105-1 Systems Project: Changes users password

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.02.17: Created
-->
<?
//// Checks if the user logged in the application
//$organizationId = (isset($_GET['organizationId']))?$_GET['organizationId']:'';
//$userName       = (isset($_GET['userName']))?$_GET['userName']:'';
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

require_once("classes/users.class.php");

// instantiate users class
$user = new Users($userName,'','','','','',$organizationId);
$resultUser = $user->selectUser();
$userPassword = $resultUser[0]['password'];
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
    <script type="text/javascript" src="js/jqueryTools.js"></script> 
    <noscript>
        <meta http-equiv="Refresh" 
              content="1;url=userPassword.php?javascript=no"/>
    </noscript>
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
            <a href="userSignInOut.php" 
               target="_self"><li rel="Sign In/Out">
                    <img src="images/ico_signIn.png" 
                         style="vertical-align:middle;"/> Sign In/Out</li></a>
            <li><img src="images/menuDivider.gif"/></li>                  
            <li id="currentPage">
                <img src="images/ico_password.png" 
                     style="vertical-align:middle;"/> Change Password</li>           
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
                <a href='userPassword.php?'
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
        $commited = (isset($_GET['commited']))?$_GET['commited']:'';
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
        <div id="passwordFormDiv">
            <div class="detailsFormHeaderDiv">CHANGE PASSWORD</div>
            <form name="entryForm" id="entryForm" method="POST" action="admCRUD.php" 
                  enctype="multipart/form-data" onsubmit="return validatePassword()">
                <table id="detailsTable">
                    <tr>
                        <td align="right">New Password:</td>
                        <td><input id="newPassword" type="password" 
                                   name="newPassword" rel="New password" 
                                   size="20" maxlenth="30"/></td>
                    </tr>
                    <tr>
                        <td align="right">Confirm Password:</td>
                        <td>
                            <input id="confirmPassword" type="password" 
                                   name="confirmPassword" rel="Confirm password" 
                                   size="20" maxlenth="30"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">
                            <input type='submit' value='Save' rel='Save new password'/>
                            <input type='reset' value='Cancel' rel='Cancel and close' 
                                   onclick="window.location.href ='userControlPanel.php'"/>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="updPassword" />
                <input type="hidden" name="table" value="users" />
            </form>
        </div>            
    </div>
    <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
