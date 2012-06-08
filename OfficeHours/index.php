<!-- index.php
        PROG8105-1 Systems Project: Index Page

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma,
            Vandana Sharma,  2012.02.01: Created
-->
<?
    session_start('officeHours');
    session_destroy();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Office Hours</title>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" href="css/mainPage.css" type="text/css" media="all" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />    
    <script type="text/javascript" src="js/jquery-1.4.4.min.js" ></script>    
    <script type="text/javascript" src="js/functions.js"></script>    
    <noscript>
        <meta http-equiv="Refresh" content="1;url=index.php?javascript=no"/>
    </noscript>  
</head>

<body>
    <div id="mainDiv">
        <div id="topDiv" style="margin-bottom: 20px">
            <div id="mainPageHeaderDiv">
                <span>OFFICE <img src="images/boardImage.png" 
                                  style="vertical-align:middle;"/> HOURS</span>
            </div>
            <div id="sitePanelNameDiv">
                <span>Welcome &bull; Bienvenue</span>                
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
                        <a href='index.php' 
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
            ?>
        </div>  
        <div id="middleDiv">
            <div id="middleLeftDiv">
                <div id="about">
                    Office Hours, is an open-source software program, that allows
                    anyone who wants to post their office hours on screens to do so.
                    The software has been developed especially for the academic setting
                    for faculty to publicize their office hours. 
                    The software was developed by Charles Borras, Will Carvahlo,
                    Huilong Ma, and Vandana Sharma to meet their systems project requirement
                    to graduate in the Computer Applications Development (CAD) Program at 
                    Conestoga College - Ontario, Canada. 
                    The software was built for the group's client, Communitech. Working over
                    a period of 16 weeks with their Faculty Advisor, Dalibor Dvorski.
                    As the software is open-source, it can be further modified for use
                    in schools, clinics, law offices and so on.
                </div>
            </div>
            <div id="middleRightDiv">
                <div id="loginCreateButtomDiv">
                    <table>
                        <tr>
                            <td id="logInHeader">
                                What would you like?
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <input id="loginButton" type="button" 
                                       value="Log In to Office Hours" 
                                       style="width:210px" 
                                       rel="Log In to Office Hours"/>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <input id="createButton" 
                                       type="button" 
                                       value="Create my Organization" 
                                       style="width:210px" 
                                       rel="Create Organization"/>
                            </td>
                        </tr>
                    </table>    
                </div>                
                <div id="logInDiv">
                    <form name="entryForm" id="entryForm" 
                          method="POST" action="admCRUD.php" 
                          enctype="multipart/form-data" 
                          onsubmit="return validateLogin()">
                    <table>
                        <tr>
                            <td id="logInHeader">
                                Log In
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Organization<br/>
                                <input id="organizationName" 
                                       type="text" 
                                       name="organizationName" 
                                       size="30" maxlenth="20" 
                                       rel="Organization name"/>  
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Access Code<br/>
                                <input id="accessCode" type="password" 
                                       name="accessCode" size="30" 
                                       maxlenth="20" rel="Acess code"/>
                            </td>
                        </tr>                        
                        <tr>
                            <td>
                                Username<br/>
                                <input id="userName" type="text" name="userName" 
                                       size="30" maxlenth="20" rel="Username"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Password<br/>
                                <input id="userPassword" type="password" 
                                       name="userPassword" size="30" 
                                       maxlenth="20" rel="User password"/>
                            </td>
                        </tr>  
                        <tr>
                            <td align="right">
                                <input type="submit" value="Log In" rel="Log In"/>
                                <input class="cancelButton" type="reset" 
                                       value="Cancel" rel="Cancel and close"/>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="action" value="logIn" />
                    <input type="hidden" name="table" value="organizations" />                        
                    </form>
                </div>

                <div id="createNewDiv">
                    <form name="entryForm" id="entryForm" method="POST" 
                          action="admCRUD.php" enctype="multipart/form-data" 
                          onsubmit="return validateCreateOrganization()">
                    <table>
                        <tr>
                            <td id="logInHeader">
                                New Organization
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Organization Name<br/>
                                <input id="newOrganizationName" type="text" 
                                       name="newOrganizationName" size="30" 
                                       maxlenth="20" rel="Organization name"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Address<br/>
                                <input id="organizationAddress" type="text" 
                                       name="organizationAddress" size="30" 
                                       maxlenth="50" rel="Organization address"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Phone<br/>
                                <input id="organizationPhone" type="text" 
                                       name="organizationPhone" size="30" 
                                       maxlenth="15" rel="Organization phone"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Administrator Username<br/>
                                <input id="adminUserName" type="text" 
                                       name="adminUserName" size="30" 
                                       maxlenth="20" rel="Administrator username"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Password<br/>
                                <input id="adminPassword" type="password" 
                                       name="adminPassword" size="30" 
                                       maxlenth="20" rel="Administrator password"/>
                            </td>      
                        </tr>  
                        <tr>
                            <td>
                                First Name<br/>
                                <input id="adminFirstName" type="text" 
                                       name="adminFirstName" size="30" 
                                       maxlenth="20" rel="Administrator first name"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Last Name<br/>
                                <input id="adminLastName" type="text" 
                                       name="adminLastName" size="30" maxlenth="20" 
                                       rel="Administrator last name"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                E-mail<br/>
                                <input id="adminEmail" type="text" 
                                       name="adminEmail" 
                                       size="30" maxlenth="20" 
                                       rel="Administrator e-mail"/>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <input type="submit" value="Create" 
                                       rel="Create organization"/>
                                <input class="cancelButton" type="reset" 
                                       value="Cancel" rel="Cancel and close"/>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="action" value="create" />
                    <input type="hidden" name="table" value="organizations" />
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
