<?php

require_once('../../config.php');
require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/formslib.php');
require('../../mod/attforblock/tcpdf/config/lang/eng.php');
require('../../mod/attforblock/tcpdf/tcpdf.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_login();
global  $PAGE,$OUTPUT,$DB;
$status       = optional_param('status', 'not joined', PARAM_RAW); // optional roleid, 0 means all enrolled users (or all on the frontpage)
$cohortid     = optional_param('cohortid', 1, PARAM_INT);  // this are required
$schoolid     = required_param('schoolid',PARAM_INT);
$export = optional_param('export', false, PARAM_BOOL);
$baseurl = new moodle_url($CFG->wwwroot.'/blocks/student_resource_center/student_joining_report.php', array(
    //'status' => $status,
   // 'cohortid' => $cohortid,
     'schoolid' => $schoolid ));
$PAGE->set_url($baseurl);
$context = get_context_instance(CONTEXT_COURSECAT, $schoolid);
require_capability('block/student_resource_center:addinstance', $context);
  
$PAGE->set_context($context);
$PAGE->set_title("Joining Report");
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading("Joining Report");
$PAGE->navbar->add('Student Joining Report',new moodle_url('/blocks/student_resource_center/student_joining_report.php?schoolid='.$schoolid));

if(!$export)
  echo $OUTPUT->header();
echo $OUTPUT->box_start();
 
/// Print settings and things in a table across the top
    $controlstable = new html_table();
    $controlstable->tablealign = 'center';
    $controlstable->align = 'center';
    $controlstable->attributes['class'] = 'controls';
    $controlstable->cellspacing = 0;
    $controlstable->data[] = new html_table_row();

 $sq = "SELECT coho.id,coho.name FROM `mdl_cohort` AS coho
        JOIN `mdl_context` AS con 
        ON con.id = coho.contextid
        JOIN `mdl_course_categories` AS cou
        ON con.instanceid = cou.id
        WHERE  cou.id = $schoolid";
 
 $cohorts = $DB->get_records_sql($sq);

  //  $popupurl2 = new moodle_url('/blocks/student_resource_center/student_new_reg_1.php?sifirst=&silast=');
        $selecting = array();
        foreach($cohorts AS $cohort)
        {
     $selecting[$cohort->id] = $cohort->name; 
         }
 
 
    $selectcohort = new single_select($baseurl, 'cohortid', $selecting, $cohortid, array(''=>'choosedots'), 'cohortid');
    $selectcohort->set_label("<b>Classes</b>");
    $cohortlistcell = new html_table_cell();
    $cohortlistcell->text = $OUTPUT->render($selectcohort);
    $controlstable->data[0]->cells[] = $cohortlistcell;
 
    //_______________________________________________________________________________________________________________________________
//__________________________________________________________________________________________________________________________________
$baseurl1 = new moodle_url($CFG->wwwroot.'/blocks/student_resource_center/student_joining_report.php', array(
    //'status' => $status,
    'cohortid' => $cohortid,
     'schoolid' => $schoolid ));
    
    $statusmenu = array( "not joined" => "not joined",
                         "joined" => "joined",
        "not registered" => "not registered");
    // $pop = new moodle_url('/blocks/student_resource_center/student_reg_rept.php?schoolid=$schoolcohortid='.$cohortid);
    $select = new single_select($baseurl1, 'status', $statusmenu, $status, null, 'statusmenu');
    $select->set_label('<b>Status</b>');
    $userlistcell = new html_table_cell();
   //$userlistcell->attributes['align'] = 'center';
    $userlistcell->text = $OUTPUT->render($select);
    $controlstable->data[0]->cells[] = $userlistcell;
if (!$export) {
    
    echo html_writer::table($controlstable);
}
      
  $coho =  $DB->get_record('cohort',array('id' => $cohortid));
      if (!$export) {

      
        echo '<div style="text-align: center; font-weight: bold; font-size: 18px">Students Registration Report <br></div>';

        echo '<div style="text-align: center; font-weight: bold;font-size: 16px">Class:&nbsp;' . $coho->name . '<br></div>';
        echo '<div style="text-align: center; font-weight: bold;font-size: 16px">Status:&nbsp;' . $status . '<br></div>';

        //	echo '<div style="text-align: center; font-weight: bold;">Department:&nbsp;'.$department .'<br></div>';
        echo '<div style="text-align: center; padding-left: 20px; margin: 5px 0;">
						<form method="post" style="display: inline; margin: 0; padding: 0;">';
        echo "<b>Download:</b>";
        echo "<select name='downloadType'>";
        echo '<option value="pdf">Download in pdf Format </option>';
        echo '<option value="Excel">Download in Excel Format </option>';
        echo '</select>';

        echo '<input type="hidden" name="export" value="true" /><input name="download" type="submit" value="Download" />
        
						</form>';
        echo "<br/>";
        echo "<br/>";
    }
     
      $table = new html_table();
      $table->tablealign = 'center';
    $table->align = 'center';
    $table->head = array();
    $table->head[] = "SN";
    $table->head[] = "Merit No";
  //  $table->head[] = "Regn No";
    $table->head[] = "Name";
    
    if($status=="joined"){
            
    $table->head[] = "Joined On";}
 //   $table->head[] = "";
   // $table->head[] = "";
   // $table->head[] = "Status";
    $table->size[] = '40px';
 if($status=="joined"){
$table->head[] = "Pending Docs";
    $table->size[] = '40px';
}

//echo $status;
   
 if($status != 'not registered' ){
        $users = $DB->get_records_sql("SELECT u.* ,rf.meritno from {user} u left join {regform} rf  on u.id=rf.userid
JOIN `mdl_cohort_members` cm ON cm.userid = u.id            
WHERE cm.cohortid = $cohortid");
  }
  else if ($status == 'not registered')
  {
     $users = $DB->get_records_sql("SELECT u.* FROM `mdl_user` u
JOIN `mdl_cohort_members` cm ON cm.userid = u.id
 WHERE NOT EXISTS (SELECT userid FROM `mdl_regform` reg where 
       cm.userid = reg.userid)
AND cm.cohortid = $cohortid");
          //   "SELECT u.* ,rf.meritno from {user} u left join {regform} rf  on u.id=rf.userid WHERE  user_subgroup ='$batch'");
  
  }
  //print_object($users);

    
    foreach ($users as $user) {
        if($status != 'not registered'){
     //   print_object($user);
   $record = $DB->get_record('user',array('id'=>$user->id));  
    profile_load_data($record); 
    if($status == 'joined'){
    if($record->profile_field_registrationstatus == 'pending' || $record->profile_field_registrationstatus == 'joined'){
  $string= '';     
if($record->user_group=="PG" && $record->profile_field_registrationstatus=='pending'){
$sql = "SELECT u.id,docs.origssc_eq as 'Original SSC / Equivalent Certificate' ,docs.orighssc_eq as 'Original HSSC / Equivalent Certificate' , docs.ibcceqv_reqd, docs.ibcceqv as 'IBCC Equivalent Certificate',docs.hsscone_reqd,docs.hsscone as 'HSSC Part One Result',
docs.copyssc_eq as 'Attested Photocopies of SSC / Equivalent Certificate',docs.copyhssc_eq as 'Attested Photocopies of HSSC / Equivalent Certificate',docs.undertaking as 'Under Taking',docs.suritybond as 'Surity Bond',docs.regform as 'Registration Form',docs.copynic as 'Copy of CNIC',docs.medicalcertificate as 'Medical Certificate',docs.photographs as 'Photographs', ";

$sql1 = "docs.pg_origbachelors_result as 'Original Bachelors Degree & Transcript', docs.pg_copybachelors_result as 'Attested Copies of Bachelors Degree & Transcript',
    docs.pg_masters_reqd,docs.pg_origmasters_result as 'Original Masters Degree & Transcript', docs.pg_copymasters_result as
    'Attested Copies of Masters Degree & Transcript', docs.pg_cgpacertificatereqd, docs.pg_cgpacertificate as 'CGPA Certicate',";

$sql2 = "u.firstname,u.lastname,u.email FROM {joiningdocs} docs 
        JOIN {user} u ON  u.id=docs.userid
        AND docs.user_group = 'PG' and u.id=$record->id";
//echo "query ".$sql.$sql1.$sql2;
$result = $DB->get_record_sql($sql.$sql1.$sql2);

 
   $to_firstname = $r->firstname; 
   $to_lastname = $r->lastname; 
   $to_email = $r->email; 
   unset($r->firstname);
   unset($r->lastname);
   unset($r->email);
 
$string .= "<html><body><ul>"; 
    $d = get_object_vars($result);
    foreach ($d as $key => $value) {
      
 if($value==0 && $key != 'ibcceqv_reqd' && $key != 'ibcc equivalent certificate' && $key != 'HSSC Part One Result' && $key != 'hsscone_reqd' 
         && $key!= 'pg_masters_reqd' && $key!='Original Masters Degree & Transcript' && $key!='Attested Copies of Masters Degree & Transcript'
         && $key!='pg_cgpacertificatereqd' && $key!='CGPA Certicate' && $key!='id' )
 {
    $string .= $key.',';
 }
 else if ($key == 'ibcceqv_reqd' && $value == 1){
     if($key == 'ibcc equivalent certificate' && $value == 0){
         $string .= $key.',';
         
     }
  
     
 }
 
else if ($key == 'hsscone_reqd' && $value == 1){
     if($key == 'HSSC Part One Result' && $value == 0){
         $string .= $key.',';
      
         
     }
   
 }
 
 else if ($key == 'pg_masters_reqd' && $value == 1){
     if($key == 'Original Masters Degree & Transcript' && $value == 0){
         $string .=$key.',';
      }
     if($key == 'Attested Copies of Masters Degree & Transcript' && $value == 0){
         $string .= $key.',';
      } 
     
 }
 else if ($key == 'pg_cgpacertificatereqd' && $value == 1){
     if($key == 'CGPA Certicate' && $value == 0){
         $string .= $key.',';
      
         
     }
     
 }
 
}}
if($record->user_group=="UG" && $record->profile_field_registrationstatus=='pending' ){
    
$sql = "SELECT u.id,docs.origssc_eq as 'Original SSC / Equivalent Certificate' ,docs.orighssc_eq as 'Original HSSC / Equivalent Certificate' , docs.ibcceqv_reqd, docs.ibcceqv as 'IBCC Equivalent Certificate',docs.hsscone_reqd,docs.hsscone as 'HSSC Part One Result',
docs.copyssc_eq as 'Attested Photocopies of SSC / Equivalent Certificate',docs.copyhssc_eq as 'Attested Photocopies of HSSC / Equivalent Certificate',docs.undertaking as 'Under Taking',docs.suritybond as 'Surity Bond',docs.regform as 'Registration Form',docs.copynic as 'Copy of CNIC',docs.medicalcertificate as 'Medical Certificate',docs.photographs as 'Photographs', 
u.firstname,u.lastname,u.email FROM {joiningdocs} docs 
        JOIN {user} u ON  u.id=docs.userid
        AND docs.user_group = 'UG'
        AND u.id = $user->id";

$result = $DB->get_record_sql($sql);

   $to_firstname = $r->firstname; 
   $to_lastname = $r->lastname; 
   $to_email = $r->email; 
$string= '';
$string .= "<html><body><ul>"; 
    $d = get_object_vars($result);
    foreach ($d as $key => $value) {
      
 if($value==0 && $key != 'ibcceqv_reqd' && $key != 'ibcc equivalent certificate' && $key != 'hssc part one result' && $key != 'hsscone_reqd' && $key != 'firstname' && $key != 'lastname' && $key != 'email' && $key!= 'id'  )
 {
    $string .= '<li>'.$key.'</li>';
 }
 else if ($key == 'ibcceqv_reqd' && $value == 1){
     if($key == 'ibcc equivalent certificate' && $value == 0){
         $string .= '<li>'.$key.'</li>';
        
     }
     
 }
 
else if ($key == 'hsscone_reqd' && $value == 1){
     if($key == 'hssc part one result' && $value == 0){
         $string .= '<li>'.$key.'</li>';
      
         
     }
 }

}}
    

        $i++;
        $table->data[$i][] = $i;
        $table->data[$i][] = $user->idnumber;
      //  $table->data[$i][] = $user->meritno;
        $table->data[$i][] = $user->firstname . " " . $user->lastname;
        
        $joiningtime = date("Y-M-d-D H:i", $record->profile_field_joiningdate); 
        
        //$joiningtime = date(" M jS, Y", $record->profile_field_joiningdate);
        $table->data[$i][] = $joiningtime;
        $table->data[$i][] = $string;

    }
    }
    else if($status == 'not joined') {
          
    if($record->profile_field_registrationstatus == $status){
             $i++;
        $table->data[$i][] = $i;
        $table->data[$i][] = $user->idnumber;
      //  $table->data[$i][] = $user->meritno;
        $table->data[$i][] = $user->firstname . " " . $user->lastname; 
    }
    }
     }
    
     
        else if ($status == 'not registered'){
           $i++;
        $table->data[$i][] = $i;
        $table->data[$i][] = $user->idnumber;
      //  $table->data[$i][] = $user->meritno;
        $table->data[$i][] = $user->firstname . " " . $user->lastname; 
     }
     
    }

?>	

<?php

if ($export) {
    if (isset($_POST['download'])) {
        $downloadType = $_POST['downloadType'];
        if ($downloadType == "Excel")
            ExportToExcel($table, $coho->name, $status);
        if ($downloadType == "pdf")
            ExportToPDF($table, $coho->name,$status);
    }

}
	if(!$export)
    echo html_writer::table($table);
    
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();

    
//================Export to Excel================//    
function ExportToExcel($data, $class) {
    global $CFG;
    global $headings;

    //require_once("$CFG->libdir/excellib.class.php");/*
    require_once($CFG->dirroot . '/lib/excellib.class.php');
    $filename = "Joining Report " . $class . ".xls";

    $workbook = new MoodleExcelWorkbook("-");
/// Sending HTTP headers
    ob_clean();
    $workbook->send($filename);
/// Creating the first worksheet
    $myxls = & $workbook->add_worksheet('Joining Report');
/// format types
    $formatbc = & $workbook->add_format();
    $formatbc1 = & $workbook->add_format();
    $formatbc->set_bold(1);
    $myxls->set_column(0, 0, 20);
    $myxls->set_column(1, 7, 30);
    $formatbc->set_align('center');
    $formatbc1->set_align('center');
    $xlsFormats = new stdClass();
    $xlsFormats->default = $workbook->add_format(array(
        'width' => 40));
    //$formatbc->set_size(14);
    $myxls->write(0, 2, "STUDENTS JOINING REPORT", $formatbc);

    $myxls->write(1, 2, "Class: " . $class, $formatbc);
    //$myxls->write(2, 2, "Employee ID: " . $uidnumber, $formatbc);


    foreach ($data->head as $heading) {
        if ($heading != "") {
            $myxls->write_string(4, $j++, strtoupper($heading), $formatbc);
        }
    }

    $i = 5;
    $j = 0;
    foreach ($data->data as $row) {
        foreach ($row as $cell) {

            //$myxls->write($i, $j++, $cell);
            if (is_numeric($cell)) {
                $myxls->write_number($i, $j++, strip_tags($cell), $formatbc1);
            } else {
                if ((strstr($cell, "<a") == false) && (strstr($cell, "---") == false)) {
                    $myxls->write_string($i, $j++, strip_tags($cell), $formatbc1);
                }
            }
        }
        $i++;
        $j = 0;
    }
    $workbook->close();
    exit;
}

//export to pdf

function ExportToPDF($data,$class, $status) {
    //echo "pdf";
    global $CFG;
    global $headings;

    //$pdf=new PDF();
    //Column titles
    //$header=$data->tabhead;
    // create new PDF document
    set_time_limit(3000);
    ini_set('memory_limit', '556M');

    //$contents="dfhonee";
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('');
    $pdf->SetTitle('Student Strength Report');
    $pdf->SetSubject('Student Strength Report Year 2013 Fall');

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

    // ---------------------------------------------------------
    // set font
    $pdf->SetFont('helvetica', 'b', 16);

    // add a pag
    $pdf->AddPage('P', 'A4');
    ob_clean();

    $htmcont = ImprovedTable($data);
   // $pdf->Image('NUST_Logo.jpg', 20, '', 20, 20, '', '', 'T', false, 0, '', false, false, 30, false, false, false);
$pdf->SetFont('helvetica', 'B', 16);

//$pdf->MultiCell(125, 2, 'School of Electronics Engineering & Computer Sciences-NUST',  0, 'L', 1, 1, 42, 10, false);
//$pdf->MultiCell(148, 2, 'School of Electrical Engineering & Computer Sciences', 0, 'L', 1, 1, 42, 15, false);
//$pdf->MultiCell(120, 2, 'Registration Form - UG/PG Program', 0, 'C', 0, 1, 42, 20, false);
 $pdf->MultiCell (0, 0, 'NUST School of Electrical Engineering & Computer Science', 0, 'C', false, 1, 15,'', true, 0, false, true, 0, 'T', false);
    //$pdf->MultiCell(0, 0, 'School of Electrical Engineering & Computer Science-NUST', 0, 1, 'C');
   // $pdf->SetFont('helvetica', 'b', 16);
     $pdf->Cell(0, 0, 'Joining Report Of Class: '.$class.' , Status: '.$status, 0, 1, 'C');
     $pdf->Ln(8);
     $pdf->SetFont('helvetica', '', 10);
   // $pdf->writeHTML($htmcont, true, false, false, false, '');
    $pdf->writeHTMLCell(180,80,15,50,$htmcont, 0, 1, false, true, '', true);
   
    //echo $htmcont;
    // ---------------------------------------------------------
    //Close and output PDF document

    $pdf->Output("Joining_Report", 'D');


//    if ($type == "email") {
//        $pdf->Output("/moodledata/RegistrationReport_" . $batch . '.pdf', "F");
//        $zipname = "RegistrationReport_" . $batch . '.pdf';
//        $actual_zip = "RegistrationReport_" . $batch . '.pdf';
//
//        //email_to_user($usrme, get_admin(), 'Attendance Short Notice', '', 'Details',$actual_zip,$zipname, $usetrueaddress=true, $replyto='', $replytoname='', $wordwrapwidth=79);
//        //@unlink("/moodledata/".$actual_zip);
//    }
    exit;
}

//

function ImprovedTable($data) {
    global $CFG;
    global $headings;

    //echo "improe";
    //Column widths
    //$w=array(40,35,40,45);
    //Header
  //  $content =  '<h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; School of Electronics Engineering & Computer Sciences-NUST</h3>';
  //  $content =  $content .  '<h3 align="center" >                     Students Strength Report (Year 2013 Fall)</h4><br/>';

//   $content = $content . '<h3 align="right">Of Year 2013</h3>';
    $content = '<br/>';
    $content = $content . '<table align="center" cellpadding="2" border="1"><tr>';
    $i = 0;
  
    foreach ($data->head as $heading) {
        //$this->Cell($w[$i],7,$header[$i],1,0,'C');
     if ($i == 0) {
                $content = $content . '<td width="5%" align="center">' . strip_tags($heading) . '</td>';
            } elseif ($i == 1) {
                $content = $content . '<td width="10%" align="center">' . strip_tags($heading) . '</td>';
            } elseif ($i == 2) {
                $content = $content . '<td width="20%" align="center">' . strip_tags($heading) . '</td>';
            }
            elseif ($i == 3) {
                $content = $content . '<td width="20%" align="center">' . strip_tags($heading) . '</td>';
            }
            elseif ($i == 4) {
                $content = $content . '<td width="45%" align="center">' . strip_tags($heading) . '</td>';
            }
        $i++;
    }
    //$this->Ln();
    $content = $content . '</tr>';
    //Data
    foreach ($data->data as $row) {
        $content = $content . '<tr>';
        $i = 0;
        foreach ($row as $col) {
            if ($i == 0) {
                $content = $content . '<td width="5%" align="center">' . strip_tags($col) . '</td>';
            } elseif ($i == 1) {
                $content = $content . '<td width="10%" align="center">' . strip_tags($col) . '</td>';
            } elseif ($i == 2) {
                $content = $content . '<td width="20%" align="center">' . strip_tags($col) . '</td>';
            }
            elseif ($i == 3) {
                $content = $content . '<td width="20%" align="center">' . strip_tags($col) . '</td>';
            }
            elseif ($i == 4) {
                $content = $content . '<td width="45%" align="center">' . strip_tags($col) . '</td>';
            }
            $i = $i + 1;
        }
        $content = $content . '</tr>';
    }
    $content = $content . '</table>';
    
    
    return $content;
    
}
