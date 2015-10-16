<?php
//lines 3-9: Gets the form elements and store them in variables
$name=$_POST["name"];
$surname=$_POST["surname"];
$telephone_number=$_POST["telephone_number"];
$email=$_POST["email"];
$pass=$_POST["password"]; 
$pass=md5($pass);
$user_role="representative";
?>

<?php 
//lines 14-18: Establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 
?>

<?php 
//lines 23-35: Gets the variable emp_system_t value from the URL and inserts the saved variable values in the DB
require_once('wp-config.php');
$emp_system_t= $_GET["t"];

$rep_system_t= generate_random_token() ;

$sql1="INSERT INTO `JobSelection`.`wp_users` (`user_login`, `user_pass`, `user_nicename`, `user_email`, `display_name`,
										`name`, `surname`, `telephone_number`, `eIdentifier`, `country`, `mandate`, `token`, `create_stamp`, `user_role`)
										
										VALUES('$name', '$pass', '$name', '$email', '$name',
										'$name', '$surname', '$telephone_number', '$eIdentifier', '$country', '$mandate', '$rep_system_t', '', '$user_role')";
										
										
$sql1_result=mysqli_query($con,$sql1); 

//lines 38-40: Creates the third and final link for the entity "business" , the employee, the representative and the company
require_once('wp-config.php');
$sql2="UPDATE user_connection SET representative_token='".$rep_system_t ."' WHERE employee_token='".$emp_system_t."'";
$sql2_result= mysqli_query($con, $sql2);

?>

<?php
//line 46: Redirects to the specified page
header("Location: http://stork-ap.aegean.gr/JobSelection/employee_final.php?t=" .$emp_system_t ."'"); 
?>