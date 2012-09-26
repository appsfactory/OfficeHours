/*  functions.js
        PROG8105-1 Systems Project: Jquery and Javascript functions

    Revision History
        Charles Borras, Will Carvalho, Huilong Ma and Vandana Sharma,  2012.02.28: Created
*/

// All functions within this block will be activated and executaded when the page loads  
$(document).ready(function(){

    /* The following block enables or disables check boxes if the user selects 
    *  create the schedule Mon-Fri or pick the especific days  
    */
    $('.daysCheckBox').attr("disabled", true);
    $("#monFriRadio").click(function(){
        $('.daysCheckBox').attr("disabled", true);
        $('.daysCheckBox').attr("checked", false); 
    })
    $("#copyScheduleRadio").click(function(){
        $('.daysCheckBox').attr("disabled", true);
        $('.daysCheckBox').attr("checked", false); 
    })
    $('#daysWeekRadio').click(function(){
        $('.daysCheckBox').attr("disabled", false);
        $('.daysCheckBox').attr("checked", false);            
    });
    $('#loginButton').click(function(){
        $('#createNewDiv').css('display','none');
        $('#logInDiv').css('display','block');
    });
    $('#createButton').click(function(){
        $('#createNewDiv').css('display','block');
        $('#logInDiv').css('display','none');
    });
    $('.cancelButton').click(function(){
        $('#createNewDiv').css('display','none');
        $('#logInDiv').css('display','none');
    });
    $('#errorButton').click(function(){
        $('#errorMessage').css('display','none');
    });

    // Assigns different colors to odd and even table lines  
    $('#boardSchedule tbody tr:odd').addClass('oddRow');		
    $('#boardSchedule tbody tr:even').addClass('evenRow');
    $('#branchesTableGrid tr:odd').addClass('oddRow');		
    $('#branchesTableGrid tr:even').addClass('evenRow');
    $('#scheduleTableGrid tr:odd').addClass('oddRow');		
    $('#scheduleTableGrid tr:even').addClass('evenRow');
    
    // Configures the office hours board clock 
    var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ]; 
    var dayNames= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]
    var newDate = new Date();
    newDate.setDate(newDate.getDate());
    $('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());
    setInterval( function() {
            var seconds = new Date().getSeconds();
            $("#sec").html(( seconds < 10 ? "0" : "" ) + seconds);
        },1000
    );
    setInterval( function() {
            var minutes = new Date().getMinutes();
            $("#min").html(( minutes < 10 ? "0" : "" ) + minutes);
        },1000
    );
    setInterval( function() {
            var hours = new Date().getHours();
            $("#hours").html(( hours < 10 ? "0" : "" ) + hours);
        }, 1000
    ); 
        
    // Hides / shows the dialog form box      
    $("#branchFormDiv").css('display','none');
    $("#locationFormDiv").css('display','none');
    $("#signInOutFormDiv").css('display','none'); 
    $("#myScheduleUpdateFormDiv").css('display','none');
    $("#userFormDiv").css('display','none');
    
    // Controls the tooltip based on the rel attribute 
    $(function(){
        $("*[rel]").hover(function(e){   								   
        $("body").append('<div class="tooltip">'+$(this).attr('rel')+'</div>');
        $('.tooltip').css({
            top : e.pageY + 20,
            left : (e.pageX)>= screen.width?screen.width - 50:e.pageX
        }).fadeIn('slow');        
        }, function(){
            $('.tooltip').remove();
        }).mousemove(function(e){
            $('.tooltip').css({
                top : e.pageY + 20,
                left : (e.pageX)>= screen.width?screen.width - 50:e.pageX
            })
        })
    });  
});  

// Reduces the flash when the page is refreshing   
function showSettingsDetails(show){
    $(document).ready
    (function(){
        if (show == true)
            $("#settingsFormDiv").fadeIn(100)
        else
            $("#settingsFormDiv").fadeOut(100);
    })
} 

// Show / hides the branch details dialog box 
function showBranchDetails(show){
    $(document).ready
    (function(){
        if (show == true)
            $("#branchFormDiv").fadeIn(100)
        else
            $("#branchFormDiv").fadeOut(100);
    })
}   

// Show / hides the location details dialog box 
function showLocationDetails(show){
    $(document).ready
    (function(){
        if (show == true)
            $("#locationFormDiv").fadeIn(100)
        else
            $("#locationFormDiv").fadeOut(100);
    })
} 

