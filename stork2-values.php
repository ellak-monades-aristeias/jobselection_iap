<?php
//lines 3-8: Calls the authenticate_supporting_service function and checks its value, true or false
require_once('wp-config.php');

if (authenticate_supporting_service()==false) {
	echo "error message in authenticating supporting service: not OK";
	exit;
}

//lines 11-15: Establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
	die('Could not connect: '.mysql_error()); 
} 

//line 18: Get the user SS_token from the URL
$SS_token=$_GET["t"];

//lines 21-29: Search and select the variable token_role matched with the saved user SS_token variable
require_once('wp-config.php');

$sql1="SELECT token_role FROM tokens WHERE SS_token='" .$SS_token."'";
$sql1_result=mysqli_query($con, $sql1);
$token_role=null;
if ($sql1_result->num_rows >0){
			$row = $sql1_result->fetch_assoc();
			$token_role= $row["token_role"];
}

error_log("$token_role", 0);
error_log("token",0);

//line 35: Check if the saved token_role value matches values student
if ($token_role=="student"){
	error_log("test1", 0);
	
	//line 39-117: Decode the received JSON response from the STORK ISS, search the saving point depending on the user SS_token
	if (isset($_POST["r"])){
		$json_string=$_POST["r"];
		$json_string=stripslashes($json_string);
		$pal = json_decode($json_string,true);
		error_log("$json_string", 0);
		error_log("$pal", 0);
		
		if ( $_POST["r"]!="{}" || $pal!=NULL ) {
				$pal_keys = array_keys($pal);
				$pal_count = count($pal_keys);
				
				$SS_token=$_GET["t"];
				
				for ($j=0 ; $j<$pal_count ; $j++){
					
					$value = $pal[$pal_keys[$j]]["value"];
					$complex = $pal[$pal_keys[$j]]["complex"];

					if ($pal_keys[$j]== "isStudent"){
						$isStudent= $value;		
						error_log("$isStudent", 0);
						
						$student_split= getSplitArray($isStudent);

						$course= $student_split['course'];
						$study_isStudent= $student_split['study'];
						$AQAA_isStudent= $student_split['AQAA'];
						$nameOfInstitution_isStudent= $student_split['nameOfInstitution'];
						
					}
					else if ($pal_keys[$j]== "hasDegree"){
						$hasDegree= $value;	
						error_log("$hasDegree", 0);
						
						$degree_split= getSplitArray($hasDegree);
						$level= $degree_split['level'];
						$yearObtained= $degree_split['yearObtained'];
						$study_hasDegree= $degree_split['study'];
						$AQAA_hasDegree= $degree_split['AQAA'];
						$nameOfInstitution_hasDegree= $degree_split['nameOfInstitution'];

					}
					else if ($pal_keys[$j]== "surname"){
						$surname= $value;
						error_log("$surname", 0);
					}
					else if ($pal_keys[$j]== "givenName"){						
						$givenName= $value;
						error_log("$givenName", 0);
					}
					else if ($pal_keys[$j]== "eIdentifier"){
						$eIdentifier= $value;
						error_log("$eIdentifier", 0);
					}
				}//end for
				
				//lines 96-103: Search for the user system_token matched with the user SS_token
				require_once('wp-config.php');
				
				$sql0="SELECT system_token FROM tokens WHERE SS_token='" .$SS_token ."'";
				$sql0_result= mysqli_query($con,$sql0);
				if ($sql0_result->num_rows ==1){
					$row = $sql0_result->fetch_assoc();
					$student_token= $row["system_token"];
				}
				
				//lines 106-110: Insert into DB the user attributes gathered from the decoding
				require_once('wp-config.php');
				$sql="INSERT INTO `JobSelection`.`students` (`student_token`, `givenName`, `surname`, `eIdentifier`, `isStudent`, `course`, `study_isStudent`, `AQAA_isStudent`, `nameOfInstitution_isStudent`, `hasDegree`, `level`, `yearObtained`, `study_hasDegree`, `AQAA_hasDegree`, `nameOfInstitution_hasDegree`) 
										 VALUES ('$student_token', '$givenName', '$surname', '$eIdentifier', '$isStudent', '$course', '$study_isStudent', '$AQAA_isStudent', '$nameOfInstitution_isStudent', '$hasDegree', '$level', '$yearObtained', '$study_hasDegree', '$AQAA_hasDegree', '$nameOfInstitution_hasDegree')";

				$sql_result = mysqli_query($con,$sql);
				
				//lines 113-120: Creates and prints the appropriate JSON message depending on the insertion's result 
				if ( isset($sql_result) || $sql_result != NULL){
					$response .= '{"status":"OK"}';
					echo $response;
				}//end status OK if
				else{
					$response .= '{"status":"NOK"}';
					echo $response;
				}//end status NOK if
		}//end post not null if
	}//end post isset if
}//end token_role=student

