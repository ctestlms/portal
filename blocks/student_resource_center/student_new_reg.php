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

$(".button_glow").click(function() {
    
var id= 1;
 $.post("mail_pending_ug.php", {id:id}, function(response) {
         alert ("successfully mailed");
});
});

$(".button_glow1").click(function() {
    
var id= 1;
 $.post("mail_pending_pg.php", {id:id}, function(response) {
         alert ("successfully mailed");
});
});



});
    
</script>
<?php

//  Lists all the users within a given course
    require_once('../../config.php');
    require_once($CFG->libdir.'/tablelib.php');
    require_once($CFG->libdir.'/filelib.php');
    include('get_childcats.php');
    require_once($CFG->dirroot.'/user/profile/lib.php');
    include('func.php');
    
    define('USER_SMALL_CLASS', 20);   // Below this is considered small
    define('USER_LARGE_CLASS', 200);  // Above this is considered large
    define('DEFAULT_PAGE_SIZE', 500);
    define('SHOW_ALL_PAGE_SIZE', 5000);

    $page         = optional_param('page', 0, PARAM_INT);                     // which page to show
    $perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);              // make sure it is processed with p() or s() when sending to output!
    $schoolid       = required_param('schoolid', PARAM_INT);
    //  $status       = optional_param('status', 'not joined', PARAM_RAW); 
 //print_object($school);

    global $DB,$USER;
  //  $contextex = get_context_instance (CONTEXT_SYSTEM);
      $context = get_context_instance(CONTEXT_COURSECAT, $schoolid);
     
     // print_object($context);
    require_capability('block/student_resource_center:addinstance', $context);
  
  // Should use this variable so that we don't break stuff every time a variable is added or changed.
    $baseurl = new moodle_url('/blocks/student_resource_center/student_new_reg.php', array(
          'page' => $page,
            'perpage' => $perpage,
                'schoolid'      => $schoolid ));
          // 'status' => $status ));
 

 
$PAGE->set_context($context);
    $PAGE->set_title("Registration");
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading("Registration");
//$PAGE->navbar->add(get_string('myhome', 'block_student_resource_center'),new moodle_url('/my/'));
$PAGE->navbar->add(get_string('Student_Registration', 'block_student_resource_center'),new moodle_url('/blocks/student_resource_center/student_new_reg.php?schoolid='.$schoolid));

    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    
 

     echo '<span ><button  style="background-color:orange;color:white" class="button_show" type="button">View/Hide Options</button></span>';
   echo '<span style="float:right;"><button style="background-color:orange;color:white" class="button_glow" type="button">Mail UG Pending Students</button>
        <button style="background-color:orange;color:white" class="button_glow1" type="button">Mail PG Pending Students</button></span>';
echo '<br/><br/>';
       echo '<div class="userlist1">';
   
 
        echo '<form  action="'.$CFG->wwwroot.'/blocks/student_resource_center/student_new_reg.php?schoolid='.$schoolid.'" id="select_form" method="GET" >';
        $record = $DB->get_record('course_categories',array('id' => $schoolid));
        echo '<p id='.$schoolid.' align="center"><font size="4"><b>'.$record->name.'</b></font></p>';    

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
  

    $tablecolumns = array('meritno', 'fullname', 'registrationstatus');
  //  $extrafields = get_extra_user_fields($systemcontext);
    $tableheaders = array('Merit no','Full Name', 'Status');
        
       
       // print_object($tablecolumns);
    $table = new flexible_table('user-index-participants-');
    $table->define_columns($tablecolumns);
    $table->define_headers($tableheaders);
    $table->define_baseurl($baseurl->out());

    $table->no_sorting('roles');
    $table->no_sorting('groups');
    $table->no_sorting('groupings');
    $table->no_sorting('select');


    $table->set_attribute('align', 'center');
    $table->set_attribute('cellspacing', '0');
    $table->set_attribute('id', 'joining_table');
    $table->column_style('registrationstatus', 'width', '25px');
    $table->column_style('meritno', 'width', '5px');
    $table->column_style('meritno', 'text-align', 'center');
    $table->column_style('registrationstatus', 'text-align', 'center');
    $table->column_style('fullname', 'width', '20px');
    //$table->set_attribute('class', 'generaltable generalbox');
    $table->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');
    
    $table->set_control_variables(array(
                TABLE_VAR_SORT    => 'ssort',
                TABLE_VAR_HIDE    => 'shide',
                TABLE_VAR_SHOW    => 'sshow',
                TABLE_VAR_IFIRST  => 'sifirst',
                TABLE_VAR_ILAST   => 'silast',
                TABLE_VAR_PAGE    => 'spage'
                ));
    $table->setup();

    $joins = array("FROM {user} u");
    $wheres = array();

    
