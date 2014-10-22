<?php
//echo "123";
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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * 
 */


require_once($CFG->libdir . "/externallib.php");

class local_schools_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_schools_parameters() {
        return new external_function_parameters(
                array());
    }

    public static function get_schools() {
        global $DB;
        
         $sql = "SELECT id, name
          FROM mdl_course_categories 
          WHERE parent = 0";
         $result=$DB->get_records_sql($sql);
 
        $schools = array();
        $school = array();
        foreach($result as $r){
           
  $school["schoolid"] = $r->id;
  $school['schoolname'] = $r->name;
   $schools[] = $school;
       }
       
        return $schools;

    }

    public static function get_schools_returns()
 {
        return new external_multiple_structure(
         new external_single_structure(
                array(
                    'schoolid' => new external_value(PARAM_INT, 'school id'),
                   'schoolname' => new external_value(PARAM_RAW, 'school name')
                    
              )
           )
        );
    }



}
