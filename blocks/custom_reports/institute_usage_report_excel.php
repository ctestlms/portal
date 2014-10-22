<?php
require_once('../../config.php');   

require_once("$CFG->libdir/excellib.class.php");
$filename = "test.xls";
$type= $_POST['type'];
$data = unserialize($_POST['data']);
$name = $_POST['name'];


//creating the needed formats

//$modules = array('assignment','assign', 'forum', 'quiz', 'resource', 'attforblock', 'turnitintool','feedback','facultyfeedback','quickfeedback','course_ev');
$modules = array(	"ASSIGNMENT2.2" ,"ASSIGNMENT2.4","FORUM","QUIZ","RESOURCE","ATTENDANCE","TURNITIN TOOL","FACULTY FEEDBACK","FACULTY COURSE OVERVIEW REPORT","QUICKFEEDBACK","STUDENT COURSE EVALUATION REPORT"); 
   	
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