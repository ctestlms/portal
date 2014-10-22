<?php
require_once('../../../../config.php');
global $CFG;
require_once("{$CFG->libdir}/formslib.php");
require_once("{$CFG->libdir}/filelib.php");

class cert_form extends moodleform {
    
    function definition() {
              global $CFG,$USER,$DB;
              //protected $toomany = false;
        $mform =& $this->_form;
        /////////////////////////////////PERSONAL INFO ////////////////////////////////////////////////////////////////////
        $mform->addElement('header','heading', get_string('personalinfo_head','block_student_resource_center'));
        //Name  // University Regn no // Programme // school 
        $name = $USER->firstname.' '.$USER->lastname;
        if(substr_count($USER->user_subgroup,"-") == 2){
 
        $lms_subgroup = substr($USER->user_subgroup, 0, -2);
  }
 else{
   $lms_subgroup = $USER->user_subgroup;}
        $sql_info = "SELECT c.enddate,d.shortname FROM {classes} c
             JOIN {degrees} d ON d.id=c.degreeid
             WHERE c.alias = \"$lms_subgroup\"";
        $row_info = $DB->get_record_sql($sql_info);
         
        $mform->addElement('static', 'personal_data', null,'<font color="red">To update your personal Information, please email Exam Branch at : <i><b>exam@seecs.edu.pk</b></i></i></font>');
        $mform->addElement('static', 'name' ,'<b>Name:</>',"<b>$name</b>");
        $mform->addElement('static', 'regno', '<b>Regn No:</>',"<b>$USER->idnumber</b>");
        $mform->addElement('static', 'class' ,'<b>Programme:</>',"<b>$row_info->shortname</b>");
        $mform->addElement('static', 'school', '<b>School/ College:</>',"<b>SEECS</b>");
        
  
          //////////////////FEE INFO/////////////////////////////////
           $mform->addElement('header','heading', get_string('degree_head','block_student_resource_center'));
         //    $amount_deposited[] =  $mform->createElement('text', 'amount_deposited', get_string("amount", 'block_student_resource_center'));
       // $amount_deposited[] = $mform->createElement('static', 'amount_note', 'amount_note','<font color="black" size="0.8px"> <i>(Total amount deposited to the HBL Bank)</i></font>');
      //  $mform->addGroup($amount_deposited, 'amount_d',get_string('d_amount','block_student_resource_center'), array(' '), false);
      //  $mform->addRule('amount_d', null, 'required', null, 'client');
        $mform->addElement('static', 'feeslip','<b>Note:</b>','<font color="red"><b>Total amount deposited to the HBL Bank : 1000Rs</b></font>');
          
           $mform->addElement('text', 'receipt_no', get_string("receipt", 'block_student_resource_center'));
           $mform->addRule('receipt_no', null, 'required', null, 'client');
           $date_arr = array(
         'startyear' => 2013, 
         'stopyear'  => 2040,
         'timezone'  => 99,
         'optional'  => false
                 );
          $mform->addElement('date_selector', 'deposit_date', get_string("date_deposited", 'block_student_resource_center'), $date_arr);
             $mform->addElement('filepicker', 'receipt', get_string('receipt_file', 'block_student_resource_center'), null,
         array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
         $mform->addRule('receipt', null, 'required', null, 'client');
          
              ////////////////////////////////////////// ///DEGREEE INFO //////////////////////////////////////////////////////////////
       
        $options1 = array(
        'For Job' => 'For Job',
        'For Higher Studies' => 'For Higher Studies',
        'For Registration with PEC/PMDC etc' => 'For Registration with PEC/PMDC etc');
 
         $reason[] = $mform->createElement('select', 'degree_reason' ,get_string('degree_reason','block_student_resource_center'),$options1);
         $reason[] = $mform->createElement('static', 'reason_note', 'note_reason','<font color="black" size="0.8px"> <i>(Reason for issuance of degree)</i></font>');
         $mform->addGroup($reason, 'degree_reasonarr',get_string('d_reason','block_student_resource_center'), array(' '), false);
        
          $mform->addElement('filepicker', 'supporting_doc', get_string('evidence_doc', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
          $mform->addElement('filepicker', 'supporting_doc1', get_string('clearance_doc', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
   
      $mform->addElement('static', 'note', '<b>Documents Required</b>','<font color="red"> You <b>MUST</b> submit original Fee receipt & duly signed pro-forma to the Exam Branch to process your request.</font>');
           
$buttonarray=array();
      
         $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'),array('onClick'=>"return confirm('Are you sure you want to submit your request to the SEECS Exam Branch?');"));
         $buttonarray[] = &$mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
          $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
         
    
    
    }
    
}
    


          // $mform->addElement('static', 'note', '<b>Deposit of Fee:</b>','<font color="red"> You are required to pay Rs. 100.00 for the requests. Fee may be deposited 
          //     in the SEECS Acct Office through deposit slip. Scanned copy of the reciept <b>MUST</b> be attached with this form.</font>');
           
     
        //  $mform->addElement('advcheckbox', 'updatecheckbox','' ,get_string("updatecheck", 'block_student_resource_center'),null, array(0, 1));   
         // $mform->addElement('text', 'phone2', get_string("contactno", 'block_student_resource_center'));
         // global $DB;
         // $user=$DB->get_record_sql("SELECT * FROM {user} WHERE id=$USER->id");
         // $mform->setDefault('phone2', $user->phone2);
         // $mform->disabledIf('phone2', 'updatecheckbox',0);
