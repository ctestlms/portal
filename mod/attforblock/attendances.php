<?PHP // $Id: attendances.php,v 1.2.2.5 2009/02/23 19:22:40 dlnsk Exp $

//  Lists all the sessions for a course

    require_once('../../config.php');    
	require_once($CFG->libdir.'/blocklib.php');
	require_once('locallib.php');
	require_once('lib.php');	
	require_once('../../enrol/locallib.php'); // to get a list of enrolled students
        require_once($CFG->dirroot.'/user/profile/lib.php');
	
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }
    $output .= '<script type="text/javascript">'."\n<!--\n";
    $output .= 'function count_all_in(elTagName, elClass, elId) {';
    $output .= "var count1 = 0;";
    $output .= "var count2 = 0;";
    $output .= ' 	$.each($("td.c5 input[type=radio]"), function(){
    					//alert($(this).attr("checked") == "checked");
    					if($(this).attr("checked"))
    						count1++;
    					else
    						count2++;
    				});';
    $output .= "var total = count1 + count2;";
    $output .= "var msg = 'Total Present Students: '+count1+'<br>';";
    $output .= "msg = msg + 'Total Absent Students: '+count2 + '<br>';";
    $output .= "msg = msg + 'Total Students: '+ total + '<br>';";
    $output .= "document.getElementById('StdCount').innerHTML = msg;";
    $output .= "}";
    $output .= "function select_all_inn(elTagName, elClass, elId) {
					$.each($(\"td.\"+elClass+\" input[type=radio]\"), function(){
						$(this).attr(\"checked\", \"checked\");
					});
				}";

     $output .= "\n-->\n</script>";
     
    $id 	= required_param('id', PARAM_INT);
    $sessionid	= required_param('sessionid', PARAM_INT);
    $group    	= optional_param('group', -1, PARAM_INT);              // Group to show
    $sort 	= optional_param('sort','idnumber', PARAM_ALPHA);
//The upper function removes _ character for some reason
//So I am hard coding it. By Fahad Satti
    if($sort == 'usersubgroup'){
      $sort='user_subgroup';
    }

    if (! $cm = $DB->get_record('course_modules', array('id'=>$id))) {
        error('Course Module ID was incorrect');
    }
    
    if (! $course = $DB->get_record('course', array('id'=>$cm->course))) {
        error('Course is misconfigured');
    }
    
    require_login($course->id);

    if (! $attforblock = $DB->get_record('attforblock', array('id'=>$cm->instance))) {
        error("Course module is incorrect");
    }
    if (! $user = $DB->get_record('user', array('id'=>$USER->id)) ) {
        error("No such user in this course");
    }
    
    if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
        print_error('badcontext');
    }
    
    $statlist = implode(',', array_keys( (array)get_statuses($course->id) ));
    if ($form = data_submitted()) {
    	$students = array();			// stores students ids
		$formarr = (array)$form;
		$i = 0;
		$now = time();
		foreach($formarr as $key => $value) {
			if(substr($key,0,7) == 'student' && $value !== '') {
				$students[$i] = new stdClass();
				$sid = substr($key,7);		// gets studeent id from radiobutton name
				$students[$i]->studentid = $sid;
				$students[$i]->statusid = $value;
				$students[$i]->statusset = $statlist;
				$students[$i]->remarks = array_key_exists('remarks'.$sid, $formarr) ? $formarr['remarks'.$sid] : '';
				$students[$i]->sessionid = $sessionid;
				$students[$i]->timetaken = $now;
				$students[$i]->takenby = $USER->id;
				$i++;
			}
		}
		$attforblockrecord = $DB->get_record('attforblock', array('course'=>$course->id));
		//Added By Hina Yousuf
		 if($form->topic!=""){
                        $topic=new stdClass();
                        $topic->topicname=$form->topic;
                        $topic->id=$sessionid;
                        $DB->update_record('attendance_sessions', $topic);
                }
		
//end
		foreach($students as $student) {
			if ($log = $DB->get_record('attendance_log', array('sessionid'=>$sessionid, 'studentid'=>$student->studentid))) {
				$student->id = $log->id; // this is id of log
				//To unmark the taken attendance. '-' will be displayed in report instead of 'A' or 'P'. Khyam Shahzad
				if($student->statusid == 0 ){
					$DB->delete_records('attendance_log', array('sessionid'=>$student->sessionid, 'studentid'=>$student->studentid));
					continue;
				}
				$DB->update_record('attendance_log', $student);
			} else {
				//To ignore the operation for unmarked attendances. Khyam Shahzad
				if($student->statusid == 0 || $student->remarks=="Medical Leave")
					continue;
				$DB->insert_record('attendance_log', $student);
			}
		}
		
		$DB->set_field('attendance_sessions', 'lasttaken', $now, array('id'=>$sessionid));
		$DB->set_field('attendance_sessions', 'lasttakenby', $USER->id, array('id'=>$sessionid));
		
		attforblock_update_grades($attforblockrecord);
		add_to_log($course->id, 'attendance', 'updated', 'mod/attforblock/report.php?id='.$id, $user->lastname.' '.$user->firstname);
		redirect('manage.php?id='.$id, get_string('attendancesuccess','attforblock'), 1);
    	exit();
    }
    
