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
 * Manual user enrolment UI.
 *
 * @package    enrol_manual
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/enrol/manual/locallib.php');
require_once($CFG->dirroot.'/mod/feedback/lib.php');
$enrolid      = required_param('enrolid', PARAM_INT);
$roleid       = optional_param('roleid', -1, PARAM_INT);
$extendperiod = optional_param('extendperiod', 0, PARAM_INT);
$extendbase   = optional_param('extendbase', 3, PARAM_INT);
$role_id      = optional_param('role_id', -1, PARAM_INT);
$sort_        = optional_param('sort_', "", PARAM_INT);
$grp             = optional_param('grp', "", PARAM_ALPHANUM);//get user group
$sgrp            = optional_param('sgrp', "", PARAM_ALPHANUM);//get user sub group.
$section           = optional_param('section', "", PARAM_ALPHANUM);//get course sections - added by Qurrat-ul-ain
$contextids           = optional_param('contextids', "", PARAM_INT);//get course contextids - added by Qurrat-ul-ain
$instance = $DB->get_record('enrol', array('id'=>$enrolid, 'enrol'=>'manual'), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$instance->courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id, MUST_EXIST);

require_login($course);
require_capability('enrol/manual:enrol', $context);
require_capability('enrol/manual:manage', $context);
require_capability('enrol/manual:unenrol', $context);

if ($roleid < 0) {
    $roleid = $instance->roleid;
}
if ($role_id < 0) {
       $role_id = $instance->role_id;
}

$roles = get_assignable_roles($context);
$roles = array('0'=>get_string('none')) + $roles;
$roles1 = get_assignable_roles($context);
$roles1 = array('0'=>get_string('all')) + $roles1;
$sortby = array('0'=>get_string('none'),'1'=>"Name",'2'=>"Reg No",'3'=>"UID") ;
if (!isset($roles[$roleid])) {
    // Weird - security always first!
    $roleid = 0;
}

if (!isset($sortby[$sort])) {
       // weird - security always first!
       $sort = 0;
}
if (!$enrol_manual = enrol_get_plugin('manual')) {
    throw new coding_exception('Can not instantiate enrol_manual');
}

$instancename = $enrol_manual->get_instance_name($instance);

$PAGE->set_url('/enrol/manual/manage.php', array('enrolid'=>$instance->id));
$PAGE->set_pagelayout('admin');
$PAGE->set_title($enrol_manual->get_instance_name($instance));
$PAGE->set_heading($course->fullname);
navigation_node::override_active_url(new moodle_url('/enrol/users.php', array('id'=>$course->id)));

// Create the user selector objects.
$options = array('enrolid' => $enrolid, 'accesscontext' => $context);

$potentialuserselector = new enrol_manual_potential_participant('addselect', $options);
$currentuserselector = new enrol_manual_current_participant('removeselect', $options);

// Build the list of options for the enrolment period dropdown.
$unlimitedperiod = get_string('unlimited');
$periodmenu = array();
for ($i=1; $i<=365; $i++) {
    $seconds = $i * 86400;
    $periodmenu[$seconds] = get_string('numdays', '', $i);
}
// Work out the apropriate default setting.
if ($extendperiod) {
    $defaultperiod = $extendperiod;
} else {
    $defaultperiod = $instance->enrolperiod;
}

// Build the list of options for the starting from dropdown.
$timeformat = get_string('strftimedatefullshort');
$today = time();
$today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);

// Enrolment start.
$basemenu = array();
if ($course->startdate > 0) {
    $basemenu[2] = get_string('coursestart') . ' (' . userdate($course->startdate, $timeformat) . ')';
}
$basemenu[3] = get_string('today') . ' (' . userdate($today, $timeformat) . ')' ;

