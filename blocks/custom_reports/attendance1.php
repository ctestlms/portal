<?php
	
	require_once('../../config.php');  	
	require_once($CFG->libdir.'/blocklib.php');
	require_once($CFG->libdir.'/formslib.php');
	require_once('./view_attendance_report_form.php');
	require_once('../../mod/attforblock/locallib.php');
	
	
	require_login($course->id);
	session_start();
	

	$categoryid = optional_param('id', '-1', PARAM_INT);

	$export = optional_param('export',false, PARAM_BOOL);
	
	$context = get_context_instance(CONTEXT_COURSECAT, $categoryid);
	
	
		
	if($categoryid!=-1)
		require_capability('block/custom_reports:getattendancereport', $context);
	
	$navlinks[] = array('name' => get_string('attendance_custom_reports', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
	$navigation = build_navigation($navlinks);		
	
	if(!$export)
		print_header('Attendance Custom Report', 'Attendance Custom Report', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
	
	if($courses = get_courses($categoryid, '', 'c.id, c.fullname, c.startdate, c.idnumber, c.shortname') or $export){
		$mform = new mod_custom_reports_view_attendance_report_form('attendance.php', array('courses'=>$courses, 'categoryid'=>$categoryid));
		if($fromform = $mform->get_data() or $export){
			$cselected = array();
			if($export){
				$export_courses = required_param('courses');
				$sessions_margin = array_reverse(explode(",", required_param('sessions')));
				
				//echo "select id, fullname, startdate, idnumber, shortname from {$CFG->prefix}courses where id IN ({$export_courses})";
				$courses = $DB->get_records_sql("select id, fullname, startdate, idnumber, shortname from {course} where id IN ({$export_courses})");
				
				$export_courses_sessions = "";
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
                                       if($hidden_role_assignment != "")
                                            //   $query .= " and u.id NOT IN ({$hidden_role_assignment})";
                                       $query .= " order by u.firstname";

					//$cselected = array("id" => array(), "name" => array(), "students" => array());
					$cselected["id"][] = $course->id;
					$cselected["name"][] = $course->fullname;
					
					$cselected["shortname"][] = $course->shortname;
					$cselected["idnumber"][] =  $course->idnumber;
					$cselected["students"][] =  $DB->get_records_sql($query);
					$cselected["startdate"][] = $course->startdate;
					if($export)
						$cselected["margin"][] = array_pop($sessions_margin);
					else
						$cselected["margin"][] = $fromform->{'session'.$course->id};
				
			}
				if($fromform->{'c'.$course->id}=='true' and !$export){
					$export_courses .= $course->id.",";
					$export_courses_sessions .= $fromform->{'session'.$course->id}.",";
				}
				
			}
			
			//$table->width = '80%';
			//$table->tablealign =  'center';
			//$table->cellpadding = '5px';
			$table = new html_table();
			$table->head = array();
			//$table->align = array();
			//$table->size = array();
			
			$table->head[] = 'S.No';
			$table->align[] = 'center';
			$table->size[] = '';
			
			$table->head[] = 'Registration No';
			$table->align[] = 'center';
			$table->size[] = '';
			
			$table->head[] = 'Name';
			$table->align[] = 'left';
			$table->size[] = '';
			
			//$students = array("idnumber" => array(), "name" => array());
			
			$user_sessions=array();
			$TotalSessions=0;
			for($i=0; $i<count($cselected["id"]); $i++){
				$temp_stu = array_slice($cselected["students"][$i],0,1);
				$temp_course = $DB->get_record('course', array('id'=> $cselected['id'][$i]));
				$temp_course->attendance_margin = $cselected['margin'][$i];
				//$max_sessions=get_maxgrade($temp_stu[0]->id,$temp_course);
				$courseCol = $cselected['name'][$i]."<br/>".$max_sessions;
				$table->head[] = $courseCol;
				$table->align[] = 'center';
				$table->size[] = '1px';
			
		
				foreach($cselected["students"][$i] as $student){
					
					
					//print_r($students["idnumber"]);
					if(!in_array($student->id, $students["userid"])){
						
						$students["userid"][] = $student->id;
						$students["idnumber"][] = $student->idnumber;
						$students["name"][] = $student->firstname.' '.$student->lastname;
					}
				}
			}
			$table->head[] = 'Total Sessions';
			$table->align[]='center';
			$table->size[]='';	
			
			$table->head[] = 'Total Absents';
                        $table->align[]='center';
                        $table->size[]='';

			
			$table->head[] = 'Cummulative Absentees(%)';
			$table->align[] = 'center';
			$table->size[] = '';

			//echo count($students["name"]);
			//print_r($students['idnumber']);
			//var_dump($students["name"]);
			$row_ite=0;
			for(; $row_ite<count($students["userid"]); $row_ite++){
				$table->data[$row_ite][] = $row_ite+1;
				$table->data[$row_ite][] = $students["idnumber"][$row_ite];
				$table->data[$row_ite][] = $students["name"][$row_ite];
				$all_sessions_missed=0;
				$all_sessions=0;
				for($j=0; $j<count($cselected['id']); $j++){
					$course = $DB->get_record('course', array('id'=> $cselected['id'][$j]));
					
					$course->attendance_margin = $cselected['margin'][$j];
					if(!key_exists($students["userid"][$row_ite], (array)$cselected["students"][$j])){
						$table->data[$row_ite][] = '---';
						continue;
					}
					$attendance = get_percent_absent($students["userid"][$row_ite], $course);
					$course_sess_att = get_grade($students["userid"][$row_ite],$course);
					$course_sessions = get_maxgrade($students["userid"][$row_ite],$course);
					$all_sessions+=$course_sessions;
					$course_sess_missed = $course_sessions - $course_sess_att;
					$all_sessions_missed+=$course_sess_missed;
					if($attendance > 25.99){
						if(!$export){
							$table->data[$row_ite][] = '<div style="background: #aaa;">'.$attendance.'%('.$course_sess_missed.')</div>';
						}
						else{
							$table->data[$row_ite][] = '!!'.$attendance.'%('.$course_sess_missed.')';
						}
					}
					else{
						$table->data[$row_ite][] = $attendance.'%('.$course_sess_missed.')';
					}
					//echo $students["userid"][$i]."--".$cselected['id'][$j]."<br>";
				}
				$table->data[$row_ite][]=$all_sessions;
				$table->data[$row_ite][]=$all_sessions_missed;
				$cum_abs = round(($all_sessions_missed/$all_sessions)*100,2);
				if($cum_abs > 25.99){
					if(!$export){
						$table->data[$row_ite][] = '<div style="background: #aaa;">'.$cum_abs.'%</div>';
					}
					else{
						$table->data[$row_ite][] = '!!'.$cum_abs.'%';
					}
				}
                                else{
                                        $table->data[$row_ite][] = $cum_abs.'%';
				}

			}
			
			//$x = (array)$cselected["students"][0];
			//echo $x[7926]->id;
			//echo key_exists(109882, $x);
			if($export){
				//print_table($table, true);
				//$category = get_record('course_categories', 'id', $categoryid);
				$table->category = $category->name;
				$table->duration = date("d M Y", $cselected["startdate"][0]).' to '.date("d M Y", time('now'));
				ExportToExcel($table);
			}
			else{
				//print_r($cselected["id"]);
				echo '<div style="text-align: center; font-weight: bold;">ATTENDANCE SUMMARY ____________________<br>ABSENTEES RECORDS (Percentage)</div>';
				echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">
							<form method="post" style="display: inline; margin: 0; padding: 0;">';
				echo 			'<input type="hidden" name="courses" value="'.rtrim($export_courses, ',').'" />';
				echo 			'<input type="hidden" name="sessions" value="'.rtrim($export_courses_sessions, ',').'" />';
				echo 			'<input type="hidden" name="id" value="'.$categoryid.'" />';	
				echo 			'<input type="hidden" name="export" value="true" /><input type="submit" value="Download Excel" />
							</form>
							<span style="text-align: left; padding-left: 20px; text-decoration: underline;">Duration :- '.date("d M Y", $cselected["startdate"][0]).' to '.date("d M Y", time('now')).'</span></div>';
				
		if($categoryid!=-1)
		require_capability('block/custom_reports:getattendancereport', $context);
	
				 echo html_writer::table($table);

			}			
			exit();
		}else
			$mform->display();
	}else{
		$OUTPUT->box_start('generalbox categorybox');
		print_whole_category_list2(NULL, NULL, NULL, -1, false);
		$OUTPUT->box_end();
	}	
	echo $OUTPUT->footer();

//================Export to Excel================//	
function ExportToExcel($data) {
	global $CFG;

    //require_once("$CFG->libdir/excellib.class.php");/*
    require_once($CFG->dirroot.'/lib/excellib.class.php');
   $filename = "Attendance_report:.xls";
   
   $workbook = new MoodleExcelWorkbook("-");
/// Sending HTTP headers
    ob_clean();
    $workbook->send($filename);
/// Creating the first worksheet
    $myxls =& $workbook->add_worksheet('Attendances');
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
	$myxls->write(1, 0, "ATTENDANCE SUMMARY",$header1);
	$myxls->write(2, 0, "ABSENTEES RECORD (Percentage)",$header2);
    $myxls->write(4, 0, 'Duration');
    $myxls->write(4, 1, $data->duration, $formatbc);

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
	$myxls->merge_cells(1,0,1,$j-1);
        $myxls->merge_cells(2,0,2,$j-1);
        $myxls->merge_cells(4,1,4,3);

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
    global $CFG, $DB, $OUTPUT;

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
/*
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function print_whole_category_list2($category=NULL, $displaylist=NULL, $parentslist=NULL, $depth=-1, $showcourses = true) {
/// Recursive function to print out all the categories in a nice format
/// with or without courses included
    global $CFG;

    // maxcategorydepth == 0 meant no limit
    if (!empty($CFG->maxcategorydepth) && $depth >= $CFG->maxcategorydepth) {
        return;
    }

    if (!$displaylist) {
        make_categories_list($displaylist, $parentslist);
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

    if ($categories = get_child_categories($category->id)) {   // Print all the children recursively
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

function print_category_info2($category, $depth, $showcourses = false) {
/// Prints the category info in indented fashion
/// This function is only used by print_whole_category_list() above

    global $CFG,$DB;
    static $strallowguests, $strrequireskey, $strsummary;

    if (empty($strsummary)) {
        $strallowguests = get_string('allowguests');
        $strrequireskey = get_string('requireskey');
        $strsummary = get_string('summary');
    }

    $catlinkcss = $category->visible ? '' : ' class="dimmed" ';

    static $coursecount = null;
    if (null === $coursecount) {
        // only need to check this once
        $coursecount = $DB->count_records('course') <= FRONTPAGECOURSELIMIT;
    }

    if ($showcourses and $coursecount) {
        $catimage = '<img src="'.$CFG->pixpath.'/i/course.gif" alt="" />';
    } else {
        $catimage = "&nbsp;";
    }

    echo "\n\n".'<table class="categorylist">';

    $courses = get_courses($category->id, 'c.sortorder ASC', 'c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.password,c.summary,c.guest,c.cost,c.currency');
    if ($showcourses and $coursecount) {

        echo '<tr>';

        if ($depth) {
            $indent = $depth*30;
            $rows = count($courses) + 1;
            echo '<td class="category indentation" rowspan="'.$rows.'" valign="top">';
            print_spacer(10, $indent);
            echo '</td>';
        }

        echo '<td valign="top" class="category image">'.$catimage.'</td>';
        echo '<td valign="top" class="category name">';
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/mod/custom_reports/attendance?id='.$category->id.'">'. format_string($category->name).'</a>';
        echo '</td>';
        echo '<td class="category info">&nbsp;</td>';
        echo '</tr>';

        // does the depth exceed maxcategorydepth
        // maxcategorydepth == 0 or unset meant no limit

        $limit = !(isset($CFG->maxcategorydepth) && ($depth >= $CFG->maxcategorydepth-1));

        if ($courses && ($limit || $CFG->maxcategorydepth == 0)) {
            foreach ($courses as $course) {
                $linkcss = $course->visible ? '' : ' class="dimmed" ';
                echo '<tr><td valign="top">&nbsp;';
                echo '</td><td valign="top" class="course name">';
                echo '<a '.$linkcss.' href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'. format_string($course->fullname).'</a>';
                echo '</td><td align="right" valign="top" class="course info">';
                if ($course->guest ) {
                    echo '<a title="'.$strallowguests.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
                    echo '<img alt="'.$strallowguests.'" src="'.$CFG->pixpath.'/i/guest.gif" /></a>';
                } else {
                    echo '<img alt="" style="width:18px;height:16px;" src="'.$CFG->pixpath.'/spacer.gif" />';
                }
                if ($course->password) {
                    echo '<a title="'.$strrequireskey.'" href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">';
                    echo '<img alt="'.$strrequireskey.'" src="'.$CFG->pixpath.'/i/key.gif" /></a>';
                } else {
                    echo '<img alt="" style="width:18px;height:16px;" src="'.$CFG->pixpath.'/spacer.gif" />';
                }
                if ($course->summary) {
                    link_to_popup_window ('/course/info.php?id='.$course->id, 'courseinfo',
                                          '<img alt="'.$strsummary.'" src="'.$CFG->pixpath.'/i/info.gif" />',
                                           400, 500, $strsummary);
                } else {
                    echo '<img alt="" style="width:18px;height:16px;" src="'.$CFG->pixpath.'/spacer.gif" />';
                }
                echo '</td></tr>';
            }
        }
    } else {

        echo '<tr>';

        if ($depth) {
            $indent = $depth*20;
            echo '<td class="category indentation" valign="top">';
            print_spacer(10, $indent);
            echo '</td>';
        }

        echo '<td valign="top" class="category name">';
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/mod/custom_reports/attendance.php?id='.$category->id.'">'. format_string($category->name).'</a>';
        echo '</td>';
        echo '<td valign="top" class="category number">';
        if (count($courses)) {
           echo count($courses);
        }
        echo '</td></tr>';
    }
    echo '</table>';
}

*/
?>