//line 126: Checks if the saved user token_role matches the login value
else if ($token_role=="login"){
	
	//line 129: Calls the is_token_valid function 
	$user_role = is_token_valid($con);
	
	//lines 132-176: Decode the received JSON response from the STORK ISS, selects the user token variable, searches if the user token variable
	//matches with an eIdentifier value saved in DB and if yes updates the user SS_token where the the saved user token variable exists 
	if (isset($_POST["r"])){
		$json_string=$_POST["r"];
		$json_string=stripslashes($json_string);
		$pal = json_decode($json_string,true);
		
		if ($_POST["r"]!="{}" || $pal!=NULL){
			$pal_keys = array_keys($pal);
			
			$pal_count=count($pal_keys);
			
			if ($pal_keys[0]== "eIdentifier"){
				$value = $pal[$pal_keys[0]]["value"];
				
				error_log("$value", 0);
				
				//lines 149-159: Selects the user token variable matched with the eIdentifier variable value saved arrived from the decoding
				require_once('wp-config.php');
				
				$query1="SELECT token FROM wp_users WHERE eIdentifier='" .$value ."'";
				$query1_result= mysqli_query($con,$query1);
				
				$check = "false";
				if ($query1_result->num_rows ==1){
					$row = $query1_result->fetch_assoc();
					$query1_result= $row["token"];
					$check= "true";
				}//end query1_result if
				
				//lines 162-180: Checks if a user exists with the saved eIdentifier, updates his SS_token variable value,
				//creates and prints the appropriate JSON message depending on the update's result 
				if ($check=="true"){
					require_once('wp-config.php');
					$query2="UPDATE wp_users SET SS_token='".$SS_token."' WHERE token='".$query1_result."'";
					$query2_result=mysqli_query($con,$query2);
				
					require_once('wp-config.php');
					$query3="UPDATE tokens SET system_token='".$query1_result."' WHERE SS_token='".$SS_token."'";
					$query3_result=mysqli_query($con,$query3);
				
					$response .= '{"status":"OK"}';
					echo $response;
					error_log("$response",0);
				}//end check true if
				else {
					$response .= '{"status":"NOK"}';
					echo $response;
					error_log("$response",0);
				}//end check false if
			}//end pal_keys if
		}//end json not null if
	}//end existance of json if
}

