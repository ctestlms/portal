<script type="text/javascript" src="jquery-1.3.2.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
			
        //if(document.getElementById('verify').clicked == true){
			
        //alert("hina");
        //}
    });
    function getXMLHttp()
    {
        var xmlHttp

        try
        {
            //Firefox, Opera 8.0+, Safari
            xmlHttp = new XMLHttpRequest();
        }
        catch(e)
        {
            //Internet Explorer
            try
            {
                xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch(e)
            {
                try
                {
                    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch(e)
                {
                    alert("Your browser does not support AJAX!")
                    return false;
                }
            }
        }
        return xmlHttp;
    }
    function verifyresult()
    {
        var xmlHttp = getXMLHttp();
			 
        xmlHttp.onreadystatechange = function()
        {
            if(xmlHttp.readyState == 4)
            {
                HandleResponse(xmlHttp.responseText);
            }
        }

        xmlHttp.open("GET", "VerifyGrades.php", true);
        xmlHttp.send(null);
        //alert("hina");
			
    }
    function HandleResponse(response)
    {
        document.getElementById('verified').innerHTML = "<b>Semester Result has been verfied.</b>";
        document.getElementById("verify").disabled = true;
    }
</script>



<?php
require_once('../../config.php');
require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('./view_attendance_report_form.php');
require_once('../../mod/attforblock/locallib.php');
require($CFG->dirroot . '/mod/attforblock/tcpdf/config/lang/eng.php');
require($CFG->dirroot . '/mod/attforblock/tcpdf/tcpdf.php');


require_login($course->id);
session_start();
$categoryid = optional_param('id', '-1', PARAM_INT);
if ($categoryid != -1) {
    $semester = $DB->get_record_sql("select name,path from {course_categories} where id=$categoryid");
    $path = explode("/", $semester->path);
    $semester = explode(" ", $semester->name);
    print_r($path);
    $semester = $semester[0];

    $school = $DB->get_record_sql("select name from {course_categories} where id=$path[1]");
    $degree = $DB->get_record_sql("select name from {course_categories} where id=$path[3]");
}
$export = optional_param('export', false, PARAM_BOOL);

$context = get_context_instance(CONTEXT_COURSECAT, $categoryid);



if ($categoryid != -1)
    require_capability('block/custom_reports:getresults', $context);

$navlinks[] = array('name' => get_string('results', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if (!$export)
    print_header('Semester Result', 'Semester Result', $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);

if ($courses = get_courses($categoryid, '', 'c.id, c.fullname, c.startdate, c.idnumber, c.shortname') or $export) {
    $mform = new mod_custom_reports_view_attendance_report_form('summarized_results.php', array('courses' => $courses, 'categoryid' => $categoryid));
    if ($fromform = $mform->get_data() or $export) {
        $cselected = array();
        if ($export) {
            $export_courses = required_param('courses', PARAM_SEQUENCE);
            $sessions_margin = array_reverse(explode(",", required_param('sessions', PARAM_SEQUENCE)));

            //echo "select id, fullname, startdate, idnumber, shortname from {$CFG->prefix}courses where id IN ({$export_courses})";
            $courses = $DB->get_records_sql("select id, fullname, startdate, idnumber, shortname from {course} where id IN ({$export_courses})");

            $export_courses_sessions = "";
        } else {

            $export_courses = "";
        }
        $temp = "";
        foreach ($courses as $course) {

            if ((!$export AND $fromform->{'c' . $course->id} == 'true') OR ($export)) {
                //new code insert by khyam 1/8/2011
                if (!$ccontext = get_context_instance(CONTEXT_COURSE, $course->id)) {
                    print_error('badcontext');
                }
                //@khyam: exclude the users with hidden role assignment.

                $hidden_users = $DB->get_records_select("role_assignments", "contextid = '$ccontext->id'");
                $hidden_role_assignment = "";
                foreach ($hidden_users as $hidden_user)
                    $hidden_role_assignment .= $hidden_user->userid . ", "; //List all users with hidden assignments.
                $hidden_role_assignment = rtrim($hidden_role_assignment, ", ");
                $query = "SELECT u.id, u.firstname, u.lastname, u.idnumber from mdl_user u
                                                               JOIN {$CFG->prefix}role_assignments ra ON ra.userid=u.id
                                                               JOIN {$CFG->prefix}role r ON ra.roleid = r.id
                                                               JOIN {$CFG->prefix}context c ON ra.contextid = c.id
                                                               where r.name = 'Student' and
                                                               c.contextlevel = 50 and
                                                               c.instanceid = {$course->id}";
                if ($hidden_role_assignment != "")
                //   $query .= " and u.id NOT IN ({$hidden_role_assignment})";
                    $query .= " order by u.firstname";

                //$cselected = array("id" => array(), "name" => array(), "students" => array());
                $cselected["id"][] = $course->id;
                $cselected["name"][] = $course->fullname;

                $cselected["shortname"][] = $course->shortname;
                $cselected["idnumber"][] = $course->idnumber;
                $cselected["students"][] = $DB->get_records_sql($query);
                $cselected["startdate"][] = $course->startdate;
                if ($export)
                    $cselected["margin"][] = array_pop($sessions_margin);
                else
                    $cselected["margin"][] = $fromform->{'session' . $course->id};
            }
            if ($fromform->{'c' . $course->id} == 'true' and !$export) {
                $export_courses .= $course->id . ",";
                $export_courses_sessions .= $fromform->{'session' . $course->id} . ",";
            }
        }

        $table = new html_table();
        //$table->head = array();

        $table->head[] = 'S.No';
        $table->align[] = 'center';
        $table->size[] = '40px';
        $table->headspan[] = 1;


        $table->head[] = 'Registration No';
        $table->align[] = 'center';
        $table->size[] = '220px';
        $table->headspan[] = 1;

        $table->head[] = "Name";
        $table->align[] = 'center';
        $table->size[] = '150px';
        $table->headspan[] = 1;
        $user_sessions = array();
        $TotalSessions = 0;
        $row = 1;
        $column = 4;
        for ($i = 0; $i < count($cselected["id"]); $i++) {
            $temp_stu = array_slice($cselected["students"][$i], 0, 1);
            $temp_course = $DB->get_record('course', array('id' => $cselected['id'][$i]));
            $temp_course->attendance_margin = $cselected['margin'][$i];

            foreach ($cselected["students"][$i] as $student) {
                if (!in_array($student->id, $students["userid"])) {

                    $students["userid"][] = $student->id;
                    $students["idnumber"][] = $student->idnumber;
                    $students["name"][] = $student->firstname . ' ' . $student->lastname;
                }
            }
        }
        $table->head[] = 'GPA';
        $table->align[] = 'center';
        $table->size[] = '';

        $table->head[] = 'CGPA';
        $table->align[] = 'center';
        $table->size[] = '';

        $table->head[] = 'Result';
        $table->align[] = 'center';
        $table->size[] = '';
       
        $table->head[] = '';
        $table->align[] = 'center';
        $table->size[] = '';

        $row_ite = 0;
        $row = 5;

        for (; $row_ite < count($students["userid"]); $row_ite++) {
            $table->data[$row][] = $row_ite + 1;
            //$table->data[$row][] = "";
            $table->data[$row][] = $students["idnumber"][$row_ite];
            $table->data[$row][] = $students["name"][$row_ite];
            $all_sessions_missed = 0;
            $all_sessions = 0;
            $GPA = 0;
            $GP = 0;
            $totalcredithours = 0;
            for ($j = 0; $j < count($cselected['id']); $j++) {
                $course = $DB->get_record('course', array('id' => $cselected['id'][$j]));

                $course->attendance_margin = $cselected['margin'][$j];
                if (!key_exists($students["userid"][$row_ite], (array) $cselected["students"][$j])) {
                    //$table->data[$row][] = '---';
                    // $table->data[$row][] = '---';
                    continue;
                }
                $user_id = $students['userid'][$row_ite];
                $CGPA = get_CGPA($user_id); //
                $grade_item = $DB->get_record("grade_items", array('courseid' => $course->id, 'itemtype' => 'course'));

                $sql = "SELECT *
                    FROM mdl_grade_grades
                    WHERE itemid = (
                    SELECT id
                    FROM mdl_grade_items
                    WHERE courseid =$course->id
                    AND itemtype = 'course' )
                    AND userid =$user_id";
                // $credithours=$course->credithours;
                $credithours = explode("+", $course->credithours);
                $credithours = $credithours[0] + $credithours[1];
                $grade = $DB->get_record_sql($sql);
                // print_r($grade);
//                $attendance = get_percent_absent($students["userid"][$row_ite], $course);
  //              $course_sess_att = get_grade($students["userid"][$row_ite], $course);
    //            $course_sessions = get_maxgrade($students["userid"][$row_ite], $course);
      //          $all_sessions+=$course_sessions;
        //        $course_sess_missed = $course_sessions - $course_sess_att;
          //      $all_sessions_missed+=$course_sess_missed;
                $subjgrade = grade_format_gradevalue_letter($grade->finalgrade, $grade_item);
                if ($grade) {
                    // $grade->finalgrade= number_format(($grade->finalgrade), 0);

                    switch ($subjgrade) {
                        case ($subjgrade == 'A' ):

                            $gradepoint = 4.0;
                            break;
                        case ($subjgrade == 'B+' ):

                            $gradepoint = 3.5;
                            break;
                        case ($subjgrade == 'B' ):

                            $gradepoint = 3.0;
                            break;
                        case ($subjgrade == 'C+' ):
                            $gradepoint = 2.5;
                            break;
                        case ($subjgrade == 'C' ):
                            $gradepoint = 2.0;
                            break;
                        case ($subjgrade == 'D' ):
                            $gradepoint = 1.0;
                            break;
                        case ($subjgrade == 'F' ):
                            $gradepoint = 0;
                            break;
                        case ($subjgrade == 'I' ):
                            $gradepoint = 0;
                            break;
                    }
                  
                } else {
                    
                    $gradepoint = 0;
                    
                }
                $length = strlen($course->fullname);
                //$characters = 1;
                $start = $length - 2;
                $section = substr($course->fullname, $start, 1);
                //$table->data[$row][1] = $section;
                $GP = ($gradepoint * $credithours) + $GP;
                $totalcredithours = $credithours + $totalcredithours;

               
            }
           
            $GPA = $GP / $totalcredithours;
            $GPA = number_format(($GPA), 2);
            $table->data[$row][] = $GPA;
            $table->data[$row][] = $CGPA;
            if (!$export) {

                $table->data[$row][] = '<input type="text" name="remarks' . $user_id . '" size="" value="">';
            } elseif ($export) {
                if ($form = data_submitted()) {
                    $formarr = (array) $form;

                    $table->data[$row][] = array_key_exists('remarks' . $user_id, $formarr) ? $formarr['remarks' . $user_id] : '';
                }
            }
           
            $table->data[$row][] = "<a href='$CFG->wwwroot/user/result.php?id=$user_id'>View Transcript</a>";
            $row++;
        }

       
        if ($export) {
          
            $table->category = $category->name;
            $table->schoolname = $school->name;
            $table->semester = $semester;
            $durationend = strtotime("+$temp_course->numsections weeks", $temp_course->timemodified);
            $table->end_duration = date(" M jS, Y", $durationend);
            $table->start_duration = date(" M jS, Y", $temp_course->timemodified);

            $text1 = array_key_exists('text1', $formarr) ? $formarr['text1'] : '';
            $text2 = array_key_exists('text2', $formarr) ? $formarr['text2'] : '';
            $dcontroller = array_key_exists('deputycontroller', $formarr) ? $formarr['deputycontroller'] : '';
            $econtroller = array_key_exists('examcontroller', $formarr) ? $formarr['examcontroller'] : '';

            //$table->duration = date("d M Y", $cselected["startdate"][0]).' to '.date("d M Y", time('now'));
            ExportToPDF($table, $text1, $text2, $dcontroller, $econtroller);
        } else {
            /* foreach($cselected["id"] as  $courseid){
              $sql="select verified from mdl_grade_items where courseid=$courseid and itemname='Grades'";
              //echo $sql;
              $verification=$DB->get_record_sql($sql);
              if(isset($verification->verified))
              $grades_verification[]=$verification->verified;
              } */

            //print_r($cselected["id"]);

            echo '<div style="text-align: center; font-weight: bold;"><u>NATIONAL UNIVERSITY OF SCIENCES AND TECHNOLOGY
ISLAMABAD</u><br></div>';
            //   echo '<div style="text-align: center; font-weight: bold;"><u>On Campus Semester Result Information</u><br></div>';
            //  $Duration_end = strtotime("+$temp_course->numsections weeks", $temp_course->timemodified);
            //  echo '<div style="text-align: center; font-weight: bold;"><br/>Semester: ' . $semester . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Duration: ' . date(" M jS, Y", $temp_course->timemodified) . '-' . date(" M jS, Y", $Duration_end) . '</div>';
            echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">
                            <form method="post" style="display: inline; margin: 0; padding: 0;">';
            echo '<div align="center"><textarea rows="1" cols="65" name="text1" size="" >Notification No NUST/SEECS/CC-10/8 August 2012</textarea></div><br/>';
            echo '<div align="center"><textarea rows="3" cols="65" name="text2" size="" >It is hereby notified that following students of --- course have completed their ' . $degree->name . ' (' . $semester . ') held at ' . $school->name . ' Date of award of BE Degree of qualified students is August 2012:-</textarea></div>';
            echo '<input type="hidden" name="courses" value="' . rtrim($export_courses, ',') . '" />';
            echo '<input type="hidden" name="sessions" value="' . rtrim($export_courses_sessions, ',') . '" />';
            echo '<input type="hidden" name="id" value="' . $categoryid . '" />';
            echo '<input type="hidden" name="export" value="true" /><input type="submit" value="Download PDF" />
                        ';
            // echo '<form method="post" style="display: inline; margin: 0; padding: 0;">';
            // if($categoryid!=-1)
            //       require_capability('block/custom_reports:getattendancereport', $context);
            echo html_writer::table($table);
            echo 'Deputy Controller of Examinations<textarea rows="1" cols="25" name="deputycontroller" size="" >
Engr.Khalid Mahmood</textarea>';
            echo '                A/Controller of Examinations<textarea rows="1" cols="25" name="examcontroller" size="" >
Dr. Mahmood A Rahi</textarea>';

            echo '</form>';
        }
        exit();
    }else
        $mform->display();
}else {
    $OUTPUT->box_start('generalbox categorybox');
    print_whole_category_list2(NULL, NULL, NULL, -1, false);
    $OUTPUT->box_end();
}
echo $OUTPUT->footer();

//================Export to Excel================//
function ExportToExcel($data, $text1, $text2) {
    global $CFG;

    //require_once("$CFG->libdir/excellib.class.php");/*
    require_once($CFG->dirroot . '/lib/excellib.class.php');
    $filename = "Semester_Result:.xls";

    $workbook = new MoodleExcelWorkbook("-");
    /// Sending HTTP headers
    ob_clean();
    $workbook->send($filename);
    /// Creating the first worksheet
    $myxls = & $workbook->add_worksheet('Semester_result');
    /// format types
    $formatbc = & $workbook->add_format();
    $formatbc->set_bold(1);
    $myxls->set_row(1, 20);
    $myxls->set_row(2, 20);
    //	$myxls->set_column(2,3,30);
    $header1 = & $workbook->add_format();
    $header1->set_bold(1);          // Make it bold
    $header1->set_align('center');  // Align text to center
    $header1->set_size(14);
    //$header1->set_fg_color(22);

    $header2 = & $workbook->add_format();
    $header2->set_bold(1);            // Make it bold
    $header2->set_align('center');  // Align text to center
    $header2->set_size(12);
    $header2->set_text_wrap(1);

    $normal = & $workbook->add_format();
    $normal->set_bold(0);
    $normal->set_align('center');
    $normal->set_size(10);
    $normal1 = & $workbook->add_format();
    $normal1->set_bold(1);
    $normal1->set_align('center');
    $normal1->set_size(10);

    $name = & $workbook->add_format();
    $name->set_bold(0);
    $name->set_size(10);
    $name->set_align('center');


    $grey_code_f = & $workbook->add_format();
    $grey_code_f->set_bold(0);            // Make it bold
    $grey_code_f->set_size(12);
    $grey_code_f->set_fg_color(22);
    $grey_code_f->set_align('center');

    //$formatbc->set_size(14);
    $myxls->write(1, 0, "NATIONAL UNIVERSITY OF SCIENCES AND TECHNOLOGY ISLAMABAD", $header1);
    $myxls->write(2, 0, $text1, $header1);
    $myxls->write(3, 3, $text2, $header2);
    //$myxls->write(3, 4, "Duration:" . $data->start_duration . "-" . $data->end_duration, $header2);

    $myxls->set_column(0, 0, 8);
    $myxls->set_column(1, 1, 25);
    $myxls->set_column(3, 3, 30);
    $myxls->set_column(2, 2, 25);
    $myxls->merge_cells(3, 1, 3, 2);
    $myxls->merge_cells(3, 3, 5, 9);
    $i = 7;
    $j = 0;
    $a = 0;
    $flag1 = false;
    foreach ($data->head as $heading) {
//        $heading = str_replace('<br/>', '', $heading);
//        $heading = trim($heading);
//
//        if ($heading == "Credits") {
//            $flag1 = true;
//        }
//        if ($flag1 == true) {
//            $myxls->set_column($j, $j, 5);
//        }
//
//        if ($j >= 4 && $flag1 == false) {
//            $myxls->set_column($j, $j, 8);
//            //$myxls->merge_cells($i,$j,$i,$j+1);/////merge
//        }
        //	if($data->headspan[$a]==2){
        //$myxls->set_column($j,$j+1,38);
        //$myxls->merge_cells($i,$j,$i,$j+1);/////merge
        //	}
        $myxls->write_string($i, $j, $heading, $header2);
        //$myxls->set_column($pos,$pos,(strlen($grade_item->get_name()))+4);
        //  $col_size = strlen($heading);
        // $col_size+=6;
        //	if(preg_match('/^NAME/i',$heading)){
        //		$col_size=39;
        //	}
        $myxls->set_row(1, 15);
        //	$myxls->set_column($j,$j,$col_size);
//        if ($data->headspan[$a] == 2) {
//            $myxls->merge_cells($i, $j, $i, $j + 1);
//            $j+=2;
//        }
//        if ($data->headspan[$a] == 1) {
//            $j++;
//        }
        $j++;
        $a++;
    }
    $myxls->set_row(5, 55);
    $myxls->merge_cells(1, 0, 1, $j - 1);
    $myxls->merge_cells(2, 0, 2, $j - 1);
    $myxls->merge_cells(4, 1, 4, 3);

    $i = 8;
    $j = 0;
//    foreach ($data->data as $row) {
//        foreach ($row as $cell) {
//            foreach ($cell as $cel) {
//                if ($cel->colspan == 2) {
//                    //$myxls->merge_cells($i,$j,$i,$j+1);/////merge
//                }
//
//                if (is_numeric($cel)) {
//                    $myxls->write_string($i, $j, strip_tags($cel->text), $normal1);
//                } else {
//                    $myxls->write_string($i, $j, strip_tags($cel->text), $normal1);
//                }
//                if ($cel->colspan == 2) {
//                    $myxls->merge_cells($i, $j, $i, $j + 1);
//                    $j+=2;
//                }
//                if ($cel->colspan == 1) {
//                    $j++;
//                }
//            }
//        }
//        $i++;
//        $j = 0;
//    }
//    $i = 9;
//    $j = 0;
    $flag = false;
    foreach ($data->data as $row) {
        foreach ($row as $cell) {
            //  if ($i >= 9 && $flag == true) {
            if (preg_match('/^<input/', $cell) || preg_match('/^<a/', $cell)) {
                
            } else {
                if (is_numeric($cell)) {
                    $myxls->write_number($i, $j++, $cell, $normal);
                } else {
                    $myxls->write_string($i, $j++, $cell, $normal);
                }
            }
            //}
        }
        $i++;
        $j = 0;
//        if ($i == 12 && $flag == false) {
//            $i = 9;
//            $flag = true;
//        }
    }




    $workbook->close();
    exit;
}

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
        $cat = html_writer::link(new moodle_url('', array('id' => $category->id)), format_string($category->name), $catlinkcss);
        $cat .= html_writer::tag('span', ' (' . count($courses) . ')', array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse'));

        if ($depth > 0) {
            for ($i = 0; $i < $depth; $i++) {
                //$html = html_writer::tag('div', $html .$cat, array('class'=>'indentation'));
                $html = html_writer::tag('div', $html . $cat, array('class' => 'indentation level' . $i));
                $cat = '';
            }
        } else {
            $html = $cat;
        }
        //Added By Hina Yousuf to show only those categories in which the user has access

        $context = get_context_instance(CONTEXT_COURSECAT, $category->id);
        if (has_capability('block/custom_reports:getresults', $context)) {
            echo html_writer::tag('div', $html, array('class' => 'category'));
        }
        //end
        echo html_writer::tag('div', '', array('class' => 'clearfloat', 'style' => 'clear: both;'));
        echo '</div>';
    }
}

////////////
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

function get_CGPA($user) {
    global $DB;
    // echo "<br/><br/>mmmmm";
    $sql = "SELECT cc.*,c.* from mdl_course_categories cc, mdl_course c, mdl_user_enrolments ue JOIN mdl_enrol e ON ( e.id = ue.enrolid AND ue.userid =$user ) where c.category=cc.id and e.courseid=c.id order by c.startdate asc;";
    $i = 0;
    $row = 0;
    $result_list = $DB->get_records_sql($sql);
    //print_r($result);
    $GP = 0;
    $k = 0;
    $semesters = sizeof($result);
    // foreach ($result as $cat) {
    $k++;
    //   $sql_list = "SELECT c.* from mdl_course_categories cc, mdl_course c, mdl_user_enrolments ue JOIN mdl_enrol e ON ( e.id = ue.enrolid AND ue.userid =$user ) where c.category=cc.id and e.courseid=c.id and cc.name='$cat->name' order by c.startdate asc; ";
    //   $result_list = $DB->get_records_sql($sql_list);
    // $categoryname = explode("/", $cat->name);
    $i++;
    $flag = true;
    // }

    $row++;

    $totalcredithours = 0;

    foreach ($result_list as $cat_list) {

        $i++;
        $grade_item = $DB->get_record("grade_items", array('courseid' => $cat_list->id, 'itemtype' => 'course'));
        $credithours = explode("+", $cat_list->credithours);
        $credithours = $credithours[0] + $credithours[1];

        ////
        $sql = "SELECT *
                    FROM mdl_grade_grades
                    WHERE itemid = (
                    SELECT id
                    FROM mdl_grade_items
                    WHERE courseid =$cat_list->id
                    AND itemname = 'Grades' )
                    AND userid =$user";

        $grade = $DB->get_record_sql($sql);

        if ($grade->finalgrade) {
            $grade->finalgrade = number_format(($grade->finalgrade), 0);
        } else {

            $sql = "SELECT *
                    FROM mdl_grade_grades
                    WHERE itemid = (
                    SELECT id
                    FROM mdl_grade_items
                    WHERE courseid =$cat_list->id
                    AND itemtype = 'course' )
                    AND userid =$user";
            $grade = $DB->get_record_sql($sql);
            $subjgrade = grade_format_gradevalue_letter($grade->finalgrade, $grade_item);
            if ($grade->finalgrade) {

                switch ($subjgrade) {
                    case ($subjgrade == 'A' ):

                        $gradepoint = 4.0;
                        break;
                    case ($subjgrade == 'B+' ):
                        $gradepoint = 3.5;
                        break;
                    case ($subjgrade == 'B' ):

                        $gradepoint = 3.0;
                        break;
                    case ($subjgrade == 'C+' ):
                        $gradepoint = 2.5;
                        break;
                    case ($subjgrade == 'C' ):
                        $gradepoint = 2.0;
                        break;
                    case ($subjgrade == 'D' ):
                        $gradepoint = 1.0;
                        break;
                    case ($subjgrade == 'F' ):
                        $gradepoint = 0;
                        break;
                    case ($subjgrade == 'I' ):
                        $gradepoint = 0;
                        break;
                }
            } else {
                $gradepoint = 0;
            }
        }

        $GP = ($gradepoint * $credithours) + $GP;

        $totalcredithours = $credithours + $totalcredithours;
        $row++;
    }
    $GPA = $GP / $totalcredithours;
    $GPA = number_format(($GPA), 2);
//        $CGP = ($GPA * $totalcredithours) + $CGP;
//        $CTotalcredithours = $totalcredithours + $CTotalcredithours;
//        $CGPA = $CGP / $CTotalcredithours;
//        $CGPA = number_format(($CGPA), 2);
    // }
    return $GPA;
}

function ExportToPDF($data, $text1, $text2, $dcontroller, $econtroller) {

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('');
    $pdf->SetTitle('Transcript');
    $pdf->SetSubject('Transcript');

    // remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // set default monospaced font
    //$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    //set margins
    //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetTopMargin(5);

    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    $pdf->setLanguageArray($l);

    // ---------------------------------------------------------
    // set font
    $pdf->SetFont('helvetica', '', 6);

    // add a page
    $pdf->AddPage('P', 'A4');
    ob_clean();
    // print a line using Cell()
    //$pdf->Cell(0, 10, 'Example 002', 1, 1, 'C');
    $htmcont = ImprovedTable($data, $text1, $text2, $dcontroller, $econtroller);
    $pdf->writeHTML($htmcont, true, false, false, false, '');
    //echo $htmcont;
    // ---------------------------------------------------------
    //Close and output PDF document
    $pdf->Output("Feedback_Report", 'D');
    exit;
}

//

function ImprovedTable($data, $text1, $text2, $dcontroller, $econtroller) {
    $date = date('d F Y');
    $content = $content . '<table border="1"><tr><td><table width="100%"  ><tr><td><table><tr><td width="45" style="vertical-align:middle" ><img src="NUST_Logo.jpg" height="52" width="52" /></td><td align="center" width="100%"> <font size="12"><b>NATIONAL UNIVERSITY OF SCIENCES AND TECHNOLOGY<br/>' . $text1 . '</b></font></td></tr></table></td></tr></table></td></tr></table>';
    $content = $content . '<h3 align="center"> ';
    $content = $content . $text2;
    $content = $content . '</h3>';
    $content = $content . '<table border="1"><tr><td><b>S.No</b></td><td><b>Registration No</b></td>';
    $content = $content . '<td><b>Name</b></td><td><b>Sem</b></td><td><b>Cum</b></td><td ><b>Result</b></td></tr>';

    foreach ($data->data as $row) {
        $content = $content . '<tr>';
        foreach ($row as $cell) {

            if (preg_match('/^<input/', $cell) || preg_match('/^<a/', $cell)) {
                
            } else {
                $content = $content . '<td>' . strip_tags($cell) . '</td>';
            }
        }
        $content = $content . '</tr>';
    }
    $content = $content . '</table><br/><br/>';
    $content = $content . '<table><tr><td>Deputy Controller of Examinations<br/>('.$dcontroller.')</td><td width="30"></td><td>Deputy Controller of Examinations<br/>('.$econtroller.')</td></tr></table>';
    return $content;
}
?>