// Process add and removes.
if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoassign = $potentialuserselector->get_selected_users();
    if (!empty($userstoassign)) {
        foreach($userstoassign as $adduser) {
            switch($extendbase) {
                case 2:
                    $timestart = $course->startdate;
                    break;
                case 3:
                default:
                    $timestart = $today;
                    break;
            }

            if ($extendperiod <= 0) {
                $timeend = 0;
            } else {
                $timeend = $timestart + $extendperiod;
            }
            $enrol_manual->enrol_user($instance, $adduser->id, $roleid, $timestart, $timeend);
            add_to_log($course->id, 'course', 'enrol', '../enrol/users.php?id='.$course->id, $course->id); //there should be userid somewhere!
			if($roleid==3){
                                $username=$DB->get_record("user", array("id" => $adduser->id)) ;
                                for( $i=0;$i<2;$i++){

                                        $feedback=new stdClass();
                                        $feedback->timemodified = time();
                                        $feedback->id = '';
                                        $feedback->course = $course->id;
                                        if($i==0){
                                                $feedback->name  = "First Student Feedback"."(".$username->firstname." ".$username->lastname.")";
                                                $feedback->intro = "First Student Feedback";
                                                $feedback->timeopen = strtotime("+42 days", $courserecord->timemodified);
                                                $feedback->timeclose = strtotime("+14 days", $feedback->timeopen);
                                        }
                                        if($i==1){
                                                $feedback->name  = "Second Student Feedback"."(".$username->firstname." ".$username->lastname.")";;
                                                $feedback->intro = "Second Student Feedback";
                                                $feedback->timeopen = strtotime("+60 days", $courserecord->timemodified);
                                                $feedback->timeclose = strtotime("+14 days", $feedback->timeopen);
                                        }
                                        $feedback->introformat = 1;
                                        $feedback->anonymous = 1;
                                        $feedback->email_notification = 0;
                                        $feedback->multiple_submit = 0;
                                        $feedback->autonumbering = 1;
                                        $feedback->site_after_submit = "";
                                        $feedback->page_after_submit = "<p>Thankyou for your feedback!!!</p>";
                                        $feedback->page_after_submitformat = 1;
                                        $feedback->publish_stats = 0;
                                        $feedback->completionsubmit = 0;
                                        $feedbackid = $DB->insert_record("feedback", $feedback);
                                        $feedback->id = $feedbackid;
                                        $xmlcontent = file_get_contents($CFG->dirroot.'/feedback.xml', true);
										if(!$xmldata = feedback_load_xml_datap($xmlcontent)) {
                                                print_error('cannotloadxml', 'feedback', 'edit.php?id='.$id);
                                        }

                                        $importerror = feedback_import_loaded_datap($xmldata, $feedback->id);

                                        if (! $module = $DB->get_record("modules", array("name" => "feedback"))) {
                                                echo $OUTPUT->notification("Could not find feedback module!!");
                                                return false;
                                        }
                                        $mod = new stdClass();
                                        $mod->course = $course->id;
                                        $mod->module = $module->id;
                                        $mod->instance = $feedback->id;
                                        $mod->section = 0;
										include_once("$CFG->dirroot/course/lib.php");
                                        if (! $mod->coursemodule = add_course_module($mod) ) {   // assumes course/lib.php is loaded
                                                echo $OUTPUT->notification("Could not add a new course module to the course '" . $courseid . "'");
                                                return false;
                                        }
                                        if (! $sectionid = add_mod_to_section($mod) ) {   // assumes course/lib.php is loaded
                                                echo $OUTPUT->notification("Could not add the new course module to that section");
                                                return false;
                                        }
                                        $DB->set_field("course_modules", "section", $sectionid, array("id" => $mod->coursemodule));
                                        include_once("$CFG->dirroot/course/lib.php");
                                        rebuild_course_cache($course->id);
                                }
                        }
        }

        $potentialuserselector->invalidate_selected_users();
        $currentuserselector->invalidate_selected_users();

        //TODO: log
    }
}

$context = context_course::instance($course->id, MUST_EXIST);
// Process incoming role unassignments.
if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstounassign = $currentuserselector->get_selected_users();
	
    if (!empty($userstounassign)) {
	$usernamer = '';
	foreach($userstounassign as $removeuser) {
		$role = $DB->get_records_sql("SELECT roleid FROM {role_assignments} ra WHERE ra.userid =$removeuser->id AND contextid =$context->id"); 
		//print_r($role);
		$roleids = array();
		$i = 0;
		foreach($role as $id) {
			$check = $id->roleid;
			if ($check == 3) {
				$roleids[$i] = 3;
				$users = $DB->get_record_sql("SELECT firstname, lastname FROM {user} WHERE id =$removeuser->id");
				if($usernamer == "")					
					$usernamer = $users->firstname."  ".$users->lastname;
				else
					$usernamer .= ",".$users->firstname."  ".$users->lastname;
			}
			$i++;
		}
		$enrol_manual->unenrol_user($instance, $removeuser->id);
    		add_to_log($course->id, 'course', 'unenrol', '../enrol/users.php?id='.$course->id, $course->id); //there should be userid somewhere!
        }
		
        $potentialuserselector->invalidate_selected_users();
        $currentuserselector->invalidate_selected_users();

        //TODO: log
    }
	
}

