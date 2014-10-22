<?php

require_once('../../config.php');
require_once($CFG->libdir.'/blocklib.php');
require_once($CFG->libdir.'/formslib.php');
require_once('./view_attendance_report_form.php');
require_once('../../mod/attforblock/locallib.php');
require_login($course->id);

//Added By Hina Yousuf
function get_lecture_sessions($course,$startdate, $enddate)
{
        
		
		global $CFG, $DB;
        $endtime =strtotime(date("Y-m-d", $endtime) . " +1 days");
		$qry ="select id from {attforblock} where course=$course->id";
		$att = $DB->get_record_sql($qry);
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='50-Mins/One Hour Lecture' and sessdate >=$startdate and sessdate<=$enddate";
        $onehoursessions=$DB->count_records_sql($sql,array());
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='90-Mins Lecture' and sessdate >=$startdate and sessdate<=$enddate";
        $ninetyminsessions=$DB->count_records_sql($sql,array());
        $ninetyminsessions=$ninetyminsessions * 1.5;
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='Two Hours Lecture' and sessdate >=$startdate and sessdate<=$enddate";
        $twohourssessions=$DB->count_records_sql($sql,array());
        $twohourssessions=$twohourssessions * 2;
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='Three Hours Lecture' and sessdate >=$startdate and sessdate<=$enddate";
        $threehourssessions=$DB->count_records_sql($sql,array());
        $threehourssessions=$threehourssessions * 3;
		$sessions = $onehoursessions + $ninetyminsessions + $twohourssessions + $threehourssessions;
        return $sessions;

}

function get_lab_sessions($course,$startdate, $enddate)
{
        global $CFG, $DB;
		$endtime =strtotime(date("Y-m-d", $endtime) . " +1 days");
		$qry ="select * from {attforblock} where course=$course->id";
		$att = $DB->get_record_sql($qry);
        $sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description IN ('One Hour Lab', 'Two Hours Lab', 'Three Hours Lab') and sessdate >=$startdate and sessdate<=$enddate";
        $sessions=$DB->count_records_sql($sql,array());
        return $sessions;

}
// end


// Added By Qurrat-ul-ain Babar
function get_amc_lab_sessions($course,$startdate, $enddate)
{
        global $CFG, $DB;
		$endtime =strtotime(date("Y-m-d", $endtime) . " +1 days");
		$qry ="select * from {attforblock} where course=$course->id";
		$att = $DB->get_record_sql($qry);
        $sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='One Hour Lab' and sessdate >=$startdate and sessdate<=$enddate";
        $onehoursessions=$DB->count_records_sql($sql,array());
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='Two Hours Lab' and sessdate >=$startdate and sessdate<=$enddate";
        $twohoursessions=$DB->count_records_sql($sql,array());
        $twohoursessions=$twohoursessions * 2;
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='Three Hours Lab' and sessdate >=$startdate and sessdate<=$enddate";
        $threehoursessions=$DB->count_records_sql($sql,array());
        $threehoursessions=$threehoursessions * 3;
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='Two Hours Ward' and sessdate >=$startdate and sessdate<=$enddate";
        $twohourward=$DB->count_records_sql($sql,array());
        $twohourward=$twohourward * 2;
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='Four Hours Ward' and sessdate >=$startdate and sessdate<=$enddate";
        $fourhourward=$DB->count_records_sql($sql,array());
        $fourhourward=$fourhourward * 4;
		$sessions = $onehoursessions + $twohoursessions + $threehoursessions + $twohourward + $fourhourward;
        return $sessions;

}

function get_studio_sessions($course,$startdate, $enddate)
{
        global $CFG, $DB;
		$endtime =strtotime(date("Y-m-d", $endtime) . " +1 days");
		$qry ="select * from {attforblock} where course=$course->id";
		$att = $DB->get_record_sql($qry);
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description IN ('One Hour Lab', 'Two Hours Lab', 'Three Hours Lab') and sessdate >=$startdate and sessdate<=$enddate";
        $labsessions=$DB->count_records_sql($sql,array());
        $sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='Two Hours Studio' and sessdate >=$startdate and sessdate<=$enddate";
        $twohoursessions=$DB->count_records_sql($sql,array());
		$sql="select count(*) from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and description='Three Hours Studio' and sessdate >=$startdate and sessdate<=$enddate";
        $threehoursessions=$DB->count_records_sql($sql,array());
        $threehoursessions=$threehoursessions * 1.5;
		$sessions = $labsessions + $twohoursessions + $threehoursessions;
        return $sessions;

}

