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
 * prints the form so the user can fill out the facultyfeedback
 *
 * @author Andreas Grabs
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package facultyfeedback
 */

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/completionlib.php');

facultyfeedback_init_facultyfeedback_session();

$id = required_param('id', PARAM_INT);
$completedid = optional_param('completedid', false, PARAM_INT);
$preservevalues  = optional_param('preservevalues', 0,  PARAM_INT);
$courseid = optional_param('courseid', false, PARAM_INT);
$gopage = optional_param('gopage', -1, PARAM_INT);
$lastpage = optional_param('lastpage', false, PARAM_INT);
$startitempos = optional_param('startitempos', 0, PARAM_INT);
$lastitempos = optional_param('lastitempos', 0, PARAM_INT);
$anonymous_response = optional_param('anonymous_response', 0, PARAM_INT); //arb

$highlightrequired = false;

if (($formdata = data_submitted()) AND !confirm_sesskey()) {
    print_error('invalidsesskey');
}

//if the use hit enter into a textfield so the form should not submit
if (isset($formdata->sesskey) AND
    !isset($formdata->savevalues) AND
    !isset($formdata->gonextpage) AND
    !isset($formdata->gopreviouspage)) {

    $gopage = $formdata->lastpage;
}

if (isset($formdata->savevalues)) {
    $savevalues = true;
} else {
    $savevalues = false;
}

if ($gopage < 0 AND !$savevalues) {
    if (isset($formdata->gonextpage)) {
        $gopage = $lastpage + 1;
        $gonextpage = true;
        $gopreviouspage = false;
    } else if (isset($formdata->gopreviouspage)) {
        $gopage = $lastpage - 1;
        $gonextpage = false;
        $gopreviouspage = true;
    } else {
        print_error('missingparameter');
    }
} else {
    $gonextpage = $gopreviouspage = false;
}

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

//check whether the facultyfeedback is located and! started from the mainsite
if ($course->id == SITEID AND !$courseid) {
    $courseid = SITEID;
}

