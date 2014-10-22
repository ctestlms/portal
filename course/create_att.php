<?php
require_once('../config.php');
require_once($CFG->dirroot .'/course/lib.php');
global $CFG, $DB;
$startdate =  mktime(0, 0, 0, date("m")-5, 1, date("Y"));
$sql="SELECT * FROM {course} WHERE startdate >".$startdate;
$course=$DB->get_records_sql($sql);
//print_r($course);

foreach ($course as $c)
{
	$courseid = $c->id;
	$category = $c->category;
	echo $courseid.": $category<br/>";
		if ($category != 0) {
			if ($attendances = $DB->get_records_select("attforblock", "course = ? ", array($courseid), "id DESC")) {
				//print_r($attendances);
				// There should always only be ONE, but with the right combination of
				// errors there might be more.  In this case, just return the oldest one (lowest ID).
				// foreach ($attendances as $attendance) {
					// print_r($attendance);
					//return $attendance;   // ie the last one
				// }
			}
			else
			{
				echo "No attendance instance<br/>";
				//saving the attendance in db 
						$attendance->id = '';
						$attendance->course = $courseid;
						$attendance->name  = "Attendance";
						$attendance->grade = 0;
						$attendanceid = $DB->insert_record("attforblock", $attendance);
				echo "Attendance ID var ".$attendanceid." ";
				print_r($attendance);
				echo "<br/>Attendance instance inserted<br/>";
				if (!$DB->get_records('attendance_statuses', array('attendanceid'=>$attendanceid))) {
					echo " in attendance statuses ";
					$statuses = $DB->get_records('attendance_statuses', array('attendanceid'=>0));
					//print_r($statuses);
					foreach($statuses as $stat) {
						//print_r($stat);
						
						// echo "$attendanceid ".$stat->attendanceid." ";
						// echo $stat->acronym." ";
						// echo $stat->description." ";
						// echo $stat->grade." ";
						// echo $stat->visible." ";
						// echo $stat->deleted." ";
						
						$rec = new stdClass();
						$rec->id = '';
						$rec->attendanceid = $attendanceid;
						$rec->acronym = $stat->acronym;
						$rec->description = $stat->description;
						$rec->grade = $stat->grade;
						$rec->visible = $stat->visible;
						$rec->deleted = $stat->deleted;
						//echo $rec."<br/>";
						$recid = $DB->insert_record('attendance_statuses', $rec);
						//echo $recid."<br/>";
						
						$rec->id = $recid;
					} 
				}
				else
				{
					//echo " statuses found ";
				}
				$attendance->id = $attendanceid;
				
				if (! $module = $DB->get_record("modules", array("name" => "attforblock"))) {
							echo $OUTPUT->notification("Could not find attendance module!!");
							return false;
					}
					$mod = new stdClass();
					$mod->course = $courseid;
					$mod->module = $module->id;
					$mod->instance = $attendance->id;
					$mod->section = 0;

					if (! $mod->coursemodule = add_course_module($mod) ) {   // assumes course/lib.php is loaded
						echo $OUTPUT->notification("Could not add a new course module to the course '" . $courseid . "'");
							return false;
					}
					if (! $sectionid = add_mod_to_section($mod) ) {   // assumes course/lib.php is loaded
						echo $OUTPUT->notification("Could not add the new course module to that section");
						return false;
					}
					$DB->set_field("course_modules", "section", $sectionid, array("id" => $mod->coursemodule));
			}
		}
		else
		{
			echo "Category is 0 <br/>";
			
		}
		
	//print_r($attendance);	
}

echo "<br/><br/>Finished!!!<br/>";
?>