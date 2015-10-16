<?php

//lines 4-6: Gets the values for the variables system_token given from the URL and telephone_number, user_email1 given from the form
$system_token=$_GET["t"];
$telephone_number= $_POST["telephone_number"];
$user_email1= $_POST["user_email"];

//lines 9-13: Establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 

//lines 16-20: Updates the variable values
require_once('wp-config.php');

$sql1 = "UPDATE wp_users SET telephone_number='". $telephone_number  ."'" .", user_email='". $user_email1 ."'" ." WHERE token='". $system_token  ."'";

$sql1_result=mysqli_query($con,$sql1);

//lines 23-26: Creates the first link for the entity "business" , the employee, the representative and the company
require_once('wp-config.php');

$sql2 = "INSERT INTO `JobSelection`.`user_connection` (`employee_token`, `company_token` , `representative_token`) VALUES ('$system_token', '', '')";
$sql2_result= mysqli_query($con, $sql2);

//line 29: Redirects the user to the company_registration.php page
header("Location: http://stork-ap.aegean.gr/JobSelection/company_registration.php?t=" .$system_token); 
?>