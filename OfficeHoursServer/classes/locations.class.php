<!-- locations.class.php
        PROG8105-1 Systems Project: DAL locations class

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma and Vandana Sharma,  2012.03.18: Created
-->
<?
// This DAL class handles all the system requests for the boardLocations table
class Locations
{
    // Attributes
    var $locationCode;   
    var $locationDetails;
    var $branchId;
    var $organizationId;
	
    // Construct for the Locations object
    function __construct($locationCode,$locationDetails,$branchId,$organizationId){
        $this->locationCode = $locationCode;
        $this->locationDetails = $locationDetails;
        $this->branchId = $branchId;
        $this->organizationId = $organizationId;
    }

    /* Insert a location in the database
    *  Parameters: locationCode (char), locationDetails (char), branchId (int),
    *              organizationId (int)
    */    
    function Insert(){
        $query = "INSERT INTO boardLocations
                (locationCode,locationDetails,locationBranchId,locationOrganizationId)
                VALUES('$this->locationCode','$this->locationDetails',$this->branchId,$this->organizationId)";	
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: INSERT query failed.");
    }

    /* Update location information in the database
    *  Parameters: locationCode (char), locationDetails (char), branchId (int),
    *              organizationId (int)
    */
    function Update(){
        $query = "UPDATE boardLocations
                    SET locationDetails = '$this->locationDetails'
                    WHERE  locationOrganizationId = $this->organizationId
                    AND locationBranchId = $this->branchId
                    AND locationCode = '$this->locationCode'";
        include("includes/dbConnection.php");		
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: UPDATE query failed.");
    }

    /* Delete location from the database
    *  Parameters: branchId (int), locationCode (char), organizationId (int)
    */
    function Delete(){
        $query = "DELETE FROM boardLocations
                    WHERE locationOrganizationId = $this->organizationId
                    AND locationBranchId = $this->branchId
                    AND locationCode = '$this->locationCode'";
        include("includes/dbConnection.php");					
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: DELETE query failed.");
    }

    /* Delete all locations from a given organization ID and branch ID
    *  Parameters: branchId (int), organizationId (int)
    */
    function DeleteAllLocations(){
        $query = "DELETE FROM boardLocations
                    WHERE locationOrganizationId = $this->organizationId
                    AND locationBranchId = $this->branchId";
        include("includes/dbConnection.php");						
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: DELETE query failed.");
    }

    /* Select all branches and locations for a given branch ID and organization ID
    *  Parameters: branchId (int), organizationId (int)
    */
    function selectAll(){
        $query = "SELECT B.branchName, L.locationCode,L.locationDetails,L.locationBranchId
                      FROM branches as B, boardlocations as L
                      WHERE locationOrganizationId = $this->organizationId
                      AND locationBranchId LIKE '$this->branchId'
                      AND L.locationBranchId = B.branchId 
                      ORDER BY L.locationBranchId, L.locationCode";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }

    /* Select location for a given organization ID, branch ID and location code
    *  Parameters: branchId (int), organizationId (int)
    */
    function selectLocation(){ 
        $query = "SELECT * 
                      FROM boardlocations 
                      WHERE locationBranchId = $this->branchId 
                      AND locationOrganizationId = $this->organizationId
                      AND locationCode = '$this->locationCode'";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }

    /* Select all locations code for a given organization ID and branch ID
    *  Parameters: branchId (int), organizationId (int)
    */
    function populate(){
        $query = "SELECT locationCode
                    FROM boardLocations
                    WHERE locationOrganizationId = $this->organizationId
                    AND locationBranchId = $this->branchId";
        include("includes/dbConnection.php");						
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");	
        $locationsResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);	
        return $locationsResult;
    }
}	
?>
