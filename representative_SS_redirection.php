<?php

//lines 4-8: Establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
}

//line 11: Get the rep_system_t variable value from the URL
$rep_system_t= $_GET["t"];

//lines 14-33: Creates the SS_token variable value used for the user-representative, saves it in the DB and creates a token_role value 
//matched with the representative's SS_token
require_once('wp-config.php');

$SS_token= generate_random_token() ;
$sql1 = "UPDATE wp_users SET SS_token='". $SS_token ."' WHERE token='". $rep_system_t  ."'";

$sql1_result=mysqli_query($con,$sql1);

require_once('wp-config.php');
$sql3 = "UPDATE wp_users SET create_stamp=NOW() WHERE token='". $rep_system_t ."'";
$sql3_result=mysqli_query($con,$sql3);

require_once('wp-config.php');

$token_role="register";
$sql2="INSERT INTO `JobSelection`.`tokens` (`SS_token`, `token_role`, `system_token`)
										
										VALUES('$SS_token', '$token_role', '$rep_system_t')";

										
$sql2_result= mysqli_query($con,$sql2); 

//line 37: Redirects to the specified page
header("Location: https://stork2.atlantis-group.gr/stork-testjs/ValidateToken?t=" . $SS_token); 

?>