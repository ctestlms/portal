<script type="text/javascript" >
    function doMenu(item) {
	
	 
        obj=document.getElementById(item);
        col=document.getElementById("x" + item);
	 
        if (obj.style.display=="none") {
            obj.style.display="block";
            col.innerHTML="<img src='../course/arw_on.gif'>";

        }
        else {
            obj.style.display="none";
            col.innerHTML="<img src='../course/arw_off.gif'>";
        }
    }
</script>
<?php
require_once('../config.php');
require_once($CFG->libdir . '/gradelib.php');
require($CFG->dirroot . '/mod/attforblock/tcpdf/config/lang/eng.php');
require($CFG->dirroot . '/mod/attforblock/tcpdf/tcpdf.php');
if (!empty($CFG->forceloginforprofiles)) {
    require_login();
    if (isguestuser()) {
        $SESSION->wantsurl = $PAGE->url->out(false);
        redirect(get_login_url());
    }
} else if (!empty($CFG->forcelogin)) {
    require_login();
}
$userid = optional_param('id', '', PARAM_INT);
$export = optional_param('result', false, PARAM_BOOL);
global $CFG, $USER, $DB, $OUTPUT;
$conferred = true;
$navigation = build_navigation($navlinks);
$context = $usercontext = context_user::instance($userid, MUST_EXIST);
if (has_capability('block/custom_reports:viewtranscript', $context)) {
    $userid = $userid;
} else {
    $userid = $USER->id;
}
$user = $DB->get_record('user', array('id' => $userid));
if (!$export) {

    $PAGE->set_pagelayout('admin');
    $navigation = build_navigation($navlinks);
    print_header('Transcript', 'Transcript', $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);
}

echo '<form name="myform" action="result.php" method="POST">';
echo '<br/><input type="submit" value="Download PDF" name="download">';
echo '<br/><input type="hidden" value=' . $userid . ' name="id">';
echo '<br/><input type="hidden" value="true" name="result">';
echo "<h1 align='center'>ACADEMIC TRANSCRIPT</h1>";
echo "<h2 align='center'>(" . strtoupper($user->user_subgroup) . ")</h2>";
echo "<table><tr><td><b>Name:</b> " . $user->firstname . " " . $user->lastname . "</td><td><b>Father's Name</b>" . $user->fathername . "</td></tr>";
echo "<tr><td colspan='2'><b>Regn No:</b> " . $user->idnumber . "</td><td ><b>Date of Award:</b><textarea rows='1' cols='15' name='dateofaward' size=''></textarea></td></tr></table>";
$htmlarray = array();
$flag == false;

$CTotalcredithours = 0;
echo $OUTPUT->box_start('coursebox');

$attributes = array('title' => s($course->fullname));

if (empty($course->visible)) {

    $attributes['class'] = 'dimmed';
}

//repeated courses
$sql = "SELECT cc.*,c.* from mdl_course_categories cc, mdl_course c, mdl_user_enrolments ue JOIN mdl_enrol e ON ( e.id = ue.enrolid AND ue.userid =$userid ) where c.category=cc.id and e.courseid=c.id group by cc.id order by c.startdate asc;";
$repeatedcourses = $DB->get_records_sql($sql);
$i = 1;
foreach ($repeatedcourses as $rc) {
    $batchs = explode("/", $rc->path);
    if ($batchs[4] != "" && $i == 1) {
        $batch = $batchs[4];
        $i++;
    }

    continue;
}
//end
$sql = "SELECT cc.* ,c.fullname from mdl_course_categories cc, mdl_course c, mdl_user_enrolments ue JOIN mdl_enrol e ON ( e.id = ue.enrolid AND ue.userid =$userid ) where c.category=cc.id and e.courseid=c.id and path like '%/$batch/%' group by cc.id order by c.startdate asc;";
$i = 0;
$row = 0;

