<?php

require_once('../../config.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
class mail
{

public function mail_users()
{
     global $DB,$CFG;
$to = new StdClass();
$from = new StdClass();
$sql = "SELECT u.id,docs.origssc_eq as 'Original SSC / Equivalent Certificate' ,docs.orighssc_eq as 'Original HSSC / Equivalent Certificate' , docs.ibcceqv_reqd, docs.ibcceqv as 'IBCC Equivalent Certificate',docs.hsscone_reqd,docs.hsscone as 'HSSC Part One Result',
docs.copyssc_eq as 'Attested Photocopies of SSC / Equivalent Certificate',docs.copyhssc_eq as 'Attested Photocopies of HSSC / Equivalent Certificate',docs.undertaking as 'Under Taking',docs.suritybond as 'Surity Bond',docs.regform as 'Registration Form',docs.copynic as 'Copy of CNIC',docs.medicalcertificate as 'Medical Certificate',docs.photographs as 'Photographs', 
u.firstname,u.lastname,u.email FROM {joiningdocs} docs 
        JOIN {user} u ON  u.id=docs.userid
        AND docs.user_group = 'UG'";

$result = $DB->get_records_sql($sql);


foreach($result as $r){
    $record = $DB->get_record('user',array('id'=>$r->id));  
    profile_load_data($record); 
    //print_object($record);
    
    if($record->profile_field_registrationstatus == 'pending'){
   $to_firstname = $r->firstname; 
   $to_lastname = $r->lastname; 
   $to_email = $r->email; 
$string= '';
$string .= "<html><body><ul>"; 
    $d = get_object_vars($r);
    foreach ($d as $key => $value) {
      
 if($value==0 && $key != 'ibcceqv_reqd' && $key != 'ibcc equivalent certificate' && $key != 'HSSC Part One Result' && $key != 'hsscone_reqd' && $key != 'firstname' && $key != 'lastname' && $key != 'email' && $key!= 'id'  )
 {
    $string .= '<li>'.$key.'</li>';
 }
 else if ($key == 'ibcceqv_reqd' && $value == 1){
     if($key == 'ibcc equivalent certificate' && $value == 0){
         $string .= '<li>'.$key.'</li>';
        
     }
     
 }
 
else if ($key == 'hsscone_reqd' && $value == 1){
     if($key == 'HSSC Part One Result' && $value == 0){
         $string .= '<li>'.$key.'</li>';
     
         
     }
 }
}
$string .='</ul>';
                $to->firstname = $to_firstname;
                $to->lastname = $to_lastname;
                $to->email = $to_email;
                
                    
                $from->firstname = 'LMS TEAM'; 
               $from->lastname = 'NUST'; 
               $from->email = 'lms@nust.edu.pk';
               $from->maildisplay = true;

                $subject = "Pending Documents";
               $bodyhtml = 'Dear Student,<br/>Please submit the following documents as soon as possible to ensure your joining:
                  <br/>'.$string.'<br/> Regards <br/> Exam Branch</body></html>';
               
              
               $body     = html_to_text($bodyhtml);
               
              // $messagehtml = "Yes";
        if (!$mail_results = email_to_user($to,$from,$subject,$body,$bodyhtml)) {
           die("could not send email!");
        }
}      
        }
}
}


$mail = new mail();
echo $mail->mail_users();
 




