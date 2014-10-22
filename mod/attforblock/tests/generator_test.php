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
 * PHPUnit data generator tests
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2012 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit data generator testcase
 *
 * @package    mod_quiz
 * @category   phpunit
 * @copyright  2012 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG, $DB;
require_once("$CFG->dirroot/mod/attforblock/locallib.php");

class mod_attforblock_generator_testcase extends advanced_testcase {

    public function test_generator() {
        global $DB, $SITE, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        /** @var mod_attforblock_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_attforblock');

        $sessid = $generator->create_instance(array('course' => $SITE->id));

        $topicname = $DB->get_record_sql("select topicname,courseid from {attendance_sessions} where id= $sessid");

        $this->assertEquals($topicname->topicname, "JAVA Reflections");


        $session_record = new stdClass();

        $session_record->courseid = $SITE->id;
        $session_record->sessdate = time();
        $session_record->duration = 3600;
        $session_record->description = 6;
        $session_record->timemodified = time();
        $session_record->lasttaken = 1251970721;
        $session_record->lasttakenby = 5635;
        $session_record->topicname = "JAVA Reflections";

        $DB->insert_record('attendance_sessions', $session_record);

        $session_record1 = new stdClass();

        $session_record1->courseid = $SITE->id;
        $session_record1->sessdate = time();
        $session_record1->duration = 3600;
        $session_record1->description = 1;
        $session_record1->timemodified = time();
        $session_record1->lasttaken = 1251970721;
        $session_record1->lasttakenby = 5635;
        $session_record1->topicname = "JAVA Reflections";

        $DB->insert_record('attendance_sessions', $session_record1);


        $session_record2 = new stdClass();

        $session_record2->courseid = $SITE->id;
        $session_record2->sessdate = time();
        $session_record2->duration = 3600;
        $session_record2->description = 4;
        $session_record2->timemodified = time();
        $session_record2->lasttaken = 1251970721;
        $session_record2->lasttakenby = 5635;
        $session_record2->topicname = "JAVA Reflections";

        $DB->insert_record('attendance_sessions', $session_record2);

        $session_record3 = new stdClass();

        $session_record3->courseid = $SITE->id;
        $session_record3->sessdate = time();
        $session_record3->duration = 3600;
        $session_record3->description = 5;
        $session_record3->timemodified = time();
        $session_record3->lasttaken = 1251970721;
        $session_record3->lasttakenby = 5635;
        $session_record3->topicname = "JAVA Reflections";

        $DB->insert_record('attendance_sessions', $session_record3);



        $session_record4 = new stdClass();
        $session_record4->courseid = $SITE->id;
        $session_record4->sessdate = time();
        $session_record4->duration = 3600;
        $session_record4->description = 2;
        $session_record4->timemodified = time();
        $session_record4->lasttaken = 1251970721;
        $session_record4->lasttakenby = 5635;
        $session_record4->topicname = "JAVA Reflections";
        $DB->insert_record('attendance_sessions', $session_record4);

        $session_record5 = new stdClass();
        $session_record5->courseid = $SITE->id;
        $session_record5->sessdate = time();
        $session_record5->duration = 3600;
        $session_record5->description = 3;
        $session_record5->timemodified = time();
        $session_record5->lasttaken = 1251970721;
        $session_record5->lasttakenby = 5635;
        $session_record5->topicname = "JAVA Reflections";
        $DB->insert_record('attendance_sessions', $session_record5);

        $course = new stdClass();
        $course->id = $SITE->id;
        $date = time();
        $newdate = strtotime(date("Y-m-d", $date) . " -1 days");
        $enddate = strtotime(date("Y-m-d", $date) . " +1 days");

        $course->startdate = $newdate;
        $markedlecsessions = get_lecture_sessions($course, $enddate);
        $markedlabsessions = get_lab_sessions($course, $enddate);


        $this->assertEquals(8.5, $markedlecsessions);
        $this->assertEquals(2, $markedlabsessions);
    }

    public function test_missing_lectures() {
        global $DB, $SITE, $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();

//        /** @var mod_attforblock_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_attforblock');

        $generator->create_instance(array('course' => $SITE->id));
        $session_record = new stdClass();

        $session_record->courseid = $SITE->id;
        $session_record->sessdate = time();
        $session_record->duration = 3600;
        $session_record->description = 6;
        $session_record->timemodified = time();
        $session_record->lasttaken = 1251970721;
        $session_record->lasttakenby = 5635;
        $session_record->topicname = "JAVA Reflections";

        $DB->insert_record('attendance_sessions', $session_record);

        $session_record1 = new stdClass();

        $session_record1->courseid = $SITE->id;
        $session_record1->sessdate = time();
        $session_record1->duration = 3600;
        $session_record1->description = 1;
        $session_record1->timemodified = time();
        $session_record1->lasttaken = 1251970721;
        $session_record1->lasttakenby = 5635;
        $session_record1->topicname = "JAVA Reflections";

        $DB->insert_record('attendance_sessions', $session_record1);


        $session_record2 = new stdClass();

        $session_record2->courseid = $SITE->id;
        $session_record2->sessdate = time();
        $session_record2->duration = 3600;
        $session_record2->description = 4;
        $session_record2->timemodified = time();
        $session_record2->lasttaken = 1251970721;
        $session_record2->lasttakenby = 5635;
        $session_record2->topicname = "JAVA Reflections";

        $DB->insert_record('attendance_sessions', $session_record2);

        $session_record3 = new stdClass();

        $session_record3->courseid = $SITE->id;
        $session_record3->sessdate = time();
        $session_record3->duration = 3600;
        $session_record3->description = 5;
        $session_record3->timemodified = time();
        $session_record3->lasttaken = 1251970721;
        $session_record3->lasttakenby = 5635;
        $session_record3->topicname = "JAVA Reflections";

        $DB->insert_record('attendance_sessions', $session_record3);



        $session_record4 = new stdClass();
        $session_record4->courseid = $SITE->id;
        $session_record4->sessdate = time();
        $session_record4->duration = 3600;
        $session_record4->description = 2;
        $session_record4->timemodified = time();
        $session_record4->lasttaken = 1251970721;
        $session_record4->lasttakenby = 5635;
        $session_record4->topicname = "JAVA Reflections";
        $DB->insert_record('attendance_sessions', $session_record4);

        $session_record5 = new stdClass();
        $session_record5->courseid = $SITE->id;
        $session_record5->sessdate = time();
        $session_record5->duration = 3600;
        $session_record5->description = 3;
        $session_record5->timemodified = time();
        $session_record5->lasttaken = 1251970721;
        $session_record5->lasttakenby = 5635;
        $session_record5->topicname = "JAVA Reflections";
        $DB->insert_record('attendance_sessions', $session_record5);

        $course = new stdClass();
        $course->id = $SITE->id;
        $date = time();
        $newdate = strtotime(date("Y-m-d", $date) . " -1 days");
        $enddate = strtotime(date("Y-m-d", $date) . " +1 days");

        $course->startdate = $newdate;
        $markedlecsessions = get_lecture_sessions($course, $enddate);
        $markedlabsessions = get_lab_sessions($course, $enddate);


        $this->assertEquals(8.5, $markedlecsessions);
        $this->assertEquals(2, $markedlabsessions);
    }

}
