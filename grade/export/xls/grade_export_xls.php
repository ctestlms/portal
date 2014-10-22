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

class grade_export_xls extends grade_export {

    public $plugin = 'xls';

    /**
     * To be implemented by child classes
     */
    public function print_grades() {
        global $CFG, $DB, $PAGE;
		$this->sum = array();  // added by Qurrat-ul-ain (7th Jan, 2014)
        require_once($CFG->dirroot . '/lib/excellib.class.php');

        $export_tracking = $this->track_exports();

        $strgrades = get_string('grades');

        // Calculate file name
        $shortname = format_string($this->course->shortname, true, array('context' => context_course::instance($this->course->id)));
        $downloadfilename = clean_filename("$shortname $strgrades.xls");
        // Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
        // Sending HTTP lheaders
        $workbook->send($downloadfilename);
        // Adding the worksheet
        $myxls = $workbook->add_worksheet($strgrades);
        $formatbc = & $workbook->add_format();
        $formatbc->set_bold(1);
        $formatbc->set_align('center');

        $formatbc1 = & $workbook->add_format();
        $formatbc1->set_bold(1);
        $formatbc1->set_align('left');
        $formatbc1->set_text_wrap();

        $formatbc2 = & $workbook->add_format();
        $formatbc2->set_bold(1);
        $formatbc2->set_align('center');
        $formatbc2->set_text_wrap();

        $formatright = & $workbook->add_format();
        $formatright->set_bold(1);
        $formatright->set_align('right');
        // Print names of all the fields
        $profilefields = grade_helper::get_user_profile_fields($this->course->id, $this->usercustomfields);
        ///
        $gpasummary = array('A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D+' => 0, 'D' => 0, 'F' => 0, 'ABS' => 0);
        $count = 0;
        $average = 0;
        $category = $this->course->category;
        $courseid = $this->course->id;
        $course = $this->course;
        $sql1 = "SELECT firstname,lastname,u.id, username
				FROM mdl_user u
				JOIN mdl_role_assignments ra ON ra.userid = u.id
				JOIN mdl_role r ON ra.roleid = r.id
				JOIN mdl_context c ON ra.contextid = c.id
				WHERE r.name = 'Teacher'
				AND c.contextlevel =50
				AND c.instanceid=$courseid";
        $teacher = $DB->get_records_sql($sql1);
		$k = 0;
		foreach ($teacher as $t) {
			if($k == 0) {
				$teachers = $t->firstname." ".$t->lastname;
				$teacherid = $t->id;
			}
			else
				$teachers = $teachers.", ".$t->firstname." ".$t->lastname;
				
			$k++;
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
        //echo "select name,path from {course_categories} where id=$category";
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

        // if (strstr($semestername, "Summer") == false) {
        if ($semesterid != "")
            $batch = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$semesterid");
        if ($batch->parent != "")
            $degree = $DB->get_record_sql("select parent,path,name from {course_categories} where id=$batch->parent");
// $batch = $DB->get_record_sql("select id,name,path from {course_categories} where id=$semester->id");
//        $degree = $DB->get_record_sql("select name,path from {course_categories} where id=$batch->id");
//
//            $degree = $DB->get_record_sql("select id, name from {course_categories} where id=$path[3]");
//            $batch = $DB->get_record_sql("select id,name from {course_categories} where id=$path[4]");
        //   }// echo $school->name . "-" . $degree->name . "-" . $batch->name . "-" . $school->name . "";
        /* $myxls->merge_cells(0, 0, 1, 19);
        $myxls->merge_cells(1, 0, 2, 19);
        $myxls->merge_cells(2, 0, 3, 19); */
        $myxls->write_string(0, 4, "Award List", $formatbc);
        $myxls->write_string(1, 4, $degree->name . " ( " . $batch->name . " ) ", $formatbc);
        $myxls->write_string(2, 4, $semestername, $formatbc);


        $pos = count($profilefields);
        $gtree = new grade_tree($this->course->id); //, true, $switch, $this->collapsed, $nooutcomes);
        $oldcatlevel = 1;
        $i = 4;
        $catgories = array();
        foreach ($this->columns as $grade_item) {
            $catgories[] = $grade_item->categoryid;
            $catgories[] = $grade_item->iteminstance;
        }
//        print_r($catgories);
        //Added by Junaid Malik
        $cat_array = array();
        $size = sizeof($gtree->get_levels()) - 1;
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
                if ($type == 'filler' or $type == 'fillerfirst' or $type == 'fillerlast') {
                    $path1 = $oldpath;
                    $path_arr1 = explode("/", $path1);
                    $prev_filler = $path_arr1[count($path_arr1) - 3];
                    if ($found = array_search($prev_filler, $catgories)) {
                        $myxls->write_string($i, ++$pos, "");
                    }
                } else if ($type == 'category') {
                    if ($oldcatlevel != $catlevel && $oldcatlevel < $catlevel) {
                        $i++;
                        //echo '</tr><tr>';
                        foreach ($profilefields as $id => $field) {
                            $myxls->write_string($i, $id, "");
                        }
                        $pos = count($profilefields) - 1;
                    }
                    if ($oldcatlevel != $catlevel && $oldcatlevel > $catlevel) {
                        $pos++;
                        // $i--;
                        //echo '</tr><tr>';
//                        foreach ($profilefields as $id => $field) {
//                            $myxls->write_string($i, $id, "");
//                        }
//                        $pos = count($profilefields) - 1;
                    }

                    $eid = ltrim($element['eid'], "c");
                    $colspan2 = sizeof(array_keys($catgories, $eid));
                    $childcatg = $DB->get_records_sql("select id from {grade_categories} where parent=$eid");
                    $colspan3 = 0;
                    foreach ($childcatg as $chcatg) {
                        $colspan3+= sizeof(array_keys($catgories, $chcatg->id));
                    }


                    if ($element['depth'] < $size && $element['depth'] > 1) {
                        if (!$found = array_search($eid, $catgories)) {
                            $colspan3 = $colspan3 - 1;
                            //$pos=$pos-1;
                        }
                        if ($colspan3 >= 0) {
                            $myxls->merge_cells($i, ++$pos, $i, $pos + $colspan3);
                            $myxls->write_string($i, $pos, shorten_text($element['object']->get_name()), $formatbc);
                            $pos+=$colspan3;
                        }
                    } elseif ($element['depth'] == $size && $colspan2 > 0) {
                        $myxls->merge_cells($i, ++$pos, $i, $pos + $colspan2 - 1);
                        $myxls->write_string($i, $pos, shorten_text($element['object']->get_name()), $formatbc2);
                        $pos+=$colspan2 - 1;
                    }

//                    $myxls->merge_cells($i, ++$pos, $i, $pos + $colspan - 1);
//                    $myxls->write_string($i, $pos, shorten_text($element['object']->get_name()), $formatbc);
//                    $pos+=$colspan - 1;
                    //Added by Junaid Malik /////////////////////////////
                    $path = $element['object']->path;

                    $path_arr = explode("/", $path);
                    $cat_id = $path_arr[count($path_arr) - 2];
                    $cat_array[$cat_id] = array('name' => $element['object']->get_name(), 'count' => 1);

                    $oldcatlevel = $catlevel;
                    $oldpath = $element['object']->path;
                    // echo $colspan . shorten_text($element['object']->get_name())."--".$catlevel;
                }
            }
        }
        // echo '</tr>';
        ///
        $i++;

//        foreach ($profilefields as $id => $field) {
//            $myxls->write_string($i, $id, "");
//        }
        $myxls->set_column(0, 0, 8);
        $myxls->set_column(1, 2, 25);
        $myxls->set_column(3, 30, 8);
        $myxls->write_string(4, 0, "Subject  Name:", $formatbc1);
        //$myxls->set_row(4, 35);
        $myxls->write_string(4, 1, $subject, $formatbc1);
        $myxls->write_string(4, 2, "Course Code: ".$coursecode, $formatbc1);
		$myxls->write_string($i, 0, "Credit Hours:", $formatbc1);
        $myxls->set_row($i, 35);
        $myxls->write_string($i, 1, $this->course->credithours, $formatbc1);
        $myxls->write_string($i, 2, "", $formatbc1);

