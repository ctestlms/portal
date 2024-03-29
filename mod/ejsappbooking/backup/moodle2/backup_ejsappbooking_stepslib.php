<?php

// This file is part of the Moodle module "EJSApp"
//
// EJSApp is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// EJSApp is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// The GNU General Public License is available on <http://www.gnu.org/licenses/>
//
// EJSApp has been developed by:
//  - Luis de la Torre: ldelatorre@dia.uned.es
//	- Ruben Heradio: rheradio@issi.uned.es
//
//  at the Computer Science and Automatic Control, Spanish Open University
//  (UNED), Madrid, Spain


/**
 * Steps file to perform the EJSAppBooking backup
 *
 * Backup/Restore files in ejsappbooking just work with data in the ejsappbooking table.
 * The data from ejsappbooking_remlab_access and ejsappbooking_useraccess is backup/restored
 * by ejsapp
 *
 * @package    mod
 * @subpackage ejsappbooking
 * @copyright  2012 Luis de la Torre and Ruben Heradio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete EJSApp-Booking structure for backup
 */
class backup_ejsappbooking_activity_structure_step extends backup_activity_structure_step {


    /**
     * Define the complete structure for backup, with file and id annotations
     */
    protected function define_structure() {
        global $DB;

        $ejsappbooking = new backup_nested_element('ejsappbooking', array('id'),
            array('course', 'name', 'intro', 'introformat', 'timecreated', 'timemodified'));

        $ejsappbooking->set_source_table('ejsappbooking', array('id' => backup::VAR_ACTIVITYID));
        return $this->prepare_activity_structure($ejsappbooking);
    }
    
}