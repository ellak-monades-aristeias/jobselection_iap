<?php
//lines 3-7: establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 

//lines 10-15: Generates the user SS_token, the token role and the user system token in the system 
require_once('wp-config.php');

$SS_token= generate_random_token() ;
$token_role="register";

$system_token=generate_random_token();

//lines 18-23: Creates the first user insertion in the DB, so that the system could manage him in the next steps of the registration process
$sql1="INSERT INTO `JobSelection`.`tokens` (`SS_token`, `token_role`, `system_token`)
										
										VALUES('$SS_token', '$token_role', '$system_token')";

										
mysqli_query($con,$sql1); 

//line 26: Creates the user role so that the system can create the appropriate JSON message in the next steps of the registration process
$user_role="employee";

//lines 29-39: Creates the second user insertion in the DB, so that the system could know the saving point when the user attributes arrive
require_once('wp-config.php');


$sql2="INSERT INTO `JobSelection`.`wp_users` (`user_login`, `user_pass`, `user_nicename`, `user_email`, `display_name`,
										`name`, `surname`, `telephone_number`, `eIdentifier`, `country`, `mandate`, `token`, `SS_token`,`create_stamp`, `user_role`)
										
										VALUES('$system_token', '$system_token', '$system_token', '', '$system_token',
										'$system_token', '$system_token', '', '$system_token', '', '$system_token', '$system_token', '$SS_token' ,NOW(), '$user_role')";

										
mysqli_query($con,$sql2); 


//line 43: Redirects the user to the STORK 2.0 ISS page for authentication and attribute collection
header("Location: https://stork2.atlantis-group.gr/stork-testjs/ValidateToken?t=". $SS_token); 
?>