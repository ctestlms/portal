<style>
#icon_table
{
    width:100%;
}
#icon_table td
{
    text-align: center;

}</style>
<?php
require_once('../../../config.php');

global $OUTPUT,$PAGE;
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/student_resource_center/requests/requestslist.php');
$PAGE->set_title(get_string('student_requests', 'block_student_resource_center'));
$PAGE->set_heading(get_string('student_requests', 'block_student_resource_center'));
$PAGE->set_pagelayout('mydashboard');
//$PAGE->navbar->add(get_string('myhome'),new moodle_url('/my/'));
//$PAGE->navbar->add("myhome",new moodle_url('/my/'));
$PAGE->navbar->add(get_string('student_requests', 'block_student_resource_center'),new moodle_url('/blocks/student_resource_center/requests/requestslist.php'));
$PAGE->navigation->clear_cache();


 echo $OUTPUT->header();
 
 echo $OUTPUT->box_start();

  
 echo '<div style="text-align:center;"><h2 style="font-family: Judson;
color: #2D3085;
background-color: #FFFFFF;">Student Resource Center</h2>';
echo "<table id = 'icon_table'/>";
echo "<tr>";
echo "<td><a href='instructions.php?req=certificates'><img src='icons/certificate_icon.jpg' alt='' border=3 height=100 width=100></img></br>Provisional/other Certificates</a></td>";
echo "<td><a href='instructions.php?req=nustid'><img src='icons/id_icon.jpg' alt='' border=3 height=100 width=100></img></br> NUST ID Card</a></td>";
echo "<td><a href='instructions.php?req=degree'><img src='icons/degree_icon.jpg' alt='' border=3 height=100 width=100></img></br> Degree Issuance </br> Before Convocation</a></td>";
echo "</tr>";
echo "<tr>";
echo "<td><a href='instructions.php?req=transcript'><img src='icons/transcript_icon2.jpg' alt='' border=3 height=100 width=80></img></br> Transcript Issuance</a></td>";
echo "<td><a href='leave/leave.php'><img src='icons/leave_icon.jpg' alt='' border=3 height=80 width=100></img></br> School Leaving Permission</a></td>";
echo "<td><a href='requests_dashboard.php'><img src='icons/track_icon2.jpg' alt='' border=3 height=100 width=100></img></br> Request Tracking</td>";
echo "</tr>";
echo "</table>";
echo "</div>";
echo $OUTPUT->box_end(); 
echo $OUTPUT->footer();
 