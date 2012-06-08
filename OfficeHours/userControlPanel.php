<!-- userControlPanel.php
        PROG8105-1 Systems Project: User Control Panel

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.02.17: Created
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>User Control Panel</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <script type="text/javascript" src="js/jquery-1.4.4.min.js" ></script>  
    <script type="text/javascript" src="js/functions.js"></script>     
    <link rel="stylesheet" href="css/userControlPanel.css" type="text/css" media="all" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />     
    <noscript>
        <meta http-equiv="Refresh" 
              content="1;url=userControlPanel.php?javascript=no"/>  
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
            <li id="currentPage" rel="Home page">
                <img src="images/ico_home.png" 
                     style="vertical-align:middle;"/> Home</li>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="userScheduleMaker.php" 
               target="_self"><li rel="Create schedule">
                               <img src="images/ico_makeSchedule.png" 
                                    style="vertical-align:middle;"/> Schedule Maker
                             </li>
            </a>
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
            <a href="help/officeHoursHelp.html" target="_new">
                <li rel="Web site Help">
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
        echo "<script>$('#errorMessage').css('display','none');</script>";
        $javascript = (isset($_GET['javascript']))?$_GET['javascript']:'';
        if($javascript == 'no'):
            echo "<script>$('#errorMessage').css('display','block');</script>";
        ?>
            <div id="noScriptMessage">
                <strong>JavaScript is disabled.</strong><br/>
                The Office Hours Web site needs the JavaScript Enabled to fully work. 
                Please turn it on and <b>
                    <a href='userControlPanel.php'
                       style="color: yellow">reload the page.
                    </a></b>            
            </div>
        <?
        endif;
        ?>
    </div>          
    <div id="middleDiv">
    </div>
    <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
