<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");


class profile_editform extends moodleform {
    
    function definition() {
              global $DB;
        $mform =& $this->_form;
        $sql = "SELECT user_group, user_subgroup FROM {user} 
            WHERE id= ".$this->_customdata['userid'];
         $result = $DB->get_record_sql($sql);
          
         if($DB->record_exists('regform', array('userid'=>$this->_customdata['userid']))){
         $sql1= 'SELECT * FROM {regform} where userid ='.$this->_customdata['userid'];
        $entry=$DB->get_record_sql($sql1); 
         $mform->addElement('hidden','id',$entry->id);
         
         }
        $mform->addElement('hidden','userid',$this->_customdata['userid']);
        $mform->addElement('hidden','subgroup',$result->user_subgroup );
     
        $mform->addElement('header','displayinfo', get_string('profile_header', 'block_student_resource_center'));
        //---------------------ADDITIONAL INFORMATION--------------------------------------------------------------
          $mform->addElement('text', 'rollno', 'Roll no.');
        $mform->addRule('rollno', get_string('error'), 'required', null, 'client');
        
       
        
        $mform->addElement('text', 'meritno', get_string('meritno', 'block_student_resource_center'));
        $mform->addRule('meritno', 'Merit no. should be numeric', 'numeric', null, 'client');
        $mform->addRule('meritno', get_string('error'), 'required', null, 'client');
        
        
       // $mform->addElement('text', 'regnno', get_string('regnno', 'block_student_resource_center'));
        //$mform->addRule('regnno', get_string('error'), 'required', null, 'client');
        
        
        $mform->addElement('date_selector', 'dob', get_string('DOB','block_student_resource_center'));
        $optionss = array(
        'Male' => 'Male',
        'Female' => 'Female'
         );
        
        $mform->addElement('select', 'gender',get_string('gender','block_student_resource_center') ,$optionss);
      
        $optionsc = array(
        'Islam' => 'Islam',
        'Christian' => 'Christian',
        'Hindu' => 'Hindu',
        'Ahmadi' => 'Ahmadi'
         );
        
        $mform->addElement('select', 'religion',get_string('religion','block_student_resource_center'),$optionsc);
        
       // $attributes_nicno = array('size'=>'13');
        $attribute = array('size'=>'15');
        $nic[] = $mform->createElement('text', 'nicno', get_string('nicno', 'block_student_resource_center'),$attribute);
        $nic[] = $mform->createElement('static', 'nicdesc', 'for Example','For Example: 61101-5149619-8');
         $mform->addGroup($nic, 'nicarr', 'CNIC/B-Form No.', array(' '), false);
        
        $mform->addRule('nicarr', get_string('error'), 'required', null, 'client');
         $options = array(
        'A+' =>'A+',
        'A-' => 'A-', 
        'B+' => 'B+',
        'B-' => 'B-',
        'AB+' => 'AB+',
        'AB-' => 'AB-',
        'O+' => 'O+',
        'O-' => 'O-'
         );
        $mform->addElement('select', 'bloodgroup', get_string('bloodgroup', 'block_student_resource_center'), $options);
        
        //$mform->addElement('text', 'name', get_string('name', 'block_student_resource_center'));
      //  $mform->addElement('text', 'fathername', get_string('fathername', 'block_student_resource_center'));
        //$mform->addRule('fathername', get_string('error'), 'required', null, 'client');
        $fatherprof[] = $mform->createElement('text', 'fatherprofession', get_string('fatherprofession', 'block_student_resource_center'));
        $fatherprof[] = $mform->createElement('static', 'fatherdesc', 'for Example','For Example: Army, Businessman, Banker, Engineer, Foreign Service, Contractor etc.');
        $mform->addGroup($fatherprof, 'fatherprofarr', 'Father`s Profession', array(' '), false);
        $mform->addRule('fatherprofarr', get_string('error'), 'required', null, 'client');
       
        $mform->addElement('text', 'fatherrank',get_string('fatherrank', 'block_student_resource_center'));
        $mform->addRule('fatherrank', 'maximum length should be 10 characters', 'maxlength', 10, null, false, false);
        $optionsb = array(
        'Serving' => 'Serving',
        'Retired' => 'Retired'
         );
        $mform->addElement('select', 'fatherservice',get_string('fatherserving', 'block_student_resource_center') ,$optionsb);
        
        $domi[] = $mform->createElement('text', 'domicile', get_string('domicile', 'block_student_resource_center'));
        $domi[] = $mform->createElement('static', 'domiciledesc', 'for Example','For Example: Capital, Punjab etc.');
        $mform->addGroup($domi, 'domiarr', 'Domicile District', array(' '), false);
        $mform->addRule('domiarr', get_string('error'), 'required', null, 'client');
        
        
        $optionsf = array(
        'Capital' => 'Capital',
        'Punjab' => 'Punjab',
        'Sindh' => 'Sindh',
        'KPK' => 'KPK',
        'Balochistan' => 'Balochistan',
        'AJ&K' => 'AJ&K',
        'Gilgit Baltistan' => 'Gilgit Baltistan',
        'FATA' => 'FATA',
        'NA(Foreigner)' => 'NA(Foreigner)');
         
        
        $mform->addElement('select', 'province', get_string('province', 'block_student_resource_center'),$optionsf);
        //$mform->addRule('province', get_string('error'), 'required',null, 'client');
      
       
        $optionsd = array(
        'TCS' => 'TCS',
        'UMS' => 'UMS',
        'By Hand' => 'By Hand'
         );
        $mform->addElement('select', 'dispatchmode',get_string('dispatchmode', 'block_student_resource_center') ,$optionsd);
        
        {
         $optionse = array(
        'Open Merit' => 'Open Merit',
        'SAT(NAT)' => 'SAT(NAT)',
        'SAT(Intl)' => 'SAT(Intl)'
         );
        $mform->addElement('select', 'admissiontype', 'Admission type' ,$optionse);
        
         //------------------------FOREIGN STUDENT  INFORMATION-----------------------------------
        $mform->addElement('header','contactinfo', "For Foreign Students Only");
        $mform->addElement('advcheckbox', 'foreignstudent', 'Are you a foreign student?'null,array(0,1));
        $mform->addElement('text', 'origncountry', 'Country of Orign');
        $mform->disabledIf('origncountry', 'foreignstudent', 0);
        $mform->addElement('text', 'passportno', 'Passport No.');
        $mform->disabledIf('passportno', 'foreignstudent', 0);
      //  $mform->addRule('passportno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
        
        //------------------------CONTACT INFORMATION-----------------------------------
        $mform->addElement('header','contactinfo', "Contact Information");
        $mform->addElement('static', 'contact_description', '<b>Hint:</b> ','Phone No. 0092512291881 <br/> Mobile No. 00923245764987 ' );
        //$mform->addElement('text', 'currentaddress', 'Current Address');
        //$mform->addRule('currentaddress', get_string('error'), 'required', null, 'client');
        
        //$mform->addElement('text', 'currentcity', 'Current City');
        //$mform->addRule('currentcity', get_string('error'), 'required', null, 'client');
        
          $mform->addElement('text', 'currphoneno', 'Current Phone no.', 'maxlength="14"');
         $mform->addRule('currphoneno', 'Only numbers are allowed in phone no. field','numeric',null,'client');
         $mform->addRule('currphoneno', get_string('error'), 'required', null, 'client');
      
        
        //$mform->addElement('text', 'studentmobileno', 'Student Mobile no.');
       //$mform->addRule('studentmobileno', 'Only numbers are allowed in phone no. field' , 'numeric', null, 'client');
       //$mform->addRule('studentmobileno', get_string('error'), 'required', null, 'client');
        $mform->addElement('text', 'permanentaddress', 'Permanent Address');
        $mform->addRule('permanentaddress', get_string('error'), 'required', null, 'client');
        $mform->addElement('text', 'permanentcity', 'Permanent City');
        $mform->addRule('permanentcity', get_string('error'), 'required', null, 'client');
        $mform->addElement('text', 'perphoneno', 'Permanent Phone no.', 'maxlength="14"');
        $mform->addRule('perphoneno', get_string('error'), 'required', null, 'client');
        $mform->addRule('perphoneno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
 
        $mform->addElement('text', 'fathermobileno', 'Father/Guardian Mobile no.', 'maxlength="14"');
        $mform->addRule('fathermobileno', get_string('error'), 'required', null, 'client');
        $mform->addRule('fathermobileno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
        $mform->addElement('text', 'emergencyno', 'Emergency Contact no.', 'maxlength="14"');
       $mform->addRule('emergencyno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
       $mform->addRule('emergencyno', get_string('error'), 'required', null, 'client');
       $mform->addElement('text', 'faxno', 'Fax No.');
        $mform->addRule('faxno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
        
        
        //------------------------EDUCATION--------------------------------------------
        $mform->addElement('header','educationalinfo',get_string('academic_information', 'block_student_resource_center'));
        
        $mform->addElement('static', 'marksdes', '<b>Hint:</b> ','Marks %Age: 80.00');

        
        

       $options1 = array(
        'SSC' => 'SSC',
        'O-Level' => 'O-Level'
         );
        
 
         $attributes_marks = array('size'=>'5');
         $attributes_m = array('size'=>'4');
        $mform->addElement('select', 'firstdegreename','Degree Name' ,$options1);
        
        $mform->addElement('text', 'firstpassingyear', 'Year of Passing', $attributes_m);
        $mform->addRule('firstpassingyear', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'firsttotalmarks', 'Total Marks');
        $mform->addRule('firsttotalmarks', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'firstmarksobtained', 'Marks Obtained');
        $mform->addRule('firstmarksobtained', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'firstpercentage', 'Marks %Age/ C.GPA', $attributes_marks);
        $mform->addRule('firstpercentage', get_string('error'), 'required', null, 'client');
        $mform->addRule('firstpercentage', 'exceeds maximum length', 'maxlength', 5, null, false, false);
        
       $board[] = $mform->createElement('text', 'firstboard', 'Board');
       $board[] = $mform->createElement('static', 'boarddesc', 'for Example','For Example: FBISE, RBISE, IBCC(for O/A Levels)');
        $mform->addGroup($board, 'boardarr', 'Domicile District', array(' '), false);
        $mform->addRule('boardarr', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'firstmajorsubjects', 'Major Subjects');
        $mform->addRule('firstmajorsubjects', get_string('error'), 'required', null, 'client');
        
        //--------------------INTER--------------------------------------------------------------------
        
        $options2 = array(
        'HSSC' => 'HSSC',
        'A-Level' => 'A-Level'
         );
        
        
        $mform->addElement('select', 'seconddegreename','Degree Name' ,$options2);
        
        $mform->addElement('text', 'secondpassingyear', 'Year of Passing', $attributes_m);
        $mform->addRule('secondpassingyear', get_string('error'), 'required', null, 'client');
        $mform->addRule('secondpassingyear', 'exceeds maximum length', 'maxlength', 4, null, false, false);
        
        $mform->addElement('text', 'secondtotalmarks', 'Total Marks');
        $mform->addRule('secondtotalmarks', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'secondmarksobtained', 'Marks Obtained');
        $mform->addRule('secondmarksobtained', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'secondpercentage', 'Marks %Age', $attributes_marks);
        $mform->addRule('secondpercentage', get_string('error'), 'required', null, 'client');
        $mform->addRule('secondpercentage', 'exceeds maximum length', 'maxlength', 5, null, false, false);
        
        $mform->addElement('text', 'secondboard', 'Board');
        $mform->addRule('secondboard', get_string('error'), 'required', null, 'client');
        
         $mform->addElement('text', 'secondmajorsubjects', 'Major Subjects');
        $mform->addRule('secondmajorsubjects', get_string('error'), 'required', null, 'client');
        
        //---------------------------------PG--------------------------------------------------------------
        
        //-----------------------BACHELORS-------------------------------------------------------------------
        if($result->user_group == 'PGCourses'){  
          $mform->addElement('static', 'cgpades', '<b>Hint:</b> ','CGPA: 3.11');

        
             $options3 = array(
        'Bachelor`s Degree' => 'Bachelor`s Degree'
         );
             
           $mform->addElement('select', 'thirddegreename','Degree Name' ,$options3);
        
        $mform->addElement('text', 'thirdpassingyear', 'Year of Passing', $attributes_m);
$mform->addRule('thirdpassingyear', 'exceeds maximum length', 'maxlength', 4, null, false, false);       
 $mform->addRule('thirdpassingyear', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'thirdtotalcgpa', 'Total Marks/CGPA');
        $mform->addRule('thirdtotalcgpa', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'thirdcgpaobtained', 'Marks/CGPA Obtained');
        $mform->addRule('thirdcgpaobtained', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'thirdcgpapercentage', 'MARKS/CGPA %Age', $attributes_marks);
        $mform->addRule('thirdcgpapercentage', 'exceeds maximum length', 'maxlength', 4, null, false, false);
        $mform->addRule('thirdcgpapercentage', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'thirduniversity', 'University');
        $mform->addRule('thirduniversity', get_string('error'), 'required', null, 'client');
        
         $mform->addElement('text', 'thirdmajorsubjects', 'Major Subjects');
        $mform->addRule('thirdmajorsubjects', get_string('error'), 'required', null, 'client');
        
        //-------------------------MASTERS-----------------------------------------------
         
           $options4 = array(
        'Master`s Degree' => 'Master`s Degree'
         );
            
        
            $mform->addElement('select', 'fourdegreename','Degree Name' ,$options4);
        
        $mform->addElement('text', 'fourpassingyear', 'Year of Passing', $attributes_m);
        $mform->addRule('thirdpassingyear', 'exceeds maximum length', 'maxlength', 4, null, false, false);
        $mform->addRule('fourpassingyear', get_string('error'), 'required', null, 'client');
        
           $mform->addElement('text', 'fourtotalcgpa', 'Total Marks/CGPA');
        $mform->addRule('fourtotalcgpa', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'fourcgpaobtained', 'Marks/CGPA Obtained');
        $mform->addRule('fourcgpaobtained', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'fourcgpapercentage', 'MARKS/CGPA %Age', $attributes_marks);
        $mform->addRule('fourcgpapercentage', 'exceeds maximum length', 'maxlength', 4, null, false, false);
        $mform->addRule('fourcgpapercentage', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'fouruniversity', 'University');
        $mform->addRule('fouruniversity', get_string('error'), 'required', null, 'client');
        
         $mform->addElement('text', 'fourmajorsubjects', 'Major Subjects');
        $mform->addRule('fourmajorsubjects', get_string('error'), 'required', null, 'client');
        
   } 
        
         
        
        //-------GUARDIAN----------------------------------------------------------
        $mform->addElement('header','guardianinfo', "If Case of Guardian");
        $mform->addElement('text', 'guardianname', 'Name');
       $guardian[] = $mform->createElement('text', 'guardianrelation', 'Relationship');
      $guardian[] = $mform->createElement('static', 'reldes', 'for Example','For Example: Brother' );
         $mform->addGroup($guardian, 'guardianarr', 'Relationship', array(' '), false);
        
        


        
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', 'Update Profile');
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);


    }
    
}
    
   


}