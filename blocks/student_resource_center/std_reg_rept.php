<script type="text/javascript" src="jquery-1.3.2.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
			
        $('#loader').hide();
        $('#show_heading').hide();
			
        $('#school_').change(function(){
			
            $('#show_departments').fadeOut();
            $('#loader').show();
            // alert($('#school_').val());
            $.post("get_batches.php", {
				
                parent_id: $('#school_').val() ,status: $('#status').val(),

            }, function(response){
					
                setTimeout("finishAjax('show_departments', '"+escape(response)+"')", 400);
            });
            return false;
        });
    });

    function finishAjax(id, response){
        $('#loader').hide();
        if($('#school_').val()!="0|NUST CAMPUS"){
            $('#show_heading').show();
        }
        if($('#school_').val()=="0|NUST CAMPUS"){
            $('#show_heading').hide();
        }
        $('#'+id).html(unescape(response));
        $('#'+id).fadeIn();
    } 

    function alert_id()
    {
        if($('#sub_category_id').val() == '')
            alert('Please select a sub category.');
        else
            alert($('#sub_category_id').val());
        return false;
    }
</script>
<?php
require_once('../../config.php');
require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/formslib.php');
require_once('./view_contact_form.php');
require('../../mod/attforblock/tcpdf/config/lang/eng.php');
require('../../mod/attforblock/tcpdf/tcpdf.php');
include('dbcon.php');
?>


<?php
require_login($course->id);
session_start();
$allow = 1;

