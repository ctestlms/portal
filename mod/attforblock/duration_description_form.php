<?php  // $Id: duration_form.php,v 1.3.2.2 2009/02/23 19:22:42 dlnsk Exp $

require_once($CFG->libdir.'/formslib.php');

class mod_attforblock_duration_form extends moodleform {

    function definition() {

        global $CFG;
        $mform    =& $this->_form;

        $course        = $this->_customdata['course'];
        $cm            = $this->_customdata['cm'];
        $modcontext    = $this->_customdata['modcontext'];
        $ids		   = $this->_customdata['ids'];

        $mform->addElement('header', 'general', get_string('changeduration','attforblock'));
		$mform->addElement('static', 'count', get_string('countofselected','attforblock'), count(explode('_', $ids)));
        
        for ($i=0; $i<=23; $i++) {
            $hours[$i] = sprintf("%02d",$i);
        }
        for ($i=0; $i<60; $i+=5) {
            $minutes[$i] = sprintf("%02d",$i);
        }
        $durselect[] =& MoodleQuickForm::createElement('select', 'hours', '', $hours);
		$durselect[] =& MoodleQuickForm::createElement('select', 'minutes', '', $minutes, false, true);
		$mform->addGroup($durselect, 'durtime', get_string('newduration','attforblock'), array(' '), true);
		
        $mform->addElement('hidden', 'ids', $ids);
       	$mform->addElement('hidden', 'id', $cm->id);
        $mform->addElement('hidden', 'action', 'changeduration');
        
        $mform->setDefaults(array('durtime' => array('hours'=>0, 'minutes'=>0)));
		
//-------------------------------------------------------------------------------
        // buttons
        $submit_string = get_string('update', 'attforblock');
        $this->add_action_buttons(true, $submit_string);

//        $mform->addElement('hidden', 'id', $cm->id);
//        $mform->addElement('hidden', 'sessionid', $sessionid);
//        $mform->addElement('hidden', 'action', 'changeduration');

    }

}


class mod_attforblock_description_form extends moodleform {

    function definition() {

        global $CFG;
        $mform    =& $this->_form;

        $course        = $this->_customdata['course'];
        $cm            = $this->_customdata['cm'];
        $modcontext    = $this->_customdata['modcontext'];
        $ids		   = $this->_customdata['ids'];

        $mform->addElement('header', 'general', get_string('changedescription','attforblock'));
		$mform->addElement('static', 'count', get_string('countofselected','attforblock'), count(explode('_', $ids)));

        $mform->addElement('select', 'sdescription', get_string('classdescriptiontype', 'attforblock'),
        	array('1' => get_string('normalclass','attforblock'),
                      '4' => get_string('ninety_minsclass','attforblock'),
                      '2' => get_string('twohourslab','attforblock'),
                      '3' => get_string('threehourslab','attforblock'),
			'5' => get_string('twohoursclass','attforblock'),
                      '6' => get_string('threehoursclass','attforblock')
                ));


        $mform->addElement('hidden', 'ids', $ids);
       	$mform->addElement('hidden', 'id', $cm->id);
        $mform->addElement('hidden', 'action', 'changedescription');

        //$mform->setDefaults(array('durtime' => array('hours'=>0, 'minutes'=>0)));

//-------------------------------------------------------------------------------
        // buttons
        $submit_string = get_string('update', 'attforblock');
        $this->add_action_buttons(true, $submit_string);

//        $mform->addElement('hidden', 'id', $cm->id);
//        $mform->addElement('hidden', 'sessionid', $sessionid);
//        $mform->addElement('hidden', 'action', 'changeduration');

    }

}
?>
