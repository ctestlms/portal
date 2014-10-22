<?php
global $CFG;
require_once("{$CFG->libdir}/formslib.php");


class joining_form extends moodleform {
    
    function definition() {
              global $DB;
              //protected $toomany = false;
        $mform =& $this->_form;
        //$sql = "SELECT user_group FROM {user} WHERE id=".$this->_customdata['userid'];
       // $student = $DB->get_record_sql($sql);
      
        if($DB->record_exists('joiningdocs', array('userid'=>$this->_customdata['userid']))){
         $sql1= 'SELECT * FROM {joiningdocs} where userid ='.$this->_customdata['userid'];
        $entry=$DB->get_record_sql($sql1); 
         $mform->addElement('hidden','id',$entry->id);
         
         }
         
        $mform->addElement('hidden','userid',$this->_customdata['userid']);
        $mform->addElement('hidden','user_group',$this->_customdata['user_group']);
        
    //     $mform->addElement('advcheckbox', 'origssc_eq', 'Original SSC/Equivalent', 'Original SSC/Equivalent', array('group' => 3), array(0, 1));
        // $mform->setType('origssc _eq', PARAM_BOOL);
      $mform->addElement('advcheckbox', 'origssc_eq','', 'Original SSC/Equivalent', array('group' => 1));
        
         
         $mform->addElement('advcheckbox', 'orighssc_eq', '', 'Original HSSC/Equivalent', array('group' => 1));
         
        $radioarray=array();
        $radioarray[] =& $mform->createElement('radio', 'ibcceqv_reqd', '', get_string('yes'), 1);
        $radioarray[] =& $mform->createElement('radio', 'ibcceqv_reqd', '', get_string('no'), 0);
        $mform->addGroup($radioarray, 'radioar', '<b>O/A Levels?</b>', array(' '), false);
        $mform->disabledIf('ibcceqv', 'ibcceqv_reqd', 0);

        $mform->addElement('advcheckbox', 'ibcceqv', null, "IBCC Equivalence Cert - 'O' or 'A' level", array('group' => 1));
       // mform->addElement('advcheckbox', 'ratingtime', get_string('ratingtime', 'forum'), 'Label displayed after checkbox', array('group' => 1), array(0, 1));
        
        if($this->_customdata['user_group'] == 'UG'){ 
        $radioarray0=array();
        $radioarray0[] =& $mform->createElement('radio', 'hsscone_reqd', '', get_string('yes'), 1);
        $radioarray0[] =& $mform->createElement('radio', 'hsscone_reqd', '', get_string('no'), 0);
        $mform->addGroup($radioarray0, 'radioar0', '<b>HSSC-1 Result Required?</b>', array(' '), false);
        $mform->disabledIf('hsscone', 'hsscone_reqd', 0);
        $mform->addElement('advcheckbox', 'hsscone', '', "HSSC-1 Result", array('group' => 1));
        
       }
        
       
         $mform->addElement('advcheckbox', 'copyssc_eq', '', 'Attested Photocopies of SSC/Equivalent', array('group' => 1));
         $mform->addElement('advcheckbox', 'copyhssc_eq', '', 'Attested Photocopies of HSSC/Equivalent', array('group' => 1));
         $mform->addElement('advcheckbox', 'regform', '', 'Registration Form', array('group' => 1));
         $mform->addElement('advcheckbox', 'undertaking', '', 'Undertaking', array('group' => 1));
         $mform->addElement('advcheckbox', 'suritybond', '', 'Surity Bond', array('group' => 1));
         $mform->addElement('advcheckbox', 'copynic', '', "Photocopies of NIC/Form 'B'", array('group' => 1));
         $mform->addElement('advcheckbox', 'photographs', '', 'Photographs (1" x 1")' , array('group' => 1));
         $mform->addElement('advcheckbox', 'medicalcertificate', '', 'Medical Certificate' , array('group' => 1));
         //PG RELATED FIELDS
         if($this->_customdata['user_group'] == 'PG'){ 
         $mform->addElement('advcheckbox', 'pg_origbachelors_result', '', 'Original Bachelors Degree & Transcript' , array('group' => 1));
        
         $mform->addElement('advcheckbox', 'pg_copybachelors_result', '', 'Attested Copies of Bachelors Degree & Transcript' , array('group' => 1));
         
         //if PG has done Post Grad
        $radioarray1=array();
        $radioarray1[] =& $mform->createElement('radio', 'pg_masters_reqd', '', get_string('yes'), 1);
        $radioarray1[] =& $mform->createElement('radio', 'pg_masters_reqd', '', get_string('no'), 0);
        $mform->addGroup($radioarray1, 'radioar1', '<b>Have a Masters Degree?</b>', array(' '), false);
        $mform->disabledIf('pg_origmasters_result', 'pg_masters_reqd', 0);
        $mform->disabledIf('pg_copymasters_result', 'pg_masters_reqd', 0);
        $mform->addElement('advcheckbox', 'pg_origmasters_result', '', 'Original Masters Degree & Transcript' , array('group' => 1));   
        $mform->addElement('advcheckbox', 'pg_copymasters_result', '', 'Attested Copies of Masters Degree & Transcript' , array('group' => 1));
//CGPA Certi Requirement
          $radioarray2=array();
$radioarray2[] =& $mform->createElement('radio', 'pg_cgpacertificate_reqd', '', get_string('yes'), 1);
$radioarray2[] =& $mform->createElement('radio', 'pg_cgpacertificate_reqd', '', get_string('no'), 0);
         
$mform->addGroup($radioarray2, 'radioar2', '<b>CGPA Certificate Required?</b>', array(' '), false);
$mform->disabledIf('pg_cgpacertificate', 'pg_cgpacertificate_reqd', 0);
$mform->addElement('advcheckbox', 'pg_cgpacertificate', '', 'CGPA Certicate' , array('group' => 1));
$mform->setDefault('pg_origbachelors_result', 1);
//$mform->setDefault('pg_origmasters_result', 1);
$mform->setDefault('pg_copybachelors_result', 1);
//$mform->setDefault('pg_origmasters_result', 1);
//$mform->setDefault('pg_cgpacertificate', 1);
         }
         
       //  $this->add_checkbox_controller(1, get_string("checkallornone"), array('style' => 'font-weight: bold;'));
//$this->add_checkbox_controller(1, '<font color=red><b>Check All / None</b></font>', array('style' => 'text-align: left;'), 0);
   
$mform->setDefault('origssc_eq', 1);
$mform->setDefault('orighssc_eq', 1);
//$mform->setDefault('ibcceqv', 1);
$mform->setDefault('copyssc_eq', 1);
$mform->setDefault('copyhssc_eq', 1);
$mform->setDefault('regform', 1);
$mform->setDefault('undertaking', 1);
$mform->setDefault('suritybond', 1);
$mform->setDefault('copynic', 1);
$mform->setDefault('photographs', 1);

//$mform->setDefault('hsscone', 1);
$mform->setDefault('medicalcertificate', 1);

         $options1 = array(
        'joined' => 'joined',
        'pending' => 'pending',
        'not joined' => 'not joined');
 
        
        $mform->addElement('select', 'status','Status' ,$options1);
        
     
       
                $buttonarray=array();
      
$buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'),array('onClick'=>"return confirm('Are you sure you want to submit your data to the NUST Exam Branch?');"));
$buttonarray[] = &$mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
         
    
    
    }
    
}
    



