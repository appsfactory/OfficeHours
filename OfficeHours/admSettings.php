<!-- admSettings.php
        PROG8105-1 Systems Project: Update access code and board colors

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.03.12: Created
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

// Includes Organizations class
require_once("classes/organizations.class.php");

// instantiate organizations class
$organization = new Organizations($organizationId,'','','','','','','','','','','','');
$result = $organization->selectSettings();

// Get all fields for a selected organization
$accessCode = $result[0]["accessCode"];
$fontHeadingColor = $result[0]["fontHeadingColor"];
$backgroundHeadingColor = $result[0]["backgroundHeadingColor"];
$fontGridOddColor = $result[0]["fontGridOddColor"];
$backgroundGridOddColor = $result[0]["backgroundGridOddColor"];
$fontGridEvenColor = $result[0]["fontGridEvenColor"];
$backgroundGridEvenColor = $result[0]["backgroundGridEvenColor"];
$fontBottomColor = $result[0]["fontBottomColor"];
$backgroundBottomColor = $result[0]["backgroundBottomColor"];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Administrator Control Panel</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="css/admControlPanel.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/farbtastic.css" type="text/css" /> 
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />     
    <script type="text/javascript" src="js/jquery-1.4.4.min.js" ></script>
    <script type="text/javascript" src="js/functions.js"></script> 
    <script type="text/javascript" src="js/jqueryTools.js"></script> 
    <script type="text/javascript" src="js/farbtastic.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("input").focus(function () {
                 var id = '#'+$(this).attr("id");
                 var colorPicker = '#colorpicker' + $(this).attr("id");
                 $(colorPicker).css('display','block');
                 $(colorPicker).farbtastic(id);
            });
            $("input").blur(function () {
                 var id = '#'+$(this).attr("id");
                 var colorPicker = '#colorpicker' + $(this).attr("id");
                 $(colorPicker).css('display','none');
            });            
        });
    </script>
    <noscript>
        <meta http-equiv="Refresh" content="1;url=admSettings.php?javascript=no"/>
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
            <a href="admUsers.php" 
               target="_self"><li rel="Create, Update and Delete Users">
                    <img src="images/ico_users.png" 
                         style="vertical-align:middle;"/> Users</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <li id="currentPage" rel="Web site Settings">
                <img src="images/ico_settings.png" 
                     style="vertical-align:middle;"/> Settings</li>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="help/officeHoursHelp.html" 
               target="_new" ><li rel="Web site Help">
                    <img src="images/ico_help.png" 
                         style="vertical-align:middle;"/> Help</li></a>
            <li><img src="images/menuDivider.gif"/></li>
            <a href="index.php" target="_self" >
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
                    <a href='admSettings.php' 
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
        <div id="settingsFormDiv">
            <div class="detailsFormHeaderDiv">EDIT SETTINGS</div>
            <form name="entryForm" id="entryForm" method="POST" action="admCRUD.php" 
                  enctype="multipart/form-data" onsubmit="return validateSettings()">
                <table id="detailsTable">
                    <tr>
                        <td align="right">Access Code:</td>
                        <td><input id="accessCode" type="text" name="accessCode" 
                                   rel="Access Code" size="20" 
                                   maxlenth="20" value="<?=$accessCode?>"/></td>
                    </tr>
                    <tr>
                        <td align="right">Font Heading Color:</td>
                        <td>
                            <input id="fontHeadingColor" type="text" 
                                   name="fontHeadingColor" rel="Font heading color" 
                                   size="20" maxlenth="6" value="<?=$fontHeadingColor?>" 
                                   style="background-color: <?=$fontHeadingColor?>"/>

                        </td>
                    </tr>
                    <tr>
                        <td align="right">Background Heading Color:</td>
                        <td>
                            <input id="backgroundHeadingColor" type="text" 
                                   name="backgroundHeadingColor" 
                                   rel="Background heading color" size="20" 
                                   maxlenth="6" value="<?=$backgroundHeadingColor?>" 
                                   style="background-color: <?=$backgroundHeadingColor?>"/>

                        </td>
                    </tr>
                    <tr>
                        <td align="right">Font Odd Line Color:</td>
                        <td>
                            <input id="fontGridOddColor" type="text" 
                                   name="fontGridOddColor" 
                                   rel="Font grid color" size="20" 
                                   maxlenth="6" value="<?=$fontGridOddColor?>" 
                                   style="background-color: <?=$fontGridOddColor?>"/>

                        </td>
                    </tr>
                    <tr>
                        <td align="right">Background Odd Line Color:</td>
                        <td>
                            <input id="backgroundGridOddColor" type="text" 
                                   name="backgroundGridOddColor" 
                                   rel="Background grid color" size="20" maxlenth="6" 
                                   value="<?=$backgroundGridOddColor?>" 
                                   style="background-color: <?=$backgroundGridOddColor?>"/>

                        </td>
                    </tr>
                    <tr>
                        <td align="right">Font Even Line Color:</td>
                        <td>
                            <input id="fontGridEvenColor" type="text" 
                                   name="fontGridEvenColor" rel="Font grid even color" 
                                   size="20" maxlenth="6" value="<?=$fontGridEvenColor?>" 
                                   style="background-color: <?=$fontGridEvenColor?>"/>

                        </td>
                    </tr>
                    <tr>
                        <td align="right">Background Even Line Color:</td>
                        <td>
                            <input id="backgroundGridEvenColor" type="text" 
                                   name="backgroundGridEvenColor" 
                                   rel="Backround grid even color" size="20" 
                                   maxlenth="6" value="<?=$backgroundGridEvenColor?>" 
                                   style="background-color: <?=$backgroundGridEvenColor?>"/>

                        </td>
                    </tr>                        
                    <tr>
                        <td align="right">Font Bottom Color:</td>
                        <td>
                            <input id="fontBottomColor" type="text" 
                                   name="fontBottomColor" rel="Font bottom color"
                                   size="20" maxlenth="6" value="<?=$fontBottomColor?>" 
                                   style="background-color: <?=$fontBottomColor?>"/>

                        </td>
                    </tr>
                    <tr>
                        <td align="right">Background Bottom Color:</td>
                        <td>
                            <input id="backgroundBottomColor" type="text" 
                                   name="backgroundBottomColor" 
                                   rel="Backgound bottom color" size="20" 
                                   maxlenth="6" value="<?=$backgroundBottomColor?>"  
                                   style="background-color: <?=$backgroundBottomColor?>"/>

                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">
                            <input type='submit' value='Save' rel="Update settings"/>
                            <input type='reset' value='Cancel' 
                                   onclick="window.location.href ='admControlPanel.php'" 
                                   rel="Cancel and close"/>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">
                            <b><a href="#" style="color:red;" onclick="resetColors()" 
                                  rel="Assign default colors." >Reset to default colors.</a> </b>  
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="upd" />
                <input type="hidden" name="table" value="organizations" />
            </form>
        </div> 
        <div id="colorPickerDiv" style="float:right; margin-top: 10px; margin-right: 50px;">
            <div id="colorpickerfontHeadingColor"></div>
            <div id="colorpickerbackgroundHeadingColor"></div>
            <div id="colorpickerfontGridOddColor"></div>
            <div id="colorpickerbackgroundGridOddColor"></div>
            <div id="colorpickerfontGridEvenColor"></div>
            <div id="colorpickerbackgroundGridEvenColor"></div>
            <div id="colorpickerfontBottomColor"></div>
            <div id="colorpickerbackgroundBottomColor"></div>
        </div>
    </div>
    <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
