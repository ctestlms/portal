<?php

require_once('../../config.php');
require_once('profile_editform.php');
require('../../mod/attforblock/tcpdf/config/lang/eng.php');
require('../../mod/attforblock/tcpdf/tcpdf.php');

require_login();
global  $OUTPUT,$USER, $PAGE, $DB;
$userid = required_param('userid', PARAM_INT);
$PAGE->set_context(context_system::instance());
$url = new moodle_url('/blocks/student_resource_center/profile_edit.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_heading("Profile Edit");
$PAGE->navbar->add(get_string('registration_form', 'block_student_resource_center'),$url);
$PAGE->set_title("Profile Edit");

  
$profile = new profile_editform(null,array('userid'=>$userid));

if($profile->is_cancelled()) {
    
    redirect("$CFG->wwwroot/blocks/student_resource_center/status_update.php");   
} else if ($fromform=$profile->get_data()) {

     if(!$DB->record_exists('regform', array('userid'=>$userid)) ) 
   {
    $DB->insert_record('regform', $fromform, false);
   }
  else
  {
   
    $DB->update_record('regform', $fromform, false);
  }
redirect("$CFG->wwwroot/blocks/student_resource_center/status_update.php");   

} 
else { 
    
   echo $OUTPUT->header();
    
    if($DB->record_exists('regform', array('userid'=>$userid)) ) 
    {
        
       $user = $DB->get_record('regform', array('userid'=>$userid)); 
   //    print_object($user);
       $profile->set_data($user);
    }
    

$profile->display();
echo $OUTPUT->footer();
}

