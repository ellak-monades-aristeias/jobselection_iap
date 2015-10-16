<?php 
//lines 3-28: Gets the variable values from the company's basic information form
//get the form elements and store them in variables
$host_category=$_POST["host_category"];
$field_of_activity=$_POST["field_of_activity"];
$company_name=$_POST["company_name"];
//$brand_name=$_POST["brand_name"];
//$VAT_number=$_POST["VAT_number"];
$company_telephone=$_POST["company_telephone"];
$fax=$_POST["fax"];
$company_email=$_POST["company_email"];
$website=$_POST["website"];
$number_of_employees=$_POST["number_of_employees"];
//$country=$_POST["country"];
//$street_name=$_POST["street_name"];
//$street_number=$_POST["street_number"];
//$zip_code=$_POST["zip_code"];
//$city=$_POST["city"];
$contact_person_name=$_POST["contact_person_name"];
$contact_person_surname=$_POST["contact_person_surname"];
$contact_person_telephone=$_POST["contact_person_telephone"];
$contact_person_mobile=$_POST["contact_person_mobile"];
$contact_person_email=$_POST["contact_person_email"];
$deputy_name=$_POST["deputy_name"];
$deputy_surname=$_POST["deputy_surname"];
$deputy_telephone=$_POST["deputy_telephone"];
$deputy_mobile=$_POST["deputy_mobile"];
$deputy_email=$_POST["deputy_email"];
 
?>

<?php 
//lines 34-38: Establishes database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 
?>

<?php 
//line 43: Gets the variable emp_system_t value from the URL
$emp_system_t= $_GET["t"];

//lines 46-61: Calls the generate_random_token function and inserts the saved variable values in the DB
require_once('wp-config.php');

$comp_system_t= generate_random_token() ;

$sql1="INSERT INTO `JobSelection`.`companies` (`host_category`, `field_of_activity`, `company_name`, `brand_name`, `VAT_number`, `company_telephone`, 
										`fax`, `company_email`, `website`, `number_of_employees`, `country`, `street_name`, `street_number`, `zip_code`,
										`city`, `contact_person_name`, `contact_person_surname`, `contact_person_telephone`, `contact_person_mobile`, 
										`contact_person_email`, `deputy_name`, `deputy_surname`, `deputy_telephone`, `deputy_mobile`, `deputy_email`, `token`)

								VALUES('$host_category', '$field_of_activity', '$company_name', '', '', '$company_telephone',
										'$fax', '$company_email', '$website', '$number_of_employees', '', '', '', '',
										'', '$contact_person_name', '$contact_person_surname', '$contact_person_telephone', '$contact_person_mobile',
										'$contact_person_email', '$deputy_name', '$deputy_surname', '$deputy_telephone', '$deputy_mobile', '$deputy_email', '$comp_system_t')";
										
										
$sql1_result=mysqli_query($con,$sql1); 

//lines 64-66: Creates the second link for the entity "business" , the employee, the representative and the company
require_once('wp-config.php');
$sql2="UPDATE user_connection SET company_token='".$comp_system_t ."' WHERE employee_token='".$emp_system_t."'";
$sql2_result=mysqli_query($con, $sql2);
?>

<?php

$emp_system_t=$_GET["t"];

//line 74: Redirects to the specified page
header("Location: http://stork-ap.aegean.gr/JobSelection/representative_registration.php?t=" .$emp_system_t); 
?>