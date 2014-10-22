<script type="text/javascript" src="jquery-1.3.2.js"></script>
<script type="text/javascript">
   
    $(document).ready(function() {
                        
      
        $(document).ajaxStop(function(){
            window.location.reload();
        });     

        $('#verify').click(function(){
            var answer = confirm("Are you sure you want to submit the grades to Hod? After submitting your gradebook will be locked and no further editing will be allowed")
            
            $.post("VerifyGrades.php", {
                   role: $('#role').val()                 
               
            }, function(response){
                                        
                setTimeout("finishAjax('show_departments', '"+escape(response)+"')", 400);
            });
            return false;
        });
        $('#unlocked').click(function(){
            var answer = confirm("Are you sure you want to unlock the Gradebook?")
            if (answer){        
            
                $.post("unlock.php", {
         comment: $('#comment').val()                   
               
                }, function(response){
                                        
                    setTimeout("finishAjax('show_departments', '"+escape(response)+"')", 400);
                });
	}else{
                return false;
            }
        });
        $('#verified').click(function(){
            var answer = confirm("Are you sure you want to submit the Gradebook?")
            if (answer){        
            
                $.post("verify.php", {
                   role: $('#role').val()                 
               
                }, function(response){
                                        
                    setTimeout("finishAjax('show_departments', '"+escape(response)+"')", 400);
                });
            }else{
                return false;
            }
        });
    });
</script>

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
 * The gradebook grader report
 *
 * @package   gradereport_grader
 * @copyright 2007 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/grader/lib.php';

$courseid      = required_param('id', PARAM_INT);        // course id
$page          = optional_param('page', 0, PARAM_INT);   // active page
$edit          = optional_param('edit', -1, PARAM_BOOL); // sticky editting mode
$perpageurl    = optional_param('perpage', 0, PARAM_INT);
$sortitemid    = optional_param('sortitemid', 0, PARAM_ALPHANUM); // sort by which grade item
$action        = optional_param('action', 0, PARAM_ALPHAEXT);
$move          = optional_param('move', 0, PARAM_INT);
$type          = optional_param('type', 0, PARAM_ALPHA);
$target        = optional_param('target', 0, PARAM_ALPHANUM);
$toggle        = optional_param('toggle', NULL, PARAM_INT);
$toggle_type   = optional_param('toggle_type', 0, PARAM_ALPHANUM);

$PAGE->set_url(new moodle_url('/grade/report/grader/index.php', array('id'=>$courseid)));
session_start();
$SESSION->courseid = $courseid;
$_SESSION['courseid'] =$courseid ;
/// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$context = context_course::instance($course->id);

require_capability('gradereport/grader:view', $context);
require_capability('moodle/grade:viewall', $context);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'grader', 'courseid'=>$courseid, 'page'=>$page));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'grader';

/// Build editing on/off buttons

if (!isset($USER->gradeediting)) {
    $USER->gradeediting = array();
}

if (has_capability('moodle/grade:edit', $context)) {
    if (!isset($USER->gradeediting[$course->id])) {
        $USER->gradeediting[$course->id] = 0;
    }

    if (($edit == 1) and confirm_sesskey()) {
        $USER->gradeediting[$course->id] = 1;
    } else if (($edit == 0) and confirm_sesskey()) {
        $USER->gradeediting[$course->id] = 0;
    }

    // page params for the turn editting on
    $options = $gpr->get_options();
    $options['sesskey'] = sesskey();

    if ($USER->gradeediting[$course->id]) {
        $options['edit'] = 0;
        $string = get_string('turneditingoff');
    } else {
        $options['edit'] = 1;
        $string = get_string('turneditingon');
    }

    $buttons = new single_button(new moodle_url('index.php', $options), $string, 'get');
} else {
    $USER->gradeediting[$course->id] = 0;
    $buttons = '';
}

$gradeserror = array();

// Handle toggle change request
if (!is_null($toggle) && !empty($toggle_type)) {
    set_user_preferences(array('grade_report_show'.$toggle_type => $toggle));
}

//first make sure we have proper final grades - this must be done before constructing of the grade tree
grade_regrade_final_grades($courseid);

// Perform actions
if (!empty($target) && !empty($action) && confirm_sesskey()) {
    grade_report_grader::do_process_action($target, $action);
}

$reportname = get_string('pluginname', 'gradereport_grader');

/// Print header
print_grade_page_head($COURSE->id, 'report', 'grader', $reportname, false, $buttons);

