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
 * prints the forms to choose an item-typ to create items and to choose a template to use
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package facultyfeedback
 */

//It must be included from a Moodle page
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once($CFG->libdir.'/formslib.php');

class facultyfeedback_edit_add_question_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        //headline
        $mform->addElement('header', 'general', get_string('add_items', 'facultyfeedback'));
        // visible elements
        $facultyfeedback_names_options = facultyfeedback_load_facultyfeedback_items_options();

        $attributes = 'onChange="M.core_formchangechecker.set_form_submitted(); this.form.submit()"';
        $mform->addElement('select', 'typ', '', $facultyfeedback_names_options, $attributes);

        // hidden elements
        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);
        $mform->addElement('hidden', 'position');
        $mform->setType('position', PARAM_INT);

        // buttons
        $mform->addElement('submit', 'add_item', get_string('add_item', 'facultyfeedback'));
    }
}

class facultyfeedback_edit_use_template_form extends moodleform {
    private $facultyfeedbackdata;

    public function definition() {
        $this->facultyfeedbackdata = new stdClass();
        //this function can not be called, because not all data are available at this time
        //I use set_form_elements instead
    }

    //this function set the data used in set_form_elements()
    //in this form the only value have to set is course
    //eg: array('course' => $course)
    public function set_facultyfeedbackdata($data) {
        if (is_array($data)) {
            if (!isset($this->facultyfeedbackdata)) {
                $this->facultyfeedbackdata = new stdClass();
            }
            foreach ($data as $key => $val) {
                $this->facultyfeedbackdata->{$key} = $val;
            }
        }
    }

    //here the elements will be set
    //this function have to be called manually
    //the advantage is that the data are already set
    public function set_form_elements() {
        $mform =& $this->_form;

        $elementgroup = array();
        //headline
        $mform->addElement('header', '', get_string('using_templates', 'facultyfeedback'));
        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // visible elements
        $templates_options = array();
        $owntemplates = facultyfeedback_get_template_list($this->facultyfeedbackdata->course, 'own');
        $publictemplates = facultyfeedback_get_template_list($this->facultyfeedbackdata->course, 'public');

        $options = array();
        if ($owntemplates or $publictemplates) {
            $options[''] = array('' => get_string('choose'));

            if ($owntemplates) {
                $courseoptions = array();
                foreach ($owntemplates as $template) {
                    $courseoptions[$template->id] = $template->name;
                }
                $options[get_string('course')] = $courseoptions;
            }

            if ($publictemplates) {
                $publicoptions = array();
                foreach ($publictemplates as $template) {
                    $publicoptions[$template->id] = $template->name;
                }
                $options[get_string('public', 'facultyfeedback')] = $publicoptions;
            }

            $attributes = 'onChange="M.core_formchangechecker.set_form_submitted(); this.form.submit()"';
            $elementgroup[] = $mform->createElement('selectgroups',
                                                     'templateid',
                                                     '',
                                                     $options,
                                                     $attributes);

            $elementgroup[] = $mform->createElement('submit',
                                                     'use_template',
                                                     get_string('use_this_template', 'facultyfeedback'));

            $mform->addGroup($elementgroup, 'elementgroup', '', array(' '), false);
        } else {
            $mform->addElement('static', 'info', get_string('no_templates_available_yet', 'facultyfeedback'));
        }
    }
}

class facultyfeedback_edit_create_template_form extends moodleform {
    private $facultyfeedbackdata;

    public function definition() {
    }

    public function data_preprocessing(&$default_values) {
        $default_values['templatename'] = '';
    }

    public function set_facultyfeedbackdata($data) {
        if (is_array($data)) {
            if (!isset($this->facultyfeedbackdata)) {
                $this->facultyfeedbackdata = new stdClass();
            }
            foreach ($data as $key => $val) {
                $this->facultyfeedbackdata->{$key} = $val;
            }
        }
    }

    public function set_form_elements() {
        $mform =& $this->_form;

        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'do_show');
        $mform->setType('do_show', PARAM_INT);
        $mform->addElement('hidden', 'savetemplate', 1);
        $mform->setType('savetemplate', PARAM_INT);

        //headline
        $mform->addElement('header', '', get_string('creating_templates', 'facultyfeedback'));

        // visible elements
        $elementgroup = array();

        $elementgroup[] = $mform->createElement('static',
                                                 'templatenamelabel',
                                                 get_string('name', 'facultyfeedback'));

        $elementgroup[] = $mform->createElement('text',
                                                 'templatename',
                                                 get_string('name', 'facultyfeedback'),
                                                 array('size'=>'40', 'maxlength'=>'200'));

        if (has_capability('mod/facultyfeedback:createpublictemplate', get_system_context())) {
            $elementgroup[] = $mform->createElement('checkbox',
                                                     'ispublic',
                                                     get_string('public', 'facultyfeedback'),
                                                     get_string('public', 'facultyfeedback'));
        }

        // buttons
        $elementgroup[] = $mform->createElement('submit',
                                                 'create_template',
                                                 get_string('save_as_new_template', 'facultyfeedback'));

        $mform->addGroup($elementgroup,
                         'elementgroup',
                         get_string('name', 'facultyfeedback'),
                         array(' '),
                         false);

        $mform->setType('templatename', PARAM_TEXT);

    }
}