// Show / hides the sign In/out dialog box
function showSignInOutDetails(show){
    $(document).ready
    (function(){
        if (show == true)
            $("#signInOutFormDiv").fadeIn(100)
        else
            $("#signInOutFormDiv").fadeOut(100);
    })
}

// Show / hides the schedule details dialog box
function showScheduleDetails(show){
    $(document).ready
    (function(){
        if (show == true)
            $("#myScheduleUpdateFormDiv").fadeIn(100)
        else
            $("#myScheduleUpdateFormDiv").fadeOut(100);
    })
}

// Show / hides the user details dialog box
function showUserDetails(show){
    $(document).ready
    (function(){
        if (show == true)
            $("#userFormDiv").fadeIn(100)
        else
            $("#userFormDiv").fadeOut(100);
    })
} 

// Show / hides the activation login dialog box
function showLaunchDetails(show){
    $(document).ready
    (function(){
        if(show == true){
            $("#logInActivationDiv").css('display','none'); 
            $("#launchFormDiv").fadeIn(100)}
        else{
            $("#launchFormDiv").fadeOut(100)}
    })
}

// Show / hides the change password dialog box
function showPasswordUpdate(show){
    $(document).ready
    (function(){
        if(show == true){
            $("#passwordFormDiv").fadeIn(100)}
        else{
            $("#passwordFormDiv").fadeOut(100)}
    })    
}

// Validates the log In and shows all error found on a pop up box
function validateLogin(){
    var inputErrorMessage = "Please provide the following information:\n";
    var inputErrors = new Array();
    
    if($.trim($("#organizationName").val())=='')
        inputErrors.push("Organization Name");
    if($.trim($("#accessCode").val())=='')
        inputErrors.push("Access Code");
    if($.trim($("#userName").val())=='')
        inputErrors.push("Username"); 
    if($.trim($("#userPassword").val())=='')
        inputErrors.push("Password"); 
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }    
}

// Validates the activation login and shows all error found on a pop up box
function validateActivation(){
    var inputErrorMessage = "Please provide the following information:\n";
    var inputErrors = new Array();
    
    if($.trim($("#organizationName").val())=='')
        inputErrors.push("Organization Name");
    if($.trim($("#accessCode").val())=='')
        inputErrors.push("Access Code");
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }    
}

// Validates the create organization and shows all error found on a pop up box
function validateCreateOrganization(){
    var inputErrorMessage = "Please provide the following information:\n";
    var inputErrors = new Array();
    
    if($.trim($("#newOrganizationName").val())=='')
        inputErrors.push("Organization Name");
    if($.trim($("#adminUserName").val())=='')
        inputErrors.push("Administrator Username"); 
    if($.trim($("#adminPassword").val())=='')
        inputErrors.push("Password"); 
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }    
}

// Validates the branch information and shows all error found on a pop up box
function validateBranch(){
    var inputErrorMessage = "Please provide the following information:\n";
    var inputErrors = new Array();
    var regularExpression=/^[A-Z0-9\s]*$/i;
    
    if($.trim($("#branchName").val())=='')
        inputErrors.push("Branch Name");
    if(!regularExpression.test($.trim($("#branchName").val())))   
        inputErrors.push("Allowed only letters and numbers for Branch Name");
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }  
}

// Validates the location information and shows all error found on a pop up box
function validateLocation(){
    var inputErrorMessage = "Please provide the following information:\n";
    var inputErrors = new Array();
    var regularExpression=/^[A-Z0-9\s]*$/i;    

    if($.trim($("#branchId").val())=='')
        inputErrors.push("Branch");
    if($.trim($("#locationCode").val())=='')
        inputErrors.push("Location Code");
    if(!regularExpression.test($.trim($("#locationCode").val())))   
        inputErrors.push("Allowed only letters and numbers for Location Code");    
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }    
}