//Initialise the grader report object that produces the table
//the class grade_report_grader_ajax was removed as part of MDL-21562
$report = new grade_report_grader($courseid, $gpr, $context, $page, $sortitemid);

// make sure separate group does not prevent view
if ($report->currentgroup == -2) {
    echo $OUTPUT->heading(get_string("notingroup"));
    echo $OUTPUT->footer();
    exit;
}

/// processing posted grades & feedback here
if ($data = data_submitted() and confirm_sesskey() and has_capability('moodle/grade:edit', $context)) {
    $warnings = $report->process_data($data);
} else {
    $warnings = array();
}
// Override perpage if set in URL
if ($perpageurl) {
    $report->user_prefs['studentsperpage'] = $perpageurl;
}
// final grades MUST be loaded after the processing
$report->load_users();
$numusers = $report->get_numusers();
$report->load_final_grades();

echo $report->group_selector;
echo '<div class="clearer"></div>';

//show warnings if any
foreach($warnings as $warning) {
    echo $OUTPUT->notification($warning);
}

 //$studentsperpage = $report->get_pref('studentsperpage');//$report->get_students_per_page();

$studentsperpage =  $report->get_students_per_page();
// Don't use paging if studentsperpage is empty or 0 at course AND site levels
if (!empty($studentsperpage)) {
    echo $OUTPUT->paging_bar($numusers, $report->page, $studentsperpage, $report->pbarurl);
}

//Added By Hina Yousuf(To get user role)
require_once("$CFG->dirroot/enrol/locallib.php");
$manager = new course_enrolment_manager($PAGE, $course, $filter);
$roles = $manager->get_user_roles($USER->id); // email_to_user($user, get_admin(), 'Gradebook Submmmmmmitted', '', '', $attachment = '', $attachname = '', $usetrueaddress = true, $replyto = '', $replytoname = '', $wordwrapwidth = 79);
$hod = 0;
$shod = 0;
$dean = 0;
$fac = 0;

foreach ($roles as $key => $role) {
	//echo "Key value: $key <br/>";
    if ($key == 19) {
        $hod = 1;
        echo '<input type="hidden" id="role" value="' . $key . '" "/>';
    }
    if ($key == 23) {
        $shod = 1;
        echo '<input type="hidden" id="role" value="' . $key . '" "/>';
    }
    if ($key == 24) {
        $dean = 1;
        echo '<input type="hidden" id="role" value="' . $key . '" "/>';
    }
    if ($key == 3) {
        $fac = 1;
        // echo '<input type="hidden" id="role" value="' . $key . '" "/>';
    }
}
//end
$reporthtml = $report->get_grade_table();
$reporthtml .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"$CFG->wwwroot/grade/report/grader/styles.css\" />";
$reporthtml .= '<script src="jquery-1.7.2.min.js" type="text/javascript"></script>';
$context1 = get_context_instance(CONTEXT_SYSTEM);
//Added By Hina Yousuf
$sql = "select * from mdl_grade_items where courseid=$courseid  ";
$verified = $DB->get_record_sql($sql);

$submission = $DB->get_record_sql("select * from {gradereport_grader} where courseid=$courseid");
if ($submission->seniorhodtimestamp != "" && $submission->deantimestamp == "" && $dean == 1 && $verified->locked == 1) {
    if (has_capability('gradereport/grader:submit', $context)) {
        echo '<input type="button" id="verified" value="Submit to ExamBranch" "/>';
        echo '<br/><div align="center"><b>The Gradebook has been submitted to Dean.</b></div>';
    }
}

if ($submission->seniorhodtimestamp == "" && $submission->hodtimestamp != "" && $shod == 1 && $verified->locked == 1) {
    if (has_capability('gradereport/grader:submit', $context)) {
        echo '<input type="button" id="verified" value="Submit to Dean" "/>';
        echo '<br/><div align="center"><b>The Gradebook has been submitted to Senior HOD.</b></div>';
    }
}
// echo "Faculty timestamp ".$submission->facultytimestamp."HOD timestamp ".$submission->hodtimestamp." Verified locked ".$verified->locked." HOD var ".$hod;
if ($submission->hodtimestamp == "" && $submission->facultytimestamp != "" && $hod == 1 && $verified->locked == 1) {
    if (has_capability('gradereport/grader:submit', $context)) {
        echo '<input type="button" id="verified" value="Submit to Senior HOD" "/>';
        echo '<br/><div align="center"><b>The Gradebook has been submitted to HOD.</b></div>';
    }
}