$result = $DB->get_records_sql($sql);
$table1 = new html_table();
$k = 0;
$semesters = sizeof($result);
foreach ($result as $cat) {
    if ($cat->name != "Miscellaneous") {
        $k++;
        //$GP=0;
        $table = new html_table();
        $table->head[] = 'Course Code';
        $table->align[] = 'center';
        $table->size[] = '40px';
        $table->headspan[] = 1;
        $table->head[] = 'Course Title';
        $table->align[] = 'center';
        $table->size[] = '50px';
        $table->headspan[] = 1;

        $table->head[] = 'Credit Hours';
        $table->align[] = 'center';
        $table->size[] = '220px';
        $table->headspan[] = 1;

        $table->head[] = "Grade";
        $table->align[] = 'center';
        $table->size[] = '150px';
        $table->headspan[] = 1;

        $table->head[] = "GPs";
        $table->align[] = 'center';
        $table->size[] = '150px';
        $table->headspan[] = 1;

        $sql_list = "SELECT c.* from mdl_course_categories cc, mdl_course c, mdl_user_enrolments ue JOIN mdl_enrol e ON ( e.id = ue.enrolid AND ue.userid =$user->id ) where c.category=cc.id and e.courseid=c.id and cc.id='$cat->id' order by c.startdate asc; ";
        $result_list = $DB->get_records_sql($sql_list);
        $categoryname = explode("/", $cat->name);
        if ($cat->parent > 0) {

            $sqls = "select name from mdl_course_categories  where id='$cat->parent' order by name asc;";

            $results = $DB->get_records_sql($sqls);

            foreach ($results as $catss) {

                $cate = $catss->name;
            }
        }
        ///
        $sql = "select parent,name from mdl_course_categories where id=$cat->id";

        $parent = $DB->get_record_sql($sql);

        if ($parent->parent != 0) {

            do {


                $sql = "select id, parent,name from mdl_course_categories where id=$parent->parent";
                $degrees = $parent->name;
                $parent = $DB->get_record_sql($sql);

                if (strstr($parent->name, "Undergraduate") == true || strstr($parent->name, "Postgraduate") == true) {
                    $program = $parent->name;
                    $degree = $degrees;
                }
            } while ($parent->parent != 0);
        }$school = $parent->name;
        ///

        $i++;
        ?>
        <?php
        echo '<h2><a href="JavaScript:doMenu(' . $i . ');" id=x' . $i . '><img src="../course/arw_off.gif"></a><a style="color:#0000A0">&nbsp;' . $categoryname[0] . '</a></h2>';
        echo '<div id=' . $i . ' style="display: none;" >';
        // if ($categoryname[0] == "1st Semester") {
        $flag = true;
        // }
        $table1->data[$row][] = "!!" . $categoryname[0];
         $row++;
        //  $k++;
        //  $totalcredithours = 0;
        $j = 0;
        $keyss = 0;
        foreach ($result_list as $keys => $cat_list) {
            $j++;
            foreach ($repeatedcourses as $key => $rc) {
                if (($cat_list->fullname == $rc->fullname) && strstr($rc->path, "/$batch/") == false) {
                    $result_list[$keyss]->fullname = "*" . $result_list[$keys]->fullname;
                    // $result_list[$keys] = $cat_list;
                    $result_list = array_slice($result_list, 0, $j, true) +
                            array($key => $rc) +
                            array_slice($result_list, $j, NULL, true);


                    unset($repeatedcourses[$key]);
                }
                $keyss = $keys;
            }
            //end 
        }

        foreach ($result_list as $cat_list) {

            $coursedetails = explode(" ", $cat_list->fullname);
            $coursecode = $coursedetails[0];
            $coursename = explode("(", $cat_list->fullname);
            if ($coursename == "")
                continue;

            $credithours = explode("+", $cat_list->credithours);
            $credithours = $credithours[0] + $credithours[1];
            if (strstr($coursecode, "*")) {
                $table1->data[$row][] = $table->data[$row][] = "<div style='font-style:italic;'><a href='$CFG->wwwroot/course/view.php?id=$cat_list->id'>" . trim($coursecode, "*") . "</a></div>";
                $table1->data[$row][] = $table->data[$row][] = "<div style='font-style:italic;'>" . trim($coursename[0], $coursecode) . "</div>";
                $table->data[$row][] = $table1->data[$row][] = "<div style='font-style:italic;'>" . $credithours . "</div>";
            } else {
                $table1->data[$row][] = $table->data[$row][] = $coursecode;
                $table1->data[$row][] = $table->data[$row][] = "<a href='$CFG->wwwroot/course/view.php?id=$cat_list->id'>" . trim($coursename[0], $coursecode) . "</a>";
                $table->data[$row][] = $table1->data[$row][] = $credithours;
            }

            ////
            $i++;


            $grade_item = $DB->get_record("grade_items", array('courseid' => $cat_list->id, 'itemtype' => 'course'));

            $sql = "SELECT *
                    FROM mdl_grade_grades
                    WHERE itemid = (
                    SELECT id
                    FROM mdl_grade_items
                    WHERE courseid =$cat_list->id
                    AND itemname = 'Grades' )
                    AND userid =$user->id";

            $grade = $DB->get_record_sql($sql);

            if ($grade->finalgrade) {
                $grade->finalgrade = number_format(($grade->finalgrade), 0);
                switch ($grade->finalgrade) {
                    case ($grade->finalgrade == 1 ):
                        $subjgrade = 'A';
                        $gradepoint = 4.0;
                        break;
                    case ($grade->finalgrade == 2 ):
                        $subjgrade = 'B+';
                        $gradepoint = 3.5;
                        break;
                    case ($grade->finalgrade == 3 ):
                        $subjgrade = 'B';
                        $gradepoint = 3.0;
                        break;
                    case ($grade->finalgrade == 4 ):
                        $subjgrade = 'C+';
                        $gradepoint = 2.5;
                        break;
                    case ($grade->finalgrade == 5 ):
                        $subjgrade = 'C';
                        $gradepoint = 2.0;
                        break;
                    case ($grade->finalgrade == 6 ):
                        $subjgrade = 'D';
                        $gradepoint = 1.0;
                        break;
                    case ($grade->finalgrade == 7 ):
                        $subjgrade = 'F';
                        $gradepoint = 0;
                        break;
                    case ($grade->finalgrade == 8 ):
                        $subjgrade = 'I';
                        $gradepoint = 0;
                        break;
                }
                if ($subjgrade == "F") {
                    $conferred = false;
                }
                if (strstr($coursecode, "*") == true) {
                    $table1->data[$row][] = $table->data[$row][] = "--";
                    $table1->data[$row][] = $table->data[$row][] = "--";
                } else {
                    $table1->data[$row][] = $table->data[$row][] = $subjgrade;
                    $table1->data[$row][] = $table->data[$row][] = $gradepoint * $credithours;
                }
            } else {

                $sql = "SELECT *
                    FROM mdl_grade_grades
                    WHERE itemid = (
                    SELECT id
                    FROM mdl_grade_items
                    WHERE courseid =$cat_list->id
                    AND itemtype = 'course' )
                    AND userid =$user->id";
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
                        case ($subjgrade == 'D+' ):
                            $gradepoint = 1.5;
                            break;
                        case ($subjgrade == 'F' ):
                            $gradepoint = 0;
                            break;
                        case ($subjgrade == 'I' ):
                            $gradepoint = 0;
                            break;
                    }
                    if ($subjgrade == "F") {
                        $conferred = false;
                    }
                    if (strstr($coursecode, "*") == true) {
                        $table1->data[$row][] = $table->data[$row][] = $subjgrade;
                        $table1->data[$row][] = $table->data[$row][] = "--";
                    } else {
                        $table1->data[$row][] = $table->data[$row][] = $subjgrade;
                        $table1->data[$row][] = $table->data[$row][] = $gradepoint * $credithours;
                    }
                } else {

                    $table1->data[$row][] = $table->data[$row][] = "Not graded";
                    $gradepoint = 0;
                    $table1->data[$row][] = $table->data[$row][] = $gradepoint * $credithours;
                }
            }
            $length = strlen($cat_list->fullname);

            $start = $length - 2;
            $section = substr($cat_list->fullname, $start, 1);

            if (strstr($coursecode, "*") != true) {
                $GP = ($gradepoint * $credithours) + $GP;
                $totalcredithours = $credithours + $totalcredithours;
            }
            $row++;
        }
        $GPA = $GP / $totalcredithours;
        $GPA = number_format(($GPA), 2);
        $CGP = ($GPA * $totalcredithours) + $CGP;
        $CTotalcredithours = $totalcredithours + $CTotalcredithours;
        $CGPA = $CGP / $CTotalcredithours;
        $CGPA = number_format(($CGPA), 2);

        if ($k == 1) {
            $table1->data[$row][] = $table->data[$row][] = "";
            $table1->data[$row][] = $table->data[$row][] = "<b>Sem Crs</b>";
            $table1->data[$row][] = $table->data[$row][] = "<b>" . $totalcredithours . "</b>";
            $table1->data[$row][] = $table->data[$row][] = "<b>Sem GPA</b>";
            $table1->data[$row][] = $table->data[$row][] = "<b>" . $GPA . "</b>";
        } elseif ($k != 1 && $k < ($semesters)) {
            $table1->data[$row][] = $table->data[$row][] = "";
            $table1->data[$row][] = $table->data[$row][] = "<b>Cum Crs</b>";
            $table1->data[$row][] = $table->data[$row][] = "<b>" . $totalcredithours . "</b>";
            $table1->data[$row][] = $table->data[$row][] = "<b>Cum GPA</b>";
            $table1->data[$row][] = $table->data[$row][] = "<b>" . $GPA . "</b>";
        }

        $row++;

        $row+=2;
        if (!$export) {
            echo html_writer::table($table);
        }

        echo '</div>';
    }
}
echo 'Note:<textarea rows="1" cols="25" name="note" size="" >
</textarea>';
//$table1->data[$row][] = $table->data[$row][] = "<b>Cum Crs</b>";
if ($flag == true) {
    $table1->data[$row][] = $table->data[$row][] = "<b>Cum Crs:  " . $CTotalcredithours . "</b>";
} else {
    $table1->data[$row][] = $table->data[$row][] = "<b>---</b>";
}
//$table1->data[$row][] = $table->data[$row][] = "<b>Cum GPA</b>";
if ($flag == true) {
    $table1->data[$row][] = $table->data[$row][] = "<b>Cum GPA:   " . $CGPA . "</b>";
} else {
    $table1->data[$row][] = $table->data[$row][] = "<b> CGPA not avaiable on LMS</b>";
}

