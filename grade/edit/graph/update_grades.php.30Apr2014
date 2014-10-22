<?php

require '../../../config.php';
require_once '../../lib.php';
require_once $CFG->libdir.'/gradelib.php';


$contextid = required_param('contextid', PARAM_INT);
$courseid_1 = optional_param('courseid', PARAM_INT);

if (!$context = get_context_instance_by_id($contextid)) {
    error('Incorrect context id');
}
if (!$course = $DB->get_record('course',array('id'=> $courseid_1))) {
	echo "No course found for id=".$courseid_1.".";
	print_error('nocourseid');
}

require_login($courseid_1);
$pagename  = get_string('letters', 'grades');
//
$navigation = grade_build_nav(__FILE__, $pagename, $courseid_1);
		 	$navlinks[] = array('name' => "Graph", 'link' => null, 'type' => 'activityinstance');
		 	$navigation = build_navigation($navlinks);
//print_header('Grader Report', 'Grader Report', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
 //print_grade_plugin_selector($courseid_1, 'edit', 'graph'); 
 
print_header('Grade: Graph', 'Grade: Graph', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);		 	

//print_grade_plugin_selector($courseid_1, 'edit', 'graph'); 
/*if ($context->contextlevel == CONTEXT_SYSTEM or $context->contextlevel == CONTEXT_COURSECAT) {
    require_once $CFG->libdir.'/adminlib.php';
    require_login();
    admin_externalpage_setup('letters');
    $admin = true;


} else if ($context->contextlevel == CONTEXT_COURSE) {
    require_login($context->instanceid);
    $admin = false;

} else {
    error('Incorrect context level');
}
*/

 
$letters=grade_get_letters($context);


 if ($records = $DB->get_records('grade_letters', array('contextid'=> $context->id), 'lowerboundary ASC')) {
                   $old_ids = array_keys($records);
                  // print_r($records);
               }
$action_label = "";
$action_update_count = 0;
$action_insert_count = 0;
    foreach($letters as $boundary=>$letter){
         $letter = trim($letter);
           if($letter==''){
             continue;
           }
           $ori_letter = $letter;
           $letter=str_replace("+","1",$letter);
           $newboundary = $_GET['textbox_'.$letter];//required_param('textbox_'.$letter,PARAM_INT);
         // echo $newboundary."hh<br/>";
          $record = new stdClass();
          $record->letter        = $ori_letter;
          $record->lowerboundary = $newboundary;
          $record->contextid     = $context->id;
          if ($old_id = array_pop($old_ids)) {
            $record->id = $old_id;
            $action_update_count++;
            $DB->update_record('grade_letters', $record);
          } else {
            $action_insert_count++;
            $DB->insert_record('grade_letters', $record);
          }
       
    }

if($action_update_count>0){
$action_label .= "<label>  The Grades have been updated successfully.</label><br/>";
}
if($action_insert_count>0){
$action_label .="<label>  The Grades have been inserted successfully.</label><br/>";
}
echo "<b>". $action_label."</b>";
	 echo $OUTPUT->footer();
?>
