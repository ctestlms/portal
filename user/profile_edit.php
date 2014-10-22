<?php

require_once('../config.php');
require_once('profile_editform.php');


require_login();
global  $OUTPUT, $PAGE, $DB;
$userid = required_param('userid', PARAM_INT);
$schoolid       = optional_param('schoolid', 83, PARAM_INT);
$PAGE->set_context(context_system::instance());
$url = new moodle_url('/blocks/student_resource_center/profile_edit.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_heading("Profile Edit");
$PAGE->navbar->add(get_string('registration_form', 'block_student_resource_center'),$url);
$PAGE->set_title("Profile Edit");

  
$profile = new profile_editform(null,array('userid'=>$userid));

if($profile->is_cancelled()) {
    
    redirect("$CFG->wwwroot/blocks/student_resource_center/status_update.php?schoolid=$schoolid");   
} else if ($fromform=$profile->get_data()) {

     if(!$DB->record_exists('regform', array('userid'=>$userid)) ) 
   {
    $DB->insert_record('regform', $fromform, false);
   }
  else
  {
   
    $DB->update_record('regform', $fromform, false);
  }
  echo $schoolid;
redirect("$CFG->wwwroot/blocks/student_resource_center/status_update.php?schoolid=$schoolid");   

} 
else { 
    
   echo $OUTPUT->header();
    
    if($DB->record_exists('regform', array('userid'=>$userid)) ) 
    {
        
       $user = $DB->get_record('regform', array('userid'=>$userid)); 
   //    print_object($user);
         
     $user->nicno1 = substr($user->nicno,0,5) ;
     $user->nicno2 = substr($user->nicno,6,7) ;
     $user->nicno3 = substr($user->nicno,14,1) ;
     unset($user->nicno);

       $profile->set_data($user);
    }
    

$profile->display();
echo $OUTPUT->footer();
}