//if (has_capability('gradereport/grader:unlock', $context)) {
//    if ( $verified->locked == 1) {
//        echo '<br/><div align="center"><b>The Gradebook has been submitted to Exam Branch.</b></div>';
//    }
//}
if ($verified->locked == 1 && $fac == 1) {
    if (!has_capability('gradereport/grader:unlock', $context)) {
        echo '<br/><div align="center"><b>The Gradebook has been locked.Contact HOD to unlock the Gradebook.</b></div>';
    }
}
if (has_capability('gradereport/grader:unlock', $context)) {
    if ($verified->locked == 1 && $hod == 1) {
        echo '<form action="index.php" method="post">';

        echo '<input type="hidden" value="' . s($courseid) . '" name="id" />';
        echo '<input type="hidden" value="' . sesskey() . '" name="sesskey" />';
        echo '<input type="hidden" value="grader" name="report"/>';
        echo '<input type="hidden" value="' . $page . '" name="page"/>';
        echo '<b>Add Comments:</b><input type="text" max="500" id="comment" name="comment" value="" ></textarea>';
        echo '<input type="submit" id="unlocked" value="Unlock Gradebook"</form>';
    }
}
//Added By Hina Yousuf(Get the depth of header dynamically)
$gtree = $report->gtree;
foreach ($gtree->get_levels() as $key => $row) {
    if ($key == 0) {
        // do not display course grade category
        // continue;
    }
    $temp = 0;
    foreach ($row as $columnkey => $element) {
        if (!empty($element['depth'])) {
            if ($element['depth'] > $temp) {
                $depth = $element['depth'];
                $temp = $depth;
            }
        }
    }
}
//end
/////////////////////////////////////////////////////////////////////////
$scrolling = get_user_preferences('grade_report_grader_reportheight');
$scrolling = $scrolling == null ? 620 : 300 + ($scrolling * 40);

// initialize the javascript that will be used to enable scrolling
// special thanks to jaimon mathew for jscript
$headerrows = $depth + 1; //($USER->gradeediting[$courseid]) ? 2 : 1;		
$extrafields = $report->extrafields;
$headercols = 1; //+ count($extrafields);
$headerinit = "fxheaderInit('lae-user-grades', $scrolling," . $headerrows . ',' . $headercols . ');';
$reporthtml .=
        '<script src="' . $CFG->wwwroot . '/grade/report/grader/fxHeader_0.6.js" type="text/javascript"></script>
		        <script type="text/javascript">' . $headerinit . 'fxheader(); </script>';

$reporthtml .=
        '<script src="' . $CFG->wwwroot . '/grade/report/grader/my_jslib.js" type="text/javascript"></script>';



// print submit button
if ($USER->gradeediting[$course->id] && ($report->get_pref('showquickfeedback') || $report->get_pref('quickgrading'))) {
    echo '<form action="index.php" method="post">';
    echo '<div>';
    echo '<input type="hidden" value="'.s($courseid).'" name="id" />';
    echo '<input type="hidden" value="'.sesskey().'" name="sesskey" />';
    echo '<input type="hidden" value="grader" name="report"/>';
    echo '<input type="hidden" value="'.$page.'" name="page"/>';
    echo $reporthtml;
    echo '<div class="submit"><input type="submit" value="Save" /></div>';
    if ($verified->locked != 1 && $fac == 1) {
        echo '<br/><div align="center"><b>It is important to submit the grades to HOD.To submit click on the &quot;Submit to HOD &quot; button.</b></div>';
        echo '<br/><div align="center"><b>Certified that I have prepared the result carefully and checked all the data entries and I have also shown the End Semester Exam answer sheets to the students.</b></div>';
        echo '<div class="submit"><input type="button" id="verify" value="Submit to HOD"/></div>';
    }

//  echo '<div class="submit"><input type="submit" id="gradersubmit" value="'.s(get_string('update')).'" /></div>';
    echo '</div></form>';
} else {
    echo $reporthtml;
}
//echo $studentsperpage;
// prints paging bar at bottom for large pages
if (!empty($studentsperpage) && $studentsperpage >= 20) {
    echo $OUTPUT->paging_bar($numusers, $report->page, $studentsperpage, $report->pbarurl);
}
echo $OUTPUT->footer();
