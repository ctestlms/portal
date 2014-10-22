
<?php

require_once('../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once('joining_form.php');

$userid =  required_param('userid', PARAM_INT);
$user_group =  required_param('user_group', PARAM_RAW);

require_login();
global  $OUTPUT, $PAGE, $DB;

$PAGE->set_context(context_system::instance());
$url = new moodle_url('/blocks/student_resource_center/joining.php?userid='.$userid.'&user_group='.$user_group);
$PAGE->set_url($url);

$use= $DB->get_record('user',array('id'=>$userid));
//$PAGE->set_pagelayout('mydashboard');
//$PAGE->set_heading('Joining Process');
//$PAGE->navbar->add('Joining Process',$url);
$PAGE->set_title('Joining Process of '.$use->firstname.' '.$use->lastname.' ('.$use->user_subgroup.')');

 $record = $DB->get_record('user',array('id'=>$userid));
$joining = new joining_form(null, array('userid' => $userid, 'user_group' => $user_group));
if($joining->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $redirect_url = new moodle_url('/my/');
    redirect($redirect_url);
}
else if ($fromform=$joining->get_data()) {
    
   
    
    $usertable = new stdClass();
    $usertable->id = $userid;
  //  $usertable->description = 3;
    $status = $fromform->status;
   $usertable->joiningdate = time() ;
    unset($fromform->status);
         if(!$DB->record_exists('joiningdocs', array('userid'=>$userid)) )
       {
           $DB->insert_record('joiningdocs', $fromform, false);
       
       }
       
       else
       {
           $DB->update_record('joiningdocs', $fromform, false);
       }
          profile_load_data($record);
   // print $record->profile_field_registrationstatus;
$record->profile_field_registrationstatus = $status ;
//if (!empty($record->joiningdate)) {
    if($status == 'pending' || $status == 'joined')
    {
     $record->profile_field_joiningdate = time() ;   
    }
    else
   {
     $record->profile_field_joiningdate = 0 ;   
    }
        
//}
/*else{
   if($status == 'pending' || $status == 'joined')
    {
     $record->profile_field_joiningdate = time() ;   
    }
    else
   {
     $record->profile_field_joiningdate = 0 ;   
    }  
}
 * 
 */
profile_save_data($record);
       
       
       
echo '<script type="text/javascript"> opener.location.reload(true);self.close(); </script>';         
//   redirect("$CFG->wwwroot/blocks/student_resource_center/student_new_reg.php");
   }
  
   // print_object($fromform);
 //  redirect("$CFG->wwwroot/blocks/student_resource_center/joining_docs.php");


else { 
    
     if($DB->record_exists('joiningdocs', array('userid'=>$userid)) ) 
    {
     
       $user = $DB->get_record('joiningdocs', array('userid'=>$userid));
       $user->status = $record->profile_field_registrationstatus;
       $joining->set_data($user);
    }
   
    
    
echo $OUTPUT->header();
$joining->display();
echo $OUTPUT->footer();
}