//line 187: Checks if the saved user token_role matches the register value
else if ($token_role=="register"){
	
	//line 190: Calls the is_token_valid function
	$user_role= is_token_valid($con);

	//lines 193-253: Checks if the user_role variable value is employee and decodes the JSON message arrived from STORK ISS
	require_once('wp-config.php');
	if ($user_role=="employee"){
		if (isset($_POST["r"])) {

			$json_string=$_POST["r"];
			$json_string=stripslashes($json_string);
			error_log("$json_string", 0);
			$pal = json_decode($json_string, true);
	
			if ( $_POST["r"]!="{}" || $pal!=NULL ) {
				$pal_keys = array_keys($pal);
			
				$pal_count = count($pal_keys);
				$SS_token=$_GET["t"];
	
				require_once('wp-config.php');
		
				$query= "UPDATE wp_users SET ";
		
		
				for($i=0; $i<$pal_count; $i++) {
					$delimiter=",";
					if ($i== $pal_count-1)
						$delimiter="";
			
					$value = $pal[$pal_keys[$i]]["value"];
					$complex = $pal[$pal_keys[$i]]["complex"];

					if ($pal_keys[$i]== "givenName")
						$query .= " name='". $value ."'". $delimiter;		
					else if ($pal_keys[$i]== "surname")
						$query .= " surname='". $value ."'". $delimiter;			
					else if ($pal_keys[$i]== "eIdentifier")
						$query .= " eIdentifier='". $value ."'". $delimiter;
					else if ($pal_keys[$i]== "eMail")
						$query .= " user_email='". $value ."'". $delimiter;
					else if ($pal_keys[$i]== "nationalityCode")
						$query .= " country='". $value ."'". $delimiter;	
					else if ($pal_keys[$i]== "mandate")
						$query .= " mandate='". $value ."'". $delimiter;	
			
				}//end for
				$query .= " WHERE SS_token='". $SS_token . "'";
	   
				$result= mysqli_query($con,$query); 
	   
				//lines 240-249: Checks update's result and prepares the appropriate JSON message for the STORK ISS
				$sql= "SELECT eIdentifier FROM wp_users WHERE SS_token='". $SS_token  ."'";
				$sql_result=mysqli_query($con,$sql);
				if ( isset($sql_result) || $sql_result != NULL){
					$response .= '{"status":"OK"}';
					echo $response;
				}//end status OK if
				else{
					$response .= '{"status":"NOK"}';
					echo $response;
				}//end status NOK if
	   
		
			}//end employee's json not null if
		}//end employee's json msg existence if
	}//end user_role=employee if
	
	//lines 257-382: Checks if the user_role variable value is representative and decodes the JSON message arrived from STORK ISS
	if ($user_role=="representative") {
		require_once('wp-config.php');

		if (isset($_POST["r"])) {

			$json_string=$_POST["r"];
			$json_string=stripslashes($json_string);
			error_log("$json_string", 0);
			$pal = json_decode($json_string, true);
				
			if ( $_POST["r"]!="{}" || $pal!=NULL ) {
				$pal_keys = array_keys($pal);
				$pal_count = count($pal_keys);
				
				$SS_token=$_GET["t"];
				
				require_once('wp-config.php');
					
				//lines 276-283: Selects the token used for representative in my system by knowing the user's token used in SS connection
				$sql1= "SELECT token FROM wp_users WHERE SS_token='" . $_GET["t"] ."'";
				$sql1_result= mysqli_query($con, $sql1);
			
			
				if ($sql1_result->num_rows ==1){
					$row = $sql1_result->fetch_assoc();
					$sql1_result= $row["token"];
				}//end sql1_result if
			
				require_once('wp-config.php');
				
				//lines 288-295: Selects the company's token used in my system by knowing the user's token used also in my system
				$sql2= "SELECT company_token FROM user_connection WHERE representative_token='" . $sql1_result ."'";
				$sql2_result= mysqli_query($con, $sql2);
			
			
				if ($sql2_result->num_rows ==1){
					$row = $sql2_result->fetch_assoc();
					$sql2_result= $row["company_token"];
				}//end sql2_result if
			
				require_once('wp-config.php');
				
				//lines 300-322, 364: Update the company's entry in my system by knowing the company's token also used in my system
				$comp_query= "UPDATE companies SET";
				for ($j=0 ; $j<$pal_count ; $j++){
					$delimiter=",";
					if ($j== $pal_count-1)
						$delimiter= "";
					
					$value = $pal[$pal_keys[$j]]["value"];
					$complex = $pal[$pal_keys[$j]]["complex"];

					if ($pal_keys[$j]== "LPFiscalNumber")
						$comp_query .= " VAT_number='". $value ."'". $delimiter;		
					else if ($pal_keys[$j]== "legalName")
						$comp_query .= " company_name='". $value ."'". $delimiter;			
					else if ($pal_keys[$j]== "alternativeName")
						$comp_query .= " brand_name='". $value ."'". $delimiter;
					else if ($pal_keys[$j]== "registeredCanonicalAddress") {
						//$comp_query .= " street_name='". $value ."'". $delimiter; //SOS to be fixed because this is a complex attribute!!!!!	
						$comp_query .= updateAddress($value, $delimiter);
					}
					else if ($pal_keys[$j]== "eLPIdentifier")
						$comp_query .= " eLPIdentifier='". $value ."'". $delimiter;
				}//end company's for
				$comp_query .= " WHERE token='". $sql2_result . "'";
				 
				error_log("$comp_query", 0);
				//lines 326-362, 365: Update the representative's entry in my system by knowing the representative's token used in the SS connection
				require_once('wp-config.php');
				$rep_query= "UPDATE wp_users SET";
				for ($z=0 ; $z<$pal_count ; $z++){
					$delimiter=",";
					if ($z== $pal_count-1) 
						$delimiter= "";
					
					$value = $pal[$pal_keys[$z]]["value"];
					$complex = $pal[$pal_keys[$z]]["complex"];
					
					if ($pal_keys[$z]== "givenName")
						$rep_query .= " name='". $value ."'". $delimiter;		
					else if ($pal_keys[$z]== "surname")
						$rep_query .= " surname='". $value ."'". $delimiter;			
					else if ($pal_keys[$z]== "eIdentifier"){
						$rep_query .= " eIdentifier='". $value ."'". $delimiter;
						$rep_eID = $value;
					}//end eIdentifier else if
					else if ($pal_keys[$z]== "mandate") {
						$rep_query .= " mandate='". $value ."'". $delimiter; //SOS to be fixed because this is a complex attribute!!!!!
						
						$rep_mandate_eID=$value;
						$rep_mandate_eID= substr($rep_mandate_eID, strpos($rep_mandate_eID, "<eIdentifier>")+13 );
						$rep_mandate_eID= substr($rep_mandate_eID, 0, strpos($rep_mandate_eID, "</eIdentifier>") );
						
						
					}//end mandate else if					
					else if ($pal_keys[$z]== "fiscalNumber")
						$rep_query .= " fiscalNumber='". $value ."'". $delimiter;
					
				
				}//end representative's for
			
				if (substr($rep_query, -1)==",")
					$rep_query = substr($rep_query, 0, -1);
			
				$rep_query .= " WHERE token='". $sql1_result . "'";
				
				$comp_query_result= mysqli_query($con,$comp_query);
				$rep_query_result= mysqli_query($con,$rep_query);
				
				//lines 368-370: Update the mandate_eID variable value where the saved user token value exists
				require_once('wp-config.php');
				$mandate_eID_query= "UPDATE wp_users SET mandate_eID='" .$rep_mandate_eID ."' WHERE token='".$sql1_result ."'";
				$mandate_eID_query_result=mysqli_query($con, $mandate_eID_query);
				
				error_log("$rep_query", 0);
				error_log("$comp_query", 0);
				
				//lines 376-385: Checks if an eIdentifier value matches with the saved SS_token value and prepares the appropriate JSON message for the STORK ISS
				$sql3= "SELECT eIdentifier FROM wp_users WHERE SS_token='". $_GET["t"]  ."'";
				$sql3_result=mysqli_query($con,$sql3);
				if ( isset($sql3_result) || $sql3_result != NULL){
					$response .= '{"status":"OK"}';
					echo $response;
				}//end status OK if
				else{
					$response .= '{"status":"NOK"}';
					echo $response;
				}//end status NOK if
			}//end representative's json not null if
		}//end representative's json msg existence if 
	}//end user_role=representative if
}
?>
<?php

	//lines 394-404: Analyzes the complex attribute array so to be decoded
    function getSplitArray($long_value) {
		$split_array= explode(",", $long_value);

		$value_split= array();
		foreach ($split_array as $value) {
	
			$ar_temp= explode("=", $value);
			$value_split[$ar_temp[0]] = $ar_temp[1];
		}
		return $value_split;
	}