// Validates the user information and shows all error found on a pop up box
function validateUser(){
    var inputErrorMessage = "Please provide the following information:\n";
    var inputErrors = new Array();
    var regularExpression=/^[A-Z]*$/i;   
    var emailRegularExpression = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    
    if($.trim($("#userName").val())=='')
        inputErrors.push("Username");
    if(!regularExpression.test($.trim($("#userName").val())))   
        inputErrors.push("Allowed only letters for Username");       
    if($.trim($("#firstName").val())=='')
        inputErrors.push("First Name"); 
    if(!regularExpression.test($.trim($("#firstName").val())))   
        inputErrors.push("Allowed only letters for First Name");    
    if($.trim($("#lastName").val())=='')
        inputErrors.push("Last Name"); 
    if(!regularExpression.test($.trim($("#lastName").val())))   
        inputErrors.push("Allowed only letters for Last Name");      
    if($.trim($("#email").val())!=''){
        if(!emailRegularExpression.test($.trim($("#email").val())))   
            inputErrors.push("Email invalid");          
    }
    if($.trim($("#password").val())=='')
        inputErrors.push("Temporary Password");
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }    
}

// Validates the password information  and shows all error found on a pop up box
function validatePassword(){
    var inputErrorMessage = "Please provide the following information:\n";
    var password1 = $.trim($("#newPassword").val());
    var password2 = $.trim($("#confirmPassword").val());
    var inputErrors = new Array();
    
    if(password1=='')
        inputErrors.push("New Password");
    if(password2=='')
        inputErrors.push("Password Confirmation"); 
    if(password1 != password2)
        inputErrors.push("Password and Confirmation must be the same"); 
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }    
}

// Validates the settings and shows all error found on a pop up box
function validateSettings(){
    var inputErrorMessage = "Please provide the following information:\n";
    var inputErrors = new Array();

    if($.trim($("#accessCode").val())=='')
        inputErrors.push("Access Code");
    if($.trim($("#fontHeadingColor").val())=='')
        inputErrors.push("Heading Font Color - Format Hex: #XXXXXX"); 
    if($.trim($("#backgroundHeadingColor").val())=='')
        inputErrors.push("Heading Background Color - Format Hex: #XXXXXX"); 
    if($.trim($("#fontGridOddColor").val())=='')
        inputErrors.push("Board Odd Lines Font Color - Format Hex: #XXXXXX"); 
    if($.trim($("#backgroundGridOddColor").val())=='')
        inputErrors.push("Board Odd Lines Backgroung Color - Format Hex: #XXXXXX");
    if($.trim($("#fontGridEvenColor").val())=='')
        inputErrors.push("Board Even Lines Font Color - Format Hex: #XXXXXX"); 
    if($.trim($("#backgroundGridEvenColor").val())=='')
        inputErrors.push("Board Even Lines Backgroung Color - Format Hex: #XXXXXX");    
    if($.trim($("#fontBottomColor").val())=='')
        inputErrors.push("Footer Font Color - Format Hex: #XXXXXX"); 
    if($.trim($("#backgroundBottomColor").val())=='')
        inputErrors.push("Footer Backgroung Color - Format Hex: #XXXXXX");        
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }    
}

// Validates if the final time is greater than starting time and shows a error message on a pop up box
function isValidTimeInputted(){
    var startingTime = parseInt($('#startingTimeHH').val() + $('#startingTimeMM').val());
    var finishingTime = parseInt($('#finishingTimeHH').val() + $('#finishingTimeMM').val());

    //if(finishingTime <= startingTime) return false;   
    return true;
}

// Validates if the final date is greater than starting date and shows a error message on a pop up box
function isValidDateInputted(){
    var fromDate = $('#txt_fromDate').val();
    var toDate = $('#txt_toDate').val(); 
    
    if(fromDate != "" && toDate != ""){
        var fromDateInteger = parseInt(fromDate.split("/")[2].toString() + fromDate.split("/")[0].toString() + fromDate.split("/")[1].toString());  
        var toDateInteger = parseInt(toDate.split("/")[2].toString() + toDate.split("/")[0].toString() + toDate.split("/")[1].toString() );         
    }  
    if (fromDateInteger > toDateInteger) return false;
    return true;  
} 

// Validates the schedule created by the user and shows a error message on a pop up box
function validateMakeSchedule(){
    var inputErrorMessage = "Please provide the following information:\n";
    var inputErrors = new Array();

    if($.trim($("#txt_fromDate").val())=='')
        inputErrors.push("Starting date");
    if($.trim($("#txt_toDate").val())=='')
        inputErrors.push("Finishing date");
    if($.trim($("#branchId").val())=='')
        inputErrors.push("Branch");
    if($.trim($("#txt_locationCode").val())=='' || $.trim($("#txt_locationCode").val())=='%' )
        inputErrors.push("Location");
    if(!isValidTimeInputted())
        inputErrors.push("Finishing time cannot be less than starting time");
    if(!isValidDateInputted())
        inputErrors.push("Final date cannot be less than starting date");
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }    
}

