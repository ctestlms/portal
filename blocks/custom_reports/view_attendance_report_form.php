<style>
#fitem_id_percent {
	/* display:none;*/
	}
</style>
<script type="text/javascript" src="jquery-1.3.2.js"></script>
 
 <script type="application/javascript">
 
 $( document ).ready(function() {
   $('#fitem_id_percent').hide();  }
   );
 
 function validate(){
	 
	 value = $('#id_percent').val();
	 error=0;
	 //alert($('#id_type').val());
	 if($('#id_type').val()== 2 ){
	  error= 1;
	  }
	   
	  if(error == 1) {
	 
	 		if(value ==""){
				alert('Please enter percentage.');	return false;	
				}
 
    		if(value>100){
				alert('Please enter value between 0 and 100 only.'); 
				return false;
				}
 	 		if(value<0){
	  			alert('Please enter value between 0 and 100 only.');
	  			return false;
	  		}
	  
	  }
	 }
 function showPercentage(value){
	 
	  if(value==2){
		 $('#fitem_id_percent').show();
		 }else {
		 $('#fitem_id_percent').hide();
		 }
	 }
 
 
 
</script>


<?php  // $Id: add_form.php,v 1.1.2.2 2009/02/23 19:22:42 dlnsk Exp $

require_once($CFG->libdir.'/formslib.php');

class mod_custom_reports_view_attendance_report_form extends moodleform {

