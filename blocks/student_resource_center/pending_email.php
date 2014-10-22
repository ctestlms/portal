<?php

require_once('../../config.php');

class email_pending_user{
    
    
    
    
}
  function email_ug_users()       { global $DB;
$to = new StdClass();
$from = new StdClass();
$sql = "SELECT docs.origssc_eq as 'Original SSC / Equivalent Certificate' ,docs.orighssc_eq as 'Original HSSC / Equivalent Certificate' , docs.ibcceqv_reqd, docs.ibcceqv as 'IBCC Equivalent Certificate',docs.hsscone_reqd,docs.hsscone as 'HSSC Part One Result',
docs.copyssc_eq as 'Attested Photocopies of SSC / Equivalent Certificate',docs.copyhssc_eq as 'Attested Photocopies of HSSC / Equivalent Certificate',docs.undertaking as 'Under Taking',docs.suritybond as 'Surity Bond',docs.regform as 'Registration Form',docs.copynic as 'Copy of CNIC',docs.medicalcertificate as 'Medical Certificate',docs.photographs as 'Photographs', ";


$sql2 = "u.firstname,u.lastname,u.email FROM {joiningdocs} docs 
        JOIN {user} u ON  u.id=docs.userid
        WHERE u.registrationstatus = 'pending'
        AND docs.user_group = 'UGCourses'";
$result = $DB->get_records_sql($sql.$sql2);

$string = "<html><body><ul>"; 

foreach($result as $r){
   
   $to_firstname = $r->firstname; 
   $to_lastname = $r->lastname; 
   $to_email = $r->email; 
   unset($r->firstname);
   unset($r->lastname);
   unset($r->email);
   print_object($r);
    $d = get_object_vars($r);
    foreach ($d as $key => $value) {
      
 if($value==0 && $key != 'ibcceqv_reqd' && $key != 'ibcc equivalent certificate' && $key != 'HSSC Part One Result' && $key != 'hsscone_reqd' )
 {
    $string .= '<li>'.$key.'</li>';
 }
 else if ($key == 'ibcceqv_reqd' && $value == 1){
     if($key == 'ibcc equivalent certificate' && $value == 0){
         $string .= '<li>'.$key.'</li>';
         break;
     }
     
 }
 
else if ($key == 'hsscone_reqd' && $value == 1){
     if($key == 'HSSC Part One Result' && $value == 0){
         $string .= '<li>'.$key.'</li>';
      break;
         
     }
 }
}
$string .='</ul>';
                $to->firstname = $to_firstname;
                $to->lastname = $to_lastname;
                $to->email = 'ayeshaanasim@gmail.com';
                
                    
                $from->firstname = 'Exam Branch'; 
               $from->lastname = 'NUST'; 
               $from->email = 'ayesha.nasim@seecs.edu.pk';
               $from->maildisplay = true;

                $subject = "Pending Documents";
               $bodyhtml = 'Dear Student,<br/>Please submit the following documents as soon as possible to ensure your joining:
                  '.$string.'<br/> Regards <br/> Exam Branch</body></html>';
               
               $body     = html_to_text($bodyhtml);
               
              // $messagehtml = "Yes";
        if (!$mail_results = email_to_user($to,$from,$subject,$body,$bodyhtml)) {
           die("could not send email!");

}      
        }
  }
      
   
 
      function email_pg_users() {
        global $DB;
$to = new StdClass();
$from = new StdClass();
$sql = "SELECT docs.origssc_eq as 'Original SSC / Equivalent Certificate' ,docs.orighssc_eq as 'Original HSSC / Equivalent Certificate' , docs.ibcceqv_reqd, docs.ibcceqv as 'IBCC Equivalent Certificate',docs.hsscone_reqd,docs.hsscone as 'HSSC Part One Result',
docs.copyssc_eq as 'Attested Photocopies of SSC / Equivalent Certificate',docs.copyhssc_eq as 'Attested Photocopies of HSSC / Equivalent Certificate',docs.undertaking as 'Under Taking',docs.suritybond as 'Surity Bond',docs.regform as 'Registration Form',docs.copynic as 'Copy of CNIC',docs.medicalcertificate as 'Medical Certificate',docs.photographs as 'Photographs', ";

$sql1 = "docs.pg_origbachelors_result as 'Original Bachelors Degree & Transcript', docs.pg_copybachelors_result as 'Attested Copies of Bachelors Degree & Transcript',
    docs.pg_masters_reqd,docs.pg_origmasters_result as 'Original Masters Degree & Transcript', docs.pg_copymasters_result as
    'Attested Copies of Masters Degree & Transcript', docs.pg_cgpacertificatereqd, docs.pg_cgpacertificate as 'CGPA Certicate',";

$sql2 = "u.firstname,u.lastname,u.email FROM {joiningdocs} docs 
        JOIN {user} u ON  u.id=docs.userid
        WHERE u.registrationstatus = 'pending'
        AND docs.user_group = 'PGCourses'";
$result = $DB->get_records_sql($sql.$sql1.$sql2);





foreach($result as $r){
  $string = "<html><body><ul>"; 
   $to_firstname = $r->firstname; 
   $to_lastname = $r->lastname; 
   $to_email = $r->email; 
   unset($r->firstname);
   unset($r->lastname);
   unset($r->email);
   print_object($r);
    $d = get_object_vars($r);
    foreach ($d as $key => $value) {
      
 if($value==0 && $key != 'ibcceqv_reqd' && $key != 'ibcc equivalent certificate' && $key != 'HSSC Part One Result' && $key != 'hsscone_reqd' 
         && $key!= 'pg_masters_reqd' && $key!='Original Masters Degree & Transcript' && $key!='Attested Copies of Masters Degree & Transcript'
         && $key!='pg_cgpacertificatereqd' && $key!='CGPA Certicate' )
 {
    $string .= '<li>'.$key.'</li>';
 }
 else if ($key == 'ibcceqv_reqd' && $value == 1){
     if($key == 'ibcc equivalent certificate' && $value == 0){
         $string .= '<li>'.$key.'</li>';
         break;
     }
     break;
     
 }
 
else if ($key == 'hsscone_reqd' && $value == 1){
     if($key == 'HSSC Part One Result' && $value == 0){
         $string .= '<li>'.$key.'</li>';
      break;
         
     }
     break;
 }
 
 else if ($key == 'pg_masters_reqd' && $value == 1){
     if($key == 'Original Masters Degree & Transcript' && $value == 0){
         $string .= '<li>'.$key.'</li>';
      }
     if($key == 'Attested Copies of Masters Degree & Transcript' && $value == 0){
         $string .= '<li>'.$key.'</li>';
      } 
      break;
 }
 else if ($key == 'pg_cgpacertificatereqd' && $value == 1){
     if($key == 'CGPA Certicate' && $value == 0){
         $string .= '<li>'.$key.'</li>';
      break;
         
     }
     break;
 }
 
}
$string .='</ul>';
                $to->firstname = $to_firstname;
                $to->lastname = $to_lastname;
                $to->email = 'ayeshaanasim@gmail.com';
                
                    
                $from->firstname = 'Exam Branch'; 
               $from->lastname = 'NUST'; 
               $from->email = 'ayesha.nasim@seecs.edu.pk';
               $from->maildisplay = true;

                $subject = "Pending Documents";
               $bodyhtml = 'Dear Student,<br/>Please submit the following documents as soon as possible to ensure your joining:
                  '.$string.'<br/> Regards <br/> Exam Branch</body></html>';
               
               $body     = html_to_text($bodyhtml);
               
              // $messagehtml = "Yes";
        if (!$mail_results = email_to_user($to,$from,$subject,$body,$bodyhtml)) {
           die("could not send email!");

}      }
        }
   

