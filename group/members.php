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
 * Add/remove members from group.
 *
 * @copyright 2006 The Open University and others, N.D.Freear AT open.ac.uk, J.White AT open.ac.uk and others
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package   core_group
 */
require_once(dirname(__FILE__) . '/../config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once($CFG->dirroot . '/user/selector/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/filelib.php');
//Added By Hina Yousuf
$grp		  = optional_param('grp', "", PARAM_ALPHANUM);//get user group
$sgrp		  = optional_param('sgrp', "", PARAM_ALPHANUM);//get user sub group.
//end
$section           = optional_param('section', "", PARAM_ALPHANUM);//get course sections - added by Qurrat-ul-ain
$contextids           = optional_param('contextids', "", PARAM_INT);//get course contextids - added by Qurrat-ul-ain
$groupid = required_param('group', PARAM_INT);
$cancel  = optional_param('cancel', false, PARAM_BOOL);

$group = $DB->get_record('groups', array('id'=>$groupid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$group->courseid), '*', MUST_EXIST);

$PAGE->set_url('/group/members.php', array('group'=>$groupid));
$PAGE->set_pagelayout('admin');

require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/course:managegroups', $context);

$returnurl = $CFG->wwwroot.'/group/index.php?id='.$course->id.'&group='.$group->id;

if ($cancel) {
    redirect($returnurl);
}

$groupmembersselector = new group_members_selector('removeselect', array('groupid' => $groupid, 'courseid' => $course->id));
$potentialmembersselector = new group_non_members_selector('addselect', array('groupid' => $groupid, 'courseid' => $course->id));

if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoadd = $potentialmembersselector->get_selected_users();
    if (!empty($userstoadd)) {
        foreach ($userstoadd as $user) {
            if (!groups_add_member($groupid, $user->id)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
            $groupmembersselector->invalidate_selected_users();
            $potentialmembersselector->invalidate_selected_users();
        }
    }
}

if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoremove = $groupmembersselector->get_selected_users();
    if (!empty($userstoremove)) {
        foreach ($userstoremove as $user) {
            if (!groups_remove_member_allowed($groupid, $user->id)) {
                print_error('errorremovenotpermitted', 'group', $returnurl,
                        $user->fullname);
            }
            if (!groups_remove_member($groupid, $user->id)) {
                print_error('erroraddremoveuser', 'group', $returnurl);
            }
            $groupmembersselector->invalidate_selected_users();
            $potentialmembersselector->invalidate_selected_users();
        }
    }
}

// Print the page and form
$strgroups = get_string('groups');
$strparticipants = get_string('participants');
$stradduserstogroup = get_string('adduserstogroup', 'group');
$strusergroupmembership = get_string('usergroupmembership', 'group');

$groupname = format_string($group->name);

$PAGE->requires->js('/group/clientlib.js');
$PAGE->navbar->add($strparticipants, new moodle_url('/user/index.php', array('id'=>$course->id)));
$PAGE->navbar->add($strgroups, new moodle_url('/group/index.php', array('id'=>$course->id)));
$PAGE->navbar->add($stradduserstogroup);

