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


require_login($course->id);
session_start();
$categoryid = optional_param('id', '-1', PARAM_INT);
if ($categoryid != -1) {
    $semester = $DB->get_record_sql("select name,path from {course_categories} where id=$categoryid");
    $path = explode("/", $semester->path);
    $semestername=$semester->name;
  if (strstr($semestername, "Summer") == false) {
   
    $school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]");
    $degree = $DB->get_record_sql("select id, name from {course_categories} where id=$path[3]");
    $batch = $DB->get_record_sql("select id,name from {course_categories} where id=$path[4]");
}
}
 $semester = explode(" ", $semester->name);
    $semester = $semester[0];
$export = optional_param('export', false, PARAM_BOOL);
$save = optional_param('save', false, PARAM_BOOL);
$context = get_context_instance(CONTEXT_COURSECAT, $categoryid);
$summary = array('Promoted' => 0, 'Promoted on Probation' => 0, 'Promoted with Warning' => 0, 'Withdrawn' => 0, 'Result Later' => 0, 'Left' => 0, 'Temp Suspended' => 0);
$gpasummary = array('4.00' => 0, '3.5 ~ 3.99' => 0, '3.00 ~ 3.49' => 0, '2.5 ~ 2.99' => 0, '2.00 ~ 2.49' => 0, 'Less Than 2.00' => 0);


if ($categoryid != -1)
    require_capability('block/custom_reports:getresults', $context);
