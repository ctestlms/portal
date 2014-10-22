<?php
	require_once('../../config.php');    
	require_once($CFG->libdir.'/blocklib.php');
	require_once($CFG->libdir.'/formslib.php');
//	require_once('./view_report_form.php');
	require_once('locallib.php');
	
	require_login($course->id);
	
	$categoryid = array(122,123,124,126,129,130,131,133,134,135,136,138,143);
	//$course = array();
	$course_ids = array();
	
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
			
			$table->head[] = 'Course Name';
			$table->align[] = 'left';
			$table->size[] = '';
			
			$table->head[] = 'Absence';
			$table->align[] = 'center';
			$table->size[] = '';
				
	foreach ($categoryid as $category){
		$courses = array_merge((array)$courses, (array)get_courses($category, '', 'c.id, c.fullname, c.startdate, c.idnumber, c.shortname'));
	}

	foreach ($courses as $course){
		$courseslist .= $course->id.",";
		$course_ids[] = $course->id;
	}
	$courseslist = rtrim($courseslist, ',');
	
	$query = "SELECT u.id, u.firstname, u.lastname, u.idnumber from mdl_user u
				JOIN {$CFG->prefix}role_assignments ra ON ra.userid=u.id
				JOIN {$CFG->prefix}role r ON ra.roleid = r.id
				JOIN {$CFG->prefix}context c ON ra.contextid = c.id
				where r.name = 'Student' and
				c.contextlevel = 50 and
				c.instanceid IN ({$courseslist}) order by u.idnumber";
	$students = get_records_sql($query);
	
	$i = 1;
	
	foreach($students as $student){
		$query = "select fullname, id from {$CFG->prefix}course where id IN 
			(select instanceid from {$CFG->prefix}context where contextlevel = 50 and id IN
			(select contextid from {$CFG->prefix}role_assignments where roleid = 
			(select id from {$CFG->prefix}role where name = 'Student') and userid = 
			(select id from {$CFG->prefix}user where id='$student->id')))";
		$my_courses = get_records_sql($query);
				
		foreach($my_courses as $my_course){
			if(in_array($my_course->id, $course_ids)){
				$course = get_record('course', 'id', $my_course->id);
				$attendance = get_percent_absent($student->id, $course);
					if($attendance > 25.0){
					//	$attendance = '<div style="background: #aaa;">'.$attendance.'</div>';
						$table->data[$i][] = $i+1;
						$table->data[$i][] = $student->idnumber;
						$table->data[$i][] = $student->firstname.' '.$student->lastname;
						$table->data[$i][] = $my_course->fullname;
						$table->data[$i][] = '<div style="background: #aaa;">'.$attendance.'%</div>';
						$i++;
					}
			}
		}
	}
	
	print_table($table);
	exit();

?>