/// Print headers
    $navlinks[] = array('name' => $attforblock->name, 'link' => "view.php?id=$id", 'type' => 'activity');
    $navlinks[] = array('name' => get_string('update', 'attforblock'), 'link' => null, 'type' => 'activityinstance');
    
    print_header("$course->shortname: ".$attforblock->name.' - ' .get_string('update','attforblock'), $course->fullname,
                 $navlinks, "", "", true, "&nbsp;", navmenu($course));

    echo $output;
//check for hack
    if (!$sessdata = $DB->get_record('attendance_sessions', array('id'=>$sessionid))) {
		error("Required Information is missing", "manage.php?id=".$id);
    }
    $help = $OUTPUT->help_icon('updateattendance', 'attforblock', '');
	$update = $DB->count_records('attendance_log', array('sessionid'=>$sessionid));
	
	if ($update) {
        require_capability('mod/attforblock:changeattendances', $context);
		echo $OUTPUT->heading(get_string('update','attforblock').' ' .get_string('attendanceforthecourse','attforblock').' :: ' .$course->fullname.$help);
	} else {
        require_capability('mod/attforblock:takeattendances', $context);
		echo $OUTPUT->heading(get_string('attendanceforthecourse','attforblock').' :: ' .$course->fullname.$help);
	}
	
//    /// find out current groups mode
//    $groupmode = groups_get_activity_groupmode($cm);
//    $currentgroup = groups_get_activity_group($cm, true);
//
//    if ($currentgroup) {
//        $students = get_users_by_capability($context, 'moodle/legacy:student', 'u.id, u.idnumber, u.firstname, u.lastname, u.picture,u.user_subgroup', "u.$sort ASC", '', '', $currentgroup, '', false);
//    } else {
//        $students = get_users_by_capability($context, 'moodle/legacy:student', 'u.id, u.idnumber, u.firstname, u.lastname, u.picture,u.user_subgroup', "u.$sort ASC", '', '', '', '', false);
//    }
    
    
    /// find out current groups mode
 	//$groupmode = groups_get_activity_groupmode($cm);
    $currentgroup = groups_get_activity_group($cm, true);
