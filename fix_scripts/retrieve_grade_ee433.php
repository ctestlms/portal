<?php

/**
 * Script for retrieving deleted grade category/items from DB
 *
 * @author Qurrat-ul-ain Babar
 * @package core
 * @subpackage course
 */
require_once('../config.php');
global $DB;
require_login();

$date = mktime(0, 0, 0, 4, 28, 2014);
//echo $date."<br/>";
$today = userdate($date, get_string('strftimedmy', 'attforblock'));
echo $today."<br/>";

if ($today == "28.04.2014") {
	// echo "Run the script only on $today<br/>";
	echo "Retrieving grades...<br/>";
	$course_gi = $DB->get_records_sql("SELECT * 
		FROM  {grade_items_history} 
		WHERE action=3
		AND courseid=9197
		AND loggeduser=26212
		AND (itemname='Assignment1' OR itemname='Assignment2')
		AND itemtype='manual'");
		
	//print_r($course_gi);
	echo "<br/>";
	
	foreach ($course_gi as $cgi) {
		
		$course = $DB->get_record_sql("SELECT * FROM  {course} WHERE id=$cgi->courseid");
		
		echo $course->fullname."<br/>";
		echo "Item Name: <b>".$cgi->itemname." </b>----- Max Marks: ".$cgi->grademax."<br/>";
		
		$student_grade = $DB->get_records_sql("SELECT * 
			FROM {grade_grades_history} 
			WHERE action=3
			AND loggeduser=26212
			AND source='grade/report/grader/category'
			AND itemid=$cgi->oldid");
			
		// print_r($student_grade);
		$i = 0;
		foreach ($student_grade as $sg) {
			$i++;
			$student = $DB->get_record_sql("SELECT firstname, lastname FROM  {user} WHERE id=$sg->userid");
			echo "$i: $student->firstname $student->lastname -- Marks Obtained: $sg->finalgrade <br/>";
			
		}
		echo "<br/><br/>";
	}
	
	echo "*****End of File*****";
}
else {
	echo "Today is not $today so can't run script....Update date in script and run again<br/>";
}



?>