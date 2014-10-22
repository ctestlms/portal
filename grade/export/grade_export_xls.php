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
        global $CFG, $DB;
        require_once($CFG->dirroot . '/lib/excellib.class.php');

        $export_tracking = $this->track_exports();

        $strgrades = get_string('grades');

        // Calculate file name
        $shortname = format_string($this->course->shortname, true, array('context' => context_course::instance($this->course->id)));
        $downloadfilename = clean_filename("$shortname $strgrades.xls");
        // Creating a workbook
        $workbook = new MoodleExcelWorkbook("-");
        // Sending HTTP headers
        $workbook->send($downloadfilename);
        // Adding the worksheet
        $myxls = $workbook->add_worksheet($strgrades);
        $formatbc = & $workbook->add_format();
        $formatbc->set_bold(1);
        $formatbc->set_align('center');
        // Print names of all the fields
        $profilefields = grade_helper::get_user_profile_fields($this->course->id, $this->usercustomfields);
        ///

        $category = $this->course->category;
        //echo "select name,path from {course_categories} where id=$category";
        $semester = $DB->get_record_sql("select name,path from {course_categories} where id=$category");
        $path = explode("/", $semester->path);
        $semester = explode(" ", $semester->name);
        $semester = $semester[0];

        $coursename = explode(" ", $this->course->fullname, 2);
        $coursenames = explode("(", $coursename[1]);
        $coursecode = $coursename[0];
        $subject = trim($coursenames[0], ")");

        $school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]");
        $degree = $DB->get_record_sql("select id, name from {course_categories} where id=$path[3]");
        $batch = $DB->get_record_sql("select id,name from {course_categories} where id=$path[4]");
        // echo $school->name . "-" . $degree->name . "-" . $batch->name . "-" . $school->name . "";
        $myxls->write_string(0, 1, "Award List", $formatbc);
        $myxls->write_string(1, 2, $degree->name . " ( " . $batch->name . " ) ", $formatbc);
        $myxls->write_string(2, 1, $semester . " Semester", $formatbc);

        // foreach ($profilefields as $id => $field) {
        $myxls->write_string(3, 1, "Subject Name", $formatbc);
        $myxls->write_string(3, 2, $subject, $formatbc);
        $myxls->write_string(3, 4, "Course Code:" . $coursecode, $formatbc);
        // }
        $pos = count($profilefields);
        $gtree = new grade_tree($this->course->id); //, true, $switch, $this->collapsed, $nooutcomes);
        $oldcatlevel = 1;
        $i = 3;
        $catgories = array();
        foreach ($this->columns as $grade_item) {
            $catgories[] = $grade_item->categoryid;
        }
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
                        $i++;
                        //echo '</tr><tr>';
                        foreach ($profilefields as $id => $field) {
                            $myxls->write_string($i, $id, "");
                        }
                        $pos = count($profilefields);
                    }
                   
                    $eid = ltrim($element['eid'], "c");
                    $colspan2 = sizeof(array_keys($catgories, $eid));
                     
                    // echo "ffff"."---".$colspan."size".sizeof(array_keys($catgories,$eid));
                    // print_r( array_keys($catgories,$eid));
                    if ($catlevel == 'catlevel1') {
                        $myxls->merge_cells($i, ++$pos, $i, $pos + $colspan - 1);
                        $myxls->write_string($i, $pos, shorten_text($element['object']->get_name()), $formatbc);
                        $pos+=$colspan - 1;
                    }
                    if ($found = array_search($eid, $catgories) && $catlevel != 'catlevel1') {
                        $myxls->merge_cells($i, ++$pos, $i, $pos + $colspan2 );
                        $myxls->write_string($i, $pos, shorten_text($element['object']->get_name()), $formatbc);
                        $pos+=$colspan2  ;
                    }


//echo "<th align='center' colspan=$colspan>" . shorten_text($element['object']->get_name()) . $colspan."</th>";

                    $oldcatlevel = $catlevel;
                    // echo $colspan . shorten_text($element['object']->get_name())."--".$catlevel;
                }
            }
        }
        // echo '</tr>';
        ///
        $i++;

        foreach ($profilefields as $id => $field) {
            $myxls->write_string($i, $id, "");
        }
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
        foreach ($profilefields as $id => $field) {
            $myxls->write_string($i, $id, $field->fullname, $formatbc);
        }
        $pos = count($profilefields);

        foreach ($this->columns as $grade_item) {
            $myxls->write_string($i, $pos++, $this->format_column_name($grade_item), $formatbc);

            // Add a column_feedback column
            if ($this->export_feedback) {
                $myxls->write_string($i, $pos++, $this->format_column_name($grade_item, true), $formatbc);
            }
        }
        $myxls->write_string($i, $pos++, "Final Grade", $formatbc);
        // Print all the lines of data.
        $i++;
        $geub = new grade_export_update_buffer();
        $gui = new graded_users_iterator($this->course, $this->columns, $this->groupid);
        $gui->require_active_enrolment($this->onlyactive);
        $gui->allow_user_custom_fields($this->usercustomfields);
        $gui->init();
        while ($userdata = $gui->next_user()) {
            $i++;
            $user = $userdata->user;

            foreach ($profilefields as $id => $field) {
                $fieldvalue = grade_helper::get_user_field_value($user, $field);
                $myxls->write_string($i, $id, $fieldvalue);
            }
            $j = count($profilefields);

            foreach ($userdata->grades as $itemid => $grade) {
                if ($export_tracking) {
                    $status = $geub->track($grade);
                }

                $gradestr = $this->format_grade($grade);
                $gradez = $DB->get_record_sql("Select * from {grade_items} where id=$itemid");
                if (is_numeric($gradestr)) {
                    $myxls->write_number($i, $j++, $gradestr);
                    if ($gradez->itemtype == "course") {
                        $subjgrade = grade_format_gradevalue_letter($gradestr, $gradez);
                        $myxls->write_string($i, $j++, $subjgrade);
                    }
                } else {
                    $myxls->write_string($i, $j++, $gradestr);
                }

                // writing feedback if requested
                if ($this->export_feedback) {
                    $myxls->write_string($i, $j++, $this->format_feedback($userdata->feedbacks[$itemid]));
                }
            }
        }
        $gui->close();
        $geub->close();

        /// Close the workbook
        $workbook->close();

        exit;
    }

}

