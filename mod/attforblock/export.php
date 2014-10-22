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

/**
 * Export attendance sessions
 *
 * @package    mod
 * @subpackage attforblock
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/export_form.php');
require_once(dirname(__FILE__).'/renderables.php');
require_once(dirname(__FILE__).'/renderhelpers.php');

$id             = required_param('id', PARAM_INT);

$cm             = get_coursemodule_from_id('attforblock', $id, 0, false, MUST_EXIST);
$course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att            = $DB->get_record('attforblock', array('id' => $cm->instance), '*', MUST_EXIST);

// required for percent calculation
$presentcount = 0;
$absentcount = 0;
$unmarkedcount = 0;
$totalcount = 0;
$decimalpoints = 2;
$teachers = array();
$context = context_module::instance($cm->id);

require_login($course, true, $cm);

$att = new attforblock($att, $cm, $course, $PAGE->context);

$att->perm->require_export_capability();

$PAGE->set_url($att->url_export());
$PAGE->set_title($course->shortname. ": ".$att->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_cacheable(true);
$PAGE->set_button($OUTPUT->update_module_button($cm->id, 'attforblock'));
$PAGE->navbar->add(get_string('export', 'quiz'));

$formparams = array('course' => $course, 'cm' => $cm, 'modcontext' => $PAGE->context);
$mform = new mod_attforblock_export_form($att->url_export(), $formparams);

if ($mform->is_submitted()) {
    $formdata = $mform->get_data();

    $pageparams = new att_page_with_filter_controls();
    $pageparams->init($cm);
    $pageparams->group = $formdata->group;
    $pageparams->set_current_sesstype($formdata->group ? $formdata->group : att_page_with_filter_controls::SESSTYPE_ALL);
    if (isset($formdata->includeallsessions)) {
        if (isset($formdata->includenottaken)) {
            $pageparams->view = ATT_VIEW_ALL;
        } else {
            $pageparams->view = ATT_VIEW_ALLPAST;
            $pageparams->curdate = time();
        }
        $pageparams->init_start_end_date();
    } else {
        $pageparams->startdate = $formdata->sessionstartdate;
        $pageparams->enddate = $formdata->sessionenddate;
    }
    $att->pageparams = $pageparams;

    $reportdata = new attforblock_report_data($att);
	//print_r($reportdata->usersstats[7210]);
	//break;
	
	$sql = "SELECT u.*
                FROM {role_assignments} ra, {user} u, {course} c, {context} cxt
                WHERE ra.userid = u.id
                    AND ra.contextid = cxt.id
                    AND cxt.contextlevel = 50
                    AND cxt.instanceid = c.id
                    AND c.id = ?
                    AND roleid = 3
                ORDER BY u.idnumber ASC";
        $params = array($cm->course);
        $teachers = $DB->get_records_sql($sql, $params);
	
	
    if ($reportdata->users) {
        $filename = clean_filename($course->shortname.'_Attendances_'.userdate(time(), '%Y%m%d-%H%M'));

		$group = $formdata->group ? $reportdata->groups[$formdata->group] : 0;
        $data = new stdClass;
        $data->tabhead = array();
        $data->course = $att->course->fullname;
        $data->group = $group ? $group->name : get_string('allparticipants');
	
		if($teachers){
            //$data->teacher = 'hello';
            $i=0;
            foreach($teachers as $teacher){
                $data->teacher = $i>0 ? $data->teacher.', '.$teacher->firstname.' '.$teacher->lastname : $teacher->firstname.' '.$teacher->lastname;
                $i = $i + 1;
            }
        }
		else
		{
			$data->teacher = '';
		} 
		
		
		
		$data->tabhead[] = get_string('serialno', 'attforblock');
		$data->tabhead[] = get_string('registrationno', 'attforblock');
		
        /* if (isset($formdata->ident['id'])) {
            $data->tabhead[] = get_string('serialno', 'attforblock');
        }
        if (isset($formdata->ident['uname'])) {
            $data->tabhead[] = get_string('registrationno', 'attforblock');
        } */
        //$data->tabhead[] = get_string('firstname');
		
		 $data->tabhead[] = get_string('fullname', 'attforblock');
		 
		 
        if (count($reportdata->sessions) > 0) {
            foreach($reportdata->sessions as $sess) {
                $text = userdate($sess->sessdate, get_string('strftimedmy', 'attforblock'))."-".userdate($sess->sessdate, get_string('strftimehm', 'attforblock'));
                $text .= '--';
                //$text .= $sess->groupid ? $reportdata->groups[$sess->groupid]->name : get_string('commonsession', 'attforblock');
                $text .= $sess->description;
                $data->tabhead[] = $text;
				if (isset($formdata->showremarks)) {
					$data->tabhead[] =get_string('remarks', 'attforblock');
				}
            }
        } else {
            print_error('sessionsnotfound', 'attforblock', $att->url_manage());
        }
			
		//$statuses = get_statuses($course->id);
        //print attendance statuses P, A etc..
        foreach ($reportdata->statuses as $status) {
			if($status->acronym == "P" || $status->acronym == "A") {
				$data->tabhead[] = $status->acronym;
			}
		}
			
		$data->tabhead[] = get_string('total','attforblock');
		$data->tabhead[] = get_string('presentpercentage','attforblock');
		$data->tabhead[] = get_string('absentpercentage','attforblock');
		if ($reportdata->gradable)
            $data->tabhead[] = get_string('grade');
		

        $i = 0;
        $data->table = array();
		global $DB, $course;
		
		// Get school for checking "Not joined" students for SEECS only - added by Qurrat-ul-ain Babar (14th Oct, 2014)
		$category = $course->category;
		$semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
		$path = explode("/", $semester->path);
		$school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]"); 
		
        foreach($reportdata->users as $user) {
			if($school->name == "School of Electrical Engineering and Computer Science (SEECS)" ) {
				if ($user->registrationstatus != 'not joined') {
					$attdata = array();
					$enrolmentsparams = array();
					$coursestartdate = array();
					
					
					$sql1 = "SELECT ue.userid, ue.status, ue.timestart, ue.timeend, ue.timecreated
							  FROM {user_enrolments} ue
							  JOIN {enrol} e ON e.id = ue.enrolid
							 WHERE ue.userid = :uid
								   AND e.courseid = :courseid";
						  //GROUP BY ue.userid, ue.status, ue.timestart, ue.timeend";
					$params1 = array('uid'=> $user->id, 'courseid'=>$course->id);
					$enrolmentsparams = $DB->get_record_sql($sql1, $params1);
					
					$sql2 = "SELECT startdate
							  FROM {course} 
							  WHERE id = :courseid";
						  //GROUP BY ue.userid, ue.status, ue.timestart, ue.timeend";
					$params2 = array('courseid'=>$course->id);
					$coursestartdate = $DB->get_record_sql($sql2, $params2);
					$csdate = $coursestartdate->startdate;
					//$string = var_export($csdate, true);
					// date ranges included - added by Qurrat-ul-ain Babar (20th Dec, 2013)
					
					$where = "ats.attendanceid = :aid AND ats.sessdate >= :csdate AND ats.sessdate >= :enrolstart AND
							  al.studentid = :uid";
				
					$qry3 = "SELECT ats.id, al.studentid, al.statusid, ats.description
							  FROM {attendance_log} al
							  JOIN {attendance_sessions} ats
								ON al.sessionid = ats.id
							 WHERE $where";
						  //GROUP BY ats.description";
			
							
						$params3 = array(
							'uid'        => $user->id,
							'aid'        => $att->id,
							'csdate'     => $csdate,
							'enrolstart' => $enrolmentsparams->timestart);

					
					$attdata = $DB->get_records_sql($qry3, $params3);
					
					$statsarray = array();
					foreach ($attdata as $status) {
						//echo "<br/> $status->statusid: Status count: $status->stcnt<br/>";
						$classtype = $status->description;
						switch ($classtype) {
							case "90-Mins Lecture":
								//echo "Its a 90-Mins Lecture";
								if (array_key_exists($status->statusid, $statsarray))
								{
									$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 1.5;
								}
								else
								{
									$statsarray[$status->statusid]->statusid = $status->statusid;
									$statsarray[$status->statusid]->stcnt = 1.5;
								}
								break;
							case "Two Hours Lecture":
								//echo "Its a Two Hours Lecture";
								if (array_key_exists($status->statusid, $statsarray))
								{
									$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 2;
								}
								else
								{
									$statsarray[$status->statusid]->statusid = $status->statusid;
									$statsarray[$status->statusid]->stcnt = 2;
								}
								break;
							case "Three Hours Lecture":
								//echo "Its a Three Hours Lecture";
								if (array_key_exists($status->statusid, $statsarray))
								{
									$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 3;
								}
								else
								{
									$statsarray[$status->statusid]->statusid = $status->statusid;
									$statsarray[$status->statusid]->stcnt = 3;
								}
								break;
							case "Three Hours Studio":
								//echo "Its a Three Hours Studio";
								if (array_key_exists($status->statusid, $statsarray))
								{
									$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 1.5;
								}
								else
								{
									$statsarray[$status->statusid]->statusid = $status->statusid;
									$statsarray[$status->statusid]->stcnt = 1.5;
								}
								break;
							case "Two Hours Ward":
								//echo "Its a Three Hours Studio";
								if (array_key_exists($status->statusid, $statsarray))
								{
									$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 2;
								}
								else
								{
									$statsarray[$status->statusid]->statusid = $status->statusid;
									$statsarray[$status->statusid]->stcnt = 2;
								}
								break;
							case "Four Hours Ward":
								//echo "Its a Three Hours Studio";
								if (array_key_exists($status->statusid, $statsarray))
								{
									$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 4;
								}
								else
								{
									$statsarray[$status->statusid]->statusid = $status->statusid;
									$statsarray[$status->statusid]->stcnt = 4;
								}
								break;
							default:
								//echo "Default lecture ";
								if (array_key_exists($status->statusid, $statsarray))
								{
									$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 1;
								}
								else
								{
									$statsarray[$status->statusid]->statusid = $status->statusid;
									$statsarray[$status->statusid]->stcnt = 1;
								}
						}
					}  
					
					$presentcount = 0;
					$absentcount = 0;
					$unmarkedcount = 0;
					$totalcount = 0;
					
					foreach ($reportdata->statuses as $status) {
					// update to get_attendance_bydate incase range is selected
						if (has_capability('mod/attforblock:unmarkattendances', $context)) {
							if (array_key_exists($status->id, $statsarray)) {
								if($status->acronym == "P") {
										//$data->table[$i][] = $statsarray[$status->id]->stcnt;
										$presentcount = $statsarray[$status->id]->stcnt;
								}
								elseif($status->acronym == "A") {
										//$data->table[$i][] = $statsarray[$status->id]->stcnt;
										$absentcount = $statsarray[$status->id]->stcnt;
								}
								else {
										//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										$unmarkedcount = $statsarray[$status->id]->stcnt;
								}
							}
							else
							{
							}
						}
						else {
							if (array_key_exists($status->id, $statsarray)) {
								if($status->acronym == "P") {
										//$data->table[$i][] = $statsarray[$status->id]->stcnt;
										$presentcount = $statsarray[$status->id]->stcnt;
								}
							elseif($status->acronym == "A") {
										//$data->table[$i][] = $statsarray[$status->id]->stcnt;
										$absentcount = $statsarray[$status->id]->stcnt;
									}
									else {}
							}
							else
							{
							}	
							
						}
					}
					$totalcount = $presentcount + $absentcount;
					$presentpercent = ($presentcount/$totalcount)*100;
					$absentpercent = ($absentcount/$totalcount)*100;
					
					if($presentpercent < 75.00 && $totalcount != 0) {
						$data->table[$i][] = $i+1;
						$data->table[$i][] = '!!r'.$user->idnumber;
						
						
						$data->table[$i][] = '!!r'.$user->firstname." ".$user->lastname;
					   
						if (isset($formdata->showremarks)) {
							//$data->table[$i][] = $rec->remarks;//$statuses[$rec->statusid]->acronym;
							$cellsgenerator = new user_sessions_cells_text_generator($reportdata, $user);
							$data->table[$i] = array_merge($data->table[$i], $cellsgenerator->get_cells_with_remarks());  // use of updated function for fething remarks
						}
						else {
							$cellsgenerator = new user_sessions_cells_text_generator($reportdata, $user);
							$data->table[$i] = array_merge($data->table[$i], $cellsgenerator->get_cells());
						}
						
						foreach ($reportdata->statuses as $status) {
						// update to get_attendance_bydate incase range is selected
							if (has_capability('mod/attforblock:unmarkattendances', $context)) {
								if (array_key_exists($status->id, $statsarray)) {
									if($status->acronym == "P") {
											$data->table[$i][] = '!!r'.$statsarray[$status->id]->stcnt;
											//$presentcount = $statsarray[$status->id]->stcnt;
									}
									elseif($status->acronym == "A") {
											$data->table[$i][] = '!!r'.$statsarray[$status->id]->stcnt;
											//$absentcount = $statsarray[$status->id]->stcnt;
									}
									else {
											//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
											//$unmarkedcount = $statsarray[$status->id]->stcnt;
									}
								}
								else
								{
									if($status->acronym == "UM") {
									}
									else
										$data->table[$i][] = '!!r'."0";
								}
							}
							else {
								if (array_key_exists($status->id, $statsarray)) {
									if($status->acronym == "P") {
											$data->table[$i][] = '!!r'.$statsarray[$status->id]->stcnt;
											//$presentcount = $statsarray[$status->id]->stcnt;
									}
								elseif($status->acronym == "A") {
											$data->table[$i][] = '!!r'.$statsarray[$status->id]->stcnt;
											//$absentcount = $statsarray[$status->id]->stcnt;
										}
										else {}
								}
								else
								{
									if($status->acronym == "UM") {
									}
									else
										$data->table[$i][] = '!!r'."0";
								}	
								
							}
						}
						
						$data->table[$i][] = '!!r'.$totalcount;
						// $row->cells[] = get_percent($user->id, $course,$where1);
						// $row->cells[] = (100-get_percent($student->id, $course,$where1));
						$data->table[$i][] = '!!r'.sprintf("%0.{$decimalpoints}f", $presentpercent)."%";
						$data->table[$i][] = '!!r'.sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
						
						if ($reportdata->gradable)
							$data->table[$i][] = '!!r'.$reportdata->grades[$user->id].' / '.$reportdata->maxgrades[$user->id];
						$i++;
					}
					else {
						$data->table[$i][] = $i+1 ;
						$data->table[$i][] = $user->idnumber;
						
						
						$data->table[$i][] = $user->firstname." ".$user->lastname;
					   
						if (isset($formdata->showremarks)) {
							//$data->table[$i][] = $rec->remarks;//$statuses[$rec->statusid]->acronym;
							$cellsgenerator = new user_sessions_cells_text_generator($reportdata, $user);
							$data->table[$i] = array_merge($data->table[$i], $cellsgenerator->get_cells_with_remarks());  // use of updated function for fething remarks
						}
						else {
							$cellsgenerator = new user_sessions_cells_text_generator($reportdata, $user);
							$data->table[$i] = array_merge($data->table[$i], $cellsgenerator->get_cells());
						}
						
						foreach ($reportdata->statuses as $status) {
						// update to get_attendance_bydate incase range is selected
							if (has_capability('mod/attforblock:unmarkattendances', $context)) {
								if (array_key_exists($status->id, $statsarray)) {
									if($status->acronym == "P") {
											$data->table[$i][] = $statsarray[$status->id]->stcnt;
											//$presentcount = $statsarray[$status->id]->stcnt;
									}
									elseif($status->acronym == "A") {
											$data->table[$i][] = $statsarray[$status->id]->stcnt;
											//$absentcount = $statsarray[$status->id]->stcnt;
									}
									else {
											//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
											//$unmarkedcount = $statsarray[$status->id]->stcnt;
									}
								}
								else
								{
									if($status->acronym == "UM") {
									}
									else
										$data->table[$i][] = 0;
								}
							}
							else {
								if (array_key_exists($status->id, $statsarray)) {
									if($status->acronym == "P") {
											$data->table[$i][] = $statsarray[$status->id]->stcnt;
											//$presentcount = $statsarray[$status->id]->stcnt;
									}
								elseif($status->acronym == "A") {
											$data->table[$i][] = $statsarray[$status->id]->stcnt;
											//$absentcount = $statsarray[$status->id]->stcnt;
										}
										else {}
								}
								else
								{
									if($status->acronym == "UM") {
									}
									else
										$data->table[$i][] = 0;
								}	
								
							}
						}
						
						$data->table[$i][] = $totalcount;
						// $row->cells[] = get_percent($user->id, $course,$where1);
						// $row->cells[] = (100-get_percent($student->id, $course,$where1));
						$data->table[$i][] = sprintf("%0.{$decimalpoints}f", $presentpercent)."%";
						$data->table[$i][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
						
						if ($reportdata->gradable)
							$data->table[$i][] = $reportdata->grades[$user->id].' / '.$reportdata->maxgrades[$user->id];
						$i++;
					} // end else
				} // end if not joined
			} // end if school
			else {
				$attdata = array();
				$enrolmentsparams = array();
				$coursestartdate = array();
				
				
				$sql1 = "SELECT ue.userid, ue.status, ue.timestart, ue.timeend, ue.timecreated
						  FROM {user_enrolments} ue
						  JOIN {enrol} e ON e.id = ue.enrolid
						 WHERE ue.userid = :uid
							   AND e.courseid = :courseid";
					  //GROUP BY ue.userid, ue.status, ue.timestart, ue.timeend";
				$params1 = array('uid'=> $user->id, 'courseid'=>$course->id);
				$enrolmentsparams = $DB->get_record_sql($sql1, $params1);
				
				$sql2 = "SELECT startdate
						  FROM {course} 
						  WHERE id = :courseid";
					  //GROUP BY ue.userid, ue.status, ue.timestart, ue.timeend";
				$params2 = array('courseid'=>$course->id);
				$coursestartdate = $DB->get_record_sql($sql2, $params2);
				$csdate = $coursestartdate->startdate;
				//$string = var_export($csdate, true);
				// date ranges included - added by Qurrat-ul-ain Babar (20th Dec, 2013)
				
				$where = "ats.attendanceid = :aid AND ats.sessdate >= :csdate AND ats.sessdate >= :enrolstart AND
						  al.studentid = :uid";
			
				$qry3 = "SELECT ats.id, al.studentid, al.statusid, ats.description
						  FROM {attendance_log} al
						  JOIN {attendance_sessions} ats
							ON al.sessionid = ats.id
						 WHERE $where";
					  //GROUP BY ats.description";
		
						
					$params3 = array(
						'uid'        => $user->id,
						'aid'        => $att->id,
						'csdate'     => $csdate,
						'enrolstart' => $enrolmentsparams->timestart);

				
				$attdata = $DB->get_records_sql($qry3, $params3);
				
				$statsarray = array();
				foreach ($attdata as $status) {
					//echo "<br/> $status->statusid: Status count: $status->stcnt<br/>";
					$classtype = $status->description;
					switch ($classtype) {
						case "90-Mins Lecture":
							//echo "Its a 90-Mins Lecture";
							if (array_key_exists($status->statusid, $statsarray))
							{
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 1.5;
							}
							else
							{
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 1.5;
							}
							break;
						case "Two Hours Lecture":
							//echo "Its a Two Hours Lecture";
							if (array_key_exists($status->statusid, $statsarray))
							{
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 2;
							}
							else
							{
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 2;
							}
							break;
						case "Three Hours Lecture":
							//echo "Its a Three Hours Lecture";
							if (array_key_exists($status->statusid, $statsarray))
							{
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 3;
							}
							else
							{
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 3;
							}
							break;
						case "Three Hours Studio":
							//echo "Its a Three Hours Studio";
							if (array_key_exists($status->statusid, $statsarray))
							{
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 1.5;
							}
							else
							{
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 1.5;
							}
							break;
						case "Two Hours Ward":
							//echo "Its a Three Hours Studio";
							if (array_key_exists($status->statusid, $statsarray))
							{
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 2;
							}
							else
							{
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 2;
							}
							break;
						case "Four Hours Ward":
							//echo "Its a Three Hours Studio";
							if (array_key_exists($status->statusid, $statsarray))
							{
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 4;
							}
							else
							{
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 4;
							}
							break;
						default:
							//echo "Default lecture ";
							if (array_key_exists($status->statusid, $statsarray))
							{
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 1;
							}
							else
							{
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 1;
							}
					}
				}  
				
				$presentcount = 0;
				$absentcount = 0;
				$unmarkedcount = 0;
				$totalcount = 0;
				
				foreach ($reportdata->statuses as $status) {
				// update to get_attendance_bydate incase range is selected
					if (has_capability('mod/attforblock:unmarkattendances', $context)) {
						if (array_key_exists($status->id, $statsarray)) {
							if($status->acronym == "P") {
									//$data->table[$i][] = $statsarray[$status->id]->stcnt;
									$presentcount = $statsarray[$status->id]->stcnt;
							}
							elseif($status->acronym == "A") {
									//$data->table[$i][] = $statsarray[$status->id]->stcnt;
									$absentcount = $statsarray[$status->id]->stcnt;
							}
							else {
									//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									$unmarkedcount = $statsarray[$status->id]->stcnt;
							}
						}
						else
						{
						}
					}
					else {
						if (array_key_exists($status->id, $statsarray)) {
							if($status->acronym == "P") {
									//$data->table[$i][] = $statsarray[$status->id]->stcnt;
									$presentcount = $statsarray[$status->id]->stcnt;
							}
						elseif($status->acronym == "A") {
									//$data->table[$i][] = $statsarray[$status->id]->stcnt;
									$absentcount = $statsarray[$status->id]->stcnt;
								}
								else {}
						}
						else
						{
						}	
						
					}
				}
				$totalcount = $presentcount + $absentcount;
				$presentpercent = ($presentcount/$totalcount)*100;
				$absentpercent = ($absentcount/$totalcount)*100;
				
				if($presentpercent < 75.00 && $totalcount != 0) {
					$data->table[$i][] = $i+1;
					$data->table[$i][] = '!!r'.$user->idnumber;
					
					
					$data->table[$i][] = '!!r'.$user->firstname." ".$user->lastname;
				   
					if (isset($formdata->showremarks)) {
						//$data->table[$i][] = $rec->remarks;//$statuses[$rec->statusid]->acronym;
						$cellsgenerator = new user_sessions_cells_text_generator($reportdata, $user);
						$data->table[$i] = array_merge($data->table[$i], $cellsgenerator->get_cells_with_remarks());  // use of updated function for fething remarks
					}
					else {
						$cellsgenerator = new user_sessions_cells_text_generator($reportdata, $user);
						$data->table[$i] = array_merge($data->table[$i], $cellsgenerator->get_cells());
					}
					
					foreach ($reportdata->statuses as $status) {
					// update to get_attendance_bydate incase range is selected
						if (has_capability('mod/attforblock:unmarkattendances', $context)) {
							if (array_key_exists($status->id, $statsarray)) {
								if($status->acronym == "P") {
										$data->table[$i][] = '!!r'.$statsarray[$status->id]->stcnt;
										//$presentcount = $statsarray[$status->id]->stcnt;
								}
								elseif($status->acronym == "A") {
										$data->table[$i][] = '!!r'.$statsarray[$status->id]->stcnt;
										//$absentcount = $statsarray[$status->id]->stcnt;
								}
								else {
										//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										//$unmarkedcount = $statsarray[$status->id]->stcnt;
								}
							}
							else
							{
								if($status->acronym == "UM") {
								}
								else
									$data->table[$i][] = '!!r'."0";
							}
						}
						else {
							if (array_key_exists($status->id, $statsarray)) {
								if($status->acronym == "P") {
										$data->table[$i][] = '!!r'.$statsarray[$status->id]->stcnt;
										//$presentcount = $statsarray[$status->id]->stcnt;
								}
							elseif($status->acronym == "A") {
										$data->table[$i][] = '!!r'.$statsarray[$status->id]->stcnt;
										//$absentcount = $statsarray[$status->id]->stcnt;
									}
									else {}
							}
							else
							{
								if($status->acronym == "UM") {
								}
								else
									$data->table[$i][] = '!!r'."0";
							}	
							
						}
					}
					
					$data->table[$i][] = '!!r'.$totalcount;
					// $row->cells[] = get_percent($user->id, $course,$where1);
					// $row->cells[] = (100-get_percent($student->id, $course,$where1));
					$data->table[$i][] = '!!r'.sprintf("%0.{$decimalpoints}f", $presentpercent)."%";
					$data->table[$i][] = '!!r'.sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
					
					if ($reportdata->gradable)
						$data->table[$i][] = '!!r'.$reportdata->grades[$user->id].' / '.$reportdata->maxgrades[$user->id];
					$i++;
				}
				else {
					$data->table[$i][] = $i+1 ;
					$data->table[$i][] = $user->idnumber;
					
					
					$data->table[$i][] = $user->firstname." ".$user->lastname;
				   
					if (isset($formdata->showremarks)) {
						//$data->table[$i][] = $rec->remarks;//$statuses[$rec->statusid]->acronym;
						$cellsgenerator = new user_sessions_cells_text_generator($reportdata, $user);
						$data->table[$i] = array_merge($data->table[$i], $cellsgenerator->get_cells_with_remarks());  // use of updated function for fething remarks
					}
					else {
						$cellsgenerator = new user_sessions_cells_text_generator($reportdata, $user);
						$data->table[$i] = array_merge($data->table[$i], $cellsgenerator->get_cells());
					}
					
					foreach ($reportdata->statuses as $status) {
					// update to get_attendance_bydate incase range is selected
						if (has_capability('mod/attforblock:unmarkattendances', $context)) {
							if (array_key_exists($status->id, $statsarray)) {
								if($status->acronym == "P") {
										$data->table[$i][] = $statsarray[$status->id]->stcnt;
										//$presentcount = $statsarray[$status->id]->stcnt;
								}
								elseif($status->acronym == "A") {
										$data->table[$i][] = $statsarray[$status->id]->stcnt;
										//$absentcount = $statsarray[$status->id]->stcnt;
								}
								else {
										//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										//$unmarkedcount = $statsarray[$status->id]->stcnt;
								}
							}
							else
							{
								if($status->acronym == "UM") {
								}
								else
									$data->table[$i][] = 0;
							}
						}
						else {
							if (array_key_exists($status->id, $statsarray)) {
								if($status->acronym == "P") {
										$data->table[$i][] = $statsarray[$status->id]->stcnt;
										//$presentcount = $statsarray[$status->id]->stcnt;
								}
							elseif($status->acronym == "A") {
										$data->table[$i][] = $statsarray[$status->id]->stcnt;
										//$absentcount = $statsarray[$status->id]->stcnt;
									}
									else {}
							}
							else
							{
								if($status->acronym == "UM") {
								}
								else
									$data->table[$i][] = 0;
							}	
							
						}
					}
					
					$data->table[$i][] = $totalcount;
					// $row->cells[] = get_percent($user->id, $course,$where1);
					// $row->cells[] = (100-get_percent($student->id, $course,$where1));
					$data->table[$i][] = sprintf("%0.{$decimalpoints}f", $presentpercent)."%";
					$data->table[$i][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
					
					if ($reportdata->gradable)
						$data->table[$i][] = $reportdata->grades[$user->id].' / '.$reportdata->maxgrades[$user->id];
					$i++;
				} // end else
			}
			
			
        }

        if ($formdata->format === 'text') {
            ExportToCSV($data, $filename);
        } else {
            ExportToTableEd($data, $filename, $formdata->format);
        }
        exit;
    } else {
        print_error('studentsnotfound', 'attendance', $att->url_manage());
    }
}