echo $OUTPUT->header();
echo $OUTPUT->heading($instancename);
// To select enrolled users of other sections - added by Qurrat-ul-ain
$thiscourseid = $course->id;
//echo $thiscourseid."<br/>";
$thiscoursefullname = $course->fullname;
// echo $thiscoursefullname."<br/>";
$coursename = explode(" ", $course->fullname, 2);
$coursecode = $coursename[0];
// echo $coursecode."<br/>";
// echo $course->category."<br/>";
$semester = $DB->get_record_sql("select id,name,path from {course_categories} where id=$course->category");
$path = explode("/", $semester->path);
if (strstr($semester->name, "Summer") == false) {
    if($path[4]!="")
     $batch = $DB->get_record_sql("select id,name from {course_categories} where id=$path[4]");
}
$thiscoursecode = explode(" ", $course->fullname);
$sql = "SELECT firstname,lastname,u.id, username
FROM mdl_user u
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_role r ON ra.roleid = r.id
JOIN mdl_context c ON ra.contextid = c.id
WHERE r.name = 'Teacher'
AND c.contextlevel =50
AND c.instanceid=$course->id ";
$teacher = $DB->get_record_sql($sql);

if (isset($teacher->id)) {
    $sql = "SELECT cc.*
	FROM mdl_user u
	JOIN mdl_role_assignments ra ON ra.userid = u.id
	JOIN mdl_role r ON ra.roleid = r.id
	JOIN mdl_context c ON ra.contextid = c.id
	JOIN mdl_course cc ON c.instanceid = cc.id
	WHERE r.name = 'Teacher'
	and u.id=$teacher->id
	AND c.contextlevel =50 and cc.startdate>=$course->startdate and cc.category=$semester->id group by c.id;";
	$courses = $DB->get_records_sql($sql);
	//print_r($courses); //Shami
	//echo "<br/>";
	
	if ($courses)
	{
		$sections = array();
		$i = 0;
		$contextids = "";
		foreach ($courses as $key => $course) {
			//echo "Course ID: ".$course->id."<br/>";
			
			if ($thiscourseid == $course->id)
			{
				//do nothing
			}
			else
			{
				$coursecode = explode(" ", $course->fullname);
				if (strpos($coursecode[1],'Elective') !== false) {
					$coursename = explode("-", $course->fullname);
					$coursename = $coursename[0]; 
				}
				else
					$coursename = $course->fullname;
				
				$coursesection = substr($coursename, -2);
				$sections[$i] = $coursesection[0]; 
				//echo "Course Section $coursesection :  $sections[$i]<br/>";
				
				if ($thiscoursecode[0] != $coursecode[0]) {
					$thiscoursecode[0] . "--" . $coursecode[0] . "==" . $key;
					unset($courses[$key]);
					//echo "Un equal IDs<br/>";
					continue;
				}
				
				$courseids.= $course->id . ",";
				$contexts = get_context_instance(CONTEXT_COURSE, $course->id);
				//$contextids.=$contexts->id . ",";	
				if($contextids == "") {
					$contextids = $contexts->id . ",";
				}
				else {
					$contextids .= $contexts->id . ",";	
				}
				$i++;
			}
			
		}
		$courseids = rtrim($courseids, ",");
		$contextids = rtrim($contextids, ",");
		// echo "Course IDs: ".$courseids."<br/>"; // Shami
		// echo "Context IDs: ".$contextids."<br/>"; // Qurrat
	}
	else
	{
	}
    
} else {
    $courseids = $course->id;
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $contextids = $context->id;
}

echo "<div style='text-align: center;'>";
	echo "<select id='section' name='section'>";                      
	echo "<option value='0'>--Select Section--</option>";
	foreach($sections as $section)
		echo "<option value='$section' id='$section'>$coursecode[0] -- $section</option>";  
	echo "</select>";
echo "</div>";
//end

