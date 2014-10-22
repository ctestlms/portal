<?php
require('../../config.php');
global $DB, $CFG, $cm;

require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/mod/feedback/lib.php');
set_time_limit(800);
$feedbacks = $_GET['feedbacks'];   
$courseid = $_GET['courseid']; 
$enrolid = $_GET['enrolid']; 

$feedbacks = str_replace("%20"," ",$feedbacks);
require_login();
require_login($courseid);

$context = context_course::instance($courseid, MUST_EXIST);

if (strpos($feedbacks,':') == false) {
    $feedbackname = $feedbacks;
	$feedback = $DB->get_records_sql("select id from {feedback} where course = $courseid AND name LIKE '%".$feedbackname."%'");
	//print_r($feedback);
	if($feedback) {
		foreach($feedback as $f) {
			$module = $DB->get_records_sql("select id from {course_modules} where course = $courseid AND module='23' AND instance=$f->id");
			//print_r($module);
			
			foreach($module as $m) {
				if($m->id) {
					$cm     = get_coursemodule_from_id('', $m->id, 0, true, MUST_EXIST);
					$cm->modname = "feedback";
					$modcontext = context_module::instance($cm->id);
					//echo "Instance $cm->instance <br/>";
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
					$eventdata->courseid   = $courseid;
					$eventdata->userid     = $USER->id;
					events_trigger('mod_deleted', $eventdata);

					add_to_log($courseid, "course", "delete mod",
							   "manage.php?id=$courseid",
							   "$cm->modname $cm->instance", $cm->id);
					$returnstr .= $feedbackname.": The feedback has been deleted successfully<br/>";
				}
			}
		}
	}
	else{
		$returnstr .= $feedbackname.": Either the feedback has already been removed or the feedback instance does not exist<br/>";
	}
	
}
else {
	$feedbackname = explode(':', $feedbacks);
	foreach ($feedbackname as $fn) {
		$feedback = $DB->get_records_sql("select id from {feedback} where course = $courseid AND name LIKE '%".$fn."%'");
		//print_r($feedback);
		if($feedback) {
			foreach($feedback as $f) {
				$module = $DB->get_records_sql("select id from {course_modules} where course = $courseid AND module='23' AND instance=$f->id");
				//print_r($module);
				
				foreach($module as $m) {
					if($m->id) {
						$cm     = get_coursemodule_from_id('', $m->id, 0, true, MUST_EXIST);
						$cm->modname = "feedback";
						$modcontext = context_module::instance($cm->id);
						//echo "Instance $cm->instance <br/>";
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
						$eventdata->courseid   = $courseid;
						$eventdata->userid     = $USER->id;
						events_trigger('mod_deleted', $eventdata);

						add_to_log($courseid, "course", "delete mod",
								   "manage.php?id=$courseid",
								   "$cm->modname $cm->instance", $cm->id);
						$returnstr .= $fn.": The feedback has been deleted successfully<br/>";
					}
				}
			}
		}
		else{
			$returnstr .= $fn.": Either the feedback has already been removed or the feedback instance does not exist<br/>";
		}
		
	}
}

redirect('manage.php?enrolid='.$enrolid, $returnstr, 1);
exit();
?>