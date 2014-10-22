<?php
	require_once('../../config.php');    
	require_once($CFG->libdir.'/blocklib.php');
	require_once($CFG->libdir.'/formslib.php');
	require_once('./view_attendance_report_form.php');
	
	require_login($course->id);
	
	$categoryid = optional_param('id', '-1', PARAM_INT);
	$export = optional_param('export',false, PARAM_BOOL);
	$context = get_context_instance(CONTEXT_COURSECAT, $categoryid);
	$modules = array('assignment', 'forum', 'quiz', 'resource', 'attforblock', 'turnitintool');
	
	if($categoryid!=-1)
		require_capability('block/custom_reports:getauditreport', $context);
	$report=get_string('audit_custom_reports', 'block_custom_reports');
	$navlinks[] = array('name' => get_string('audit_custom_reports', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
	$navigation = build_navigation($navlinks);		
	
	if(!$export)
		print_header('Course Audit Report', 'Course Audit Report', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
	
	if($courses = get_courses($categoryid, '', 'c.id, c.fullname, c.startdate, c.idnumber, c.shortname') or $export){
		$mform = new mod_custom_reports_view_attendance_report_form('audit.php', array('courses'=>$courses, 'categoryid'=>$categoryid,'report'=>$report));
		if($fromform = $mform->get_data() or $export){
			if($export){
				$export_courses = required_param('courses',PARAM_SEQUENCE);
				$courses = $DB->get_records_sql("select id, fullname, startdate, idnumber, shortname from {course} where id IN ({$export_courses})");
				 $startperiod =$_SESSION['startperiod'];
                        $endperiod=$_SESSION['endperiod'];

//$period =$_SESSION['period'];
			}else{
				$export_courses = "";
				 $startperiod=strtotime($fromform->startperiod);
                        $endperiod=strtotime($fromform->endperiod);
                        $_SESSION['startperiod'] =$startperiod;
                        $_SESSION['endperiod'] =$endperiod;

//				$period=$fromform->period;
//				$_SESSION['period'] =$period;
			}
			/////
			
		//	$period=$fromform->period;
		/*	if($period=="Weekly"){
	            $gap=7;
	        }
	        if($period=="Monthly"){
	            $gap=30;
	        }
	        if($period=="Yearly"){
	            $gap=365;
	        }*/
	        
			
			//////
			$table = new html_table();
			$table->head = array();
			
			$table->head[] = 'S.No';
			$table->align[] = 'center';
			$table->size[] = '';
			
			$table->head[] = 'Period';
			$table->align[] = 'center';
			$table->size[] = '';
			
			$table->head[] = 'Course Name';
			$table->align[] = 'left';
			$table->size[] = '';
			
			$table->head[] = 'Teacher';
			$table->align[] = 'left';
			$table->size[] = '';
			
			$table->head[] = 'Activities';
			$table->align[] = 'center';
			$table->size[] = '';
			
			foreach($modules as $module){
				if($module=="attforblock"){
					$table->head[] = "ATTENDANCE";
				}
				else
				{
					$table->head[] = strtoupper($module);
				}
			//	$table->head[] = strtoupper($module);
				$table->align[] = 'center';
				$table->size[] = '';
			}
			
			$i = 0;
			foreach($courses as $course){
				if((!$export AND $fromform->{'c'.$course->id}=='true') OR ($export)){
					///
				/*	$time->end =1272672000;
					do{
					if($period=="ALL"){                   
                    $time->start =$time->end ;
                    $time->end = time();
                    
	                }
	                else{
	                    $time->start =$time->end ;
	                    $time->end = strtotime("+$gap days", $time->start);
	                    
	                }
	                */
	
	                $starttime= date(" M jS, Y", $startperiod);
	                $endtime=   date(" M jS, Y", $endperiod);   
	                
                	////
					$startdate = $course->startdate;
					$activity = $DB->get_record_sql("select count(id) as activities from {log} where course = '{$course->id}' and time between  $startperiod and $endperiod ");				
					$context = get_context_instance(CONTEXT_COURSE, $course->id);
					$role = $DB->get_record('role', array('shortname'=>'editingteacher'));
					$teachers = get_role_users($role->id, $context);//('teacher', $context);
					$course_teacher = "";
					foreach($teachers as $teacher)
						if(!$export)
							$course_teacher .= "<a href='{$CFG->wwwroot}/user/view.php?id={$teacher->id}' target='_blank'>".$teacher->firstname." ".$teacher->lastname."</a><br>";
						else
							$course_teacher .= $teacher->firstname." ".$teacher->lastname."<br>";
					
                	
					$table->data[$i][] = $i+1;
					$table->data[$i][] ="<b> ".$starttime." - ". $endtime."</b>" ;
					$table->data[$i][] = (!$export)?'<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" target="_blank">'.$course->fullname.'</a>':$course->fullname;;
					$table->data[$i][] = rtrim($course_teacher, "<br>");
					$table->data[$i][] = $activity->activities;
					
					foreach ($modules as $module){
						
						if($module == 'attforblock')
							{
								if($sessions = $DB->get_record_sql("select count(id) as total from {$CFG->prefix}attendance_sessions where timemodified between $startperiod and $endperiod and  courseid = {$course->id} and lasttaken != 'NULL'")){
									$table->data[$i][] = $sessions->total;					
								}
								else{
									$table->data[$i][] = "0";
								}
							}
							else
							{ 
								if($resource = $DB->get_record_sql("SELECT count(a.id) as total, b.name FROM {course_modules} a, {modules} b WHERE a.added between  $startperiod and $endperiod and a.course =? and a.module = b.id and b.name = ? group by b.name order by b.id",array($course->id, $module)))
									$table->data[$i][] = $resource->total;
								else
									$table->data[$i][] = "0";
							}
					}
			/*		if($period=="ALL"){
                    $time->end =  strtotime("+1 days", $time->end );
               	    }*/
               	    $i++;
			//	}while($time->end <= time());//hina
				}
				if($fromform->{'c'.$course->id}=='true' and !$export)
					$export_courses .= $course->id.",";
				//Loop increment;
				//$i++;
			
			}
			
			if($export){
				//print_table($table, true);
				//$category = get_record('course_categories', 'id', $categoryid);
				//$table->category = $category->namei;
			//	$table->duration = date("d M Y", $startdate).' to '.date("d M Y", time('now'));
				ExportToExcel($table);
			}
			else{
				//print_r($cselected["id"]);
					echo '<div style="text-align: center; font-weight: bold;">COURSE AUDIT SUMMARY<br></div>';
				echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">
							<form method="post" style="display: inline; margin: 0; padding: 0;">';
				echo 			'<input type="hidden" name="courses" value="'.rtrim($export_courses, ',').'" />';
				echo 			'<input type="hidden" name="id" value="'.$categoryid.'" />';	
				echo 			'<input type="hidden" name="export" value="true" /><input type="submit" value="Download Excel" />
							</form></div>';
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
	global $modules;
    //require_once("$CFG->libdir/excellib.class.php");/*
    require_once($CFG->dirroot.'/lib/excellib.class.php');
   $filename = "Course_audit_report.xls";
   
   $workbook = new MoodleExcelWorkbook("-");
/// Sending HTTP headers
    ob_clean();
    $workbook->send($filename);
/// Creating the first worksheet
    $myxls =& $workbook->add_worksheet('Autid Report');
/// format types
    $formatbc =& $workbook->add_format();
    $formatbc->set_bold(1);
    //$formatbc->set_size(14);
	$myxls->write(0, 3, "Audit SUMMARY", $formatbc);
	$myxls->write(1, 3, "ACTIVITIES RECORD", $formatbc);
//	$myxls->write(3, 0, "Duration", $formatbc);
    $myxls->write(3, 1, $data->duration, $formatbc);
	
    $myxls->write_string(5, 0, "S.No", $formatbc);
    $myxls->write_string(5, 1, "Period", $formatbc);
    $myxls->write_string(5, 2, "Course Name", $formatbc);

    $myxls->write_string(5, 3, "Teacher Name", $formatbc);   
    $myxls->write_string(5, 4, "Actitivties", $formatbc);
    $j = 5;
    foreach ($modules as $module){
	if($module=="attforblock"){
			$myxls->write_string(5, $j++, "Attendance", $formatbc);
		}
		else{
	    	$myxls->write_string(5, $j++, strtoupper($module), $formatbc);
		}
}
	

    $i = 6;
    $j = 0;
    foreach ($data->data as $row) {
    	foreach ($row as $cell) {
    		//$myxls->write($i, $j++, $cell);
    		if (is_numeric($cell)) {
   			$myxls->write_number($i, $j++,strip_tags( $cell));
    		} else {
    			$myxls->write_string($i, $j++,strip_tags( $cell));
    		}
    	}
		$i++;
		$j = 0;
    }
	$workbook->close();
    exit;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////	
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

/*function print_whole_category_list2($category=NULL, $displaylist=NULL, $parentslist=NULL, $depth=-1, $showcourses = true) {
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
        echo '<a '.$catlinkcss.' href="'.$CFG->wwwroot.'/mod/custom_reports/audit.php?id='.$category->id.'">'. format_string($category->name).'</a>';
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