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
 * Student Resource Center Block page.
 *
 * @package    block
 * @subpackage blog_menu
 * @copyright  SEECS, NUST
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The blog menu block class
 */

class block_student_resource_center extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_student_resource_center');
    }
    function instance_allow_config() {
     return FALSE;
    }
    function instance_allow_multiple() {
  // Are you going to allow multiple instances of each block?
  // If yes, then it is assumed that the block WILL USE per-instance configuration
     return FALSE;
      
}function get_content() {
        global $CFG, $OUTPUT,$PAGE, $USER, $DB;
        if ($this->content !== NULL) {
            return $this->content;
        }//, array('id' => $this->instance->id)
        $this->content = new stdClass;
  
//print_r($USER->access);
//if (has_capability('block/student_resource_center:examview', $this->context))
  //     {
       $sql = "SELECT cou.id FROM `mdl_course_categories` cou
	JOIN `mdl_context` con ON con.instanceid = cou.id
        JOIN `mdl_role_assignments` ra ON ra.contextid = con.id
        WHERE con.contextlevel = 40
        AND ra.userid = $USER->id
        AND ra.roleid = 21";
        
       
        $rec = $DB->get_record_sql($sql);
        //$arr = (array)$rec;
        //print_r($arr);
if ($rec == NULL) {
    $this->content->text .= '<ul class="list">';
  $this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/student_resource_center/requests/requestslist.php">Exam Branch Dashboard</a></li>' ;  // do stuff
$this->content->text  .= '</ul>';
  
}
 else{     
        $this->content->text .= '<ul class="list">';
          $this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/student_resource_center/requests/requestslist.php">Exam Branch Dashboard</a></li>' ;  // do stuff
$this->content->text  .= '<li><a href="'.$CFG->wwwroot.'/blocks/student_resource_center/student_new_reg.php?schoolid='.$rec->id.'">Student Joining</a></li>';
        //$this->content->text  .= '<li><a href="'.$CFG->wwwroot.'/blocks/student_resource_center/profile_pdf.php">Student Profile</a></li>';
        $this->content->text  .= '<li><a href="'.$CFG->wwwroot.'/blocks/student_resource_center/update_record.php?schoolid='.$rec->id.'">Update Record</a></li>';
        $this->content->text  .= '<li><a href="'.$CFG->wwwroot.'/blocks/student_resource_center/strength_report1.php?schoolid='.$rec->id.'">Student Strength Report</a></li>';
        $this->content->text  .= '<li><a href="'.$CFG->wwwroot.'/blocks/student_resource_center/student_joining_report.php?schoolid='.$rec->id.'">Student Joining Report</a></li>';       
        // $this->content->text  .= '<li><a href="'.$CFG->wwwroot.'/blocks/student_resource_center/pending_email.php">Pending Students Email</a></li>';
        $this->content->text  .= '</ul>';      
       
 }
        $this->content->footer = '';
	
        return $this->content;
        
    }

    // The PHP tag and the curly bracket for the class definition 
} // will only be closed after there is another function added in the next section.