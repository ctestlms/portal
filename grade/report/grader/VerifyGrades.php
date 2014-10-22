<?php

require_once('../../../config.php');
session_start();
$roleid = $_REQUEST['role'];
$courseid = $_SESSION['courseid'];
$sql = "select id from mdl_grade_items where courseid=$courseid ";
$grade_ids = $DB->get_records_sql($sql);

foreach ($grade_ids as $grade_id) {
    $record = new stdClass();
    $record->id = $grade_id->id;
    $record->courseid = $courseid;
    $record->locked = 1;
    $DB->update_record("grade_items", $record);
}

$sql1="select id from mdl_gradereport_grader where courseid=$courseid ";
$ids=$DB->get_record_sql($sql1);
if($ids) {
	$record1 = new stdClass();
	//if ($roleid == 3){
	$record1->id = $ids->id;
	$record1->facultytimestamp = time();
	$record1->courseid =$courseid ;

	$DB->update_record("gradereport_grader", $record1);
}
else {
	$record1 = new stdClass();
	//if ($roleid == 3){
	$record1->facultytimestamp = time();
	$record1->courseid =$courseid ;

	$DB->insert_record("gradereport_grader", $record1);
}

echo "The grades have been submitted to HOD. To change the grades contact HOD of your school to unlock the gradebook.";
$course = $DB->get_record('course', array('id' => $courseid));

require_once("$CFG->dirroot/enrol/locallib.php");
$manager = new course_enrolment_manager($PAGE, $course, $filter);
//print_r($manager->get_context()->instanceid);

$users_ = $manager->get_users('firstname'); //get_user_roles($USER->id);
   
foreach ($users_ as $user) {
    $roles = $manager->get_user_roles($user->id);
    // print_r($roles);
    foreach ($roles as $key => $role) {
        if ($key == 19) {
            $user->mailformat=1;
            $user->maildigest=0;
            $user->maildisplay=2;
            $user->htmleditor=1;
            $user->policyagreed=1;
            $user->suspended=0;
            email_to_user($user, get_admin(), 'Gradebook Submitted','','The Gradebook of the course <a href="lms.nust.edu.pk/portal/course/view.php?id='.$course->id.'">'. $course->fullname . ' </a> has been submitted by ' . $USER->firstname . ' ' . $USER->lastname, $attachment = '', $attachname = '', $usetrueaddress = true, $replyto = '', $replytoname = '', $wordwrapwidth = 79);
 
           }
    }
}

?>

