<!-- admControlPanel.php
        PROG8105-1 Systems Project: Administrator Control Panel

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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Administrator Control Panel</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <script type="text/javascript" src="js/jquery-1.4.4.min.js" ></script>    
    <script type="text/javascript" src="js/functions.js"></script>     
    <link rel="stylesheet" href="css/admControlPanel.css" type="text/css" media="all" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />     
    <noscript>
        <meta http-equiv="Refresh" 
              content="1;url=admControlPanel.php?javascript=no"/>
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
            <li id="currentPage" rel="Home page">
                <img src="images/ico_home.png" 
                     style="vertical-align:middle;"/> Home</li>
            <li><img src="images/menuDivider.gif" 
                     style="vertical-align:middle;"/></li>
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
            <a href="index.php" target="_self" ><li rel="Log Out">
                    <img src="images/ico_logout.png" 
                         style="vertical-align:middle;"/> Log Out</li></a>  
            <li><img src="images/menuDivider.gif" 
                     style="vertical-align:middle;"/></li>                         
        </ul>
    </div>

    <div id="errorMessage">
        <?
        // Checks if JavaSript is activated
        echo "<script>$('#errorMessage').css('display','none');</script>";
        $javascript = (isset($_GET['javascript']))?$_GET['javascript']:'';
        if($javascript == 'no'):
            echo "<script>$('#errorMessage').css('display','block');</script>";
        ?>
            <div id="noScriptMessage">
                <strong>JavaScript is disabled.</strong><br/>
                The Office Hours Web site needs the JavaScript 
                Enabled to fully work.Please turn it on and
                <b><a href='admControlPanel.php' 
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