?>
<?php

//lines 410-422: Checks if the variables user and pass values passed from the URL are correct so to authenticate the STORK ISS
function authenticate_supporting_service() {
	$username = "supporting";
	$password = "service_Survey14";

	$outcome = false;

	if ( isset($_GET["user"]) && $_GET["user"]==$username ) {
		if ( isset($_GET["pass"]) && $_GET["pass"]==$password )
			$outcome = true;
	}//end if

	return $outcome;
}//end authenticate_supporting_service

?>
<?php


//lines 431-454: The is_token_valid function checks if the user's SS_token variable has expired
//Return NULL if no token was provided or if it is invalid, else it will
//return the token string
require_once('wp-config.php');
function is_token_valid($con) {
	global $_STORK2_TOKEN_GRACE_PERIOD;

	$outcome = null;
	if ( isset($_GET["t"]) && strlen($_GET["t"])>0 ) {
		$token= $_GET["t"];
		$query = "SELECT user_role FROM wp_users WHERE SS_token='". $token ."' AND create_stamp>SUBDATE(NOW(), INTERVAL ". $_STORK2_TOKEN_GRACE_PERIOD ." SECOND)";

		

		$result= mysqli_query($con,$query); 
		

		if ($result->num_rows ==1){
			$row = $result->fetch_assoc();
			$outcome= $row["user_role"];
			
		}//end if
		
	}//end if
	
	return $outcome;	
}//end is_token_valid
?>