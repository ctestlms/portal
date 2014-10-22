<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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
        
        // for ($i=0; $i<=23; $i++) {
            // $hours[$i] = sprintf("%02d",$i);
        // }
        // for ($i=0; $i<60; $i+=5) {
            // $minutes[$i] = sprintf("%02d",$i);
        // }
        // $durselect[] =& $mform->createElement('select', 'hours', '', $hours);
		// $durselect[] =& $mform->createElement('select', 'minutes', '', $minutes, false, true);
		// $mform->addGroup($durselect, 'durtime', get_string('newduration','attforblock'), array(' '), true);
		
		for ($i=0; $i<=23; $i++) {
            $hours[$i] = sprintf("%02d",$i);
        }
        for ($i=0; $i<60; $i+=5) {
            $minutes[$i] = sprintf("%02d",$i);
        }
		$hoursattributes=array('size'=>'1', 'readonly'=>'readonly', 'value'=>$hours);
		$minutesattributes=array('size'=>'1', 'readonly'=>'readonly', 'value'=>$minutes);
        //$durtime = array();
        $durselect[] =& $mform->createElement('text', 'hours', get_string('hour', 'form'), $hoursattributes);
		$durselect[] =& $mform->createElement('text', 'minutes', get_string('minute', 'form'), $minutesattributes);
        // $durselect[] =& $mform->createElement('select', 'hours', '', $hours);
		// $durselect[] =& $mform->createElement('select', 'minutes', '', $minutes, false, true);
		$mform->addGroup($durselect, 'durtime', get_string('newduration','attforblock'), array(' '), true);
		
        /* $mform->addElement('editor', 'sdescription', get_string('description', 'attforblock'), null, $defopts);
        $mform->setType('sdescription', PARAM_RAW); */
		
		//This will add class types i.e. normal class, lab, studio etc and weightage to ease the calculations
         $sdescription_action = 'javascript: 
			switch(this.value){ 
				case "50-Mins/One Hour Lecture":
					document.getElementById("id_durtime_hours").value = "01";
					document.getElementById("id_durtime_minutes").value = "00";
					break;
        		case "90-Mins Lecture":
        			document.getElementById("id_durtime_hours").value = "01";
        			document.getElementById("id_durtime_minutes").value = "30";
        			break;
        		case "Two Hours Lecture":
        			document.getElementById("id_durtime_hours").value = "02";
        			document.getElementById("id_durtime_minutes").value = "00";
        			break;
        		case "Three Hours Lecture":
        			document.getElementById("id_durtime_hours").value = "03";
        			document.getElementById("id_durtime_minutes").value = "00";
					break;
				case "Two Hours Lab":
        			document.getElementById("id_durtime_hours").value = "02";
        			document.getElementById("id_durtime_minutes").value = "00";
        			break;
        		case "Three Hours Lab":
        			document.getElementById("id_durtime_hours").value = "03";
        			document.getElementById("id_durtime_minutes").value = "00";
        			break;
				case "Two Hours Studio":
        			document.getElementById("id_durtime_hours").value = "02";
        			document.getElementById("id_durtime_minutes").value = "00";
        			break;
				case "Three Hours Studio":
        			document.getElementById("id_durtime_hours").value = "03";
        			document.getElementById("id_durtime_minutes").value = "00";
        			break;
				case "Two Hours Ward":
        			document.getElementById("id_durtime_hours").value = "02";
        			document.getElementById("id_durtime_minutes").value = "00";
        			break;
				case "Four Hours Ward":
        			document.getElementById("id_durtime_hours").value = "04";
        			document.getElementById("id_durtime_minutes").value = "00";
        			break;
				default:
        			document.getElementById("id_durtime_hours").value = "01";
        			document.getElementById("id_durtime_minutes").value = "00";
        		}';
								
		$sdescription_attributes = array('onChange' => $sdescription_action);
		
		$mform->addElement('select', 'sdescription', get_string('description', 'attforblock'),
        	array('50-Mins/One Hour Lecture' => get_string('fiftyminclass','attforblock'),
                  '90-Mins Lecture' => get_string('ninetyminsclass','attforblock'),
				  'Two Hours Lecture' => get_string('twohoursclass','attforblock'),
				  'Three Hours Lecture' => get_string('threehoursclass','attforblock'),
				  'One Hour Lab' => get_string('onehourlab','attforblock'),
				  'Two Hours Lab' => get_string('twohourslab','attforblock'),
				  'Three Hours Lab' => get_string('threehourslab','attforblock'),
				  'Two Hours Studio' => get_string('twohoursstudio','attforblock'),
				  'Three Hours Studio' => get_string('threehoursstudio','attforblock'),
				  'Two Hours Ward' => get_string('twohoursward','attforblock'),
				  'Four Hours Ward' => get_string('fourhoursward','attforblock'),
                  ), $sdescription_attributes);
		$mform->addHelpButton('sdescription', 'description', 'attforblock');
		
        $mform->addElement('hidden', 'ids', $ids);
       	$mform->addElement('hidden', 'id', $cm->id);
        $mform->addElement('hidden', 'action', att_sessions_page_params::ACTION_CHANGE_DURATION);
        
        $mform->setDefaults(array('durtime' => array('hours'=>'01', 'minutes'=>'00')));
		
//-------------------------------------------------------------------------------
        // buttons
        $submit_string = get_string('update', 'attforblock');
        $this->add_action_buttons(true, $submit_string);

//        $mform->addElement('hidden', 'id', $cm->id);
//        $mform->addElement('hidden', 'sessionid', $sessionid);
//        $mform->addElement('hidden', 'action', 'changeduration');

    }

}
