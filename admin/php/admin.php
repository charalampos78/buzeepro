<?php

  require_once '../php/db_connect.php';
  session_start();
  
 //get user count
   $sql ="select count(*) as userCount from login";
   $stmt = sqlsrv_query($conn, $sql);		 
   $row = sqlsrv_fetch_array($stmt);
   $_POST['userCount'] = $row['userCount'];
							 

//record user email and ipaddress							 
$email =  $_SESSION["email"];
$ip_address = $_SERVER['REMOTE_ADDR'];							 
$sql2 = "exec sp_record_session @email = ?, @ip_address = ? ";
$stmt = sqlsrv_query($conn, $sql2, array($email,$ip_address ));

//close_connection
//sqlsrv_close($conn);



//function handler
$functionName = filter_input(INPUT_GET, "functionName");
if ($functionName == "morrisLine") {morrisLine();}
elseif ($functionName == "pieChart") {pieChart();}
elseif ($functionName == "sql") 
			{
				$query = filter_input(INPUT_GET, "query");
				//echo $query;
				sql($query);
				
			}



function sql($q){


	 require '../php/db_connect.php';
	 
	 $sql4 = $q;
	// echo $sql4;
	 $stmt = sqlsrv_query($conn, $sql4);
	 
	  if ( $stmt )
       {
        echo "Statement executed.\n";
		//echo sqlsrv_num_fields($stmt);
       } 
       else 
       {
     echo "Error in statement execution.\n";
     die( print_r( sqlsrv_errors(), true));
	   }
	 
    $numFields =  sqlsrv_num_fields($stmt); 
	//echo $numFields;
	 
	$data = [];
	
	
//	while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
	{
		for ($i=0; $i < $numFields; $i++ )
		{
			 $eachrow = sqlsrv_get_field($stmt, $i);
			 
			echo $eachrow;
		}
		
		//$eachrow += "]";
	//echo $eachrow; 
		//$data[] = ["email" => $row['email'], 
		//           "visits" => $row['visits']];
	}
	
	echo json_encode($data);
	
	
}


// morrisline
 function morrisLine(){
	 require '../php/db_connect.php';

	$sql3 ="select
			concat (cast(log_time as date) ,' ',
			datepart(HH, log_time)) log_hour,  count(*) visits
			 from sessions where email is not null and ip_address is not null
			 group by concat (cast(log_time as date) ,' ',
			datepart(HH, log_time)) 
			order by concat (cast(log_time as date) ,' ',
			datepart(HH, log_time)) desc
			";
	$stmt = sqlsrv_query($conn, $sql3);

/*     if ( $stmt )
       {
        echo "Statement executed.\n";
       } 
       else 
       {
     echo "Error in statement execution.\n";
     die( print_r( sqlsrv_errors(), true));
      }
  */
 $data = [];
	while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
	{
		$data[] = ["log_hour" => $row['log_hour'], "visits" => $row['visits'] ];
		 //echo json_encode(sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC));
		// echo json_encode(sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC));
		//echo $row["log_time"];
	//	$output = $output." ". $row["log_time"]." - ". $row["email_count"]. "<br>";
	}

echo json_encode($data);
}


 function pieChart(){
	 require '../php/db_connect.php';

	$sql4 ="select
			email,  count(*) visits
			 from sessions where email is not null and ip_address is not null
			 group by email
			";
	$stmt = sqlsrv_query($conn, $sql4);

/*     if ( $stmt )
       {
        echo "Statement executed.\n";
       } 
       else 
       {
     echo "Error in statement execution.\n";
     die( print_r( sqlsrv_errors(), true));
      }
  */
 $data = [];
	while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))
	{
		$data[] = ["email" => $row['email'], "visits" => $row['visits'] ];
		 //echo json_encode(sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC));
		// echo json_encode(sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC));
		//echo $row["log_time"];
	//	$output = $output." ". $row["log_time"]." - ". $row["email_count"]. "<br>";
	}

echo json_encode($data);
}

 
?>