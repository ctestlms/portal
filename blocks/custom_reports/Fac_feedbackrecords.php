<?php
require_once('../../config.php');
require_once("../../mod/feedback/lib.php");
require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('./view_attendance_report_form.php');
require_once('../../mod/attforblock/locallib.php');
require('../../mod/attforblock/tcpdf/config/lang/eng.php');
require('../../mod/attforblock/tcpdf/tcpdf.php');
include('dbcon.php');
?>


<?php
require_login($course->id);
session_start();
$categoryid = optional_param('id', '-1', PARAM_INT);
$cats = $_POST[catg];
$allow = 0;
if ($cats) {
    $_SESSION['cats'] = $cats;
}
$cats = $_SESSION['cats'];

if ($categoryid == 2) {
    foreach ($cats as $cat) {
        $context = get_context_instance(CONTEXT_COURSECAT, $cat);
        require_capability('block/custom_reports:getcoursefeedback_avg', $context);
        $allow =1;
    }
}
foreach ($cats as $cat) {
    $catgcourse = get_courses($cat, '', 'c.id, c.fullname, c.startdate,c.credithours, c.idnumber, c.shortname');
    $courses_ = array_merge((array) $catgcourse, (array) $courses_);
}
//print_r($courses_);
$dept = optional_param('dept', "", PARAM_ALPHANUM); //get user sub group.
$school_ = $_GET['school_'];
$unique_courses[] = -1;
$hod = 0;
$observer = 0;
$user->id = 0;
$export = optional_param('export', false, PARAM_BOOL);
$context = get_context_instance(CONTEXT_COURSECAT, $categoryid);
$navlinks[] = array('name' => get_string('feedback_report', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if (!$export)
    print_header(get_string('feedback_report', 'block_custom_reports'), get_string('feedback_report', 'block_custom_reports'), $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);
 $user = $_POST['user'];

    if (isset($_POST['view'])) {
        $_SESSION['user'] = $user;
    }
    if ($export) {
        $user = $_SESSION['user'];
    }
    $uid_name = explode("|", $user);
    $uid = $uid_name[0];         // school id
    $uname = $uid_name[1];
    $uidnumber = $uid_name[2];

if (!$export && isset($_POST['report']) || isset($_POST['view'])) {
    if ($allow == 1) {
        Print_form($courses_,$uname);
    }else{
        echo "<b>You do no have permission to view this report.</b>".$categoryid;
    }
}
if ($export || isset($_POST['view'])) {
    $department = $_SESSION['department'];
    $school = $_SESSION['school'];
    $id_name = explode("|", $school);
    $id = $id_name[0];         // school id
    $name = $id_name[1];
}

if (isset($_POST['view']) or $export) {

    $month = (int) date('m');


    $user = $_POST['user'];

    if (isset($_POST['view'])) {
        $_SESSION['user'] = $user;
    }
    if ($export) {
        $user = $_SESSION['user'];
    }
    $uid_name = explode("|", $user);
    $uid = $uid_name[0];         // school id
    $uname = $uid_name[1];
    $uidnumber = $uid_name[2];
    if ($id != 0) {
        $user_subgroup = "AND user_subgroup = '$department'";
    }
    if ($id == 0) {
        $user_subgroup = "";
    }

    //checking for permissions
    $context = get_context_instance(CONTEXT_COURSECAT, $id);

    if ($id != 0) {     //If the school is selected not NUST
    }



//    $context = get_context_instance(CONTEXT_USER, $USER->id);
//    if (has_capability('block/custom_reports:getfeedbackreport', $context)) {
//        $admin = 1;
//        // echo  "admin";
//    }
    // if ($hod == 1 || $observer == 1 || $admin == 1) {
    echo "<br/>";
    echo '<div style="text-align: center; font-weight: bold;">FACULTY FEEDBACK REPORT <br></div>';
        echo '<div style="text-align: center; font-weight: bold;">Teacher:&nbsp;' . $uname . '<br></div>';
        echo '<div style="text-align: center; font-weight: bold;">Employee ID:&nbsp;' . $uidnumber . '<br></div>';
    //	echo '<div style="text-align: center; font-weight: bold;">Department:&nbsp;'.$department .'<br></div>';
    echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">
						<form method="post" style="display: inline; margin: 0; padding: 0;">';
    echo "<b>Download:</b>";
    echo "<select name='downloadType'>";
    // echo '<option value="pdf">Download in pdf Format </option>';
    echo '<option value="Excel">Download in Excel Format </option>';
    echo '</select>';


    echo '<input type="hidden" name="export" value="true" /><input type="submit" value="Download" />
						</form>';
    echo "<br/>";
    $headings = array('Teacher', 'Subject', 'Class', 'Average', 'Rating', 'Total Students', 'Submissions');
    $feedback_no = $_POST['feedback_no'];
    $fb = explode("|", $feedback_no);
    $feedback_no = $fb[0];
    $feedbak_no = $fb[1];
    if (isset($_POST['report'])) {
        $_SESSION['feedback_no'] = $feedback_no;
        $_SESSION['feedbak_no'] = $feedbak_no;
    }

    if ($export) {
        $feedback_no = $_SESSION['feedback_no'];
        $feedbak_no = $_SESSION['feedbak_no'];
    }
    $no_of_departments = 0;
    $table = new html_table();
    $qavg = array();
    $table->head = array();
    $table->head[] = "S/NO";
    $table->head[] = "Performance Criterion";





    ////get course of the selected department
    if ($id != 0) {
        $user_subgroup = "AND user_subgroup = '$department'";
    }
    if ($id == 0) {
        $user_subgroup = "";
    }

 $course_no = 0;
    $i = 0;

    foreach ($cats as $cat) {
        $count = 0;

        $sem_avg = 0;
        $sn = 1;
        $sql = "SELECT e.courseid,cc.name as semester,cc.id,c.category, e.courseid as courseid, fullname,shortname,e.timecreated
				as timecreated FROM mdl_user_enrolments ue JOIN mdl_enrol e ON ( e.id = ue.enrolid ) JOIN mdl_course c 
				ON ( c.id = e.courseid ) JOIN mdl_course_categories cc ON(cc.id=c.category) AND ue.userid =$uid AND cc.id =$cat ";

        $courses = $DB->get_records_sql($sql);

        foreach ($courses as $course) {
            $itemnr = 0;

            // $qavg=0;
            // $course_no++;
            //$sem_avg = 0;
            // $count++;
            ////
            $feedbacks = $DB->get_records_sql("SELECT id,name from {feedback} f WHERE course =$course->courseid and (name like'%First Student Feedback%' or name like'%Second Student Feedback%' ) and name like '%$uname%' ");
            $totalcount = 0;
            $totalusers = 0;
            $feedback_avg = 0;
            $feedbackno = 0;
            foreach ($feedbacks as $feedback) {
                $courseno++;
                $course_no++;
                $sem = explode(" ", $course->shortname);
                $sems = substr($sem[1], 0, 1);
                $year = substr($sem[1], 1);
                if ($sems == "S") {
                    $semster = "Spring";
                }
                if ($sems == "F") {
                    $semster = "Fall";
                }
                if ($count == 0) {
                    
                }


                if (!$items = $DB->get_records('feedback_item', array('feedback' => $feedback->id, 'hasvalue' => 1), 'position')) {
                    
                }
                $_SESSION['totalavg'] = 0;
                $_SESSION['questions'] = 0;
                unset($_SESSION['datasize']);
                $i = -1;
                $i++;
                $table->head[] = $course->fullname . "(" . $feedback->name . ")";
                foreach ($items as $item) {
                    //    $table->data[$i][]=$course->fullname;
                    $i++;
                    $_SESSION['count'] = $_SESSION['count'] + 1;
                    $itemobj = feedback_get_item_class($item->typ);
                    if ($itemobj == new feedback_item_multichoicerated()) {
                        $itemnr++;
                        if ($feedback->autonumbering) {
                            $printnr = $itemnr . '.';
                        } else {
                            $printnr = '';
                        }
                        $itemobj->print_detailed_analysed($item, $printnr, $mygroupid, '', &$table, $i, $courseno, &$qavg);
                    }
                }
            }
        }
    }
    $i = 1;
     $questions = 0;
    $total_avg = 0;
    $table->head[] = "Avg";
	 if ($course_no != 0) {

        foreach ($qavg as $avg) {
            $questions++;
            $q_avg = number_format((($avg / $course_no)), 2);
            $table->data[$i][] = $q_avg;
            $total_avg = $q_avg + $total_avg;
            $i++;
        }
//echo $total_avg;
        $totalmarks = $questions * 5;
        $percentavg = ($total_avg / $totalmarks) * 100;
        for ($j = 0; $j < $course_no + 1; $j++) {
            $table->data[$i][$j] = "";
        }
        $table->data[$i][$j++] = "<b>Total Avg:</b>";
        $table->data[$i++][$j++] = number_format(($percentavg), 2);
        for ($j = 0; $j < $course_no + 1; $j++) {
            $table->data[$i][$j] = "";
        }
        $table->data[$i][$j++] = "";
        $table->data[$i++][$j++] = "";
        for ($j = 0; $j < $course_no + 1; $j++) {
            $table->data[$i][$j] = "";
        }
        $table->data[$i][$j++] = "<b>Total Points Scored based on the following formula= :</b>";
        $table->data[$i++][$j++] = "";
        for ($j = 0; $j < $course_no + 1; $j++) {
            $table->data[$i][$j] = "";
        }
        $table->data[$i][$j++] = "<b> For Teaching Focused :(Total Marks ÷100 x 25)</b>";
        $table->data[$i++][$j++] = "";

        for ($j = 0; $j < $course_no + 1; $j++) {
            $table->data[$i][$j] = "";
        }
        $table->data[$i][$j++] = "<b> For Research Focused :(Total Marks ÷100 x 10)</b>";
        $table->data[$i++][$j++] = "";
        for ($j = 0; $j < $course_no + 1; $j++) {
            $table->data[$i][$j] = "";
        }
        $table->data[$i][$j++] = "<b> For Hybrid Focused :(Total Marks ÷100 x 20)</b>";
        $table->data[$i++][$j++] = "";//number_format((($total_avg / 100) * 20), 2);
    }
//        echo "Sorry! You do not have permission to access this report.";
//    }
} elseif (!isset($_POST['view']) && !isset($_POST['report'])) {
    echo '<form name="myform" action="Fac_feedbackrecords.php?id=2" method="POST">';
    echo "<div align='center'><h1>Faculty Feedback Report</h1></div>";

    echo "<br/><b>Select Semester:</b>";
    print_whole_category_list2(NULL, NULL, NULL, -1, false);

//        
    ?>	

    <?php
    // }
    echo '<br/><input type="submit" value="View Report" name="report">';
    $OUTPUT->box_start('generalbox categorybox');
    echo '</form>';
}
if ($export) {

    $downloadType = $_POST['downloadType'];
    if ($downloadType == "Excel")
        ExportToExcel($table, $name, $department, $uname, $uidnumber);
    if ($downloadType == "pdf")
        ExportToPDF($table, $name, $department, $uname, $uidnumber);
}

if (isset($_POST['view'])) {
    echo html_writer::table($table);
}
$OUTPUT->box_end();
echo $OUTPUT->footer();

//================Export to Excel================//    
function ExportToExcel($data, $name, $department, $uname, $uidnumber) {
    global $CFG;
    global $headings;

    //require_once("$CFG->libdir/excellib.class.php");/*
    require_once($CFG->dirroot . '/lib/excellib.class.php');
  $filename = "Feedback Report " . $uname . ".xls";
    $workbook = new MoodleExcelWorkbook("-");
/// Sending HTTP headers
    ob_clean();
    $workbook->send($filename);
/// Creating the first worksheet
    $myxls = & $workbook->add_worksheet('Faculty Feedback Report');
/// format types
    $formatbc = & $workbook->add_format();
    $formatbc1 = & $workbook->add_format();
    $formatbc->set_bold(1);
    $myxls->set_column(0, 0, 20);
    $myxls->set_column(1, 7, 30);
    $formatbc->set_align('center');
    $formatbc1->set_align('center');
    $xlsFormats = new stdClass();
    $xlsFormats->default = $workbook->add_format(array(
        'width' => 40));
    //$formatbc->set_size(14);
    $myxls->write(0, 2, "FEEDBACK REPORT", $formatbc);

    $myxls->write(1, 2, "Teacher: " . $uname, $formatbc);
    $myxls->write(2, 2, "Employee ID: " . $uidnumber, $formatbc);


    foreach ($data->head as $heading)
        $myxls->write_string(4, $j++, strtoupper($heading), $formatbc);

    $i = 5;
    $j = 0;
    foreach ($data->data as $row) {
        foreach ($row as $cell) {

            //$myxls->write($i, $j++, $cell);
            if (is_numeric($cell)) {
                $myxls->write_number($i, $j++, strip_tags($cell), $formatbc1);
            } else {
                $myxls->write_string($i, $j++, strip_tags($cell), $formatbc1);
            }
        }
        $i++;
        $j = 0;
    }
    $workbook->close();
    exit;
}

//export to pdf

function ExportToPDF($data, $name, $department, $uname, $uidnumber) {
    //echo "pdf";
    global $CFG;
    global $headings;

    //$pdf=new PDF();
    //Column titles
    //$header=$data->tabhead;
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('');
    $pdf->SetTitle('Feedback Report');
    $pdf->SetSubject('Feedback Report');

    // remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // set default monospaced font
    //$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    //set margins
    //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);


    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    $pdf->setLanguageArray($l);

    // ---------------------------------------------------------
    // set font
    $pdf->SetFont('helvetica', '', 8);

    // add a page
    $pdf->AddPage('P', 'A4');
    ob_clean();
    // print a line using Cell()
    //$pdf->Cell(0, 10, 'Example 002', 1, 1, 'C');
    $htmcont = ImprovedTable($headings, $data, $name, $department, $uname, $uidnumber);
    $pdf->writeHTML($htmcont, true, false, false, false, '');
    //echo $htmcont;
    // ---------------------------------------------------------
    //Close and output PDF document
    $pdf->Output("Feedback_Report", 'D');
    exit;
}

//

function ImprovedTable($headings, $data, $name, $department, $uname, $uidnumber) {
    global $CFG;
    global $headings;

    //echo "improe";
    //Column widths
    //$w=array(40,35,40,45);
    //Header
    $content = $content . '<table cellpadding="2" border="0"><tr><td ><img src="NUST_Logo.jpg" height="52" width="52" /> <font size="15"><b>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Faculty Feedback Report</b><br/></font></td></tr></table>';

    $content = $content . '<h3 align="left">School: ' . $name . '</h3>';
    $content = $content . '<h3 align="left">Department: ';
    $content = $content . $department;
    $content = $content . '</h3>';
    $content = $content . '<table cellpadding="2" border="1"><tr>';
    /* $content = $content.'<td width="3%">-</td><td width="%10">Lecture Hours/Lab</td><td width="13%">-</td>';
      foreach ($lecture as $l){
      $content = $content.'<td width="18">'.$l.'</td>';
      }
      $content = $content.'<td width="18">-</td><td width="18">-</td><td width="25">-</td></tr><tr>'; */
    for ($i = 0; $i < count($headings); $i++)
    //$this->Cell($w[$i],7,$header[$i],1,0,'C');
        if ($i == 0) {
            $content = $content . '<td width="5%"><b>' . $headings[$i] . '</b></td>';
        } elseif ($i == 1) {
            $content = $content . '<td width="20%"><b>' . $headings[$i] . '</b></td>';
        } elseif ($i == 2) {
            $content = $content . '<td width="20%"><b>' . $headings[$i] . '</b></td>';
        } elseif ($i == 3) {
            $content = $content . '<td width="15%"><b>' . $headings[$i] . '</b></td>';
        } elseif ($i == 4) {
            $content = $content . '<td width="10%"><b>' . $headings[$i] . '</b></td>';
        } elseif ($i == 5) {
            $content = $content . '<td width="10%"><b>' . $headings[$i] . '</b></td>';
        } elseif ($i == 6) {
            $content = $content . '<td width="10%"><b>' . $headings[$i] . '</b></td>';
        }
    //$this->Ln();
    $content = $content . '</tr>';
    //Data
    foreach ($data->data as $row) {
        $content = $content . '<tr>';
        $i = 0;
        foreach ($row as $col) {
            if ($i == 0) {
                $content = $content . '<td width="5%">' . strip_tags($col) . '</td>';
            } elseif ($i == 1) {
                $content = $content . '<td width="20%">' . strip_tags($col) . '</td>';
            } elseif ($i == 2) {
                $content = $content . '<td width="20%">' . strip_tags($col) . '</td>';
            } elseif ($i == 3) {
                $content = $content . '<td width="15%">' . strip_tags($col) . '</td>';
            } elseif ($i == 4) {
                $content = $content . '<td width="10%">' . strip_tags($col) . '</td>';
            } elseif ($i == 5) {
                $content = $content . '<td width="10%">' . strip_tags($col) . '</td>';
            } elseif ($i == 6) {
                $content = $content . '<td width="10%">' . strip_tags($col) . '</td>';
            }
            $i = $i + 1;
        }
        $content = $content . '</tr>';
    }
    $content = $content . '</table>';
    //Closure line
    //$this->Cell(array_sum($w),0,'','T');
    //echo 'Hello';
    return $content;
}

function Print_form($courses,$name) {
    global $CFG, $DB;
    foreach ($courses as $course) {
        if ($courses_ == "") {
            $courses_ = $course->id;
        } else {
            $courses_.= "," . $course->id;
        }
    }

    $sql = "SELECT u.id as id, username,firstname,lastname,idnumber
		FROM mdl_user u
		JOIN mdl_role_assignments ra ON ra.userid = u.id
		JOIN mdl_role r ON ra.roleid = r.id
		JOIN mdl_context c ON ra.contextid = c.id
		WHERE r.name = 'Teacher'
		AND c.contextlevel =50
		AND c.instanceid
		IN (
		
		$courses_
		)
		GROUP BY username";
    $users = $DB->get_records_sql($sql);
//    echo $sql;
    // echo "<div style='text-align: center; font-weight: bold;'>Faculty Feedback Record </div>";
    echo "<form name='school_report' method='post' action='Fac_feedbackrecords.php'>";

    echo "<br/><br/><table   border='1'><tr><td>";
    echo "<label><b>Select Teacher:</b></label></td><td>";

    echo '<input type="hidden" name="export" value="false" />';

    echo "<input type='hidden' name='id' id='id' value=2>";
    echo "<select name='user'>";
    foreach ($users as $user) {
        $value = $user->id . "|" . $user->firstname . " " . $user->lastname . "|" . $user->idnumber;
	$selected = ($name == $user->firstname . " " . $user->lastname) ? "selected = 'selected'" : "";
        ?>
        <option value="<?php echo $value; ?>"<?php echo $selected; ?>>
        <?php echo $user->idnumber . "(" . $user->firstname . " " . $user->lastname . ")"; ?>
        </option>
        <?php
    }
    echo "</select></td></tr>";
    ?>


    <td>&nbsp;</td>

    <td><input type="submit" name="view" value="View"></td>
    </form>
    </tr>
    </table>


    <?php
}

function get_rating($percentavg) {
    switch ($percentavg) {
        case ($percentavg >= 90 && $percentavg <= 100 ):
            $rating = '<div style="background: green;color:white;">Excellent</div>';
            break;
        case ($percentavg >= 75 && $percentavg <= 89.99 ):
            $rating = '<div style="background: #ADFF2F;">Very Good</div>';
            break;
        case ($percentavg >= 60 && $percentavg <= 74.99 ):
            $rating = '<div style="background: #FF9900;color:white;">Good</div>';
            break;
        case ($percentavg >= 40 && $percentavg <= 59.99 ):
            $rating = '<div style="background: #FF4500;color:white;">Satisfactory</div>';
            break;
        case ($percentavg > 0 && $percentavg <= 39.99 ):
            $rating = '<div style="background: red;color:white;">Poor</div>';
            break;
        case ($percentavg == 0 ):
            $rating = "Feedback not completed";
            break;
    }
    return $rating;
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
