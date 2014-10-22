<?php
global $CFG;
require_once('../../../../config.php');
require_once("{$CFG->libdir}/formslib.php");
require_once("{$CFG->libdir}/filelib.php");

class cert_form extends moodleform {
    
    function definition() {
              global $CFG,$USER;
              //protected $toomany = false;
        $mform =& $this->_form;
        global $DB;
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
         $mform->addElement('header','heading', get_string('personalinfo_head','block_student_resource_center'));
        $mform->addElement('static', 'personal_data', null,'<font color="red">To update your personal Information, please email Exam Branch at : <i><b>exam@seecs.edu.pk</b></i></i></font>');
        $mform->addElement('static', 'name' ,'<b>Name:</>',"<b>$name</b>");
        $mform->addElement('static', 'regno', '<b>Regn No:</>',"<b>$USER->idnumber</b>");
        $mform->addElement('static', 'class' ,'<b>Programme:</>',"<b>$row_info->shortname</b>");
        $mform->addElement('static', 'regno', '<b>Email:</b>',"<b>$USER->email</b>");
        $mform->addElement('static', 'regno', '<b>Phone:</b>',"<b>$USER->phone2</b>");
        
    $mform->addElement('header','heading', get_string('nustid','block_student_resource_center'));
        $options1 = array(
        'Faded' => 'Faded',
        'Lost' => 'Lost',
        'Damaged' => 'Damaged');
 
           $mform->addElement('select', 'id_reason' ,get_string('id_reason','block_student_resource_center'),$options1);
           
           //Prescribed fee  i,e Rs. 500/- has been deposited in NUST tuition fee account No. 22927000000301 and receipt is attached
          // $mform->addElement('static', 'note', '<b>Deposit of Fee:</b>','<font color="red"> You are required to pay Rs. 500.00 for the requests. Fee may be deposited 
          //     in the NUST tuition fee account No. 22927000000301 through deposit slip. Scanned copy of the reciept <b>MUST</b> be attached with this form.</font>');
           /*
          $mform->addElement('advcheckbox', 'updatecheckbox','' ,get_string("updatecheck", 'block_student_resource_center'),null, array(0, 1));   
          $mform->addElement('text', 'phone2', get_string("contactno", 'block_student_resource_center'));
         // $mform->setType('phone2', PARAM_INT);
          global $DB;
          $user=$DB->get_record_sql("SELECT * FROM {user} WHERE id=$USER->id");
          $mform->setDefault('phone2', $user->phone2);
          $mform->disabledIf('phone2', 'updatecheckbox',0);
            * 
            */
             $mform->addElement('static', 'feeslip','<b>Note:</b>','<font color="red">It is mandatory to attach fee slip in case of <b>"LOST"</b> ID Card</font>');
          $mform->addElement('filepicker', 'receipt', get_string('receipt_file', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
  
          

         $mform->disabledIf('receipt', 'id_reason','eq','Faded');
         $mform->disabledIf('receipt', 'id_reason','eq','Damaged');
         //$mform->addRule('receipt', null, 'required', null, 'client');
         $mform->addElement('filepicker', 'supporting_doc', get_string('supp_docs', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
         
          $buttonarray=array();
      
         $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'),array('onClick'=>"return confirm('Are you sure you want to submit your request to the SEECS Exam Branch?');"));
         $buttonarray[] = &$mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
          $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
         
    
    
    }
    
}
    



