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
        if(substr_count($USER->user_subgroup,"-") == 2){
 
        $lms_subgroup = substr($USER->user_subgroup, 0, -2);
  }
 else{
   $lms_subgroup = $USER->user_subgroup;}
         $name = $USER->firstname.' '.$USER->lastname;
        $sql_info = "SELECT c.enddate,d.shortname FROM {classes} c
             JOIN {degrees} d ON d.id=c.degreeid
             WHERE c.alias = \"$lms_subgroup\"";
        $row_info = $DB->get_record_sql($sql_info);
         $mform->addElement('header','heading', get_string('personalinfo_head','block_student_resource_center'));
        $mform->addElement('static', 'personal_data', null,'<font color="red">To update your personal Information, please email Exam Branch at : <i><b>exam@seecs.edu.pk</b></i></font>');
        $mform->addElement('static', 'name' ,'<b>Name:</>',"<b>$name</b>");
        $mform->addElement('static', 'regno', '<b>Regn No:</>',"<b>$USER->idnumber</b>");
        $mform->addElement('static', 'class' ,'<b>Programme:</>',"<b>$row_info->shortname</b>");
        $mform->addElement('static', 'school', '<b>School/ College:</>',"<b>SEECS</b>");
        $mform->addElement('static', 'regno', '<b>Email:</b>',"<b>$USER->email</b>");
        $mform->addElement('static', 'regno', '<b>Phone:</b>',"<b>$USER->phone2</b>");
        
        
    $mform->addElement('header','heading', get_string('head','block_student_resource_center'));
        $options1 = array(
        'Provisional Certificate' => 'Provisional Certificate',
        'English Language Proficiency Certificate' => 'English Language Proficiency Certificate',
        'To Whom It May Concern' => 'To Whom It May Concern',
            'Character Certificate' => 'Character Certificate'
             );
 
           $mform->addElement('select', 'cert_type' ,get_string('cert_type','block_student_resource_center'),$options1);
           
           $mform->addElement('textarea', 'cert_desc', get_string("cert_desc", 'block_student_resource_center'), 'wrap="virtual" rows="5" cols="40"');
        $mform->addRule('cert_desc', 'maximum length should be 285 characters', 'maxlength', 285, 'client');
       
        //   $mform->addElement('static', 'note', '<b>Deposit of Fee:</b>','<font color="red"> You are required to pay Rs. 100.00 for the requests. Fee may be deposited 
        //       in the SEECS Acct Office through deposit slip. Scanned copy of the reciept <b>MUST</b> be attached with this form.</font>');
           
           $mform->addElement('text', 'amount_deposited', get_string("amount", 'block_student_resource_center'));
       $mform->addRule('amount_deposited', null, 'required', null, 'client');
           $mform->addElement('text', 'receipt_no', get_string("receipt", 'block_student_resource_center'));
           $mform->addRule('receipt_no', null, 'required', null, 'client');
           $date_arr = array(
         'startyear' => 2013, 
         'stopyear'  => 2040,
         'timezone'  => 99,
         'optional'  => false
                 );
          $mform->addElement('date_selector', 'deposit_date', get_string("date_deposited", 'block_student_resource_center'), $date_arr);
          /*
          $mform->addElement('advcheckbox', 'updatecheckbox','' ,get_string("updatecheck", 'block_student_resource_center'),null, array(0, 1));   
          $mform->addElement('text', 'phone2', get_string("contactno", 'block_student_resource_center'),'maxlength="16" size="25"');
           
          global $DB;
          $user=$DB->get_record_sql("SELECT * FROM {user} WHERE id=$USER->id");
          $mform->setDefault('phone2', $user->phone2);
         // $mform->setType('phone2', PARAM_RAW);
          $mform->disabledIf('phone2', 'updatecheckbox',0);
           * 
           */
          $mform->addElement('filepicker', 'receipt', get_string('receipt_file', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
      $mform->addRule('receipt', null, 'required', null, 'client');
          $mform->addElement('filepicker', 'supporting_doc', get_string('supp_docs', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
          $mform->addElement('filepicker', 'supporting_doc1', get_string('supp_docs1', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
   
       
$buttonarray=array();
      
         $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'),array('onClick'=>"return confirm('Are you sure you want to submit your request to the SEECS Exam Branch?');"));
         $buttonarray[] = &$mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
          $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
         
    
    
    }
    
}
    



