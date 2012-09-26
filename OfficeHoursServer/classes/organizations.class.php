<?php
// This DAL class handles all the system requests for the organizations table
class Organizations
{
    // Attributes
    var $organizationId;
    var $organizationName;
    var $organizationAddress;
    var $organizationPhone;
    var $accessCode;
    var $fontHeadingColor;
    var $backgroundHeadingColor;
    var $fontGridOddColor;
    var $backgroundGridOddColor;        
    var $fontGridEvenColor;
    var $backgroundEvenGridColor; 
    var $fontBottomColor;
    var $backgroundBottomColor; 
    
    // Construct for the Organization object
    function __construct($organizationId,$organizationName,$organizationAddress,$organizationPhone,$accessCode,
            $fontHeadingColor,$backgroundHeadingColor,$fontGridOddColor,$backgroundGridOddColor,$fontGridEvenColor,
            $backgroundGridEvenColor,$fontBottomColor,$backgroundBottomColor){
        $this->organizationId = $organizationId;
        $this->organizationName = $organizationName;
        $this->organizationAddress = $organizationAddress;
        $this->organizationPhone = $organizationPhone;
        $this->accessCode = $accessCode;
        $this->fontHeadingColor = $fontHeadingColor;
        $this->backgroundHeadingColor = $backgroundHeadingColor;
        $this->fontGridOddColor = $fontGridOddColor;
        $this->backgroundGridOddColor = $backgroundGridOddColor;
        $this->fontGridEvenColor = $fontGridEvenColor;
        $this->backgroundGridEvenColor = $backgroundGridEvenColor;        
        $this->fontBottomColor = $fontBottomColor;
        $this->backgroundBottomColor = $backgroundBottomColor;    
    }

    /* Insert an organization in the database
    *  Parameters: organizationName (char), organizationAddress (char),
    *              organizationPhone (char), accessCode (char),
    *              fontHeadingColor (char), backgroundHeadingColor (char),
    *              fontGridOddColor (char), backgroundGridOddColor (char),
    *              fontGridEvenColor (char), backgroundGridEvenColor (char),
    *              fontBottomColor (char), backgroundBottomColor (char),       
    */
    function Insert(){
        $query = "INSERT INTO organizations
            (organizationName,organizationAddress,organizationPhone,accessCode,fontHeadingColor,backgroundHeadingColor,
            fontGridOddColor,backgroundGridOddColor,fontGridEvenColor,backgroundGridEvenColor,fontBottomColor,backgroundBottomColor)
            VALUES('$this->organizationName','$this->organizationAddress','$this->organizationPhone','$this->accessCode',
                '$this->fontHeadingColor','$this->backgroundHeadingColor','$this->fontGridOddColor','$this->backgroundGridOddColor',
                '$this->fontGridEvenColor','$this->backgroundGridEvenColor','$this->fontBottomColor','$this->backgroundBottomColor')";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: INSERT query failed.");
    }

    /* Select organization information for a given organizationName
    *  Parameters: organizationName (char)
    */
    function selectOrganization(){ 
        $query = "SELECT *
                    FROM organizations
                    WHERE organizationName = '$this->organizationName'";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }
    
    /* Select organization information for a given organization ID
    *  Parameters: organizationId (int)
    */
    function selectSettings(){ 
        $query = "SELECT *
                    FROM organizations
                    WHERE organizationId = $this->organizationId";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    } 
    
    /* Update organization information for a given organization ID
    *  Parameters: accessCode (char),
    *              fontHeadingColor (char), backgroundHeadingColor (char),
    *              fontGridOddColor (char), backgroundGridOddColor (char),
    *              fontGridEvenColor (char), backgroundGridEvenColor (char),
    *              fontBottomColor (char), backgroundBottomColor (char),       
    */    
    function updateSettings(){
        $query = "UPDATE organizations
                    SET accessCode = '$this->accessCode', 
                        fontHeadingColor = '$this->fontHeadingColor',
                        backgroundHeadingColor = '$this->backgroundHeadingColor',
                        fontGridOddColor = '$this->fontGridOddColor',
                        backgroundGridOddColor = '$this->backgroundGridOddColor',
                        fontGridEvenColor = '$this->fontGridEvenColor',
                        backgroundGridEvenColor = '$this->backgroundGridEvenColor',
                        fontBottomColor = '$this->fontBottomColor',
                        backgroundBottomColor = '$this->backgroundBottomColor'                 
                    WHERE organizationId = $this->organizationId";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: UPDATE query failed.");
    }
}	
?>