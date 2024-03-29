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

require_once($CFG->dirroot . '/grade/export/lib.php');
require_once($CFG->dirroot.'/lib/tcpdf/tcpdf.php');





class grade_export_pdf extends grade_export {

    public $plugin = 'pdf';

    /**
     * To be implemented by child classes
     */
    public function print_grades() {
        global $CFG, $DB, $PAGE;
        require_once($CFG->dirroot . '/lib/pdflib.php');

        $doc = new pdf;

        $export_tracking = $this->track_exports();

        $strgrades = get_string('grades');
        $count = 0;
        $average = 0;
        $doc = new pdf;
        $doc->setPrintHeader(false);
        $doc->setPrintFooter(false);
		
		// set footer fonts
		$doc->setFooterFont(Array('helvetica', '', 6));

		// set margins
		//$doc->SetMargins(10, 10, 10);
		//$doc->SetHeaderMargin(0);
		//$doc->SetFooterMargin(10);
		
        $doc->AddPage();
		
		
		
        //Added By Hina Yousuf
        $doc->SetFont('helvetica', '', 8);
        $catgories = array();
        $courseid = $this->course->id;
        $category = $this->course->category;
        $course = $this->course;
        if ($category != "")
            $semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
        $semesterid = $semester->parent;
        $semestername = $semester->name;
        $path = explode("/", $semester->path);
        $semester = explode(" ", $semester->name);
        $semester = $semester[0];

        $coursename = explode(" ", $this->course->fullname, 2);
        $coursenames = explode("(", $coursename[1]);
        $coursecode = $coursename[0];
        $subject = trim($coursenames[0], ")");

        if ($path[1] != "")
            $school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]");
        if ($semesterid != "")
            $batch = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$semesterid");
        if ($batch->parent != "")
            $degree = $DB->get_record_sql("select parent,path,name from {course_categories} where id=$batch->parent");

        $tbl .= '<table border="1" align="center" >';
        $tbl .= '<tr><th colspan="4"><b>Awardlist</b></th></tr>';
        $tbl .= '<tr><th colspan="4"><b>' . $degree->name . '(' . $batch->name . ')</b> </th></tr>';
        $tbl .= '<tr><th colspan="4"><b>' . $semestername . '</b></th></tr>';

        $tbl .= '<tr>';
        $tbl .= '<th colspan="2"><b>Subject Name:</b>' . $subject . '</th>';

        $tbl .= '<th colspan="1"><b>Course Code:</b>' . $coursecode . '</th>';

        $tbl .= '<th colspan="1"><b>Credit Hours:</b> ' . $this->course->credithours . '</th>';
        $tbl .= '</tr>';



        $tbl .= '</table><br/><br/>';

        $sql1 = "SELECT firstname,lastname,u.id, username
FROM mdl_user u
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_role r ON ra.roleid = r.id
JOIN mdl_context c ON ra.contextid = c.id
WHERE r.name = 'Teacher'
AND c.contextlevel =50
AND c.instanceid=$courseid";
        $teacher = $DB->get_records_sql($sql1);
		// Show multiple teachers in pdf doc - Added by Qurrat-ul-ain (21st April, 2014)
		$i = 0;
		foreach ($teacher as $t) {
			if($i == 0) {
				$teachers = $t->firstname." ".$t->lastname;
				$teacherid = $t->id;
			}
			else
				$teachers = $teachers.", ".$t->firstname." ".$t->lastname;
				
			$i++;
		}
         ///Added By Hina Yousuf
        require_once("$CFG->dirroot/enrol/locallib.php");
        $PAGE->set_url(new moodle_url('/grade/report/grader/index.php', array('id' => $courseid)));
        $manager = new course_enrolment_manager($PAGE, $course, $filter);
        $users = $manager->get_users('id'); // email_to_user($user, get_admin(), 'Gradebook Submmmmmmitted', '', '', $attachment = '', $attachname = '', $usetrueaddress = true, $replyto = '', $replytoname = '', $wordwrapwidth = 79);
        foreach ($users as $user) {
            $roles = $manager->get_user_roles($user->id);
            foreach ($roles as $key => $role) {
                if ($key == 19) {
                    $hod = $user->firstname . " " . $user->lastname;
                }
                if ($key == 23) {
                    $shod = $user->firstname . " " . $user->lastname;
                }
                if ($key == 24) {
                    $dean = $user->firstname . " " . $user->lastname;
                }
            }
        }
        foreach ($this->columns as $grade_item) {
            $catgories[] = $grade_item->categoryid;
            $catgories[] = $grade_item->iteminstance;
        }
        $cat_array = array();
        $gtree = new grade_tree($this->course->id); //, true, $switch, $this->collapsed, $nooutcomes);

