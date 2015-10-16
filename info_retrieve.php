<?php

//lines 4-8: Establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 

//line 11: Gets the SS_token variable value from the URL
$SS_token= $_GET["t"];

//lines 14-22: Selects the user token value matched with the saved SS_token value
require_once('wp-config.php');

$sql1 = "SELECT token FROM wp_users WHERE SS_token='". $SS_token ."'";
$sql1_result= mysqli_query($con,$sql1); 
		
if ($sql1_result->num_rows ==1){
	$row = $sql1_result->fetch_assoc();
	$system_token= $row["token"];
}

//lines 25-32: Selects the token_role value matched with the saved SS_token value
require_once('wp-config.php');
$sql2 = "SELECT token_role FROM tokens WHERE SS_token='". $SS_token ."'";
$sql2_result= mysqli_query($con,$sql2);

if ($sql2_result->num_rows ==1){
	$row = $sql2_result->fetch_assoc();
	$token_role= $row["token_role"];
}
//STOP USING SS_token!!!!!!!!!!

//lines 36-43: Selects the user_role value matched with the saved token value
require_once('wp-config.php');
$sql3 = "SELECT user_role FROM wp_users WHERE token='". $system_token ."'";
$sql3_result= mysqli_query($con,$sql3);

if ($sql3_result->num_rows ==1){
	$row = $sql3_result->fetch_assoc();
	$user_role= $row["user_role"];
}

//lines 46-53: Selects the system_token value matched with the saved SS_token
require_once('wp-config.php');
$sql4 = "SELECT system_token FROM tokens WHERE SS_token='". $SS_token ."'";
$sql4_result= mysqli_query($con,$sql4); 
		
if ($sql4_result->num_rows ==1){
	$row = $sql4_result->fetch_assoc();
	$system_token= $row["system_token"];
}

//lines 56-81: Checks if the token_role value is register, login or student
if ($token_role=="register"){
	
	//lines 59-68: Checks if the user_role value is employee or representative
	if ($user_role=="employee") {
		header("Location: http://stork-ap.aegean.gr/JobSelection/employee_info_retrieve.php?t=". $system_token); 
	}
	else if ($user_role=="representative"){
		header("Location: http://stork-ap.aegean.gr/JobSelection/representative_info_retrieve.php?t=". $system_token); 
	}
	else{
		//correct to Process Failed page
		exit;
	}
}
else if ($token_role=="login"){
	//line 72: Redirect the user to the what_to_do.php page
	header("Location: http://stork-ap.aegean.gr/JobSelection/what_to_do.php?t=". $system_token);
}
else if ($token_role=="student"){
	//line 76: Redirect the user to the successful_apply.php page
	header("Location: http://stork-ap.aegean.gr/JobSelection/successful_apply.php?t=". $system_token);
}
else{
	//line 80: Redirect to process_failed.php page
	header("Location: http://stork-ap.aegean.gr/JobSelection/process_failed.php");
}

?>

<?php

/**

//Return NULL if no token was provided or if it is invalid, else it will
//return the token string
require_once('wp-config.php');
function is_token_valid($con) {
	global $_STORK2_TOKEN_GRACE_PERIOD;

	$outcome = null;
	if ( isset($_GET["t"]) && strlen($_GET["t"])>0 ) {
		$SS_token= $_GET["t"];
		$query = "SELECT user_role FROM wp_users WHERE SS_token='". $SS_token ."' AND create_stamp>SUBDATE(NOW(), INTERVAL ". $_STORK2_TOKEN_GRACE_PERIOD ." SECOND)";

		

		$result= mysqli_query($con,$query); 
		

		if ($result->num_rows ==1){
			$row = $result->fetch_assoc();
			$outcome= $row["user_role"];
			
		}
		
	}

	return $outcome;
	
}

*/
?>