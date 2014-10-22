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

class mod_attforblock_add_form extends moodleform {

    function definition() {

        global $CFG, $USER;
        $mform    =& $this->_form;

        $course        = $this->_customdata['course'];
        $cm            = $this->_customdata['cm'];
        $modcontext    = $this->_customdata['modcontext'];


        $mform->addElement('header', 'general', get_string('addsession','attforblock'));//fill in the data depending on page params
                                                    //later using set_data

        $groupmode = groups_get_activity_groupmode($cm);
        switch ($groupmode) {
            case NOGROUPS:
                $mform->addElement('static', 'sessiontypedescription', get_string('sessiontype', 'attforblock'),
                                  get_string('commonsession', 'attforblock'));
                $mform->addHelpButton('sessiontypedescription', 'sessiontype', 'attforblock');
                $mform->addElement('hidden', 'sessiontype', attforblock::SESSION_COMMON);
                break;
            case SEPARATEGROUPS:
                $mform->addElement('static', 'sessiontypedescription', get_string('sessiontype', 'attforblock'),
                                  get_string('groupsession', 'attforblock'));
                $mform->addHelpButton('sessiontypedescription', 'sessiontype', 'attforblock');
                $mform->addElement('hidden', 'sessiontype', attforblock::SESSION_GROUP);
                break;
            case VISIBLEGROUPS:
                $radio=array();
                $radio[] = &$mform->createElement('radio', 'sessiontype', '', get_string('commonsession','attforblock'), attforblock::SESSION_COMMON);
                $radio[] = &$mform->createElement('radio', 'sessiontype', '', get_string('groupsession','attforblock'), attforblock::SESSION_GROUP);
                $mform->addGroup($radio, 'sessiontype', get_string('sessiontype','attforblock'), ' ', false);
                $mform->addHelpButton('sessiontype', 'sessiontype', 'attforblock');
                $mform->setDefault('sessiontype', attforblock::SESSION_COMMON);
                break;
        }
        if ($groupmode == SEPARATEGROUPS or $groupmode == VISIBLEGROUPS) {
            if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $modcontext))
                $groups = groups_get_all_groups ($course->id, $USER->id);
            else
                $groups = groups_get_all_groups($course->id);
            if ($groups) {
                $selectgroups = array();
                foreach ($groups as $group) {
                    $selectgroups[$group->id] = $group->name;
                }
                $select = &$mform->addElement('select', 'groups', get_string('groups', 'group'), $selectgroups);
                $select->setMultiple(true);
                $mform->disabledIf('groups','sessiontype','neq', attforblock::SESSION_GROUP);
            }
            else {
                $mform->updateElementAttr($radio, array('disabled'=>'disabled'));
                $mform->addElement('static', 'groups', get_string('groups', 'group'),
                                  get_string('nogroups', 'attforblock'));
                if ($groupmode == SEPARATEGROUPS)
                    return;
            }
        }
        
        $mform->addElement('checkbox', 'addmultiply', '', get_string('createmultiplesessions','attforblock'));
		$mform->addHelpButton('addmultiply', 'createmultiplesessions', 'attforblock');
		
		// $mform->addElement('date_selector', 'sessiondate', get_string('sessiondate','attforblock'));
        $mform->addElement('date_time_selector', 'sessiondate', get_string('sessiondate','attforblock'));

        for ($i=0; $i<=23; $i++) {
            $hours[$i] = sprintf("%02d",$i);
        }
        for ($i=0; $i<60; $i+=5) {
            $minutes[$i] = sprintf("%02d",$i);
        }
		$hoursattributes=array('size'=>'1', 'readonly'=>'readonly', 'value'=>$hours);
		$minutesattributes=array('size'=>'1', 'readonly'=>'readonly', 'value'=>$minutes);
        $durtime = array();
        $durtime[] =& $mform->createElement('text', 'hours', get_string('hour', 'form'), $hoursattributes);
		$durtime[] =& $mform->createElement('text', 'minutes', get_string('minute', 'form'), $minutesattributes);
        $mform->addGroup($durtime, 'durtime', get_string('duration','attforblock'), array(' '), true);
		$mform->setDefault('durtime[hours]', '01');
		$mform->setDefault('durtime[minutes]', '00');
		
        
        $mform->addElement('date_selector', 'sessionenddate', get_string('sessionenddate','attforblock'));
		$mform->disabledIf('sessionenddate', 'addmultiply', 'notchecked');
        
        $sdays = array();
		if ($CFG->calendar_startwday === '0') { //week start from sunday
        	$sdays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday','calendar'));
		}
        $sdays[] =& $mform->createElement('checkbox', 'Mon', '', get_string('monday','calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Tue', '', get_string('tuesday','calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Wed', '', get_string('wednesday','calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Thu', '', get_string('thursday','calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Fri', '', get_string('friday','calendar'));
        $sdays[] =& $mform->createElement('checkbox', 'Sat', '', get_string('saturday','calendar'));
		if ($CFG->calendar_startwday !== '0') { //week start from monday
        	$sdays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday','calendar'));
		}
        $mform->addGroup($sdays, 'sdays', get_string('sessiondays','attforblock'), array(' '), true);
		$mform->disabledIf('sdays', 'addmultiply', 'notchecked');
        
        $period = array(1=>1,2,3,4,5,6,7,8);
        $periodgroup = array();
        $periodgroup[] =& $mform->createElement('select', 'period', '', $period, false, true);
        $periodgroup[] =& $mform->createElement('static', 'perioddesc', '', get_string('week','attforblock'));
        $mform->addGroup($periodgroup, 'periodgroup', get_string('period','attforblock'), array(' '), false);
		$mform->disabledIf('periodgroup', 'addmultiply', 'notchecked');
        
		$sessionsperday[] =& $mform->createElement('select', 'sessionsperday', '', array(1=>1, 2, 3), false, true);
		$sessionsperday[] =& $mform->createElement('static', 'sessionsperdaydesc', '', "Consective Sessions");
		$mform->addGroup($sessionsperday, 'sessionsperday', get_string('sessionsperday', 'attforblock'), array(' '), false);
		
       /*  $mform->addElement('editor', 'sdescription', get_string('description', 'attforblock'), null, array('maxfiles'=>EDITOR_UNLIMITED_FILES, 'noclean'=>true, 'context'=>$modcontext));
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
		
//-------------------------------------------------------------------------------
        // buttons
        $submit_string = get_string('addsession', 'attforblock');
        $this->add_action_buttons(false, $submit_string);
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['sessiontype'] == attforblock::SESSION_GROUP and empty($data['groups'])) {
            $errors['groups'] = get_string('errorgroupsnotselected','attforblock');
        }
        return $errors;
    }

}
