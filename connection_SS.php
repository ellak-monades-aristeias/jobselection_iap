<?php 
//lines 3-7: establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 
?>
<?php
//lines 11-16: Calls function "authenticate_supporting_service" and prints error message if the STORK ISS is not authenticated successfully
require_once('wp-config.php');

if (authenticate_supporting_service()==false) {
	echo "error message in supporting service: not OK";
	exit;
}

//line 19: Get the user SS_token variable from the URL so to authenticate which user he is 
$SS_token=$_GET["t"];

//lines 22-30: Searches and selects the user token_role matched with the user SS_token arrived from the STORK ISS 
require_once('wp-config.php');
$sql1="SELECT token_role FROM tokens WHERE SS_token='" .$SS_token ."'";
$sql1_result=mysqli_query($con, $sql1);
$token_role=null;
if ($sql1_result->num_rows >0){
			$row = $sql1_result->fetch_assoc();
			$token_role= $row["token_role"];
			
		}

//lines 33-37: Checks if the selected token_role variable has the login value and calls the getLogIn_json function 
if ($token_role=="login"){
	$request=	getLogIn_json();
	error_log("$request", 0);
	echo $request;
}

//lines 40-42: Checks if the selected token_role variable has the register value and calls the is_token_valid function 
else if ($token_role=="register"){
	$user_role= is_token_valid($db_conn);
	error_log("$user_role", 0);
	
	//lines 45-48: Checks if the user value is empty and if it is prints an error message
	if ($user_role==null) {
		echo "error message in supporting service: token not valid";
		exit;
	}

//line 51: Initializes the request variable
	$request="";
	
	error_log("$user_role", 0);
	
	//line 56: Checks if user SS_token exists
	if (!isset($_GET["r"])) {
		
		//lines 59-63: Checks if user_role variable has the employee value, calls the getEmployee_json function and prints the function's result
		if ($user_role=="employee") {
			$request=	getEmployee_json();
			error_log("$request", 0);
			echo $request;
		}
		
		//lines 66-72: Checks if user_role variable has the representative value, calls the getRepresentative_json function and prints the function's result
		else if ($user_role=="representative") {
			$request=	getRepresentative_json();
			error_log("----", 0);
			error_log("$request", 0);
			error_log("----", 0);
			echo $request;
		}
	}
	
	//lines 76-78: Calls the process_json_response function if the user_role variable value is neither employee nor representative
	else {
		process_json_response($user_role);
	}
}

//lines 82-86: Checks if the user token_role variable value is student, calls the getStudent_json function and prints the function's result
else if ($token_role=="student"){
	$request= getStudent_json();
	error_log("$request", 0);
	echo $request;
}


//lines 90-129: The function getStudent_json creates a JSON message with the student's attributes request
function getStudent_json() {
	
    $request .= ' {"status":"OK",';
	$request .= ' "list":{';
	$request .= ' "givenName":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' }, '; //givenName
	
	$request .= ' "surname":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= '"required":"1"';
	$request .= '}, '; //surname
	
	$request .= ' "eIdentifier":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' }, '; //eIdentifier
	
	$request .= ' "isStudent":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"1",';
	$request .= ' "required":"1"';
	$request .= ' }, '; //isStudent
	
	$request .= ' "hasDegree":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"1",';
	$request .= ' "required":"0"';
	$request .= ' }'; //hasDegree
	
	
	$request .= '}'; //list
   $request .= '}'; //request
   
   return $request;
}


//lines 133-147: The getLogIn_json creates a JSON message with the login's attribute request
function getLogIn_json() {
	
    $request .= ' {"status":"OK",';
	$request .= ' "list":{';
	$request .= ' "eIdentifier":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' } '; //eIdentifier	
	
	$request .= '}'; //list
    $request .= '}'; //request
   
   return $request;
}

//lines 150-157: The db_connect function establishes connection with the database
function db_connect() {
		$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
		//on connection failure, throw an error
		if(!$con) {  
			die('Could not connect: '.mysql_error()); 
		}
		return $con;
}

//lines 162-168: The is_token_valid function checks if the user's SS_token variable has expired
//Return NULL if no token was provided or if it is invalid, else it will
//return the token string
require_once('wp-config.php');
function is_token_valid($db_conn) {
	global $_STORK2_TOKEN_GRACE_PERIOD;

	$outcome = null;
	if ( isset($_GET["t"]) && strlen($_GET["t"])>0 ) {
		$token= $_GET["t"];
		error_log("$token", 0);
		$query = "SELECT user_role FROM wp_users WHERE SS_token='". $token ."' AND create_stamp>SUBDATE(NOW(), INTERVAL ". $_STORK2_TOKEN_GRACE_PERIOD ." SECOND)";
		error_log("$query", 0);
		
		$con= db_connect();

		$result= mysqli_query($con,$query); 
		

		if ($result->num_rows ==1){
			$row = $result->fetch_assoc();
			$outcome= $row["user_role"];
			error_log("$user_role", 0);
		}
		
	}

	return $outcome;
	
}

//lines 191-230: The getEmployee_json function prepares a JSON message with the employee's attributes request
function getEmployee_json() {
	
    $request .= ' {"status":"OK",';
	$request .= ' "list":{';
	$request .= ' "givenName":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' }, '; //givenName
	
	$request .= ' "surname":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= '"required":"1"';
	$request .= '}, '; //surname
	
	$request .= ' "eIdentifier":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' }, '; //eIdentifier
	
	$request .= ' "eMail":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"0"';
	$request .= ' }, '; //eIdentifier
	
	$request .= ' "nationalityCode":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"0"';
	$request .= ' }'; //country
	
	
	$request .= '}'; //list
   $request .= '}'; //request
   
   return $request;
}

//lines 233-306: The getRepresentative_json prepares a JSON message with the representative's attributes request
function getRepresentative_json() {
	
    $request .= '{"status":"OK",';
	$request .= '"list":{';
	

	
	$request .= '"eIdentifier":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' },'; //eIdentifier
	
	$request .= '"givenName":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' },'; //givenName
	
	$request .= '"surname":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' },'; //surname
	
	$request .= '"fiscalNumber":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' },'; //fiscalNumber
	
	$request .= '"LPFiscalNumber":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' },'; //LPFiscalNumber
	
	$request .= '"legalName":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' },'; //legalName
	
	$request .= '"alternativeName":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' },'; //alternativeName
	
	/**
	$request .= '"registeredCanonicalAddress":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"1",';
	$request .= ' "required":"1"';
	$request .= ' },'; //registeredCanonicalAddress
	*/
	
	$request .= '"mandate":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' },'; //mandate   
		
	$request .= ' "eLPIdentifier":{';
	$request .= ' "value":null,';
	$request .= ' "complex":"0",';
	$request .= ' "required":"1"';
	$request .= ' }'; //eLPIdentifier   
	
	
	$request .= '}'; //list
   $request .= '}'; //request 
   return $request;
}

?>
<?php

//lines 312-324: The authenticate_supporting_service function checks is the variables user and pass values arrived passed from the URL are correct 
function authenticate_supporting_service() {
	$username = "supporting";
	$password = "service_Survey14";

	$outcome = false;

	if ( isset($_GET["user"]) && $_GET["user"]==$username ) {
		if ( isset($_GET["pass"]) && $_GET["pass"]==$password )
			$outcome = true;
	}

	return $outcome;
}

?>