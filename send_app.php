<?php
//lines 3-4: Gets the variable values from the form
$telephone_number= $_POST["student_tel"];
$student_email= $_POST["student_email"];

//lines 7-11: Establish database connection
$con = mysqli_connect("localhost","root","fcD6EGaTQsp6LXC9","JobSelection"); 
//on connection failure, throw an error
if(!$con) {  
die('Could not connect: '.mysql_error()); 
} 

//line 14: Get the user's system token passed from the URL
$student_token= $_GET["t"];

//lines 17-1: Update student's entry in DB
require_once('wp-config.php');

$sql0 = "UPDATE students SET telephone_number='". $telephone_number  ."'" .", student_email='". $student_email ."'" ." WHERE student_token='". $student_token  ."'";

$sql0_result=mysqli_query($con,$sql0);

//lines 24-31: Get the job id matched with the student_token value
require_once('wp-config.php');
$sql1 = "SELECT job_id FROM tokens WHERE system_token='". $student_token ."'";
	$sql1_result= mysqli_query($con,$sql1); 
		
	if ($sql1_result->num_rows ==1){
		$row = $sql1_result->fetch_assoc();
		$job_id= $row["job_id"];
	}
	
//lines 34- 54: Get the job's info matched with the saved job_id value
require_once('wp-config.php');
$sql2 = "SELECT * FROM job_vacancies WHERE job_id='". $job_id ."'";
	$sql2_result= mysqli_query($con,$sql2); 
		
	if ($sql2_result->num_rows ==1){
		$row = $sql2_result->fetch_assoc();
		$job_id= $row["job_id"];
		$title= $row["title"];
		$description= $row["description"];
		$duration= $row["duration"];
		$country= $row["country"];
		$city= $row["city"];
		$position= $row["position"];
		$start_period= $row["start_period"];
		$end_period= $row["end_period"];
		$supervisor_name= $row["supervisor_name"];
		$supervisor_email= $row["supervisor_email"];
		$telephone= $row["telephone"];
		$author= $row["author"];
		$company_token= $row["company_token"];
	}

//lines 57-71: Get the student's info from students table matched with the saved student_token value
$sql3 = "SELECT * FROM students WHERE student_token='" .$student_token ."'";
	$sql3_result=mysqli_query($con,$sql3);
	
	if ($sql3_result->num_rows ==1){
		$row = $sql3_result->fetch_assoc();
		$givenName = $row["givenName"];
		$surname = $row["surname"];
		$isStudent = $row["isStudent"];
		$nameOfInstitution_isStudent = $row["nameOfInstitution_isStudent"];
		$study_isStudent = $row["study_isStudent"];
		$hasDegree = $row["hasDegree"];
		$level = $row["level"];
		$yearObtained = $row["yearObtained"];
		$nameOfInstitution_hasDegree = $row["nameOfInstitution_hasDegree"];
	}


$headers="";
$opts = '-F"Internship Apllication Platform - UAegean"';
	
//lines 78- 120: Prepare and send email to student and job's supervisor
if(isset($student_email) || isset($supervisor_email)){
	$msg1 .= "Dear $givenName $surname, \n\n";
	$msg1 .="Your application has been successfully send. \n\n";
	$msg1 .="Find below the internship position details: \n";
	$msg1 .="Job ID: $job_id \n";
	$msg1 .="Title: $title \n";
	$msg1 .="Description: $description \n";
	$msg1 .="Duration: $duration \n";
	$msg1 .="Country: $country \n";
	$msg1 .="City: $city \n";
	$msg1 .="Position: $position \n";
	$msg1 .="Start Period: $start_period \n";
	$msg1 .="End Period: $end_period \n";
	$msg1 .="Supervisor Name: $supervisor_name \n";
	$msg1 .="Supervisor Email: $supervisor_email \n";
	$msg1 .="Telephone Number: $telephone \n\n";
	$msg1 .="The company's supervisor will contact you in order to announce the selection's result. \n\n";
	$msg1 .="With regards, \n the Internship Application Platform Team";
	
	mail($student_email, 'Application Details - Internship Application Platform', $msg1, $headers, $opts);
	
	$msg2 .= "Dear $supervisor_name, \n\n";
	$msg2 .="A new application was submited for the position of \"$title\". \n\n";
	$msg2 .="The student's details, provided by his/ her application, are listed below: \n";
	$msg2 .="First Name: $givenName \n";
	$msg2 .="Last Name: $surname \n";
	$msg2 .="Email: $student_email \n";
	$msg2 .="Telephone Number: $telephone_number \n";
	$msg2 .="Student: \n";
	$msg2 .="Institution: $nameOfInstitution_isStudent \n";
	$msg2 .="Field of Study: $study_isStudent \n";
	
if ($level!="" || $yearObtained="" || $nameOfInstitution_hasDegree!=""){	
	$msg2 .="Degree: \n";
	$msg2 .="Level: $level \n";
	$msq2 .="Year Obtained: $yearObtained \n";
	$msg2 .="Institution: $nameOfInstitution_hasDegree \n\n";
}
	$msg2 .="Please contact the applicant in order to inform him/ her about your decision. \n\n";
	$msg2 .="With regards, \n the Internship Application Platform Team";
	
	
	mail($supervisor_email, 'New Application - Internship Application Platform', $msg2, $headers, $opts);
	
	//line 123: Redirect the user-student to the final page of the apply for a vacancy process
	header("Location: http://stork-ap.aegean.gr/JobSelection/application_submited.php");
}else{
	header("Location: http://stork-ap.aegean.gr/JobSelection/process_failed.php"); 
}

?>