//Start of LDAP filter script.
echo "<div style='text-align: center;'>";
$query = "select distinct user_group from {$CFG->prefix}user where user_group NOT IN ('', 'NULL')";
$path = $CFG->wwwroot."/enrol/manual/manage.php?enrolid=".$enrolid;
if($groups = $DB->get_records_sql($query)){
        echo "<select name='grp' id='grp'>";
        echo "<option value=''>.::Select::.</option>";
        foreach ($groups as $group){
                $selected = ($group->user_group == $grp) ? "selected = 'selected'" : "";
                echo "<option value='{$group->user_group}' {$selected} >{$group->user_group}</option>";
        }
        echo "</select>";
        //Get subgroups
        if($grp != ""){
                $query = "select distinct user_subgroup from {$CFG->prefix}user where user_group = '{$grp}'";
                if($sub_groups = $DB->get_records_sql($query)){
                        echo "<select name='sgrp' id='sgrp'>";
                        echo "<option value=''>.::Select::.</option>";
                        foreach ($sub_groups as $sub_group){
                                $selected = ($sub_group->user_subgroup == $sgrp) ? "selected = 'selected'" : "";
                                echo "<option value='{$sub_group->user_subgroup}' {$selected}>{$sub_group->user_subgroup}</option>";
                        }
                        echo "</select>";
                }
        }
}
?>
<!-- <script type="text/javascript" src="jquery-1.3.2.js"></script> -->
<script type="text/javascript">
        $(document).ready(function(){
                $("select#grp").change(function(){
                        if($(this).val() != "")
                                document.location = "<?php echo $path; ?>&grp="+$(this).val();
                });
                $("select#sgrp").change(function(){
                        if($(this).val() != "" && $("select#grp").val() != "")
                                document.location = "<?php echo $path; ?>&grp="+$("select#grp").val()+"&sgrp="+$(this).val();
                });
				// Added by Qurrat-ul-ain
				$("select#section").change(function(){
                        if($(this).val() != "")
                                document.location = "<?php echo $path; ?>&section="+$(this).val()+"&contextids="+<?php echo $contextids; ?>;
                });
				//Changes by Miss. Hina 14-10-2011
              $("select#sort").change(function(){
                         if($(this).val() != "" ){
                                 if($("select#sgrp").val() != "" && $("select#grp").val() != ""){
                              document.location = "<?php echo $path; ?>&grp="+$("select#grp").val()+"&sgrp="+$("select#sgrp").val()+"&sort_="+$(this).val();
                         }
                         if($("select#sgrp").val() == "" && $("select#grp").val() != ""){
                    document.location = "<?php echo $path; ?>&grp="+$("select#grp").val()+"&sort_="+$(this).val();
                }
                if($("select#sgrp").val() == "" && $("select#grp").val() == ""){            
                    document.location = "<?php echo $path; ?>&sort_="+$(this).val();
                }
                
            }
        });
        });

		
		
</script>
</div>

<form id="assignform" method="post" action="<?php echo $PAGE->url ?>"><div>
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />

  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
	<td colspan="2">
                        <h2>Current role:</h2>
                        <?php echo html_writer::select($roles1, 'role_id', $role_id, false,array('onchange' => 'this.form.submit()')); ?>
                </td>
	<td>            <b>Sort and Display:</b>             <select
                                       name="sort" id="sort">                
                                               <option value="0"
                                               <?php if($sort_ == 0) echo "selected = 'selected'"; ?>>All</option>                                                                  
                                               <option value="1"
                                               <?php if($sort_ == 1) echo "selected = 'selected'"; ?>>Name</option>
                                                                              
                                               <option value="2"
                                               <?php if($sort_ == 2) echo "selected = 'selected'"; ?>>UID</option>
                                                              
                                               <option value="3"
                                               <?php if($sort_ == 3) echo "selected = 'selected'"; ?>>Email</option>
                                                          
                               </select>             </td>
                       </tr>
                       </td>
                       <td></td>

