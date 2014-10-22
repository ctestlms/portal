<?php

require_once('../../config.php');
require_once($CFG->libdir.'/blocklib.php');
require_once($CFG->libdir.'/formslib.php');
require_once('./view_attendance_report_form.php');
require_once('../../mod/attforblock/locallib.php');
require('../../mod/attforblock/tcpdf/config/lang/eng.php');
require('../../mod/attforblock/tcpdf/tcpdf.php');
include('dbcon.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

require_login($course->id);
session_start();
$decimalpoints = 2;

function get_percent_absent($userid, $course, $where='')
{
    global $DB;
	
    $category = $course->category;
    $semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
    $path = explode("/", $semester->path);
    $school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]");
	//echo "School: ".$school->name."<br/>";
	 
	$aid = $DB->get_record_sql("SELECT id from {attforblock} where course=$course->id");
	//print_r($aid);
	$sql1 = "SELECT ue.userid, ue.status, ue.timestart, ue.timeend
			  FROM {user_enrolments} ue
			  JOIN {enrol} e ON e.id = ue.enrolid
			 WHERE ue.userid = :uid
				   AND e.courseid = :courseid";
		  //GROUP BY ue.userid, ue.status, ue.timestart, ue.timeend";
	$params1 = array('uid'=> $userid, 'courseid'=>$course->id);
	$enrolmentsparams = $DB->get_record_sql($sql1, $params1);
	
	$presentqry="select * from {attendance_statuses} where attendanceid=".$aid->id." AND description='Present'";
	$present=$DB->get_record_sql($presentqry);
	//print_r($present->id);
	$absentqry="select * from {attendance_statuses} where attendanceid=".$aid->id." AND description='Absent'";
	$absent=$DB->get_record_sql($absentqry);
	
			// date ranges included - added by Qurrat-ul-ain Babar (20th Dec, 2013)
			$where = "ats.attendanceid = :aid AND ats.sessdate >= :csdate AND ats.sessdate >= :enrolstart AND
						  al.studentid = :uid";
			
			$qry = "SELECT ats.id, al.studentid, al.statusid, ats.description
                      FROM {attendance_log} al
                      JOIN {attendance_sessions} ats
                        ON al.sessionid = ats.id
                     WHERE $where
                  GROUP BY ats.id";
					
			$params = array(
				'uid'       => $userid,
				'aid'       => $aid->id,
				'csdate'    => $course->startdate,
				'enrolstart' => $enrolmentsparams->timestart);

			
			$data = $DB->get_records_sql($qry, $params);
			
			
			$statsarray = array();

			foreach ($data as $status) {
				//echo "<br/> $status->statusid: Status count: $status->stcnt<br/>";
				$classtype = $status->description;
				if($school->name == "Army Medical College (AMC)") {
					switch ($classtype) {
						case "90-Mins Lecture":
							//echo "Its a 90-Mins Lecture";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 1.5;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 1.5;
							}
							break;
						case "Two Hours Lecture":
							//echo "Its a Two Hours Lecture";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 2;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 2;
							}
							break;
						case "Three Hours Lecture":
							//echo "Its a Three Hours Lecture";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 3;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 3;
							}
							break;
						case "Two Hours Lab":
							//echo "Its a Three Hours Studio";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 2;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 2;
							}
							break;
						case "Three Hours Lab":
							//echo "Its a Three Hours Studio";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 3;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 3;
							}
							break;
						case "Two Hours Studio":
							//echo "Its a Three Hours Studio";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 2;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 2;
							}
							break;
						case "Three Hours Studio":
							//echo "Its a Three Hours Studio";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 3;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 3;
							}
							break;
						case "Two Hours Ward":
							//echo "Its a Three Hours Studio";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 2;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 2;
							}
							break;
						case "Four Hours Ward":
							//echo "Its a Three Hours Studio";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 4;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 4;
							}
							break;
						default:
							//echo "Default lecture ";
							if (array_key_exists($status->statusid, $statsarray)) {
								$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + 1;
							}
							else {
								$statsarray[$status->statusid]->statusid = $status->statusid;
								$statsarray[$status->statusid]->stcnt = 1;
							}
					}
				}
				else {
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
				
            }
			//print_r($statsarray);
			//return $statsarray;
			
			$presentcount = $statsarray[$present->id]->stcnt;
			//echo "Present count: ".$presentcount;
						
			$absentcount = $statsarray[$absent->id]->stcnt;
			//echo "ABsent count: ".$absentcount;
						
			$totalcount = $presentcount + $absentcount;
			//echo "Total count: ".$totalcount;
						
			if($presentcount)
				$percent = ($presentcount / $totalcount) * 100;
			else {
				$presentcount = 0;
				$percent = ($presentcount / $totalcount) * 100;
			}
			
			if(!$absentcount) {
				$absentcount = 0;
			}
			
			$sessions = array();
			$sessions['present'] = $presentcount;
			$sessions['absent'] = $absentcount;
			$sessions['total'] = $totalcount;
			return $sessions;			
}

