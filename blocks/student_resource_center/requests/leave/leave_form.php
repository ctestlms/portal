<?php
global $CFG;
require_once('../../../../config.php');
require_once("{$CFG->libdir}/formslib.php");
require_once("{$CFG->libdir}/filelib.php");

class cert_form extends moodleform {
    
    function definition() {
              global $CFG,$USER,$DB;
              //protected $toomany = false;
        $mform =& $this->_form;
    $mform->addElement('header','heading', get_string('leave_title','block_student_resource_center'));
            $mform->addElement('header','heading', get_string('personalinfo_head','block_student_resource_center'));
        //Name  // University Regn no // Programme // school 
            if(substr_count($USER->user_subgroup,"-") == 2){
 
        $lms_subgroup = substr($USER->user_subgroup, 0, -2);
  }
 else{
   $lms_subgroup = $USER->user_subgroup;}
        $name = $USER->firstname.' '.$USER->lastname;
        $sql_info = "SELECT c.name FROM {classes} c WHERE c.alias = \"$lms_subgroup\"";
        $row_info = $DB->get_record_sql($sql_info);
         
        $mform->addElement('static', 'personal_data', null,'<font color="red">To update your personal Information, please email Exam Branch at : <i><b>exam@seecs.edu.pk</b></i></i></font>');
        $mform->addElement('static', 'name' ,'<b>Name:</>',"<b>$name</b>");
        $mform->addElement('static', 'regno', '<b>Regn No:</>',"<b>$USER->idnumber</b>");
        $mform->addElement('static', 'class' ,'<b>Class:</>',"<b>$row_info->name</b>");
        $mform->addElement('static', 'school', '<b>School/ College:</>',"<b>SEECS</b>");
        
       
             /////////////////////////////////////////////////////////////////FILES UPLOAD///////////////////////////////////////////
          $mform->addElement('header','heading', get_string('other_head1','block_student_resource_center')); 
          $mform->addElement('static', 'note', '<b>Deposit of Fee:</b>','<font color="red"> Please Submit Clearance Form to Exam Branch to Process the request.</font>');
           
           $mform->addElement('filepicker', 'supporting_doc1', get_string('clearance_doc1', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
         
          $buttonarray=array();
      
         $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'),array('onClick'=>"return confirm('Are you sure you want to submit your request to the SEECS Exam Branch?');"));
         $buttonarray[] = &$mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
          $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
         
    
    
    }
    
}
    