// Added By Aftab Aslam  Date Modified 15-May-2014 //

function get_session_status($course, $status){
	
	 global $CFG, $DB;
  
	 $qry ="select id from {attforblock} where course=$course->id";
     $att = $DB->get_record_sql($qry);
	 
	 $sql="select count(*)  from {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL and  attendenceStatus = ".$status;
     $TimeSessions=$DB->count_records_sql($sql,array());
	 return $TimeSessions ;
  
}

function get_all_sessions($course,$startdate,$enddate)
{
        
		
		global $CFG, $DB;
       
		$qry ="select id from {attforblock} where course=$course->id";
		$att = $DB->get_record_sql($qry);
		//$sql="select  id,sessdate,duration  FROM {attendance_sessions} where attendanceid=$att->id and lasttaken IS NOT NULL  ";
		
	     $sql="select  id,sessdate,duration  FROM {attendance_sessions} 
		 WHERE  attendanceid=$att->id and lasttaken IS NOT NULL  AND 
		 sessdate >=$startdate and sessdate<=$enddate";
    
  
        $sessions=$DB->get_records_sql($sql,array());
         return $sessions;

}
function get_all_session_logs($course,$sessionId)
{
		 global $CFG, $DB;
	
	    $sql="SELECT time as timetaken FROM {log}  WHERE `url` LIKE '%".$sessionId."%'  AND course = ".$course->id."  AND action  = 'attendance taken' 
		ORDER BY `id`  LIMIT 1";
        $sessionsTime=$DB->get_record_sql($sql,array());
        return $sessionsTime;
	
}


//end

$categoryid = optional_param('id', '-1', PARAM_INT);
$export = optional_param('export',false, PARAM_BOOL);
$sort = optional_param('sort',false, PARAM_BOOL);
$sortby=$_POST[sortby];
$perd=$_POST[perod];
$weeks=$_POST[weeks];
$sortorder=$_POST['sortorder'];
$cats=$_POST[catg];
if($cats){
	$_SESSION['cats'] =$cats;
}
$cats=$_SESSION['cats'];

if($categoryid==2){
	foreach ($cats as $cat)
	{
		$context = get_context_instance(CONTEXT_COURSECAT, $cat);
		require_capability('block/custom_reports:getmcr', $context);
	}
}
foreach ($cats as $cat)
{
	$courses1 = get_courses($cat, '', 'c.id, c.fullname, c.startdate,c.credithours, c.idnumber, c.shortname, c.category');
	$courses = array_merge((array)$courses1,(array)$courses);
}

