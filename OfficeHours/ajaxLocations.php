<!-- ajaxLocations.php
        PROG8105-1 Systems Project: Populate locations drop box from branches
            asynchronously 

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma
            Vandana Sharma, 2012.03.26: Created
-->
<?
/* Gets all locations for a given branch ID and organization ID
*  and populates the location dropbox
*/ 
$branchId = $_POST['branchId'];
if($branchId != '%'):
    $organizationId = $_POST['organizationId'];
    $query = "SELECT locationCode
                FROM boardlocations 
                WHERE locationBranchId = $branchId
                AND locationOrganizationId = $organizationId";
    include("includes/dbConnection.php");
    $executeQuery = $db->prepare($query);
    $executeQuery->execute() or exit("Error: SELECT query failed.");
    $locationsResult = $executeQuery->fetchAll(PDO::FETCH_BOTH);  
    echo "<option value='%'>&laquo;&nbsp; All Locations &nbsp;&raquo;</option>";
    for ($i=0;$i<count($locationsResult);$i++):
        echo "<option value='".$locationsResult[$i]['locationCode']."'>".$locationsResult[$i]['locationCode']."</option>";      
    endfor; 
else:
    echo "<option value='%'>&laquo;&nbsp; All Locations &nbsp;&raquo;</option>";
endif;    
?>