//$table1->data[$row][] = $table->data[$row][] = "<b>Status</b>";
if ($CTotalcredithours < 137 || $CGPA < 2.0 || $conferred == false) {
    $status = "Degree Not Conferred";
} else {
    $status = "Degree Conferred";
}

$table1->data[$row][] = $table->data[$row][] = "<b>         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Status: </b>";
$table1->data[$row][] = $table->data[$row][] = "<b> " . $status . "</b>";


echo '</form>';
echo $OUTPUT->box_end();

if ($export) {
    if ($form = data_submitted()) {
        $formarr = (array) $form;
        $dateofaward = array_key_exists('dateofaward', $formarr) ? $formarr['dateofaward'] : '';
        $note = array_key_exists('note', $formarr) ? $formarr['note'] : '';
    }
    ExportToPDF(strtoupper($user->user_subgroup), $user->fathername, $user->firstname . "" . $user->lastname, $user->idnumber, $table1, $school, $program, $degree, $note, $dateofaward);
} else {
    
}
echo $OUTPUT->footer();

//export to pdf

function ExportToPDF($dscp, $fathername, $name, $regno, $data, $school, $program, $degree, $note, $dateofaward) {

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
    $htmcont = ImprovedTable($dscp, $fathername, $name, $regno, $data, $school, $program, $degree, $note, $dateofaward);
    $pdf->writeHTML($htmcont, true, false, false, false, '');
    //echo $htmcont;
    // ---------------------------------------------------------
    //Close and output PDF document
    $pdf->Output("Transcript-" . $name, 'D');
    exit;
}

