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
    
        //---------------------ADDITIONAL INFORMATION--------------------------------------------------------------
        $mform->addElement('text', 'rollno', 'Roll no.');
        $mform->addRule('rollno', get_string('error'), 'required', null, 'client');
        $mform->addRule('rollno', 'Roll no. should be numeric', 'numeric', null, 'client');
       
        
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
        $attribute1 = array('size'=>'5');
        $attribute2 = array('size'=>'7');
        $attribute3 = array('size'=>'1');
        $nic[] = $mform->createElement('text', 'nicno1', get_string('nicno', 'block_student_resource_center'));
        $nic[] = $mform->createElement('static', 'nics1', 'for Example','<font color="black">-</font>');
        $nic[] = $mform->createElement('text', 'nicno2', get_string('nicno', 'block_student_resource_center'));
        $nic[] = $mform->createElement('static', 'nics2', 'for Example','<font color="black">-</font>');
        $nic[] = $mform->createElement('text', 'nicno3', get_string('nicno', 'block_student_resource_center'));
        $nic[] = $mform->createElement('static', 'nicdesc', 'for Example','<font color="black">For Example: 61101-5149619-8</font>');
        $mform->addGroup($nic, 'nicarr', 'CNIC/B-Form No.', array(' '), false);
        
        $mform->addRule('nicarr', get_string('error'), 'required', null, 'client');
        
         $nicarrrules = array();
         $nicarrrules['nicno1'][] = array('Should be numeric', 'numeric', null, 'client');
         $nicarrrules['nicno2'][] = array('Should be numeric', 'numeric', null, 'client');
         $nicarrrules['nicno3'][] = array('Should be numeric', 'numeric', null, 'client');
         $nicarrrules['nicno1'][] = array('Exceeds Max Length', 'maxlength', 5, 'client');
         $nicarrrules['nicno2'][] = array('Exceeds Max Length', 'maxlength', 7, 'client');
         $nicarrrules['nicno3'][] = array('Exceeds Max Length', 'maxlength', 1, 'client');
         $mform->addGroupRule('nicarr', $nicarrrules);
        
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
        //$mform->addElement('text', 'fathername', get_string('fathername', 'block_student_resource_center'));
        //$mform->addRule('fathername', get_string('error'), 'required', null, 'client');
        $fatherprof[] = $mform->createElement('text', 'fatherprofession', get_string('fatherprofession', 'block_student_resource_center'));
        $fatherprof[] = $mform->createElement('static', 'fatherdesc', 'for Example','<font color="black">For Example: Army, Businessman, Banker, Engineer, Foreign Service, Contractor etc.</font>');
        $mform->addGroup($fatherprof, 'fatherprofarr', 'Father`s Profession', array(' '), false);
        $mform->addRule('fatherprofarr', get_string('error'), 'required', null, 'client');
        $fatherprofarrrules = array();
        $fatherprofarrrules['fatherprofession'][] = array('Exceeding maximum length', 'maxlength', 20, 'client');
        $mform->addGroupRule('fatherprofarr', $fatherprofarrrules);
        
        
        $mform->addElement('text', 'fatherrank',get_string('fatherrank', 'block_student_resource_center'));
        $mform->addRule('fatherrank', 'maximum length should be 10 characters', 'maxlength', 10, 'client');
        $optionsb = array(
        'Serving' => 'Serving',
        'Retired' => 'Retired'
         );
        $mform->addElement('select', 'fatherservice',get_string('fatherserving', 'block_student_resource_center') ,$optionsb);
        
        $domi[] = $mform->createElement('text', 'domicile', get_string('domicile', 'block_student_resource_center'));
        $domi[] = $mform->createElement('static', 'domiciledesc', 'for Example','<font color="black">For Example: Rawalpindi, Peshawer etc.</font>');
        $mform->addGroup($domi, 'domiarr', 'Domicile District', array(' '), false);
        $mform->addRule('domiarr', get_string('error'), 'required', null, 'client');
        $domiarrrules = array();
        $domiarrrules['domicile'][] = array('Should be letters only', 'lettersonly', null, 'client');
         $mform->addGroupRule('domiarr', $domiarrrules);
        
        
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
            
        'By Hand' => 'By Hand',
        'TCS' => 'TCS',
        'UMS' => 'UMS'
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
        $mform->addElement('advcheckbox', 'foreignstudent', 'Are you a foreign student?');
        $mform->setType('foreignstudent', PARAM_BOOL);
        $mform->setDefault('foreignstudent', 0);
        
        $mform->addElement('text', 'origncountry', 'Country of Orign');
        $mform->addRule('origncountry', 'Letters Only', 'lettersonly', null, 'client');
        
        $mform->disabledIf('origncountry', 'foreignstudent', 0);
        $mform->addElement('text', 'passportno', 'Passport No.');
        $mform->disabledIf('passportno', 'foreignstudent', 0);
        
        if($freeze==true && $DB->record_exists('regform',array('foreignstudent' => 1))){
        
         
            $mform->hardFreeze('origncountry');
            $mform->hardFreeze('passportno');
        }
      //  $mform->addRule('passportno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
        
        //------------------------CONTACT INFORMATION-----------------------------------
        $mform->addElement('header','contactinfo', "Contact Information");
        $mform->addElement('static', 'contact_description', '<font color="black"><b>Hint:</b></font>','<font color="black">Phone No. 0092512291881 <br/> Mobile No. 00923245764987</font>' );
      
        
          $mform->addElement('text', 'currphoneno', 'Current Phone no.');
         $mform->addRule('currphoneno', 'Only numbers are allowed in phone no. field','numeric',null,'client');
         $mform->addRule('currphoneno', 'less than minimum length', 'minlength', 13, 'client');
         $mform->addRule('currphoneno', get_string('error'), 'required', null, 'client');
      
        
        //$mform->addElement('text', 'studentmobileno', 'Student Mobile no.');
       //$mform->addRule('studentmobileno', 'Only numbers are allowed in phone no. field' , 'numeric', null, 'client');
       //$mform->addRule('studentmobileno', get_string('error'), 'required', null, 'client');
        $mform->addElement('text', 'permanentaddress', 'Permanent Address');
        $mform->addRule('permanentaddress', get_string('error'), 'required', null, 'client');
        $mform->addRule('permanentaddress', 'Exceeding maximum length', 'maxlength', 60, 'client');
        $mform->addElement('text', 'permanentcity', 'Permanent City');
        $mform->addRule('permanentcity', get_string('error'), 'required', null, 'client');
        $mform->addElement('text', 'perphoneno', 'Permanent Phone no.');
        $mform->addRule('perphoneno', get_string('error'), 'required', null, 'client');
        $mform->addRule('perphoneno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
        $mform->addRule('perphoneno', 'less than minimum length', 'minlength', 13, 'client');
 
        $mform->addElement('text', 'fathermobileno', 'Father/Guardian Mobile no.');
        $mform->addRule('fathermobileno', get_string('error'), 'required', null, 'client');
        $mform->addRule('fathermobileno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
        $mform->addRule('fathermobileno', 'less than minimum length', 'minlength', 14, 'client');
        $mform->addElement('text', 'emergencyno', 'Emergency Contact no.');
       $mform->addRule('emergencyno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
       $mform->addRule('emergencyno', get_string('error'), 'required', null, 'client');
       $mform->addRule('emergencyno', 'less than minimum length', 'minlength', 13, 'client');
       $mform->addElement('text', 'faxno', 'Fax No.');
        $mform->addRule('faxno', 'Only numbers are allowed in phone no. field', 'numeric', null, 'client');
        
        
        //------------------------EDUCATION--------------------------------------------
        $mform->addElement('header','educationalinfo',get_string('academic_information', 'block_student_resource_center'));
        
        $mform->addElement('static', 'marksdes', '<b>Hint: </b> ','<font color="black">Marks %Age: 80.00</font>');

        
        

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
       $board[] = $mform->createElement('static', 'boarddesc', 'for Example','<font color="black">For Example: FBISE, RBISE, IBCC(for O/A Levels)</font>');
        $mform->addGroup($board, 'boardarr', 'Board', array(' '), false);
        $mform->addRule('boardarr', get_string('error'), 'required', null, 'client');
        
        
         $optionssubs1 = array(
        'Science' => 'Science',
        'Computers' => 'Computers',
        'Arts' => 'Arts'
         );
         
        $mform->addElement('select', 'firstmajorsubjects', 'Major Subjects',$optionssubs1);
        $mform->addRule('firstmajorsubjects', get_string('error'), 'required', null, 'client');
        
        //--------------------INTER--------------------------------------------------------------------
        $mform->addElement('static', 'hsscresult', '<b>Hint: </b> ','<font color="black"><b>If your HSSC-2 result is not out, please provide your HSSC-1 Result information in the related fields.</b></font>');
        $options2 = array(
        'HSSC' => 'HSSC',
        'A-Level' => 'A-Level'
         );
        
        
        $mform->addElement('select', 'seconddegreename','Degree Name' ,$options2);
        
        $mform->addElement('text', 'secondpassingyear', 'Year of Passing', $attributes_m);
        $mform->addRule('secondpassingyear', get_string('error'), 'required', null, 'client');
        $mform->addRule('secondpassingyear', 'exceeds maximum length', 'maxlength', 4, null, false, false);
        
        $mform->addElement('text', 'secondtotalmarks', 'Total Marks');
        $mform->addRule('secondtotalmarks', 'Only Numeric Values Allowed', 'numeric', null, 'client');
        $mform->addRule('secondtotalmarks', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'secondmarksobtained', 'Marks Obtained');
        $mform->addRule('secondmarksobtained', 'Only Numeric Values Allowed', 'numeric', null, 'client');
        $mform->addRule('secondmarksobtained', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'secondpercentage', 'Marks %Age', $attributes_marks);
        $mform->addRule('secondpercentage', get_string('error'), 'required', null, 'client');
        $mform->addRule('secondpercentage', 'exceeds maximum length', 'maxlength', 5, null, false, false);
        
        $mform->addElement('text', 'secondboard', 'Board');
        $mform->addRule('secondboard', get_string('error'), 'required', null, 'client');
        
        $optionssubs = array(
        'Pre Engg' => 'Pre Engg',
        'Computers' => 'Computers',
        'Pre Med' => 'Pre Med'
         );
        
         $mform->addElement('select', 'secondmajorsubjects', 'Major Subjects' , $optionssubs);
        $mform->addRule('secondmajorsubjects', get_string('error'), 'required', null, 'client');
        
        //---------------------------------PG--------------------------------------------------------------
        
        //-----------------------BACHELORS-------------------------------------------------------------------
        if($result->user_group  == 'PG'){  
         
             $options3 = array(
        'Bachelor`s Degree' => 'Bachelor`s Degree'
         );
             
           $mform->addElement('select', 'thirddegreename','Degree Name' ,$options3);
        
        $mform->addElement('text', 'thirdpassingyear', 'Year of Passing', $attributes_m);
$mform->addRule('thirdpassingyear', 'exceeds maximum length', 'maxlength', 4, 'client');       
 $mform->addRule('thirdpassingyear', get_string('error'), 'required', null, 'client');
        
        
        $mform->addElement('text', 'thirdtotalcgpa', 'Total Marks/CGPA');
        
        $mform->addRule('thirdtotalcgpa', get_string('error'), 'required', null, 'client');
        $mform->addRule('thirdtotalcgpa', 'exceeds maximum length', 'maxlength', 4, 'client');
        
        $mform->addElement('text', 'thirdcgpaobtained', 'Marks/CGPA Obtained');
        $mform->addRule('thirdcgpaobtained', get_string('error'), 'required', null, 'client');
        $mform->addRule('thirdcgpaobtained', 'exceeds maximum length', 'maxlength', 4, 'client');
        
        $mform->addElement('text', 'thirdcgpapercentage', 'MARKS/CGPA %Age', $attributes_marks);
        $mform->addRule('thirdcgpapercentage', 'exceeds maximum length', 'maxlength', 5, 'client');
        $mform->addRule('thirdcgpapercentage', get_string('error'), 'required', null, 'client');
        
        $thirduniv[] = $mform->CreateElement('text', 'thirduniversity', 'University');
        $thirduniv[] = $mform->CreateElement('static', 'univ', '<font color="black"><b>Hint:</b></font>','<font color="black"><b>Write Abbreviation of Your University ONLY</b></font>');
        
        $mform->addGroup($thirduniv, 'thirdunivarr', 'University', array(' '), false);
        $mform->addRule('thirdunivarr', get_string('error'), 'required', null, 'client');
        $thirdarrrules = array();
        $thirdarrrules['thirduniversity'][] = array('exceeds maximum length', 'maxlength', 8, 'client');
         $mform->addGroupRule('thirdunivarr', $thirdarrrules);
   
         $mform->addElement('text', 'thirdmajorsubjects', 'Specialization');
         $mform->addRule('thirdmajorsubjects', get_string('error'), 'required', null, 'client');
         $mform->addRule('thirdmajorsubjects', 'exceeds maximum length', 'maxlength', 15, 'client');
        
        //-------------------------MASTERS-----------------------------------------------
         
           $options4 = array(
        'Master`s Degree' => 'Master`s Degree'
         );
            
        
            $mform->addElement('select', 'fourdegreename','Degree Name' ,$options4);
        
        $mform->addElement('text', 'fourpassingyear', 'Year of Passing', $attributes_m);
        $mform->addRule('fourpassingyear', 'exceeds maximum length', 'maxlength', 4, 'client');
       // $mform->addRule('fourpassingyear', get_string('error'), 'required', null, 'client');
        
           $mform->addElement('text', 'fourtotalcgpa', 'Total Marks/CGPA');
           $mform->addRule('fourtotalcgpa', 'exceeds maximum length', 'maxlength', 4, 'client');
       // $mform->addRule('fourtotalcgpa', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'fourcgpaobtained', 'Marks/CGPA Obtained');
        $mform->addRule('fourcgpaobtained', 'exceeds maximum length', 'maxlength', 4, 'client');
      //  $mform->addRule('fourcgpaobtained', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'fourcgpapercentage', 'MARKS/CGPA %Age', $attributes_marks);
        $mform->addRule('fourcgpapercentage', 'exceeds maximum length', 'maxlength', 5, 'client');
      //  $mform->addRule('fourcgpapercentage', get_string('error'), 'required', null, 'client');
        
        $mform->addElement('text', 'fouruniversity', 'University');
        $mform->addRule('fouruniversity', 'exceeds maximum length', 'maxlength', 8, 'client');
      //  $mform->addRule('fouruniversity', get_string('error'), 'required', null, 'client');
        
         $mform->addElement('text', 'fourmajorsubjects', 'Specialization');
         $mform->addRule('fourmajorsubjects', 'exceeds maximum length', 'maxlength', 15, 'client');
         
      //  $mform->addRule('fourmajorsubjects', get_string('error'), 'required', null, 'client');
        
        
        
        
   } 
        
         
        
        //-------GUARDIAN----------------------------------------------------------
        $mform->addElement('header','guardianinfo', "If Case of Guardian");
        $mform->addElement('text', 'guardianname', 'Name');
       $guardian[] = $mform->createElement('text', 'guardianrelation', 'Relationship');
      $guardian[] = $mform->createElement('static', 'reldes', 'for Example','<font color="black">For Example: Brother</font>' );
         $mform->addGroup($guardian, 'guardianarr', 'Relationship', array(' '), false);
        
        


        
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', 'Update Profile');
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);


    }
    
}
    
   


}