$output = $PAGE->get_renderer('mod_attforblock');
$tabs = new attforblock_tabs($att, attforblock_tabs::TAB_EXPORT);
echo $output->header();
echo $output->heading(get_string('attendanceforthecourse','attforblock').' :: ' .$course->fullname);
echo $output->render($tabs);

$mform->display();

echo $OUTPUT->footer();


function ExportToTableEd($data, $filename, $format) {
	global $CFG;

    if ($format === 'excel') {
	    require_once("$CFG->libdir/excellib.class.php");
	    $filename .= ".xls";
	    $workbook = new MoodleExcelWorkbook("-");
    } else {
	    require_once("$CFG->libdir/odslib.class.php");
	    $filename .= ".ods";
	    $workbook = new MoodleODSWorkbook("-");
    }
/// Sending HTTP headers
	ob_clean();
    $workbook->send($filename);
/// Creating the first worksheet
    $myxls = $workbook->add_worksheet('Attendances');
/// format types
    $formatbc = $workbook->add_format();
    $formatbc->set_bold(1);
	
	$red =& $workbook->add_format();
	$red->set_bold(0);            // Make it bold
	$red->set_fg_color(10);
	$red->set_align('center');

    $myxls->write(0, 0, get_string('course'), $formatbc);
    $myxls->write(0, 1, $data->course);
	$myxls->write(1, 0, get_string('teacher','attforblock'), $formatbc);
    $myxls->write(1, 1, $data->teacher);
    $myxls->write(2, 0, get_string('group'), $formatbc);
    $myxls->write(2, 1, $data->group);
	

    $i = 4;
    $j = 0;
	//$formatbc->set_size(10);
    foreach ($data->tabhead as $cell) {
    	$myxls->write($i, $j++, $cell, $formatbc);
    }
	
    $i++;
    $j = 0;
    foreach ($data->table as $row) {
    	foreach ($row as $cell) {
			if(preg_match('/^!!r/',$cell)){
					$cell = str_replace("!!r",'',$cell);
					$myxls->write_string($i, $j++, $cell,$red);
			}
			else
				$myxls->write($i, $j++, $cell);
    	}
		$i++;
		$j = 0;
    }
	$workbook->close();
}

function ExportToCSV($data, $filename) {
    $filename .= ".txt";

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    echo get_string('course')."\t".$data->course."\n";
	 echo get_string('teacher', 'attforblock')."\t"."Teacher"."\n\n";
    echo get_string('group')."\t".$data->group."\n\n";
    //echo get_string('teacher', 'attforblock')."\t"."Teacher"."\n\n";
	

    echo implode("\t", $data->tabhead)."\n";
    foreach ($data->table as $row) {
    	echo implode("\t", $row)."\n";
    }
}
