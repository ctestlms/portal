<?php
require_once('../../config.php');
require_once('profile_form.php');
require('../../mod/attforblock/tcpdf/config/lang/eng.php');
require('../../mod/attforblock/tcpdf/tcpdf.php');

require_login();
global  $OUTPUT,$USER, $PAGE, $DB;

$PAGE->set_context(context_system::instance());
$url = new moodle_url('/blocks/student_resource_center/profile_pdf.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_heading(get_string('profile_header', 'block_student_resource_center'));
$PAGE->navbar->add(get_string('registration_form', 'block_student_resource_center'),$url);
$PAGE->set_title(get_string('profile_header', 'block_student_resource_center'));



  //---EXPORT TO PDF------------
    function ExportToPDF() {
   $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
global $USER,$CFG,$DB;
$sql = "SELECT * FROM {regform} WHERE userid=$USER->id";
$fromform = $DB->get_record_sql($sql);
   if($USER->user_group != 'PG')
    {
           
        $fromform->thirddegreename = "NA";
        $fromform->thirdpassingyear = "NA";
        $fromform->thirdtotalcgpa = "NA";
        $fromform->thirdcgpaobtained = "NA";
        $fromform->thirdcgpapercentage = "NA";
        $fromform->thirdtotalcgpa = "NA";
        $fromform->thirduniversity = "NA";
        $fromform->thirdmajorsubjects = "NA";
        
        $fromform->fourdegreename = "NA";
        $fromform->fourpassingyear = "NA";
        $fromform->fourtotalcgpa = "NA";
        $fromform->fourcgpaobtained = "NA";
        $fromform->fourcgpapercentage = "NA";
        $fromform->fourtotalcgpa = "NA";
        $fromform->fouruniversity = "NA";
        $fromform->fourmajorsubjects = "NA";
      
    }
    if($fromform->foreignstudent != 1)
   {
       $fromform->origncountry = "NA";
       $fromform->passportno = "NA";
   }
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('MOODLE NUST');
//$pdf->SetTitle('Registration Form');
//$pdf->SetSubject('Registration Form');
//$pdf->SetKeywords('Registration Form');
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
// add a page
$pdf->AddPage();


// set color for background
$pdf->SetFillColor(255, 255, 255);
$pdf->Image('NUST_Logo.jpg', 20, '', 20, 20, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
$pdf->SetFont('helvetica', 'B', 13);

$pdf->MultiCell(125, 2, 'National University of Sciences & Technology',  0, 'C', 1, 1, 42, 10, false);
$pdf->MultiCell(148, 2, 'School of Electrical Engineering & Computer Sciences', 0, 'L', 1, 1, 42, 15, false);
$pdf->MultiCell(120, 2, 'Registration Form - UG/PG Program ('.$fromform->subgroup.')', 0, 'C', 0, 1, 42, 20, false);
//$pdf->Ln(8);
$pdf->SetFont('helvetica', '', 10);

//if($USER->user_group != 'PGCourses'){
// Multicell test
$pdf->MultiCell(80, 10, '1. Registration No.:  '.$USER->idnumber, 0, 'L', 1, 0, '', '', true, 0, false, true, 'C', 'T');

//print_object($fromform);
$pdf->MultiCell(55, 10, '2. Merit No.:  '.$fromform->meritno, 0, 'L', 1, 0, '', '', true, 0, false, true, 40, false);


$pdf->MultiCell(45, 50, 'Affix Passport Size Photograph here',1, 'C', 1, 0, '', '', true, 0, false, true, 40, 'C');

$pdf->Ln(10);

$pdf->MultiCell(35, 10, "3. Type:  ".$fromform->admissiontype, 0, 'L', 1, 0, '', '', true, 0, false, true, 40, false);
$pdf->MultiCell(45, 10, '4. Roll No.:  '.$fromform->rollno, 0, 'L', 1, 0, '', '', true, 0, false, true, 40, false);
$dateofbirth = date('m/d/Y', $fromform->dob);
$pdf->MultiCell(50, 10, '5. DOB:  '.$dateofbirth,0, 'L', 1, 0, '', '', true, 0, false, true, 40, false);
$pdf->Ln(10);

$pdf->MultiCell(130, 10, '6. CNIC No.(or Registration No. of Form B):  '.$fromform->nicno,0, 'L', 1, 0, '', '', true, 0, false, true, 40, false);

//$pdf->MultiCell(67.5, 10, '6. Gender: '.$fromform->gender,0, 'L', 1, 0, '', '', true, 0, false, true, 40, false);
$pdf->Ln(10);
//$pdf->Ln();
$pdf->Cell(80, 10, '7. Name:  '.$USER->firstname.' '.$USER->lastname,0,0,'L');
$pdf->Cell(55, 10, '8. Gender:  '.$fromform->gender,0,1,'L');
//=--
$pdf->Cell(135, 10, '9. Father`s Name:  '.$USER->fathername,0,1,'L');

//---Father Profession
//$pdf->Cell(50, 10, '9. Father`s Profession: '.$fromform->fatherprofession,0,0,'L');
$pdf->Cell(65, 10, '10. Profession:  '.$fromform->fatherprofession,0,0,'L');
$pdf->Cell(75, 10, '(If Army/Navy/PAF/State Rank:  '.$fromform->fatherrank,0,0,'L');
$pdf->Cell(40, 10, 'Serving/Retired:  '.$fromform->fatherservice.')',0,1,'L');
//--Domicile & Province
$pdf->Cell(80, 10, '11. Domicile District:  '.$fromform->domicile,0,0,'L');
$pdf->Cell(100, 10, '12. Province: '.$fromform->province,0,1,'L');
//---Foreign

  
   
$pdf->Cell(40, 10, '13. Foreign Student/Only:    ',0,0,'L');
$pdf->Cell(70, 10, 'a. Orign Country:  '.$fromform->origncountry,0,0,'L');
$pdf->Cell(70, 10, 'b. Passport No.: '.$fromform->passportno,0,1,'L');
   
//--Current Address
$pdf->Cell(180, 10, '14. Current Postal Address:  '.$USER->address,0,1,'L');
$pdf->Cell(80, 10, 'a) Phone no.:  '.$fromform->currphoneno,0,0,'L');
$pdf->Cell(100, 10, 'c) Student`s Mobile no.:  '.$USER->phone2,0,1,'L');
//---Permanent Address
$pdf->Cell(180, 10, '15. Permanent Address:  '.$fromform->permanentaddress.", ".$fromform->permanentcity,0,1,'L');
$pdf->Cell(80, 10, 'a) Phone no.:  '.$fromform->perphoneno,0,0,'L');
$pdf->Cell(100, 10, 'c) Father/Guardian`s Mobile no.:  '.$fromform->fathermobileno,0,1,'L');
//--Email & Religion
$pdf->Cell(80, 10, '16. Email:  '.$USER->email,0,0,'L');
$pdf->Cell(50, 10, '17. Religion:  '.$fromform->religion,0,0,'L');
$pdf->Cell(50, 10, '18. Fax No.:  '.$fromform->faxno,0,1,'L');
//--Blood Group & Emergency Contact No.
$pdf->Cell(80, 10, '19. Blood Group:  '.$fromform->bloodgroup,0,0,'L');
$pdf->Cell(100, 10, '20. Parent/Guardian`s No.(emergency):  '.$fromform->emergencyno,0,1,'L');

//----Academic Details
$pdf->Cell(180, 10, '21. Academic Details:  ',0,1,'L');
$pdf->SetFont('helvetica','B', 9);
//$pdf->setCellMargins('', '1', '', '1');
$pdf->MultiCell(35, 12, 'Degree Name', 1, 'C', 1, 0, '', '', true, 0, false, true, 'C', 'T');
$pdf->MultiCell(20, 12, 'Passing Year', 1, 'C', 1, 0, '', '', true, 0, false, true, 'C', 'T');
$pdf->MultiCell(20, 12, 'Tot. Mrks/CGPA', 1, 'C', 1, 0, '', '', true, 0, false, true, 'C', 'T');
$pdf->MultiCell(20, 12, 'Mrks/CGPA Obt.', 1, 'C', 1, 0, '', '', true, 0, false, true, 'C', 'T');

$pdf->MultiCell(20, 12, '%Age', 1, 'C', 1, 0, '', '', true, 0, false, true, 'C', 'T');
$pdf->MultiCell(25, 12, 'Board/ Univ', 1, 'C', 1, 0, '', '', true, 0, false, true, 'C', 'T');
$pdf->MultiCell(35, 12, 'Major Subjects', 1, 'C', 1, 0, '', '', true, 0, false, true, 'C', 'T');
 $pdf->Ln(12);
$pdf->SetFont('helvetica','', 10);
//-- firs
//t
  
$pdf->Cell(35, 5,$fromform->firstdegreename ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->firstpassingyear ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->firsttotalmarks ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->firstmarksobtained ,'LTB',0,'C');
$pdf->Cell(20, 5, $fromform->firstpercentage,'LRB',0,'C');
$pdf->Cell(25, 5, $fromform->firstboard,'TRB',0,'C');
$pdf->Cell(35, 5, $fromform->firstmajorsubjects,'TRB',1,'C');
//-- Second
$pdf->Cell(35, 5,$fromform->seconddegreename ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->secondpassingyear ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->secondtotalmarks ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->secondmarksobtained ,'LTB',0,'C');
$pdf->Cell(20, 5, $fromform->secondpercentage,'LRB',0,'C');
$pdf->Cell(25, 5, $fromform->secondboard,'TRB',0,'C');
$pdf->Cell(35, 5, $fromform->secondmajorsubjects,'TRB',1,'C');
//---- 
 
    
$pdf->Cell(35, 5,$fromform->thirddegreename ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->thirdpassingyear ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->thirdtotalcgpa ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->thirdcgpaobtained ,'LTB',0,'C');
$pdf->Cell(20, 5, $fromform->thirdcgpapercentage,'LRB',0,'C');
$pdf->Cell(25, 5, $fromform->thirduniversity,'TRB',0,'C');
$pdf->Cell(35, 5, $fromform->thirdmajorsubjects,'TRB',1,'C');
//---- Fourth
$pdf->Cell(35, 5,$fromform->fourdegreename ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->fourpassingyear ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->fourtotalcgpa ,'LTB',0,'C');
$pdf->Cell(20, 5,$fromform->fourcgpaobtained ,'LTB',0,'C');
$pdf->Cell(20, 5, $fromform->fourcgpapercentage,'LRB',0,'C');
$pdf->Cell(25, 5, $fromform->fouruniversity,'TRB',0,'C');
$pdf->Cell(35, 5, $fromform->fourmajorsubjects,'TRB',1,'C');
    
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(180,10,'DECLARATION',0,1,'C');


$pdf->SetFont('helvetica','', 10);
$html = '<ol>
    <li>I certify that I have read and understood the contents of prospectus and certify that all the answers I have given are 
complete and accurate to the best of my knowledge and belief. If admitted, I agree to observe all the rules and 
regulations of the National University of Sciences and Technology as applicable after the date of admission. Failure 
to comply can result to my expulsion from the University.<br/></li>
<li>I certify to bear all expenses of the program of study.</li></ol>';
 


$pdf->writeHTMLCell(180,40,10,215,$html, 0, 1, false, true, '', true);
    
      $pdf->Cell(90,2,'__________________________',0,0,'C');
      $pdf->Cell(90,2,'____________________________',0,1,'C');
      $pdf->Cell(90,5,'Signature of the Applicant',0,0,'C');
      $pdf->Cell(90,5,'Signature of Parent/Guardian',0,1,'C');


 // $pdf->ln();   
      $pdf->SetFont('helvetica','B', 10);
$pdf->Cell(40,12,'(In case of guardian)',0,0,'C');
$pdf->SetFont('helvetica','', 10);
       $pdf->Cell(70,12,'Guardian Name:  '.$fromform->guardianname,0,0,'L');
      $pdf->Cell(70,12,'Relationship:  '.$fromform->guardianrelation,0,0,'L');
      
//ob_end_clean();
//Close and output PDF document
$pdf->Output('registration_form.pdf', 'D');
  
//redirect("$CFG->wwwroot/my/");
exit();
    }
    
 //   print_object($USER);
$profile = new profile_form();

if ($fromform=$profile->get_data()) {
  $fromform->nicno = $fromform->nicno1.'-'.$fromform->nicno2.'-'.$fromform->nicno3;
  unset($fromform->nicno1);
  unset($fromform->nicno2);
  unset($fromform->nicno3);
  
   /// When Clicks on Save button
   if($fromform->submitbutton == "Save Profile"){
       
        if(!$DB->record_exists('regform', array('userid'=>$USER->id)) )
       {
           $DB->insert_record('regform', $fromform, false);
           
       }
       else
       {
           $DB->update_record('regform', $fromform, false);
       }
        
   redirect("$CFG->wwwroot/blocks/student_resource_center/profile_pdf.php");
   }

   //When Clicks on Print&Next
  else if($fromform->submitbutton == "Print Form"){
     
  ExportToPDF();

       
      
  }
    else if ($fromform->submitbutton == "Submit")
   {
          
 
      $fromform->freeze_edit = 1;
      $DB->update_record('regform', $fromform, false);
         
       
   redirect("$CFG->wwwroot/blocks/student_resource_center/profile_pdf.php");
// ExportToPDF();
  //redirect("$CFG->wwwroot/my/");
   
  
  
   
   }
 
}
else { 
    
    
     if($DB->record_exists('regform', array('userid'=>$USER->id)) ) 
    {
         
       $user = $DB->get_record('regform', array('userid'=>$USER->id)); 
    // $user->nicno1 = substr($user->nicno,0,5) ;
    // $user->nicno2 = substr($user->nicno,6,7) ;
    // $user->nicno3 = substr($user->nicno,14,1) ;
     unset($user->nicno);
       $profile->set_data($user);
    }
     
    
echo $OUTPUT->header();

$profile->display();
echo $OUTPUT->footer();
}