$select = "SELECT u.id,u.idnumber, u.username, u.firstname, u.lastname, u.user_group";
$joins[] = "JOIN {cohort_members} cm ON cm.userid = u.id";
$joins[] = "JOIN {cohort} c ON c.id = cm.cohortid";

    // performance hacks - we preload user contexts together with accounts
    list($ccselect, $ccjoin) = context_instance_preload_sql('u.id', CONTEXT_USER, 'ctx');
    $select .= $ccselect;
    $joins[] = $ccjoin;


       $wheres[] = "c.id = :cohort";
       if(isset($_GET["cohort"]) && !empty($_GET["cohort"]))
       {
        $params['cohort'] = $_GET["cohort"];
       }
       else
       {
           $params['cohort'] = 1;
       }
      

    $from = implode("\n", $joins);
    if ($wheres) {
        $where = "WHERE " . implode(" AND ", $wheres);
    } else {
        $where = "";
    }

    $totalcount = $DB->count_records_sql("SELECT COUNT(u.id) $from $where", $params);


    list($twhere, $tparams) = $table->get_sql_where();
    if ($twhere) {
        $wheres[] = $twhere;
        $params = array_merge($params, $tparams);
    }

    $from = implode("\n", $joins);
    if ($wheres) {
        $where = "WHERE " . implode(" AND ", $wheres);
    } else {
        $where = "";
    }

    if ($table->get_sql_sort()) {
        $sort = ' ORDER BY '.$table->get_sql_sort();
    } else {
        $sort = 'ORDER BY u.idnumber ASC';
    }

    $matchcount = $DB->count_records_sql("SELECT COUNT(u.id) $from $where", $params);
    $table->pagesize($perpage, $matchcount);
    
    // list of users at the current visible page - paging makes it relatively short
    $userlist = $DB->get_recordset_sql("$select $from $where $sort", $params, $table->get_page_start(), $table->get_page_size());

        if ($userlist)  {
    
     
            foreach ($userlist as $user) {
      
    $record = $DB->get_record('user',array('id'=>$user->id));  
    profile_load_data($record);
$link = new moodle_url ("$CFG->wwwroot/blocks/student_resource_center/joining_docs.php?userid=$user->id&user_group=$user->user_group");
  $profilelink =  $OUTPUT->action_link($link, fullname($user), new popup_action ('click', $link, null, array('height' => 680, 'width' => 700)));               

                $data = array ($user->idnumber,$profilelink);
                        if($record->profile_field_registrationstatus=='joined'){
                       $data[] .= '<font color="green"><b>'.$record->profile_field_registrationstatus.'<b></font>';}
                       else if($record->profile_field_registrationstatus=='pending'){
                        $data[] .= '<font color="orange"><b>'.$record->profile_field_registrationstatus.'<b></font>';}
                        else{
            $data[] .= '<font color="red"><b>'.$record->profile_field_registrationstatus.'<b></font>';}
         
      
          $table->add_data($data);
            }
        }
    //     echo'</br></br></br>'; 
        $table->print_html();
  //echo '</br>';
   //    echo '<button style="background-color:orange;" class="button_glow" type="button" align="right">Mail Pending Students</button>';
       
        $module = array('name'=>'core_user', 'fullpath'=>'/user/module.js');
        $PAGE->requires->js_init_call('M.core_user.init_participation', null, false, $module);

    $perpageurl = clone($baseurl);
    $perpageurl->remove_params('perpage');
    if ($perpage == SHOW_ALL_PAGE_SIZE) {
        $perpageurl->param('perpage', DEFAULT_PAGE_SIZE);
        echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showperpage', '', DEFAULT_PAGE_SIZE)), array(), 'showall');

    } else if ($matchcount > 0 && $perpage < $matchcount) {
        $perpageurl->param('perpage', SHOW_ALL_PAGE_SIZE);
        echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showall', '', $matchcount)), array(), 'showall');
    }

 
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();

   if ($userlist) {
        $userlist->close();
    }
    
 
