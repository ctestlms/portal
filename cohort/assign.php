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
 * Cohort related management functions, this file needs to be included manually.
 *
 * @package    core_cohort
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->dirroot.'/cohort/locallib.php');
$grp		  = optional_param('grp', "", PARAM_ALPHANUM);//get user group
$sgrp		  = optional_param('sgrp', "", PARAM_ALPHANUM);//get user sub group.

$id = required_param('id', PARAM_INT);

require_login();

$cohort = $DB->get_record('cohort', array('id'=>$id), '*', MUST_EXIST);
$context = context::instance_by_id($cohort->contextid, MUST_EXIST);

require_capability('moodle/cohort:assign', $context);

$PAGE->set_context($context);
$PAGE->set_url('/cohort/assign.php', array('id'=>$id));

$returnurl = new moodle_url('/cohort/index.php', array('contextid'=>$cohort->contextid));

if (!empty($cohort->component)) {
    // We can not manually edit cohorts that were created by external systems, sorry.
    redirect($returnurl);
}

if (optional_param('cancel', false, PARAM_BOOL)) {
    redirect($returnurl);
}

if ($context->contextlevel == CONTEXT_COURSECAT) {
    $category = $DB->get_record('course_categories', array('id'=>$context->instanceid), '*', MUST_EXIST);
    navigation_node::override_active_url(new moodle_url('/cohort/index.php', array('contextid'=>$cohort->contextid)));
    $PAGE->set_pagelayout('report');

} else {
    navigation_node::override_active_url(new moodle_url('/cohort/index.php', array()));
    $PAGE->set_pagelayout('admin');
}
$PAGE->navbar->add(get_string('assign', 'cohort'));

$PAGE->set_title(get_string('cohort:assign', 'cohort'));
$PAGE->set_heading($COURSE->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('assignto', 'cohort', format_string($cohort->name)));

echo $OUTPUT->notification(get_string('removeuserwarning', 'core_cohort'));

// Get the user_selector we will need.
$potentialuserselector = new cohort_candidate_selector('addselect', array('cohortid'=>$cohort->id, 'accesscontext'=>$context));
$existinguserselector = new cohort_existing_selector('removeselect', array('cohortid'=>$cohort->id, 'accesscontext'=>$context));

// Process incoming user assignments to the cohort

if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoassign = $potentialuserselector->get_selected_users();
    if (!empty($userstoassign)) {

        foreach ($userstoassign as $adduser) {
            cohort_add_member($cohort->id, $adduser->id);
        }

        $potentialuserselector->invalidate_selected_users();
        $existinguserselector->invalidate_selected_users();
    }
}

// Process removing user assignments to the cohort
if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
    $userstoremove = $existinguserselector->get_selected_users();
    if (!empty($userstoremove)) {
        foreach ($userstoremove as $removeuser) {
            cohort_remove_member($cohort->id, $removeuser->id);
        }
        $potentialuserselector->invalidate_selected_users();
        $existinguserselector->invalidate_selected_users();
    }
}

// Print the form.
echo "<div style='text-align: center;'>";
$query = "select distinct user_group from {$CFG->prefix}user where user_group NOT IN ('', 'NULL')";
$path = $CFG->wwwroot."/cohort/assign.php?id=".$id;
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
// Print the form.
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
</div>
<form id="assignform" method="post" action="<?php echo $PAGE->url ?>"><div>
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />

  <table summary="" class="generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="removeselect"><?php print_string('currentusers', 'cohort'); ?></label></p>
          <?php $existinguserselector->display() ?>
      </td>
      <td id="buttonscell">
          <div id="addcontrols">
              <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.s(get_string('add')); ?>" title="<?php p(get_string('add')); ?>" /><br />
          </div>

          <div id="removecontrols">
              <input name="remove" id="remove" type="submit" value="<?php echo s(get_string('remove')).'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php p(get_string('remove')); ?>" />
          </div>
      </td>
      <td id="potentialcell">
          <p><label for="addselect"><?php print_string('potusers', 'cohort'); ?></label></p>
          <?php $potentialuserselector->display() ?>
      </td>
    </tr>
    <tr><td colspan="3" id='backcell'>
      <input type="submit" name="cancel" value="<?php p(get_string('backtocohorts', 'cohort')); ?>" />
    </td></tr>
  </table>
</div></form>

<?php

echo $OUTPUT->footer();
