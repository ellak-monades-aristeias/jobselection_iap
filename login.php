<?php

//lines 4-8: Establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 

//lines 11-20: Call generate_random_token function and save SS_token, token_role variables in DB
require_once('wp-config.php');

$SS_token= generate_random_token() ;
$token_role="login";
$sql="INSERT INTO `JobSelection`.`tokens` (`SS_token`, `token_role`, `system_token`)
										
										VALUES('$SS_token', '$token_role', '')";

										
mysqli_query($con,$sql); 

//line 23: Redirects the user to the specified page
header("Location: https://stork2.atlantis-group.gr/stork-testjs/ValidateToken?t=". $SS_token); 

?>