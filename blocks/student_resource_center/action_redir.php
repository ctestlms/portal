<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Wrapper script redirecting user operations to correct destination.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package user
 */

require_once('../../config.php');
require_once('action_redir_form.php');
require_once($CFG->libdir.'/moodlelib.php');
///lib/moodlelib.php

//$posts     = optional_param('posts', null, PARAM_RAW);
global $DB;
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_heading(get_string('confirmation', 'block_student_resource_center'));
$PAGE->navbar->add(get_string('myhome', 'block_student_resource_center'),new moodle_url('/my/'));
$PAGE->navbar->add(get_string('Student_Registration', 'block_student_resource_center'),new moodle_url('/blocks/student_resource_center/student_new_reg.php'));
$PAGE->navbar->add(get_string('confirmation', 'block_student_resource_center'), new moodle_url('/blocks/student_resource_center/action_redir.php'));


$formaction = required_param('formaction', PARAM_FILE);

$posts = $_POST;
$us = urlencode(serialize($_POST));

global $PAGE,$OUTPUT;
$PAGE->set_url('/blocks/student_resource_center/action_redir.php', array('formaction'=>$formaction));
$action = new action_form( null, array('formaction'=>$formaction,'posts'=>$us) );

if($action->is_cancelled()) {
    // Cancelled forms redirect to the course main page.
    $registration_url = new moodle_url('/blocks/student_resource_center/student_new_reg.php');
    redirect($registration_url);
    
}
else if ($fromform=$action->get_data()) {
 $posts = unserialize(urldecode($posts['posts']));
 $u = array();
$user = $posts;
  
  

print_r($user);
if ($posts["formaction"] == 'joined')
{ 
   
   foreach($user["user"] as $u){
              $record = new stdClass();
               $record->id  = $u["id"];
               $record->registrationstatus = 'joined' ;
               $record->joiningdate = time() ;
        $DB->update_record('user', $record, false);
    
   
    }
    
}
else {
    // print_object($user);
   foreach($user["user"] as $u)
   {
      // print_object($u);
    $docs = implode(",",$u["docs"]);
      
            $record = new stdClass();
               $record->id  = $u["id"];
               $record->registrationstatus = 'pending' ;
               $record->docspending = $docs ;
               $record->joiningdate = time() ;
     
               if ($DB->record_exists('user', array('id' => $u["id"])))
               {
               $DB->update_record('user', $record, false);
               $sql= 'SELECT firstname,lastname,email FROM {user} WHERE id='.$u["id"];
               $result = $DB->get_record_sql($sql);
               $to = new object();
                $to->firstname = $result->firstname;
                $to->lastname = $result->lastname;
                $to->email = $result->email;
                $from = new object();
                $from->firstname = null; 
               $from->lastname = null; 
               $from->email = 'exam.branch420@gmail.com';
               $from->maildisplay = true;
               $subject = "Pending Documents";
               $message = 'Dear Student,<br/>Please submit the following documents as soon as possible to ensure your joining: <br/>'.$docs.'<br/> Regards';
               $messagehtml = "No";
               email_to_user($to, $from, $subject, $message, $messagehtml);
            //   if (!$mail_results=email_to_user($to, $from, $subject, $message, $messagehtml)){
             /*  die("could not send email!");
               }else
               {
                echo "Mail Sent Successfully!";
               }
              * 
              */
               }
 
             }
              
            }

            $url = new moodle_url('/blocks/student_resource_center/student_new_reg.php');
  redirect($url);
  
   }

    

else {
    


echo $OUTPUT->header();
$action->display();
echo $OUTPUT->footer();
}
 