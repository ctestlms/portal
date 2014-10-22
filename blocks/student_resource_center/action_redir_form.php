<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");

require_once("../../config.php");
 
class action_form extends moodleform {
 
    
    function definition() {
    global $DB;

        $mform =& $this->_form;
        
       
        
        $mform->addElement('header','displayinfo', "Confirmation");
        
        
         $mform->addElement('static', 'msgalert', '',
    get_string('alertmsg', 'block_student_resource_center', $this->_customdata['formaction']));
$formaction = $this->_customdata['formaction'];
$mform->addElement('hidden', 'formaction', $formaction);
$posts = $this->_customdata['posts'];
$mform->addElement('hidden', 'posts', $posts);

//$mform->addElement('hidden', 'request', '50');
   $buttonarray=array();
$buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
$buttonarray[] = &$mform->createElement('cancel');
$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);


    }
    

}
    
   