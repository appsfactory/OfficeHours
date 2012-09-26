<?php
// This DAL class handles all the system requests for the userschedules table
class UserSchedules
{
    // Attributes
    var $userName;
    var $organizationId;
    var $branchId;
    var $locationCode;
    var $date;
    var $fromDate;
    var $toDate;
    var $startingTime;
    var $signedIn;
    var $finishingTime;
    var $signedOut;
    var $status;
    var $referenceId;
    
    // Construct for the UserSchedules object
    function __construct($userName,$organizationId,$branchId,$locationCode,$date,$fromDate,$toDate,$startingTime,$signedIn,$finishingTime,$signedOut,$status,$referenceId){
        $this->userName = userName;
        $this->organizationId = organizationId;
        $this->branchId = branchId;
        $this->locationCode = locationCode;
        $this->date = date;
        $this->fromDate = fromDate;
        $this->toDate = toDate;
        $this->startingTime = startingTime;
        $this->signedIn = signedIn;
        $this->finishingTime = finishingTime;
        $this->signedOut = signedOut;
        $this->status = status;
        $this->referenceId = referenceId;
    }

    /* Insert a schedule in the database
    *  Parameters: userName (char), organizationId (int),
    *              branchId (int), accessCode (char),
    *              fontHeadingColor (char), locationCode (char),
    *              date (date), startingTime (time),
    *              signedIn (time), finishingTime (time),
    *              signedOut (time), status (char),       
    */
    function Insert(){
        $query = "INSERT INTO userschedules
                    (userName,organizationId,branchId,locationCode,date,startingTime,signedIn,finishingTime,signedOut,status)
                    VALUES('$this->userName',$this->organizationId,$this->branchId,'$this->locationCode','$this->date',
                        '$this->startingTime','$this->signedIn','$this->finishingTime','$this->signedOut','$this->status')";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: INSERT query failed.");
    }

    /* Update a schedule in the database
    *  Parameters: branchId (int), locationCode (char),
    *              date (date), accessCode (char),
    *              startingTime (time), finishingTime (time),
    *              status (char), referenceId (int), organizationId (int)    
    */
    function Update(){
        $query = "UPDATE userschedules
                    SET branchId = $this->branchId, 
                        locationCode = '$this->locationCode',
                        date = '$this->date',
                        startingTime = '$this->startingTime',
                        finishingTime = '$this->finishingTime',
                        status = '$this->status'
                    WHERE referenceId = $this->referenceId
                    AND organizationId = $this->organizationId";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: UPDATE query failed.");
    }
	
    /* Delete a schedule for a given reference ID and organization ID
    *  Parameters: referenceId (int), organizationId (int)
    */
    function Delete(){
        $query = "DELETE FROM userschedules
                    WHERE referenceId = $this->referenceId
                    AND organizationId = $this->organizationId";
        include("includes/dbConnection.php");						
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: DELETE query failed.");
    }
	
    /* Select all schedules for a given organization ID, userName and a period 
    *  Parameters: organizationId (int), userName (char), fromDate (date),
    *  toDate (date)  
    */
    function selectAll(){
        $query = "SELECT B.branchName, S.*
                    FROM branches AS B, userschedules AS S  
                    WHERE S.organizationId = $this->organizationId
                    AND B.organizationId = S.organizationId
                    AND B.branchId LIKE S.branchId
                    AND S.locationCode LIKE '$locationCode'    
                    AND S.userName = '$this->userName'
                    AND S.date BETWEEN '".date('Y-m-d',strtotime($this->fromDate))."' AND '".date('Y-m-d',strtotime($this->toDate))."'
                    ORDER BY S.date, S.branchId, S.locationCode";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }
}	
?>