$catg = optional_param('catg', 0, PARAM_INT); //get user sub group.
$school_ = $_GET['school_'];
$unique_courses[] = -1;
$hod = 0;
$observer = 0;
$user->id = 0;
$export = optional_param('export', false, PARAM_BOOL);
//$context = get_context_instance(CONTEXT_COURSECAT, $categoryid);
$navlinks[] = array('name' => get_string('std_reg_report', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if (!$export)
    print_header(get_string('std_reg_report', 'block_custom_reports'), get_string('std_reg_report', 'block_custom_reports'), $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);
$batch = $_POST['batch'];
$batch = optional_param('batch', "", PARAM_ALPHANUM); //get user sub group.
$status = optional_param('status', "", PARAM_TEXT); //get user status.
if (isset($_POST['view'])) {
    $_SESSION['batch'] = $batch;
    $_SESSION['status']=$status;
}
if ($export || isset($_POST['email'])) {
    $batch = $_SESSION['batch'];
    $status = $_SESSION['status'];
}

//if (isset($_POST['email']) || $SESSION->sendemail) {
//
//    $mform = new mod_custom_reports_view_contact_form('std_reg_rept.php', array('batch' => $batch, 'catg' => $catg));
//    if ($fromform = $mform->get_data()) {
//        $batch = $fromform->batch;
//        $subject = $fromform->batch;
//        $message = $fromform->message;
//        foreach ($message as $msg) {
//            $message = $fromform->text;
//        }
//        $sendemail = true;
//    } else {
//        $SESSION->sendemail = true;
//        $mform->display();
//    }
//}


if ((!$export || isset($_POST['view']))) {
    if ($allow == 1) {

        Print_form($courses_, $uname);
    } else {
        echo "<b>You do no have permission to view this report.</b>" . $categoryid;
    }
}
if ($export || isset($_POST['view']) || $batch || $sendemail) {
    $department = $_SESSION['department'];
    $school = $_POST['school'];
    $id_name = explode("|", $school);
    $schoolid = $id_name[0];         // school id
    if ($catg != "") {
        $schoolid = $catg;
    }
    $context = get_context_instance(CONTEXT_COURSECAT, $schoolid);
    require_capability('block/custom_reports:getembaregreport', $context);
    $schoolname = $id_name[1];
}

if ((isset($_POST['view']) or $export || $batch) && !isset($_POST['email'])) {

    $month = (int) date('m');



    if ($id != 0) {
        $user_subgroup = "AND user_subgroup = '$department'";
    }
    if ($id == 0) {
        $user_subgroup = "";
    }

    //checking for permissions
    $context = get_context_instance(CONTEXT_COURSECAT, $id);

    if ($id != 0) {     //If the school is selected not NUST
    }


    if (!$export) {

        echo "<br/>";
        echo '<div style="text-align: center; font-weight: bold;">Students Registration Report <br></div>';

        echo '<div style="text-align: center; font-weight: bold;">Batch:&nbsp;' . $batch . '<br></div>';

        //	echo '<div style="text-align: center; font-weight: bold;">Department:&nbsp;'.$department .'<br></div>';
        echo '<div style="text-align: left; padding-left: 20px; margin: 5px 0;">
						<form method="post" style="display: inline; margin: 0; padding: 0;">';
        echo "<b>Download:</b>";
        echo "<select name='downloadType'>";
        echo '<option value="pdf">Download in pdf Format </option>';
        echo '<option value="Excel">Download in Excel Format </option>';
        echo '</select>';

        echo '<input type="hidden" name="catg" value="' . $schoolid . '" />';
        echo '<input type="hidden" name="export" value="true" /><input name="download" type="submit" value="Download" />
        
						</form>';
        echo "<br/>";
    }

//    echo' <form action="sendemail.php" method="post" style="display: inline; margin: 0; padding: 0;">';
//    echo' <input name="batch" id="batch" type="hidden" value="' . $batch . '" />';
//    echo' <input name="catg" type="hidden" value="' . $catg . '" />';
//    echo' <input name="email" type="submit" value="Send Email to Department" />
//						</form>';


    $no_of_departments = 0;
    $table = new html_table();
    $qavg = array();
    $table->head = array();
    $table->head[] = "S/NO";
    $table->head[] = "Merit No";
    $table->head[] = "Regn No";
    $table->head[] = "Name";
    $table->head[] = "Joined On";
    $table->head[] = "";
    $table->head[] = "";
    $table->head[] = "Status";
    $table->size[] = '40px';

    ////get course of the selected department
    if ($id != 0) {
        $user_subgroup = "AND user_subgroup = '$department'";
    }
    if ($id == 0) {
        $user_subgroup = "";
    }


    $i = 0;
    $course_no = 0;
    if($status=="Left"){
        $users = $DB->get_records_sql("SELECT * from {user} u WHERE user_subgroup ='$batch' and deleted=1 ");
    }
    elseif($status=="Pending Documents"){
        $users = $DB->get_records_sql("SELECT * from {user} u WHERE user_subgroup ='$batch' and registrationstatus='$status' ");
    }
    else{
        $users = $DB->get_records_sql("SELECT * from {user} u WHERE user_subgroup ='$batch' and registrationstatus='$status' ");
    }

    foreach ($users as $user) {

        $i++;
        $table->data[$i][] = $i;
        $table->data[$i][] = $user->idnumber;
        $table->data[$i][] = $user->idnumber;
        $table->data[$i][] = $user->firstname . " " . $user->lastname;
        $joiningtime = date(" M jS, Y", $user->timemodified);
        $table->data[$i][] = $joiningtime;
        if ($user->deleted != 1) {
            $table->data[$i][] = "<a href='editadvanced.php?id=" . $user->id . "&schoolid=" . $schoolid . "&batch=" . $batch ."&status=" . $status . "'>Edit Profile</a>";
            $table->data[$i][] = "<a href='user.php?delete=" . $user->id . "&sesskey=" . sesskey() . "&schoolid=" . $schoolid . "&batch=" . $batch ."&status=" . $status . "'>Delete User</a>";
            $table->data[$i][] = "Active";
        }
        if ($user->deleted == 1) {
            $table->data[$i][] = "---";
            $table->data[$i][] = "---";
            $table->data[$i][] = "Left";
        }
    }
}
?>	

<?php

if ($export) {
    if (isset($_POST['download'])) {
        $downloadType = $_POST['downloadType'];
        if ($downloadType == "Excel")
            ExportToExcel($table, $batch, $department, $uname, $uidnumber);
        if ($downloadType == "pdf")
            ExportToPDF($table, $batch, "download");
    }

}
//}

if ((isset($_POST['view']) || $batch)) {
//    if ($sendemail) {
//
//        ExportToPDF($table, $batch, "email");
//    }
    echo html_writer::table($table);
}

$OUTPUT->box_end();
echo $OUTPUT->footer();

//================Export to Excel================//    
function ExportToExcel($data, $name, $department, $uname, $uidnumber) {
    global $CFG;
    global $headings;

    //require_once("$CFG->libdir/excellib.class.php");/*
    require_once($CFG->dirroot . '/lib/excellib.class.php');
    $filename = "Feedback Report " . $uname . ".xls";

    $workbook = new MoodleExcelWorkbook("-");
/// Sending HTTP headers
    ob_clean();
    $workbook->send($filename);
/// Creating the first worksheet
    $myxls = & $workbook->add_worksheet('Faculty Feedback Report');
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
    $myxls->write(0, 2, "STUDENTS REGISTRATION REPORT", $formatbc);

    $myxls->write(1, 2, "Batch: " . $name, $formatbc);
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



function ExportToPDF($data, $batch, $type) {
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
    $pdf->SetTitle('Attendance_short_notice');
    $pdf->SetSubject('Attendance_short_notice');

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
    $pdf->SetFont('helvetica', '', 8);

    // add a page
    $pdf->AddPage('P', 'A4');
    ob_clean();
    $htmcont = ImprovedTable($batch, $data);
    $pdf->writeHTML($htmcont, true, false, false, false, '');
    //echo $htmcont;
    // ---------------------------------------------------------
    //Close and output PDF document

    $pdf->Output("Feedback_Report", 'D');


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

function ImprovedTable($batch, $data) {
    global $CFG;
    global $headings;

    //echo "improe";
    //Column widths
    //$w=array(40,35,40,45);
    //Header
    $content = $content . '<table cellpadding="2" border="0"><tr><td ><img src="NUST_Logo.jpg" height="52" width="52" /> <font size="15"><b>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Student Registration Report</b><br/></font></td></tr></table>';

    $content = $content . '<h3 align="left">Batch: ' . $batch . '</h3>';

    $content = $content . '<table cellpadding="2" border="1"><tr>';
    $i = 0;
  
    foreach ($data->head as $heading) {
        //$this->Cell($w[$i],7,$header[$i],1,0,'C');
        if ($i == 0) {
            $content = $content . '<td width="5%"><b>' . $heading . '</b></td>';
        } elseif ($i == 1) {
            $content = $content . '<td width="20%"><b>' . $heading . '</b></td>';
        } elseif ($i == 2) {
            $content = $content . '<td width="20%"><b>' . $heading . '</b></td>';
        } elseif ($i == 3) {
            $content = $content . '<td width="15%"><b>' . $heading . '</b></td>';
        } elseif ($i == 4) {
            $content = $content . '<td width="10%"><b>' . $heading . '</b></td>';
        } elseif ($i == 7) {
            $content = $content . '<td width="10%"><b>' . $heading . '</b></td>';
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
                $content = $content . '<td width="5%">' . strip_tags($col) . '</td>';
            } elseif ($i == 1) {
                $content = $content . '<td width="20%">' . strip_tags($col) . '</td>';
            } elseif ($i == 2) {
                $content = $content . '<td width="20%">' . strip_tags($col) . '</td>';
            } elseif ($i == 3) {
                $content = $content . '<td width="15%">' . strip_tags($col) . '</td>';
            } elseif ($i == 4) {
                $content = $content . '<td width="10%">' . strip_tags($col) . '</td>';
            } elseif ($i == 7) {
                $content = $content . '<td width="10%">' . strip_tags($col) . '</td>';
            }
            $i = $i + 1;
        }
        $content = $content . '</tr>';
    }
    $content = $content . '</table>';
    //Closure line
    //$this->Cell(array_sum($w),0,'','T');
    //echo 'Hello';
    return $content;
}

function Print_form($courses, $name) {
    global $CFG, $DB;
    echo "<br/><b>Select Status Type:</b>";

    echo "<select name='status' id='status'>";
    echo "<option value='Joined'>Joined</option>";
    echo "<option value='not joined'>Not Joined</option>";
    echo "<option value='Pending'>Pending</option>";
    echo "<option value='Left'>Left</option>";
    echo "<option value='Pending Documents'>Pending Documents</option>";

    echo "</select><br/>";
    echo "<br/><b>Select School:</b>";
    $query = "SELECT id,name FROM {course_categories} WHERE parent =0";
    if ($groups = $DB->get_records_sql($query)) {
        echo "<select name='school_' id='school_'>";
        echo "<option value='0|NUST CAMPUS'>NUST CAMPUS</option>";
        foreach ($groups as $group) {
            $school_name = str_replace("&", "and", $group->name);
            $value = $group->id . "|" . $school_name;
            $selected = ($value == $school_) ? "selected = 'selected'" : "";
            echo "<option value='{$value}' {$selected} >{$group->name}</option>";
        }
        echo "</select><br/>";
        ?>	

        <div id="show_departments" >
            <img src="loader.gif"  id="loader" alt="" />
        </div> 

        <?php
    }
    
      
    
}


?>
