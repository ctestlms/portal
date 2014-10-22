<?php 
date_default_timezone_set('Asia/Karachi');
require('../lib/fpdi/fpdi.php');  
require_once('../../../config.php');
$reqid = $_GET['reqid'];
	$sql = "SELECT r.* FROM {requests} r WHERE r.id = $reqid";
                // JOIN {request_info_data} rid ON r.id = rid.requestid WHERE r.id = $reqid
	global $DB,$USER;
        $row = $DB->get_record_sql($sql); 
        global $DB;
        //  $user=$DB->get_record_sql("SELECT * FROM {user} WHERE id=$USER->id");
        $rowphone = $DB->get_record_sql("SELECT phone2 FROM {user} WHERE id=$USER->id");
        
     if($row->request_typeid == 1){   
         $sql_cert1 = "SELECT * FROM {request_info_data} WHERE requestid = $reqid AND fieldid = 2";
        $row_cert1 = $DB->get_record_sql($sql_cert1); 
$pdf = new FPDI();

$pdf->AddPage(); 

$pdf->setSourceFile('certificates/cert_prov.pdf'); 
// import page 1 
$tplIdx = $pdf->importPage(1); 
//use the imported page and place it at point 0,0; calculate width and height
//automaticallay and ajust the page size to the size of the imported page 
$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 

// now write some text above the imported page 
$pdf->SetFont('Arial', '', '13'); 
$pdf->SetTextColor(0,0,0);
//set position in pdf document
$pdf->SetXY(75, 92);
//first parameter defines the line height
$pdf->Write(0, $USER->firstname.' '.$USER->lastname);

$pdf->SetXY(75, 102);
//first parameter defines the line height
$pdf->Write(0, $USER->user_subgroup);

$pdf->SetXY(75, 112);
//first parameter defines the line height
$pdf->Write(0, $USER->idnumber);

$first = substr($row_cert1->data, 0, 31);

$theRest = substr($row_cert1->data, 31);

$pdf->SetXY(113, 122);
//first parameter defines the line height
$pdf->Write(0, $first);


$pdf->SetXY(35, 124.5);
//first parameter defines the line height
$pdf->MultiCell( 150, 10,$theRest , 0);

$pdf->SetXY(70, 174.5);
$pdf->Write(0,$row->amount_deposited);

$pdf->SetXY(128, 174.5);
$pdf->Write(0,$row->receipt_no);

$depositdate = date('d/m/Y', $row->deposit_date);
$pdf->SetXY(175, 174.5);
$pdf->Write(0,$depositdate);


$currentdate = date('d/m/Y');
$pdf->SetXY(50, 210);
$pdf->Write(0,$currentdate);

$pdf->SetXY(150, 210);
$pdf->Write(0,$rowphone->phone2);
//$pdf->Write(0, );
//force the browser to download the output
$pdf->Output('proforma_certificate_'.$reqid.'.pdf', 'D');
     }
     elseif ($row->request_typeid == 2) {
           $sql_id = "SELECT * FROM {request_info_data} WHERE requestid = $reqid AND fieldid = 3";
        $row_id = $DB->get_record_sql($sql_id);
         if(substr_count($USER->user_subgroup,"-") == 2){
 
        $lms_subgroup = substr($USER->user_subgroup, 0, -2);
  }
 else{
   $lms_subgroup = $USER->user_subgroup;}
        $sql_info = "SELECT name FROM {classes} 
             WHERE alias = \"$lms_subgroup\"";
        $row_info = $DB->get_record_sql($sql_info);
     $pdf = new FPDI();

$pdf->AddPage(); 

$pdf->setSourceFile('nustid/issuance_nustid.pdf'); 
// import page 1 
$tplIdx = $pdf->importPage(1); 
//use the imported page and place it at point 0,0; calculate width and height
//automaticallay and ajust the page size to the size of the imported page 
$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 

// now write some text above the imported page 
$pdf->SetFont('Arial', '', '10'); 
$pdf->SetTextColor(0,0,0);
//set position in pdf document
$pdf->SetXY(55, 53.5);
//first parameter defines the line height
$pdf->Write(0, $USER->firstname.' '.$USER->lastname);

$pdf->SetXY(140, 53.5);
//first parameter defines the line height
$pdf->Write(0, $USER->fathername);

$pdf->SetXY(120, 63.5);
//first parameter defines the line height
$pdf->Write(0, $row_info->name);

$sec=substr($USER->user_subgroup,-1);
$section = strtoupper($sec);
$pdf->SetXY(180, 63.5);
//first parameter defines the line height
$pdf->Write(0, $section);

$pdf->SetXY(35, 63.5);
//first parameter defines the line height
$pdf->Write(0, $USER->idnumber);


$pdf->SetXY(90, 73.5);
//first parameter defines the line height
$pdf->Write(0, $row_id->data);


$currentdate = date('m/d/Y');
$pdf->SetXY(30, 116.5);
$pdf->Write(0,$currentdate);

//$pdf->Write(0, );
//force the browser to download the output
$pdf->Output('Proforma_NustID_'.$reqid.'.pdf', 'D');
//$pdf->Output('/var/www/html/performas/Performa_NustID_'.$USER->id.'.pdf', 'F');

}
   elseif ($row->request_typeid == 3) {
           $sql_degree = "SELECT * FROM {request_info_data} WHERE requestid = $reqid AND fieldid = 4";
        $row_degree = $DB->get_record_sql($sql_degree);
        if(substr_count($USER->user_subgroup,"-") == 2){
 
        $lms_subgroup = substr($USER->user_subgroup, 0, -2);
  }
 else{
   $lms_subgroup = $USER->user_subgroup;}
       
         $sql_info = "SELECT c.enddate,d.shortname FROM {classes} c
             JOIN {degrees} d ON d.id=c.degreeid
             WHERE c.alias = \"$lms_subgroup\"";
        $row_info = $DB->get_record_sql($sql_info);
     $pdf = new FPDI();

$pdf->AddPage(); 

$pdf->setSourceFile('degree/proforma_degree_bachelors.pdf'); 
// import page 1 
$tplIdx = $pdf->importPage(1); 
//use the imported page and place it at point 0,0; calculate width and height
//automaticallay and ajust the page size to the size of the imported page 
$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 

// now write some text above the imported page 
$pdf->SetFont('Arial', '', '11'); 
$pdf->SetTextColor(0,0,0);
//set position in pdf document
//$pdf->SetXY(75, 53.5);
//first parameter defines the line height

$pdf->SetXY(41, 66);
//first parameter defines the line height
$pdf->Write(0, $USER->firstname.' '.$USER->lastname);
//$pdf->Write(0, $USER->idnumber);
$pdf->SetFont('Arial', '', '9'); 
$pdf->SetXY(145, 66);
$pdf->Write(0, $USER->idnumber);

$pdf->SetFont('Arial', '', '11'); 
$pdf->SetXY(54, 74);
//first parameter defines the line height
$pdf->Write(0, $row_info->shortname);

$pdf->SetXY(145, 74);
//first parameter defines the line height
$pdf->Write(0, 'SEECS');

if($row_degree->data == 'For Job'){
 $pdf->Rect(90.3, 90, 6.8,4.8, 'DF');
    
 $sql = "SELECT * FROM {requests_files} WHERE req_id=$reqid AND name LIKE \"%evidencedoc_degree_$reqid%\"";
if($DB->get_record_sql($sql)){
    //$pdf->SetXY(191, 92.5); 
 $pdf->Rect(190, 90, 6.8,4.8, 'DF');
  }
}

else if($row_degree->data == 'For Higher Studies'){
 $pdf->Rect(90.3, 101.5, 6.8,4.8, 'DF');
 $sql = "SELECT * FROM {requests_files} WHERE req_id=$reqid AND name LIKE \"%evidencedoc_degree_$reqid%\"";
  if($DB->get_record_sql($sql)){
     $pdf->Rect(190, 101.5, 6.8,4.8, 'DF');
  }
}
else{
 $pdf->Rect(90.3, 113.5, 6.8,4.8, 'DF');
 $sql = "SELECT * FROM {requests_files} WHERE req_id=$reqid AND name LIKE \"%evidencedoc_degree_$reqid%\"";
if($DB->get_record_sql($sql)){
      $pdf->Rect(190, 113.5, 6.8,4.8, 'DF');
  }
}
 

$enddate = date('d/m/Y',$row_info->enddate);
$pdf->SetXY(82, 130.5);
$pdf->Write(0,$enddate);


   $sql = "SELECT * FROM {requests_files} WHERE req_id=$reqid AND name LIKE \"%clearancedoc_degree_$reqid%\"";
if($DB->get_record_sql($sql)){
  $pdf->Rect(190, 139, 6.8,4.8, 'DF');
     
 }
 
$pdf->SetXY(67, 152.5);
$pdf->Write(0,$row->receipt_no);

$depositdate = date('d/m/Y', $row->deposit_date);
$pdf->SetXY(105, 152.5);
$pdf->Write(0,$depositdate);

//$pdf->SetXY(191, 152.6);
$pdf->Rect(190, 149.5, 6.8,4.8, 'DF');
//$pdf->Write(0,'X'); 

$pdf->SetXY(132, 174.5);
$pdf->Write(0,date('m/d/Y')); 

//$pdf->Write(0, );
//force the browser to download the output
$pdf->Output('Proforma_degree_'.$reqid.'.pdf', 'D');
//$pdf->Output('/var/www/html/performas/Performa_NustID_'.$USER->id.'.pdf', 'F');

}
elseif ($row->request_typeid == 4) {
           $sql_t = "SELECT fieldid,data FROM {request_info_data} WHERE requestid = $reqid";
        $row_t = $DB->get_records_sql($sql_t);
        $sql_1 = "SELECT nicno FROM {regform} WHERE userid=$USER->id";
        $row_info1 = $DB->get_record_sql($sql_1);
         if(substr_count($USER->user_subgroup,"-") == 2){
 
        $lms_subgroup = substr($USER->user_subgroup, 0, -2);
  }
 else{
   $lms_subgroup = $USER->user_subgroup;}
         $sql_info = "SELECT c.enddate,d.shortname,c.name FROM {classes} c
             JOIN {degrees} d ON d.id=c.degreeid
             WHERE alias = \"$lms_subgroup\"";
        $row_info = $DB->get_record_sql($sql_info);
//print_object($row_degree[5]);

$pdf = new FPDI('P','mm',array(215.9,355.6));

$pdf->AddPage(); 

$pdf->setSourceFile('transcript/proforma_transcript.pdf'); 
// import page 1 
$tplIdx = $pdf->importPage(1); 
//use the imported page and place it at point 0,0; calculate width and height
//automaticallay and ajust the page size to the size of the imported page 
$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 
 

// now write some text above the imported page 
$pdf->SetFont('Arial', '', '21'); 
$pdf->SetTextColor(0,0,0);
//set position in pdf document
//$pdf->SetXY(75, 53.5);
//first parameter defines the line height


if($row_t[5]->data == 'Urgent'){

$pdf->Rect(147, 42.2, 25.2,7.8, 'DF'); 
}
else{
$pdf->Rect(175.4, 42.2, 25.2,7.8, 'DF');
}


$pdf->SetFont('Arial', '', '10'); 
$pdf->SetXY(85, 69);
//first parameter defines the line height
$pdf->Write(0, $USER->firstname.' '.$USER->lastname);


$pdf->SetXY(85, 75);
//first parameter defines the line height
$pdf->Write(0, $USER->idnumber);

$pdf->SetXY(85, 91);
//first parameter defines the line height
$pdf->Write(0, 'SEECS');

$pdf->SetXY(85, 97.5);
$pdf->Write(0, $row_info->name);

$pdf->SetXY(85, 103.5);
$pdf->Write(0, $row_t[6]->data);

$pdf->SetXY(85, 109);
$pdf->Write(0,$row->receipt_no);

$depositdate = date('d/m/Y', $row->deposit_date);
$pdf->SetXY(138, 109);
$pdf->Write(0,$depositdate);

$pdf->SetXY(43, 115.5);
$pdf->Write(0,$row->amount_deposited);
//Changes/Correction Bio-data
$pdf->SetFont('Arial', '', '15'); 
if($row_t[7]->data == 1){
   $first = substr($row_t[8]->data, 0, 48);

$theRest = substr($row_t[7]->data, 48);
 $pdf->Rect(136.8, 124.7, 6.2,4.4, 'DF');
$pdf->SetFont('Arial', '', '10');
 $pdf->SetXY(56, 133.5);
$pdf->Write(0, $first);
 $pdf->SetXY(25, 139.8);
$pdf->Write(0, $theRest);
}
else{
$pdf->Rect(146.6, 125.1, 6.2,4.4, 'DF');
}
//Been issued before or not
$pdf->SetFont('Arial', '', '15'); 
if($row_t[9]->data == 1){
    $pdf->Rect(136.2, 141.7, 6.2,4.4, 'DF');
}
else{
    $pdf->Rect(145.8, 141.7, 6.2,4.4, 'DF');
}
///////////////////////////////////////Sealed Envelope////////////////////////////////////////////////////////////
$pdf->SetFont('Arial', '', '12'); 
if($row_t[15]->data == 1){
    $pdf->Line(90.5, 155, 94, 155);
$pdf->SetFont('Arial', '', '10');
 $pdf->SetXY(49, 157);
$pdf->Write(0, $row_t[15]->data);
}
else{
   
 $pdf->Line(95, 155, 97.5, 155);}
 //print_object($row_t);
//////////////////////////////////////MODE OF DELIVERY//////////////////////////////////////////////////////////////
if($row_t[10]->data == 'By Hand'){
    ////////IF AUTHORIZE
    if($row_t[11]->data == 1){
        //CHECK
 $pdf->SetFont('Arial', 'b', '10');
 $pdf->Line(97.5, 193, 123.5, 193);

/////Authorized's name
 $pdf->SetFont('Arial', '', '10');
 $pdf->SetXY(100, 195.8);
 $pdf->Write(0, $row_t[12]->data);
/////Authorized's CNIC
$first1 = substr($row_t[13]->data, 0, 5);
$theRest1 = substr($row_t[13]->data, 5,3);
$theRest2 = substr($row_t[13]->data, 8,4);
$theRest3 = substr($row_t[13]->data, 12);
$pdf->SetFont('Arial', '', '10');
 $pdf->SetXY(105.5, 203);
$pdf->CellFitSpaceForce(0, 0, $first1.'-', 0, 0, '', 0);
$pdf->SetXY(140.5, 203);
$pdf->CellFitSpaceForce(0, 0, $theRest1, 0, 0, '', 0);
$pdf->SetXY(158.5, 203);
$pdf->CellFitSpaceForce(0, 0, $theRest2.'-', 0, 0, '', 0);
$pdf->SetXY(188.5, 203);
$pdf->CellFitSpaceForce(0, 0, $theRest3, 0, 0, '', 0);

//Auth's Phone
$pdf->SetFont('Arial', '', '10');
 $pdf->SetXY(112, 212);
$pdf->Write(0, $row_t[14]->data);

    }
    else {
        //CHECK
        $pdf->SetFont('Arial', 'b', '10');
        $pdf->Line(87, 193, 93, 193);
    //SELF CNIC
$pdf->SetFont('Arial', '', '10');
$first1 = substr($row_info1->nicno, 0, 5);
$theRest1 = substr($row_info1->nicno, 5,3);
$theRest2 = substr($row_info1->nicno, 8,4);
$theRest3 = substr($row_info1->nicno, 12);
$pdf->SetFont('Arial', '', '10');
 $pdf->SetXY(105.5, 203);
$pdf->CellFitSpaceForce(0, 0, $first1.'-', 0, 0, '', 0);
$pdf->SetXY(140.5, 203);
$pdf->CellFitSpaceForce(0, 0, $theRest1, 0, 0, '', 0);
$pdf->SetXY(158.5, 203);
$pdf->CellFitSpaceForce(0, 0, $theRest2.'-', 0, 0, '', 0);
$pdf->SetXY(188.5, 203);
$pdf->CellFitSpaceForce(0, 0, $theRest3, 0, 0, '', 0);
   //SELF PHONE
$pdf->SetXY(112, 212);
$pdf->Write(0, $USER->phone2);      
    }
    $pdf->Line(95, 223, 99, 223);
    
   }
    else{
       //CHECK
        $pdf->SetFont('Arial', '', '12');
         $pdf->Line(87, 223, 92, 223);
$pdf->SetFont('Arial', '', '10');
/////Mailing Address
 $first = substr($USER->address, 0, 48);

$theRest = substr($USER->address, 48);
 $pdf->SetXY(115, 228.5);
$pdf->Write(0, $first);
 $pdf->SetXY(89, 234);
$pdf->Write(0, $theRest);
/////phone
 $pdf->SetXY(104, 239.9);
$pdf->Write(0, $USER->phone2);
///////////////////////////////////////////////////////////////////////////// nicno /////////////////////////////////
$first1 = substr($row_info1->nicno, 0, 5);
$theRest1 = substr($row_info1->nicno, 5,3);
$theRest2 = substr($row_info1->nicno, 8,4);
$theRest3 = substr($row_info1->nicno, 12);
$pdf->SetFont('Arial', '', '10');
 $pdf->SetXY(105.5, 250);
$pdf->CellFitSpaceForce(0, 0, $first1.'-', 0, 0, '', 0);
$pdf->SetXY(140.5, 250);
$pdf->CellFitSpaceForce(0, 0, $theRest1, 0, 0, '', 0);
$pdf->SetXY(158.5, 250);
$pdf->CellFitSpaceForce(0, 0, $theRest2.'-', 0, 0, '', 0);
$pdf->SetXY(188.5, 250);
$pdf->CellFitSpaceForce(0, 0, $theRest3, 0, 0, '', 0); 
// email
 $pdf->SetXY(104, 256);
$pdf->Write(0, $USER->email); 
    }
   $pdf->SetXY(42, 269);
$pdf->Write(0,date('m/d/Y')); 
 $pdf->AddPage();
 $tplIdx1 = $pdf->importPage(2); 
//use the imported page and place it at point 0,0; calculate width and height
//automaticallay and ajust the page size to the size of the imported page 
$pdf->useTemplate($tplIdx1, 0, 0, 0, 0, true);  
//clearance
$pdf->SetFont('Arial', 'b', '14');
if($row_t[17]->data == 1){
 $pdf->Rect(180.4, 37, 6.2,4.4, 'DF');
}
else{
 $pdf->Rect(190, 37, 6.2,4.4, 'DF'); 
}
    //feeslip
$pdf->Rect(180.2, 46.2, 6.2,4.4, 'DF');

//urgent fee
if($row_t[5]->data == 'Urgent')
{
    $pdf->Rect(180.2, 53, 6.2,4.4, 'DF');
}
else{ 
 $pdf->Rect(189.8, 53, 6.2,4.4, 'DF');
}
 

$pdf->Output('Proforma_transcript_'.$reqid.'.pdf', 'D');
 
}