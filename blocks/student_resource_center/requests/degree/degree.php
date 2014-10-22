<?php

require_once('../../../../config.php');
require_once('degree_form.php');
require_once($CFG->dirroot.'/lib/formslib.php'); 
require_once $CFG->libdir.'/filelib.php';
require_login();
global  $OUTPUT, $PAGE,$DB,$USER;

$PAGE->set_context(context_system::instance());
$url = new moodle_url('/blocks/student_resource_center/requests/degree/degree.php');
$PAGE->set_url($url);

$PAGE->set_title(get_string('degree','block_student_resource_center'));
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
 
       // Function doc extension
    function get_extension($file_ext){
        $exten = substr($file_ext, -4);    // get last 4 characters of the filename
        if(ctype_alpha($exten)){
         $exten = substr($file_ext, -5);    
        
        }
        return $exten;     
    }
/*
  if($fromform->updatecheckbox == 1){
       $req = new stdClass();
       $req->id = $USER->id;
       $req->phone2 = $fromform->phone2;
     
     $DB->update_record('user',$req,false);
 * 
 */
     
   // }
    $fromform->userid = $USER->id;
    $fromform->request_date = time() ;
    $fromform->request_typeid = 3;  
    $fromform->amount_deposited = 1000;
   $que = "SELECT req_flag FROM {requesttype_config} WHERE typeid = 3";
    $flag = $DB->get_record_sql($que);
 //  print_object($flag);
    if($flag->req_flag == 1){
    $fromform->Status = 'Pending at Student';
    }
    else
    {
         $fromform->Status = 'Applied';
    }    
    //$fromform->receipt_no = $fromform->receipt_no;
    //$fromform->deposit_date = $fromform->deposit_date;
    //$fromform->status = 'Applied';
   // unset($fromform->phone2);
   // unset($fromform->updatecheckbox); 
    //unset($fromform->receipt); 
    
     $requestid =  $DB->insert_record('requests', $fromform, true);
  
      $req_data = new stdClass();
    $req_data->id = '';
    $req_data->fieldid = 4;
    $req_data->requestid = $requestid;
    $req_data->data = $fromform->degree_reason;
    
    insert_data($req_data);    
 
  
  // Feeslip
  $file_feeslip= $form->get_new_filename('receipt');
 $fullpath = $CFG->tempdir . '/uploads/request_degree/feeslip_degree_'.$requestid.get_extension($file_feeslip);
  
  $override = true;
  $success = $form->save_file('receipt', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'feeslip_degree_'.$requestid.get_extension($file_feeslip);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
    
}
  

//Supporting Doc
if($fromform->supporting_doc)
{
    
  $file_sd= $form->get_new_filename('supporting_doc');
  $fullpath = $CFG->tempdir . '/uploads/request_degree/evidencedoc_degree_'.$requestid.get_extension($file_sd);
  $override = true;
  $success = $form->save_file('supporting_doc', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'evidencedoc_degree_'.$requestid.get_extension($file_sd);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
}  
}

 if($fromform->supporting_doc1)
{
     
  $file_sd1= $form->get_new_filename('supporting_doc1');
  $fullpath = $CFG->tempdir . '/uploads/request_degree/clearancedoc_degree_'.$requestid.get_extension($file_sd1);
  $override = true;
  $success = $form->save_file('supporting_doc1', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'clearancedoc_degree_'.$requestid.get_extension($file_sd1);
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
