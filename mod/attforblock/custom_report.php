<?php
	require_once('../../config.php');    
	require_once($CFG->libdir.'/blocklib.php');
	require_once($CFG->libdir.'/formslib.php');
	require_once('./view_report_form.php');
	require_once('locallib.php');
	require_login($course->id);
	
	$categoryid = optional_param('id', '-1', PARAM_INT);
	$export = optional_param('export',false, PARAM_BOOL);
	$context = get_context_instance(CONTEXT_COURSECAT, $categoryid);
	if($categoryid!=-1)
		require_capability('block/custom_reports:getattendancereport', $context);
	
	$navlinks[] = array('name' => $attforblock->name, 'link' => "view.php?id=$id", 'type' => 'activity');
    $navlinks[] = array('name' => get_string('attendance_custom_reports', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
	$navigation = build_navigation($navlinks);		
	
	if(!$export)
		print_header('Attendance Custom Report', 'Attendance Custom Report', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
	
	if($courses = get_courses($categoryid, '', 'c.id, c.fullname, c.startdate, c.idnumber, c.shortname') or $export){
		$mform = new mod_attforblock_view_report_form('custom_report.php', array('courses'=>$courses, 'categoryid'=>$categoryid));
		if($fromform = $mform->get_data() or $export){
			$cselected = array();
			if($export){
				$export_courses = required_param('courses');
				//echo "select id, fullname, startdate, idnumber, shortname from {$CFG->prefix}courses where id IN ({$export_courses})";
				$courses = get_records_sql("select id, fullname, startdate, idnumber, shortname from {$CFG->prefix}course where id IN ({$export_courses})");
			}else{
				$export_courses = "";
			}
			foreach($courses as $course){
				if((!$export AND $fromform->{'c'.$course->id}=='true') OR ($export)){
					$query = "SELECT u.id, u.firstname, u.lastname, u.idnumber from mdl_user u
							JOIN {$CFG->prefix}role_assignments ra ON ra.userid=u.id
							JOIN {$CFG->prefix}role r ON ra.roleid = r.id
							JOIN {$CFG->prefix}context c ON ra.contextid = c.id
							where r.name = 'Student' and
							c.contextlevel = 50 and
							c.instanceid = {$course->id} order by u.firstname";
					//$cselected = array("id" => array(), "name" => array(), "students" => array());
					$cselected["id"][] = $course->id;
					$cselected["name"][] = $course->fullname;
					$cselected["shortname"][] = $course->shortname;
					$cselected["idnumber"][] = $course->idnumber;
					$cselected["students"][] = get_records_sql($query);
					$cselected["startdate"][] = $course->startdate;
				}
				if($fromform->{'c'.$course->id}=='true' and !$export)
					$export_courses .= $course->id.",";
			}
			
			//$table->width = '80%';
			//$table->tablealign =  'center';
			//$table->cellpadding = '5px';
			
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
			
			
			for($i=0; $i<count($cselected["id"]); $i++){
				$table->head[] = $cselected['name'][$i];
				$table->align[] = 'center';
				$table->size[] = '1px';
				
				//echo count($cselected["students"][$i]);
				
				foreach($cselected["students"][$i] as $student){
					//echo "<br>".$student->idnumber;
					//print_r($students["idnumber"]);
					if(!in_array($student->id, $students["userid"])){
						$students["userid"][] = $student->id;
						$students["idnumber"][] = $student->idnumber;
						$students["name"][] = $student->firstname.' '.$student->lastname;
					}
				}
			}
			
			//echo count($students["name"]);
			//print_r($students['idnumber']);
			//var_dump($students["name"]);
			
			for($i=0; $i<count($students["userid"]); $i++){
				$table->data[$i][] = $i+1;
				$table->data[$i][] = $students["idnumber"][$i];
				$table->data[$i][] = $students["name"][$i];
				for($j=0; $j<count($cselected['id']); $j++){
					$course = get_record('course', 'id', $cselected['id'][$j]);
					if(!key_exists($students["userid"][$i], (array)$cselected["students"][$j])){
						$table->data[$i][] = '---';
						continue;
					}
					$attendance = get_percent_absent($students["userid"][$i], $course);
					if($attendance > 25 AND !$export)
						$table->data[$i][] = '<div style="background: #aaa;">'.$attendance.'%</div>';
					else
						$table->data[$i][] = $attendance.'%';
					//echo $students["userid"][$i]."--".$cselected['id'][$j]."<br>";
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
				echo 			'<input type="hidden" name="id" value="'.$categoryid.'" />';	
				echo 			'<input type="hidden" name="export" value="true" /><input type="submit" value="Download Excel" />
							</form>
							<span style="text-align: left; padding-left: 20px; text-decoration: underline;">Duration :- '.date("d M Y", $cselected["startdate"][0]).' to '.date("d M Y", time('now')).'</span></div>';
				print_table($table);
			}			
			exit();
		}else
			$mform->display();
	}else{
		print_box_start('generalbox categorybox');
		print_whole_category_list2(NULL, NULL, NULL, -1, false);
		print_box_end();
	}	
	print_footer();

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
    //$formatbc->set_size(14);
	$myxls->write(1, 3, "ATTENDANCE SUMMARY");
	$myxls->write(2, 3, "ABSENTEES RECORD (Percentage)");
    $myxls->write(4, 0, 'Duration');
    $myxls->write(4, 1, $data->duration, $formatbc);

    $i = 6;
    $j = 0;
    foreach ($data->data as $row) {
    	foreach ($row as $cell) {
    		//$myxls->write($i, $j++, $cell);
    		if (is_numeric($cell)) {
   			$myxls->write_number($i, $j++, $cell);
    		} else {
    			$myxls->write_string($i, $j++, $cell);
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

    global $CFG;
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
        $coursecount = count_records('course') <= FRONTPAGECOURSELIMIT;
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
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/mod/attforblock/custom_report.php?id='.$category->id.'">'. format_string($category->name).'</a>';
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
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/mod/attforblock/custom_report.php?id='.$category->id.'">'. format_string($category->name).'</a>';
        echo '</td>';
        echo '<td valign="top" class="category number">';
        if (count($courses)) {
           echo count($courses);
        }
        echo '</td></tr>';
    }
    echo '</table>';
}


?>
