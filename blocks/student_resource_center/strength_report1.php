<?php
require_once('../../config.php');
//require_once($CFG->libdir . '/blocklib.php');
//require_once($CFG->libdir . '/formslib.php');
//require_once('./view_contact_form.php');
require('../../mod/attforblock/tcpdf/config/lang/eng.php');
require('../../mod/attforblock/tcpdf/tcpdf.php');
//include('dbcon.php');
?>
<?php
require_login();
//session_start();
$allow = 1;


$export = optional_param('export', false, PARAM_BOOL);
 $schoolid       = required_param('schoolid', PARAM_INT);
 
$context = get_context_instance(CONTEXT_COURSECAT, $schoolid);
 require_capability('block/student_resource_center:addinstance', $context);
$navlinks[] = array('name' => 'Students Strength Report', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if (!$export){
    echo $OUTPUT->header();
}
   // print_header('Students Strength Report', 'Students Strength Report', $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);


  $baseurl = new moodle_url('/blocks/student_resource_center/strength_report1.php');

echo $OUTPUT->box_start();


    if (!$export) {

    echo '<div style="text-align: center; font-weight: bold; font-size: 20pt;">Students Registration Report <br></div>';
 echo '<br/><br/>';
        //	echo '<div style="text-align: center; font-weight: bold;">Department:&nbsp;'.$department .'<br></div>';
        echo '<div style="text-align: center; padding-left: 20px; margin: 5px 0;">
						<form method="post" style="display: inline; margin: 0; padding: 0;">';
        echo "<b>Download:</b>";
        echo "<select name='downloadType'>";
        echo '<option value="pdf">Download in pdf Format </option>';
       // echo '<option value="Excel">Download in Excel Format </option>';
        echo '</select>';

        echo '<input type="hidden" name="catg" value="year2013" />';
        echo '<input type="hidden" name="export" value="true" /><input name="download" type="submit" value="Download" />
        
						</form></div><br/>';
      
    }

   // $no_of_departments = 0;
    $table = new html_table();
    $table->tablealign = 'center';
    $table->align = 'center';
    $table->head = array();
    $table->head[] = "Batch";
    $table->head[] = "Open Merit";
    $table->head[] = "SAT(NAT)";
    $table->head[] = "SAT(Intl)";
    $table->head[] = "Registered";
    $table->head[] = "Not Registered";
    $table->head[] = "Joined";
    $table->head[] = "Pending";
    $table->head[] = "Not Joined";
    
    $table->head[] = "Total";
    
    
       $table1 = new html_table();
    $table1->tablealign = 'center';
    $table1->align = 'center';
    $table1->head = array();
    $table1->head[] = "Batch";
    $table1->head[] = "Open Merit";
    $table1->head[] = "SAT(NAT)";
    $table1->head[] = "SAT(Intl)";
    $table1->head[] = "Registered";
    $table1->head[] = "Not Registered";
    $table1->head[] = "Joined";
    $table1->head[] = "Pending";
    $table1->head[] = "Not Joined";
    
    $table1->head[] = "Total";
    
  

$i = 0;

 
/*$sql2 = "SELECT  u.user_subgroup 
FROM `mdl_regform` rf
JOIN `mdl_user` u ON u.id = rf.userid
group by u.user_subgroup";
 * 
 */
 

$sql2 = "SELECT name,id FROM {cohort}
   WHERE ( id = 462 OR id = 463 OR id = 464 OR id = 465 OR id = 466 OR
   id = 467 OR id = 468 OR id = 469 OR id = 472 OR id = 472 OR id = 473 OR id = 474 OR id = 479 )";

    $classlist = $DB->get_records_sql($sql2);
    

    foreach ($classlist as $class) {
      
    
    
    if(!$class->name == ''){
     
        
    $sql = "SELECT COUNT(rf.admissiontype) AS type
              FROM {regform} rf  
              JOIN {cohort_members} cm ON rf.userid = cm.userid
              WHERE cm.cohortid = $class->id";
              
    
        
    $op = " AND rf.admissiontype = 'Open Merit'";
    $nat = " AND rf.admissiontype = 'SAT(NAT)'";
    $sat = " AND rf.admissiontype = 'SAT(Intl)'";
     
      $openmerit_count = $DB->get_record_sql($sql.$op);
      $nat_count = $DB->get_record_sql($sql.$nat);
      $sat_count = $DB->get_record_sql($sql.$sat);
        
       $sql1 =  "SELECT COUNT(uid.data) as joi
              FROM {user_info_data} uid 
              JOIN {cohort_members} cm ON cm.userid = uid.userid
              WHERE cm.cohortid =  '$class->id'
              AND uid.data = 'joined'
               ";
       $rec = $DB->get_record_sql($sql1);
        $sql2 =  "SELECT COUNT(uid.data) as pen
            FROM {cohort_members} cm 
              JOIN {user_info_data} uid ON cm.userid = uid.userid
              WHERE cm.cohortid =  '$class->id'
              AND uid.data = 'pending'
               ";
        
       $rec2 = $DB->get_record_sql($sql2);
       
       $sql3 =  "SELECT COUNT(id) AS users
              FROM {cohort_members} 
              WHERE cohortid =  '$class->id'
               ";
       $rec3 = $DB->get_record_sql($sql3);
    
      $totalcount = $rec3->users;
          $totalregistered =  $openmerit_count->type + $sat_count->type + $nat_count->type;
       //print_r(count($rec->id));
      $join = $rec->joi;
      $pend = $rec2->pen;
      $total = $totalcount - $join;
      $tot = $total - $pend;
      $notregistered = $totalcount - $totalregistered;
       // $table->data[$i][] = $i;
      
      $table->data[$i][] = $class->name;
        $table->data[$i][] = $openmerit_count->type;
        $table->data[$i][] = $nat_count->type;
        $table->data[$i][] =  $sat_count->type;
        $table->data[$i][] =   $totalregistered;
        $table->data[$i][] =   $notregistered;
        $table->data[$i][] =  $join;
        $table->data[$i][] =  $pend;
        $table->data[$i][] =  $tot;
        
        $table->data[$i][] = $totalcount;
       $i++; 
 
   
    }
    }
    if (!$export){
   echo html_writer::table($table);  
    }
    

    //Start
    $group = "SELECT LEFT(`name`, LOCATE('-', `name`)-1) AS name
 FROM `mdl_cohort`  WHERE( name LIKE '%BEE5-%'  OR name LIKE '%BESE4-%' OR name LIKE '%BSCS3-%')";
$grouplist = $DB->get_records_sql($group);
$groupms = new stdClass();
$groupms->name = 'MS';

$grouplists = (object) array_merge((array) $grouplist, (array) $groupms);


    foreach ($grouplists as $class) {

        if ($class == 'MS'){
    
                $sql = "SELECT COUNT(rf.admissiontype) AS type
              FROM {regform} rf  
              JOIN {cohort_members} cm ON rf.userid = cm.userid
              JOIN {cohort} c ON c.id = cm.cohortid 
              WHERE (c.name = 'MSIT-14' OR c.name = 'MS (CS)-3' OR c.name = 'MSEE-5' OR c.name = 'MS (CCS)-6') ";
              
    
        
    $op = " AND rf.admissiontype = 'Open Merit'";
    $nat = " AND rf.admissiontype = 'SAT(NAT)'";
    $sat = " AND rf.admissiontype = 'SAT(Intl)'";
     
      $openmerit_count = $DB->get_record_sql($sql.$op);
      $nat_count = $DB->get_record_sql($sql.$nat);
      $sat_count = $DB->get_record_sql($sql.$sat);
        
       $sql1 =  "SELECT COUNT(uid.data) as joi
              FROM {user_info_data} uid 
              JOIN {cohort_members} cm ON cm.userid = uid.userid
               JOIN {cohort} c ON c.id = cm.cohortid 
              WHERE (c.name = 'MSIT-14' OR c.name = 'MS (CS)-3' OR c.name = 'MSEE-5' OR c.name = 'MS (CCS)-6')
              AND uid.data = 'joined'
               ";
       $rec = $DB->get_record_sql($sql1);
        $sql2 =  "SELECT COUNT(uid.data) as pen
            FROM {cohort_members} cm 
              JOIN {user_info_data} uid ON cm.userid = uid.userid
               JOIN {cohort} c ON c.id = cm.cohortid 
               WHERE (c.name = 'MSIT-14' OR c.name = 'MS (CS)-3' OR c.name = 'MSEE-5' OR c.name = 'MS (CCS)-6')
              AND uid.data = 'pending'
               ";
        
       $rec2 = $DB->get_record_sql($sql2);
       
       $sql3 =  "SELECT COUNT(cm.id) AS users
              FROM {cohort_members} cm 
              JOIN {cohort} c ON c.id = cm.cohortid 
              WHERE (c.name = 'MSIT-14' OR c.name = 'MS (CS)-3' OR c.name = 'MSEE-5' OR c.name = 'MS (CCS)-6')
               ";
       $rec3 = $DB->get_record_sql($sql3);
    
   
      $totalcount = $rec3->users;
          $totalregistered =  $openmerit_count->type + $sat_count->type + $nat_count->type;
       //print_r(count($rec->id));
      $join = $rec->joi;
      $pend = $rec2->pen;
      $total = $totalcount - $join;
      $tot = $total - $pend;
      $notregistered = $totalcount - $totalregistered;
      
      $table1->data[$i][] = $class;
        $table1->data[$i][] = $openmerit_count->type;
        $table1->data[$i][] = $nat_count->type;
        $table1->data[$i][] =  $sat_count->type;
        $table1->data[$i][] =   $totalregistered;
        $table1->data[$i][] =   $notregistered;
        $table1->data[$i][] =  $join;
        $table1->data[$i][] =  $pend;
        $table1->data[$i][] =  $tot;
        
        $table1->data[$i][] = $totalcount;
       $i++; 
 
        }        
            
        
        
   else if (!$class->name == '') {
         $sql = "SELECT COUNT(rf.admissiontype) AS type
              FROM {regform} rf  
              JOIN {cohort_members} cm ON rf.userid = cm.userid
              JOIN {cohort} c ON c.id = cm.cohortid 
              WHERE c.name LIKE '%$class->name%'";
              
    
        
    $op = " AND rf.admissiontype = 'Open Merit'";
    $nat = " AND rf.admissiontype = 'SAT(NAT)'";
    $sat = " AND rf.admissiontype = 'SAT(Intl)'";
     
      $openmerit_count = $DB->get_record_sql($sql.$op);
      $nat_count = $DB->get_record_sql($sql.$nat);
      $sat_count = $DB->get_record_sql($sql.$sat);
        
       $sql1 =  "SELECT COUNT(uid.data) as joi
              FROM {user_info_data} uid 
              JOIN {cohort_members} cm ON cm.userid = uid.userid
               JOIN {cohort} c ON c.id = cm.cohortid 
              WHERE c.name LIKE '%$class->name%'
              AND uid.data = 'joined'
               ";
       $rec = $DB->get_record_sql($sql1);
        $sql2 =  "SELECT COUNT(uid.data) as pen
            FROM {cohort_members} cm 
              JOIN {user_info_data} uid ON cm.userid = uid.userid
               JOIN {cohort} c ON c.id = cm.cohortid 
               WHERE c.name LIKE '%$class->name%'
              AND uid.data = 'pending'
               ";
        
       $rec2 = $DB->get_record_sql($sql2);
       
       $sql3 =  "SELECT COUNT(cm.id) AS users
              FROM {cohort_members} cm 
              JOIN {cohort} c ON c.id = cm.cohortid 
              WHERE c.name LIKE  '%$class->name%'
               ";
       $rec3 = $DB->get_record_sql($sql3);
    
   
      $totalcount = $rec3->users;
          $totalregistered =  $openmerit_count->type + $sat_count->type + $nat_count->type;
       //print_r(count($rec->id));
      $join = $rec->joi;
      $pend = $rec2->pen;
      $total = $totalcount - $join;
      $tot = $total - $pend;
      $notregistered = $totalcount - $totalregistered;
      
      $table1->data[$i][] = $class->name;
        $table1->data[$i][] = $openmerit_count->type;
        $table1->data[$i][] = $nat_count->type;
        $table1->data[$i][] =  $sat_count->type;
        $table1->data[$i][] =   $totalregistered;
        $table1->data[$i][] =   $notregistered;
        $table1->data[$i][] =  $join;
        $table1->data[$i][] =  $pend;
        $table1->data[$i][] =  $tot;
        
        $table1->data[$i][] = $totalcount;
       $i++; 
 
    
    }
    }
    if (!$export){
   echo html_writer::table($table1);  
    }
    
    
    
echo $OUTPUT->box_end();
?>	

<?php

if ($export) {
    if (isset($_POST['download'])) {
        $downloadType = $_POST['downloadType'];
   //     if ($downloadType == "Excel")
     //       ExportToExcel($table, "download");
        if ($downloadType == "pdf")
            ExportToPDF($table, $table1, "download");
    }

}
//}

   
   


//$OUTPUT->box_end();
echo $OUTPUT->footer();


//================Export to Excel================//    

function ExportToExcel($data, $type) {
    global $CFG;
    global $headings;

    //require_once("$CFG->libdir/excellib.class.php");/*
    require_once($CFG->dirroot . '/lib/excellib.class.php');
    $filename = "strength_report_2013_fall.xls";

    $workbook = new MoodleExcelWorkbook("-");
/// Sending HTTP headers
    ob_clean();
    $workbook->send($filename);
/// Creating the first worksheet
    $myxls = & $workbook->add_worksheet('Students Strength Report (Year 2013 Fall)');
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
    $myxls->write(0, 2, "Students Strength Report (Year 2013 Fall)", $formatbc);

    

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



function ExportToPDF($data,$data1, $type) {
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
    $pdf->AddPage('L', 'A4');
    ob_clean();

    $htmcont = ImprovedTable($data);
    $pdf->Image('NUST_Logo.jpg', 20, '', 20, 20, '', '', 'T', false, 0, '', false, false, 30, false, false, false);
$pdf->SetFont('helvetica', 'B', 16);

//$pdf->MultiCell(125, 2, 'School of Electronics Engineering & Computer Sciences-NUST',  0, 'L', 1, 1, 42, 10, false);
//$pdf->MultiCell(148, 2, 'School of Electrical Engineering & Computer Sciences', 0, 'L', 1, 1, 42, 15, false);
//$pdf->MultiCell(120, 2, 'Registration Form - UG/PG Program', 0, 'C', 0, 1, 42, 20, false);
 $pdf->MultiCell (0, 0, 'NUST School of Electrical Engineering & Computer Science', 0, 'C', false, 1, 15,'', true, 0, false, true, 0, 'T', false);
    //$pdf->MultiCell(0, 0, 'School of Electrical Engineering & Computer Science-NUST', 0, 1, 'C');
   // $pdf->SetFont('helvetica', 'b', 16);
     $pdf->Cell(250, 0, ' Students Strength Report (Year 2013 Fall)', 0, 1, 'C');
     $pdf->Ln(8);
     $pdf->SetFont('helvetica', '', 10);
   // $pdf->writeHTML($htmcont, true, false, false, false, '');
    $pdf->writeHTMLCell(180,80,15,50,$htmcont, 0, 1, false, true, '', true);
    $htmcont1 = ImprovedTable($data1);
    $pdf->writeHTMLCell(180,40,15,140,$htmcont1, 0, 1, false, true, '', true);
    //echo $htmcont;
    // ---------------------------------------------------------
    //Close and output PDF document

    $pdf->Output("Strength_Report", 'D');


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
    $content = $content . '<table  cellpadding="2" border="1"><tr>';
    $i = 0;
  
    foreach ($data->head as $heading) {
        //$this->Cell($w[$i],7,$header[$i],1,0,'C');
        if ($i == 0) {
            $content = $content . '<td width="20%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
        } elseif ($i == 1) {
            $content = $content . '<td width="15%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
        } elseif ($i == 2) {
            $content = $content . '<td width="15%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
        } elseif ($i == 3) {
            $content = $content . '<td width="15%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
        } elseif ($i == 4) {
            $content = $content . '<td width="15%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
        }
        elseif ($i == 5) {
            $content = $content . '<td width="15%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
        }
        elseif ($i == 6) {
            $content = $content . '<td width="15%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
        }
        elseif ($i == 7) {
            $content = $content . '<td width="10%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
        }        elseif ($i == 8) {
          $content = $content . '<td width="15%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
        }
             elseif ($i == 9) {
          $content = $content . '<td width="10%" bgcolor="#cccccc" align="center"><b>' . $heading . '</b></td>';
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
                $content = $content . '<td width="20%" align="center">' . strip_tags($col) . '</td>';
            } elseif ($i == 1) {
                $content = $content . '<td width="15%" align="center">' . strip_tags($col) . '</td>';
            } elseif ($i == 2) {
                $content = $content . '<td width="15%" align="center">' . strip_tags($col) . '</td>';
            } elseif ($i == 3) {
                $content = $content . '<td width="15%" align="center">' . strip_tags($col) . '</td>';
            } elseif ($i == 4) {
                $content = $content . '<td width="15%" align="center">' . strip_tags($col) . '</td>';
            }
            elseif ($i == 5) {
                $content = $content . '<td width="15%" align="center">' . strip_tags($col) . '</td>';
            }
            elseif ($i == 6) {
                $content = $content . '<td width="15%" align="center">' . strip_tags($col) . '</td>';
            }
            elseif ($i == 7) {
                $content = $content . '<td width="10%" align="center">' . strip_tags($col) . '</td>';
            }
            elseif ($i == 8) {
                $content = $content . '<td width="15%" align="center">' . strip_tags($col) . '</td>';
           }
            elseif ($i == 9) {
                $content = $content . '<td width="10%" align="center">' . strip_tags($col) . '</td>';
           }
            $i = $i + 1;
        }
        $content = $content . '</tr>';
    }
    $content = $content . '</table>';
    
    
    return $content;
    
}



?>