</tr>
    <tr>
		<td id="existingcell">
		  <p><label for="removeselect"><?php print_string('enrolledusers', 'enrol'); ?></label></p>
		  <?php $currentuserselector->display() ?>
		</td>
		<td id="buttonscell">
		  <div id="addcontrols">
			  <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />

			  <div class="enroloptions">

			  <p><label for="menuroleid"><?php print_string('assignrole', 'enrol_manual') ?></label><br />
			  <?php echo html_writer::select($roles, 'roleid', $roleid, false); ?></p>

			  <p><label for="menuextendperiod"><?php print_string('enrolperiod', 'enrol') ?></label><br />
			  <?php echo html_writer::select($periodmenu, 'extendperiod', $defaultperiod, $unlimitedperiod); ?></p>

			  <p><label for="menuextendbase"><?php print_string('startingfrom') ?></label><br />
			  <?php echo html_writer::select($basemenu, 'extendbase', $extendbase, false); ?></p>

			  </div>
		  </div>

		  <div id="removecontrols">
			  <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('remove'); ?>" />
			  <input type="hidden" id="faculty" name="faculty" value="<?php if(isset($usernamer)){echo $usernamer;} else "";?>" />
		  </div>
			<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/ui-darkness/jquery-ui.css" rel="stylesheet">
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
			<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
			<div id="dialog" title="Delete Faculty Feedback" style="display:none;">
				<p>Do you want to delete the faculty feedbacks for <?php echo "$usernamer" ?> along with unenrolment?</p>
				<input type="hidden" id="courseid" name="courseid" value="<?php echo $course->id;?>" />
			</div>
			<div id="deletesuccess" title="Faculty Feedback Deletion" style="display:none;">
				<p>The selected faculty feedbacks have been deleted successfully</p>
			</div>
			<div id="cblist" style="display: none;">
			</div>
			<script>
			/*
			* Added by Qurrat-ul-ain Babar (11th June, 2014)
			* For removing feedback upon teacher's unenrolment
			* Asks the user which feedback to delete
			*
			*/
			var fac = new Array();
			
			function addCheckbox(name) {
			   var container = $('#cblist');
			   var inputs = container.find('input');
			   var id = inputs.length+1;

			   $('<input />', { type: 'checkbox', id: 'cb'+id, value: name }).appendTo(container);
			   $('<label />', { 'for': 'cb'+id, text: name }).appendTo(container);
			   $('<br />').appendTo(container);
			}
			
			function displayVals() {
				var hidval = $( "#faculty" ).val();
				
				if(hidval != "")
				{
					var myArray = hidval.split(',');

					// display the result in myDiv
					for(var i=0;i<myArray.length;i++){
						fac.push(myArray[i]);
					}
					$("#dialog").dialog({
						modal: true,
						resizable: false,
						buttons: {
							"Yes": function() {
								$(this).dialog("close");
								showanotherPopup(fac);
							},
							"No": function() {
								$(this).dialog("close");
								return false;
							}
						}
					});					
				}
				else {
					// do nothing
				}
					
				
			}
			 
			$(function() {
				displayVals();
			});

			function showanotherPopup(fac)
			{
				for(var i=0;i<fac.length;i++){
						addCheckbox('First Student Feedback('+fac[i]+')');
						addCheckbox('Second Student Feedback('+fac[i]+')');
				}
				
				$(function() {
					var buttons = {
						'Select All': select,
						'Deselect All': deselect,
						'Delete Selected': deleteselected,
						Cancel: cancel
					};
					
					$("#cblist").dialog({
						modal: true,
						resizable: false,
						buttons: buttons
					});
				});
			}
			
			
			function select() {
					$(':checkbox', this).prop('checked', true);
			}

			function deselect() {
					$(':checkbox', this).prop('checked', false);
			}

			function deleteselected() {
					// XXX how to implement?
					var selected = '';
					$(this).find('input[type="checkbox"]:checked').each(function() {
						if (selected == "")
							selected = $(this).val();
						else
							selected = selected + ":" + $(this).val();
						
					});
					
					var courseid = <?php echo "$thiscourseid" ?>;
					var enrolid = <?php echo "$enrolid" ?>;
					//document.forms["assignform"].submit();
					window.open('http://lms.nust.edu.pk/portal/enrol/manual/removefeedback.php?feedbacks='+selected+'&courseid='+courseid+'&enrolid='+enrolid);
					/* $.ajax({
						type: 'post',
						url: 'removefeedback.php',
						data: {
							feedbacks: selected,
							courseid : <?php echo "$thiscourseid" ?>
						},
						success: function( data ) {
							alert('Success');
							window.open('http://localhost/lms_live/portal/enrol/manual/remove.php','title',feedbacks,courseid);
							/* setTimeout(2000);
							alert('Success');
							console.log( data );
							alert(data);
							$("#deletesuccess").html(data).dialog({
								modal: true,
								resizable: false,
								buttons: {
									"OK": function() {
										$(this).dialog("close");
									}
								}
							}); */
							/* $("#deletesuccess").dialog({
								modal: true,
								resizable: false,
								buttons: {
									"OK": function() {
										$(this).dialog("close");
									}
								}
							}); 
						}
					}); */
					$(this).dialog('close');
			}

			function cancel() {
					$(this).find('input[type="checkbox"]').each(function() {
						$(this).prop('checked', $(this).prop('original'));
					});
					$(this).dialog('close');
			}
			</script>
      </td>
      <td id="potentialcell">
          <p><label for="addselect"><?php print_string('enrolcandidates', 'enrol'); ?></label></p>
          <?php $potentialuserselector->display() ?>
      </td>
    </tr>
  </table>
</div></form>
<?php


echo $OUTPUT->footer();