$categoryid = optional_param('id', '-1', PARAM_INT);
$type = optional_param('type', '-1', PARAM_INT);
if($type==1){
	$reportname="Attendance Report";
}
if($type==2){
	$reportname="Short Attendance Report";
}
if($type==3){
	$reportname="Absentee Report (Period Wise)";
}
$export = optional_param('export',false, PARAM_BOOL);
$template = optional_param('template',false, PARAM_BOOL);
$content = explode('@',$_POST['content']);
if($template){
	//echo $content;
	ExportToPDF($content);

}

$perd = optional_param('perd',false, PARAM_BOOL);


$start = $_POST['start_time'];
$end =  $_POST['end_time'];


$context = get_context_instance(CONTEXT_COURSECAT, $categoryid);

if($categoryid!=-1)
require_capability('block/custom_reports:getattendancereport', $context);
$report=get_string('attendance_custom_reports', 'block_custom_reports');
$navlinks[] = array('name' => get_string('attendance_custom_reports', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if(!$export)
print_header('Attendance Custom Report', 'Attendance Custom Report', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);

if($courses = get_courses($categoryid, 'c.id ASC', 'c.id, c.fullname, c.startdate, c.idnumber, c.shortname, c.category') or $export or $perd){
	$mform = new mod_custom_reports_view_attendance_report_form('attendance.php', array('courses'=>$courses, 'categoryid'=>$categoryid,'report'=>$report));
	if($fromform = $mform->get_data() or $export or $perd){
	 $formdata = $mform->get_data();
	 
		
		$cselected = array();
		if($export or $perd){

			$export_courses = required_param('courses',PARAM_TEXT);

			if($type==1 || $type==2 || $type==3){
				$sessions_margin = array_reverse(explode(",", optional_param('sessions','',PARAM_TEXT)));
				$cselected["margin"][] = array_pop($sessions_margin);

			}

			//echo "select id, fullname, startdate, idnumber, shortname from {$CFG->prefix}courses where id IN ({$export_courses})";
			$courses = $DB->get_records_sql("select id, fullname, startdate, idnumber, shortname, category from {course} where id IN ({$export_courses})");
			$export_courses_sessions = "";
		}else{

			$export_courses = "";
		}
		$temp="";

		if($type==2 || $type==3){

			$table = new html_table();
			$table->head = array();

			/*$table->head[] = "S/No";
			 $table->align[] = 'center';
			 $table->size[] = '40px';
			 $table->headspan[] = 1;*/
		}

		$j=0;
		$i=0;
		foreach($courses as $course) {
			
			if((!$export AND $fromform->{'c'.$course->id}=='true') OR ($export or $perd)){
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
				$query = "SELECT u.* from mdl_user u
                    JOIN {$CFG->prefix}role_assignments ra ON ra.userid=u.id
                    JOIN {$CFG->prefix}role r ON ra.roleid = r.id
                    JOIN {$CFG->prefix}context c ON ra.contextid = c.id
                    where r.name = 'Student' and
                    c.contextlevel = 50 and
                    c.instanceid = {$course->id}";
				if($hidden_role_assignment != "")
					$query .= " order by u.firstname";

				$cselected["id"][] = $course->id;
				$cselected["name"][] = $course->fullname."  ".$school->name;
				$cselected["school"][] = $school->name;
				

				if($type==2  ||  $type==3){
					$table->head[] = $course->fullname;
					$table->align[] = 'center';
					$table->size[] = '40px';
					$table->headspan[] = 5;
					$row=0;
					$table->data[$row][]="Fullname";
					$table->data[$row][]="Reg: No";
					$table->data[$row][]="Abs %";
					$table->data[$row][]="Course Sessions";
					$table->data[$row][]="Sessions Missed";
				}
				$cselected["shortname"][] = $course->shortname;
				$cselected["idnumber"][] =  $course->idnumber;
				$cselected["students"][] =  $DB->get_records_sql($query);
				//print_r($cselected);
				if($type==2 || $type==3){

					$students =  $DB->get_records_sql($query);
					$row=1;
					$flag=false;
					foreach($students as $student){
						
						
							if($perd or ($export && $start)){
								$where="sessdate between $start and $end";
							}
							$course->attendance_margin = $fromform->{'session'.$course->id};
							if($export){
								$course->attendance_margin = $cselected["margin"][$j];
							}
							//$attendance = get_percent_absent($students["userid"][$row_ite], $course, '');
							$attendance = get_percent_absent($student->id, $course,'');
							
							$presentcount = 0;
							$absentcount = 0;
							$totalcount = 0;
							$presentpercent = 0;
							$absentpercent = 0;
							//get_percent_absent($students->id, $course, '');
							
							foreach($attendance as $key => $value) {
								if ($key == "present") {
									//$presentcount = $value+$fromform->{'session'.$course->id};
									
									$presentcount = $value+$_POST['session'.$course->id];
									
								}
								if ($key == "absent") {
									$absentcount = $value;
								}
								if ($key == "total") {
									//$totalcount = $value+$fromform->{'session'.$course->id};
									$totalcount = $value+$_POST['session'.$course->id];
								}					
							}
							
							if($presentcount)
								$presentpercent = ($presentcount / $totalcount) * 100;
							else {
								$presentcount = 0;
								$presentpercent = ($presentcount / $totalcount) * 100;
							}
							
							if($absentcount)
								$absentpercent = ($absentcount / $totalcount) * 100;
							else {
								$absentcount = 0;
								$absentpercent = ($absentcount / $totalcount) * 100;
							}
							
							$course_sess_att = $presentcount;
							$course_sessions = $totalcount;
							$all_sessions+=$course_sessions;
							$course_sess_missed = $absentcount;
							$all_sessions_missed+=$course_sess_missed;
							//echo "<br/>";
							

							if($type==3){
								if($absentpercent == 100) {
									for ($k=0;$k<$i;$k++){
										if( $table->data[$row][$k]==""){
											$table->data[$row][$k]="---";
											$table->data[$row][$k+1]="---";
											$table->data[$row][$k+2]="---";
										}
									}
									$warning[$student->id][]=$course->fullname."|".$student->firstname." ".$student->lastname."|".sprintf("%0.{$decimalpoints}f", $absentpercent)."%"."|".$course->startdate."|".$student->idnumber."|".$student->address."|".$student->phone2."|".$student->fathername;
									$table->data[$row][$i]=  $student->firstname.' '.$student->lastname;
									$table->data[$row][$i+1]=$student->idnumber;
									$table->data[$row][$i+2]=sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
									$row++;
								}
								elseif($absentpercent < 100 && $table->data[$row][$i-1]!="" ){
									$table->data[$row][$i]  ="---";//$student->idnumber."<br/>".$student->firstname.' '.$student->lastname."<br/>".$attendance."%";
									$table->data[$row][$i+1]="---";
									$table->data[$row][$i+2]="---";
								}
								else {}
							}
							
							if($type==2){	 
							
								// adding new percent value taken from the text field. //
								$percentage = $_POST['percent'];
									
								if($absentpercent >= $percentage){
									for ($k=0;$k<$i;$k++){
										if( $table->data[$row][$k]==""){
											$table->data[$row][$k]="---";
											$table->data[$row][$k+1]="---";
											$table->data[$row][$k+2]="---";
											$table->data[$row][$k+3]="---";
											$table->data[$row][$k+4]="---";
										}
									}
									$flag=false;
									$warning[$student->id][]=$course->fullname."|".$student->firstname." ".$student->lastname."|".sprintf("%0.{$decimalpoints}f", $absentpercent)."%"."|".$course->startdate."|".$student->idnumber."|".$student->address."|".$student->phone2."|".$student->fathername;
									$table->data[$row][$i]=$student->firstname.' '.$student->lastname;
									$table->data[$row][$i+1]=$student->idnumber;
									$table->data[$row][$i+2]=sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
									$table->data[$row][$i+3]=$course_sessions!= "" ? $course_sessions : "-";
									$table->data[$row][$i+4]=$course_sess_missed!= "" ? $course_sess_missed : "-";
									$row++;
								}
								else if($absentpercent < $percentage && $table->data[$row][$i-1]!="" ){
									$table->data[$row][$i]="---";//$student->idnumber."<br/>".$student->firstname.' '.$student->lastname."<br/>".$attendance."%";
									$table->data[$row][$i+1]="---";
									$table->data[$row][$i+2]="---";
									$table->data[$row][$i+3]="---";
									$table->data[$row][$i+4]="---";
								}
								else {}
							}	
					}
				}

				////
				if($type==1 || $type==2 || $type==3){
					$cselected["startdate"][] = $course->startdate;
					$cselected["category"][] = $course->category;
					if($export)
					$cselected["margin"][] = array_pop($sessions_margin);
					else
					//$cselected["margin"][] = $fromform->{'session'.$course->id};
					$cselected["margin"][] =$_POST['session'.$course->id];
				}
				$i+=5;

			}
			if($fromform->{'c'.$course->id}=='true' and !$export){
				$export_courses .= $course->id.",";
				if($type==1 || $type==2 || $type==3){
					$export_courses_sessions .= $fromform->{'session'.$course->id}.",";
				}
			}

		//	$i+=3;
				
			$j++;


		}
		//for warning letter templates
		$content="";
		$classs=$DB->get_record_sql("SELECT path from {course_categories} cat WHERE id =$categoryid");
		$path=explode("/", $classs->path);

		$school=$DB->get_record_sql("SELECT name,id from {course_categories} ct WHERE path like '/$path[1]' and parent=0");
		$school_info=$DB->get_record_sql("Select * from {school_info} WHERE id=$school->id");


		$degree= $class->name;
		$class=$DB->get_record_sql("SELECT name,id from {course_categories} ct WHERE id =$categoryid");
			
		$semester= $class->name;
                if($categoryid!="")
		$class=$DB->get_record_sql("SELECT name,id from {course_categories} ct WHERE id =(SELECT parent from {course_categories} cat WHERE id =$categoryid)");
			
		$degree= $class->name;
		if($class->id!="")
                    $class=$DB->get_record_sql("SELECT name,id from {course_categories} ct WHERE id =(SELECT parent from {course_categories} cat WHERE id =$class->id)");
		if($class->id!="")
                    $class=$DB->get_record_sql("SELECT name,id from {course_categories} ct WHERE id =(SELECT parent from {course_categories} cat WHERE id =$class->id)");
		$graduate= $class->name;
                $string = $graduate;
                $find = "Undergraduate";
                if(strstr($string, $find) ==true){
                        $fileno=$school_info->ug_fileno;
                }
                 $find = "Postgraduate";
                if(strstr($string, $find) ==true){
                        $fileno=$school_info->pg_fileno;
                }

                $cntnts=array();

		foreach ($warning as $warnin){
			$i=1;
			//$image="NUST_Logo.jpg";
			//$content.='<img src="NUST_Logo.jpg"></img>';
			$content='';		
			$content1='';
			$content1.='<br/><br/><table align="center" border="1"><tr><td>S/No</td><td>Subject</td><td>Abs %</td></tr>';
			foreach ($warnin as $warn){
				$letter = explode("|", $warn);
				$content1.='<tr><td>'.$i.'</td><td>'.$letter[0].'</td><td>'.$letter[2].'</td></tr>';
				$i++;
			}
			$content1.='</table>';
		
$content.='<table width="100%" border="0"><tr><td width="150"><img src="NUST_Logo.jpg" height="52" width="52" /></td><td align="center">'.$school->name.
'
<br/>National University of Sciences & Technology<br/>Sector H-12, Islamabad – 44000, Pakistan</td></tr></table>';

 $content.='<table  border="0"><tr><td>&nbsp;</td><td align="right"><br/>'.$fileno.
'<br/> Tel: '.$school_info->phone.'
<br/> '.date('M Y').'</td></tr></table>';
$address=explode('*',$letter[5]);

		$content.='<br/><br/><table align="left" border="0"><tr align="left"><td width="40">To:</td><td align="left"> Mr. '.$letter[7].'</td></tr><tr><td width="40">&nbsp;</td><td align="left">(Father of '.$letter[1].') </td></tr>
            <tr><td width="40">&nbsp;</td><td>'.$address[0].'</td></tr>
                  <tr><td width="40">&nbsp;</td><td> '.$address[1].'</td></tr>

            <tr><td width="40">&nbsp;</td><td>Telephone: '.$letter[6].'</td></tr></table>';
	
$content.='<br/>Subject: <b>Attendance – ['.$graduate.'] ['.$degree.'] </b> <br/>';				
			$content.='<li>1. I would like to inform you that your ward ['.$letter[1].'] [ '. $letter[4].']  student of '.$semester.' '  .$degree.' was absent for numerous periods in the subject/s shown below during the current semester which commenced on '.date("d-m-Y",$letter[3]) .':-</li> ';
			$content.=$content1;
			$content.='<br/><li>2. The present attendance of your ward is falling short of the minimum requisite criteria of 75% under the provision of Para 33 (a) Chapter V of NUST Statues/ Regulations. You are, therefore, required to please ensure his/her regularity in all subjects, failing which institute authorities will be forced to take strict action and he/she will not be allowed to take the end semester examination in respective subject/s.</li>';
			$content.='<br/><li>3. Please acknowledge the receipt.</li><br/><br/>';
			$content.='<table align="right"><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Deputy Controller of Examinations</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>('.$school_info->controller_name.')</td></tr></table>';
			$content.='</table>@';
			$cntnts[]=$content;

		}
		//echo $content;
		///
			
			


		///


		if($type==1){
			$table = new html_table();
			$table->head = array();

			$table->head[] = 'S.No';
			$table->align[] = 'center';
			$table->size[] = '40px';
			$table->headspan[] = 1;

			$table->head[] = 'Registration No';
			$table->align[] = 'center';
			$table->size[] = '220px';
			$table->headspan[] = 1;

			$table->head[] = "Name";
			$table->align[] = 'center';
			$table->size[] = '150px';
			$table->headspan[] = 1;
			$user_sessions=array();
			$TotalSessions=0;

			for($i=0; $i<count($cselected["id"]); $i++){
				$temp_stu = array_slice($cselected["students"][$i],0,1);
				$temp_course = $DB->get_record('course', array('id'=> $cselected['id'][$i]));
				$temp_course->attendance_margin = $cselected['margin'][$i];
				// Get school - added by Qurrat-ul-ain Babar (21st Oct, 2014)
				$category = $temp_course->category;
				$semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
				$path = explode("/", $semester->path);
				$school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]"); 

				$courseCol = $cselected['name'][$i]."<br/>".$max_sessions;

				$table->headspan[] = 4;
				$table->head[] = $courseCol;
				$table->align[] = 'center';
				$table->size[] = '25px';

				if($i==0){

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
					$row3->cells = array($cell1, $cell2,$cell3);
				}

				$cell9 = new html_table_cell();
				$cell9->text =  "<b>Present %Age</b>";
				$cell9->colspan = 1;

				$cell10 = new html_table_cell();
				$cell10->text =  "<b>Absent %Age</b>";
				$cell10->colspan = 1;
				$cell11 = new html_table_cell();
				$cell11->text =  "<b>Course Sessions</b>";
				$cell11->colspan = 1;
				$cell12 = new html_table_cell();
				$cell12->text =  "<b>Sessions Missed</b>";
				$cell12->colspan = 1;

				$row1->cells[] = $cell5;
				$row3->cells[] = $cell9;
				$row3->cells[] = $cell10;
				$row3->cells[] = $cell11;
				$row3->cells[] = $cell12;


				foreach($cselected["students"][$i] as $student){
					if(!in_array($student->id, $students["userid"])){
						// Get reg status of student and school of the course - Added by Qurrat-ul-ain Babar (21st Oct, 2014)
						$record = $DB->get_record('user',array('id'=>$student->id));  
						profile_load_data($record);
						$regstatus = $record->profile_field_registrationstatus;
						
						$students["userid"][] = $student->id;
						$students["idnumber"][] = $student->idnumber;
						$students["name"][] = $student->firstname.' '.$student->lastname;
						// added by Qurrat-ul-ain Babar (21st Oct, 2014)
						$students["reg"][] = $regstatus;
						$students["school"][] = $school->name;
					}
				}
			}
			$cell14 = new html_table_cell();
			$cell14->text =  "";
			$cell14->colspan = 1;
			$row3->cells[] = $cell14;
			$row3->cells[] = $cell14;
			$row3->cells[] = $cell14;
			$table->data = array($row3);

			$table->head[] = 'Total Sessions';
			$table->align[]='center';
			$table->size[]='';
			$table->headspan[] = 1;

			$table->head[] = 'Total Absents';
			$table->align[]='center';
			$table->size[]='';
			$table->headspan[] = 1;


			$table->head[] = 'Cummulative Absentees(%)';
			$table->align[] = 'center';
			$table->size[] = '';
			$table->headspan[] = 1;
			$row_ite=0;
			$row=2;
			for(; $row_ite<count($students["userid"]); $row_ite++){
				// Check for not joined students in SEECS - Added by Qurrat-ul-ain Babar (21st Oct, 2014)
				if($students["school"][$row_ite] == 'School of Electrical Engineering and Computer Science (SEECS)') {
					if($students["reg"][$row_ite] != 'not joined') {
						$table->data[$row][] = $row_ite+1;
						$table->data[$row][] = $students["idnumber"][$row_ite];
						$table->data[$row][] = $students["name"][$row_ite];
						$all_sessions_missed=0;
						$all_sessions=0;
						for($j=0; $j<count($cselected['id']); $j++){
							$course = $DB->get_record('course', array('id'=> $cselected['id'][$j]));
							$course->attendance_margin = $cselected['margin'][$j];
							if(!key_exists($students["userid"][$row_ite], (array)$cselected["students"][$j])){
								$table->data[$row][] = '---';
								$table->data[$row][] = '---';
								$table->data[$row][]='---';
								$table->data[$row][]='---';
								continue;
							}
							$attendance = array();
							$attendance = get_percent_absent($students["userid"][$row_ite], $course, '');
							$presentcount = 0;
							$absentcount = 0;
							$totalcount = 0;
							$presentpercent = 0;
							$absentpercent = 0;
							//get_percent_absent($students->id, $course, '');
							
							foreach($attendance as $key => $value)
							{
								if ($key == "present") {
									//$presentcount = $value+$fromform->{'session'.$course->id};
									$presentcount = $value+$_POST['session'.$course->id];
									
								}
								if ($key == "absent") {
									$absentcount = $value;
								}
								if ($key == "total") {
									//$totalcount = $value+$fromform->{'session'.$course->id};
									$totalcount = $value+$_POST['session'.$course->id];
								}					
							}
							
							if($presentcount)
								$presentpercent = ($presentcount / $totalcount) * 100;
							else {
								$presentcount = 0;
								$presentpercent = ($presentcount / $totalcount) * 100;
							}
							
							if($absentcount)
								$absentpercent = ($absentcount / $totalcount) * 100;
							else {
								$absentcount = 0;
								$absentpercent = ($absentcount / $totalcount) * 100;
							}
							
							$course_sess_att = $presentcount;
							$course_sessions = $totalcount;
							$all_sessions+=$course_sessions;
							$course_sess_missed = $absentcount;
							$all_sessions_missed+=$course_sess_missed;
							
							// $course_sess_att = get_grade($students["userid"][$row_ite],$course);
							// $course_sessions = get_maxgrade($students["userid"][$row_ite],$course);
							// $all_sessions+=$course_sessions;
							// $course_sess_missed = $course_sessions - $course_sess_att;
							// $all_sessions_missed+=$course_sess_missed;
							if($presentpercent > 85.00){
								if(!$export){

									$table->data[$row][] = '<div style="background: green;">'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%</div>';
									$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
									$table->data[$row][]=$course_sessions;
									$table->data[$row][]=$course_sess_missed;
								}
								else{
									$table->data[$row][] = '!!g'.sprintf("%0.{$decimalpoints}f", $presentpercent)."%";
									$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
									$table->data[$row][]=$course_sessions;
									$table->data[$row][]=$course_sess_missed;
								}
							}
							else{
								if($presentpercent >= 75.00 && $presentpercent <= 84.99){
									if(!$export){

										$table->data[$row][] = '<div style="background: orange;">'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%</div>';
										$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
										$table->data[$row][]=$course_sessions;
										$table->data[$row][]=$course_sess_missed;
									}
									else {
										$table->data[$row][] = '!!o'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%';
										$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
										$table->data[$row][]=$course_sessions;
										$table->data[$row][]=$course_sess_missed;
									}
								}
								if($presentpercent < 75.00) {
									if(!$export) {

										$table->data[$row][] = '<div style="background: red;">'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%</div>';
										$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
										$table->data[$row][]=$course_sessions;
										$table->data[$row][]=$course_sess_missed;
									}
									else {
										$table->data[$row][] = '!!r'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%';
										$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
										$table->data[$row][]=$course_sessions;
										$table->data[$row][]=$course_sess_missed;
									}
								}
								/*$table->data[$row][] = (100-$attendance).'%';
								 $table->data[$row][] = $attendance.'%';
								 $table->data[$row][]=$course_sessions;
								 $table->data[$row][]=$course_sess_missed;*/
							}
						}
						$table->data[$row][]=$all_sessions;
						$table->data[$row][]=$all_sessions_missed;
						$cum_abs = round(($all_sessions_missed/$all_sessions)*100,2);
						if($cum_abs > 25.00){
							if(!$export){
								$table->data[$row][] = '<div style="background: #red;">'.$cum_abs.'%</div>';
							}
							else{
								$table->data[$row][] = '!!r'.$cum_abs.'%';
							}
						}
						else{
							$table->data[$row][] = $cum_abs.'%';
						}
						$row++;
					} // end if not joined 
				} // end if seecs
				else {
					$table->data[$row][] = $row_ite+1;
					$table->data[$row][] = $students["idnumber"][$row_ite];
					$table->data[$row][] = $students["name"][$row_ite];
					$all_sessions_missed=0;
					$all_sessions=0;
					for($j=0; $j<count($cselected['id']); $j++){
						$course = $DB->get_record('course', array('id'=> $cselected['id'][$j]));
						$course->attendance_margin = $cselected['margin'][$j];
						if(!key_exists($students["userid"][$row_ite], (array)$cselected["students"][$j])){
							$table->data[$row][] = '---';
							$table->data[$row][] = '---';
							$table->data[$row][]='---';
							$table->data[$row][]='---';
							continue;
						}
						$attendance = array();
						$attendance = get_percent_absent($students["userid"][$row_ite], $course, '');
						$presentcount = 0;
						$absentcount = 0;
						$totalcount = 0;
						$presentpercent = 0;
						$absentpercent = 0;
						//get_percent_absent($students->id, $course, '');
						
						foreach($attendance as $key => $value)
						{
							if ($key == "present") {
								//$presentcount = $value+$fromform->{'session'.$course->id};
								$presentcount = $value+$_POST['session'.$course->id];
								
							}
							if ($key == "absent") {
								$absentcount = $value;
							}
							if ($key == "total") {
								//$totalcount = $value+$fromform->{'session'.$course->id};
								$totalcount = $value+$_POST['session'.$course->id];
							}					
						}
						
						if($presentcount)
							$presentpercent = ($presentcount / $totalcount) * 100;
						else {
							$presentcount = 0;
							$presentpercent = ($presentcount / $totalcount) * 100;
						}
						
						if($absentcount)
							$absentpercent = ($absentcount / $totalcount) * 100;
						else {
							$absentcount = 0;
							$absentpercent = ($absentcount / $totalcount) * 100;
						}
						
						$course_sess_att = $presentcount;
						$course_sessions = $totalcount;
						$all_sessions+=$course_sessions;
						$course_sess_missed = $absentcount;
						$all_sessions_missed+=$course_sess_missed;
						
						// $course_sess_att = get_grade($students["userid"][$row_ite],$course);
						// $course_sessions = get_maxgrade($students["userid"][$row_ite],$course);
						// $all_sessions+=$course_sessions;
						// $course_sess_missed = $course_sessions - $course_sess_att;
						// $all_sessions_missed+=$course_sess_missed;
						if($presentpercent > 85.00){
							if(!$export){

								$table->data[$row][] = '<div style="background: green;">'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%</div>';
								$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
								$table->data[$row][]=$course_sessions;
								$table->data[$row][]=$course_sess_missed;
							}
							else{
								$table->data[$row][] = '!!g'.sprintf("%0.{$decimalpoints}f", $presentpercent)."%";
								$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
								$table->data[$row][]=$course_sessions;
								$table->data[$row][]=$course_sess_missed;
							}
						}
						else{
							if($presentpercent >= 75.00 && $presentpercent <= 84.99){
								if(!$export){

									$table->data[$row][] = '<div style="background: orange;">'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%</div>';
									$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
									$table->data[$row][]=$course_sessions;
									$table->data[$row][]=$course_sess_missed;
								}
								else {
									$table->data[$row][] = '!!o'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%';
									$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
									$table->data[$row][]=$course_sessions;
									$table->data[$row][]=$course_sess_missed;
								}
							}
							if($presentpercent < 75.00) {
								if(!$export) {

									$table->data[$row][] = '<div style="background: red;">'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%</div>';
									$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
									$table->data[$row][]=$course_sessions;
									$table->data[$row][]=$course_sess_missed;
								}
								else {
									$table->data[$row][] = '!!r'.sprintf("%0.{$decimalpoints}f", $presentpercent).'%';
									$table->data[$row][] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
									$table->data[$row][]=$course_sessions;
									$table->data[$row][]=$course_sess_missed;
								}
							}
							/*$table->data[$row][] = (100-$attendance).'%';
							 $table->data[$row][] = $attendance.'%';
							 $table->data[$row][]=$course_sessions;
							 $table->data[$row][]=$course_sess_missed;*/
						}
					}
					$table->data[$row][]=$all_sessions;
					$table->data[$row][]=$all_sessions_missed;
					$cum_abs = round(($all_sessions_missed/$all_sessions)*100,2);
					if($cum_abs > 25.00){
						if(!$export){
							$table->data[$row][] = '<div style="background: #red;">'.$cum_abs.'%</div>';
						}
						else{
							$table->data[$row][] = '!!r'.$cum_abs.'%';
						}
					}
					else{
						$table->data[$row][] = $cum_abs.'%';
					}
					$row++;
				}
				
				
			}

		}

		if($export){

			$table->category = $category->name;
			//$table->duration = date("d M Y", $cselected["startdate"][0]).' to '.date("d M Y", time('now'));
			ExportToExcel($table);
		}
		else{

			echo '<div style="text-align: center; font-weight: bold;">ATTENDANCE SUMMARY <br>ABSENTEES RECORDS (Percentage)</div>';
			echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">';
			if($type==3){
				$start = isset($_POST['start_time'])?$_POST['start_time']:"";
				$end = isset($_POST['end_time'])?$_POST['end_time']:"";
				if($perd){
					
					echo '<div style="text-align: center; font-weight: bold;">Period : ( '.$start.' - '.$end.' )</div>';
				}
				$courseids = explode(",", $export_courses);
				
				foreach ($courses as $course) {
					foreach ($courseids as $cid) {
						if($cid == $course->id)
							$startdate= $course->startdate;
					}
					
				}

				if(!$perd){
					$time->end =$startdate;
					$enddate =strtotime("+18 weeks", $time->end);
					// echo '<div style="text-align: center; font-weight: bold;">Period : ( '.date(" M jS, Y", $startdate).' - '.date(" M jS, Y", $enddate).' )</div>';
				}

				echo '<form method="post" style="display: inline; margin: 0; padding: 0;">';
				echo 			'<input type="hidden" name="courses" value="'.rtrim($export_courses, ',').'" />';
				echo 			'<input type="hidden" name="id" value="'.$categoryid.'" />';
				echo 			'<input type="hidden" name="perod" value="'.$perd.'" />';
				echo 			'<input type="hidden" name="type" value="'.$type.'" />';
				
				echo "<b>Start Period: </b>";
				echo "<input type='date' name ='start_time' id='start_time' />";
				
				echo "&nbsp;&nbsp;<b>End Period</b>";
				echo "<input type='date' name ='end_time' id='end_time' />";
				
				echo '<input type="hidden" name="perd" value="true" /><input type="submit" name="periods" value="Select Period" />';
				echo "<br/>";
				echo '</form>';
			}
			echo			'<form method="post" style="display: inline; margin: 0; padding: 0;">';
			echo 			'<input type="hidden" name="courses" value="'.rtrim($export_courses, ',').'" />';
			echo 			'<input type="hidden" name="sessions" value="'.rtrim($export_courses_sessions, ',').'" />';
		    foreach ($courses as $course) {
				if($fromform->{'session'.$course->id}!== ''){
		   		echo 			'<input type="hidden" name="session'.$course->id.'"  value="'.$fromform->{'session'.$course->id}.'" />';
				}
		   }
		   
		    echo 			'<input type="hidden" name="percent"  value="'.$fromform->percent.'" />';
			echo 			'<input type="hidden" name="id" value="'.$categoryid.'" />';
			echo 			'<input type="hidden" name="type" value="'.$type.'" />';
			//echo 			'<input type="hidden" name="start" value="'.$start.'" />';
			//echo 			'<input type="hidden" name="end" value="'.$end.'" />';
			echo 			'<input type="hidden" name="export" value="true" /><input type="submit" value="Download Excel" />
							</form>';


			////
			if($type==3 || $type==2){
				echo	'<form method="post" style="display: inline; margin: 0; padding: 0;">';
				echo 	"<input type='hidden' name='content' value='". implode('@',$cntnts)."'>";
				echo 	'<input type="hidden" name="template" value="true" /><input type="submit" value="Download Warning Letters" /></form>';
			}


			///

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
	global $reportname;
	global $start;
	global $end;

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
	$myxls->set_row(1, 20 );//Added By Hina
	$myxls->set_row(2, 20 );//Added By Hina

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

	$green =& $workbook->add_format();
	$green->set_bold(0);            // Make it bold
	$green->set_size(12);
	$green->set_fg_color(3);
	$green->set_align('center');


	$red =& $workbook->add_format();
	$red->set_bold(0);            // Make it bold
	$red->set_size(12);
	$red->set_fg_color(10);
	$red->set_align('center');

	$orange =& $workbook->add_format();
	$orange->set_bold(0);            // Make it bold
	$orange->set_size(12);
	$orange->set_fg_color('orange');
	$orange->set_align('center');

	$normal =& $workbook->add_format();
	$normal->set_bold(0);
	$normal->set_align('center');
	$normal->set_size(10);

	//Added By Hina Yousuf//
	$normal1 =& $workbook->add_format();
	$normal1->set_bold(1);
	$normal1->set_align('center');
	$normal1->set_size(10);
	//end

	$name =& $workbook->add_format();
	$name->set_bold(0);
	$name->set_size(10);

	$grey_code_f =& $workbook->add_format();
	$grey_code_f->set_bold(0);            // Make it bold
	$grey_code_f->set_size(12);
	$grey_code_f->set_fg_color(22);
	$grey_code_f->set_align('center');

	//$formatbc->set_size(14);
	$myxls->write(1, 1, "ATTENDANCE SUMMARY",$header1);
	$myxls->write(2, 1, $reportname,$header2);
	if($start){
		$duration='Period : ( '.$start.' - '.$end.')';
		$myxls->write(4, 1, $duration, $formatbc);
	}
	//$myxls->write(4, 0, $duration);

	$myxls->set_column(3,100,15);//Added By Hina
	$i = 6;
	$j = 0;
	$a=0;

	$a=0;

	foreach ($data->head as $heading){
		$heading = str_replace('<br/>','',$heading);
		$heading = trim($heading);
		$myxls->write_string($i, $j, $heading,$header2);

		$col_size = strlen($heading);
		$col_size+=6;

		if(preg_match('/^NAME/i',$heading)){
			$col_size=25;
		}
		$myxls->set_column($j,$j,$col_size);
		//added By Hina Yousuf
		if($data->headspan[$a]==4){
			$myxls->merge_cells($i,$j,$i,$j+3);
			$j+=4;
		}
		 if($data->headspan[$a]==5){
                        $myxls->merge_cells($i,$j,$i,$j+4);
                        $j+=5;
                }

		if($data->headspan[$a]==3){
			$myxls->merge_cells($i,$j,$i,$j+2);
			$j+=3;
		}
		if($data->headspan[$a]==1){
			$j++;
		}
		//$j++;
		$a++;//end
		//$j++;
	}
	//$myxls->merge_cells(1,0,1,$j-1);
	//$myxls->merge_cells(2,0,2,$j-1);
	//$myxls->merge_cells(4,1,4,3);

	$i = 7;
	$j = 0;

	///Added By Hina Yousuf


	foreach ($data->data as $row) {
		foreach ($row as $cell) {
			foreach ($cell as $cel) {
				$myxls->write_string($i, $j, strip_tags($cel->text),$normal1);
				$j++;

			}
		}


	}
	///end
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
				///

				if(preg_match('/^!!r/',$cell)){
					$cell = str_replace("!!r",'',$cell);
					$myxls->write_string($i, $j++, $cell,$red);
				}
				elseif(preg_match('/^!!g/',$cell)){
					$cell = str_replace("!!g",'',$cell);
					$myxls->write_string($i, $j++, $cell,$green);
				}

				elseif(preg_match('/^!!o/',$cell)){
					$cell = str_replace("!!o",'',$cell);
					$myxls->write_string($i, $j++, $cell,$orange);
				}

				//


				/*if(preg_match('/^!!/',$cell)){
					$cell = str_replace("!!",'',$cell);
					$myxls->write_string($i, $j++, $cell,$grey_code_f);
					}*/
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
	$myxls->write_string($i+3, 4, "Note: This is a computer generated report for which signatures are not required.",$normal1);
	$workbook->close();
	exit;
}


function ExportToPDF($content){
	//echo "pdf";
	global $CFG;

	//$pdf=new PDF();
	//Column titles
	//$header=$data->tabhead;
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('');
	$pdf->SetTitle('Attendance Report');
	$pdf->SetSubject('Attendance Report');

	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);

	// set default monospaced font
	//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);


	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);
	$pdf->setCellHeightRatio(1.5);
	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('helvetica', '',10 );
	foreach($content as $cntnt){
		if($cntnt !=""){
        	// add a page
        	$pdf->AddPage('P','A4');
        	ob_clean();
        	$pdf->writeHTML($cntnt, true, false,false,false,'');
		}
	}
	// ---------------------------------------------------------

	//Close and output PDF document
	$pdf->Output("Attendance_Report", 'D');
	exit;

}
//

function ImprovedTable($name)
{

	return $name;

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
function make_categories_list2(&$list, &$parents, $requiredcapability = '',$excludeid = 0, $category = NULL, $path = "") {

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
?>