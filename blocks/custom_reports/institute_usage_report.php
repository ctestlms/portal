<?php
require_once('../../config.php');    
require_once($CFG->libdir.'/blocklib.php');
require_once($CFG->libdir.'/formslib.php');
//require_once('./view_attendance_report_form.php');
require_once('../../mod/attforblock/locallib.php');
require_once('../../mod/feedback/lib.php');
require_once('../../mod/facultyfeedback/lib.php');
require_login($course->id);
session_start();
$categoryid = optional_param('id', '-1', PARAM_INT);
$export = optional_param('export',false, PARAM_BOOL);
$context = get_context_instance(CONTEXT_COURSECAT, $categoryid);
$navlinks[] = array('name' => get_string('institute_usage_report', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if(!$export)
    print_header('Institute Usage Report', 'Institute Usage Report', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
$type = $_POST['stats'];
$selected_school= $_POST['school'] ;
$start_time = $_POST['start_time'] ;
$end_time = $_POST['end_time'] ;
if(isset($_POST['view'])){
	$_SESSION['type']=$type;
	$_SESSION['school']=$selected_school;
        $_SESSION['start_time']=$start_time;
        $_SESSION['end_time']=$end_time;
        
}
if($export){
	$type = $_SESSION['type'];
	$selected_school= $_SESSION['school'];
        $start_time = $_SESSION['start_time'];
         $end_time = $_SESSION['end_time'];
}

$id_name = explode("|", $selected_school);
$id =   $id_name[0]; // school id
$name = $id_name[1]; // school name;
//added later
$context = get_context_instance(CONTEXT_USER, $USER->id);
if( has_capability('block/custom_reports:getusagereport', $context)){
	$admin=1;
	// echo  "admin";
}
$context = get_context_instance(CONTEXT_COURSECAT, $id);
if(isset($_POST['view']) or $export){ 

echo '<div style="text-align: center; font-weight: bold;">'.strtoupper ($type).' REPORT </div>';

}else {
echo '<div style="text-align: center; font-weight: bold; "> INSTITUTE USAGE REPORT  '.'</div>';
}

echo '<div style="text-align: center; font-weight: bold;">  '.$name.'</div>';


echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">
<form method="post"  action="institute_usage_report_excel.php">';
//addedlater
///$context = get_context_instance(CONTEXT_USER, $USER->id);

if(isset($_POST['view']) && (($admin==1)|| has_capability('block/custom_reports:getusagereport', $context))){
	echo '<input type="hidden" name="export" value="true" /><input type="submit" value="Download Excel" />';
}

//end

if(isset($_POST['view']) or $export){
	if($admin==1 || has_capability('block/custom_reports:getusagereport', $context)){//added
 

 

        $type = $_POST['stats'];
        $selected_school= $_POST['school'] ;
        $start_time = $_POST['year1']."/".$_POST['month1']."/".$_POST['day1'] ;
        $end_time =$_POST['year2']."/".$_POST['month2']."/".$_POST['day2'] ;;
        
        
        if(isset($_POST['view'])){
            $_SESSION['type']=$type;
            $_SESSION['school']=$selected_school;
            $_SESSION['start_time']=$start_time;
            $_SESSION['end_time']=$end_time;
            
            
        }
        if($export){
            $type = $_SESSION['type'];
          //  $period= $_SESSION['period'];
            $selected_school= $_SESSION['school'];
            $start_time= $_SESSION['start_time'];
            $end_time= $_SESSION['end_time'];
            
        }
      
        $date = new DateTime($start_time);
$start = $date->getTimestamp();

 

$date1 = new DateTime($end_time);
$end1 = $date1->getTimestamp();
$end = $end1 + (60*24*60);

   
        
        $id_name = explode("|", $selected_school);
        $id = $id_name[0];         // school id
        $name = $id_name[1];     // school name
       
        if($id!="all"){
        	$school_ids=$DB->get_records_sql("SELECT id FROM {course} WHERE category IN ( SELECT id FROM {course_categories} WHERE path LIKE '/$id%')");
        }
        foreach($school_ids as $school_id){
            $ids.= $school_id->id.", ";
        }
        $ids = rtrim($ids, ", ");
        if($type=="Activities"){
            $table = new html_table();
            $table->head = array();
            $table->head[] =  'Start Date - End Date';
            $table->align[] = 'center';
            $table->width[] = '100%';
            $modules = array('assignment','assign', 'forum', 'quiz', 'resource', 'attforblock', 'turnitintool','feedback','facultyfeedback','quickfeedback','course_ev');
            $modules1 = array('assignment2.2','assignment2.4', 'forum', 'quiz', 'resource', 'attendance', 'turnitin tool','faculty feedback','Faculty Course Overview Report','quickfeedback','Student Course Evaluation Report');
            
            foreach($modules1 as $module){
            $table->head[] = strtoupper($module);
            $table->align[] = 'center';
            $table->size[] = '';
            }
            $i=0;
      
         //    echo $start_time .' '.$end_time.'</br>';
          //echo $start. ' '.$end;
            $table->data[0][] ="<b>".$start_time." - ". $end_time."</b>" ;
            foreach ($modules as $module){  
            	if($id!="all"){
            		$course="AND  a.course IN ($ids)";
            		$course1="AND  courseid IN ($ids)";
					$course2="AND  course  IN ($ids)";
            	}
            	else{
            		$course="";
            		$course1="";
					$course2="";
            	}           
		  $count=0;
    
				   if($module == 'attforblock' ){
					
					   
				   $attendence_ids = $DB->get_records_sql("select id from {attforblock} WHERE  1=1  $course2  ") ;
					 
					//print_r($attendence_ids);
					 
						 
			 		  foreach($attendence_ids as $idss){
 					       	$temp_ids .= $idss->id.",";
				 	} 
						 
						  $temp_ids= trim($temp_ids, ",") ;
 
					 	 if($temp_ids)
							{$attendenceIds = "AND  attendanceid IN ($temp_ids)";   }
						 else 
						 {
						 	$attendenceIds=""; 
						 } 
 
                      
	    if($sessions = $DB->get_records_sql("select id from mdl_attendance_sessions where timemodified between $start and $end $attendenceIds and lasttaken IS NOT NULL"))
               { 
                                        foreach($sessions AS $s)
                                        {
	                                     //if($DB->record_exists_select('attendance_log',"sessionid = $s->id"))
											{
                                               $count++;  
                                            }
                                          //  $table->data[0][] = $sessions->total;
                                        }
                                         $table->data[0][] =  $count;
                                    }
			            else
			            	$table->data[0][] = "0";
                                    
                           }
                      
                                      
			     elseif ($module == 'feedback') {
                            $feedbacks = $DB->get_records_sql("SELECT id,name from {feedback} a WHERE  a.timeopen between $start and $end $course and name != 'Student Course Evaluation'");
                            foreach ($feedbacks as $feedback) {
                                $completedscount = feedback_get_completeds_group_count($feedback, $mygroupid);
                                if ($completedscount != 0) {
                          //    echo $feedback->name."</br>";
                                    $count++;
                                }
                            }
                            


                            $table->data[0][] =  $count;
                        }
                        //check
                            elseif ($module == 'facultyfeedback') {
                            $feedbacks = $DB->get_records_sql("SELECT id,name from {facultyfeedback} a
	            WHERE  a.timeopen between $start and $end $course");
                            foreach ($feedbacks as $feedback) {
                                $completedscount = facultyfeedback_get_completeds_group_count($feedback, $mygroupid);
                                if ($completedscount != 0) {
                         //     echo $feedback->name;
                                    $count++;
                                }
                            }
                            


                            $table->data[0][] =  $count;
                        }
                         elseif ($module == 'course_ev') {
                            $feedbacks = $DB->get_records_sql("SELECT id,name from {feedback} a
	            WHERE  a.timeopen between $start and $end $course and name = 'Student Course Evaluation'");
                          foreach ($feedbacks as $feedback) {
                              $completedscount = feedback_get_completeds_group_count($feedback, $mygroupid);
                               if ($completedscount != 0) {
                           //   echo $feedback->name;
                                    $count++;
                                }
                            }
                            


                            $table->data[0][] =  $count;
                        }
                        
                       else if($resource = $DB->get_record_sql("SELECT count(a.id) as total, b.name FROM {course_modules} a JOIN {modules} b ON b.id = a.module 
	            WHERE a.added between $start and $end $course and b.name = '$module' group by b.name order by b.id"))
                        {  
                               $table->data[0][] = $resource->total;
                           
                           }
                           
                    
                    
	            else
	            $table->data[0][] = "0";
                    
            }
     
        }
        
        else if($type=="usrSummary"){
            $modules = array('Period', 'Manager', 'Teacher', 'Non-editing Teacher','Student','Authenticated User','Teaching Assistant');
        	$usertypes=array(1,3,4,5,7,10);
      
            $table = new html_table();
            $table->head = array();
        
            $table->head[] = 'Period';
            $table->align[] = 'center';
            $table->size[] = '';
             
            $table->head[] = 'Manager';
            $table->align[] = 'center';
            $table->size[] = '';
            
            $table->head[] = 'Teacher';
            $table->align[] = 'center';
            $table->size[] = '';
            
            $table->head[] = 'Non-editing teacher';
            $table->align[] = 'center';
            $table->size[] = '';
            
            $table->head[] = 'Student';
            $table->align[] = 'center';
            $table->size[] = '';
            
            $table->head[] = 'Authenticated user';
            $table->align[] = 'center';
            $table->size[] = '';
            
            $table->head[] = 'Teaching Assistant';
            $table->align[] = 'center';
            $table->size[] = '';
            $i=0;
            
          //  do{
             /*   if($period=="all"){
                    
                    $time->start =$time->end ;
                    $time->end = time();
                }
                else{
                    $time->start =$time->end ;
                    $time->end = strtotime("+$gap days", $time->start);
                }
              * 
              */

            //    $starttime= date(" M jS, Y", $time->start);
              //  $endtime=   date(" M jS, Y", $time->end);   
                $table->data[$i][] ="<b>Period ".$start_time." - ". $end_time."</b>" ;
                foreach ($usertypes as $usertype){ 
                	if($id!="all"){
            			$course="AND c.instanceid IN ($ids)";
            		
	            	}
	            	else{
	            		$course="";
	            		
	            	}   
	                $sql="SELECT username
                          FROM mdl_user u JOIN mdl_role_assignments ra ON ra.userid = u.id
                          JOIN mdl_role r ON ra.roleid = r.id
                          JOIN mdl_context c ON ra.contextid = c.id
                          WHERE r.id  =$usertype 
                          AND u.timemodified between  $start and $end
                          AND c.contextlevel =50
                          $course
                          GROUP BY username";
	               	
	                
	                $users = $DB->get_records_sql($sql);
	                $usercount=sizeof($users);
	                $table->data[$i][] = $usercount;
                }
                
               
             //   $i++;
            /*   
                if($period=="all"){
                    $time->end =  strtotime("+1 days", $time->end );
                }
               
                
                 
            }while ($time->end <= time());       
   }
             * 
             */
        }
        else{
            $modules = array('FULL NAME', 'Account Type', 'Time Created', 'Last Access');
        
            if($type=="users"){
                $usertype="1,2,3,4,5,6,7,9,10,15";
            }
            
            if($type=="students"){
                $usertype="5";
            }
            
            if($type=="faculty"){
                $usertype="3,4,10";
            }
        
            $table = new html_table();
            $table->head = array();
        
            $table->head[] = 'Full Name';
            $table->align[] = 'center';
            $table->size[] = '';
            
            $table->head[] = 'Account Type';
            $table->align[] = 'center';
            $table->size[] = '';
            
            $table->head[] = 'Time Created';
            $table->align[] = 'center';
            $table->size[] = '';
            
            $table->head[] = 'Last Access';
            $table->align[] = 'center';
            $table->size[] = '';
            $i=0;
            
        
                $i++;
                $table->data[$i][]="";
                $table->data[$i][] ="<b>Period ".$start_time." - ". $end_time."</b>" ;
                
                if($id!="all"){
            			$course="AND c.instanceid IN ($ids)";
            		
	            	}
	            	else{
	            		$course="";
	            		
	            	}  
                $sql="SELECT  u.id as userid,firstname, lastname , name , u.firstaccess as time , lastaccess  
                                                    FROM mdl_user u JOIN mdl_role_assignments ra ON ra.userid = u.id
                                                    JOIN mdl_role r ON ra.roleid = r.id
                                                    JOIN mdl_context c ON ra.contextid = c.id
                                                    WHERE r.id IN ($usertype) 
                                                    AND u.timemodified between  $start and $end
                                                    AND c.contextlevel =50
                                                    $course
                                                    GROUP BY username";
               
                 
                $users = $DB->get_records_sql($sql);
                $usercount=sizeof($users);
               
               
                $i++;
                
                foreach ($users as $user){ 
				
				if($user->time !=0) {
					$date=  date(" M jS, Y", $user->time) ;
					}else {
					$date="Never Logged IN";
					}
                    $table->data[$i][] = $user->firstname." ".$user->lastname;
                    $table->data[$i][] = $user->name;
                    $table->data[$i][] =  $date;
                    $table->data[$i][] = date(" M jS, Y", $user->lastaccess); 
                    $i++;
                    
                }
                
                $table->data[$i][] ="" ;
                $table->data[$i][] = "<b>Total no of users:</b>";
                $table->data[$i][] = "<b>".$usercount."</b>";
                $table->data[$i][] = "";
                $table->data[$i][] = ""; 
                $i++;
                $table->data[$i][]="";
                $table->data[$i][]="";
                $table->data[$i][]="";
                $table->data[$i][]="";
                
                //$i=$i+2;
                
                //echo $time->timestart."time". time()."<br>";
            
                /*if($period=="all"){
                    $time->end =  strtotime("+1 days", $time->end );
                }
               
                
                 
            }while ($time->end <= time()); 
                 * 
                 */       
    }
        }          
                 else{
		echo "You do not have permissions to access this report!";
	}


	
}
if(isset($_POST['view']) && (($admin==1)|| has_capability('block/custom_reports:getusagereport', $context))){


?>
  <input type="hidden" name="type" value="<?php echo $_SESSION['type']; ?>"  />
  <input type='hidden' name='data' value="<?php echo htmlentities(serialize($table)); ?>" />
    <input type="hidden" name="name" value="<?php echo $name; ?>"  />
<?php 
}
echo '</form>';
Print_form();
if($export && ($admin==1 || has_capability('block/custom_reports:getusagereport', $context)) ){

                
                $table->duration = date("d M Y", 1185926400).' to '.date("d M Y", time('now'));
                ExportToExcel($table);
            }

if(isset($_POST['view'] )&& ($admin==1 || has_capability('block/custom_reports:getusagereport', $context))){
    echo html_writer::table($table,$name,$type);
}
echo $OUTPUT->footer();

//================Export to Excel================//

function ExportToExcel($data,$name,$type) {
   global $CFG;
   global $modules;
   global $name,$type;
    //require_once("$CFG->libdir/excellib.class.php");/*
   require_once($CFG->dirroot.'/lib/excellib.class.php');
   $filename = "Institute_audit_report.xls";
   
   $workbook = new MoodleExcelWorkbook("-");
/// Sending HTTP headers
    ob_clean();
    $workbook->send($filename);
/// Creating the first worksheet
    $myxls =& $workbook->add_worksheet('Autid Report');
/// format types
    $formatbc =& $workbook->add_format();
	$formatbc1 =& $workbook->add_format();
	    $formatbc->set_bold(1);
	$myxls->set_column(0, 0, 30);
	$myxls->set_column(1, 7, 20);
	$formatbc->set_align('center') ;
	$formatbc1->set_align('center') ;
    $xlsFormats = new stdClass();
    $xlsFormats->default = $workbook->add_format(array(
                            'width'=>40));
    //$formatbc->set_size(14);
    $myxls->write(0, 2, "AUDIT REPORT", $formatbc);
    $myxls->write(1, 2, $name, $formatbc);
    
    if($type=="Activities"){
        $myxls->write_string(4, 0, "Period", $formatbc);
        $j = 1;
    }
    foreach ($modules as $module)
        $myxls->write_string(4, $j++, strtoupper($module), $formatbc);

    $i = 5;
    $j = 0;
    foreach ($data->data as $row) {
        foreach ($row as $cell) {
            //$myxls->write($i, $j++, $cell);
            if (is_numeric($cell)) {
               $myxls->write_number($i, $j++, strip_tags($cell),$formatbc1);
            } else {
                $myxls->write_string($i, $j++, strip_tags($cell),$formatbc1);
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
   
    /**
    *
    * @Create dropdown of years
    *
    * @param int $start_year
    *
    * @param int $end_year
    *
    * @param string $id The name and id of the select object
    *
    * @param int $selected
    *
    * @return string
    *
    */
    function createYears($start_year, $end_year, $id, $selected=null)
    {

        /*** the current year ***/
        $selected = is_null($selected) ? date('Y') : $selected;

        /*** range of years ***/
        $r = range($start_year, $end_year);

        /*** create the select ***/
        $select = '<select name="'.$id.'" id="'.$id.'">';
        foreach( $r as $year )
        {
            $select .= "<option value=\"$year\"";
            $select .= ($year==$selected) ? ' selected="selected"' : '';
            $select .= ">$year</option>\n";
        }
        $select .= '</select>';
        return $select;
    }

    /*
    *
    * @Create dropdown list of months
    *
    * @param string $id The name and id of the select object
    *
    * @param int $selected
    *
    * @return string
    *
    */
    function createMonths($id, $selected=null)
    {
        /*** array of months ***/
        $months = array(
                1=>'January',
                2=>'February',
                3=>'March',
                4=>'April',
                5=>'May',
                6=>'June',
                7=>'July',
                8=>'August',
                9=>'September',
                10=>'October',
                11=>'November',
                12=>'December');

        /*** current month ***/
        $selected = is_null($selected) ? date('m') : $selected;

        $select = '<select name="'.$id.'" id="'.$id.'">'."\n";
        foreach($months as $key=>$mon)
        {
            $select .= "<option value=\"$key\"";
            $select .= ($key==$selected) ? ' selected="selected"' : '';
            $select .= ">$mon</option>\n";
        }
        $select .= '</select>';
        return $select;
    }


    /**
    *
    * @Create dropdown list of days
    *
    * @param string $id The name and id of the select object
    *
    * @param int $selected
    *
    * @return string
    *
    */
    function createDays($id, $selected=null)
    {
        /*** range of days ***/
        $r = range(1, 31);

        /*** current day ***/
        $selected = is_null($selected) ? date('d') : $selected;

        $select = "<select name=\"$id\" id=\"$id\">\n";
        foreach ($r as $day)
        {
            $select .= "<option value=\"$day\"";
            $select .= ($day==$selected) ? ' selected="selected"' : '';
            $select .= ">$day</option>\n";
        }
        $select .= '</select>';
        return $select;
    }

 
  
  

function Print_form() {
    global $CFG,$DB;
    $sql="SELECT id,name FROM {course_categories} WHERE parent =0";
    $schools =  $DB->get_records_sql($sql);
    echo "<br/><br/><table   border='1'><tr><td>";
    echo "<label><b>Select School:</b></label></td><td>";
    echo "<form name='school_report' method='post' action='institute_usage_report.php'>";
    echo "<select name='school'>";
    echo '<option value="all|NUST LMS">NUST LMS </option>';
    foreach($schools as $school){
    	 
        $value= $school->id."|".$school->name;
        ?>
        <option value="<?php echo $value; ?>"><?php echo $school->name ?> </option>
        <?php
    }
    echo "</select></td>";
    ?>
    </tr><tr>
    
    
    <td>
    <label><b>Audit Type:</b></label></td>
    <td>
    <input type="radio" name="stats" value="Activities" checked> Activities Report<br>
    <input type="radio" name="stats" value="users" > Detailed Users Report<br>
     <input type="radio" name="stats" value="usrSummary" > Summarized User Report<br>
    <input type="radio" name="stats" value="students" > Students Report<br>
    <input type="radio" name="stats" value="faculty" >  Teacher (Editing / Non-Editing) Report<br>
   </td>
   </tr>
     
         <tr>
        <td><label><b>Start Date:</b></label><?php echo createYears(2008, 2020, 'year1', date('Y')); ?>

<?php echo createMonths('month1', 4); ?>

 <?php echo createDays('day1', 20); ?></td>
        
        
        <td>
		<label><b>End Date:</b></label>
		<?php echo createYears(2008, 2020, 'year2', date('Y')); ?>

<?php echo createMonths('month2', 4); ?>

<?php echo createDays('day2', 20); ?></td>
        
        
        
         
        </tr>
   <tr>
    <td><input type="submit" name="view" value="View"></td>
   </tr> 
    
    
    </table><br>
    <?php

}

