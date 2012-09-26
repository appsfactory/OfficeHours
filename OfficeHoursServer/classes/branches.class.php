<!-- branches.class.php
        PROG8105-1 Systems Project: DAL branches class

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma and Vandana Sharma,  2012.03.17: Created
-->
<?
// This DAL class handles all the system requests for the branches table
class Branches
{
    // Attributes
    var $branchName;
    var $branchAddress;
    var $branchPhone;
    var $branchId;
    var $organizationId;

    // Construct for the Branches object
    function __construct($branchId,$branchName,$branchAddress,$branchPhone,$organizationId){ 
        $this->branchName = $branchName;
        $this->branchAddress = $branchAddress;
        $this->branchPhone = $branchPhone;
        $this->branchId = $branchId;
        $this->organizationId = $organizationId;
    }
    
    /* Insert a branch in the database
    *  Parameters: branchName (char), branchAddress (char), branchPhone (char),
    *              organizationId (int)
    */
    function Insert(){
        $query = "INSERT INTO branches
                    (branchName,branchAddress,branchPhone,organizationId)
                    VALUES('$this->branchName','$this->branchAddress',
                           '$this->branchPhone',$this->organizationId)";	
        include("includes/dbConnection.php");
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: INSERT query failed.");
    }

    /* Update branch information in the database
    *  Parameters: branchName (char), branchAddress (char), branchPhone (char),
    *              branchId (int), organizationId (int)
    */
    function Update(){
        $query = "UPDATE branches
                    SET branchName = '$this->branchName', 
                        branchAddress = '$this->branchAddress',
                        branchPhone = '$this->branchPhone'
                    WHERE branchId = $this->branchId
                    AND organizationId = $this->organizationId";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: UPDATE query failed.");
    }

    /* Delete branch for a given branch ID and organization ID
    *  Parameters: branchId (int), organizationId (int)
    */
    function Delete(){
        $query = "DELETE FROM branches
                    WHERE branchId = $this->branchId
                    AND organizationId = $this->organizationId";
        include("includes/dbConnection.php");						
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: DELETE query failed.");
    }

    /* Select all branches from a given organization ID
    *  Parameters: organizationId (int)
    */
    function selectAll(){
        $query = "SELECT *
                    FROM branches
                    WHERE organizationId = $this->organizationId";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }

    /* Select branch for a given organization ID and branch ID
    *  Parameters: branchId (int), organizationId (int)
    */
    function selectBranch(){ 
        $query = "SELECT *
                    FROM branches
                    WHERE organizationId = $this->organizationId
                    AND branchId = $this->branchId";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }	

    /* Select branch by name for a given organization ID and branchName
    *  Parameters: branchName (char), organizationId (int)
    */
    function selectBranchByName(){ 
        $query = "SELECT *
                    FROM branches
                    WHERE organizationId = $this->organizationId
                    AND UPPER(branchName) = UPPER('$this->branchName')";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }        
}	
?>