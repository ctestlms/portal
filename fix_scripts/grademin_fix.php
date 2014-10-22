<?php
/**
 * Script for updating grademin from 1 to 0 for Spring Semester 2014 courses
 *
 * @author Qurrat-ul-ain Babar
 * @package core
 * @subpackage course
 */
require_once('../config.php');
global $DB;
require_login();

$date = mktime(0, 0, 0, 3, 3, 2014);
//echo $date."<br/>";
$today = userdate($date, get_string('strftimedmy', 'attforblock'));
// echo $today."<br/>";

if ($today == "3.03.2014") {
	echo "Run the script only on $today<br/>";
	$springsemdate = mktime(0, 0, 0, 2, 3, 2014);
	
	$springcourses = $DB->get_records_sql("select id, fullname from {course} where startdate >= $springsemdate");

	// print_r($springcourses);

	foreach($springcourses as $course) {
		echo "<b>".$course->fullname."</b><br/>";
		$gradeitems = $DB->get_records_sql("select * from {grade_items} where itemtype='manual' AND courseid = $course->id");
		// print_r($gradeitems);
		foreach($gradeitems as $gi) {
			echo $gi->itemname.": Grade Min: ".$gi->grademin."<br/>";
			if ($gi->grademin == 1.00000) {
				//$grade = $DB->get_record('grade_items', array('id' => $gi->id);
				$grade = new stdClass();
				$grade->grademin = 0.00000;
				$grade->id = $gi->id;
				$DB->update_record('grade_items', $grade);	
			}
		}
		
		$gradeitemshistory = $DB->get_records_sql("select * from {grade_items_history} where itemtype='manual' AND courseid = $course->id");
		
		foreach($gradeitemshistory as $gi) {
			echo $gi->itemname.": Grade Min: ".$gi->grademin."<br/>";
			if ($gi->grademin == 1.00000) {
				//$grade = $DB->get_record('grade_items', array('id' => $gi->id);
				$grade = new stdClass();
				$grade->grademin = 0.00000;
				$grade->id = $gi->id;
				$DB->update_record('grade_items_history', $grade);	
				echo "Grademin updated <br/>";
			}
		}
	}

	echo "<br/>Grade items updated!!!<br/><br/>";
	echo "*****End of File*****";
}
else {
	echo "Today is not $today so can't run script....Update date in script and run again<br/>";
}



?>