<?php
//lines 3-16: Gets the system_token variable value from the URL and the variable value from the form
$system_token=$_GET["t"];

$end_app_date=$_POST["end_app_date"];
$title=$_POST["title"];
$description=$_POST["description"];
$duration=$_POST["duration"];
$country=$_POST["country"];
$city=$_POST["city"];
$position=$_POST["position"];
$start_period=$_POST["start_period"];
$end_period=$_POST["end_period"];
$supervisor_name=$_POST["supervisor_name"];
$supervisor_email=$_POST["supervisor_email"];
$telephone=$_POST["telephone"];

//lines 19-23: Establish dababase connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 

//lines 26-33: Selects the user_role variable value matched with the saved system_token value
require_once('wp-config.php');
$sql1="SELECT user_role FROM wp_users WHERE token='".$system_token."'";
$sql1_result=mysqli_query($con,$sql1);
if ($sql1_result->num_rows ==1){
	$row = $sql1_result->fetch_assoc();
	$user_role= $row["user_role"];
}
echo $user_role;

//lines 36-55: Checks if the user_role value is employee or representative and selects the company_token value
if ($user_role=="employee"){
	require_once('wp-config.php');
	$sql2="SELECT company_token FROM user_connection WHERE employee_token='".$system_token."'";
	$sql2_result=mysqli_query($con,$sql2);
	if ($sql2_result->num_rows ==1){
		$row = $sql2_result->fetch_assoc();
		$company_token= $row["company_token"];
		echo $company_token;
	}
}
if ($user_role=="representative"){
	require_once('wp-config.php');
	$sql3="SELECT company_token FROM user_connection WHERE representative_token='".$system_token."'";
	$sql3_result=mysqli_query($con,$sql3);
	
	if ($sql3_result->num_rows ==1){
		$row = $sql3_result->fetch_assoc();
		$company_token= $row["company_token"];
	}
}

//lines 58-64: Saves the upload job vacancy form variable values in the DB
require_once('wp-config.php');
$sql4="INSERT INTO `JobSelection`.`job_vacancies` (`end_app_date`, `title`, `description`, `duration`, `country`, `city`, `position`, `start_period`,`end_period`, `supervisor_name`,
													 `supervisor_email`, `telephone`, `author`, `company_token`)
													 
											 VALUES ('$end_app_date', '$title', '$description', '$duration', '$country', '$city', '$position', '$start_period', '$end_period', '$supervisor_name', 
													 '$supervisor_email', '$telephone', '$system_token', '$company_token')";
$sql4_result=mysqli_query($con,$sql4); 

//line 67: Redirects the user to the final page of upload job vacancy process
header("Location: http://stork-ap.aegean.gr/JobSelection/successful_vacancy_post.php?t=".$system_token);
													 
?>