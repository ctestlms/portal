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
 * prints the form to confirm use template
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package facultyfeedback
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class mod_facultyfeedback_use_templ_form extends moodleform {
    public function definition() {
        $mform =& $this->_form;

        //headline
        $mform->addElement('header', 'general', '');

        // visible elements
        $mform->addElement('radio', 'deleteolditems', '1)', get_string('delete_old_items', 'facultyfeedback'), 1);
        $mform->addElement('radio', 'deleteolditems', '2)', get_string('append_new_items', 'facultyfeedback'), 0);
        $mform->setType('deleteolditems', PARAM_INT);

        // hidden elements
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'templateid');
        $mform->setType('templateid', PARAM_INT);
        $mform->addElement('hidden', 'do_show');
        $mform->setType('do_show', PARAM_INT);
        $mform->addElement('hidden', 'confirmadd');
        $mform->setType('confirmadd', PARAM_INT);

        //-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();

    }
}

