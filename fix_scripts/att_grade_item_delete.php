<?php
/**
 * Script for deleting attendance grade item from grade book for Spring Semester 2014 courses
 *
 * @author Qurrat-ul-ain Babar
 * @package core
 * @subpackage course
 */
require_once('../config.php');
global $DB, $CFG;
require_once($CFG->libdir.'/gradelib.php');
require_login();

$date = mktime(0, 0, 0, 3, 12, 2014);
// echo $date."<br/>";
$today = userdate($date, get_string('strftimedmy', 'attforblock'));
// echo $today."<br/>";

if ($today == "12.03.2014") {
	echo "Run the script only on $today<br/>";
	$getattgradeitems = $DB->get_records_sql("select * from {grade_items} where itemtype='mod' AND itemmodule='attforblock'");

	//print_r($getattgradeitems);
	$i = 0;
	foreach($getattgradeitems as $gi) {
		$i++;
		echo "$i: Course ID: $gi->courseid --- Category ID: $gi->categoryid --- ";
		$course = $DB->get_record('course', array('id'=> $gi->courseid));
		echo "$course->fullname<br/>";
		if($gi->gradetype != 0) {
			$grade = new stdClass();
			$grade->grademax = 0.00000;
			$grade->id = $gi->id;
			$grade->gradetype = 0.00000;
			$DB->update_record('grade_items', $grade);
			echo "Attendance grade item updated <br/>";
		}
		else
			echo "Attendance grade item was already updated by user <br/>";
		
		
	}
	echo "*****End of File*****";
}
else {
	echo "Today is not $today so can't run script....Update date in script and run again<br/>";
}

?>