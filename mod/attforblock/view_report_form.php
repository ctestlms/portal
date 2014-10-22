<?php  // $Id: add_form.php,v 1.1.2.2 2009/02/23 19:22:42 dlnsk Exp $

require_once($CFG->libdir.'/formslib.php');

class mod_attforblock_view_report_form extends moodleform {

    function definition() {

        global $CFG;
        $mform    =& $this->_form;

        $courses    = $this->_customdata['courses'];
		$categoryid = $this->_customdata['categoryid'];	

        $mform->addElement('header', 'general', get_string('select_courses','block_custom_reports'));
		$mform->addElement('hidden', 'id', $categoryid);
		
        foreach($courses as $course){
			$mform->addElement('advcheckbox', 'c'.$course->id, '', $course->fullname, array('group'=>1), array('false', 'true'));
		}	
		
		$group = array();
        //$SUBMIT_STRING = GET_STRING('VIEW_REPORT', 'BLOCK_CUSTOM_REPORTS');
        
        $group[] = & $mform->createElement('SUBMIT', 'SUBMITBUTTON', get_string('view_report', 'block_custom_reports'));
        $group[] = & $mform->createElement('RESET', 'RESETBUTTON', get_string('reset'));
       
        $mform->addGroup($group, 'group2');
        $this->add_checkbox_controller(1, get_string('Select all/none'));

    }

}
?>
