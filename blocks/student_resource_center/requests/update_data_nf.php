<?php

require_once('../../../config.php');

 global $DB,$CFG,$PAGE,$USER;
require_login();

$sql = "SELECT * FROM {sdata} WHERE remarks = \"NF\"";
$record = $DB->get_records_sql($sql);
//print_object($record);
$i = 0;
$j = 0;

foreach ($record AS $r)
{
    $reg = $r->registrationno;
    
                       if($reg[0] == "2"){
                       
                        $last = (int)substr($reg, -3);
                     //   $lasts = substr($reg, -3);
                        //echo $last." "."<br>";
                           if($last < 100) {
                            //   echo $reg." ";  
                                   $check = (string)$last;
                                   //echo "cropped".$check." ";
                                 $regnew = substr($reg, 0, -3);
                                 $nregnew = $regnew.$check;
                              //   echo "new reg: ".$nregnew."<br>"; 
                               
                           }
                           else {
                               $nregnew = $reg;
                           }
                              
                           
			
                      
                        }
                        else {
                            $nregnew = $reg;
                        }
                        
    
    $q1 = "SELECT * FROM {user} WHERE idnumber = \"$nregnew\"";
   
    if($stud = $DB->get_records_sql($q1)){
  // print_object($stu);
   foreach($stud as $stu){
       //print_object($stu);
       
        $dob = strtotime($r->dob);
        //update user table
        $upuser = new stdClass();
        $upuser->id = $stu->id;
       $upuser->firstname = $r->firstname;
        $upuser->lastname = $r->lastname;
        $upuser->fathername = $r->fathername;
        $upuser->idnumber = $r->registrationno;
        $upuser->phone2 = $r->currphoneno;
        $upuser->address = $r->currentaddress;
        $DB->update_record('user', $upuser, false);
       print_object($upuser);
       
 
 $u = new stdClass();      
         if($result = $DB->get_record('regform',array('userid' => $stu->id ))){
        $u->id = $result->id;
        $u->userid = $stu->id; 
        $u->rollno = $r->rollno;
        $u->meritno = $r->meritno;
        $u->nicno = $r->nicno;
        $u->bloodgroup = $r->bloodgroup;
        
        $u->dob = $dob;
        $u->gender = $r->gender;
       $u->religion = $r->religion;
       $u->fatherprofession = $r->fatherprofession;
        $u->fatherrank = $r->fatherrank;
       $u->domicile = $r->domicile;
       $u->province = $r->province;
       $u->admissiontype = $r->admissiontype;
       $u->dispatchmode = $r->dispatchmode;
       
      $u->currentaddress = $r->currentaddress;
       $u->currentcity = $r->currentcity;
       
       $u->permanentaddress = $r->permanentaddress;
       $u->permanentcity = $r->permanentcity;
       
        $u->perphoneno = $r->perphoneno;
        $u->fathermobileno = $r->fathermobileno;
        $u->emergencyno = $r->emergencyno;
        $u->freeze_edit = 1;
        //print_object($u);
         $DB->update_record('regform',$u);
         
         }
         
          else {
       
        $u->id = '';
        $u->userid = $stu->id;
          
        $u->rollno = $r->rollno;
        $u->meritno = $r->meritno;
        $u->nicno = $r->nicno;
        $u->bloodgroup = $r->bloodgroup;
        
        $u->dob = $dob;
        $u->gender = $r->gender;
       $u->religion = $r->religion;
       $u->fatherprofession = $r->fatherprofession;
        $u->fatherrank = $r->fatherrank;
       $u->domicile = $r->domicile;
       $u->province = $r->province;
       $u->admissiontype = $r->admissiontype;
       $u->dispatchmode = $r->dispatchmode;
       
      $u->currentaddress = $r->currentaddress;
       $u->currentcity = $r->currentcity;
       
       $u->permanentaddress = $r->permanentaddress;
       $u->permanentcity = $r->permanentcity;
       
      $u->perphoneno = $r->perphoneno;
      $u->fathermobileno = $r->fathermobileno;
      $u->emergencyno = $r->emergencyno;
        
      $u->freeze_edit = 1;
      //print_object($u);
        $DB->insert_record('regform', $u);
          }
          
         
        //user_info_fields;//Last CGPA
        $u2 = new stdClass();
        $u2->data = $r->cgpa;
         if($result1 = $DB->get_record('user_info_data',array('userid' => $stu->id, 'fieldid' => 12 ))){
         $u2->id = $result1->id;
         $DB->update_record('user_info_data', $u2);
         }
          else {
          $u2->id = '';
          $u2->userid = $stu->id;
        $u2->fieldid = 12;
         $DB->insert_record('user_info_data', $u2);
          }
          
          //Degree Status New
           $u3 = new stdClass();
        $u3->data = $r->degreestatus;
         if($result2 = $DB->get_record('user_info_data',array('userid' => $stu->id, 'fieldid' => 13))){
         $u3->id = $result2->id;
         $DB->update_record('user_info_data', $u3);
         }
          else {
          $u3->id = '';
          $u3->userid = $stu->id;
        $u3->fieldid = 13;
         $DB->insert_record('user_info_data', $u3);
          }
       
         
        //JOINING DATE
 
        $reg = $r->registrationno; 
                       if($reg[0] == "2"){
			$enrolyear = $reg[0].$reg[1].$reg[2].$reg[3];
			} else if($reg[0] == "N"){
					$enrolyear = $reg[4].$reg[5].$reg[6].$reg[7];
				}
           $u5 = new stdClass();
        $u5->data = $enrolyear;
         if($result3 = $DB->get_record('user_info_data',array('userid' => $stu->id, 'fieldid' => 11))){
         $u5->id = $result3->id;
         $DB->update_record('user_info_data', $u5);
         }
          else {
          $u5->id = '';
          $u5->userid = $stu->id;
        $u5->fieldid = 11;
         $DB->insert_record('user_info_data', $u5);
          }
       
        //Graduation Year
  if(substr_count($stu->user_subgroup,"-") == 2){
 
        $lms_subgroup = substr($stu->user_subgroup, 0, -2);
  }
 else{
   $lms_subgroup = $stu->user_subgroup;}
 
        $q2 = "SELECT * FROM {classes} WHERE alias = \"$lms_subgroup\"";
        
        if($c = $DB->get_record_sql($q2)){
        $u6 = new stdClass();
        $u6->data = $c->enddate;
         if($result4 = $DB->get_record('user_info_data',array('userid' => $stu->id, 'fieldid' => 15))){
         $u6->id = $result4->id;
         $DB->update_record('user_info_data', $u6);
         }
          else {
          $u6->id = '';
          $u6->userid = $stu->id;
        $u6->fieldid = 15;
        $DB->insert_record('user_info_data', $u6);
          }
        }
        else{
        $u6 = new stdClass();
        $u6->data = "NF";
         if($result4 = $DB->get_record('user_info_data',array('userid' => $stu->id, 'fieldid' => 15))){
         $u6->id = $result4->id;
       $DB->update_record('user_info_data', $u6);
         }
          else {
          $u6->id = '';
          $u6->userid = $stu->id;
        $u6->fieldid = 15;
      $DB->insert_record('user_info_data', $u6);
          } 
        }
        
       
       //sdata
            
         $u1 = new stdClass();
        $u1->id = $r->id;
        $u1->remarks = 'F';
        $u1->userid = $stu->id;
        $u1->email = $stu->email;
        $u1->username = $stu->username;
        $u1->joiningdate = $enrolyear;
        if($c){
        $u1->graduationyear = $c->enddate; 
        }
        else{
        $u1->graduationyear = "NF";    
        }
        $DB->update_record('sdata', $u1, false);
    
        
  
         
  
        
         
        $i++;
        
    }
    
         

    }
    
    else
    {
        
     
        $u1 = new stdClass();
        $u1->id = $r->id;
        $u1->remarks = 'NFA';
      $DB->update_record('sdata', $u1, false);
    
       $j++;
        
        
    }
    
   
  
    
}
 

//echo $i."<br>".$j;
         
