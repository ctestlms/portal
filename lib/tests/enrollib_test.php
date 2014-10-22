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
 * Tests events subsystems
 *
 * @package    core
 * @subpackage group
 * @copyright  2007 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("$CFG->dirroot/lib/enrollib.php");

class enrollib_testcase extends advanced_testcase {

    public function test_enrol_add_fee_defaulter() {
        global $CFG,$DB;
        require_once("$CFG->dirroot/lib/enrollib.php");
        $this->resetAfterTest(true);

        // $generator = $this->getDataGenerator();
        $admin = get_admin();
        $user1 = $this->getDataGenerator()->create_user();
       
        if (!$enrol_manual = enrol_get_plugin('manual')) {
            throw new coding_exception('Can not instantiate enrol_manual');
        }
         // Call the add defaulter method to set the user as a fee defaulter
        $enrol_manual->add_defaulter($user1->id, 0, 0);
        $user = $DB->get_record('user', array('id' => $user1->id));
        $this->assertEquals($user->defaulter, 1);
         // Call the remove defaulter method to set the user as a non-fee defaulter
        $enrol_manual->remove_defaulter($user1->id, 0, 0);
        $user = $DB->get_record('user', array('id' => $user1->id));
        $this->assertEquals($user->defaulter, 0);
    }

}