//check whether the facultyfeedback is mapped to the given courseid
if ($course->id == SITEID AND !has_capability('mod/facultyfeedback:edititems', $context)) {
    if ($DB->get_records('facultyfeedback_sitecourse_map', array('facultyfeedbackid'=>$facultyfeedback->id))) {
        $params = array('facultyfeedbackid'=>$facultyfeedback->id, 'courseid'=>$courseid);
        if (!$DB->get_record('facultyfeedback_sitecourse_map', $params)) {
            print_error('notavailable', 'facultyfeedback');
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

if (!$facultyfeedback_complete_cap) {
    print_error('error');
}

// Mark activity viewed for completion-tracking
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

/// Print the page header
$strfacultyfeedbacks = get_string("modulenameplural", "facultyfeedback");
$strfacultyfeedback  = get_string("modulename", "facultyfeedback");

if ($course->id == SITEID) {
    $PAGE->set_cm($cm, $course); // set's up global $COURSE
    $PAGE->set_pagelayout('incourse');
}

$PAGE->navbar->add(get_string('facultyfeedback:complete', 'facultyfeedback'));
$urlparams = array('id'=>$cm->id, 'gopage'=>$gopage, 'courseid'=>$course->id);
$PAGE->set_url('/mod/facultyfeedback/complete.php', $urlparams);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_title(format_string($facultyfeedback->name));
echo $OUTPUT->header();

//ishidden check.
//facultyfeedback in courses
if ((empty($cm->visible) AND
        !has_capability('moodle/course:viewhiddenactivities', $context)) AND
        $course->id != SITEID) {
    notice(get_string("activityiscurrentlyhidden"));
}

//ishidden check.
//facultyfeedback on mainsite
if ((empty($cm->visible) AND
        !has_capability('moodle/course:viewhiddenactivities', $context)) AND
        $courseid == SITEID) {
    notice(get_string("activityiscurrentlyhidden"));
}

//check, if the facultyfeedback is open (timeopen, timeclose)
$checktime = time();
$facultyfeedback_is_closed = ($facultyfeedback->timeopen > $checktime) OR
                      ($facultyfeedback->timeclose < $checktime AND
                            $facultyfeedback->timeclose > 0);

if ($facultyfeedback_is_closed) {
    echo $OUTPUT->box_start('generalbox boxaligncenter');
        echo '<h2><font color="red">';
        echo get_string('facultyfeedback_is_not_open', 'facultyfeedback');
        echo '</font></h2>';
        echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

//additional check for multiple-submit (prevent browsers back-button).
//the main-check is in view.php
$facultyfeedback_can_submit = true;
if ($facultyfeedback->multiple_submit == 0 ) {
    if (facultyfeedback_is_already_submitted($facultyfeedback->id, $courseid)) {
        $facultyfeedback_can_submit = false;
    }
}
if ($facultyfeedback_can_submit) {
    //preserving the items
    if ($preservevalues == 1) {
        if (!isset($SESSION->facultyfeedback->is_started) OR !$SESSION->facultyfeedback->is_started == true) {
            print_error('error', '', $CFG->wwwroot.'/course/view.php?id='.$course->id);
        }
        //checken, ob alle required items einen wert haben
        if (facultyfeedback_check_values($startitempos, $lastitempos)) {
            $userid = $USER->id; //arb
            if ($completedid = facultyfeedback_save_values($USER->id, true)) {
                if ($userid > 0) {
                    add_to_log($course->id,
                               'facultyfeedback',
                               'startcomplete',
                               'view.php?id='.$cm->id,
                               $facultyfeedback->id,
                               $cm->id,
                               $userid);
                }
                if (!$gonextpage AND !$gopreviouspage) {
                    $preservevalues = false;//es kann gespeichert werden
                }

            } else {
                $savereturn = 'failed';
                if (isset($lastpage)) {
                    $gopage = $lastpage;
                } else {
                    print_error('missingparameter');
                }
            }
        } else {
            $savereturn = 'missing';
            $highlightrequired = true;
            if (isset($lastpage)) {
                $gopage = $lastpage;
            } else {
                print_error('missingparameter');
            }

        }
    }

    //saving the items
    if ($savevalues AND !$preservevalues) {
        //exists there any pagebreak, so there are values in the facultyfeedback_valuetmp
        $userid = $USER->id; //arb

        if ($facultyfeedback->anonymous == FEEDBACK_ANONYMOUS_NO) {
            $facultyfeedbackcompleted = facultyfeedback_get_current_completed($facultyfeedback->id, false, $courseid);
        } else {
            $facultyfeedbackcompleted = false;
        }
        $params = array('id' => $completedid);
        $facultyfeedbackcompletedtmp = $DB->get_record('facultyfeedback_completedtmp', $params);
        //fake saving for switchrole
        $is_switchrole = facultyfeedback_check_is_switchrole();
        if ($is_switchrole) {
            $savereturn = 'saved';
            facultyfeedback_delete_completedtmp($completedid);
        } else {
            $new_completed_id = facultyfeedback_save_tmp_values($facultyfeedbackcompletedtmp,
                                                         $facultyfeedbackcompleted,
                                                         $userid);
            if ($new_completed_id) {
                $savereturn = 'saved';
                if ($facultyfeedback->anonymous == FEEDBACK_ANONYMOUS_NO) {
                    add_to_log($course->id,
                              'facultyfeedback',
                              'submit',
                              'view.php?id='.$cm->id,
                              $facultyfeedback->id,
                              $cm->id,
                              $userid);

                    facultyfeedback_send_email($cm, $facultyfeedback, $course, $userid);
                } else {
                    facultyfeedback_send_email_anonym($cm, $facultyfeedback, $course, $userid);
                }
                //tracking the submit
                $tracking = new stdClass();
                $tracking->userid = $USER->id;
                $tracking->facultyfeedback = $facultyfeedback->id;
                $tracking->completed = $new_completed_id;
                $DB->insert_record('facultyfeedback_tracking', $tracking);
                unset($SESSION->facultyfeedback->is_started);

                // Update completion state
                $completion = new completion_info($course);
                if ($completion->is_enabled($cm) && $facultyfeedback->completionsubmit) {
                    $completion->update_state($cm, COMPLETION_COMPLETE);
                }

            } else {
                $savereturn = 'failed';
            }
        }

    }


    if ($allbreaks = facultyfeedback_get_all_break_positions($facultyfeedback->id)) {
        if ($gopage <= 0) {
            $startposition = 0;
        } else {
            if (!isset($allbreaks[$gopage - 1])) {
                $gopage = count($allbreaks);
            }
            $startposition = $allbreaks[$gopage - 1];
        }
        $ispagebreak = true;
    } else {
        $startposition = 0;
        $newpage = 0;
        $ispagebreak = false;
    }

    //get the facultyfeedbackitems after the last shown pagebreak
    $select = 'facultyfeedback = ? AND position > ?';
    $params = array($facultyfeedback->id, $startposition);
    $facultyfeedbackitems = $DB->get_records_select('facultyfeedback_item', $select, $params, 'position');

    //get the first pagebreak
    $params = array('facultyfeedback' => $facultyfeedback->id, 'typ' => 'pagebreak');
    if ($pagebreaks = $DB->get_records('facultyfeedback_item', $params, 'position')) {
        $pagebreaks = array_values($pagebreaks);
        $firstpagebreak = $pagebreaks[0];
    } else {
        $firstpagebreak = false;
    }
    $maxitemcount = $DB->count_records('facultyfeedback_item', array('facultyfeedback'=>$facultyfeedback->id));

    //get the values of completeds before done. Anonymous user can not get these values.
    if ((!isset($SESSION->facultyfeedback->is_started)) AND
                          (!isset($savereturn)) AND
                          ($facultyfeedback->anonymous == FEEDBACK_ANONYMOUS_NO)) {

        $facultyfeedbackcompletedtmp = facultyfeedback_get_current_completed($facultyfeedback->id, true, $courseid);
        if (!$facultyfeedbackcompletedtmp) {
            $facultyfeedbackcompleted = facultyfeedback_get_current_completed($facultyfeedback->id, false, $courseid);
            if ($facultyfeedbackcompleted) {
                //copy the values to facultyfeedback_valuetmp create a completedtmp
                $facultyfeedbackcompletedtmp = facultyfeedback_set_tmp_values($facultyfeedbackcompleted);
            }
        }
    } else {
        $facultyfeedbackcompletedtmp = facultyfeedback_get_current_completed($facultyfeedback->id, true, $courseid);
    }

    /// Print the main part of the page
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    $analysisurl = new moodle_url('/mod/facultyfeedback/analysis.php', array('id'=>$id));
    if ($courseid > 0) {
        $analysisurl->param('courseid', $courseid);
    }
    echo $OUTPUT->heading(format_text($facultyfeedback->name));

    if ( (intval($facultyfeedback->publish_stats) == 1) AND
            ( has_capability('mod/facultyfeedback:viewanalysepage', $context)) AND
            !( has_capability('mod/facultyfeedback:viewreports', $context)) ) {

        $params = array('userid' => $USER->id, 'facultyfeedback' => $facultyfeedback->id);
        if ($multiple_count = $DB->count_records('facultyfeedback_tracking', $params)) {
            echo $OUTPUT->box_start('mdl-align');
            echo '<a href="'.$analysisurl->out().'">';
            echo get_string('completed_facultyfeedbacks', 'facultyfeedback').'</a>';
            echo $OUTPUT->box_end();
        }
    }

    if (isset($savereturn) && $savereturn == 'saved') {
        if ($facultyfeedback->page_after_submit) {

            require_once($CFG->libdir . '/filelib.php');

            $page_after_submit_output = file_rewrite_pluginfile_urls($facultyfeedback->page_after_submit,
                                                                    'pluginfile.php',
                                                                    $context->id,
                                                                    'mod_facultyfeedback',
                                                                    'page_after_submit',
                                                                    0);

            echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
            echo format_text($page_after_submit_output,
                             $facultyfeedback->page_after_submitformat,
                             array('overflowdiv' => true));
            echo $OUTPUT->box_end();
        } else {
            echo '<p align="center">';
            echo '<b><font color="green">';
            echo get_string('entries_saved', 'facultyfeedback');
            echo '</font></b>';
            echo '</p>';
            if ( intval($facultyfeedback->publish_stats) == 1) {
                echo '<p align="center"><a href="'.$analysisurl->out().'">';
                echo get_string('completed_facultyfeedbacks', 'facultyfeedback').'</a>';
                echo '</p>';
            }
        }

        if ($facultyfeedback->site_after_submit) {
            $url = facultyfeedback_encode_target_url($facultyfeedback->site_after_submit);
        } else {
            if ($courseid) {
                if ($courseid == SITEID) {
                    $url = $CFG->wwwroot;
                } else {
                    $url = $CFG->wwwroot.'/course/view.php?id='.$courseid;
                }
            } else {
                if ($course->id == SITEID) {
                    $url = $CFG->wwwroot;
                } else {
                    $url = $CFG->wwwroot.'/course/view.php?id='.$course->id;
                }
            }
        }
        echo $OUTPUT->continue_button($url);
    } else {
        if (isset($savereturn) && $savereturn == 'failed') {
            echo $OUTPUT->box_start('mform error');
            echo get_string('saving_failed', 'facultyfeedback');
            echo $OUTPUT->box_end();
        }

        if (isset($savereturn) && $savereturn == 'missing') {
            echo $OUTPUT->box_start('mform error');
            echo get_string('saving_failed_because_missing_or_false_values', 'facultyfeedback');
            echo $OUTPUT->box_end();
        }

        //print the items
        if (is_array($facultyfeedbackitems)) {
            echo $OUTPUT->box_start('facultyfeedback_form');
            echo '<form action="complete.php" method="post" onsubmit=" ">';
            echo '<fieldset>';
            echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
            echo $OUTPUT->box_start('facultyfeedback_anonymousinfo');
            switch ($facultyfeedback->anonymous) {
                case FEEDBACK_ANONYMOUS_YES:
                    echo '<input type="hidden" name="anonymous" value="1" />';
                    $inputvalue = 'value="'.FEEDBACK_ANONYMOUS_YES.'"';
                    echo '<input type="hidden" name="anonymous_response" '.$inputvalue.' />';
                   // echo get_string('mode', 'facultyfeedback').': '.get_string('anonymous', 'facultyfeedback');
		echo "<font size='3'color='red'>Note: This facultyfeedback does not collect any information about the User. Your facultyfeedback is being recorded in an anonymous mode.<br/>"."</font>";
                echo "</br>";   
                    break;
                case FEEDBACK_ANONYMOUS_NO:
                    echo '<input type="hidden" name="anonymous" value="0" />';
                    $inputvalue = 'value="'.FEEDBACK_ANONYMOUS_NO.'"';
                    echo '<input type="hidden" name="anonymous_response" '.$inputvalue.' />';
                  //  echo get_string('mode', 'facultyfeedback').': ';
                    //echo get_string('non_anonymous', 'facultyfeedback');
			echo "<font size='3'>". get_string('mode', 'facultyfeedback').': '.get_string('non_anonymous', 'facultyfeedback')."</font>";
                    break;
            }
            echo $OUTPUT->box_end();
            //check, if there exists required-elements
            $params = array('facultyfeedback' => $facultyfeedback->id, 'required' => 1);
            $countreq = $DB->count_records('facultyfeedback_item', $params);
            if ($countreq > 0) {
		echo "<br/><font size='3'>";echo '<span class="facultyfeedback_required_mark">(*)' . get_string('items_are_required', 'facultyfeedback') . '</span>';
                    echo "</font>"; 
               // echo '<span class="facultyfeedback_required_mark">(*)';
                //echo get_string('items_are_required', 'facultyfeedback');
                //echo '</span>';
            }
            echo $OUTPUT->box_start('facultyfeedback_items');

            unset($startitem);
            $select = 'facultyfeedback = ? AND hasvalue = 1 AND position < ?';
            $params = array($facultyfeedback->id, $startposition);
            $itemnr = $DB->count_records_select('facultyfeedback_item', $select, $params);
            $lastbreakposition = 0;
            $align = right_to_left() ? 'right' : 'left';

            foreach ($facultyfeedbackitems as $facultyfeedbackitem) {
                if (!isset($startitem)) {
                    //avoid showing double pagebreaks
                    if ($facultyfeedbackitem->typ == 'pagebreak') {
                        continue;
                    }
                    $startitem = $facultyfeedbackitem;
                }

                if ($facultyfeedbackitem->dependitem > 0) {
                    //chech if the conditions are ok
                    $fb_compare_value = facultyfeedback_compare_item_value($facultyfeedbackcompletedtmp->id,
                                                                    $facultyfeedbackitem->dependitem,
                                                                    $facultyfeedbackitem->dependvalue,
                                                                    true);
                    if (!isset($facultyfeedbackcompletedtmp->id) OR !$fb_compare_value) {
                        $lastitem = $facultyfeedbackitem;
                        $lastbreakposition = $facultyfeedbackitem->position;
                        continue;
                    }
                }

                if ($facultyfeedbackitem->dependitem > 0) {
                    $dependstyle = ' facultyfeedback_complete_depend';
                } else {
                    $dependstyle = '';
                }

                echo $OUTPUT->box_start('facultyfeedback_item_box_'.$align.$dependstyle);
                $value = '';
                //get the value
                $frmvaluename = $facultyfeedbackitem->typ . '_'. $facultyfeedbackitem->id;
                if (isset($savereturn)) {
                    $value = isset($formdata->{$frmvaluename}) ? $formdata->{$frmvaluename} : null;
                    $value = facultyfeedback_clean_input_value($facultyfeedbackitem, $value);
                } else {
                    if (isset($facultyfeedbackcompletedtmp->id)) {
                        $value = facultyfeedback_get_item_value($facultyfeedbackcompletedtmp->id,
                                                         $facultyfeedbackitem->id,
                                                         true);
                    }
                }
                if ($facultyfeedbackitem->hasvalue == 1 AND $facultyfeedback->autonumbering) {
                    $itemnr++;
                    echo $OUTPUT->box_start('facultyfeedback_item_number_'.$align);
                 //   echo $itemnr;
			echo "<font size='3'><b>".$itemnr."</b></font>.&nbsp;&nbsp;&nbsp;";	
                    echo $OUTPUT->box_end();
                }
                if ($facultyfeedbackitem->typ != 'pagebreak') {
                    echo $OUTPUT->box_start('box generalbox boxalign_'.$align);
                    facultyfeedback_print_item_complete($facultyfeedbackitem, $value, $highlightrequired);
                    echo $OUTPUT->box_end();
                }

                echo $OUTPUT->box_end();

                $lastbreakposition = $facultyfeedbackitem->position; //last item-pos (item or pagebreak)
                if ($facultyfeedbackitem->typ == 'pagebreak') {
                    break;
                } else {
                    $lastitem = $facultyfeedbackitem;
                }
            }
            echo $OUTPUT->box_end();
            echo '<input type="hidden" name="id" value="'.$id.'" />';
            echo '<input type="hidden" name="facultyfeedbackid" value="'.$facultyfeedback->id.'" />';
            echo '<input type="hidden" name="lastpage" value="'.$gopage.'" />';
            if (isset($facultyfeedbackcompletedtmp->id)) {
                $inputvalue = 'value="'.$facultyfeedbackcompletedtmp->id.'"';
            } else {
                $inputvalue = 'value=""';
            }
            echo '<input type="hidden" name="completedid" '.$inputvalue.' />';
            echo '<input type="hidden" name="courseid" value="'. $courseid . '" />';
            echo '<input type="hidden" name="preservevalues" value="1" />';
            if (isset($startitem)) {
                echo '<input type="hidden" name="startitempos" value="'.$startitem->position.'" />';
                echo '<input type="hidden" name="lastitempos" value="'.$lastitem->position.'" />';
            }

            if ( $ispagebreak AND $lastbreakposition > $firstpagebreak->position) {
                $inputvalue = 'value="'.get_string('previous_page', 'facultyfeedback').'"';
                echo '<input name="gopreviouspage" type="submit" '.$inputvalue.' />';
            }
            if ($lastbreakposition < $maxitemcount) {
                $inputvalue = 'value="'.get_string('next_page', 'facultyfeedback').'"';
                echo '<input name="gonextpage" type="submit" '.$inputvalue.' />';
            }
            if ($lastbreakposition >= $maxitemcount) { //last page
                $inputvalue = 'value="'.get_string('save_entries', 'facultyfeedback').'"';
                echo '<input name="savevalues" type="submit" '.$inputvalue.' />';
            }

            echo '</fieldset>';
            echo '</form>';
            echo $OUTPUT->box_end();

            echo $OUTPUT->box_start('facultyfeedback_complete_cancel');
            if ($courseid) {
                $action = 'action="'.$CFG->wwwroot.'/course/view.php?id='.$courseid.'"';
            } else {
                if ($course->id == SITEID) {
                    $action = 'action="'.$CFG->wwwroot.'"';
                } else {
                    $action = 'action="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'"';
                }
            }
            echo '<form '.$action.' method="post" onsubmit=" ">';
            echo '<fieldset>';
            echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
            echo '<input type="hidden" name="courseid" value="'. $courseid . '" />';
            echo '<button type="submit">'.get_string('cancel').'</button>';
            echo '</fieldset>';
            echo '</form>';
            echo $OUTPUT->box_end();
            $SESSION->facultyfeedback->is_started = true;
        }
    }
} else {
    echo $OUTPUT->box_start('generalbox boxaligncenter');
        echo '<h2>';
        echo '<font color="red">';
        echo get_string('this_facultyfeedback_is_already_submitted', 'facultyfeedback');
        echo '</font>';
        echo '</h2>';
        echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$course->id);
    echo $OUTPUT->box_end();
}
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

echo $OUTPUT->footer();
