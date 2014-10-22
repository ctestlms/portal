<?php

require_once('../../../../config.php');
require_once('transcript_form.php');
require_once($CFG->dirroot.'/lib/formslib.php');
//global $CFG;
require('../../lib/fpdi/fpdi.php'); 
require_once $CFG->libdir.'/filelib.php';
//require_once("{$CFG->libdir}/formslib.php");
//require('lib/fpdi/fpdf.php'); 


require_login();
global  $OUTPUT, $PAGE,$DB,$USER;

$PAGE->set_context(context_system::instance());
$url = new moodle_url('/blocks/student_resource_center/requests/transcript/transcript.php');
$PAGE->set_url($url);

$PAGE->set_title(get_string('transcript_title','block_student_resource_center'));
//$pdf = new FPDI();


$form = new cert_form();
if($form->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $redirect_url = new moodle_url('/my/');
    redirect($redirect_url);
}
else if ($fromform=$form->get_data()) {
   
    //Function to insert in request_info_fields
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

  
    ///////////////////////MAIN REQUEST INSERT RECORD ////////////////////////////////////////////////////
    
    $main_rec = new stdClass();
    $main_rec->id = '';
    $main_rec->userid = $USER->id;
    $main_rec->request_date = time() ;
    $main_rec->request_typeid = 4;
    $copies = (int)$fromform->copies_no;
    if($fromform->request_type == 'Urgent'){
        
    $main_rec->amount_deposited = 500*$copies+500;
    }
    else{
    $main_rec->amount_deposited = 500*$copies;    
    }
    $main_rec->receipt_no = $fromform->receipt_no;
    $main_rec->deposit_date = $fromform->deposit_date;
    //Flag Check
    
$flag = $DB->get_record('requesttype_config',array('typeid'=> 4));
   if($flag->req_flag == 1){
    $main_rec->Status = 'Pending at Student';
    }
    else
    {
         $main_rec->Status = 'Applied';
    }        
    
    $requestid =  $DB->insert_record('requests', $main_rec, true);
  
    ////////////////////////////REQUEST_INFO_DATE ////////////////////////////////////////////////////////////////
    $req_data = new stdClass();
    $req_data->id = '';
    $req_data->fieldid = 5;
    $req_data->requestid = $requestid;
    $req_data->data = $fromform->request_type;
 
    $req_data1 = new stdClass();
    $req_data1->id = '';
    $req_data1->fieldid = 6;
    $req_data1->requestid = $requestid;
    $req_data1->data = $fromform->copies_no;
    
    $req_data2 = new stdClass();
    $req_data2->id = '';
    $req_data2->fieldid = 7;
    $req_data2->requestid = $requestid;
    $req_data2->data = $fromform->changes_biodata;
    
    $req_data3 = new stdClass();
    $req_data3->id = '';
    $req_data3->fieldid = 9;
    $req_data3->requestid = $requestid;
    $req_data3->data = $fromform->before_copy;
    
      $req_data9 = new stdClass();
    $req_data9->id = '';
    $req_data9->fieldid = 15;
    $req_data9->requestid = $requestid;
    $req_data9->data = $fromform->sealed_env;
    
    $req_data4 = new stdClass();
    $req_data4->id = '';
    $req_data4->fieldid = 10;
    $req_data4->requestid = $requestid;
    $req_data4->data = $fromform->delivery_mode;
    
     $req_data12 = new stdClass();
    $req_data12->id = '';
    $req_data12->fieldid = 17;
    $req_data12->requestid = $requestid;
    $req_data12->data = $fromform->updatecheckbox4;
    
   
   $DB->insert_records_via_batch('request_info_data',array($req_data,$req_data1,$req_data2,$req_data3,$req_data4,$req_data9,$req_data12));
      
           if($fromform->delivery_mode == 'By Hand'){
               $req_data5 = new stdClass();
    $req_data5->id = '';
    $req_data5->fieldid = 11;
    $req_data5->requestid = $requestid;
    $req_data5->data = $fromform->byhand_person;
     insert_data($req_data5);
     if($fromform->byhand_person == 1){
          $req_data6 = new stdClass();
    $req_data6->id = '';
    $req_data6->fieldid = 12;
    $req_data6->requestid = $requestid;
    $req_data6->data = $fromform->authorized_name;
    
    $req_data7 = new stdClass();
    $req_data7->id = '';
    $req_data7->fieldid = 13;
    $req_data7->requestid = $requestid;
    $req_data7->data = $fromform->authorized_cnic;
    
    $req_data8 = new stdClass();
    $req_data8->id = '';
    $req_data8->fieldid = 14;
    $req_data8->requestid = $requestid;
    $req_data8->data = $fromform->authorized_phone;
    
    
    
    $DB->insert_records_via_batch('request_info_data',  array($req_data6,$req_data7,$req_data8));
     }
           }
           
           if($fromform->changes_biodata == 1){
    $req_data9 = new stdClass();
    $req_data9->id = '';
    $req_data9->fieldid = 8;
    $req_data9->requestid = $requestid;
    $req_data9->data = $fromform->details_biodata; 
    insert_data($req_data9);
    }
    if($fromform->sealed_env == 1){
    $req_data10 = new stdClass();
    $req_data10->id = '';
    $req_data10->fieldid = 16;
    $req_data10->requestid = $requestid;
    $req_data10->data = $fromform->env_copy; 
    insert_data($req_data10);
    }
    
     
   
    
    //////////////////////////////////FILES UPLOAD////////////////////////////////////////////////////////////////////////////////////////
  // Feeslip
  $file_feeslip= $form->get_new_filename('receipt');
 $fullpath = $CFG->tempdir . '/uploads/request_transcript/feeslip_transcript_'.$requestid.get_extension($file_feeslip);
  
  $override = true;
  $success = $form->save_file('receipt', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'feeslip_transcript_'.$requestid.get_extension($file_feeslip);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
    
}

//If before issued transcript, attach copy
     if($fromform->before_copy){
if($fromform->supporting_doc)
{
    
  $file_sd= $form->get_new_filename('supporting_doc');
  $fullpath = $CFG->tempdir . '/uploads/request_transcript/beforecopy_transcript_'.$requestid.get_extension($file_sd);
  $override = true;
  $success = $form->save_file('supporting_doc', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'beforecopy_transcript_'.$requestid.get_extension($file_sd);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
}  
}
}
     
//Clearance form
 if($fromform->supporting_doc1)
{
     
  $file_sd1= $form->get_new_filename('supporting_doc1');
  $fullpath = $CFG->tempdir . '/uploads/request_transcript/clearancedoc_transcript_'.$requestid.get_extension($file_sd1);
  $override = true;
  $success = $form->save_file('supporting_doc1', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'clearancedoc_transcript_'.$requestid.get_extension($file_sd1);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
}  
}
     
     if($fromform->byhand_person){
     if($fromform->supporting_doc2)
{
     
  $file_sd1= $form->get_new_filename('supporting_doc2');
  $fullpath = $CFG->tempdir . '/uploads/request_transcript/authorizedpersoncnic_transcript_'.$requestid.get_extension($file_sd1);
  $override = true;
  $success = $form->save_file('supporting_doc2', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'authorizedpersoncnic_transcript_'.$requestid.get_extension($file_sd1);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
}


}
 
     if($fromform->supporting_docauth)
{
     
  $file_sd1= $form->get_new_filename('supporting_docauth');
  $fullpath = $CFG->tempdir . '/uploads/request_transcript/authorizationletter_transcript_'.$requestid.get_extension($file_sd1);
  $override = true;
  $success = $form->save_file('supporting_docauth', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'authorizationletter_transcript_'.$requestid.get_extension($file_sd1);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
}


}
     
     }
     /*
     ////////////////
    if($fromform->process_speed == 'Urgent'){
     if($fromform->supporting_doc4)
{
     
  $file_sd1= $form->get_new_filename('supporting_doc4');
  $fullpath = $CFG->tempdir . '/uploads/request_transcript/urgentfeeslip_transcript_'.$requestid.get_extension($file_sd1);
  $override = true;
  $success = $form->save_file('supporting_doc4', $fullpath, $override);

if($success){
    $fileinfo = new stdClass();
    $fileinfo->req_id =  $requestid;
    $fileinfo->name = 'urgentfeeslip_transcript_'.$requestid.get_extension($file_sd1);
    $fileinfo->path = $fullpath;
    
    $DB->insert_record('requests_files', $fileinfo, false);
}


}
    }
      * 
      */
      
     

      
    



//REDIRECT LINK
$redirect_url = new moodle_url('/blocks/student_resource_center/requests/requests_dashboard.php'); 
 redirect($redirect_url); 
    
   // print_object($fromform);
}    
else { 
   
echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
}
