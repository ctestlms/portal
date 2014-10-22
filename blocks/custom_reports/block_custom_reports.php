<?php
	class block_custom_reports extends block_base{
		
		function init(){
			$this->title = get_string('custom_reports', 'block_custom_reports');
			
		}//Function init
		
		function get_content(){
			global $CFG;
			if($this->content !== NULL){
				return $this->content;
			}//if
			$this->content = new stdClass;
			$this->content->text .= '<ul class="list">';
			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/attendance.php">Attendance Report</a></li>';
			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/audit.php">Course Audit Report</a></li>';
			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/institute_usage_report.php">Institute Usage Report</a></li>';

			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/feedback_report.php">Feedback Report</a></li>';
			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/students_penalty_list.php">Students Penalty List</a></li>';
			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/missinglectures.php">Missing Lectures Report</a></li>';
			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/std_reg_report.php">Course Registration Report</a></li>';
			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/std_reg_rept.php">Students Registration Report</a></li>';
			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/semester_results.php">Semester Results</a></li>';
			//$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/summarized_results.php">Result Notification Report</a></li>';

			$this->content->text .= '<li><a href="'.$CFG->wwwroot.'/blocks/custom_reports/fee_defaulters.php">Fee Defaulters Interface</a></li>';
			$this->content->text .= '<li><a href="' . $CFG->wwwroot . '/blocks/custom_reports/std_semestereport.php">Students Semester Report</a></li>';
			$this->content->text .= '<li><a href="' . $CFG->wwwroot . '/blocks/custom_reports/submittedresults.php">Submitted Results Report</a></li>';
			$this->content->text .= '<li><a href="' . $CFG->wwwroot . '/blocks/custom_reports/exam_scheduler_report.php">Exam Scheduler Report</a></li>';

			$this->content->text .= '</ul>';
			return $this->content;
		}//function get_content
	}
?>