//

function ImprovedTable($dscp, $fathername, $name, $regno, $data, $school, $program, $degree, $note, $dateofaward) {
    $date = date('d F Y');
    $content = $content . '<table border="1"><tr><td><table width="100%"  ><tr><td><table><tr><td width="45" style="vertical-align:middle" ><img src="NUST_Logo.jpg" height="52" width="52" /></td><td align="center" width="100%"> <font size="12"><b>NATIONAL UNIVERSITY OF SCIENCES AND TECHNOLOGY<br/>Academic Transcript(' . $program . ')</b></font></td></tr></table></td></tr>';


    $content = $content . '<tr><td><table cellpadding="5"  border="0"><tr>';
    $content = $content . '<td width="10%">Name:</td><td><b>' . $name . '</b></td><td width="10%">Registration No:</td><td><b>' . $regno . '</b></td></tr>';
    $content = $content . '<tr><td width="10%">Degree:</td><td><b>' . $degree . '</b></td><td width="10%" >Date of Award:</td><td><b>' . $dateofaward . '</b></td></tr>';
    $content = $content . '<tr><td width="10%" >Campus:</td><td><b>' . $school . '</b></td><td width="10%" >Date of Issue:</td><td><b>' . $date . '</b></td></tr></table></td></tr></table></td></tr></table><br/>';
    $content = $content . '<table cellpadding="2" border="1">';
    $content = $content . '<tr><td><table >';
    $content = $content . '<table style="border:1px solid black;"><tr><td><b>Course Code</b></td ><td ><b>Title</b></td>';
    $content = $content . '<td ><b>CRs</b></td><td ><b>Grade</b></td><td ><b>GPs</b></td></tr></table>';
    $i = 0;
    $j = 0;
    $next = false;
    //Data
    foreach ($data->data as $row) {
        if ($i == "6" && $next == true) {
            if ($j == 0) {
                $content = $content . '</table></td><td><table >';
                $content = $content . '<table border="1"><tr><td  ><b>Course Code</b></td ><td ><b> Title</b></td>';
                $content = $content . '<td ><b>CRs</b></td><td ><b>Grade</b></td><td ><b>GPs</b></td></tr></table>';
            }
            $j++;
        }

        $content = $content . '<tr>';

        foreach ($row as $col) {
            if (strstr($col, '!!') == true) {
                $content = $content . '<td><br/><b><u>' . ltrim(strip_tags($col), "!!") . '</u></b></td><td></td><td></td><td></td><td></td>';
                $i++;
            } else {
                if (strstr($col, '<b>') == true)
                    $content = $content . '<td><b>' . strip_tags($col) . '</b></td>';
                else
                    $content = $content . '<td>' . strip_tags($col) . '</td>';
                if (strstr($col, 'Cum GPA') == true && $i == 6) {
                    $next = true;
                }
            }
        }
        $content = $content . '</tr>';
    }
    $content = $content . '<br/><b>Note:' . $note . '</b>';
    $content = $content . '<br/>.............................................END OF TRANSCRIPT...............................................';
    $content = $content . '</table></td></tr>';
    $content = $content . '</table>';
    return $content;
}

//echo $OUTPUT->footer();
?>


