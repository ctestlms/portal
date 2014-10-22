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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_facultyfeedback_activity_task
 */

/**
 * Structure step to restore one facultyfeedback activity
 */
class restore_facultyfeedback_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('facultyfeedback', '/activity/facultyfeedback');
        $paths[] = new restore_path_element('facultyfeedback_item', '/activity/facultyfeedback/items/item');
        if ($userinfo) {
            $paths[] = new restore_path_element('facultyfeedback_completed', '/activity/facultyfeedback/completeds/completed');
            $paths[] = new restore_path_element('facultyfeedback_value', '/activity/facultyfeedback/completeds/completed/values/value');
            $paths[] = new restore_path_element('facultyfeedback_tracking', '/activity/facultyfeedback/trackings/tracking');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_facultyfeedback($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the facultyfeedback record
        $newitemid = $DB->insert_record('facultyfeedback', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_facultyfeedback_item($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->facultyfeedback = $this->get_new_parentid('facultyfeedback');

        //dependitem
        $data->dependitem = $this->get_mappingid('facultyfeedback_item', $data->dependitem);

        $newitemid = $DB->insert_record('facultyfeedback_item', $data);
        $this->set_mapping('facultyfeedback_item', $oldid, $newitemid, true); // Can have files
    }

    protected function process_facultyfeedback_completed($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->facultyfeedback = $this->get_new_parentid('facultyfeedback');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('facultyfeedback_completed', $data);
        $this->set_mapping('facultyfeedback_completed', $oldid, $newitemid);
    }

    protected function process_facultyfeedback_value($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->completed = $this->get_new_parentid('facultyfeedback_completed');
        $data->item = $this->get_mappingid('facultyfeedback_item', $data->item);
        $data->course_id = $this->get_courseid();

        $newitemid = $DB->insert_record('facultyfeedback_value', $data);
        $this->set_mapping('facultyfeedback_value', $oldid, $newitemid);
    }

    protected function process_facultyfeedback_tracking($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->facultyfeedback = $this->get_new_parentid('facultyfeedback');
        $data->completed = $this->get_mappingid('facultyfeedback_completed', $data->completed);
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('facultyfeedback_tracking', $data);
    }


    protected function after_execute() {
        // Add facultyfeedback related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_facultyfeedback', 'intro', null);
        $this->add_related_files('mod_facultyfeedback', 'page_after_submit', null);
        $this->add_related_files('mod_facultyfeedback', 'item', 'facultyfeedback_item');
    }
}
