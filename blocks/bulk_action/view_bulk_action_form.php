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

/**
 * @package   block-custom_reports
 * @copyright 2012 Hina Yousuf
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . '/formslib.php');

class mod_bulk_action_form extends moodleform {

    function definition() {

        global $CFG,$DB;
        $mform = & $this->_form;

        $courses = $this->_customdata['courses'];
        $categoryid = $this->_customdata['categoryid'];
        $report = $this->_customdata['report'];


        $mform->addElement('hidden', 'id', $categoryid);

        if ($report == "Bulk Settings") {
            $mform->addElement('header', 'general', get_string('feedback_type', 'block_bulk_action'));

            $attributes = array("class" => "newclass");
            $feedbacktype["First Student Feedback"] = "First Student Feedback";
            $feedbacktype["Second Student Feedback"] = "Second Student Feedback";
            $feedbacktype["Student Course Evaluation"] = "Student Course Evaluation";
            $feedbacktype["Faculty Course Overview Report"] = "Faculty Course Overview Report";
            $mform->addElement('select', 'feedbacktype', "Select Type", $feedbacktype, $attributes);
            $mform->addElement('header', 'timinghdr', get_string('timing', 'form'));
        }
        if ($report == "Bulk Settings" or $report == "Bulk Enrollment Settings") {
            $enableopengroup = array();
            $enableopengroup[] = & $mform->createElement('checkbox', 'openenable', get_string('startdate', 'block_bulk_action'));
            $enableopengroup[] = & $mform->createElement('date_time_selector', 'timeopen', '');
            $mform->addGroup($enableopengroup, 'enableopengroup', get_string('startdate', 'block_bulk_action'), ' ', false);

            $mform->disabledIf('enableopengroup', 'openenable', 'notchecked');

            $enableclosegroup = array();
            $enableclosegroup[] = & $mform->createElement('checkbox', 'closeenable', get_string('enddate', 'block_bulk_action'));
            $enableclosegroup[] = & $mform->createElement('date_time_selector', 'timeclose', '');
            $mform->addGroup($enableclosegroup, 'enableclosegroup', get_string('enddate', 'block_bulk_action'), ' ', false);

            $mform->disabledIf('enableclosegroup', 'closeenable', 'notchecked');
	    $cohorts = array(0 => get_string('no'));
            foreach ($courses as $course) {
            	$context = context_course::instance($course->id, MUST_EXIST);
                list($sqlparents, $params) = $DB->get_in_or_equal($context->get_parent_context_ids(), SQL_PARAMS_NAMED);
                foreach($params as $param){
                    $contextid.=$param.",";
                }
	    }
             $contextid= rtrim($contextid,",");
            
            //list($sqlparents, $params) = $DB->get_in_or_equal($context->get_parent_context_ids(), SQL_PARAMS_NAMED);
            $params['current'] = $instance->customint5;
            $sql = "SELECT id, name, idnumber, contextid
                  FROM {cohort}
                 WHERE contextid IN( $contextid) 
              ORDER BY name ASC, idnumber ASC";
            $rs = $DB->get_recordset_sql($sql, array());
            foreach ($rs as $c) {
                $ccontext = context::instance_by_id($c->contextid);
                if ($c->id != $instance->customint5 and !has_capability('moodle/cohort:view', $ccontext)) {
                    continue;
                }
                $cohorts[$c->id] = format_string($c->name, true, array('context' => $context));
                if ($c->idnumber) {
                    $cohorts[$c->id] .= ' [' . s($c->idnumber) . ']';
                }
            }
            if (!isset($cohorts[$instance->customint5])) {
                // Somebody deleted a cohort, better keep the wrong value so that random ppl can not enrol.
                $cohorts[$instance->customint5] = get_string('unknowncohort', 'cohort', $instance->customint5);
            }
            $rs->close();
            if (count($cohorts) > 1) {
                $mform->addElement('select', 'customint5', get_string('cohortonly', 'enrol_self'), $cohorts);
                $mform->addHelpButton('customint5', 'cohortonly', 'enrol_self');
            } else {
                $mform->addElement('hidden', 'customint5');
                $mform->setType('customint5', PARAM_INT);
                $mform->setConstant('customint5', 0);
            }        
}
        
	
	 if ($report == "Bulk Enrollment Settings") {
            

            $mform->addElement('header', 'general', 'Change Enrollment Method');
            $mform->addElement('advcheckbox', 'manual', '', 'Manual', array('group' => 1), array('false', 'true'));
            $mform->addElement('advcheckbox', 'self', '', 'Self', array('group' => 1), array('false', 'true'));
            $mform->addElement('advcheckbox', 'none', '', 'None', array('group' => 1), array('false', 'true'));          
        }

        $mform->addElement('header', 'general', get_string('select_courses', 'block_bulk_action'));
        foreach ($courses as $course) {
            $mform->addElement('advcheckbox', 'c' . $course->id, '', $course->fullname, array('group' => 1), array('false', 'true'));
	    if ($report == "Bulk Enrollment Settings") {
                $mform->addElement('text', 'users' . $course->id, "Maximum Enrolled Users", array("size" => "5"));
            }
        }




        $group = array();


        $group[] = &$mform->createElement('SUBMIT', 'SUBMITBUTTON', get_string('apply_changes', 'block_bulk_action'));
        $group[] = &$mform->createElement('RESET', 'RESETBUTTON', get_string('reset'));

        $mform->addGroup($group, 'group2');

        $this->add_checkbox_controller(1, "select all/none");
    }

}

?>
