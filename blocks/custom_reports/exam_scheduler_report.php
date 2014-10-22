<?php

require_once('../../config.php');
require_once($CFG->libdir.'/blocklib.php');
require_once($CFG->libdir.'/formslib.php');
require_once('./view_attendance_report_form.php');
require_once('../../mod/attforblock/locallib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

require_once('../../config.php');
require_once("../../mod/feedback/lib.php");
require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('./view_attendance_report_form.php');
require('../../mod/attforblock/tcpdf/config/lang/eng.php');
require('../../mod/attforblock/tcpdf/tcpdf.php');
require_once("../../mod/facultyfeedback/lib.php");
require_login($course->id);
//$categoryid = 2;
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
$headings = array('Teacher', 'Subject', 'Class', 'Average', 'Rating', 'Total Students', 'Submissions');
if ($categoryid == 2) {
    foreach ($cats as $cat) {
        $context = get_context_instance(CONTEXT_COURSECAT, $cat);
        has_capability('block/custom_reports:getfeedbackreport', $context);
    }
}
foreach ($cats as $cat) {
    $courses1 = get_courses($cat, '', 'c.id, c.fullname, c.startdate,c.credithours, c.idnumber, c.shortname');
    $courses = array_merge((array) $courses1, (array) $courses);
}

$report = get_string('exam_scheduler_report', 'block_custom_reports');
$navlinks[] = array('name' => get_string('exam_scheduler_report', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if (!$export)
    print_header('Exam Scheduler Report', 'Exam Scheduler Report', $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);

// if(!$export)
// print_header('Registration Report', 'Registration Report', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);

//if($courses = get_courses($categoryid, '', 'c.id, c.fullname, c.startdate, c.idnumber, c.shortname') or $export){
if (($courses && $categoryid == 2) or $export) {
	$mform = new mod_custom_reports_view_attendance_report_form('exam_scheduler_report.php', array('courses'=>$courses, 'categoryid'=>$categoryid,'report'=>$report));
	if($fromform = $mform->get_data() or $export){
		$cselected = array();
		if($export){
			$export_courses = required_param('courses',PARAM_SEQUENCE);
			//$sessions_margin = array_reverse(explode(",", required_param('sessions',PARAM_SEQUENCE)));

			//echo "select id, fullname, startdate, idnumber, shortname from {$CFG->prefix}courses where id IN ({$export_courses})";
			$courses = $DB->get_records_sql("select id, fullname, startdate, idnumber, shortname from {course} where id IN ({$export_courses})");

			//$export_courses_sessions = "";
		}else{

			$export_courses = "";
		}
		$temp="";
		foreach($courses as $course){

			if((!$export AND $fromform->{'c'.$course->id}=='true') OR ($export)){
				//new code insert by khyam 1/8/2011
				if(!$ccontext = get_context_instance(CONTEXT_COURSE, $course->id)){
					print_error('badcontext');
				}
				//@khyam: exclude the users with hidden role assignment.
					
				$hidden_users = $DB->get_records_select("role_assignments", "contextid = '$ccontext->id'");
				$hidden_role_assignment = "";
				foreach ($hidden_users as $hidden_user)
				$hidden_role_assignment .= $hidden_user->userid.", "; //List all users with hidden assignments.
				$hidden_role_assignment = rtrim($hidden_role_assignment, ", ");
				$query = "SELECT u.id, u.firstname, u.lastname, u.idnumber from mdl_user u
					   JOIN {$CFG->prefix}role_assignments ra ON ra.userid=u.id
					   JOIN {$CFG->prefix}role r ON ra.roleid = r.id
					   JOIN {$CFG->prefix}context c ON ra.contextid = c.id
					   where r.name = 'Student' and
					   c.contextlevel = 50 and
					   c.instanceid = {$course->id}";
                $query3 = "SELECT u.id, u.firstname, u.lastname, u.idnumber from mdl_user u
					   JOIN {$CFG->prefix}role_assignments ra ON ra.userid=u.id
					   JOIN {$CFG->prefix}role r ON ra.roleid = r.id
					   JOIN {$CFG->prefix}context c ON ra.contextid = c.id
					   where r.name = 'Teacher' and
					   c.contextlevel = 50 and
					   c.instanceid = {$course->id}";
				if($hidden_role_assignment != "")
				//   $query .= " and u.id NOT IN ({$hidden_role_assignment})";
				$query .= " order by u.firstname";

				//$cselected = array("id" => array(), "name" => array(), "students" => array());
				$cselected["id"][] = $course->id;
				$cselected["name"][] = $course->fullname;
					
				$cselected["shortname"][] = $course->shortname;
				$cselected["idnumber"][] =  $course->idnumber;
                $teacher = $DB->get_records_sql($query3);
				$teacher_id = "";
				$teacher_fullname = "";
				foreach($teacher as $t)
				{
					if($teacher_id != "")
						$teacher_id = $teacher_id.", ".$t->id;
					else
						$teacher_id = $t->id;
						
					if($teacher_fullname != "")
						$teacher_fullname = $teacher_fullname.", ".$t->firstname." ".$t->lastname;
					else
						$teacher_fullname = $t->firstname." ".$t->lastname;
				}
				
                $cselected["teachername"][] = $teacher_id;
                $cselected["teacherfullname"][] = $teacher_fullname;
				$cselected["students"][] =  $DB->get_records_sql($query);
				$cselected["startdate"][] = $course->startdate;
				if($export)
					$cselected["margin"][] = array_pop($sessions_margin);
				else
					$cselected["margin"][] = $fromform->{'session'.$course->id};

			}
			if($fromform->{'c'.$course->id}=='true' and !$export){
				$export_courses .= $course->id.",";
				//$export_courses_sessions .= $fromform->{'session'.$course->id}.",";
			}

		}
			
		//$table->width = '80%';
		//$table->tablealign =  'center';
		//$table->cellpadding = '5px';
		$table = new html_table();
		$table->head = array();
		//$table->align = array();
		//$table->size = array();
			
		$table->head[] = 'SNO';
		$table->align[] = 'center';
		$table->size[] = '';
			
		$table->head[] = 'STU_ID';
		$table->align[] = 'center';
		$table->size[] = '';
                
        $table->head[] = 'Instructor_COURSE_ID';
		$table->align[] = 'center';
		$table->size[] = '';
			
		$table->head[] = 'Instructor ID';
		$table->align[] = 'left';
		$table->size[] = '';
		
		$table->head[] = 'Instructor Name';
		$table->align[] = 'left';
		$table->size[] = '';
		
		$table->head[] = 'Course_ID';
		$table->align[] = 'left';
		$table->size[] = '';
		
		$table->head[] = 'Course_Name';
		$table->align[] = 'left';
		$table->size[] = '';
		
		$table->head[] = 'Course_Code';
		$table->align[] = 'left';
		$table->size[] = '';
		
		$table->head[] = 'STU_REG';
		$table->align[] = 'left';
		$table->size[] = '';
		
		$table->head[] = 'STU_NAME';
		$table->align[] = 'left';
		$table->size[] = '';
		
		//var_dump($students["name"]);
		$row_ite=0;
		for($j=0; $j<count($cselected['id']); $j++){
			// Get school - added by Qurrat-ul-ain Babar (21st Oct, 2014)
			$temp_course = $DB->get_record('course', array('id'=> $cselected['id'][$j]));
			$category = $temp_course->category;
			$semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
			$path = explode("/", $semester->path);
			$school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]"); 
			// end
			foreach($cselected["students"][$j] as $student){
				// Get reg status - added by Qurrat-ul-ain Babar (21st Oct, 2014)
				$record = $DB->get_record('user',array('id'=>$student->id));  
				profile_load_data($record);
				$regstatus = $record->profile_field_registrationstatus;	
				// end
				
				if($school->name == 'School of Electrical Engineering and Computer Science (SEECS)') {
					if($regstatus != 'not joined') {
						$table->data[$row_ite][] = $row_ite+1;
						$table->data[$row_ite][] = $student->id;
						$table->data[$row_ite][] = $cselected['teachername'][$j].'-'.$cselected['id'][$j];
						$table->data[$row_ite][] = $cselected['teachername'][$j];
						$table->data[$row_ite][] = $cselected['teacherfullname'][$j];
						$table->data[$row_ite][] = $cselected['id'][$j];
						$table->data[$row_ite][] = $cselected['name'][$j];
						
						$coursecode = explode(" ", $cselected['name'][$j]);
						$table->data[$row_ite][] = $coursecode[0];
						$table->data[$row_ite][] = $student->idnumber;
						$table->data[$row_ite][] = $student->firstname.' '.$student->lastname;
						$row_ite++;
					}
				}
				else {
					$table->data[$row_ite][] = $row_ite+1;
					$table->data[$row_ite][] = $student->id;
					$table->data[$row_ite][] = $cselected['teachername'][$j].'-'.$cselected['id'][$j];
					$table->data[$row_ite][] = $cselected['teachername'][$j];
					$table->data[$row_ite][] = $cselected['teacherfullname'][$j];
					$table->data[$row_ite][] = $cselected['id'][$j];
					$table->data[$row_ite][] = $cselected['name'][$j];
					
					$coursecode = explode(" ", $cselected['name'][$j]);
					$table->data[$row_ite][] = $coursecode[0];
					$table->data[$row_ite][] = $student->idnumber;
					$table->data[$row_ite][] = $student->firstname.' '.$student->lastname;
					$row_ite++;
				}
			}
		}
		
		if($export){
			$table->category = $category->name;
			$table->duration = date("d M Y", $cselected["startdate"][0]).' to '.date("d M Y", time('now'));
			ExportToExcel($table);
		}
		else{
			//print_r($cselected["id"]);
			echo '<div style="text-align: center; font-weight: bold;">Exam Scheduler Report </div>';
			echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">
							<form method="post" style="display: inline; margin: 0; padding: 0;">';
			echo 			'<input type="hidden" name="courses" value="'.rtrim($export_courses, ',').'" />';
			echo 			'<input type="hidden" name="sessions" value="'.rtrim($export_courses_sessions, ',').'" />';
			echo 			'<input type="hidden" name="id" value="'.$categoryid.'" />';
			echo 			'<input type="hidden" name="export" value="true" /><input type="submit" value="Download Excel" />
							</form>';

		//	if($categoryid!=-1)
		//	require_capability('block/custom_reports:getembaregreport', $context);

			echo html_writer::table($table);

		}
		exit();
	}else
	$mform->display();
}else{
	$OUTPUT->box_start('generalbox categorybox');
	echo '<form method="post" action="exam_scheduler_report.php?id=2" style="display: inline; margin: 0; padding: 0;">';
    print_whole_category_list2(NULL, NULL, NULL, -1, false);
    echo '<input type="submit" value="Select Courses" />';
    echo '</form>';
	$OUTPUT->box_end();
}
echo $OUTPUT->footer();

//================Export to Excel================//
function ExportToExcel($data) {
	global $CFG;

	//require_once("$CFG->libdir/excellib.class.php");/*
	require_once($CFG->dirroot.'/lib/excellib.class.php');
	$filename = "exam_scheduler_report:.xls";

	$workbook = new MoodleExcelWorkbook("-");
	/// Sending HTTP headers
	ob_clean();
	$workbook->send($filename);
	/// Creating the first worksheet
	$myxls =& $workbook->add_worksheet('exam_scheduler_report');
	/// format types
	$formatbc =& $workbook->add_format();
	$formatbc->set_bold(1);

	$header1 =& $workbook->add_format();
	$header1->set_bold(1);          // Make it bold
	$header1->set_align('center');  // Align text to center
	$header1->set_size(14);
	//$header1->set_fg_color(22);

	$header2 =& $workbook->add_format();
	$header2->set_bold(1);            // Make it bold
	$header2->set_align('center');  // Align text to center
	$header2->set_size(12);
	//$header2->set_fg_color(23);

	$normal =& $workbook->add_format();
	$normal->set_bold(0);
	$normal->set_align('center');
	$normal->set_size(10);

	$name =& $workbook->add_format();
	$name->set_bold(0);
	$name->set_size(10);

	$grey_code_f =& $workbook->add_format();
	$grey_code_f->set_bold(0);            // Make it bold
	$grey_code_f->set_size(12);
	$grey_code_f->set_fg_color(22);
	$grey_code_f->set_align('center');

	//$formatbc->set_size(14);
	$myxls->write(1, 2, "Course Registration Report",$header1);
	//$myxls->write(4, 0, 'Duration');
	//$myxls->write(4, 1, $data->duration, $formatbc);

	$i = 6;
	$j = 0;

	foreach ($data->head as $heading){
		$heading = str_replace('<br/>','',$heading);
		$heading = trim($heading);
		$myxls->write_string($i, $j, $heading,$header2);
		// $myxls->set_column($pos,$pos,(strlen($grade_item->get_name()))+4);
		$col_size = strlen($heading);
		$col_size+=6;

		if(preg_match('/^NAME/i',$heading)){
			$col_size=20;
		}
		$myxls->set_column($j,$j,$col_size);
		$j++;
	}
	//$myxls->merge_cells(1,0,1,$j-1);
	$myxls->merge_cells(2,0,2,$j-1);
	$myxls->merge_cells(4,1,4,3);
	$myxls->set_row(1, 25 );
	$i = 7;
	$j = 0;
	foreach ($data->data as $row) {
		foreach ($row as $cell) {
			//$myxls->write($i, $j++, $cell);
			if (is_numeric($cell)) {
				//if($cell>25.99){
				//	$myxls->write_number($i, $j++, $cell,$grey_code_f);
				//}
				//else{
				$myxls->write_number($i, $j++, $cell,$normal);
				//}
			} else {
				if(preg_match('/^!!/',$cell)){
					$cell = str_replace("!!",'',$cell);
					$myxls->write_string($i, $j++, $cell,$grey_code_f);
				}
				else{
					if($j==2){
						$myxls->write_string($i, $j++, $cell,$name);
					}
					else{
						$myxls->write_string($i, $j++, $cell,$normal);
					}
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
function print_whole_category_list2($category=NULL, $displaylist=NULL, $parentslist=NULL, $depth=-1, $showcourses = true) {
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
function print_category_info2($category, $depth=0, $showcourses = false) {
	/* global $CFG, $DB, $OUTPUT;

	$strsummary = get_string('summary');

	$catlinkcss = null;
	if (!$category->visible) {
		$catlinkcss = array('class'=>'dimmed');
	}
	static $coursecount = null;
	if (null === $coursecount) {
		// only need to check this once
		$coursecount = $DB->count_records('course') <= FRONTPAGECOURSELIMIT;
	}

	if ($showcourses and $coursecount) {
		$catimage = '<img src="'.$OUTPUT->pix_url('i/course') . '" alt="" />';
	} else {
		$catimage = "&nbsp;";
	}

	$courses = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.summary');
	if ($showcourses and $coursecount) {
		echo '<div class="categorylist clearfix '.$depth.'">';
		$cat = '';
		$cat .= html_writer::tag('div', $catimage, array('class'=>'image'));
		$catlink = html_writer::link(new moodle_url('', array('id'=>$category->id)), format_string($category->name), $catlinkcss);
		$cat .= html_writer::tag('div', $catlink, array('class'=>'name'));

		$html = '';
		if ($depth > 0) {
			for ($i=0; $i< $depth; $i++) {
				$html = html_writer::tag('div', $html . $cat, array('class'=>'indentation'));
				$cat = '';
			}
		} else {
			$html = $cat;
		}
		echo html_writer::tag('div', $html, array('class'=>'category'));
		echo html_writer::tag('div', '', array('class'=>'clearfloat'));

		// does the depth exceed maxcategorydepth
		// maxcategorydepth == 0 or unset meant no limit
		$limit = !(isset($CFG->maxcategorydepth) && ($depth >= $CFG->maxcategorydepth-1));
		if ($courses && ($limit || $CFG->maxcategorydepth == 0)) {
			foreach ($courses as $course) {
				$linkcss = null;
				if (!$course->visible) {
					$linkcss = array('class'=>'dimmed');
				}

				$courselink = html_writer::link(new moodle_url('/course/view.php', array('id'=>$course->id)), format_string($course->fullname), $linkcss);

				// print enrol info
				$courseicon = '';
				if ($icons = enrol_get_course_info_icons($course)) {
					foreach ($icons as $pix_icon) {
						$courseicon = $OUTPUT->render($pix_icon).' ';
					}
				}

				$coursecontent = html_writer::tag('div', $courseicon.$courselink, array('class'=>'name'));

				if ($course->summary) {
					$link = new moodle_url('/course/info.php?id='.$course->id);
					$actionlink = $OUTPUT->action_link($link, '<img alt="'.$strsummary.'" src="'.$OUTPUT->pix_url('i/info') . '" />',
					new popup_action('click', $link, 'courseinfo', array('height' => 400, 'width' => 500)),
					array('title'=>$strsummary));

					$coursecontent .= html_writer::tag('div', $actionlink, array('class'=>'info'));
				}

				$html = '';
				for ($i=0; $i <= $depth; $i++) {
					$html = html_writer::tag('div', $html . $coursecontent , array('class'=>'indentation'));
					$coursecontent = '';
				}
				echo html_writer::tag('div', $html, array('class'=>'course clearfloat'));
			}
		}
		echo '</div>';
	} else {
		echo '<div class="categorylist level'.$depth.'">';
		$html = '';
		$cat = html_writer::link(new moodle_url('', array('id'=>$category->id)), format_string($category->name), $catlinkcss);
		$cat .= html_writer::tag('span', ' ('.count($courses).')', array('title'=>get_string('numberofcourses'), 'class'=>'numberofcourse'));

		if ($depth > 0) {
			for ($i=0; $i< $depth; $i++) {
				//$html = html_writer::tag('div', $html .$cat, array('class'=>'indentation'));
				$html = html_writer::tag('div', $html .$cat, array('class'=>'indentation level'.$i ));
				$cat = '';
			}
		} else {
			$html = $cat;
		}

		echo html_writer::tag('div', $html, array('class'=>'category'));
		echo html_writer::tag('div', '', array('class'=>'clearfloat', 'style'=>'clear: both;'));
		echo '</div>';
	} */
	
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

////////////
function make_categories_list2(&$list, &$parents, $requiredcapability = '',
$excludeid = 0, $category = NULL, $path = "") {

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

			$path = $path.' / '.format_string($category->name);
		} else {

			$path = format_string($category->name);
		}

		// Add this category to $list, if the permissions check out.
		if (empty($requiredcapability)) {
			$list[$category->id] = $path;


		} else {
			ensure_context_subobj_present($category, CONTEXT_COURSECAT);
			$requiredcapability = (array)$requiredcapability;

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
					$parents[$cat->id]   = $parents[$category->id];
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