    function definition() {

        global $CFG;
        $mform    =& $this->_form;

        $courses    = $this->_customdata['courses'];
		$categoryid = $this->_customdata['categoryid'];	
		$report     = $this->_customdata['report'];	
		if($report=="Missing Lectures Report"){
		?>
        
        <script type="application/javascript">
 
  		function validateMissingLecture(){
			
			if($("input.logInfo:checkbox:checked").length ==1 ){
				if($("input.checkboxgroup0:checkbox:checked").length >2 ){
					alert('You can select upto 2 courses only.'); return false;
				}
			  }
		
			} 
 
 $( document ).ready(function() {
   $('#fitem_id_percent').hide();
 
 		
		 $('.checkboxgroup0').click(function () {
    		if($("input.logInfo:checkbox:checked").length ==1 ){
				 if($("input.checkboxgroup0:checkbox:checked").length ==2 ){
	 				alert('You can select upto 2 courses only.')
				 		$('input.checkboxgroup0:not(:checked)').attr('disabled', 'disabled');
						}else {
			 		$('input.checkboxgroup0').removeAttr('disabled');
		   }
		}else {
			$('input.checkboxgroup0').removeAttr('disabled');
			 }
	 	});
  }
 
   
   );</script>
   <?php 
		}
        $mform->addElement('header', 'general', get_string('select_courses','block_custom_reports'));
		$mform->addElement('hidden', 'id', $categoryid);
	    if($report=="Course Audit Report"){
			$attributes = array("class" => "newclass");
			  //Added By Hina Yousuf
                        $date=array();
                        $time->end =1272672000;
                        do{
                                $time->end = strtotime("+1 days", $time->end);
                                $date[date(" M jS, Y", $time->end)] =date(" M jS, Y", $time->end);



                        }while($time->end <= time());
                        $options =$date;

                        $periodarray=array();

                        $periodarray[] =& $mform->createElement('select', 'startperiod', "<b>Start:</b>", $options,$attributes);

                        $periodarray[] =& $mform->createElement('select', 'endperiod', "<b>End:</b>", $options,$attributes);
                        $mform->addGroup($periodarray, 'period', '<b>Select Period:</b>', array(' to '), false);

                        $mform->addElement('html', '<br />');
                        //end   
                        

/*			$options = array('ALL' => 'ALL',
						
							'Weekly' => 'Weekly',
							'Monthly' => 'Monthly',
							'Yearly' => 'Yearly'
							
							);
			
					
			$mform->addElement('select', 'period', "<b>Select Period:</b>", $options,$attributes);
			$mform->addElement('html', '<br />');
			*/
			}

			if($report=="Missing Lectures Report"){
			/* $attributes = array("class" => "newclass");
			$weeks=1;
			do{
				if($weeks==1){
				$time=strtotime("+4 days", $courses[0]->startdate);
				}
				else{
					$time=strtotime("+1 week", $time);
					
				}
				
				$realtime=date(" d-m-Y", $time);
				$noofweeks[$weeks." (Till ".$time.")"] =$weeks." (Till ".$realtime.")";
				$weeks +=1;

					
			}while($weeks <= 18);
			$options =$noofweeks;

			$mform->addElement('select', 'weeks', "<b>No of weeks:</b>", $options,$attributes); */
			$mform->addElement('date_selector', 'startdate', '<b>Start Date</b>');
			$mform->addElement('date_selector', 'enddate', '<b>End Date</b>');

		}
		
		/* if($report=="Exam Scheduler Report"){
			$mform->addElement('date_selector', 'coursestartdate', 'Course Start Date');
		} */

	   if ($report == "Students Penalty List" or $report == "Feedback Report") {
		   
		   
            $attributes = array("class" => "newclass");
            $feedbacktype["First Student Feedback"] = "First Student Feedback";
            $feedbacktype["Second Student Feedback"] = "Second Student Feedback";
            // $feedbacktype["Student Course Evaluation | Student Course Evaluation"] = "Student Course Evaluation";   
            // $feedbacktype["Faculty Course Overview Report |Faculty Course Overview Report"] = "Faculty Course Overview Report";
			
			$feedbacktype["Student Course Evaluation"] = "Student Course Evaluation";                 // updated by Qurrat-ul-ain Babar (4th June, 2014)
			$feedbacktype["Faculty Course Overview Report"] = "Faculty Course Overview Report";		  // updated by Qurrat-ul-ain Babar (4th June, 2014)
            
			$mform->addElement('select', 'feedbacktype', "<b>Select Feedback Type:</b>", $feedbacktype, $attributes);
			if($report == "Feedback Report") {
				 
				$mform->addElement('date_selector', 'startdate', '<b>Start Date :</b>');
				$mform->addElement('date_selector', 'enddate', '<b>End Date :</b>');

			}
			
        }

		
	if($report=="Missing Lectures Report"){
			$attributes = array("class" => "newclass");
			$weeks=0;
			do{
				
				
				
				$skipweeks[$weeks] =$weeks;
				$weeks +=1;

					
			}while($weeks <=5);
			

			$mform->addElement('select', 'skip', "<b>Skip weeks:</b>", $skipweeks,$attributes);



		}

  if($report=="Custom Attendance Report"){

                            $attributes = array("class" => "newclass","onchange"=>"showPercentage(this.value);");
                            $type=array("1"=>"Attendance Report",
                                                        "2"=>"Short Attendance Report",
                                                        "3"=>"Absentee Report(Period Wise)"
                                                        );

							$options =$type;
							
							$mform->addElement('select', 'type', "<b>Report Type:</b>", $options,$attributes);
							// $mform->addElement('text', 'session', 'Sessions', array("size"=>"5"));
							
							  $attribute =array('value'=>'25','size'=>10,'maxlength'=>3,"style"=>"width:220px","onblur"=>'validate();' );
							
							$mform->addElement('text', 'percent',  '<b>Enter Percentage:</b>'  ,'%', $attribute );
				  
						//	adding two rules for validating the percentage.//
						
						
						// $mform->addRule('percent', 'Please enter numbers only.', 'numeric',null, 'client');
		                // $mform->addRule('percent', 'Please add number here.', 'required', null, 'client');
			 
					 
                        }
			
				foreach($courses as $course){
                        if($report!=="Missing Lectures Report"){
							$mform->addElement('advcheckbox', 'c'.$course->id, '', $course->fullname, array('group'=>1), array('false', 'true'));
						 }else {
						$mform->addElement('advcheckbox', 'c'.$course->id, '', $course->fullname, array('group'=>0), array('false', 'true'));
						}
                        if($report!="Missing Lectures Report" and $report!= "Students Penalty List" and $report != "Feedback Report" and $report !='Semester Result' and $report !='Semester Result' and $report != "Exam Scheduler Report"){
                         $mform->addElement('text', 'session'.$course->id, 'Sessions', array("size"=>"5"));
						//  $mform->addRule('session'.$course->id, 'It should be numeric only.', 'numeric', true, 'client');
						    $mform->addRule('session'.$course->id, 'Please enter numbers only.', 'numeric',0, 'client');
						  
						//  $mform->addRule('shortname', null, 'required', null, 'client');
                        }
                }
				
				
					// This checkbox added by Aftab Aslam //
		
				if($report=="Missing Lectures Report"){
					
					//$attribute =array('class'=>"logInfo");
						$mform->addElement('checkbox', 'logInfo', '<span style="color:red"><b>Log Information:</b></span>','(By including log information, sessions marking information will be displayed.)', array('group' => 0,'class'=>"logInfo"), array(0, 1));
				}
	
        /*foreach($courses as $course){
			$mform->addElement('advcheckbox', 'c'.$course->id, '', $course->fullname, array('group'=>1), array('false', 'true'));
			$mform->addElement('text', 'session'.$course->id, 'Sessions', array("size"=>"5"));
		}*/	
		
		$group = array();
        //$SUBMIT_STRING = GET_STRING('VIEW_REPORT', 'BLOCK_CUSTOM_REPORTS');
				if($report=="Missing Lectures Report"){
        
        $group[] = &$mform->createElement('SUBMIT', 'SUBMITBUTTON', get_string('view_report', 'block_custom_reports') ,array('onClick'=>"return validate(),validateMissingLecture()") );
				}else {
					
			$group[] = &$mform->createElement('SUBMIT', 'SUBMITBUTTON', get_string('view_report', 'block_custom_reports') ,array('onClick'=>"return validate()") );		
					
					}
		
        $group[] = &$mform->createElement('RESET', 'RESETBUTTON', get_string('reset'));
       
        $mform->addGroup($group, 'group2');
         if($report!=="Missing Lectures Report"){
        		$this->add_checkbox_controller(1, "select all/none");
		 }
    }

}
?>
