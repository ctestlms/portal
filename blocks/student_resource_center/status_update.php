<script type="text/javascript" src="jquery-1.3.2.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

    
    $("select#program").change(function(){
    var id = $("select#program option:selected").attr('value');
    $.post("select_degree.php", {id:id}, function(data){
        $("select#degree").html(data);
        });
    $.post("get_cohort.php", {id:id}, function(data){
        $("select#cohort").html(data);
        });    
    });
    
     $("select#degree").change(function(){
    var id = $("select#degree option:selected").attr('value');
   
    $.post("get_cohort.php", {id:id}, function(data){
        $("select#cohort").html(data);
        });    
    });
    
    
   $(".button_show").click(function () {
$(".userlist1").toggle();
});

});
    
</script>
<?php

    require_once('../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/user/filters/lib.php');

    $delete       = optional_param('delete', 0, PARAM_INT);
    $confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   //md5 confirmation hash
    $confirmuser  = optional_param('confirmuser', 0, PARAM_INT);
    $sort         = optional_param('sort', 'name', PARAM_ALPHANUM);
    $dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
    $page         = optional_param('page', 0, PARAM_INT);
    $perpage      = optional_param('perpage', 30, PARAM_INT);        // how many per page
    $ru           = optional_param('ru', '2', PARAM_INT);            // show remote users
    $lu           = optional_param('lu', '2', PARAM_INT);            // show local users
    $acl          = optional_param('acl', '0', PARAM_INT);           // id of user to tweak mnet ACL (requires $access)
    $suspend      = optional_param('suspend', 0, PARAM_INT);
    $unsuspend    = optional_param('unsuspend', 0, PARAM_INT);
   // $cohortid    = optional_param('cohortid', '', PARAM_INT);
    $schoolid    = required_param('schoolid', PARAM_INT);
  $returnurl = new moodle_url('/blocks/student_resource_center/status_update.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'page'=>$page, 'schoolid'=>$schoolid));

   // admin_externalpage_setup('editusers');
   global $OUTPUT,$PAGE,$USER;
require_login();
$PAGE->set_context(context_system::instance());
$PAGE->set_url($returnurl);
$PAGE->set_title(get_string('status_update', 'block_student_resource_center'));
$PAGE->set_heading(get_string('status_update', 'block_student_resource_center'));
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add(get_string('Student_Record_Update', 'block_student_resource_center'),new moodle_url('/blocks/student_resource_center/update_record.php'));
$PAGE->navbar->add(get_string('status_update', 'block_student_resource_center'),$returnurl);
$PAGE->navigation->clear_cache();

$context = get_context_instance(CONTEXT_COURSECAT, $schoolid);
$site = get_site();

require_capability('block/student_resource_center:updateuser', $context);      

    $stredit   = get_string('edit');
    $strdelete = get_string('delete');
    $strdeletecheck = get_string('deletecheck');
    $strshowallusers = get_string('showallusers');
    $strsuspend = get_string('suspenduser', 'admin');
    $strunsuspend = get_string('unsuspenduser', 'admin');
    $strconfirm = get_string('confirm');

    if (empty($CFG->loginhttps)) {
        $securewwwroot = $CFG->wwwroot;
    } else {
        $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
    }

  
    if ($confirmuser and confirm_sesskey()) {
    require_capability('block/student_resource_center:updateuser', $context);
    if (!$user = $DB->get_record('user', array('id'=>$confirmuser, 'mnethostid'=>$CFG->mnet_localhost_id))) {
    print_error('nousers');
        }

    $auth = get_auth_plugin($user->auth);

    $result = $auth->user_confirm($user->username, $user->secret);

        if ($result == AUTH_CONFIRM_OK or $result == AUTH_CONFIRM_ALREADY) {
            redirect($returnurl);
        } else {
            echo $OUTPUT->header();
            redirect($returnurl, get_string('usernotconfirmed', '', fullname($user, true)));
        }

    }
    else if ($delete and confirm_sesskey()) {              // Delete a selected user, after confirmation
        require_capability('block/student_resource_center:deletestudent', $context);

        $user = $DB->get_record('user', array('id'=>$delete, 'mnethostid'=>$CFG->mnet_localhost_id), '*', MUST_EXIST);

        if (is_siteadmin($user->id)) {
            print_error('useradminodelete', 'error');
        }

        if ($confirm != md5($delete)) {
            echo $OUTPUT->header();
            $fullname = fullname($user, true);
            echo $OUTPUT->heading(get_string('deleteuser', 'admin'));
            $optionsyes = array('delete'=>$delete, 'confirm'=>md5($delete), 'sesskey'=>sesskey());
            echo $OUTPUT->confirm(get_string('deletecheckfull', '', "'$fullname'"), new moodle_url($returnurl, $optionsyes), $returnurl);
            echo $OUTPUT->footer();
            die;
        } else if (data_submitted() and !$user->deleted) {
            if (delete_user($user)) {
                session_gc(); // remove stale sessions
                redirect($returnurl);
            } else {
                session_gc(); // remove stale sessions
                echo $OUTPUT->header();
                echo $OUTPUT->notification($returnurl, get_string('deletednot', '', fullname($user, true)));
            }
        }
    } 
    
      else if ($acl and confirm_sesskey()) {
     
        if (!has_capability('block/student_resource_center:updateuser', $context)) {
            print_error('nopermissions', 'error', '', 'modify the NMET access control list');
        }
        if (!$user = $DB->get_record('user', array('id'=>$acl))) {
            print_error('nousers', 'error');
        }
        if (!is_mnet_remote_user($user)) {
            print_error('usermustbemnet', 'error');
        }
        $accessctrl = strtolower(required_param('accessctrl', PARAM_ALPHA));
        if ($accessctrl != 'allow' and $accessctrl != 'deny') {
            print_error('invalidaccessparameter', 'error');
        }
        $aclrecord = $DB->get_record('mnet_sso_access_control', array('username'=>$user->username, 'mnet_host_id'=>$user->mnethostid));
        if (empty($aclrecord)) {
            $aclrecord = new stdClass();
            $aclrecord->mnet_host_id = $user->mnethostid;
            $aclrecord->username = $user->username;
            $aclrecord->accessctrl = $accessctrl;
            $DB->insert_record('mnet_sso_access_control', $aclrecord);
        } else {
            $aclrecord->accessctrl = $accessctrl;
            $DB->update_record('mnet_sso_access_control', $aclrecord);
        }
        $mnethosts = $DB->get_records('mnet_host', null, 'id', 'id,wwwroot,name');
        redirect($returnurl);

    } else if ($suspend and confirm_sesskey()) {
        require_capability('block/student_resource_center:suspendstudent', $context);

        if ($user = $DB->get_record('user', array('id'=>$suspend, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0))) {
            if (!is_siteadmin($user) and $USER->id != $user->id and $user->suspended != 1) {
                $user->suspended = 1;
                $user->timemodified = time();
                $DB->set_field('user', 'suspended', $user->suspended, array('id'=>$user->id));
                $DB->set_field('user', 'timemodified', $user->timemodified, array('id'=>$user->id));
                // force logout
                session_kill_user($user->id);
                events_trigger('user_updated', $user);
            }
        }
        redirect($returnurl);

    } else if ($unsuspend and confirm_sesskey()) {
        require_capability('block/student_resource_center:suspendstudent', $context);

        if ($user = $DB->get_record('user', array('id'=>$unsuspend, 'mnethostid'=>$CFG->mnet_localhost_id, 'deleted'=>0))) {
            if ($user->suspended != 0) {
                $user->suspended = 0;
                $user->timemodified = time();
                $DB->set_field('user', 'suspended', $user->suspended, array('id'=>$user->id));
                $DB->set_field('user', 'timemodified', $user->timemodified, array('id'=>$user->id));
                events_trigger('user_updated', $user);
            }
        }
        redirect($returnurl);
    }

    // create the user filter form
   // $ufiltering = new user_filtering();
    echo $OUTPUT->header();
     
     echo '<span ><button  style="background-color:orange;color:white" class="button_show" type="button">View/Hide Options</button></span>';
  
echo '<br/><br/>';
$co_name = $_GET["cohort"];
       echo '<div class="userlist1">';
       if(isset($co_name)){
  //echo $_GET["cohort"];
 $q1 = "SELECT name FROM {cohort} WHERE id = $co_name";
 $class = $DB->get_record_sql($q1);
       }
       else
       {
           $class = new stdClass();
           $class->name = '';
       }
       
       
        echo '<form  action="'.$CFG->wwwroot.'/blocks/student_resource_center/status_update.php?schoolid='.$schoolid.'" id="select_form" method="GET" >';
        $record = $DB->get_record('course_categories',array('id' => $schoolid));
        echo '<p id='.$schoolid.' align="center"><font size="4"><b>'.$record->name."</br>".$class->name.'</b></font></p>';    

        echo '<table width="100%">';
         $sql1 = "SELECT * FROM {course_categories} WHERE parent = $schoolid" ;
        $result = $DB->get_records_sql($sql1);
        echo '<tr align="center"><td style="padding-right:50px;">';
        echo   '<b>Choose a Program : </b>';
        echo     '<select id="program">';
        echo '<option value="0">choose...</option>';
        foreach($result as $r)     {
        echo '<option value="' . $r->id . '">' . $r->name . '</option>';
    }
 
        echo    '</select>';
      
     
        echo       '<b> Choose a Degree: </b>';
        echo  '<select id="degree">
                <option value="0">choose...</option>
            </select>';
        echo     '</tr>';
        echo '<tr align="center"><td>';
        echo       ' <b>Choose a Batch:</b> ';
        echo  '<select id="batch">
                <option value="0">choose...</option>
            </select>';
          $sq = "SELECT coho.id,coho.name FROM `mdl_cohort` AS coho
JOIN `mdl_context` AS con 
     ON con.id = coho.contextid
JOIN `mdl_course_categories` AS cou
     ON con.instanceid = cou.id
WHERE cou.id = $schoolid";
        $cohorts = $DB->get_records_sql($sq); 
        echo       '<b> Choose a Cohort:  &nbsp<b>';
        //echo '<option value="" selected="selected"></option>';
        echo  '<select name="cohort" id="cohort">';
          echo    '<option value="0">choose...</option>';
               foreach($cohorts as $r)     {
        echo '<option value="' . $r->id . '">' . $r->name . '</option>';
    }
 
          
        echo    '</select>';
       
     
        echo "<input type='hidden' name='schoolid' value='$schoolid'/>";
        echo '</td></tr>';        
        echo '<tr><td>';
        echo   '<center><input style="width:150;height:25" class="button_contact" type="submit" value="Get the list of students" /></center>';
        echo '</td></tr>';
        echo '</form>';
        echo '</table>';
    
 
        echo '</div>';
    
 

    // Carry on with the user listing
    $systemcontext = context_system::instance();
    $extracolumns = get_extra_user_fields($systemcontext);
    $columns = array_merge(array('firstname', 'lastname'), $extracolumns,
            array('city', 'country', 'lastaccess'));

    foreach ($columns as $column) {
        $string[$column] = get_user_field_name($column);
        if ($sort != $column) {
            $columnicon = "";
            if ($column == "lastaccess") {
                $columndir = "DESC";
            } else {
                $columndir = "ASC";
            }
        } else {
            $columndir = $dir == "ASC" ? "DESC":"ASC";
            if ($column == "lastaccess") {
                $columnicon = ($dir == "ASC") ? "sort_desc" : "sort_asc";
            } else {
                $columnicon = ($dir == "ASC") ? "sort_asc" : "sort_desc";
            }
            $columnicon = "<img class='iconsort' src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

        }
        $$column = "<a href=\"user.php?sort=$column&amp;dir=$columndir\">".$string[$column]."</a>$columnicon";
    }

    if ($sort == "name") {
        $sort = "firstname";
    }

 
  // $users = get_users_listing($sort, $dir, $page*$perpage, $perpage, '', '', '',
        // $extrasql, $params, $context);
   // print_object($users);
    //Ayesha
    $sql = "SELECT u.id,u.username,u.email, u.firstname, u.lastname,u.idnumber, u.user_group, u.user_subgroup,
                           u.city, u.country, u.lastaccess, u.confirmed, u.mnethostid, u.suspended
                           FROM {user} u
JOIN {cohort_members} cm ON cm.userid = u.id
JOIN {cohort} c ON c.id = cm.cohortid ";
    if(isset($_GET["cohort"]) && !empty($_GET["cohort"])){
$sql .= 'WHERE c.id ='. $_GET["cohort"];
    }
    else{
        $sql .= 'WHERE c.id = 1';
    }
    
    $users = $DB->get_records_sql($sql);

//    $usersearchcount = $DB->count_records_sql($sql); 
     $usersearchcount = count($users);
    $usercount = get_users(false);
   //$usersearchcount = get_users(false, '', false, null, "", '', '', '', '', '*', $extrasql, $params);

    //if ($extrasql !== '') {
        echo $OUTPUT->heading("$usersearchcount / $usercount ".get_string('users'));
       // $usercount = $usersearchcount;
   // } else {
      //echo $OUTPUT->heading("$usercount ".get_string('users'));
   // }

    $strall = get_string('all');

    $baseurl = new moodle_url('/blocks/student_resource_center/status_update.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage,'schoolid'=>$schoolid));
    echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);

    flush();


    if (!$users) {
        $match = array();
        echo $OUTPUT->heading(get_string('nousersfound'));

        $table = NULL;

    } else {

        $countries = get_string_manager()->get_list_of_countries(false);
        if (empty($mnethosts)) {
            $mnethosts = $DB->get_records('mnet_host', null, 'id', 'id,wwwroot,name');
        }

        foreach ($users as $key => $user) {
            if (isset($countries[$user->country])) {
                $users[$key]->country = $countries[$user->country];
            }
        }
        if ($sort == "country") {  // Need to resort by full country name, not code
            foreach ($users as $user) {
                $susers[$user->id] = $user->country;
            }
            asort($susers);
            foreach ($susers as $key => $value) {
                $nusers[] = $users[$key];
            }
            $users = $nusers;
        }

        $override = new stdClass();
        $override->firstname = 'firstname';
        $override->lastname = 'lastname';
        $fullnamelanguage = get_string('fullnamedisplay', '', $override);
        if (($CFG->fullnamedisplay == 'firstname lastname') or
            ($CFG->fullnamedisplay == 'firstname') or
            ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'firstname lastname' )) {
            $fullnamedisplay = "$firstname / $lastname";
        } else { // ($CFG->fullnamedisplay == 'language' and $fullnamelanguage == 'lastname firstname')
            $fullnamedisplay = "$lastname / $firstname";
        }

        $table = new html_table();
        $table->head = array ();
        $table->align = array();
        $table->head[] = $fullnamedisplay;
        $table->align[] = 'left';
        foreach ($extracolumns as $field) {
            $table->head[] = ${$field};
            $table->align[] = 'left';
        }
        $table->head[] = $city;
        $table->align[] = 'left';
        $table->head[] = $country;
        $table->align[] = 'left';
        $table->head[] = $lastaccess;
        $table->align[] = 'left';
        $table->head[] = get_string('edit');
        $table->align[] = 'center';
     //   $table->head[] = "";
     //   $table->align[] = 'center';

        $table->width = "95%";
        foreach ($users as $user) {
            if (isguestuser($user)) {
                continue; // do not display guest here
            }

            $buttons = array();
            $lastcolumn = '';
/*
            // delete button
            if (has_capability('block/student_resource_center:deletestudent', $context)) {
             
                    $buttons[] = html_writer::link(new moodle_url($returnurl, array('delete'=>$user->id, 'sesskey'=>sesskey())), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'), 'alt'=>$strdelete, 'class'=>'iconsmall')), array('title'=>$strdelete));
                 add_to_log($site->id,"delete",$user->username,$USER->id);
                      
                    
            }
 * 
 */

            // suspend button
            if (has_capability('block/student_resource_center:suspendstudent', $context)) {
              
                    if ($user->suspended) {
                        $buttons[] = html_writer::link(new moodle_url($returnurl, array('unsuspend'=>$user->id, 'sesskey'=>sesskey())), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/show'), 'alt'=>$strunsuspend, 'class'=>'iconsmall')), array('title'=>$strunsuspend));
                   add_to_log($site->id,"unsuspend",$user->username,$USER->id);
                        } else {
                        if ($user->id == $USER->id or is_siteadmin($user)) {
                            // no suspending of admins or self!
                        } else {
                            $buttons[] = html_writer::link(new moodle_url($returnurl, array('suspend'=>$user->id, 'sesskey'=>sesskey())), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/hide'), 'alt'=>$strsuspend, 'class'=>'iconsmall')), array('title'=>$strsuspend));
                           add_to_log($site->id,"suspend",$user->username,$USER->id);
                      
                            
                    }

                }
            }
            

            // edit button
            if (has_capability('block/student_resource_center:editprofile', $context)) {
                // prevent editing of admins by non-admins
                if (is_siteadmin($USER) or !is_siteadmin($user)) {
                    $buttons[] = html_writer::link(new moodle_url($securewwwroot.'/user/editadvanced.php', array('id'=>$user->id, 'course'=>$site->id, 'schoolid'=>$schoolid)), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/edit'), 'alt'=>$stredit, 'class'=>'iconsmall')), array('title'=>$stredit));
                }
            }

             if ($user->confirmed == 0) {
                if (has_capability('block/student_resource_center:addinstance', $context)) {
                    $lastcolumn = html_writer::link(new moodle_url($returnurl, array('confirmuser'=>$user->id, 'sesskey'=>sesskey())), $strconfirm);
                } else {
                    $lastcolumn = "<span class=\"dimmed_text\">".get_string('confirm')."</span>";
                }
            }

            if ($user->lastaccess) {
                $strlastaccess = format_time(time() - $user->lastaccess);
            } else {
                $strlastaccess = get_string('never');
            }
            $fullname = fullname($user, true);

            $row = array ();
            $row[] = "<a href=\"../../user/view.php?id=$user->id&amp;course=$site->id\">$fullname</a>";
            foreach ($extracolumns as $field) {
                $row[] = $user->{$field};
            }
            $row[] = $user->city;
            $row[] = $user->country;
            $row[] = $strlastaccess;
            if ($user->suspended) {
                foreach ($row as $k=>$v) {
                    $row[$k] = html_writer::tag('span', $v, array('class'=>'usersuspended'));
                }
            }
            $row[] = implode(' ', $buttons);
       //     $row[] = $lastcolumn;
            $table->data[] = $row;
        }
    }

    
    if (!empty($table)) {
        echo html_writer::table($table);
        echo $OUTPUT->paging_bar($usercount, $page, $perpage, $baseurl);
      
    }
 


  //  echo $OUTPUT->single_button(new moodle_url('/blocks/student_resource_center/print_ion.php'), get_string('print_ion', 'block_student_resource_center'));
  //  echo $OUTPUT->single_button(new moodle_url('/blocks/student_resource_center/get_logs.php'), get_string('get_logs', 'block_student_resource_center'));
    
                
    echo $OUTPUT->footer();