//    $manager = new course_enrolment_manager($course);
//
//    if ($currentgroup) {
//        $students = $manager->get_users($sort); // FIXME add $currentgroup somehow
//    } else {
//    	//$students = $manager->
//        $students = $manager->get_users($sort);
//    }

	$query = "SELECT u.id, u.firstname, u.lastname, u.idnumber, u.user_subgroup from mdl_user u
				JOIN {$CFG->prefix}role_assignments ra ON ra.userid=u.id
				JOIN {$CFG->prefix}role r ON ra.roleid = r.id
				JOIN {$CFG->prefix}context c ON ra.contextid = c.id
				where  (u.defaulter = 0 or defaulter is NULL) and r.name = 'Student' and
				c.contextlevel = 50 and
				c.instanceid = {$course->id} order by u.{$sort}";

    $students = $DB->get_records_sql($query);
	$sort = $sort == 'firstname' ? 'firstname' : 'idnumber';
    /// Now we need a menu for separategroups as well!
    if ($groupmode == VISIBLEGROUPS || 
            ($groupmode && has_capability('moodle/site:accessallgroups', $context))) {
        groups_print_activity_menu($cm, "attendances.php?id=$id&amp;sessionid=$sessionid&amp;sort=$sort");
    }
	$table = new html_table();
	$table->data[][] = '<b>'.get_string('sessiondate','attforblock').': '.userdate($sessdata->sessdate, get_string('strftimedate').', '.get_string('strftimehm', 'attforblock')).
							', "'.($sessdata->description ? $sessdata->description : get_string('nodescription', 'attforblock')).'"</b>';
	
	echo html_writer::table($table);
	
    $statuses = get_statuses($course->id);
	$i = 5;
  	foreach($statuses as $st) {
		$tabhead[] = "<a href=\"javascript:select_all_inn('TD', 'c{$i}', null);javascript:count_all_in('TD', 'cell c4', null);\"><u>$st->acronym</u></a>";
		$i++;
	}
	//Unmark the attendance. @khyam
        if(has_capability('mod/attforblock:unmarkattendance', $context))
                $tabhead[] = get_string('unmark', 'attforblock');
    $tabhead[] = get_string('summary'); //to display the summary @khyam
	$tabhead[] = get_string('remarks','attforblock');
	
	$firstname = "<a href=\"attendances.php?id=$id&amp;sessionid=$sessionid&amp;sort=firstname\">".get_string('firstname').'</a>';
	$lastname  = "<a href=\"attendances.php?id=$id&amp;sessionid=$sessionid&amp;sort=lastname\">".get_string('lastname').'</a>';
        $registrationno  = "<a href=\"attendances.php?id=$id&amp;sessionid=$sessionid&amp;sort=idnumber\">".get_string('registrationno','attforblock').'</a>';
