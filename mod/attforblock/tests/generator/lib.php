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

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz module test data generator class
 *
 * @package mod_quiz
 * @copyright 2012 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attforblock_generator extends phpunit_module_generator {

    /**
     * Create new quiz module instance.
     * @param array|stdClass $record
     * @param array $options (mostly course_module properties)
     * @return stdClass activity record with extra cmid field
     */
    public function create_instance($record = null, array $options = null) {
        global $CFG, $DB;
        require_once("$CFG->dirroot/mod/attforblock/lib.php");

        $this->instancecount++;
        $i = $this->instancecount;

        $record = (object) (array) $record;
        $options = (array) $options;

        if (empty($record->course)) {
            throw new coding_exception('module generator requires $record->course');
        }
        if (isset($options['idnumber'])) {
            $record->cmidnumber = $options['idnumber'];
        } else {
            $record->cmidnumber = '';
        }


        $defaultattsettings = array(
            'name' => get_string('pluginname', 'attforblock'),
            'grade' => "100",
        );

        foreach ($defaultattsettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }


        $record->coursemodule = $this->precreate_course_module($record->course, $options);
        $id = attforblock_add_instance($record);
        $this->post_add_instance($id, $record->coursemodule);

        $session_record = new stdClass();

        $session_record->courseid = $record->course;
        $session_record->sessdate = time();
        $session_record->duration = 3600;
        $session_record->description = 1;
        $session_record->timemodified = time();
        $session_record->lasttaken = 1251970721;
        $session_record->lasttakenby = 5635;
        $session_record->topicname = "JAVA Reflections";

        if ($DB->insert_record('attendance_sessions', $session_record)) {
            return 1;
        } else {
            return 0;
        }
       
       
    }

}