        foreach ($gtree->get_levels() as $key => $row) {
            if ($key == 0) {
                // do not display course grade category
                // continue;
            }

            foreach ($row as $columnkey => $element) {
                $type = $element['type'];
                if (!empty($element['colspan'])) {
                    $colspan = $element['colspan'];
                } else {
                    $colspan = 1;
                }
                if (!empty($element['depth'])) {
                    $catlevel = 'catlevel' . $element['depth'];
                } else {
                    $catlevel = '';
                }

                if ($type == 'category') {
                    if ($oldcatlevel != $catlevel) {
                        // echo '</tr><tr>';
                        foreach ($userprofilefields as $field) {
                            //echo '<th></th>';
                        }
                    }
                    $eid = ltrim($element['eid'], "c");
                    $colspan2 = sizeof(array_keys($catgories, $eid));
                    $path = $element['object']->path;
                    $childcatg = $DB->get_records_sql("select id from {grade_categories} where parent=$eid");
                    //echo $eid . "<br/>";
                    //print_r($childcatg);
                    $colspan3 = 0;
                    foreach ($childcatg as $chcatg) {
                        $colspan3+= sizeof(array_keys($catgories, $chcatg->id));
                    }

                    $path = $element['object']->path;
                    $path_arr = explode("/", $path);
                    $cat_id = $path_arr[count($path_arr) - 2];
                    $cat_array[$cat_id] = array('name' => $element['object']->get_name(), 'count' => 1);


                    $oldcatlevel = $catlevel;
                    $oldelement = $element;

                    // echo $colspan . shorten_text($element['object']->get_name())."--".$catlevel;
                }
            }
        }
        //end
        $tbl.= '<table align="center" border="1">';
        $tbl .= '<tr>';
        $tbl .= '<th><b> S/No.</b></th>' . '<th><b>' . get_string("fullname") . '</b></th><th><b>' . get_string("idnumber") . '</b></th>';
        foreach ($this->columns as $grade_item) {
            if ($grade_item->categoryid == "") {//Added by Hina Yousuf (To display only categories)
                $categoryname = ($this->format_column_name($grade_item) == "Course total") ? $this->format_column_name($grade_item) : $cat_array[$grade_item->iteminstance]['name'] . " Total";
                $tbl .= '<th><b>' . $categoryname . '</b></th>';
            }
            if ($this->export_feedback) {
                $tbl .= '<th><b>' . $this->format_column_name($grade_item, true) . '</b></th>';
            }
        }
        $tbl .='<th><b>' . "Final Grade" . '</b></th>';
        $tbl .= '</tr>';

        $tbl .= '<tr>';
        $tbl .= '<th></th><th></th><th></th>';
        foreach ($this->columns as $grade_item) {
            if ($grade_item->categoryid == "") {//Added by Hina Yousuf (To display only categories)
                $tbl .= '<th>' . number_format(($grade_item->grademax), 2) . ' %</th>';
            }
            if ($this->export_feedback) {
                $tbl .= '<th>' . $this->format_column_name($grade_item, true) . '</th>';
            }
        }
        $tbl .='<th>' . "" . '</th>';
        $tbl .= '</tr>';

