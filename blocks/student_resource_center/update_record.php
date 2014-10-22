<?php
require_once('../../config.php');
$schoolid    = required_param('schoolid', PARAM_INT);

global $OUTPUT,$PAGE;
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/student_resource_center/update_record.php');
$PAGE->set_title(get_string('Student_Record_Update', 'block_student_resource_center'));
$PAGE->set_heading(get_string('Student_Record_Update', 'block_student_resource_center'));
$PAGE->set_pagelayout('admin');
//$PAGE->navbar->add(get_string('myhome'),new moodle_url('/my/'));
$PAGE->navbar->add("myhome",new moodle_url('/my/'));
$PAGE->navbar->add(get_string('Student_Record_Update', 'block_student_resource_center'),new moodle_url('/blocks/student_resource_center/update_record.php?schoolid='.$schoolid));
//$PAGE->navbar->add(get_string('Student_Record_Update', 'block_student_resource_center'));
$PAGE->navigation->clear_cache();

//$link1 = new moodle_url();


 echo $OUTPUT->header();
 echo $OUTPUT->box_start();
echo $OUTPUT->action_link(('/blocks/student_resource_center/status_update.php?schoolid='.$schoolid),get_string( 'status_update' , 'block_student_resource_center'));
echo "<br/>";
echo $OUTPUT->action_link(('/blocks/student_resource_center/student_changes.php?schoolid='.$schoolid),get_string( 'section_discipline_change' , 'block_student_resource_center'));
echo $OUTPUT->box_end(); 
echo $OUTPUT->footer();
 