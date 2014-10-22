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
require_once('./view_attendance_report_form.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_login($course->id);

$categoryid = optional_param('id', '-1', PARAM_INT);
$export = optional_param('export', false, PARAM_BOOL);
$sort = optional_param('sort', false, PARAM_BOOL);
$sortby = $_POST[sortby];
$perd = $_POST[perod];
$sortorder = $_POST['sortorder'];
$cats = $_POST[catg];

if ($cats) {
    $_SESSION['cats'] = $cats;
}

$cats = $_SESSION['cats'];
$headings=array('S/No:','Registration Number','Name','Course');
if ($categoryid == 2) {
    foreach ($cats as $cat) {
        $context = get_context_instance(CONTEXT_COURSECAT, $cat);
        require_capability('block/custom_reports:getstudentsList', $context);
    }
}
foreach ($cats as $cat) {
    $courses1 = get_courses($cat, '', 'c.id, c.fullname, c.startdate,c.credithours, c.idnumber, c.shortname');
    $courses = array_merge((array) $courses1, (array) $courses);
}

$report = get_string('students_penalty', 'block_custom_reports');
$navlinks[] = array('name' => get_string('students_penalty', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if (!$export)
    print_header('Students Penalty List', 'Students Penalty List', $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);

if (($courses && $categoryid == 2) or $export) {
    $mform = new mod_custom_reports_view_attendance_report_form('students_penalty_list.php', array('courses' => $courses, 'categoryid' => $categoryid, 'report' => $report));
    if ($fromform = $mform->get_data() or $export or $sort) {
        if ($export or $sort) {
            $export_courses = required_param('courses',PARAM_SEQUENCE);
            $courses = $DB->get_records_sql("select id, fullname,credithours ,startdate, idnumber, shortname from {course} where id IN ({$export_courses})");
            $feedback_no = $_SESSION['feedbacktype'];
        } else {
            $export_courses = "";
            $feedback_no = $fromform->feedbacktype;
            $_SESSION['feedbacktype'] = $feedback_no;
        }

        $table = new html_table();
        $table->head = array();

        $table->head[] = 'S.No';
        $table->align[] = 'center';
        $table->size[] = '';
        $table->headspan[] = 1;


        $table->head[] = 'Regisration No.';
        $table->align[] = 'center';
        $table->size[] = '';
        $table->headspan[] = 1;

        $table->head[] = 'Name';
        $table->align[] = 'center';
        $table->size[] = '';
        $table->headspan[] = 1;

        $table->head[] = 'Subject';
        $table->align[] = 'left';
        $table->size[] = '';
        $table->headspan[] = 1;




        $i = 1;

        ///start
        if (!$export) {
            foreach ($courses as $course) {

                if ($fromform->{'c' . $course->id} == 'true' and !$export)
                    $courses1 .= $course->id . ",";
            }

            $courses1 = rtrim($courses1, ",");
            $courses1 = ltrim($courses1, "Array");
        }
        if ($export) {
            $courses1=$export_courses;
        }

        $sql = "SELECT u.id, username,firstname,lastname,idnumber
		FROM mdl_user u
		JOIN mdl_role_assignments ra ON ra.userid = u.id
		JOIN mdl_role r ON ra.roleid = r.id
		JOIN mdl_context c ON ra.contextid = c.id
		WHERE r.name = 'Student'
		AND c.contextlevel =50
		AND c.instanceid
		IN (	
		$courses1
		)
		GROUP BY username";

        $users = $DB->get_records_sql($sql);
        ////
        $sno = 1;
		
        foreach ($users as $user) {
            $user_courses = "";
			// Get reg status - added by Qurrat-ul-ain Babar (21st Oct, 2014)
			$record = $DB->get_record('user',array('id'=>$student->id));  
			profile_load_data($record);
			$regstatus = $record->profile_field_registrationstatus;	
			// end

            foreach ($courses as $course) {
				// Get school - added by Qurrat-ul-ain Babar (21st Oct, 2014)
				$temp_course = $DB->get_record('course', array('id'=> $course->id));
				$category = $temp_course->category;
				$semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
				$path = explode("/", $semester->path);
				$school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]"); 
				// end
                if ((!$export AND $fromform->{'c' . $course->id} == 'true') OR ($export OR $sort)) {
                    ///
                    $context = get_context_instance(CONTEXT_COURSE, $course->id, MUST_EXIST);
                    $sql = " SELECT ra.userid FROM mdl_role_assignments ra WHERE ra.roleid =5 AND ra.userid=$user->id and contextid =$context->id ";

                    if ($student = $DB->get_records_sql($sql)) {
                        // $feedbacks = $DB->get_records_sql("SELECT id,name from {feedback} f WHERE course =$course->id and name like'$feedback_no%'");
						// Query updated to generate penalty list for Student Course Evaluation  - updated bu Qurrat-ul-ain Babar (8th May, 2014)
						$feedbacks = $DB->get_records_sql("SELECT id,name from {feedback} f WHERE course =$course->id AND name LIKE '%$feedback_no%'");
						
                        // if(isset($feedback->id)){
                        foreach ($feedbacks as $feedback) {
                            $userfeedback = $DB->get_record_sql("SELECT * FROM {feedback_completed} where feedback = $feedback->id and userid=$user->id");
                            if (!isset($userfeedback->id)) {

                                if ($user_courses == "") {
                                    $user_courses.=$course->fullname . "-<b>(" . $feedback->name . ")</b>";
                                } else {
                                    $user_courses.=" , " . $course->fullname . "-<b>(" . $feedback->name . ")</b>";
                                }
                            }
                        }
                    }
                }
            }
            if ($user_courses != "") {
                $table->data[$j][] = $sno++;
                $table->data[$j][] = $user->idnumber;
                $table->data[$j][] = $user->firstname . " " . $user->lastname;
                $table->data[$j][] = $user_courses;
                $j++;
            }
        }
        // end
		
        foreach ($courses as $course) {
            if ((!$export AND $fromform->{'c' . $course->id} == 'true') OR ($export OR $sort)) {
                $startdate = $course->startdate;
                $context = get_context_instance(CONTEXT_COURSE, $course->id);
                $i++;
            }
            if ($fromform->{'c' . $course->id} == 'true' and !$export)
                $export_courses .= $course->id . ",";
        }

        if ($export) {
            ExportToExcel($table, $perd, $weeks);
        } else {

            echo '<div style="text-align: center; font-weight: bold;">Students Penalty List<br></div>';

            echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">';



            echo '<input type="hidden" name="courses" value="' . rtrim($export_courses, ',') . '" />';
            echo '<input type="hidden" name="id" value="' . $categoryid . '" />';

            echo '<form method="post" style="display: inline; margin: 0; padding: 0;">';
            echo '<input type="hidden" name="courses" value="' . rtrim($export_courses, ',') . '" />';
            echo '<input type="hidden" name="id" value="' . $categoryid . '" />';

            echo '<input type="hidden" name="export" value="true" /><input type="submit" value="Download Excel" />';
            echo html_writer::table($table);
            echo '</form></div>';
        }
        exit();
    }else
        $mform->display();
}else {
    $OUTPUT->box_start('generalbox categorybox');
    echo '<form method="post" action="students_penalty_list.php?id=2" style="display: inline; margin: 0; padding: 0;">';
    print_whole_category_list2(NULL, NULL, NULL, -1, false);
    echo '<input type="submit" value="Select Courses" />';
    echo '</form>';
    $OUTPUT->box_end();
}
echo $OUTPUT->footer();

//================Export to Excel================//
function ExportToExcel($data, $name, $type) {
    global $CFG;
    global $headings;
    global $name, $type;

    //require_once("$CFG->libdir/excellib.class.php");/*
    require_once($CFG->dirroot . '/lib/excellib.class.php');
    $filename = "Students_Penalty_List.xls";

    $workbook = new MoodleExcelWorkbook("-");
    /// Sending HTTP headers
    ob_clean();
    $workbook->send($filename);
    /// Creating the first worksheet
    $myxls = & $workbook->add_worksheet('Students Penalty List');
    /// format types
    $formatbc = & $workbook->add_format();
    $formatbc1 = & $workbook->add_format();
    $formatbc->set_bold(1);
    $myxls->set_column(0, 0, 10);
    $myxls->set_column(1, 2, 30);
    $myxls->set_column(3, 3, 500);
    $formatbc->set_align('center');
    $formatbc1->set_align('center');
    $xlsFormats = new stdClass();
    $xlsFormats->default = $workbook->add_format(array(
        'width' => 40));
    //$formatbc->set_size(14);
    $myxls->write(0, 1, "Students Penalty List", $formatbc);
    


    foreach ($headings as $heading)
        $myxls->write_string(2, $j++, strtoupper($heading), $formatbc);

    $i = 3;
    $j = 0;
    foreach ($data->data as $row) {
        foreach ($row as $cell) {

            if (is_numeric($cell)) {
                if (strstr($cell, "<b>") == true) {
                    $myxls->write_number($i, $j++, strip_tags($cell), $formatbc1);
                } else {
                    $myxls->write_number($i, $j++, strip_tags($cell), $formatbc1);
                }
            } else {
                if (strstr($cell, "<b>") == true) {
                    $myxls->write_string($i, $j++, strip_tags($cell), $formatbc1);
                } else {
                    $myxls->write_string($i, $j++, strip_tags($cell), $formatbc1);
                }
            }
        }
        $i++;
        $j = 0;
    }
    $workbook->close();
    exit;
}

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