        $i = 0;
        $geub = new grade_export_update_buffer();
        $gui = new graded_users_iterator($this->course, $this->columns, $this->groupid);
        $gui->init();

        while ($userdata = $gui->next_user()) {
            $i++;
            $user = $userdata->user;
            $sno++;
            $noofstudents++;
            $tbl .= '<tr>';
            $tbl .= '<td>' . $sno . '</td><td>' . $user->firstname . ' ' . $user->lastname . '</td><td>' . $user->idnumber . '</td>';

//            foreach ($userdata->grades as $itemid => $grade) {
//                if ($export_tracking) {
//                    $status = $geub->track($grade);
//                }
//                if ($grade->categoryid != "") {
//                    continue;
//                }
//                $gradestr = $this->format_grade($grade);
//                if (is_numeric($gradestr)) {
//                    $tbl .= '<td>' . $gradestr . '</td>';
//                } else {
//                    $tbl .= '<td>' . $gradestr . '</td>';
//                }
//
//                if ($this->export_feedback) {
//                    $tbl .= '<td>' . $this->format_feedback($userdata->feedbacks[$itemid]) . '</td>';
//                }
//            }

            foreach ($this->columns as $itemid => $unused) {
                if ($unused->categoryid != "") {
                    continue;
                }

                $gradetxt = $this->format_grade($userdata->grades[$itemid]);


                // get the status of this grade, and put it through track to get the status
                $g = new grade_export_update_buffer();
                $grade_grade = new grade_grade(array('itemid' => $itemid, 'userid' => $user->id));
                $status = $g->track($grade_grade);

                if ($this->updatedgradesonly && ($status == 'nochange' || $status == 'unknown')) {
                    $tbl .= '<td>' . get_string('unchangedgrade', 'grades') . '</td>';
                } else {
                    if ($itemid != "")
                        $grades = $DB->get_record_sql("Select * from {grade_items} where id=$itemid");

					if ($grades->itemtype == "course") {
						$gradetxt = round($gradetxt);
						// Compare the timestamps of each grade modified and print the latest - added by Qurrat-ul-ain Babar (23rd April, 2014)
						$lastmodified = $DB->get_records('grade_grades', array('itemid' => $itemid));

						 $timemod = "";
						 foreach($lastmodified as $lm) {
							$temptimemod = $lm->timemodified;
							// $usermod = $lm->usermodified;
							if($timemod == "") {
								$timemod = $temptimemod;
								$usermod = $DB->get_record('user', array('id' => $lm->usermodified));
								$username= $usermod->firstname." ".$usermod->lastname;
								$time = userdate($timemod, get_string('strftimedmy', 'attforblock'))."-".userdate($timemod, get_string('strftimehm', 'attforblock'));
								
							}
							else {
								if ($temptimemod > $timemod) {
									$timemod = $temptimemod;
									$usermod = $DB->get_record('user', array('id' => $lm->usermodified));
									$username= $usermod->firstname." ".$usermod->lastname;
									$time = userdate($timemod, get_string('strftimedmy', 'attforblock'))."-".userdate($timemod, get_string('strftimehm', 'attforblock'));
								}
							}
						}

						
						//end
					}
					if ($grade_grade->is_excluded())
						$gradetxt = $gradetxt." (E)";
						
                    $tbl .= "<td>$gradetxt</td>";
                    $gradeupdated = true;

                    if ($grades->itemtype == "course") {
                        if ($gradetxt != "-" && !$grade_grade->is_excluded() || $gradetxt == "0.000000") { // updated to add the && and || conditions - Updated bu Qurrat-ul-ain (21st April, 2014)
                            $average+= $gradetxt;
							$count++;
                        }
						
						if(!$userdata->grades[$itemid]->is_excluded()){
							$subjgrade = grade_format_gradevalue_letter($gradetxt, $grades);
							switch ($subjgrade) {
								case ($subjgrade == "A" ):
									$gpasummary['A']++;
									$tbl .= "<td>" . $subjgrade . "</td>";
									break;
								case ($subjgrade == "B+" ):
									$gpasummary['B+']++;
									$tbl .= "<td>" . $subjgrade . "</td>";
									break;
								case ($subjgrade == "B" ):
									$gpasummary['B']++;
									$tbl .= "<td>" . $subjgrade . "</td>";
									break;
								case ($subjgrade == "C+" ):
									$gpasummary['C+']++;
									$tbl .= "<td>" . $subjgrade . "</td>";
									break;
								case ($subjgrade == "C" ):
									$gpasummary['C']++;
									$tbl .= "<td>" . $subjgrade . "</td>";
									break;
								case ($subjgrade == "D" ):
									$gpasummary['D']++;
									$tbl .= "<td>" . $subjgrade . "</td>";
									break;
								case ($subjgrade == "D+" ):
									$gpasummary['D+']++;
									$tbl .= "<td>" . $subjgrade . "</td>";
									break;
								case ($subjgrade == "F" ):
									$gpasummary['F']++;
									$tbl .= "<td>".$subjgrade."</td>";
									break;
							}
						}
						else {
							$tbl .= "<td>ABS</td>";
							$gpasummary['ABS']++;
						}
						
                        /* $subjgrade = grade_format_gradevalue_letter($gradetxt, $grades);
                        switch ($subjgrade) {
                            case ($subjgrade == "A" ):
                                $gpasummary['A']++;
                                break;
                            case ($subjgrade == "B+" ):
                                $gpasummary['B+']++;
                                break;
                            case ($subjgrade == "B" ):
                                $gpasummary['B']++;
                                break;
                            case ($subjgrade == "C+" ):

                                $gpasummary['C+']++;
                                break;
                            case ($subjgrade == "C" ):

                                $gpasummary['C']++;
                                break;
                            case ($subjgrade == "D" ):
                                $gpasummary['D']++;
                                break;
                            case ($subjgrade == "D+" ):
                                $gpasummary['D+']++;
                                break;
                        }
                        if ($userdata->grades[$itemid]->is_excluded()) {
                            $tbl .= "<td>ABS</td>";
                            $gpasummary['ABS']++;
                        } else {
                            $tbl .= "<td>" . $subjgrade . "</td>";
                            if ($subjgrade == "F") {
                                $gpasummary['F']++;
                            }
                        } */
                    }
                }

                if ($this->export_feedback) {
                    $tbl .= '<td>' . $this->format_feedback($userdata->feedbacks[$itemid]) . '</td>';
                }
            }
            $tbl .= '</tr>';
        }
        $tbl .= '</table>';
        $tbl .= '<br/><br/><table align="center"  >';
        $tbl .= '<tr><td>';
        $tbl .= '<table border="1" align="center" >';
        $tbl .= '<tr><th colspan="4"><b>Summary of Grades / Grading Criteria</b></th></tr>';
        $tbl .= '<tr>';
        $tbl .= "<th><b>Grades</b></th>";
        $tbl .= "<th><b>From</b></th>";
        $tbl .= "<th><b>To</b></th>";
        $tbl .= "<th><b>No of Students</b></th>";
        $tbl .= '</tr>';
        $i++;
        $total = 0;
		// $gradez was written in place of $grades - updated by Qurrat-ul-ain Babar (10th July, 2014)
        $context = context_course::instance($grades->courseid, IGNORE_MISSING);
		
