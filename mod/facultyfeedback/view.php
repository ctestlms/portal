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
 * the first page to view the facultyfeedback
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package facultyfeedback
 */
require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);
$courseid = optional_param('courseid', false, PARAM_INT);

$current_tab = 'view';

if (! $cm = get_coursemodule_from_id('facultyfeedback', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $facultyfeedback = $DB->get_record("facultyfeedback", array("id"=>$cm->instance))) {
    print_error('invalidcoursemodule');
}

$context = context_module::instance($cm->id);

$facultyfeedback_complete_cap = false;

if (has_capability('mod/facultyfeedback:complete', $context)) {
    $facultyfeedback_complete_cap = true;
}

if (isset($CFG->facultyfeedback_allowfullanonymous)
            AND $CFG->facultyfeedback_allowfullanonymous
            AND $course->id == SITEID
            AND (!$courseid OR $courseid == SITEID)
            AND $facultyfeedback->anonymous == FEEDBACK_ANONYMOUS_YES ) {
    $facultyfeedback_complete_cap = true;
}

//check whether the facultyfeedback is located and! started from the mainsite
if ($course->id == SITEID AND !$courseid) {
    $courseid = SITEID;
}

//check whether the facultyfeedback is mapped to the given courseid
if ($course->id == SITEID AND !has_capability('mod/facultyfeedback:edititems', $context)) {
    if ($DB->get_records('facultyfeedback_sitecourse_map', array('facultyfeedbackid'=>$facultyfeedback->id))) {
        $params = array('facultyfeedbackid'=>$facultyfeedback->id, 'courseid'=>$courseid);
        if (!$DB->get_record('facultyfeedback_sitecourse_map', $params)) {
            print_error('invalidcoursemodule');
        }
    }
}

if ($facultyfeedback->anonymous != FEEDBACK_ANONYMOUS_YES) {
    if ($course->id == SITEID) {
        require_login($course, true);
    } else {
        require_login($course, true, $cm);
    }
} else {
    if ($course->id == SITEID) {
        require_course_login($course, true);
    } else {
        require_course_login($course, true, $cm);
    }
}

//check whether the given courseid exists
if ($courseid AND $courseid != SITEID) {
    if ($course2 = $DB->get_record('course', array('id'=>$courseid))) {
        require_course_login($course2); //this overwrites the object $course :-(
        $course = $DB->get_record("course", array("id"=>$cm->course)); // the workaround
    } else {
        print_error('invalidcourseid');
    }
}

if ($facultyfeedback->anonymous == FEEDBACK_ANONYMOUS_NO) {
    add_to_log($course->id, 'facultyfeedback', 'view', 'view.php?id='.$cm->id, $facultyfeedback->id, $cm->id);
}

/// Print the page header
$strfacultyfeedbacks = get_string("modulenameplural", "facultyfeedback");
$strfacultyfeedback  = get_string("modulename", "facultyfeedback");

if ($course->id == SITEID) {
    $PAGE->set_context($context);
    $PAGE->set_cm($cm, $course); // set's up global $COURSE
    $PAGE->set_pagelayout('incourse');
}
$PAGE->set_url('/mod/facultyfeedback/view.php', array('id'=>$cm->id, 'do_show'=>'view'));
$PAGE->set_title(format_string($facultyfeedback->name));
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header();

//ishidden check.
//facultyfeedback in courses
$cap_viewhiddenactivities = has_capability('moodle/course:viewhiddenactivities', $context);
if ((empty($cm->visible) and !$cap_viewhiddenactivities) AND $course->id != SITEID) {
    notice(get_string("activityiscurrentlyhidden"));
}

//ishidden check.
//facultyfeedback on mainsite
if ((empty($cm->visible) and !$cap_viewhiddenactivities) AND $courseid == SITEID) {
    notice(get_string("activityiscurrentlyhidden"));
}

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

/// print the tabs
require('tabs.php');

$previewimg = $OUTPUT->pix_icon('t/preview', get_string('preview'));
$previewlnk = '<a href="'.$CFG->wwwroot.'/mod/facultyfeedback/print.php?id='.$id.'">'.$previewimg.'</a>';

echo $OUTPUT->heading(format_text($facultyfeedback->name.' '.$previewlnk));

//show some infos to the facultyfeedback
if (has_capability('mod/facultyfeedback:edititems', $context)) {
    //get the groupid
    $groupselect = groups_print_activity_menu($cm, $CFG->wwwroot.'/mod/facultyfeedback/view.php?id='.$cm->id, true);
    $mygroupid = groups_get_activity_group($cm);

    echo $OUTPUT->box_start('boxaligncenter boxwidthwide');
    echo $groupselect.'<div class="clearer">&nbsp;</div>';
    $completedscount = facultyfeedback_get_completeds_group_count($facultyfeedback, $mygroupid);
    echo $OUTPUT->box_start('facultyfeedback_info');
    echo '<span class="facultyfeedback_info">';
    echo get_string('completed_facultyfeedbacks', 'facultyfeedback').': ';
    echo '</span>';
    echo '<span class="facultyfeedback_info_value">';
    echo $completedscount;
    echo '</span>';
    echo $OUTPUT->box_end();

    $params = array('facultyfeedback'=>$facultyfeedback->id, 'hasvalue'=>1);
    $itemscount = $DB->count_records('facultyfeedback_item', $params);
    echo $OUTPUT->box_start('facultyfeedback_info');
    echo '<span class="facultyfeedback_info">';
    echo get_string('questions', 'facultyfeedback').': ';
    echo '</span>';
    echo '<span class="facultyfeedback_info_value">';
    echo $itemscount;
    echo '</span>';
    echo $OUTPUT->box_end();

    if ($facultyfeedback->timeopen) {
        echo $OUTPUT->box_start('facultyfeedback_info');
        echo '<span class="facultyfeedback_info">';
        echo get_string('facultyfeedbackopen', 'facultyfeedback').': ';
        echo '</span>';
        echo '<span class="facultyfeedback_info_value">';
        echo userdate($facultyfeedback->timeopen);
        echo '</span>';
        echo $OUTPUT->box_end();
    }
    if ($facultyfeedback->timeclose) {
        echo $OUTPUT->box_start('facultyfeedback_info');
        echo '<span class="facultyfeedback_info">';
        echo get_string('timeclose', 'facultyfeedback').': ';
        echo '</span>';
        echo '<span class="facultyfeedback_info_value">';
        echo userdate($facultyfeedback->timeclose);
        echo '</span>';
        echo $OUTPUT->box_end();
    }
    echo $OUTPUT->box_end();
}

if (has_capability('mod/facultyfeedback:edititems', $context)) {
    echo $OUTPUT->heading(get_string('description', 'facultyfeedback'), 4);
}
echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
$options = (object)array('noclean'=>true);
echo format_module_intro('facultyfeedback', $facultyfeedback, $cm->id);
echo $OUTPUT->box_end();

if (has_capability('mod/facultyfeedback:edititems', $context)) {
    require_once($CFG->libdir . '/filelib.php');

    $page_after_submit_output = file_rewrite_pluginfile_urls($facultyfeedback->page_after_submit,
                                                            'pluginfile.php',
                                                            $context->id,
                                                            'mod_facultyfeedback',
                                                            'page_after_submit',
                                                            0);

    echo $OUTPUT->heading(get_string("page_after_submit", "facultyfeedback"), 4);
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    echo format_text($page_after_submit_output,
                     $facultyfeedback->page_after_submitformat,
                     array('overflowdiv'=>true));

    echo $OUTPUT->box_end();
}

if ( (intval($facultyfeedback->publish_stats) == 1) AND
                ( has_capability('mod/facultyfeedback:viewanalysepage', $context)) AND
                !( has_capability('mod/facultyfeedback:viewreports', $context)) ) {

    $params = array('userid'=>$USER->id, 'facultyfeedback'=>$facultyfeedback->id);
    if ($multiple_count = $DB->count_records('facultyfeedback_tracking', $params)) {
        $url_params = array('id'=>$id, 'courseid'=>$courseid);
        $analysisurl = new moodle_url('/mod/facultyfeedback/analysis.php', $url_params);
        echo '<div class="mdl-align"><a href="'.$analysisurl->out().'">';
        echo get_string('completed_facultyfeedbacks', 'facultyfeedback').'</a>';
        echo '</div>';
    }
}

//####### mapcourse-start
if (has_capability('mod/facultyfeedback:mapcourse', $context)) {
    if ($facultyfeedback->course == SITEID) {
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
        echo '<div class="mdl-align">';
        echo '<form action="mapcourse.php" method="get">';
        echo '<fieldset>';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input type="hidden" name="id" value="'.$id.'" />';
        echo '<button type="submit">'.get_string('mapcourses', 'facultyfeedback').'</button>';
        echo $OUTPUT->help_icon('mapcourse', 'facultyfeedback');
        echo '</fieldset>';
        echo '</form>';
        echo '<br />';
        echo '</div>';
        echo $OUTPUT->box_end();
    }
}
//####### mapcourse-end

//####### completed-start
if ($facultyfeedback_complete_cap) {
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    //check, whether the facultyfeedback is open (timeopen, timeclose)
    $checktime = time();
    if (($facultyfeedback->timeopen > $checktime) OR
            ($facultyfeedback->timeclose < $checktime AND $facultyfeedback->timeclose > 0)) {

        echo '<h2><font color="red">'.get_string('facultyfeedback_is_not_open', 'facultyfeedback').'</font></h2>';
        echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
        echo $OUTPUT->box_end();
        echo $OUTPUT->footer();
        exit;
    }

    //check multiple Submit
    $facultyfeedback_can_submit = true;
    if ($facultyfeedback->multiple_submit == 0 ) {
        if (facultyfeedback_is_already_submitted($facultyfeedback->id, $courseid)) {
            $facultyfeedback_can_submit = false;
        }
    }
    if ($facultyfeedback_can_submit) {
        //if the user is not known so we cannot save the values temporarly
        if (!isloggedin() or isguestuser()) {
            $completefile = 'complete_guest.php';
            $guestid = sesskey();
        } else {
            $completefile = 'complete.php';
            $guestid = false;
        }
        $url_params = array('id'=>$id, 'courseid'=>$courseid, 'gopage'=>0);
        $completeurl = new moodle_url('/mod/facultyfeedback/'.$completefile, $url_params);

        $facultyfeedbackcompletedtmp = facultyfeedback_get_current_completed($facultyfeedback->id, true, $courseid, $guestid);
        if ($facultyfeedbackcompletedtmp) {
            if ($startpage = facultyfeedback_get_page_to_continue($facultyfeedback->id, $courseid, $guestid)) {
                $completeurl->param('gopage', $startpage);
            }
            echo '<a href="'.$completeurl->out().'">'.get_string('continue_the_form', 'facultyfeedback').'</a>';
        } else {
            echo '<a href="'.$completeurl->out().'">'.get_string('complete_the_form', 'facultyfeedback').'</a>';
        }
    } else {
        echo '<h2><font color="red">';
        echo get_string('this_facultyfeedback_is_already_submitted', 'facultyfeedback');
        echo '</font></h2>';
        if ($courseid) {
            echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$courseid);
        } else {
            echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
        }
    }
    echo $OUTPUT->box_end();
}
//####### completed-end

/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();

