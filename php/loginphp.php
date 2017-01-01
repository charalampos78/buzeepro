<?php 
session_start();
//$_SESSION['email'] = 'Harry';
//$_SESSION['pwd'] = '1234';

//echo("success");


if(isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    switch($action) {
        case 'login' : login();break;
        case 'logout' : logout();break;
        // ...etc...
    }
}


function login(){
	
require_once 'db_connect.php';




		if(isset($_POST['email']) && isset($_POST['pwd']))
    			 { 
 				       
   				 $email = sanitizeString($_POST['email']);
   				 $pwd = $_POST['pwd']; 
    					 
    
       				  //tokenize pwd
      				  // $token = tokenization($pwd);    
      				  //build query and send to mssql
	   
   				 $sql ="exec sp_login @email = '$email',@password = '$pwd'";
  				 $stmt = sqlsrv_query($conn, $sql);
				  
				 
                                 $row = sqlsrv_fetch_array($stmt);
								 //echo("row: ". $row["email"]);
	                         $status = $row['status'];	
							 	 //echo("row: ". $row["status"]);
                                 $role = $row['role'];

    				 //create session if email and pwd exist
                                 if ($email == trim($row["email"]) && $status == "Active" ) 
                                 {   
                              
                                 $_SESSION['email'] = $email;
                               
                                 echo('success');                
				 }
                                 else
                                 {
                                 echo('error');
                                 }
     
	                     }

}




function userCount(){
	
require_once 'db_connect.php';

 				       
   				 
   				 $count = $_POST['count']; 
    					 
    
       				  //tokenize pwd
      				  // $token = tokenization($pwd);    
      				  //build query and send to mssql
	   
   				 $sql ="select count(*) as row_count from login";
  				 $stmt = sqlsrv_query($conn, $sql);
				  
				 
                                 $row = sqlsrv_fetch_array($stmt);
								 //echo("row: ". $row["email"]);
	                         $count = $row['row_count'];	
							 	echo ($count);

}
           
















function sanitizeString($str)
{
    $str = strip_tags($str);
    $str = htmlentities($str);
    $str = stripslashes($str);
    return $str;
}



function logout()
{


//print_r(session_name());
//print_r(session_id());

//print_r($_SESSION["email"]);

session_unset();
session_destroy();

echo ("success");

}


?>





    