        if ($letters = grade_get_letters($context)) {
            $max = 100;
            foreach ($letters as $boundary => $letter) {
                $tbl .= '<tr>';
                $j = 0;
                //  $myxls->merge_cells($i, $j, $i, $j + 1);
                $tbl .= '<td>' . $letter . '</td>';
                $tbl .= '<td>' . format_float($boundary, 0) . ' %' . '</td>';
                ;
                $tbl .='<td>' . format_float($max, 0) . ' %' . '</td>';
                ;
                $tbl .= '<td>' . $gpasummary["$letter"] . '</td>';
                ;
                $total = $total + $gpasummary["$letter"];
                $max = $boundary - 1;
                $i++;
                $tbl .= '</tr>';
            }
        }
        $tbl .= '<tr>';
        $tbl .= '<td>' . "ABS" . '</td>';
        $tbl .= '<td  colspan="2">' . "ABSENT" . '</td>';
        $tbl .= '<td>' . $gpasummary["ABS"] . '</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td></td>';

        $tbl .= '<td colspan="2" align="right">' . "<b>Total Students  " . '</b></td>';
        $tbl .= '<td>' . $noofstudents . '</td>';
        $tbl .= '</tr>';
        $tbl .= '<tr>';
        $tbl .= '<td></td>';

        $tbl .= '<td colspan="2" align="right">' . "<b>Class Average  </b>" . '</td>';
        $tbl .='<td>' .number_format(($average / $count), 2) . '</td>';
        $tbl .= '</tr>';
        $tbl .= '</table>';
        $tbl .= '</td><td width="1%"></td><td>';

