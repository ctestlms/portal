<?php

require_once('../../../config.php');
//session_start();
$roleid = $_REQUEST['role'];
$courseid = $SESSION->courseid;
//$sql = "select * from mdl_gradereport_grader where courseid=$courseid";
$submission = $DB->get_record_sql("select * from {gradereport_grader} where courseid=$courseid");
//$record = new stdClass();
$record->id = $submission->id;
$record->courseid = $courseid;
// $record->hodtimestamp = time();
if ($roleid == 23)
    $record->seniorhodtimestamp = time();
if ($roleid == 19)
    $record->hodtimestamp = time();
if ($roleid == 3)
    $record->facultytimestamp = time();
if ($roleid == 24)
    $record->deantimestamp = time();
$DB->update_record("gradereport_grader", $record);
//echo "The grades have been submitted to HOD. To change the grades contact HOD of your school to unlock the gradebook.";
?>
