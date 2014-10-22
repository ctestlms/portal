<?php

require_once('../../../../config.php');
require_once('leave_form.php');
require_once($CFG->dirroot.'/lib/formslib.php');
require_once $CFG->libdir.'/filelib.php';

require_login();
global  $OUTPUT, $PAGE,$DB,$USER;

$PAGE->set_context(context_system::instance());
$url = new moodle_url('/blocks/student_resource_center/requests/leave/leave.php');
$PAGE->set_url($url);

$PAGE->set_title(get_string('leave_title','block_student_resource_center'));
//$pdf = new FPDI();


$form = new cert_form();
if($form->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $redirect_url = new moodle_url('/my/');
    redirect($redirect_url);
}   
else if ($fromform=$form->get_data()) {
 
       // Function doc extension
    function get_extension($file_ext){
        $exten = substr($file_ext, -4);    // get last 4 characters of the filename
        if(ctype_alpha($exten)){
         $exten = substr($file_ext, -5);    
        
        }
        return $exten;     
    }

    $fromform->userid = $USER->id;
    $fromform->request_date = time() ;
    $fromform->request_typeid = 5; 
    $flag = $DB->get_record('requesttype_config',array('typeid'=> 5));
   if($flag->req_flag == 1){
    $fromform->Status = 'Pending at Student';
    }
    else
    {
         $fromform->Status = 'Applied';
    }  
    
     $requestid =  $DB->insert_record('requests', $fromform, true);

//Supporting Doc
if($fromform->supporting_doc1)
{
    
  $file_sd= $form->get_new_filename('supporting_doc1');
  $fullpath = $CFG->tempdir . '/uploads/request_leave/clearanceform_leave_'.$requestid.get_extension($file_sd);
  $override = true;
  $success = $form->save_file('supporting_doc1', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'clearanceform_leave_'.$requestid.get_extension($file_sd);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
}  
}




$redirect_url = new moodle_url('/blocks/student_resource_center/requests/requests_dashboard.php'); 
 redirect($redirect_url);  
}  
else { 
   
echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
}