//Added By Fahad Satti
        $SubGroup = "<a href=\"attendances.php?id=$id&amp;sessionid=$sessionid&amp;sort=user_subgroup\">Sub Group</a>";

    if ($CFG->fullnamedisplay == 'lastname firstname') { // for better view (dlnsk)
        $fullnamehead = "$lastname / $firstname";
    } else {
        $fullnamehead = "$firstname / $lastname";
    }
	
	if ($students) {
        unset($table);
        $table = new html_table();
	
	 $table->head[] = "S/No";
        $table->align[] = 'left';
        $table->size[] = '20px';

        $table->head[] = $registrationno;
        $table->align[] = 'left';
        $table->size[] = '200';
        
        $table->head[] = '';
        $table->align[] = '';
        $table->size[] = '40px';
        
        $table->head[] = $fullnamehead;
        $table->align[] = 'left';
        $table->size[] = '100';
        
//Added By Fahad Satti
        $table->head[] = $SubGroup;
        $table->align[] = 'left';
        $table->size[] = '50';

        foreach ($tabhead as $hd) {
            $table->head[] = $hd;
            $table->align[] = 'center';
            $table->size[] = '40px';
        }
        $count1 = 0;
        $count2 = 0;
        $counttotal = 0;
         $serial_no=1;
        foreach($students as $student) {
     
              if($student->user_subgroup == 'beee5a' || $student->user_subgroup == 'beee5b' || $student->user_subgroup == 'beee5c' || $student->user_subgroup == 'beee5d' || $student->user_subgroup == 'BESE4A'
         || $student->user_subgroup == 'BESE4B'|| $student->user_subgroup == 'BSCS3A' || $student->user_subgroup == 'BSCS3B' || $student->user_subgroup == 'MSIT14' || $student->user_subgroup == 'MSCCS6' 
            || $student->user_subgroup == 'MSEE5' || $student->user_subgroup == 'MSCS3'){
            
            $record = $DB->get_record('user',array('id'=>$student->id));  
    profile_load_data($record); 
   // print_object($record);
  
    if($record->profile_field_registrationstatus == 'pending' || $record->profile_field_registrationstatus == 'joined'){

		$table->data[$student->id][]=$serial_no++;

            $counttotal = $counttotal + 1;
            $att = $DB->get_record('attendance_log', array('sessionid'=>$sessionid, 'studentid'=>$student->id));
            $table->data[$student->id][] = (!$att && $update) ? "<font color=\"red\"><b>$student->idnumber</b></font>" : $student->idnumber;
            $table->data[$student->id][] = $OUTPUT->user_picture($student, array($course->id));//, $returnstring=false, $link=true, $target=''); 
			$table->data[$student->id][] = "<a href=\"view.php?id=$id&amp;student={$student->id}\">".((!$att && $update) ? '<font color="red"><b>' : '').fullname($student).((!$att && $update) ? '</b></font>' : '').'</a>';
            $table->data[$student->id][] = (!$att && $update) ? "<font color=\"red\"><b>$student->user_subgroup</b></font>" : $student->user_subgroup;
            $i = 1;
            $present = false;
            foreach($statuses as $st) {
                @$table->data[$student->id][] = "<input onclick=\"javascript:count_all_in('TD', 'cell c4', null);\" name=\"student".$student->id.'" type="radio" value="'.$st->id.'" '.($st->id == $att->statusid ? 'checked' : '').'>';
                if($st->id == $att->statusid){
                    if($i==1) $count1 = $count1 + 1;
                    //if($i==2) $count2 = $count2 + 1;
                }
                if($st->grade)
                	$present = $st->id;
                 
                 $i++;
            }
		//Unmark the attendance radio button.
            if(has_capability('mod/attforblock:unmarkattendance', $context))
                    @$table->data[$student->id][] = '<input name="student'.$student->id.'" type="radio" value="0"'.((!$att && $update)? "checked" : "").' />';
			$table->data[$student->id][] = get_attendance($student->id, $course, $present).'/'.get_attendance($student->id, $course).'='.get_percent($student->id, $course).'%';
//            $table->data[$student->id][] = '<input type="text" name="remarks'.$student->id.'" size="" value="'.($att ? $att->remarks : '').'">';
$table->data[$student->id][] = '<select name="remarks'.$student->id.'" ><option value=""'.($att->remarks == "" ? "selected = 'selected'" : "").'>None</option>  <option value="Medical Leave"'.($att->remarks == "Medical Leave" ? "selected = 'selected'" : "").'>Medical Leave</option><option value="On Field Duty"'.($att->remarks == "On Field Duty" ? "selected = 'selected'" : "").'>On Field Duty</option><option value="Left Early"'.($att->remarks == "Left Early" ? "selected = 'selected'" : "").'>Left Early</option><option value="Came Late"'.($att->remarks == "Came Late" ? "selected = 'selected'" : "").'>Came Late</option><option value="Advisory Note"'.($att->remarks == "Advisory Note" ? "selected = 'selected'" : "").'>Advisory Note
                </option>
<option value="Reporting Sick"'.($att->remarks == "Reporting Sick" ? "selected = 'selected'" : "").'>Reporting Sick
                </option><option value="On Leave"'.($att->remarks == "On Leave" ? "selected = 'selected'" : "").'>On Leave
                </option>
<option value="Other"'.($att->remarks == "Other" ? "selected = 'selected'" : "").'>Other


                </option>';
        }
            }
            else{
                	$table->data[$student->id][]=$serial_no++;

            $counttotal = $counttotal + 1;
            $att = $DB->get_record('attendance_log', array('sessionid'=>$sessionid, 'studentid'=>$student->id));
            $table->data[$student->id][] = (!$att && $update) ? "<font color=\"red\"><b>$student->idnumber</b></font>" : $student->idnumber;
            $table->data[$student->id][] = $OUTPUT->user_picture($student, array($course->id));//, $returnstring=false, $link=true, $target=''); 
			$table->data[$student->id][] = "<a href=\"view.php?id=$id&amp;student={$student->id}\">".((!$att && $update) ? '<font color="red"><b>' : '').fullname($student).((!$att && $update) ? '</b></font>' : '').'</a>';
            $table->data[$student->id][] = (!$att && $update) ? "<font color=\"red\"><b>$student->user_subgroup</b></font>" : $student->user_subgroup;
            $i = 1;
            $present = false;
            foreach($statuses as $st) {
                @$table->data[$student->id][] = "<input onclick=\"javascript:count_all_in('TD', 'cell c4', null);\" name=\"student".$student->id.'" type="radio" value="'.$st->id.'" '.($st->id == $att->statusid ? 'checked' : '').'>';
                if($st->id == $att->statusid){
                    if($i==1) $count1 = $count1 + 1;
                    //if($i==2) $count2 = $count2 + 1;
                }
                if($st->grade)
                	$present = $st->id;
                 
                 $i++;
            }
		//Unmark the attendance radio button.
            if(has_capability('mod/attforblock:unmarkattendance', $context))
                    @$table->data[$student->id][] = '<input name="student'.$student->id.'" type="radio" value="0"'.((!$att && $update)? "checked" : "").' />';
			$table->data[$student->id][] = get_attendance($student->id, $course, $present).'/'.get_attendance($student->id, $course).'='.get_percent($student->id, $course).'%';
//            $table->data[$student->id][] = '<input type="text" name="remarks'.$student->id.'" size="" value="'.($att ? $att->remarks : '').'">';
$table->data[$student->id][] = '<select name="remarks'.$student->id.'" ><option value=""'.($att->remarks == "" ? "selected = 'selected'" : "").'>None</option>  <option value="Medical Leave"'.($att->remarks == "Medical Leave" ? "selected = 'selected'" : "").'>Medical Leave</option><option value="On Field Duty"'.($att->remarks == "On Field Duty" ? "selected = 'selected'" : "").'>On Field Duty</option><option value="Left Early"'.($att->remarks == "Left Early" ? "selected = 'selected'" : "").'>Left Early</option><option value="Came Late"'.($att->remarks == "Came Late" ? "selected = 'selected'" : "").'>Came Late</option><option value="Advisory Note"'.($att->remarks == "Advisory Note" ? "selected = 'selected'" : "").'>Advisory Note
                </option>
<option value="Reporting Sick"'.($att->remarks == "Reporting Sick" ? "selected = 'selected'" : "").'>Reporting Sick
                </option><option value="On Leave"'.($att->remarks == "On Leave" ? "selected = 'selected'" : "").'>On Leave
                </option>
<option value="Other"'.($att->remarks == "Other" ? "selected = 'selected'" : "").'>Other


                </option>';
                
            }
            
        }
        if($counttotal!=$serial_no-1){
          echo "<div>$counttotal is not equal to $serial_no. Unable to compute</div>";
        }
        $count2 = $counttotal - $count1;
        echo '<form name="takeattendance" method="post" action="attendances.php">';
        echo html_writer::table($table);
        echo '<input type="hidden" name="id" value="'.$id.'">';
        echo '<input type="hidden" name="sessionid" value="'.$sessionid.'">';
        echo '<input type="hidden" name="formfrom" value="editsessvals">';
        echo '<center><input type="submit" name="esv" value="'.get_string('ok').'" onClick="javascript: if($(\'input[type=radio]:checked\').length<'.$counttotal.'){ alert(\'Some students are still unmarked. Please mark all students.\'); return false;}"></center>';
        echo '<center><b>Topic Name:</b><input type="text" id="topic" name="topic" value=""></center>';//Added By Hina yousuf
        echo '</form>';
    } else {
		$OUTPUT->heading(get_string('nothingtodisplay'));
		
	}
       
       
	 
	echo get_string('status','attforblock').':<br />'; 
	echo '<span id="StdCount">';
        //foreach($statuses as $st) {
	//	echo $st->acronym.' - '.$st->description.' - {!'.$st->acronym.'} <br />';
	//}
        echo 'Total Present Students: '.$count1.' <br>';
        echo 'Total Absent Students: '.$count2.' <br>';
        echo 'Total Students: '.$counttotal.' <br>';
        echo '</span>';

    print_footer($course);
    
?>
