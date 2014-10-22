<?php
/**
 * Script for adding missing grades in QA feedback form for Spring 2014 courses
 * it first deletes the existing facultyfeedback form and then creates a new one.
 *
 * @author Qurrat-ul-ain Babar
 * @package core
 * @subpackage course
 *
 */

require_once('../config.php');
global $DB, $CFG, $cm;

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_login();	
$springsemstartdate = mktime(0, 0, 0, 2, 3, 2014);

$school = $DB->get_record_sql("select id, name from {course_categories} where name='School of Mechanical and Manufacturing Engineering (SMME)'");
$schoolcategories = $DB->get_records_sql("select * from {course_categories} where path LIKE '%/{$school->id}%'");
$i = 0;
echo "<b>$school->name</b><br/>";
foreach($schoolcategories as $sc) {
	//echo "$sc->id<br/>";
	$springcourses = $DB->get_records_sql("select id, fullname, category from {course} where startdate >= $springsemstartdate AND category = $sc->id");
	if($springcourses) {
		//print_r($springcourses);
		
		foreach ($springcourses as $course) {
			$i++;
			echo "$i ---- $course->category / $course->id : $course->fullname<br/>";
			$module = $DB->get_record_sql("select id from {course_modules} where course = $course->id AND module='29'");
			
			if($module->id) {
				echo "Module ID: ".$module->id."<br/>";
				$cm  = get_coursemodule_from_id('', $module->id, 0, true, MUST_EXIST);
				$cm->modname = "facultyfeedback";
				echo "Instance $cm->instance <br/>";
				$modlib = "$CFG->dirroot/mod/$cm->modname/lib.php";

				if (file_exists($modlib)) {
					include_once($modlib);
				} else {
					throw new moodle_exception("Ajax rest.php: This module is missing mod/$cm->modname/lib.php");
				}
				$deleteinstancefunction = $cm->modname."_delete_instance";

				// Run the module's cleanup funtion.
				if (!$deleteinstancefunction($cm->instance)) {
					throw new moodle_exception("Ajax rest.php: Could not delete the $cm->modname $cm->name (instance)");
					die;
				}

				// remove all module files in case modules forget to do that
				$fs = get_file_storage();
				$fs->delete_area_files($modcontext->id);

				if (!delete_course_module($cm->id)) {
					throw new moodle_exception("Ajax rest.php: Could not delete the $cm->modname $cm->name (coursemodule)");
				}

				// Remove the course_modules entry.
				if (!delete_mod_from_section($cm->id, $cm->section)) {
					throw new moodle_exception("Ajax rest.php: Could not delete the $cm->modname $cm->name from section");
				}

				// Trigger a mod_deleted event with information about this module.
				$eventdata = new stdClass();
				$eventdata->modulename = $cm->modname;
				$eventdata->cmid       = $cm->id;
				$eventdata->courseid   = $course->id;
				$eventdata->userid     = $USER->id;
				events_trigger('mod_deleted', $eventdata);

				// add_to_log($courseid, "course", "delete mod",
						   // "view.php?id=$courseid",
						   // "$cm->modname $cm->instance", $cm->id);
				echo "Deleted!!!<br/>";
						
				create_feedback_activity($course, "Faculty Course Overview Report", "facultycoursevaluation.xml", "facultyfeedback");
				echo "Created after deletion!!!<br/> ----------------------------------<br/>";
			}
			else {
				create_feedback_activity($course, "Faculty Course Overview Report", "facultycoursevaluation.xml", "facultyfeedback");
				echo "Created first time!!!<br/>----------------------------------------<br/>";
			} 
		}
	}
	// $springcourses .= $courses;
}

echo "<br/>Faculty Course Overview Reports updated!!!<br/><br/>";
echo "***** End of File *****";

?>