<?php

require_once('../../../../config.php');
require_once('nustid_form.php');
global $CFG;
require('../../lib/fpdi/fpdi.php'); 
//require('lib/fpdi/fpdf.php'); 


require_login();
global  $OUTPUT, $PAGE,$DB,$USER;

$PAGE->set_context(context_system::instance());
$url = new moodle_url('/blocks/student_resource_center/requests/nustid/nustid.php');
$PAGE->set_url($url);

$PAGE->set_title(get_string('nustid','block_student_resource_center'));
//$pdf = new FPDI();


$form = new cert_form();
if($form->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $redirect_url = new moodle_url('/my/');
    redirect($redirect_url);
}
else if ($fromform=$form->get_data()) {
    
    function insert_data($request_data){
         global $DB;
         $DB->insert_record('request_info_data', $request_data, true);
     }
 
   function get_extension($file_ext){
        $exten = substr($file_ext, -4);    // get last 4 characters of the filename
        if(ctype_alpha($exten)){
         $exten = substr($file_ext, -5);    
        
        }
        return $exten;     
    }
    //  print_object($fromform);
    /*
  if($fromform->updatecheckbox == 1){
       $req = new stdClass();
       $req->id = $USER->id;
       $req->phone2 = $fromform->phone2;
     $DB->update_record('user',$req,false);
       
    }
     * 
     */
   
    $fromform->userid = $USER->id;
    $fromform->request_date = time() ;
    $fromform->request_typeid = 2; 
    $fromform->amount_deposited = 500;
    unset($fromform->phone2);
    unset($fromform->updatecheckbox); 
    $flag = $DB->get_record('requesttype_config',array('typeid'=> 2));
   if($flag->req_flag == 1){
    $fromform->Status = 'Pending at Student';
    }
    else
    {
         $fromform->Status = 'Applied';
    }  

     $requestid =  $DB->insert_record('requests', $fromform, true);
     
    $req_data = new stdClass();
    $req_data->id = '';
    $req_data->fieldid = 3;
    $req_data->requestid = $requestid;
    $req_data->data = $fromform->id_reason;
    
    insert_data($req_data);    
    
     
  // Feeslip
  $file_feeslip= $form->get_new_filename('receipt');
 $fullpath = $CFG->tempdir . '/uploads/request_nustid/feeslip_nustid_'.$requestid.get_extension($file_feeslip);
  
  $override = true;
  $success = $form->save_file('receipt', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'feeslip_nustid_'.$requestid.get_extension($file_feeslip);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);   
}
//Supporting Doc
if($fromform->supporting_doc)
{   
  $file_sd= $form->get_new_filename('supporting_doc');
  $fullpath = $CFG->tempdir . '/uploads/request_nustid/supportingdocs_nustid_'.$requestid.get_extension($file_sd);
  $override = true;
  $success = $form->save_file('supporting_doc', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'supportingdocs_nustid_'.$requestid.get_extension($file_sd);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
}  
}
    $redirect_url1 = new moodle_url('/blocks/student_resource_center/requests/requests_dashboard.php'); 
    redirect($redirect_url1);
    
    
}  
else { 
   
echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
}
