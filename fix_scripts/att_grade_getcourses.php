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
	//$springsemdate = mktime(0, 0, 0, 2, 3, 2014);
	$getattgradeitems = $DB->get_records_sql("select * from {grade_items} where itemtype='mod' AND itemmodule='attforblock'");

	//print_r($springcourses);
	$i = 0;
	foreach($getattgradeitems as $gi) {
		$i++;
		echo "$i: Course ID: $gi->courseid --- Category ID: $gi->categoryid --- ";
		$course = $DB->get_record('course', array('id'=> $gi->courseid));
		echo "$course->fullname<br/>";
	}
	echo "*****End of File*****";
}
else {
	echo "Today is not $today so can't run script....Update date in script and run again<br/>";
}

?>