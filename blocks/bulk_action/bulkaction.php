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
 * @package   block-custom_reports
 * @copyright 2012 Hina Yousuf
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('./view_bulk_action_form.php');
require_login($course->id);
$categoryid = optional_param('id', '-1', PARAM_INT);
$cats = $_POST[catg];
if ($cats) {
    $_SESSION['cats'] = $cats;
}
$cats = $_SESSION['cats'];

if ($categoryid == 2) {
    foreach ($cats as $cat) {
        $context = get_context_instance(CONTEXT_COURSECAT, $cat);
        require_capability('block/bulk_action:performbulkaction', $context);
    }
}
foreach ($cats as $cat) {
    $courses1 = get_courses($cat, '', 'c.id, c.fullname, c.startdate,c.credithours, c.idnumber, c.shortname');
    $courses = array_merge((array) $courses1, (array) $courses);
}
 $count=false;

$report = get_string('bulk_action', 'block_bulk_action');
$navlinks[] = array('name' => get_string('bulk_action', 'block_bulk_action'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
print_header('Bulk Settings', 'Bulk Settings', $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);

if (($courses && $categoryid == 2) or $export or $sort) {
    $mform = new mod_bulk_action_form('bulkaction.php', array('courses' => $courses, 'categoryid' => $categoryid, 'report' => $report));
    if ($fromform = $mform->get_data()) {

        foreach ($courses as $course) {
            if (( $fromform->{'c' . $course->id} == 'true')) {
		$count=true;
                if($fromform->feedbacktype == "Faculty Course Overview Report"){
                $facultyfeedbacks = $DB->get_records_sql("select * from {facultyfeedback} where name like '%$fromform->feedbacktype%' and course=$course->id ");
                foreach ($facultyfeedbacks as $facultyfeedback) {
                    if (empty($fromform->openenable)) {
                        $facultyfeedback->timeopen = $facultyfeedback->timeopen;
                    } else {
                        $facultyfeedback->timeopen = $fromform->timeopen;
                    }
                    if (empty($fromform->closeenable)) {
                        $facultyfeedback->timeclose = $facultyfeedback->timeclose;
                    } else {
                        $facultyfeedback->timeclose = $fromform->timeclose;
                    }
                    $facultyfeedback->timemodified = time();
                    $feedbackid = $DB->update_record("facultyfeedback", $facultyfeedback);      
                }
                }
                else{
                $feedbacks = $DB->get_records_sql("select * from {feedback} where name like '%$fromform->feedbacktype%' and course=$course->id ");
                foreach ($feedbacks as $feedback) {
                    if (empty($fromform->openenable)) {
                        $feedback->timeopen = $feedback->timeopen;
                    } else {
                        $feedback->timeopen = $fromform->timeopen;
                    }
                    if (empty($fromform->closeenable)) {
                        $feedback->timeclose = $feedback->timeclose;
                    } else {
                        $feedback->timeclose = $fromform->timeclose;
                    }
                    $feedback->timemodified = time();
                    $feedbackid = $DB->update_record("feedback", $feedback);
                }
            }
            }
        }
	 if($count==false){
             echo '<div style="text-align: center; font-weight: bold;">Please select courses<br></div>';
        }
        if (empty($fromform->openenable) && empty($fromform->closeenable))  {
             echo '<div style="text-align: center; font-weight: bold;">You should select the feedback opening or closing date<br></div>';
        }
        if ($feedbackid == 1) {
            echo '<div style="text-align: center; font-weight: bold;">Settings updated successfully<br></div>';

            echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">';
        }


        exit();
    }else
        $mform->display();
}else {
    $OUTPUT->box_start('generalbox categorybox');
    echo '<form method="post" action="bulkaction.php?id=2" style="display: inline; margin: 0; padding: 0;">';
    print_whole_category_list2(NULL, NULL, NULL, -1, false);
    echo '<input type="submit" value="Select Courses" />';
    echo '</form>';
    $OUTPUT->box_end();
}
echo $OUTPUT->footer();

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function print_whole_category_list2($category = NULL, $displaylist = NULL, $parentslist = NULL, $depth = -1, $showcourses = true) {
    global $CFG;

    // maxcategorydepth == 0 meant no limit
    if (!empty($CFG->maxcategorydepth) && $depth >= $CFG->maxcategorydepth) {
        return;
    }

    if (!$displaylist) {
        make_categories_list2($displaylist, $parentslist);
    }

    if ($category) {
        if ($category->visible or has_capability('moodle/category:viewhiddencategories', get_context_instance(CONTEXT_SYSTEM))) {
            print_category_info2($category, $depth, $showcourses);
        } else {
            return;  // Don't bother printing children of invisible categories
        }
    } else {
        $category->id = "0";
    }

    if ($categories = get_child_categories2($category->id)) {   // Print all the children recursively
        $countcats = count($categories);
        $count = 0;
        $first = true;
        $last = false;
        foreach ($categories as $cat) {
            $count++;
            if ($count == $countcats) {
                $last = true;
            }
            $up = $first ? false : true;
            $down = $last ? false : true;
            $first = false;

            print_whole_category_list2($cat, $displaylist, $parentslist, $depth + 1, $showcourses);
        }
    }
}

//////////////////////////////////////////////
function print_category_info2($category, $depth = 0, $showcourses = false) {
    global $CFG, $DB, $OUTPUT;

    $strsummary = get_string('summary');

    $catlinkcss = null;
    if (!$category->visible) {
        $catlinkcss = array('class' => 'dimmed');
    }
    static $coursecount = null;
    if (null === $coursecount) {
        // only need to check this once
        $coursecount = $DB->count_records('course') <= FRONTPAGECOURSELIMIT;
    }

    if ($showcourses and $coursecount) {
        $catimage = '<img src="' . $OUTPUT->pix_url('i/course') . '" alt="" />';
    } else {
        $catimage = "&nbsp;";
    }

    $courses = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.summary');
    if ($showcourses and $coursecount) {
        echo '<div class="categorylist clearfix ' . $depth . '">';
        $cat = '';
        $cat .= html_writer::tag('div', $catimage, array('class' => 'image'));
        $catlink = html_writer::link(new moodle_url('', array('id' => $category->id)), format_string($category->name), $catlinkcss);
        $cat .= html_writer::tag('div', $catlink, array('class' => 'name'));

        $html = '';
        if ($depth > 0) {
            for ($i = 0; $i < $depth; $i++) {
                $html = html_writer::tag('div', $html . $cat, array('class' => 'indentation'));
                $cat = '';
            }
        } else {
            $html = $cat;
        }
        echo html_writer::tag('div', $html, array('class' => 'category'));
        echo html_writer::tag('div', '', array('class' => 'clearfloat'));

        // does the depth exceed maxcategorydepth
        // maxcategorydepth == 0 or unset meant no limit
        $limit = !(isset($CFG->maxcategorydepth) && ($depth >= $CFG->maxcategorydepth - 1));
        if ($courses && ($limit || $CFG->maxcategorydepth == 0)) {
            foreach ($courses as $course) {
                $linkcss = null;
                if (!$course->visible) {
                    $linkcss = array('class' => 'dimmed');
                }

                $courselink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), format_string($course->fullname), $linkcss);

                // print enrol info
                $courseicon = '';
                if ($icons = enrol_get_course_info_icons($course)) {
                    foreach ($icons as $pix_icon) {
                        $courseicon = $OUTPUT->render($pix_icon) . ' ';
                    }
                }

                $coursecontent = html_writer::tag('div', $courseicon . $courselink, array('class' => 'name'));

                if ($course->summary) {
                    $link = new moodle_url('/course/info.php?id=' . $course->id);
                    $actionlink = $OUTPUT->action_link($link, '<img alt="' . $strsummary . '" src="' . $OUTPUT->pix_url('i/info') . '" />', new popup_action('click', $link, 'courseinfo', array('height' => 400, 'width' => 500)), array('title' => $strsummary));

                    $coursecontent .= html_writer::tag('div', $actionlink, array('class' => 'info'));
                }

                $html = '';
                for ($i = 0; $i <= $depth; $i++) {
                    $html = html_writer::tag('div', $html . $coursecontent, array('class' => 'indentation'));
                    $coursecontent = '';
                }
                echo html_writer::tag('div', $html, array('class' => 'course clearfloat'));
            }
        }
        echo '</div>';
    } else {
        echo '<div class="categorylist level' . $depth . '">';
        $html = '';
        if (count($courses) > 0) {
            $cat = '<input name="catg[]" type="checkbox" value="' . $category->id . '"/>' . $category->name;
            $cat .= html_writer::tag('span', ' (' . count($courses) . ')', array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse'));
        } else {
            $cat = $category->name; //html_writer::link(new moodle_url('', array('id'=>$category->id)), format_string($category->name), $catlinkcss);
            $cat .= html_writer::tag('span', ' (' . count($courses) . ')', array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse'));
        }
        if ($depth > 0) {
            for ($i = 0; $i < $depth; $i++) {
                //$html = html_writer::tag('div', $html .$cat, array('class'=>'indentation'));
                $html = html_writer::tag('div', $html . $cat, array('class' => 'indentation level' . $i));
                $cat = '';
            }
        } else {
            $html = $cat;
        }

        echo html_writer::tag('div', $html, array('class' => 'category'));
        echo html_writer::tag('div', '', array('class' => 'clearfloat', 'style' => 'clear: both;'));
        echo '</div>';
    }
}

function make_categories_list2(&$list, &$parents, $requiredcapability = '', $excludeid = 0, $category = NULL, $path = "") {

    // initialize the arrays if needed
    if (!is_array($list)) {
        $list = array();
    }
    if (!is_array($parents)) {
        $parents = array();
    }

    if (empty($category)) {
        // Start at the top level.
        $category = new stdClass;
        $category->id = 0;
    } else {

        // This is the excluded category, don't include it.
        if ($excludeid > 0 && $excludeid == $category->id) {

            return;
        }

        // Update $path.
        if ($path) {

            $path = $path . ' / ' . format_string($category->name);
        } else {

            $path = format_string($category->name);
        }

        // Add this category to $list, if the permissions check out.
        if (empty($requiredcapability)) {
            $list[$category->id] = $path;
        } else {
            ensure_context_subobj_present($category, CONTEXT_COURSECAT);
            $requiredcapability = (array) $requiredcapability;

            if (has_all_capabilities($requiredcapability, $category->context)) {

                $list[$category->id] = $path;
            }
        }
    }

    // Add all the children recursively, while updating the parents array.
    if ($categories = get_child_categories2($category->id)) {

        foreach ($categories as $cat) {
            if (!empty($category->id)) {
                if (isset($parents[$category->id])) {
                    $parents[$cat->id] = $parents[$category->id];
                }
                $parents[$cat->id][] = $category->id;
            }
            make_categories_list2($list, $parents, $requiredcapability, $excludeid, $cat, $path);
        }
    }
}

///////////////
function get_child_categories2($parentid) {

    static $allcategories = null;

    // only fill in this variable the first time
    if (null == $allcategories) {
        $allcategories = array();

        $categories = get_categories();

        foreach ($categories as $category) {
            if (empty($allcategories[$category->parent])) {

                $allcategories[$category->parent] = array();
            }

            $allcategories[$category->parent][] = $category;
        }
    }

    if (empty($allcategories[$parentid])) {

        return array();
    } else {

        return $allcategories[$parentid];
    }
}

?>
