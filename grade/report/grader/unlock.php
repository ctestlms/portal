<?php
require_once('../../../config.php');

session_start();
$comment = $_REQUEST['comment'];
$courseid=$_SESSION['courseid']  ;
$sql="select id from mdl_grade_items where courseid=$courseid ";
$grade_ids=$DB->get_records_sql($sql);
foreach ($grade_ids as $grade_id){
        $record = new stdClass();
        $record->id         = $grade_id->id;
        $record->courseid         = $courseid;
        $record->locked = 0;
        $DB->update_record("grade_items", $record);
         
}         
echo "Gradebook has been unlocked";

$sql1="select * from mdl_gradereport_grader where courseid=$courseid ";
$ids=$DB->get_record_sql($sql1);

if($ids) {
	$record1 = new stdClass();
	//if ($roleid == 3){
	$record1->id = $ids->id;
	$record1->facultytimestamp = NULL;
	$record1->hodtimestamp = NULL;
	$record1->shodtimestamp = NULL;
	$record1->deantimestamp = NULL;
	$record1->courseid =$courseid ;

	$DB->update_record("gradereport_grader", $record1);
}

$course = $DB->get_record('course', array('id' => $courseid));
$query = "SELECT u.* from mdl_user u
                                                               JOIN {$CFG->prefix}role_assignments ra ON ra.userid=u.id
                                                               JOIN {$CFG->prefix}role r ON ra.roleid = r.id
                                                               JOIN {$CFG->prefix}context c ON ra.contextid = c.id
                                                               where r.name = 'Teacher' and
                                                               c.contextlevel = 50 and
                                                               c.instanceid = {$courseid}";
$teachers = $DB->get_records_sql($query);
foreach($teachers as $teacher){
    email_to_user($teacher, get_admin(), 'Gradebook Unlocked', '', 'The Gradebook has been unlocked for the course ' . $course->fullname . '<br/>Message: '.$comment, $attachment = '', $attachname = '', $usetrueaddress = true, $replyto = '', $replytoname = '', $wordwrapwidth = 79);
} 
 
    
    

?>

