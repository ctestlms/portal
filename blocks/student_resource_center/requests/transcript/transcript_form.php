<?php
require_once('../../../../config.php');
global $CFG;
require_once("{$CFG->libdir}/formslib.php");
require_once("{$CFG->libdir}/filelib.php");

class cert_form extends moodleform {
    
    function definition() {
              global $CFG,$USER,$DB;
           $mform =& $this->_form;
             /////////////////////////////////PERSONAL INFO ////////////////////////////////////////////////////////////////////
        $mform->addElement('header','heading', get_string('personalinfo_head','block_student_resource_center'));
        //Name  // University Regn no // Programme // school 
        $name = $USER->firstname.' '.$USER->lastname;
         $sql1 = "SELECT nicno FROM {regform} WHERE userid=$USER->id";
               $user_info = $DB->get_record_sql($sql1);
         
        $mform->addElement('static', 'personal_data', null,'<font color="red">To update your personal Information, please email Exam Branch at : <i><b>exam@seecs.edu.pk</b></i></i></font>');
        $mform->addElement('static', 'name' ,'<b>Name:</b>',"<b>$name</b>");
        $mform->addElement('static', 'regno', '<b>Email:</b>',"<b>$USER->email</b>");
        $mform->addElement('static', 'address' ,'<b>Mailing Address:</b>',"<b>$USER->address</b>");
        $mform->addElement('static', 'school', '<b>CNIC No.:</b>',"<b>$user_info->nicno</b>");
        
  
        /////////////////////////////////////////////Request Information///////////////////////////////////////////////////////
           $mform->addElement('header','heading', get_string('requestinfo_head','block_student_resource_center'));
           $requesttype =array();
           $requesttype[] =& $mform->createElement('radio', 'request_type', '', 'Normal', 'Normal');
           $requesttype[] =& $mform->createElement('radio', 'request_type', '', 'Urgent', 'Urgent');
           $mform->addGroup($requesttype, 'request_typearr', 'Request Type', array(' '), false);
           $mform->setDefault('request_type', 'Normal');
           //No. of Copies Requireds
              $copies_deposited[] =  $mform->createElement('text', 'copies_no', '');
             // $copies_deposited[] =  $mform->createElement('text', 'amount_deposited', get_string("amount", 'block_student_resource_center'));
           $copies_deposited[] = $mform->createElement('static', 'amount_note', 'amount_note','<font color="black" size="0.8px"> <i>(Total No. of Copies Required)</i></font>');
           $mform->addGroup($copies_deposited, 'copies_d',get_string('copies_no2','block_student_resource_center'), array(' '), false);
           $mform->addRule('copies_d', get_string('error'), 'required', null, 'client'); 
        //envelope
           //Questions :3
           $envelope =array();
           $envelope[] =& $mform->createElement('radio', 'sealed_env', '', 'Yes', 1);
           $envelope[] =& $mform->createElement('radio', 'sealed_env', '', 'No', 0);
           
           $envelope[] =&  $mform->createElement('static', 'note1', '',"<font color='black' size='0.8px'><i>Do you require transcripts in sealed envelope?</i></font>");
           $mform->addGroup($envelope, 'sealed_envarr', 'Sealed Envelope', array(' '), false);
           $mform->setDefault('sealed_env', 0);
           //$mform->addElement('text', 'env_copy', get_string("env_copy2", 'block_student_resource_center'));
             $copies_deposited1[] =  $mform->createElement('text', 'env_copy', '');
             // $copies_deposited[] =  $mform->createElement('text', 'amount_deposited', get_string("amount", 'block_student_resource_center'));
           $copies_deposited1[] = $mform->createElement('static', 'amount_note', 'amount_note','<font color="black" size="0.8px"> <i>(Total No. of copies in each enveloper)</i></font>');
           $mform->addGroup($copies_deposited1, 'copies_d1',get_string('env_copy3','block_student_resource_center'), array(' '), false);
           $mform->disabledIf('env_copy', 'sealed_env',0);
           
           ///////////////////////////////////////////HISTORY//////////////////////////////////////////////////////////////////
           $mform->addElement('header','heading', get_string('history_head1','block_student_resource_center'));
            //Questions :1
           
           $biodata =array();
           $biodata[] =& $mform->createElement('radio', 'changes_biodata', '', 'Yes', 1);
           $biodata[] =& $mform->createElement('radio', 'changes_biodata', '', 'No', 0);
           $biodata[] =& $mform->createElement('static', 'note2', '',"<font color='black' size='0.8px'> <i>(Have you applied for changes/correction of personal bio-data during your stay at NUST?)</i></font>");
           $mform->addGroup($biodata, 'changes_biodataarr', 'Bio-data history', array(' '), false);
           $mform->setDefault('changes_biodata', 0);
           $mform->addElement('textarea', 'details_biodata', '','wrap="virtual" rows="2" cols="20"');
           $mform->addRule('details_biodata', 'maximum length should be 85 characters', 'maxlength', 118, 'client');
           $mform->disabledIf('details_biodata', 'changes_biodata',0);
          
          //Questions :2
           //$mform->createElement('static', 'note3', '',"<b>Have you ever been issued with transcripts for this course?</b>");
           $changes =array();
           $changes[] =& $mform->createElement('radio', 'before_copy', '', 'Yes', 1);
           $changes[] =& $mform->createElement('radio', 'before_copy', '', 'No', 0);
           $changes[] =& $mform->createElement('static', 'note2', '',"<font color='black' size='0.8px'><i>(Have you ever been issued with transcripts for this course?)</i></font>");
           
           $mform->addGroup($changes, 'before_copyarr', 'Transcript History', array(' '), false);
           $mform->setDefault('before_copy', 0); 
           $mform->addElement('filepicker', 'supporting_doc', '', null,
           array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
           $mform->disabledIf('supporting_doc', 'before_copy',0);
          
             ///////////////////////////////////MODE OF DELIVERY/////////////////////////////////////////////////////////////////////
          //Mode of Delivery
          $mform->addElement('header','heading', get_string('mode_title','block_student_resource_center'));
          $mode =array();
          $mode[] =& $mform->createElement('radio', 'delivery_mode', '', 'Through Mail', 'Through Mail');
          $mode[] =& $mform->createElement('radio', 'delivery_mode', '', 'By Hand', 'By Hand');
          $mform->addGroup($mode, 'delivery_modearr', 'Delivery Mode', array(' '), false);
          $mform->setDefault('delivery_mode', 'Through Mail');
           //Hand
           $person =array();
           
           $person[] =& $mform->createElement('radio', 'byhand_person', '', 'Self', 0);
           $person[] =& $mform->createElement('radio', 'byhand_person', '', 'Authorized Person', 1);
           $mform->addGroup($person, 'byhand_personarr', 'Recieved By', array(' '), false);
           $mform->setDefault('byhand_person', 0);
           
           $mform->disabledIf('byhand_person', 'delivery_mode', 'eq','Through Mail');
           
           //when authorized person
                      $mform->addElement('text', 'authorized_name', get_string("authorized_name1", 'block_student_resource_center'));
                      $cnic[] =& $mform->createElement('text', 'authorized_cnic', get_string("authorized_cnic1", 'block_student_resource_center'));
                      $cnic[] =& $mform->createElement('static', 'note2', '',"<font color='red' size='0.8px'> <b>For Example: 6110141219198</b></font>");           
                         $mform->addGroup($cnic, 'auth_c',get_string('authorized_cnic1','block_student_resource_center'), array(' '), false);
                      $mform->addElement('text', 'authorized_phone', get_string("authorized_phone2", 'block_student_resource_center'));
                      $mform->setType('authorized_cnic', PARAM_INT);
                      $mform->disabledIf('authorized_name', 'byhand_person',0);
                      $mform->disabledIf('authorized_cnic', 'byhand_person',0);
                      $mform->disabledIf('authorized_phone', 'byhand_person',0);
                      $mform->addElement('filepicker', 'supporting_doc2', get_string('authorized_cnicfile1', 'block_student_resource_center'), null,
                      array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
                      $mform->disabledIf('supporting_doc2', 'byhand_person',0);
                      $mform->addElement('filepicker', 'supporting_docauth', get_string('authorized_auth', 'block_student_resource_center'), null,
                      array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
                      $mform->disabledIf('supporting_docauth', 'byhand_person',0);
                //      $mform->disabledIf('byhand_person', 'delivery_mode', 'neq','By Hand');
                      
          //Mail
                    //  $mform->disabledIf('byhand_person', 'delivery_mode', 'eq','Through Mail');
                    
          ///////////////////////////////////////////////////////Bank Info//////////////////////////////////////////////////////////////
                      $mform->addElement('header','heading', get_string('fee_head','block_student_resource_center'));
                          $mform->addElement('static', 'feeslip','<b>Note:</b>','<font color="red"><b>Urgent Fee</b> (Min. 7 Working Days) : 1000Rs, <b>Normal Fee</b>(Min. 14 Working Days) : 500Rs </font>');
         
       //   $amount_deposited[] =  $mform->createElement('text', 'amount_deposited', get_string("amount", 'block_student_resource_center'));
       // $amount_deposited[] = $mform->createElement('static', 'amount_note', 'amount_note','<font color="black" size="0.8px"> <i>(Total amount deposited to the HBL Bank)</i></font>');
       // $mform->addGroup($amount_deposited, 'amount_d',get_string('d_amount','block_student_resource_center'), array(' '), false);
       // $mform->addRule('amount_d', null, 'required', null, 'client');
        
           $mform->addElement('text', 'receipt_no', get_string("receipt", 'block_student_resource_center'));
           $mform->addRule('receipt_no', null, 'required', null, 'client');
           $date_arr = array(
         'startyear' => 2013, 
         'stopyear'  => 2040,
         'timezone'  => 99,
         'optional'  => false
                 );
          $mform->addElement('date_selector', 'deposit_date', get_string("date_deposited", 'block_student_resource_center'), $date_arr);
         
          $mform->addElement('filepicker', 'receipt', get_string('receipt_file', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
          $mform->addRule('receipt', null, 'required', null, 'client');
         
          
        
                      /*
         /////////////////////////////////////UPDATE INFORMATION///////////////////////////////////////////////////////////
                       $mform->addElement('header','heading', get_string('update_head','block_student_resource_center'));
                      //update phone
                    $mform->addElement('advcheckbox', 'updatecheckbox','' ,get_string("updatecheck", 'block_student_resource_center'),null, array(0, 1));   
          $mform->addElement('text', 'phone2', get_string("contactno", 'block_student_resource_center'));
          global $DB;
          $user=$DB->get_record_sql("SELECT * FROM {user} WHERE id=$USER->id");
          $mform->setDefault('phone2', $user->phone2);
          $mform->disabledIf('phone2', 'updatecheckbox',0);
          //update address
              $mform->addElement('advcheckbox', 'updatecheckbox1','' ,get_string("updatecheck1", 'block_student_resource_center'),null, array(0, 1));   
          $mform->addElement('text', 'address', get_string("address", 'block_student_resource_center'));
          $mform->setDefault('address', $USER->address);
          $mform->disabledIf('address', 'updatecheckbox1',0);
          //update email
              $mform->addElement('advcheckbox', 'updatecheckbox2','' ,get_string("updatecheck2", 'block_student_resource_center'),null, array(0, 1));   
          $mform->addElement('text', 'email', get_string("email", 'block_student_resource_center'));
          $mform->setDefault('email', $USER->email);
          $mform->disabledIf('email', 'updatecheckbox2',0); 
               //update nicno
               global $DB;
               $sql = "SELECT nicno FROM {regform} WHERE userid=$USER->id";
               $user_info = $DB->get_record_sql($sql);
              $mform->addElement('advcheckbox', 'updatecheckbox3','' ,get_string("updatecheck3", 'block_student_resource_center'),null, array(0, 1));   
          $mform->addElement('text', 'nicno', get_string("nicno", 'block_student_resource_center'));
          $mform->setDefault('nicno', $user_info->nicno);
          $mform->disabledIf('nicno', 'updatecheckbox3',0);
                       * 
                       */
          
          /////////////////////////////////////////////////////////////////FILES UPLOAD///////////////////////////////////////////
          $mform->addElement('header','heading', get_string('other_head1','block_student_resource_center')); 
          $mform->addElement('static', 'note3', '',"<font color='red'><i>Have you cleared all outstanding dues of the college/school/centre/HQ NUST before submitting
          this application ? If yes please enclose copy of clearance certificate/copy of degree.</i></font>");
           $changes1 =array();
           $changes1[] =& $mform->createElement('radio', 'updatecheckbox4', '', 'Yes', 1);
           $changes1[] =& $mform->createElement('radio', 'updatecheckbox4', '', 'No', 0);
           $mform->addGroup($changes1, 'updatecheckbox4arr', '', array(' '), false);
           $mform->setDefault('updatecheckbox4', 0);
           $mform->addElement('filepicker', 'supporting_doc1', get_string('clearance_doc1', 'block_student_resource_center'), null,
          array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*'));
          $mform->disabledIf('supporting_doc1', 'updatecheckbox4',0);
          //$mform->addRule('supporting_doc1', null, 'required', null, 'client');
       
         $buttonarray=array();
      
         $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'),array('onClick'=>"return confirm('Are you sure you want to submit your request to the SEECS Exam Branch?');"));
         $buttonarray[] = &$mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
         $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
         
    
    
    }
    
}
    