        $pos = count($profilefields);

        foreach ($this->columns as $grade_item) {
            $myxls->write_string($i, $pos++, number_format(($grade_item->grademax), 2), $formatbc);

            // Add a column_feedback column
            if ($this->export_feedback) {
                $myxls->write_string($i, $pos++, "");
            }
        }
        $myxls->write_string($i, $pos++, "", $formatbc);
        $i++;
        $myxls->set_row($i, 35);
        foreach ($profilefields as $id => $field) {
            if ($field->shortname == "lastname") {

                continue;
            }
            $myxls->write_string($i, 0, "S/No", $formatbc);
            if ($field->shortname == "firstname") {
                $myxls->write_string($i, 1, "Fullname", $formatbc);
                continue;
            } else {
                $myxls->write_string($i, $id, $field->fullname, $formatbc);
            }
        }
        $pos = count($profilefields);

        foreach ($this->columns as $grade_item) {
            // Added by Junaid Malik///////////////////////////////////
            $name = substr($cat_array[$grade_item->categoryid]['name'], 0, 1);
            if ($name != '')
                $count = $cat_array[$grade_item->categoryid]['count']++;
            else
                $count = '';
            //////////////////////////////////////////////////////////
            if ($grade_item->categoryid == "") {//Added by Hina Yousuf (To display only categories)
                $categoryname = ($this->format_column_name($grade_item) == "Course total") ? $this->format_column_name($grade_item) : $cat_array[$grade_item->iteminstance]['name'] . " Total";
                $myxls->write_string($i, $pos++, $categoryname, $formatbc2);
            } else {
                $myxls->write_string($i, $pos++, $name . '' . $count, $formatbc2);
            }
            // Add a column_feedback column
            if ($this->export_feedback) {
                $myxls->write_string($i, $pos++, $this->format_column_name($grade_item, true), $formatbc2);
            }
        }
        $myxls->write_string($i, $pos++, "Final Grade", $formatbc2);
        // Print all the lines of data.
        $i++;
        $geub = new grade_export_update_buffer();
        $gui = new graded_users_iterator($this->course, $this->columns, $this->groupid);
        $gui->require_active_enrolment($this->onlyactive);
        $gui->allow_user_custom_fields($this->usercustomfields);
        $gui->init();
        $sno = 1;
		//$noofstudentsforavg = 0;
        while ($userdata = $gui->next_user()) {
            $i++;
            $myxls->write_number($i, 0, $sno, $formatbc);
            $sno++;
            $noofstudents++;
			//$noofstudentsforavg=0;
			
            $user = $userdata->user;

            foreach ($profilefields as $id => $field) {
                $fieldvalue = grade_helper::get_user_field_value($user, $field);
                if ($field->shortname == "firstname") {
                    $firstname = $fieldvalue;
                    continue;
                }
                if ($field->shortname == "lastname") {
                    $myxls->write_string($i, 1, $firstname . " " . $fieldvalue);
                } else {
                    $myxls->write_string($i, $id, $fieldvalue);
                }
            }
            $j = count($profilefields);

            foreach ($userdata->grades as $itemid => $grade) {
				$noofstudentsforavg=0;
				
                if ($export_tracking) {
                    $status = $geub->track($grade);
                }

                $gradestr = $this->format_grade($grade);
                $gradez = $DB->get_record_sql("Select * from {grade_items} where id=$itemid");
				if ($gradez->itemtype == "course") {
					$gradestr = round($gradestr);
					
					
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

                if (is_numeric($gradestr)) {
					if ($grade->is_excluded()) {
						$myxls->write_string($i, $j++, $gradestr."(E)");
					}
					else {
						$myxls->write_string($i, $j++, number_format($gradestr, 2));
						$noofstudentsforavg++;
					}
                    // $myxls->write_string($i, $j++, number_format($gradestr, 2));
					// $noofstudentsforavg++;
                    //if ($gradez->itemtype == "course") {
                    //   $subjgrade = grade_format_gradevalue_letter($gradestr, $gradez);
                    //   $myxls->write_string($i, $j++, $subjgrade);
                    //  }
                } else {
                    $myxls->write_string($i, $j++, $gradestr);
                }
                if ($gradez->itemtype == "course") {
                    if ($gradestr != "-" && !$grade->is_excluded() || $gradestr == "0.00000") { // updated to add the AND condition - added by Qurrat-ul-ain (7th Jan, 2014)
                        $average+= $gradestr;
                        $count++;
                    }
					
					if(!$grade->is_excluded()){
						$subjgrade = grade_format_gradevalue_letter($gradestr, $gradez);
						switch ($subjgrade) {
							case ($subjgrade == "A" ):
								$gpasummary['A']++;
								$myxls->write_string($i, $j++, $subjgrade);
								break;
							case ($subjgrade == "B+" ):
								$gpasummary['B+']++;
								$myxls->write_string($i, $j++, $subjgrade);
								break;
							case ($subjgrade == "B" ):
								$gpasummary['B']++;
								$myxls->write_string($i, $j++, $subjgrade);
								break;
							case ($subjgrade == "C+" ):
								$gpasummary['C+']++;
								$myxls->write_string($i, $j++, $subjgrade);
								break;
							case ($subjgrade == "C" ):
								$gpasummary['C']++;
								$myxls->write_string($i, $j++, $subjgrade);
								break;
							case ($subjgrade == "D" ):
								$gpasummary['D']++;
								$myxls->write_string($i, $j++, $subjgrade);
								break;
							case ($subjgrade == "D+" ):
								$gpasummary['D+']++;
								$myxls->write_string($i, $j++, $subjgrade);
								break;
							case ($subjgrade == "F" ):
								$gpasummary['F']++;
								$myxls->write_string($i, $j++, $subjgrade);
								break;
						}
					}
					else {
						$myxls->write_string($i, $j++, "ABS");
                        $gpasummary['ABS']++;
					}
					
                    /* $subjgrade = grade_format_gradevalue_letter($gradestr, $gradez);
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
                    if ($grade->is_excluded()) {
                        $myxls->write_string($i, $j++, "ABS");
                        $gpasummary['ABS']++;
                    } else {
                        $myxls->write_string($i, $j++, $subjgrade);
                        if ($subjgrade == "F") {
                            $gpasummary['F']++;
                        }
                    } */
                }
				
				// To calculate the average and display in exported excel - added by Qurrat-ul-ain (7th Jan, 2014)
				//if (!$grade->is_excluded()) {
				if (array_key_exists($itemid, $this->sum)) {
					if ($grade->is_excluded()) {
						$this->sum[$itemid]->grade = $this->sum[$itemid]->grade + 0;
						$this->sum[$itemid]->noofstudents = $this->sum[$itemid]->noofstudents;
					}
					else {
						$this->sum[$itemid]->grade = $this->sum[$itemid]->grade + $gradestr;
						$this->sum[$itemid]->noofstudents = $this->sum[$itemid]->noofstudents + $noofstudentsforavg;
					}
				}
				else {
					$this->sum[$itemid]->itemid = $itemid;
					if ($grade->is_excluded())
						$this->sum[$itemid]->grade = 0;
					else
						$this->sum[$itemid]->grade = $gradestr;
						$this->sum[$itemid]->noofstudents = $noofstudentsforavg;
				}
				//}

                // writing feedback if requested
                if ($this->export_feedback) {
                    $myxls->write_string($i, $j++, $this->format_feedback($userdata->feedbacks[$itemid]));
                }
            }
        }
		// display averages of all columns in exported excel - added by Qurrat-ul-ain (7th Jan, 2014)
		$k = 2;
		//print_r($this->sum);
		$myxls->write_string($i+1, $k, "Overall Average", $formatbc);
		$counter = 0;
		foreach ($this->sum as $sumitem) {
			$counter++;
			$coltotal = $sumitem->grade;
			$colnoofstudents = $sumitem->noofstudents;
			//echo "$i-----Grade from array sum: ".$total."<br/>";
			
			$colaverage = $coltotal/$colnoofstudents;
			//echo "Average : ".$average."<br/>";
            $myxls->write_string($i+1, $k+$counter, number_format($colaverage,2), $formatbc);
        }
		
        $i+=4;
        $myxls->write_string($i, 1, "Summary of Grades / Grading Criteria", $formatbc);
        $myxls->write_string($i, 6, "Certified that I have prepared the result carefully and checked all the data entries and I have also shown the End Semester Exam answer sheets to the students.", $formatbc1);
        $myxls->write_string($i + 3, 6, "Instructor Sign: _______________________________________________________________________", $formatbc1);
        $myxls->write_string($i + 4, 6, "                          ".$teachers, $formatbc1);
        $myxls->write_string($i + 6, 6, "Certified that cross checking of Marking/Grading of at least 10% of Answer sheets has been carried out by me.", $formatbc1);
        $myxls->write_string($i + 8, 6, "HOD Sign      ________________________", $formatbc1);
        $myxls->write_string($i + 9, 6, "$hod", $formatbc1);
        $myxls->write_string($i + 11, 6, "Associate Dean Sign:__________________", $formatbc1);
        $myxls->write_string($i + 12, 6, "$shod", $formatbc1);
        $schoolname = explode('(', $school->name);
        $schoolname = trim($schoolname[1], ")");

        $myxls->write_string($i + 11, 11, "Dean $schoolname Sign:_________________", $formatbc1);
        $myxls->write_string($i + 12, 11, "$dean", $formatbc1);

        $myxls->merge_cells($i, 6, $i + 1, 16);
        $myxls->merge_cells($i + 3, 6, $i + 3, 16);
        $myxls->merge_cells($i + 4, 6, $i + 4, 16);
        $myxls->merge_cells($i + 6, 6, $i + 7, 16);
        $myxls->merge_cells($i + 8, 6, $i + 8, 16);
        $myxls->merge_cells($i + 9, 6, $i + 9, 16);
        $myxls->merge_cells($i + 11, 6, $i + 11, 10);
        $myxls->merge_cells($i + 12, 6, $i + 12, 10);
        $myxls->merge_cells($i + 11, 11, $i + 11, 16);
        $myxls->merge_cells($i + 12, 11, $i + 12, 16);
        $myxls->merge_cells($i, 1, $i + 1, 4);
        $i++;
        $myxls->write_string($i+1, 1, "Grades", $formatbc);
        $myxls->write_string($i+1, 2, "From", $formatbc);
        $myxls->write_string($i+1, 3, "To", $formatbc);
        $myxls->write_string($i+1, 4, "No of Students", $formatbc);
        $i++;
        $total = 0;
        $context = context_course::instance($gradez->courseid, IGNORE_MISSING);
        if ($letters = grade_get_letters($context)) {
            $max = 100;
            foreach ($letters as $boundary => $letter) {
                $j = 0;
                //  $myxls->merge_cells($i, $j, $i, $j + 1);
                $myxls->write_string($i+1, $j + 1, $letter, $formatbc);
                $myxls->write_number($i+1, $j + 2, format_float($boundary, 0) . ' %', $formatbc);
                $myxls->write_number($i+1, $j + 3, format_float($max, 0) . ' %', $formatbc);
                $myxls->write_number($i+1, $j + 4, $gpasummary["$letter"], $formatbc);
                $total = $total + $gpasummary["$letter"];
                $max = $boundary - 1;
                $i++;
            }
        }
		$i++;
        $myxls->write_string($i, $j + 1, "ABS", $formatbc);
        $myxls->write_string($i, $j + 2, "ABSENT", $formatbc);
        //$myxls->merge_cells($i, $j + 2, $i, $j + 3);
        $myxls->write_number($i, $j + 4, $gpasummary["ABS"], $formatbc);
        $i++;
        $myxls->merge_cells($i, $j + 2, $i, $j + 2);
        $myxls->merge_cells($i, 2, $i, 3);


        $myxls->write_string($i, 2, "Total Students: ", $formatright);
        $myxls->write_number($i++, 4, $noofstudents, $formatbc);
        //$myxls->merge_cells($i, $j + 2, $i, $j + 2);
        $myxls->merge_cells($i, 2, $i, 3);
        $myxls->write_string($i, 2, "Class Average: ", $formatright);
        $myxls->write_number($i, 4, number_format(($average / $count), 2), $formatbc);
	// Add timestamp to exported excel grade sheet - added by Qurrat-ul-ain (22nd April, 2014)
		if($timemod != "")
			$myxls->write_string($i+2, 1, "Last Edited By: $username at $time", $formatbc);
		$myxls->merge_cells($i+2, 1, $i+2, 3);
		$gui->close();
        $geub->close();

        /// Close the workbook
        $workbook->close();

        exit;
    }

}

