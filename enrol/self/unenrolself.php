<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Self enrolment plugin - support for user self unenrolment.
 *
 * @package    enrol_self
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$enrolid = required_param('enrolid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);



$instance = $DB->get_record('enrol', array('id'=>$enrolid, 'enrol'=>'self'), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);



// $context = get_context_instance(CONTEXT_COURSE,$course->id);

/*if ($roles = get_user_roles($context, $USER->id)) {

		foreach ($roles as $role) {
   			$role = $role->name.'<br />';
		}
}
 */

// New Functionality to unenroll a student within start and end date of the enrollement. By Aftab Aslam //


$roles = get_user_roles($context, $USER->id, false);
$role = key($roles);
$rolename = $roles[$role]->name; 

$allowUnEnroll = 0; 
$rolename;

if($rolename == 'Student') {
	   $qry ="select enrolstartdate, enrolenddate from {enrol} where id=$enrolid"; 
		$enrolData = $DB->get_record_sql($qry);
		 
	     $current_date= strtotime(date("Y-m-d h:i:s"));  
		 
		 $startDate = $enrolData->enrolstartdate ; 
		 $endDate = $enrolData->enrolenddate;
		
	if(  ($startDate <= $current_date) && ($current_date <= $endDate)   ){
          $allowUnEnroll = 1; 
	 	} 
		
}
 
  
require_login();
if (!is_enrolled($context)) {
    redirect(new moodle_url('/'));
}
require_login($course);

$plugin = enrol_get_plugin('self');

// Security defined inside following function.
if (!$plugin->get_unenrolself_link($instance)) {
    redirect(new moodle_url('/course/view.php', array('id'=>$course->id)));
}

$PAGE->set_url('/enrol/self/unenrolself.php', array('enrolid'=>$instance->id));
$PAGE->set_title($plugin->get_instance_name($instance));

if ($confirm and confirm_sesskey()) {
    $plugin->unenrol_user($instance, $USER->id);
    add_to_log($course->id, 'course', 'unenrol', '../enrol/users.php?id='.$course->id, $course->id); //TODO: there should be userid somewhere!
    redirect(new moodle_url('/index.php'));
}

echo $OUTPUT->header();
$yesurl = new moodle_url($PAGE->url, array('confirm'=>1, 'sesskey'=>sesskey()));
$nourl = new moodle_url('/course/view.php', array('id'=>$course->id));
$message = get_string('unenrolselfconfirm', 'enrol_self', format_string($course->fullname));

if($rolename == 'Student' && $allowUnEnroll == 0) {
		
		$yesurl = new moodle_url($PAGE->url, array('confirm'=>1, 'sesskey'=>sesskey()));

		  // $message = "You cannot unenroll from this course";
		  echo $OUTPUT->container('You cannot unenroll from this course', 'important', 'notice');
		 	// echo $OUTPUT->confirm($message, $yesurl,$nourl);
	 
  			 
}else {
		echo $OUTPUT->confirm($message, $yesurl, $nourl);
}

echo $OUTPUT->footer();