// Validates the day schedule created by the user and shows a error message on a pop up box
function validateMySchedule(){
    var inputErrorMessage = "Please provide the following information:\n";
    var inputErrors = new Array();

    if($.trim($("#txt_date").val())=='')
        inputErrors.push("Date");
    if($.trim($("#branchIdForm").val())=='')
        inputErrors.push("Branch");
    if($.trim($("#txt_locationCodeForm").val())=='')
        inputErrors.push("Location");
    if(!isValidTimeInputted())
       inputErrors.push("Finishing time cannot be less than starting time");
    for(i=0;i<inputErrors.length;i++)
            inputErrorMessage+="\n- "+inputErrors[i];
    if(inputErrors.length==0){
        return true;
    }else{
        alert(inputErrorMessage);
        return false;
    }    
}

// Reset the office hours board colors with all defaults color 
function resetColors(){
    $("#fontHeadingColor").attr('value','#54e4f2');
    $("#fontHeadingColor").css('background-color','#54e4f2');
    $("#fontHeadingColor").css('color','#FFFFFF');
    
    $("#backgroundHeadingColor").attr('value','#1d30b9');
    $("#backgroundHeadingColor").css('background-color','#1d30b9');
    $("#backgroundHeadingColor").css('color','#FFFFFF');
    
    $("#fontGridOddColor").attr('value','#FFFFFF');
    $("#fontGridOddColor").css('background-color','#FFFFFF');
    $("#fontGridOddColor").css('color','#000000');    
    
    $("#backgroundGridOddColor").attr('value','#5f94d8');
    $("#backgroundGridOddColor").css('background-color','#5f94d8');
    $("#backgroundGridOddColor").css('color','#FFFFFF');
    
    $("#fontGridEvenColor").attr('value','#FFFFFF'); 
    $("#fontGridEvenColor").css('background-color','#FFFFFF');
    $("#fontGridEvenColor").css('color','#000000');     
    
    $("#backgroundGridEvenColor").attr('value','#000000')
    $("#backgroundGridEvenColor").css('background-color','#000000');
    $("#backgroundGridEvenColor").css('color','#FFFFFF');    
    
    $("#fontBottomColor").attr('value','#000000') 
    $("#fontBottomColor").css('background-color','#000000');
    $("#fontBottomColor").css('color','#FFFFFF');    
    
    $("#backgroundBottomColor").attr('value','#FFFFFF')   
    $("#backgroundBottomColor").css('background-color','#FFFFFF');
    $("#backgroundBottomColor").css('color','#000000');     
    return true
}

// Validates if the final time is greater than starting time and shows a error message on a pop up box
function compareStartingAndFinishingTime(){
 /*   
    var startingTime = parseInt($('#startingTimeHH').val() + $('#startingTimeMM').val());
    var finishingTime = parseInt($('#finishingTimeHH').val() + $('#finishingTimeMM').val());
  
    if (startingTime > 0 && finishingTime > 0){ 
        if(finishingTime <= startingTime){
          alert("Finishing time cannot be less than starting time");  
          return false;   
        }
    }
*/    
    return true;
}   

// Validates if the final date is greater than starting date and shows a error message on a pop up box
function compareFromAndToDates(){
    var fromDate = $('#txt_fromDate').val();
    var toDate = $('#txt_toDate').val(); 
    var fromDateInteger = parseInt(fromDate.split("/")[2].toString() + fromDate.split("/")[0].toString() + fromDate.split("/")[1].toString());  
    var toDateInteger = parseInt(toDate.split("/")[2].toString() + toDate.split("/")[0].toString() + toDate.split("/")[1].toString() );  
  
    if (fromDateInteger > toDateInteger) {  
        alert("Final date cannot be less than starting date");
        return false;
    }
    return true;  
}    

// Reduces the refresh effect
function LoadFadeInBody(){
   $("#boardGrid").fadeIn(100); 
}

// Reduces the refresh effect
function LoadFadeOutBody(){
   $("#boardGrid").fadeOut(100); 
}