$report=get_string('missinglectures', 'block_custom_reports');
$navlinks[] = array('name' => get_string('missinglectures', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if(!$export)
print_header('Missing Lectures Report', 'Missing Lectures Report', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);

if(($courses && $categoryid==2) or $export or $sort){
	$mform = new mod_custom_reports_view_attendance_report_form('missinglectures.php', array('courses'=>$courses, 'categoryid'=>$categoryid,'report'=>$report));
	
	if($fromform = $mform->get_data() or $export or $sort){
		//print_r($fromform->logInfo);exit;
		
		if(isset($_POST['logInfo']) && $_POST['logInfo']==1 ) {
					$logInfo = 1;
		}
		else {
					$logInfo = 0;
		}
	 
		if($export or $sort){
			$export_courses = required_param('courses',PARAM_SEQUENCE);
			$courses = $DB->get_records_sql("select id, fullname,credithours ,startdate, idnumber, shortname, category from {course} where id IN ({$export_courses})");
			//$weeklist =$_SESSION['weeks'];
			$startdate =$_SESSION['startdate'];
			$enddate =$_SESSION['enddate'];
			//$enddate =$fromform->enddate;
			$skipweeks =$_SESSION['skipweeks'];
		}else{
			$export_courses = "";
			//$weeklist=$fromform->weeks;
			$startdate=$fromform->startdate;
			$enddate=$fromform->enddate;
			$skipweeks=$fromform->skip;
			//$_SESSION['weeks'] =$weeklist;
			$_SESSION['startdate'] =$startdate;
			$_SESSION['enddate'] =$enddate;
			$_SESSION['skipweeks'] =$skipweeks;
		}

		$table = new html_table();
		$table->head = array();
			
		$table->head[] = 'S.No';
		$table->align[] = 'center';
		$table->size[] = '';
		$table->headspan[] = 1;
			
		$table->head[] = 'Class/Sec';
		$table->align[] = 'center';
		$table->size[] = '';
		$table->headspan[] = 1;
			
		$table->head[] = 'Subject';
		$table->align[] = 'left';
		$table->size[] = '';
		$table->headspan[] = 1;

		$table->head[] = 'Department';
		$table->align[] = 'left';
		$table->size[] = '';
		$table->headspan[] = 1;
			
		$table->head[] = 'Instructor';
		$table->align[] = 'left';
		$table->size[] = '';
		$table->headspan[] = 1;

		$table->head[] = 'Credit Hours';
		$table->align[] = 'center';
		$table->size[] = '';
		$table->headspan[] = 1;



		$table->head[] = 'Sessions Required';
		$table->align[] = 'center';
		$table->size[] = '';
		$table->headspan[] = 3;


		$table->head[] = 'Sessions Marked';
		$table->align[] = 'center';
		$table->size[] = '';
		$table->headspan[] = 3;

		$table->head[] = 'Sessions Missed';
		$table->align[] = 'center';
		$table->size[] = '';
		$table->headspan[] = 3;
		
		if(isset($_POST['logInfo']) && $_POST['logInfo']==1 ) {
				
				$table->head[] = 'Marked Status';
				$table->align[] = 'center';
				$table->size[] = '';
				$table->headspan[] = 2;
		 
		}
	 
	 

		$table->head[] = 'Remarks';
		$table->align[] = 'center';
		$table->size[] = '';



		$i = 0;
		
		foreach($courses as $course){
			
			if((!$export AND $fromform->{'c'.$course->id}=='true') OR ($export OR $sort)){
				//$startdate = $course->startdate;
				$category = $course->category;
				$semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
				$path = explode("/", $semester->path);
				$school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]");
				
				$context = get_context_instance(CONTEXT_COURSE, $course->id);
				$role = $DB->get_record('role', array('shortname'=>'editingteacher'));
				$teachers = get_role_users($role->id, $context);//('teacher', $context);
				$course_teacher = "";
				$department="";
				foreach($teachers as $teacher){
					if(!$export){
						$department.= $teacher->user_subgroup."<br>";
						$course_teacher .= "<a href='{$CFG->wwwroot}/user/view.php?id={$teacher->id}' target='_blank'>".$teacher->firstname." ".$teacher->lastname."</a><br>";
						$enddate=$fromform->enddate;
					}
					else{
					if($teacher->user_subgroup){
						$department.= $teacher->user_subgroup.", ";
					}
						$course_teacher .= $teacher->firstname." ".$teacher->lastname.", ";
						
						
						$enddate=$_SESSION['enddate'];
					}
				}
				
				$course_teacher = trim($course_teacher, ", ");
				//$weeklists= explode("(Till ", $weeklist);
				
				// echo $startdate."----".$enddate."<br/>";
				// $sdate = userdate($startdate, '%H:%M %d.%m.%Y');
				
				$duration = '86340';
				$enddate = $enddate+$duration;
				
				// $edate = userdate($enddate, '%H:%M %d.%m.%Y');
				// echo $sdate."----".$edate."<br/>";
				
				$dateDiff = $enddate - $startdate;
				$fullWeeks = round(($dateDiff/(60*60*24))/7);
				// echo "Differernce is $fullWeeks weeks<br/>";
				$weeks=  $fullWeeks-$skipweeks;
				// $endtime=rtrim($weeklists[1],")");
				$coursefullname = explode("(", $course->fullname);
				$data[$i]["sno"] = $i+1;
				//$data[$i]["period"] ="(".date("d-m-Y",$course->startdate).")-(". date("d-m-Y",$endtime).")";
				$data[$i]["class"] = rtrim($coursefullname[1],")");
				$data[$i]["coursefullname"] = (!$export)?'<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" target="_blank">'.$coursefullname[0].'</a>':$coursefullname[0];
				$data[$i]["dept"] =$department;
				$data[$i]["teacher"] = rtrim($course_teacher, "<br>");
				// echo $startdate."----".$enddate."<br/>";
				// $sdate = userdate($startdate, '%H:%M %d.%m.%Y');
				
				// $duration = '86340';
				// $enddate = $enddate+$duration;
				
				// $edate = userdate($enddate, '%H:%M %d.%m.%Y');
				// echo $sdate."----".$edate."<br/>";
				 
				 
				// New Requirement to check the Session Attendance is taken on time -- Added By Aftab Aslam 16 May 2014//
				
				if(isset($_POST['logInfo']) && $_POST['logInfo']==1 )  
				{
				 
				$sessions = get_all_sessions($course,$startdate, $enddate);
			 
				 
				$inClass=array(); $notInclass =array();
				
				 
				foreach($sessions as $sessionData){
					
					
					$sessionStartime = $sessionData->sessdate;
					$sessionEndtime = $sessionData->sessdate+$sessionData->duration;
					
					 
					//$logs = get_all_session_logs($course,$sessionData->id);
					$sql="SELECT time as timetaken FROM {log}  WHERE `url` LIKE '%".$sessionData->id."%'  AND course = ".$course->id."  AND action  = 'attendance taken' 
					ORDER BY `id`  LIMIT 1";
					$sessionsTime=$DB->get_record_sql($sql,array());
					 		
					if(  ($sessionStartime <= $sessionsTime->timetaken) && ($sessionsTime->timetaken <= $sessionEndtime)   ){
						  
	 						$inClass[]  = array(check=>'1');
					} 
						
					 else {
						  $notInclass[]  = array(check=>'0');
						 }
						  
					
					}
				}
				
			 
				//////////////////////// End ///////////////////////////////
				
				$markedlecsessions  =get_lecture_sessions($course,$startdate, $enddate);
				
				
				//$markedlecsessions  =0;
				// New Functions for in /out class percentages. //
				
			/*	$inTimeSessions  = get_session_status($course, 1);
			    $inTimePresentage =  ($inTimeSessions/$markedlecsessions)* 100 ;
				
				$ouTimeSessions  = get_session_status($course, 0);
			    $ouTimePresentage =  ($ouTimeSessions/$markedlecsessions)* 100 ;*/
				
				
				
				
			
				if($school->name == "Army Medical College (AMC)")
					$markedlabsessions=get_amc_lab_sessions($course,$startdate, $enddate);
				else if($school->name == "School of Art, Design and Architecture (SADA)")
					$markedlabsessions=get_studio_sessions($course,$startdate, $enddate);
				else
					$markedlabsessions=get_lab_sessions($course,$startdate, $enddate);
				$totalmarkedsessions=$markedlecsessions+$markedlabsessions;
				$credithours = explode("+", $course->credithours);
				$lechours = $credithours[0];
				$labhours = $credithours[1];
				$reqlecsession=$lechours*$weeks;
				$reqlabsession=$labhours*$weeks;
				$totalreqsessions=$reqlecsession+$reqlabsession;
				$missedlecsessions=$markedlecsessions-$reqlecsession;
				$missedlabsessions=$markedlabsessions-$reqlabsession;
				$totalmissedsessions=$missedlecsessions+$missedlabsessions;

				$data[$i]["crdthours"] =$course->credithours;
				//$data[$i]["weeks"] =$weeks;
				$data[$i]["reqlec"] =$reqlecsession;
				$data[$i]["reqlab"] =$reqlabsession;
				$data[$i]["totalreq"] =$totalreqsessions;
				$data[$i]["markedlec"] =$markedlecsessions;
				$data[$i]["markedlab"] =$markedlabsessions;
				$data[$i]["totalmarked"] =$totalmarkedsessions;
				$data[$i]["missedlec"] =$missedlecsessions;
				$data[$i]["missedlab"] =$missedlabsessions;
				$data[$i]["totalmissed"] = $totalmissedsessions;
				if(isset($_POST['logInfo']) && $_POST['logInfo']==1 ) {
						$data[$i]["percentageInClass"] =count($inClass);
						$data[$i]["percentageOutClass"] =count($notInclass);
				}
				/*if($totalmissedsessions==0){
					if(!$export){
						$data[$i]["totalmissed"] = '<div style="background: green;">'.$totalmissedsessions.'</div>';
					}
					else{
						$data[$i]["totalmissed"] = '!!g'.$totalmissedsessions;
					}
				}
				elseif($totalmissedsessions <=-3){
					if(!$export){
						$data[$i]["totalmissed"] = '<div style="background: red;">'.$totalmissedsessions.'</div>';
					}
					else{
						$data[$i]["totalmissed"] = '!!r'.$totalmissedsessions;
					}
				}
				elseif($totalmissedsessions <0 && $totalmissedsessions >-3){
					if(!$export){
						$data[$i]["totalmissed"] = '<div style="background: orange;">'.$totalmissedsessions.'</div>';
					}
					else{
						$data[$i]["totalmissed"] = '!!o'.$totalmissedsessions;
					}
				}
				else{
					$data[$i]["totalmissed"] = $totalmissedsessions;
				}*/
				
				if(!$export){

					$data[$i]["remarks"]= '<input type="text" name="remarks'.$course->id.'" size="" value="">';

				}
				elseif($export)
				{
					if ($form = data_submitted()) {
						$formarr = (array)$form;
						$data[$i]["remarks"] = array_key_exists('remarks'.$course->id, $formarr) ? $formarr['remarks'.$course->id] : '';
					}
				}
				$i++;
				
				
			}
			if($fromform->{'c'.$course->id}=='true' and !$export)
				$export_courses .= $course->id.",";
		}

		$data = array_orderby($data,  $sortby, constant($sortorder));
		
		for($i=0;$i<sizeof($data);$i++){
                if($data[$i]["totalmissed"]==0){
                                        if(!$export){
                                                $data[$i]["totalmissed"] = '<div style="background: white;">'.$data[$i]["totalmissed"].'</div>';
                                        }
                                        else{
                                                $data[$i]["totalmissed"] = '!!g'.$data[$i]["totalmissed"];
                                        }
                                }
                                elseif($data[$i]["totalmissed"] <=-3){
                                        if(!$export){
                                                $data[$i]["totalmissed"] = '<div style="background: red;">'.$data[$i]["totalmissed"].'</div>';
                                        }
                                        else{
                                                $data[$i]["totalmissed"] = '!!r'.$data[$i]["totalmissed"];
                                        }
                                }
                                elseif($data[$i]["totalmissed"] <0 && $data[$i]["totalmissed"] >-3){
                                        if(!$export){
                                                $data[$i]["totalmissed"] = '<div style="background: orange;">'.$data[$i]["totalmissed"].'</div>';
                                        }
                                        else{
                                                $data[$i]["totalmissed"] = '!!o'.$data[$i]["totalmissed"];
                                        }
                                }
                                else{
                                        $data[$i]["totalmissed"] =$data[$i]["totalmissed"];
                                }

                }

		$i = 0;
		$datas[$i]["sno"] = "";
		//$datas[$i]["period"] = "";
		$datas[$i]["class"] = "";
		$datas[$i]["coursefullname"] = "";
		$datas[$i]["dept"] = "";
		$datas[$i]["teacher"] = "";
		$datas[$i]["crdthours"] = "";
		//$datas[$i]["weeks"] = "";
		$datas[$i]["reqlec"] = "<b>Lecture</b>";
		$datas[$i]["reqlab"] = "<b>Labs/Studio/Wards</b>";
		$datas[$i]["totalreq"] = "<b>Total</b>";
		$datas[$i]["markedlec"] = "<b>Lecture</b>";
		$datas[$i]["markedlab"] = "<b>Labs/Studio/Wards</b>";
		$datas[$i]["totalmarked"] = "<b>Total</b>";
		$datas[$i]["missedlec"] = "<b>Lecture</b>";
		$datas[$i]["missedlab"] = "<b>Labs/Studio/Wards</b>";
		$datas[$i]["totalmissed"] = "<b>Total</b>";
		
		if(isset($_POST['logInfo']) && $_POST['logInfo']==1 ) {
				$datas[$i]["inClassSessions"] = "<b>In Class</b>";
				$datas[$i]["outClassSessions"] = "<b>Out of Class</b>";
		}
		$datas[$i]["remarks"] = "";
		if($sort or $export){
			$i = 0;
			foreach($courses as $course){

				$data[$i]["sno"] = $i+1;
				$i++;

			}
		}
		$data=array_merge($datas,$data);
		$table->data=$data;
			
		if($export){
			ExportToExcel($table,$perd,$weeks);
		}
		else{

			echo '<div style="text-align: center; font-weight: bold;">Missing Lectures Report<br></div>';
			echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">';
				
			if(!$sort)
			$perd=date("d-m-Y",$startdate)." ) - ( ". date("d-m-Y",$enddate);

			echo '<div style="text-align: center; font-weight: bold;">Period : ( '.$perd.' )</div>';
			echo '<div style="text-align: center; font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Weeks: '.$weeks.'</b></div>';

			echo '<form method="post" style="display: inline; margin: 0; padding: 0;">';
			echo 			'<input type="hidden" name="courses" value="'.rtrim($export_courses, ',').'" />';
			echo 			'<input type="hidden" name="id" value="'.$categoryid.'" />';
			echo 			'<input type="hidden" name="perod" value="'.$perd.'" />';
			echo 			'<input type="hidden" name="weeks" value="'.$weeks.'" />';
			echo "<b>Sort By:</b>";
			echo "<select name='sortby' id='sortby'>";
			echo "<option value='dept'>Department</option>";
			echo "<option value='class'>Class</option>";
			echo "<option value='totalmissed'>Total Missed Sessions</option>";
			echo "</select>";
			echo "&nbsp;&nbsp;<b>Sort Order</b>";
			echo "<select name='sortorder' id='sortorder'>";
			echo "<option value='SORT_ASC'>Ascending</option>";
			echo "<option value='SORT_DESC'>Descending</option>";
			echo "</select>";
			echo 			'<input type="hidden" name="sort" value="true" /><input type="submit" name="sort" value="Sort" />';
			echo "<br/>";
			echo			'</form>';
			echo '<form method="post" style="display: inline; margin: 0; padding: 0;">';
			echo 			'<input type="hidden" name="courses" value="'.rtrim($export_courses, ',').'" />';
			echo 			'<input type="hidden" name="id" value="'.$categoryid.'" />';
			echo 			'<input type="hidden" name="sortby" value="'.$sortby.'" />';
			echo 			'<input type="hidden" name="perod" value="'.$perd.'" />';
			echo 			'<input type="hidden" name="sortorder" value="'.$sortorder.'" />';
			echo 			'<input type="hidden" name="logInfo" value="'.$logInfo.'" />';
			
			echo 			'<input type="hidden" name="export" value="true" /><input type="submit" value="Download Excel" />';
			echo html_writer::table($table);
			echo			'</form></div>';

		}
		exit();
	}else
	$mform->display();
}else{
	$OUTPUT->box_start('generalbox categorybox');
	echo '<form method="post" action="missinglectures.php?id=2" style="display: inline; margin: 0; padding: 0;">';
	print_whole_category_list2(NULL, NULL, NULL, -1, false);
	echo '<input type="submit" value="Select Courses" />';
	echo '</form>';
	$OUTPUT->box_end();
}
echo $OUTPUT->footer();

//================Export to Excel================//
function ExportToExcel($data ,$perd,$weeks) {
	global $CFG,$USER;
	global $modules;
	//require_once("$CFG->libdir/excellib.class.php");/*
	require_once($CFG->dirroot.'/lib/excellib.class.php');
	$filename = "Missing Lectures Report.xls";

	$workbook = new MoodleExcelWorkbook("-");
	
	/// Sending HTTP headers
	ob_clean();
	$workbook->send($filename);
	/// Creating the first worksheet
	$myxls =& $workbook->add_worksheet('MCR');
	
	/// format types
	$green =& $workbook->add_format();
	$green->set_bold(0);            // Make it bold
	$green->set_size(12);
	$green->set_fg_color("white");
	$green->set_align('center');
 

	$red =& $workbook->add_format();
	$red->set_bold(0);            // Make it bold
	$red->set_size(12);
	$red->set_fg_color(10);
	$red->set_align('center');

	$orange =& $workbook->add_format();
	$orange->set_bold(0);            // Make it bold
	$orange->set_size(12);
	$orange->set_fg_color("orange");
	$orange->set_align('center');

	$normal =& $workbook->add_format();
	$normal->set_bold(0);            // Make it bold
	$normal->set_size(10);
	$normal->set_align('center');

	$formatbc =& $workbook->add_format();
	$formatbc->set_bold(1);
	$formatbc->set_align('center');

	//$formatbc->set_size(14);
	$myxls->write(0, 3, "Missing Lectures Record", $formatbc);
	$myxls->write(2, 2, "Period : ( ".$perd." ) ", $formatbc);
	$myxls->write(3, 2, "Weeks: ".$weeks, $formatbc);
	$myxls->write(4, 2, "The Remarks are entered by ".$USER->firstname." ".$USER->lastname, $formatbc);

  
 

	$i = 6;
	$j = 0;
	$a=0;
	foreach ($data->head as $heading){

		$myxls->write_string($i, $j, $heading,$formatbc);
		if($data->headspan[$a]==3){
			$myxls->merge_cells($i,$j,$i,$j+2);
			$j+=3;
		}
		if($data->headspan[$a]==1){
			$j++;
		}
		$a++;

	}
	$myxls->set_column(0,0,6,$formatbc);
	$myxls->set_column(1,1,22,$formatbc);
	$myxls->set_column(2,2,12,$formatbc);
	$myxls->set_column(3,3,30,$formatbc);
	$myxls->set_column(4,4,15,$formatbc);
	$myxls->set_column(5,5,25,$formatbc);
	$myxls->set_column(6,6,12,$formatbc);
	$i = 7;
	$j = 0;
	foreach ($data->data as $row) {
		foreach ($row as $cell) {
			//$myxls->write($i, $j++, $cell);
			if (is_numeric($cell)) {
				$myxls->write_number($i, $j++, $cell,$normal);
			} else {
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
				else{
					if(strstr($cell,"<b>")==true){
						$myxls->write_string($i, $j++, strip_tags($cell),$formatbc);
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
		if(count($courses)>0){
			$cat= '<input name="catg[]" type="checkbox" value="'.$category->id.'"/>'.$category->name;
			$cat .= html_writer::tag('span', ' ('.count($courses).')', array('title'=>get_string('numberofcourses'), 'class'=>'numberofcourse'));

		}
		else{
			$cat =$category->name; //html_writer::link(new moodle_url('', array('id'=>$category->id)), format_string($category->name), $catlinkcss);
			$cat .= html_writer::tag('span', ' ('.count($courses).')', array('title'=>get_string('numberofcourses'), 'class'=>'numberofcourse'));
		}
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

//sort report
function array_orderby()
{
	$args = func_get_args();
	$data = array_shift($args);
	foreach ($args as $n => $field) {
		if (is_string($field)) {
			$tmp = array();
			foreach ($data as $key => $row)
			$tmp[$key] = $row[$field];
			$args[$n] = $tmp;
		}
	}
	$args[] = &$data;
	call_user_func_array('array_multisort', $args);
	return array_pop($args);
}
?>