        $tbl .= '<table border="1" width="200%">';
        $tbl .= '<tr>';
        //$tbl .= "<td>Summary of Grades / Grading Criteria</td>";
        $tbl .= '<td colspan="2">Certified that I have prepared the result carefully and checked all the data entries and I have also shown the End Semester Exam answer sheets to the students.</td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td colspan="2">                      </td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td style="text-align: right; width:20%;">Instructor Sign:</td><td style="text-align: left; width: 80%;"> _____________________________________</td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td style="width: 20%;"></td><td style=" text-align: left; width: 80%;">                                              '.$teachers.'</td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td colspan="2">                      </td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td colspan="2">Certified that cross checking of Marking/Grading of at least 10% of Answer sheets has been carried out by me.</td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td colspan="2">                      </td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td colspan="2">HOD Sign:            ________________________</td>';
        $tbl .= '</tr><tr>';        
        $tbl .= '<td colspan="2">                      '.$hod.'</td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td colspan="2">  </td>';
        $tbl .= '</tr><tr>';
        $tbl .= '<td style="width: 50%; text-align: right;">Associate Dean Sign:              ______________________</td>';
       
        $schoolname = explode('(', $school->name);
        $schoolname = trim($schoolname[1], ")");
        $tbl .= '<td style="width: 50%; text-align: right;">'."Dean " . $schoolname . " Sign:                           ______________________</td>";
        $tbl .= '</tr><tr>';        
        $tbl .= '<td style="width: 50%; text-align: right;">'.$shod.'  </td><td style="width: 50%; text-align: right;">'.$dean.'</td></tr>';
		
		$tbl .= '</table>';
		$tbl .= '</td></tr>';
        $tbl .= '</table>';
		
		$doc->writeHTML($tbl, true, false, false, false, '');
		
		// Display timestamp and usermodified in footer - Added by Qurrat-ul-ain Babar (5th May, 2014)
		// Position at mm from top
        $doc->SetY(274);
		
		// Set font
        $doc->SetFont('helvetica', 'I', 6);
		
		//set style for cell border
		$line_width = (0.85 / $this->k);
		$doc->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
		
		// Page footer
		if($timemod != "")
			$doc->Cell(0, 0, 'Last Edited By:'.$username.' at '.$time, 'T', 0, 'R');
		
		

		
        $shortname = format_string($this->course->shortname, true, array('context' => get_context_instance(CONTEXT_COURSE, $this->course->id)));
        $downloadfilename = clean_filename("$shortname $strgrades.pdf");
        $doc->Output($downloadfilename, 'D');

        exit;
    }
}
