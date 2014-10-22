<?php
require_once('../../config.php');

class mail
{

public function ShowSchool()
{
    global $DB;
$to = new StdClass();
$from = new StdClass();
$sql = "SELECT docs.origssc_eq as 'Original SSC / Equivalent Certificate' ,docs.orighssc_eq as 'Original HSSC / Equivalent Certificate' , docs.ibcceqv_reqd, docs.ibcceqv as 'IBCC Equivalent Certificate',docs.hsscone_reqd,docs.hsscone as 'HSSC Part One Result',
docs.copyssc_eq as 'Attested Photocopies of SSC / Equivalent Certificate',docs.copyhssc_eq as 'Attested Photocopies of HSSC / Equivalent Certificate',docs.undertaking as 'Under Taking',docs.suritybond as 'Surity Bond',docs.regform as 'Registration Form',docs.copynic as 'Copy of CNIC',docs.medicalcertificate as 'Medical Certificate',docs.photographs as 'Photographs', ";


$sql2 = "u.firstname,u.lastname,u.email FROM {joiningdocs} docs 
        JOIN {user} u ON  u.id=docs.userid
        WHERE u.registrationstatus = 'pending'
        AND docs.user_group = ".$_POST[id];
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
        return "could not send email!" ;}
           else {
           return 'success!';}
           }
}

}

$opt = new mail();