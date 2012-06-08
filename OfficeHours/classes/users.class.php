<!-- users.class.php
        PROG8105-1 Systems Project: DAL users class

     Revision History
        Charles Borras, Will Carvalho, Huilong Ma and Vandana Sharma,  2012.03.22: Created
-->
<?
// This DAL class handles all the system requests for the users table
class Users
{
    // Attributes
    var $userName;
    var $firstName;
    var $lastName;
    var $email;
    var $password;
    var $isAdministrator;
    var $organizationId;
	
    // Construct for the Users object
    function __construct($userName,$firstName,$lastName,$email,$password,$isAdministrator,$organizationId){
        $this->userName = $userName;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->isAdministrator = $isAdministrator;	
        $this->organizationId = $organizationId;
    }

    /* Insert a user in the database
    *  Parameters: userName (char), firstName (char), lastName (char),
    *              email (char), password (char), isAdministrator (char),
    *              organizationId (int)
    */
    function Insert(){
        $salt1 = 'qm&h';
        $salt2 = 'pg!@';
        $passwordHash = md5("$salt1.$this->password.$salt2");        
        $query = "INSERT INTO users
                    (userName,firstName,lastName,email,password,isAdministrator,organizationId)
                    VALUES('$this->userName','$this->firstName','$this->lastName','$this->email',
                           '$passwordHash','$this->isAdministrator',$this->organizationId)";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: INSERT query failed.");
    }

    /* Update user information the database
    *  Parameters: userName (char), firstName (char), lastName (char),
    *              email (char), password (char), isAdministrator (char),
    *              organizationId (int)
    */
    function Update(){
        $query = "UPDATE users
                    SET firstName = '$this->firstName', 
                        lastName = '$this->lastName',
                        email = '$this->email',
                        isAdministrator = '$this->isAdministrator'        
                    WHERE userName = '$this->userName'
                    AND organizationId = $this->organizationId";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: UPDATE query failed.");
    }
	
    /* Delete user for a given userName and organization ID
    *  Parameters: userName (char), organizationId (int)
    */
    function Delete(){
        $query = "DELETE FROM users
                    WHERE userName = '$this->userName'
                    AND organizationId = $this->organizationId";
        include("includes/dbConnection.php");						
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: DELETE query failed.");
    }
	
    /* Select all users from a given organization ID
    *  Parameters: organizationId (int)
    */
    function selectAll(){
        $query = "SELECT * 
                    FROM users
                    WHERE organizationId = $this->organizationId
                    ORDER BY firstName";
        include("includes/dbConnection.php");		
        $executeQuery = $db->prepare($query);
        $executeQuery->execute()  or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }

    /* Select user for a given userName and organization ID
    *  Parameters: userName (char), organizationId (int)
    */
    function selectUser(){ 
        $query = "SELECT * 
                    FROM users
                    WHERE userName = '$this->userName'
                    AND organizationId = $this->organizationId";	
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute() or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }	
	
    /* Valid user for a given userName, password  and organization ID
    *  Parameters: userName (char), password (char) and organizationId (int)
    */    
    function checkUserPassword(){ 
        $salt1 = 'qm&h';
        $salt2 = 'pg!@';
        $passwordHash = md5("$salt1.$this->password.$salt2");          
        $query = "SELECT * 
                    FROM users
                    WHERE userName = '$this->userName'
                    AND password = '$passwordHash'
                    AND organizationId = $this->organizationId";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute()  or exit("Error: SELECT query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }
      
    /* Update user password for a given userName and organization ID
    *  Parameters: userName (char), password (char) and organizationId (int)
    */    
    function updateUserPassword(){ 
        $salt1 = 'qm&h';
        $salt2 = 'pg!@';
        $passwordHash = md5("$salt1.$this->password.$salt2");            
        $query = "UPDATE users
                    SET password = '$passwordHash'
                    WHERE userName = '$this->userName'
                    AND organizationId = $this->organizationId";
        include("includes/dbConnection.php");			
        $executeQuery = $db->prepare($query);
        $executeQuery->execute()  or exit("Error: UPDATE query failed.");
        $result = $executeQuery->fetchAll(PDO::FETCH_BOTH);
        return $result;
    }        
}	
?>