/// Print header
$PAGE->set_title("$course->shortname: $strgroups");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('adduserstogroup', 'group').": $groupname", 3);
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
		$sectioncontext = array();
		$contextids = "";
		foreach ($courses as $key => $course) {
			// if ($thiscourseid == $course->id)
			// {
				// do nothing
			// }
			// else
			// {
				$coursecode = explode(" ", $course->fullname);
				if (strpos($coursecode[1],'Elective') !== false) {
					$coursename = explode("-", $course->fullname);
					$coursename = $coursename[0]; 
				}
				else
					$coursename = $course->fullname;
					
				$coursesection = substr($coursename, -2);
				$sections[$i] = $coursesection[0]; 
				$sectioncontext[$i]->section = $coursesection[0]; 
				
				if ($thiscoursecode[0] != $coursecode[0]) {
					$thiscoursecode[0] . "--" . $coursecode[0] . "==" . $key;
					unset($courses[$key]);
					//echo "Un equal IDs<br/>";
					continue;
				}
				
				$courseids.= $course->id . ",";
				$contexts = get_context_instance(CONTEXT_COURSE, $course->id);
				//$contextids = $contexts->id;
				$sectioncontext[$i]->contextid = $contexts->id;
				$contextids = $sectioncontext[$i]->contextid;
					
			//}
			$i++;
		}
		$courseids = rtrim($courseids, ",");
		$contextids = rtrim($contextids, ",");
		// echo "Course IDs: ".$courseids."<br/>"; // Shami
		// echo "Context IDs: ".$contextids."<br/>"; // Qurrat
		// print_r($sectioncontext);
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
	foreach($sectioncontext as $section)
		echo "<option value='$section->section' id='$section->section' name='$section->contextid'>$coursecode[0] -- $section->section</option>";  
	echo "</select>";
echo "</div>";
//end

//Start of LDAP filter script.
echo "<div style='text-align: center;'>";
$query = "select distinct user_group from {$CFG->prefix}user where user_group NOT IN ('', 'NULL')";
$path = $CFG->wwwroot."/group/members.php?group=".$groupid;
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
<script type="text/javascript">
<!--
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
                    if($(this).val() != "") {
						var section = $(this).val();
						var contextids = document.getElementById(section);
						var contextid = contextids.getAttribute("name");
						document.location = "<?php echo $path; ?>&section="+$(this).val()+"&contextids="+contextid;
					}
        });
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
//-->
</script>
<?php 
//end
/// Print group info -  TODO: remove tables for layout here
$groupinfotable = new html_table();
$groupinfotable->attributes['class'] = 'groupinfobox';
$picturecell = new html_table_cell();
$picturecell->attributes['class'] = 'left side picture';
$picturecell->text = print_group_picture($group, $course->id, true, true, false);

$contentcell = new html_table_cell();
$contentcell->attributes['class'] = 'content';

$group->description = file_rewrite_pluginfile_urls($group->description, 'pluginfile.php', $context->id, 'group', 'description', $group->id);
if (!isset($group->descriptionformat)) {
    $group->descriptionformat = FORMAT_MOODLE;
}
$options = new stdClass;
$options->overflowdiv = true;
$contentcell->text = format_text($group->description, $group->descriptionformat, $options);
$groupinfotable->data[] = new html_table_row(array($picturecell, $contentcell));
echo html_writer::table($groupinfotable);

/// Print the editing form
?>

<div id="addmembersform">
    <form id="assignform" method="post" action="<?php echo $CFG->wwwroot; ?>/group/members.php?group=<?php echo $groupid; ?>">
    <div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

    <table class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">
    <tr>
      <td id='existingcell'>
          <p>
            <label for="removeselect"><?php print_string('groupmembers', 'group'); ?></label>
          </p>
          <?php $groupmembersselector->display(); ?>
          </td>
      <td id='buttonscell'>
        <p class="arrow_button">
            <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />
            <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('remove'); ?>" />
        </p>
      </td>
      <td id='potentialcell'>
          <p>
            <label for="addselect"><?php print_string('potentialmembs', 'group'); ?></label>
          </p>
          <?php $potentialmembersselector->display(); ?>
      </td>
      <td>
        <p><?php echo($strusergroupmembership) ?></p>
        <div id="group-usersummary"></div>
      </td>
    </tr>
    <tr><td colspan="3" id='backcell'>
        <input type="submit" name="cancel" value="<?php print_string('backtogroups', 'group'); ?>" />
    </td></tr>
    </table>
    </div>
    </form>
</div>

<?php
    //outputs the JS array used to display the other groups users are in
    $potentialmembersselector->print_user_summaries($course->id);

    //this must be after calling display() on the selectors so their setup JS executes first
    $PAGE->requires->js_init_call('init_add_remove_members_page', null, false, $potentialmembersselector->get_js_module());

    echo $OUTPUT->footer();
