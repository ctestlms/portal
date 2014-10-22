<?php
require_once('../../config.php');
require_login();
global  $PAGE;
$url = new moodle_url('/blocks/student_resource_center/profile_pdf.php');
$PAGE->set_url($url);
//require_login();

//$context = context_user::instance($USER->id);
//print_object($context);
//require_login(0, false);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('base');
$PAGE->set_title("Profile Complete");
$PAGE->set_heading("Student Strength Report");

$PAGE->navbar->add(get_string('registration_form', 'block_student_resource_center'),$url);
$PAGE->navbar->add("Profile complete",new moodle_url('/blocks/student_resource_center/profile_complete.php'));
global $DB;
    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
   echo '<br/>';
    echo '<p align="center"> Your Profile has been Successfully Created on Moodle NUST, please bring the printed form when you join along with other required documents.</p>';
    echo '<p align="center"><font color="red"> Now you cannot edit your profile, if you need to do that, contact exam branch of your respective school to make changes.</font></b></p>';
 
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer(); 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

