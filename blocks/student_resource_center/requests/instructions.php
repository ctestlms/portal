<script language="Javascript" type="text/javascript">  
 function enable(){  
  if (document.agreement_form.agreed.checked==''){  
   document.agreement_form.submit_button.disabled=true  
  }else{  
   document.agreement_form.submit_button.disabled=false  
  }  
 }  
</script>  
<?php
require_once('../../../config.php');

global $OUTPUT,$PAGE;
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/student_resource_center/requests/requestslist.php');
$PAGE->set_title(get_string('instructions', 'block_student_resource_center'));
$PAGE->set_heading(get_string('instructions', 'block_student_resource_center'));
$PAGE->set_pagelayout('mydashboard');
$PAGE->navbar->add(get_string('instructions', 'block_student_resource_center'),new moodle_url('/blocks/student_resource_center/requests/instructions.php'));
$PAGE->navigation->clear_cache();


 echo $OUTPUT->header();
 
 echo $OUTPUT->box_start();
 $req = $_GET['req'];
 if($req == 'certificates'){
echo '<h2>Certificates</h2>';
 echo '<ul>';
echo '<li>Select the type of certificate and enter a brief description.</li>';
echo '<li>Deposit slip number must be exactly same as per the fee slip.</li>';
echo '<li>Upload the clearly visible scanned copy of the fee slip.</li>';
echo '<li>Student are required to pay Rs. 100.00 for the request '
. '<ul><li><b>Regular Students:</b> Fee may be deposited in SEECS Acct Office through deposit slip.</li>'
        . '<li><b>For Passed Out/Left Over Students:</b>Fee may be deposited in Student Scholarship and Misc charges account (HBL) No 229227000746301).</li></ul></li>';
echo '<li>You can also upload any supporting documents if required.</li>';
echo '<li>Normally two working days are required for issuance of the certificate and student will be informed through email when certificate is ready for collection.</li>';
echo '<li>The student is required to submit original deposit slip in Exams Branch SEECS at the time of collection of the certificate.</li>';
echo '</ul>';
echo '</br>';
echo '<div style="text-align:center;">';
echo "<form name=\"agreement_form\" action=\"certificates/certificates.php\">";  
 echo "<input type=\"checkbox\" name=\"agreed\" onchange=\"enable()\"><b>I am ready to proceed.</b>";  
 echo "<br>";  
 echo "<input type=\"submit\" value=\"Submit\" disabled name=\"submit_button\">"; 
 echo "</div>";
 }
 
  else if($req == 'nustid'){
echo '<h2 style = "text-align:center;">ISSUANCE OF NUST ID CARD</h2>';
 echo '<ul>';
 echo '<li>Select the applicable reason for issuance of card.</li>';
 echo '<li><b>In case of Lost ID Card (Permanent/Temporary):</b></li>';
 echo '<ul>';
 echo '<li>Print the duly filled form, get signed from the respective class Advisor and parents /guardian and submit at the SEECS exam branch along with the original deposit slip.</li>';
 echo '<li>Upload the clearly visible scanned copy of fee slip.</li>';
 echo '<li>The student will sign the warning letter for his/her negligence appended at the end of proforma before submission to Exams Branch SEECS.</li>';
 echo '<li>In case of loss of permanent ID card, the student is required to deposit prescribed fee i.e. Rs. 500/- in NUST tuition fee account No. 22927000267401.</li>';
 echo '<li>In case of loss of temporary ID card, the student is required to deposit prescribed fee i.e. Rs. 200/- in Accts Branch SEECS through deposit slip.</li>';
 echo '</ul>';
 echo '<li><b>In case of Damaged/Faded Out ID Card (Permanent/Temporary):</b></li>';
 echo '<ul>';
 echo '<li>Students are required to deposit old original ID card in Exams Branch SEECS along with the proforma.</li>';
 echo '<li>In this case no fee is required.</li>';
echo '</ul>'; 
echo '<li>The request will be forwarded to Main Office NUST and student will be informed through email when card is ready for collection.</li>';
echo '<li><font color=red><b>It is to be noted that case will be forwarded to Main Office NUST after receipt of original deposit slip / old ID Card.</b></font></li>';
echo '</ul>';
echo '<br>';

echo '<div style="text-align:center;">';
echo "<form name=\"agreement_form\" action=\"nustid/nustid.php\">";  
 echo "<input type=\"checkbox\" name=\"agreed\" onchange=\"enable()\"><b>I am ready to proceed.</b>";  
 echo "<br>";  
 echo "<input type=\"submit\" value=\"Submit\" disabled name=\"submit_button\">"; 
 echo "</div>";
 }
 
  else if($req == 'degree'){
echo '<h2 style="text-align:center;">DEGREE</h2>';
 echo '<ul>';
echo '<li>Fill in the form carefully</li>';
echo '<li>Enter the exact receipt No and deposit date as per the fee slip.</li>';
echo '<li>Select the reason for issuance of degree and upload clearly visible scanned copy of evidence if required.</li>';
echo '<li>Submit original fee slip along with duly signed proforma to Exam Branch SEECS.</li>';
echo '<li>The request will be forwarded to Main Office NUST and student will be informed through email when degree is ready for collection.</li>';
echo '<li><font color = red><b>It is to be noted that case will be forwarded to Main Office NUST after receipt of original deposit slip.</b></font></li>';
echo '</ul>';
echo '</br>';
echo '<div style="text-align:center;">';
echo "<form name=\"agreement_form\" action=\"degree/degree.php\">";  
 echo "<input type=\"checkbox\" name=\"agreed\" onchange=\"enable()\"><b>I am ready to proceed.</b>";  
 echo "<br>";  
 echo "<input type=\"submit\" value=\"Submit\" disabled name=\"submit_button\">"; 
 echo "</div>";
 }
 
  else if($req == 'transcript'){
echo '<h2 style="text-align:center;">TRANSCRIPT</h2>';
echo '</br>';
 echo '<ul>';
 echo '<li>Fill in the form carefully strictly according to the instruction given there.</li>';
echo '<li>Select required type of processing i.e. urgent or normal.</li>';
echo '<li>The time frame for issuance of transcript will commence on receipt of application in Exam Branch HQ NUST.</li>';
echo '<li>If you are an On campus student or have completed degree requirements within last 60 days, submit application here. Otherwise approach directly to Student Affairs Directorate NUST, Islamabad.</li>';
echo '<li>Insert the delivery information carefully in case of authorizing a person. Upload the clearly visible scanned copy of CNIC and authorization letter.</li>';
echo '<li>Upload the clearly visible scanned copy of the deposit slip and accounts clearance certificate.</li>';
echo '<li>The request will be forwarded to Main Office NUST and student will be informed through email when transcript is ready for collection.</li>';
echo '<li>In case you opt to receive transcript by hand please ensure receipt within 30 days of application.</li>';
echo '<li>On receipt of transcript, indicate errors if any.</li>';
echo '<li><font color = red><b>It is to be noted that case will be forwarded to Main Office NUST after receipt of original deposit slip.</b></font></li>';

echo '</ul>';
echo '</br>';
echo '<div style="text-align:center;">';
echo "<form name=\"agreement_form\" action=\"transcript/transcript.php\">";  
echo "<input type=\"checkbox\" name=\"agreed\" onchange=\"enable()\"><b>I am ready to proceed.</b>";  
echo "<br>";  
echo "<input type=\"submit\" value=\"Submit\" disabled name=\"submit_button\">";
echo "</div>";
 }
 
 echo '</div>';
 echo "<a href='requestslist.php'><img src='icons/dashboard_icon.jpg' alt='' border=3 height=30 width=30></img>Back to Exam Branch Dashboard</a>";

echo $OUTPUT->box_end(); 
echo $OUTPUT->footer();
 