$report = 'Semester Result';
$navlinks[] = array('name' => get_string('results', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if (!$export)
    print_header('Semester Result', 'Semester Result', $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);

if ($courses = get_courses($categoryid, '', 'c.id, c.fullname, c.startdate, c.idnumber, c.shortname') or $export or $save) {
    $mform = new mod_custom_reports_view_attendance_report_form('semester_results.php', array('courses' => $courses, 'categoryid' => $categoryid, 'report' => $report));
    if ($fromform = $mform->get_data() or $export or $save) {
        $cselected = array();
        if ($export or $save) {
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

            if ((!$export AND $fromform->{'c' . $course->id} == 'true') OR ($export or $save)) {
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
                                                               c.instanceid = {$course->id}";//  and u.user_subgroup like '$batch->name%'  
                if ($hidden_role_assignment != "")
                //   $query .= " and u.id NOT IN ({$hidden_role_assignment})";
                    $query .= " order by u.idnumber";

                //$cselected = array("id" => array(), "name" => array(), "students" => array());
                $cselected["id"][] = $course->id;
                $cselected["name"][] = $course->fullname;

                $cselected["shortname"][] = $course->shortname;
                $cselected["idnumber"][] = $course->idnumber;
                $cselected["students"][] = $DB->get_records_sql($query);
                $cselected["startdate"][] = $course->startdate;
                if ($export or $save)
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

        $table->head[] = 'Sec';
        $table->align[] = 'center';
        $table->size[] = '50px';
        $table->headspan[] = 1;

        $table->head[] = 'Registration No';
        $table->align[] = 'center';
        $table->size[] = '220px';
        $table->headspan[] = 1;

        $table->head[] = "Subject=>";
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
            $courseCol = $cselected['name'][$i] . "<br/>" . $max_sessions;
            /*
              $table->headspan[] = 2;
              $table->head[] = $courseCol;
              $table->align[] = 'center';
              $table->size[] = '1px';
             */
            if ($i == 0) {
                $row1 = new html_table_row();
                $row2 = new html_table_row();
                $row3 = new html_table_row();
                $cell1 = new html_table_cell();
                $cell1->text = '';
                $cell1->colspan = 1;
                $cell2 = new html_table_cell();
                $cell2->text = '';
                $cell2->colspan = 1;
                $cell3 = new html_table_cell();
                $cell3->text = '';
                $cell3->colspan = 1;
                $cell4 = new html_table_cell();
                $cell4->text = '<b>Subject Code=></b>';
                $cell4->colspan = 1;
                $cell6 = new html_table_cell();
                $cell6->text = '<b>Credit Hours=></b>';
                $cell6->colspan = 1;
                $cell7 = new html_table_cell();
                $cell7->text = '<b>Name</b>';
                $cell7->colspan = 1;
                $row1->cells = array($cell1, $cell2, $cell3, $cell4);
                $row2->cells = array($cell1, $cell2, $cell3, $cell6);
                $row3->cells = array($cell1, $cell2, $cell3, $cell7);
            }

            $coursename = explode(" ", $temp_course->fullname, 2);
            $coursenames = explode("(", $coursename[1]);
            $coursecode = $coursename[0];
            if ($temp_coursecode != $coursecode) {
                $table->headspan[] = 2;
                $table->head[] = "<a href='$CFG->wwwroot/grade/report/grader/index.php?id=".$cselected['id'][$i]."'>".trim($coursenames[0], ")")."</a>";
                $table->align[] = 'center';
                $table->size[] = '1px';
                $cell5 = new html_table_cell();
                $cell5->text = "<b>" . $coursecode . "<b>";
                $cell5->colspan = 2;
                $cell9 = new html_table_cell();
                $credithours = explode("+", $temp_course->credithours);
                $credithours = $credithours[0] + $credithours[1];
                $cell9->text = "<b>" . $credithours . "</b>";
                $cell9->colspan = 2;
                $cell10 = new html_table_cell();
                $cell10->text = "<b>GR</b>";
                $cell10->colspan = 1;
                $cell11 = new html_table_cell();
                $cell11->text = "<b>GPs</b>";
                $cell11->colspan = 1;

                $row1->cells[] = $cell5;
                $row2->cells[] = $cell9;
                $row3->cells[] = $cell10;
                $row3->cells[] = $cell11;
            }
            $temp_coursecode = $coursecode;

            foreach ($cselected["students"][$i] as $student) {
                //print_r($students["idnumber"]);
                if (!in_array($student->id, $students["userid"])) {

                    $students["userid"][] = $student->id;
                    $students["idnumber"][] = $student->idnumber;
                    $students["name"][] = $student->firstname . ' ' . $student->lastname;
                }
            }
        }
        $cell12 = new html_table_cell();
        $cell12->text = "<b>Crs</b>";
        $cell12->colspan = 1;
        $cell13 = new html_table_cell();
        $cell13->text = "<b>GPs</b>";
        $cell13->colspan = 1;
        $cell14 = new html_table_cell();
        $cell14->text = "<b>GPA</b>";
        $cell14->colspan = 1;
        $cell15 = new html_table_cell();
        $cell15->text = "<b>No of Fs</b>";
        $cell15->colspan = 1;
        $cell16 = new html_table_cell();
        $cell16->text = "<b>No of Ws</b>";
        $cell16->colspan = 1;
        $cell17 = new html_table_cell();
        $cell17->text = "<b>Prob No.</b>";
        $cell17->colspan = 1;
        $cell18 = new html_table_cell();
        $cell18->text = "";
        $cell18->colspan = 1;

        $cell19 = new html_table_cell();
        $cell19->text = "";
        $cell19->colspan = 3;
        $row1->cells[] = $cell19;
        $row1->cells[] = $cell19;

        //$row1->cells[] = $cell19;
        $row1->cells[] = $cell18;
        $row1->cells[] = $cell18;
        $row1->cells[] = $cell18;
        $row1->cells[] = $cell18;


        $row2->cells[] = $cell19;
        $row2->cells[] = $cell19;
        // $row2->cells[] = $cell19;
        $row2->cells[] = $cell18;
        $row2->cells[] = $cell18;
        $row2->cells[] = $cell18;
        $row2->cells[] = $cell18;

        $row3->cells[] = $cell12;
        $row3->cells[] = $cell13;
        $row3->cells[] = $cell14;
        $row3->cells[] = $cell12;
        $row3->cells[] = $cell13;
        $row3->cells[] = $cell14;

        $row3->cells[] = $cell15;
        $row3->cells[] = $cell16;
        $row3->cells[] = $cell17;

        $row3->cells[] = $cell18;
        $table->data = array($row1, $row2, $row3);

        $table->headspan[] = 3;
        $table->head[] = 'Semester';
        $table->align[] = 'center';
        $table->size[] = '';
        /* $table->head[] = '';
          $table->align[]='center';
          $table->size[]=''; */
        $table->headspan[] = 3;
        $table->head[] = 'Cumulative';
        $table->align[] = 'center';
        $table->size[] = '';

        $table->headspan[] = 3;
        $table->head[] = "Summary";
        $table->align[] = 'center';
        $table->size[] = '';


//        $table->headspan[] = 3;
//        $table->head[] = 'GPA';
//        $table->align[] = 'center';
//        $table->size[] = '';

        $table->headspan[] = 1;
        $table->head[] = 'Result Status';
        $table->align[] = 'center';
        $table->size[] = '';



        //echo count($students["name"]);
        //print_r($students['idnumber']);
        //var_dump($students["name"]);
        $row_ite = 0;
        $row = 5;
        for (; $row_ite < count($students["userid"]); $row_ite++) {
            $table->data[$row][] = $row_ite + 1;
            $table->data[$row][] = "";
            $table->data[$row][] = $students["idnumber"][$row_ite];
            $table->data[$row][] = $students["name"][$row_ite];
            $all_sessions_missed = 0;
            $all_sessions = 0;
            $GPA = 0;
            $GP = 0;
            $totalcredithours = 0;
            for ($j = 0; $j < count($cselected['id']); $j++) {
                $course = $DB->get_record('course', array('id' => $cselected['id'][$j]));
                $coursecodecurr = explode(" ", $cselected['name'][$j], 2);
                $coursecodecurr = $coursecodecurr[0];
		if (strstr($semestername, "Summer") == false  || strstr($batch->name, "MS") == false) {
                if ($cselected['name'][$j - 1] != "") {
                    $coursecodeprev = explode(" ", $cselected['name'][$j - 1], 2);
                    $coursecodeprev = $coursecodeprev[0];
                } else {
                    $coursecodeprev = $coursecodecurr;
                }
                if ($coursecodecurr != $coursecodeprev && $table->data[$row][$j + 3] == "") {
                    $table->data[$row][$j + 2] =  '---';
                    $table->data[$row][$j + 3] = '---';
                }
		}

                if (!key_exists($students["userid"][$row_ite], (array) $cselected["students"][$j])) {
		if (strstr($semestername, "Summer") == true  || strstr($batch->name, "MS") == true) {
			$table->data[$row][] =  '---';
                    	$table->data[$row][] = '---';
		}
                    continue;
                }
                $user_id = $students['userid'][$row_ite];
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
                $subjgrade = grade_format_gradevalue_letter($grade->finalgrade, $grade_item);
                if ($grade->finalgrade) {
                    // $grade->finalgrade= number_format(($grade->finalgrade), 0);
                    $gradepoint = 0;

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
                            $gradeno[$user_id]++;
                            break;
                        case ($subjgrade == 'I' ):
                            $gradepoint = 0;
                            break;
                    }
                    $table->data[$row][] = $subjgrade;
                    $GPs = $gradepoint * $credithours;
                    $table->data[$row][] = (number_format(($GPs), 2) == "0" ? "-" : number_format(($GPs), 2)) . "<b>";
                } else {
                    $table->data[$row][] = "-";
                    $credithours = 0;
                    $gradepoint = 0;
                    $GPs = $gradepoint * $credithours;
                    $table->data[$row][] = (number_format(($GPs), 2) == "0" ? "-" : number_format(($GPs), 2)) . "<b>";
                }
                $length = strlen($course->fullname);
                //$characters = 1;
                $start = $length - 2;
                $section = substr($course->fullname, $start, 1);
                $table->data[$row][1] = $section;
                $GP = ($gradepoint * $credithours) + $GP;
                $totalcredithours = $credithours + $totalcredithours;

                //echo $students["userid"][$i]."--".$cselected['id'][$j]."<br>";
            }
if (strstr($semestername, "Summer") == false  || strstr($batch->name, "MS") == false) {
            if ($table->data[$row][$j + 3] == "") {
                $table->data[$row][$j + 2] = '---';
                $table->data[$row][$j + 3] = '---';
            }
}
            //echo "user".$user_id." gpa ".$GPA." crd ".$totalcredithours."<br>";
            $GPA = $GP / $totalcredithours;
            $GPA = number_format(($GPA), 2);
            switch ($GPA) {
                case ($GPA == 4.0 ):
                    $gpasummary['4.00']++;
                    break;
                case ($GPA >= 3.5 && $GPA <= 3.99 ):
                    $gpasummary['3.5 ~ 3.99']++;
                    break;
                case ($GPA >= 3.00 && $GPA <= 3.49 ):

                    $gpasummary['3.00 ~ 3.49']++;
                    break;
                case ($GPA >= 2.5 && $GPA <= 2.99 ):
                    $gpasummary['2.5 ~ 2.99']++;
                    break;
                case ($GPA >= 2.00 && $GPA <= 2.49):
                    $gpasummary['2.00 ~ 2.49']++;
                    break;
                case ($GPA < 2.00 ):
                    $gpasummary['Less Than 2.00']++;
                    break;
            }
            $table->data[$row][] = $totalcredithours;
            $table->data[$row][] = (number_format(($GP), 2) == 0 ? "-" : number_format(($GP), 2)) . "<b>";
            $table->data[$row][] = $GPA . "<b>";
            $CGPA = get_CGPA($user_id, $batch);
            $CGPA = explode("-", $CGPA);
            $table->data[$row][] = $CGPA[1];
            $table->data[$row][] = number_format(($CGPA[0]), 2) . "<b>";

            $table->data[$row][] = $CGPA[2] . "<b>";
            if (!$export || $save) {
                if ($save) {                    
                    if ($form = data_submitted()) {                    
                        $record = new stdClass();
                        $record->id = $user_id;
                        $formarr = (array) $form;
                        $record->fno = array_key_exists('fno' . $user_id, $formarr) ? $formarr['fno' . $user_id] : '';
                        $record->wno = array_key_exists('wno' . $user_id, $formarr) ? $formarr['wno' . $user_id] : '';
                        $record->probno= array_key_exists('pno' . $user_id, $formarr) ? $formarr['pno' . $user_id] : '';
                        $record->status= array_key_exists('remarks' . $user_id, $formarr) ? $formarr['remarks' . $user_id] : '';
                       // print_r($record);
                        $res=$DB->update_record("user", $record);
                    }
                }
                $user_record = $DB->get_record_sql("select * from {user} where id=$user_id");
                $table->data[$row][] = '<input type="text" size="2px"   name="fno' . $user_id . '" value="' . ($user_record->fno != "" ? $user_record->fno : $gradeno[$user_id]) . '"/>';
                $table->data[$row][] = '<input  type="text"  size="2px" name="wno' . $user_id . '" value="' . ($user_record->wno != "" ? $user_record->wno : "") . '" />';
                $table->data[$row][] = '<input type="text"   size="2px" name="pno' . $user_id . '" value="' . ($user_record->probno != "" ? $user_record->probno : "") . '"/>';
                $table->data[$row][] = '<select name="remarks' . $user_id . '" ><option value="None">None</option>  <option value="Promoted" '.($user_record->status == "Promoted" ? "selected = selected" : "").')>Promoted</option><option value="Promoted on Probation"'.($user_record->status == "Promoted on Probation" ? "selected = selected" : "").')>Promoted on Probation</option><option value="Promoted with Warning" '.($user_record->status == "Promoted with Warning" ? "selected = selected" : "").')>Promoted with Warning</option><option value="Withdrawn" '.($user_record->status == "Withdrawn" ? "selected = selected" : "").')>Withdrawn</option><option value="Result Later" '.($user_record->status == "Result Later" ? "selected = selected" : "").')>Result Later
                </option>
<option value="Left" '.($user_record->status == "Left" ? "selected = selected" : "").')>Left
                </option><option value="Temp Suspended" '.($user_record->status == "Temp Suspended" ? "selected = selected" : "").')>Temp Suspended
                </option>
<option value="Other" '.($user_record->status == "Other" ? "selected = selected" : "").')>Other


                </option>';
            } elseif ($export) {
                $user_record = $DB->get_record_sql("select * from {user} where id=$user_id");
                $table->data[$row][] = $user_record->fno;
                $table->data[$row][] = $user_record->wno;
                $table->data[$row][] = $user_record->probno;
                $status = $table->data[$row][] = $user_record->status;
                switch ($status) {
                    case ($status == 'Promoted' ):
                        $promoted++;
                        $summary['Promoted']++; //=$promoted;
                        break;
                    case ($status == 'Promoted on Probation' ):
                        $promotedonprobation++;
                        $summary['Promoted on Probation']++; //=$promotedonprobation;
                        break;
                    case ($status == 'Promoted with Warning' ):
                        $promotedwithwarning++;
                        $summary['Promoted with Warning']++;
                        break;
                    case ($status == 'Withdrawn' ):
                        $withdrawn++;
                        $summary['Withdrawn']++;
                        break;
                    case ($status == 'Result Later' ):
                        $resultlater++;
                        $summary['Result Later']++;
                        break;
                    case ($status == 'Left' ):
                        $left++;
                        $summary['Left']++;
                        break;
                    case ($status == 'Temp Suspended' ):
                        $tempsuspended++;
                        $summary['Temp Suspended']++;
                        break;
                }
            }
            $row++;
        }

        //$x = (array)$cselected["students"][0];
        //echo $x[7926]->id;
        //echo key_exists(109882, $x);
        if ($export) {
            //print_table($table, true);
            //$category = get_record('course_categories', 'id', $categoryid);
            $table->category = $category->name;
            $table->schoolname = $school->name;
            $table->semester = $semester;
            $table->degbatch = $degree->name . " (" . $batch->name . ")";
            $durationend = strtotime("+$temp_course->numsections weeks", $temp_course->timemodified);
            $form = data_submitted();
            $formarr = (array) $form;
            $table->duration = array_key_exists('duration', $formarr) ? $formarr['duration'] : '';
            //$table->duration = date("d M Y", $cselected["startdate"][0]).' to '.date("d M Y", time('now'));
            ExportToExcel($table, $summary, $gpasummary);
        } else {
            /* foreach($cselected["id"] as  $courseid){
              $sql="select verified from mdl_grade_items where courseid=$courseid and itemname='Grades'";
              //echo $sql;
              $verification=$DB->get_record_sql($sql);
              if(isset($verification->verified))
              $grades_verification[]=$verification->verified;
              } */

            //print_r($cselected["id"]);
            
            echo '  <form method="post" style="display: inline; margin: 0; padding: 0;"><div style="text-align: center; font-weight: bold;"><u>' . $school->name;
            '</u><br></div>';
            echo '<div style="text-align: center; font-weight: bold;"><u>A Center of Excellence for Quality Education and Research</u></div>';
            echo' <div style="text-align: center; font-weight: bold;"><u>' . $degree->name . '(' . $batch->name . ')</u><br></div>';
            $Duration_end = strtotime("+18 weeks", $temp_course->timemodified);
            echo '<div style="text-align: center; font-weight: bold;"> ' . $semester . ' Semester Result<br/>Duration:<input type="text" size="50" name="duration" value=" ' . date(" M jS, Y", $temp_course->timemodified) . '-' . date(" M jS, Y", $Duration_end) . '"></input></div>';
            echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">
                          ';
            echo '<input type="hidden" name="courses" value="' . rtrim($export_courses, ',') . '" />';
            echo '<input type="hidden" name="sessions" value="' . rtrim($export_courses_sessions, ',') . '" />';
            echo '<input type="hidden" name="id" value="' . $categoryid . '" />';
            echo '<input type="hidden" name="export" value="true" /><input type="submit" value="Download Excel" />
                          ';   // echo '<form method="post" style="display: inline; margin: 0; padding: 0;">';
            ?>
            <?php
            // if($categoryid!=-1)
            //       require_capability('block/custom_reports:getattendancereport', $context);

            echo '</form>';
            echo '  <form method="post" style="display: inline; margin: 0; padding: 0;">';
            $Duration_end = strtotime("+18 weeks", $temp_course->timemodified);
            echo '<input type="hidden" name="courses" value="' . rtrim($export_courses, ',') . '" />';
            echo '<input type="hidden" name="sessions" value="' . rtrim($export_courses_sessions, ',') . '" />';
            echo '<input type="hidden" name="id" value="' . $categoryid . '" />';
            echo '<input type="hidden" name="save" value="true" /><input type="submit" value="Save" />';
            echo html_writer::table($table);
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

function ExportToExcel($data, $summary, $gpasummary) {
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
    $myxls->set_row(3, 20);
    $myxls->set_row(4, 20);
    $myxls->set_row(5, 20);
//	$myxls->set_column(2,3,30);
    $header1 = & $workbook->add_format();
    $header1->set_bold(1);          // Make it bold
    $header1->set_align('center');  // Align text to center
    $header1->set_size(14);
    $header1->set_v_align('vcenter');
//$header1->set_fg_color(22);

    $header4 = & $workbook->add_format();
    $header4->set_bold(1);          // Make it bold
    $header4->set_align('center');  // Align text to center
    $header4->set_size(10);
    $header4->set_v_align('vcenter');

    $header3 = & $workbook->add_format();
    $header3->set_bold(1);          // Make it bold
    $header3->set_align('center');  // Align text to center
    $header3->set_size(12);
    $header3->set_v_align('vcenter');

    $header2 = & $workbook->add_format();
    $header2->set_bold(1);            // Make it bold
    $header2->set_align('center');  // Align text to center
    $header2->set_size(12);
    $header2->set_text_wrap(1);
    $header2->set_v_align('vcenter');

    $normal = & $workbook->add_format();
    $normal->set_bold(0);
    $normal->set_align('center');
    $normal->set_v_align('vcenter');
    $normal->set_size(10);

    $normal1 = & $workbook->add_format();
    $normal1->set_bold(1);
    $normal1->set_align('center');
    $normal1->set_v_align('vcenter');

    $normal2 = & $workbook->add_format();
    $normal2->set_bold(1);
    $normal2->set_align('left');
    $normal2->set_size(10);
    $normal2->set_v_align('vcenter');

    $normal3 = & $workbook->add_format();
    $normal3->set_bold(0);
    $normal3->set_h_align('left');
    $normal3->set_v_align('vcenter');
    $normal3->set_size(10);

    $name = & $workbook->add_format();
    $name->set_bold(0);
    $name->set_size(10);
    $name->set_align('center');
    $name->set_v_align('vcenter');


    $grey_code_f = & $workbook->add_format();
    $grey_code_f->set_bold(0);            // Make it bold
    $grey_code_f->set_size(12);
    $grey_code_f->set_fg_color(22);
    $grey_code_f->set_align('center');
    $grey_code_f->set_v_align('vcenter');

//$formatbc->set_size(14);
    $myxls->write(1, 0, $data->schoolname, $header1);
    $myxls->write(2, 0, "A Center of Excellence for Quality Education and Research", $header4);
    $myxls->write(3, 0, $data->degbatch, $header3);
    $myxls->write(4, 0, $data->semester . " Semester Result " . "(" . $data->duration . ")", $header4);

    $myxls->set_column(0, 0, 8);
    $myxls->set_column(1, 1, 10);
    $myxls->set_column(3, 3, 30);
    $myxls->set_column(2, 2, 20);
    $myxls->merge_cells(3, 1, 3, 2);
    $myxls->merge_cells(3, 4, 3, 10);
    $i = 5;
    $j = 0;
    $a = 0;
    $flag1 = false;
    foreach ($data->head as $heading) {
        $heading = str_replace('<br/>', '', $heading);
        $heading = trim($heading);

        if ($heading == "Credits") {
            $flag1 = true;
        }
        if ($flag1 == true) {
            $myxls->set_column($j, $j, 5);
        }

        if ($j >= 4 && $flag1 == false) {
            $myxls->set_column($j, $j, 8);
//$myxls->merge_cells($i,$j,$i,$j+1);/////merge
        }
//	if($data->headspan[$a]==2){
//$myxls->set_column($j,$j+1,38);
//$myxls->merge_cells($i,$j,$i,$j+1);/////merge
//	}
        $myxls->write_string($i, $j, $heading, $header2);
//$myxls->set_column($pos,$pos,(strlen($grade_item->get_name()))+4);
        $col_size = strlen($heading);
        $col_size+=6;

//	if(preg_match('/^NAME/i',$heading)){
//		$col_size=39;
//	}
        $myxls->set_row(1, 15);
//	$myxls->set_column($j,$j,$col_size);
        if ($data->headspan[$a] == 2) {
            $myxls->merge_cells($i, $j, $i, $j + 1);
            $j+=2;
        }
        if ($data->headspan[$a] == 3) {
            $myxls->merge_cells($i, $j, $i + 2, $j + 2);
            $j+=3;
        }
        if ($data->headspan[$a] == 1) {
            if ($heading == "Result Status") {
                $myxls->merge_cells($i, $j, $i + 3, $j); //her
            }
            $j++;
        }
//$j++;
        $a++;
    }
    $myxls->set_row(5, 55);
    $myxls->merge_cells(1, 0, 1, $j - 1);
    $myxls->merge_cells(2, 0, 2, $j - 1);
    $myxls->merge_cells(3, 0, 3, $j - 1);
    $myxls->merge_cells(4, 0, 4, $j - 1);
    //$myxls->merge_cells(5, 0, 5, $j - 1);
// $myxls->merge_cells(4, 1, 4, 3);
    $myxls->merge_cells(5, 0, 8, 0); //here
    $myxls->merge_cells(5, 1, 8, 1);
    $myxls->merge_cells(5, 2, 8, 2);
    $i = 6;
    $j = 0;
    foreach ($data->data as $row) {
        foreach ($row as $cell) {
            foreach ($cell as $cel) {
                if ($cel->colspan == 2) {
//$myxls->merge_cells($i,$j,$i,$j+1);/////merge
                }
                if (is_numeric($cel)) {
                    $myxls->write_string($i, $j, strip_tags($cel->text), $normal1);
                } else {
                    $myxls->write_string($i, $j, strip_tags($cel->text), $normal1);
                }
                if ($cel->colspan == 2) {
                    $myxls->merge_cells($i, $j, $i, $j + 1);
                    $j+=2;
                }
                if ($cel->colspan == 3) {
                    $myxls->merge_cells($i, $j, $i, $j + 2);
                    $j+=3;
                }
                if ($cel->colspan == 1) {
                    $j++;
                }
            }
        }
        $i++;
        $j = 0;
    }
    $i = 9;
    $j = 0;
    $flag = false;
    foreach ($data->data as $row) {
        foreach ($row as $cell) {
            if ($i >= 9 && $flag == true) {
                if (is_numeric($cell)) {
                    $myxls->write_number($i, $j++, $cell, $normal);
                } else {
                    if ($j==1 ||$j==2 || $j==3 ) {
                        $myxls->write_string($i, $j++, strip_tags("$cell"), $normal3);
                    } else {
                        $myxls->write_string($i, $j++, strip_tags("$cell"), $normal);
                    }
                }
            }
        }
        $i++;
        $j = 0;
        if ($i == 12 && $flag == false) {
            $i = 9;
            $flag = true;
        }
    }
    $row = $i+=2;
    $myxls->write_string($i, 3, "Summary", $normal1);
    $i++;
    $total = 0;
    foreach ($summary as $key => $value) {
        $j = 3;
        $myxls->write_string($i, $j++, $key, $normal2);
        $myxls->write_number($i, $j++, $value, $normal2);
        $total = $total + $value;
        $i++;
    }
    $myxls->write_string($i, 3, "Total", $normal1);
    $myxls->write_number($i, 4, $total, $normal2);


    $i = $row;
    $myxls->write_string($i, 7, "GPA Wise Summary", $normal1);
    $i++;
    $total = 0;
    foreach ($gpasummary as $key => $value) {

        $j = 6;
        $myxls->merge_cells($i, $j, $i, $j + 1);

        $myxls->write_string($i, $j, $key, $normal2);
        $myxls->write_number($i, $j + 2, $value, $normal2);
        $myxls->merge_cells($i, $j - 2, $i, $j - 1);

        $total = $total + $value;
        $i++;
    }
    $myxls->merge_cells($i, $j, $i, $j + 1);

    $myxls->write_string($i, 6, "Total", $normal1);
    $myxls->write_number($i, 8, $total, $normal2);


    $workbook->close();
    exit;
}

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

function get_CGPA($user, $batch) {
    global $DB;
    $sql = "SELECT cc.*,c.* from mdl_course_categories cc, mdl_course c, mdl_user_enrolments ue JOIN mdl_enrol e ON ( e.id = ue.enrolid AND ue.userid =$user ) where c.category=cc.id and e.courseid=c.id group by cc.id order by c.startdate asc;";
    $repeatedcourses = $DB->get_records_sql($sql);
    $sql = "SELECT cc.*,c.* from mdl_course_categories cc, mdl_course c, mdl_user_enrolments ue JOIN mdl_enrol e ON ( e.id = ue.enrolid AND ue.userid =$user ) where c.category=cc.id  and path like '%/$batch->id/%' and  e.courseid=c.id order by c.startdate asc;";
    $i = 0;
    $row = 0;
    $result_list = $DB->get_records_sql($sql);

    $GP = 0;
    $k = 0;
    $semesters = sizeof($result);
    // foreach ($result as $cat) {
    $k++;

    $i++;
    $flag = true;


    $row++;

    $totalcredithours = 0;

    $j = 0;
    $keyss = 0;
    foreach ($result_list as $keys => $cat_list) {
        $j++;
        foreach ($repeatedcourses as $key => $rc) {
            if (($cat_list->fullname == $rc->fullname) && strstr($rc->path, "/$batch->id/") == false) {
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
                $credithours = 0;
            }
        }
        if (strstr($cat_list->fullname, "*") == false) {
            $GP = ($gradepoint * $credithours) + $GP;

            $totalcredithours = $credithours + $totalcredithours;
        }
        $row++;
    }
    $GPA = $GP / $totalcredithours;
    $GPA = number_format(($GPA), 2);
//        $CGP = ($GPA * $totalcredithours) + $CGP;
//        $CTotalcredithours = $totalcredithours + $CTotalcredithours;
//        $CGPA = $CGP / $CTotalcredithours;
//        $CGPA = number_format(($CGPA), 2);
    // }
    return $GP . "-" . $totalcredithours . "-" . $GPA;
}
?>
