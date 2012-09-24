<!-- boardActivation.php
        PROG8105-1 Systems Project: activates the office hours board

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma, 2012.03.12: Created
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Office Hours Board</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="css/mainPage.css" type="text/css" media="all" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />     
    <script type="text/javascript" src="js/jquery-1.4.4.min.js" ></script>    
    <script type="text/javascript" src="js/functions.js"></script>  
    <noscript>
        <meta http-equiv="Refresh" content="1;url=boardActivation.php?javascript=no"/>
    </noscript> 
    <?
    // Checks if the organization and access code where validated
    require_once("includes/dbConnection.php");
    $access  = (isset($_GET['access']))?$_GET['access']:'';
    
    // If logged in, branches and locations dropbox are populated
    if($access == 'ok'):
        $organizationId = $_GET['organizationId'];
        $query = "SELECT * FROM branches WHERE organizationId = $organizationId ORDER BY branchName";
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $branchesResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);

        $query = "SELECT * 
              FROM boardlocations 
              WHERE locationOrganizationId = $organizationId";
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $locationsResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);  
        echo "<script>showLaunchDetails(true)</script>";
    endif;
    ?>  
    <script type="text/javascript" charset="utf-8">
        //Populate locations dropbox using Ajax in the search form
        $(document).ready(function(){
            $('select[name=branchId]').change(function(){
                $('select[name=txt_locationCode]').html('<option value="0">Loading...</option>');

            $.post('ajaxLocations.php', 
            {branchId:$(this).val(),organizationId:<?=$organizationId?>},
                 function(value){
                      $('select[name=locationCode]').html(value);}
             )
            })
        });       
        //Activate the calendar
        $(function(){
          $('.date-pick').datePicker({autoFocusNextInput: true});
        });        
    </script>      
</head>
<body>
<div id="mainDiv">
    <div id="topDiv">
        <div id="mainPageHeaderDiv">
            <span>OFFICE <img src="images/boardImage.png" 
                              style="vertical-align:middle;"/> HOURS</span>
        </div>
        <div id="sitePanelNameDiv">
            <span>Office Hours Board Activation</span>                
        </div>            
    </div>
    <div id="menuDiv">
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
                    <a href='boardActivation.php' 
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
        ?>
    </div>        
    <div id="middleDiv">
        <div id="middleLeftDiv">
        </div>
        <div id="middleRightDiv">
            <div id="logInActivationDiv">
                <form name="logInForm" id="logInForm" method="POST" action="admCRUD.php" 
                      enctype="multipart/form-data" onsubmit="return validateActivation()">
                <table>
                    <tr>
                        <td id="logInHeader">
                            Log In
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Organization<br/>
                            <input id="organizationName" type="text" 
                                   name="organizationName" size="30" 
                                   maxlenth="20" rel="Organization name"/>
                        </td>
                    </tr>
                     <tr>
                        <td>
                            Access Code<br/>
                            <input id="accessCode" type="password" name="accessCode" 
                                   size="30" maxlenth="20" rel="Acess code"/>
                        </td>
                    </tr>  
                    <tr>
                        <td align="right">
                            <input type="submit" value="Log In" rel="Log In"/>
                            <input class="cancelButton" type="reset" value="Cancel" 
                                   rel="Cancel and close"/>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="activation" />
                <input type="hidden" name="table" value="organizations" />                        
                </form>
            </div>

            <div id="launchFormDiv">
                <form name="launchForm" id="launchForm" method="GET" 
                      action="officeHoursBoard.php" enctype="multipart/form-data">
                <table>
                    <tr>
                        <td id="logInHeader">
                            Launch Office Hours Board
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Select a Branch<br/>
                            <select name="branchId" id="branchId" 
                                    style="width:210px; font-size: 14px; padding: 3px;" 
                                    rel="Select branch">
                                <option value="%">&laquo;&nbsp; All Branches &nbsp;&raquo;</option>
                                <?	
                                for ($i=0;$i<count($branchesResult);$i++):
                                    echo "<option value=".$branchesResult[$i]['branchId'].">".$branchesResult[$i]['branchName']."</option>";
                                endfor;
                                ?>
                            </select>                                
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Select a Location<br/>
                            <select name="locationCode" id="locationCode" 
                                    style="width:210px; font-size: 14px; padding: 3px;" 
                                    rel="Select location">
                                <option value="%">&laquo;&nbsp; All Locations &nbsp;&raquo;</option>
                                <?	
                                for ($i=0;$i<count($locationsResult);$i++):
                                    echo "<option value='".$locationsResult[$i]['locationCode']."'>".$locationsResult[$i]['locationCode']."</option>";
                                endfor;
                                ?>
                            </select>                                
                        </td>
                    </tr>
                    <tr>
                        <td align="right">
                            <input type="submit" value="Launch" 
                                   rel="Launch Office Hours Board "/>
                            <input class="cancelButton" type="reset" 
                                   value="Cancel" 
                                   onclick="window.location.href ='boardActivation.php'" 
                                   rel="Cancel and close"/>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="launch" />
                <input type="hidden" name="table" value="organizations" />
                <input type="hidden" name="organizationId" 
                       value="<?=$organizationId?>"/>
                </form>    
            </div>
        </div>
    </div>
    <div id="bottomDiv">
        <?include("includes/footer.php")?>            
    </div>
</div>
</body>
</html>
