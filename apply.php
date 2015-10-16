<?php
//lines 3-7: Establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 

//line 10: Gets the job_id variable value from the URL
$job_id=$_GET["t"];

//lines 13-22: Calls the generate_random_token function in order to create values for the student_SS, student_token, token_role variables and saves them in DB
require_once('wp-config.php');

$student_SS= generate_random_token() ;
$student_token= generate_random_token();
$token_role= "student";

$sql="INSERT INTO `JobSelection`.`tokens` (`SS_token`,`token_role`,`system_token`, `job_id`) 
										 VALUES ('$student_SS', '$token_role', '$student_token', '$job_id')";

$sql_result = mysqli_query($con,$sql);

//line 25: Regidects the user to the specified page in order to authenticate
header("Location: https://stork2.atlantis-group.gr/stork-testjs/ValidateToken?t=". $student_SS);										 
?>