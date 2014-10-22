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
 * @package    enrol
 * @subpackage manual
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once('locallib.php');
require_once($CFG->dirroot.'/mod/feedback/lib.php');
$enrolid      =16;// required_param('enrolid', PARAM_INT);
$school       = optional_param('school', 0, PARAM_INT);
$roleid       = optional_param('roleid', -1, PARAM_INT);
$role_id      = optional_param('role_id', -1, PARAM_INT);
$sort_        = optional_param('sort_', "", PARAM_INT);
$extendperiod = optional_param('extendperiod', 0, PARAM_INT);
$extendbase   = optional_param('extendbase', 3, PARAM_INT);
$grp		  = optional_param('grp', "", PARAM_ALPHANUM);//get user group
$sgrp		  = optional_param('sgrp', "", PARAM_ALPHANUM);//get user sub group.

//$instance = $DB->get_record('enrol', array('id'=>$enrolid, 'enrol'=>'manual'), '*', MUST_EXIST);
$context = get_context_instance(CONTEXT_COURSECAT, $school);
require_login($course);
if($school!=0){
	require_capability('block/custom_reports:add_remove_feeDefaulter', $context);
}

//echo "schol".$school;

$sortby = array('0'=>get_string('none'),'1'=>"Name",'2'=>"Reg No",'3'=>"UID") ;

if (!isset($sortby[$sort])) {
	// weird - security always first!
	$sort = 0;
}

if (!$enrol_manual = enrol_get_plugin('manual')) {
	throw new coding_exception('Can not instantiate enrol_manual');
}

//$instancename = $enrol_manual->get_instance_name($instance);

//$PAGE->set_url('/enrol/manual/manage.php', array('enrolid'=>$instance->id));
$PAGE->set_pagelayout('admin');
//$PAGE->set_title($enrol_manual->get_instance_name($instance));
$PAGE->set_heading("Defaulters Interface");
//navigation_node::override_active_url(new moodle_url('/enrol/users.php', array('id'=>$course->id)));

// Create the user selector objects.
//$options = array('enrolid' => $enrolid);

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

// enrolment start
$basemenu = array();
if ($course->startdate > 0) {
	$basemenu[2] = get_string('coursestart') . ' (' . userdate($course->startdate, $timeformat) . ')';
}
$basemenu[3] = get_string('today') . ' (' . userdate($today, $timeformat) . ')' ;

// process add and removes
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
			$enrol_manual->add_defaulter( $adduser->id, $timestart, $timeend);

			//add_to_log($course->id, 'course', 'enrol', '../enrol/users.php?id='.$course->id, $course->id); //there should be userid somewhere!


		}

		$potentialuserselector->invalidate_selected_users();
		$currentuserselector->invalidate_selected_users();

		//TODO: log
	}
}

// Process incoming role unassignments
if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
	$userstounassign = $currentuserselector->get_selected_users();
	if (!empty($userstounassign)) {
		foreach($userstounassign as $removeuser) {
			$enrol_manual->remove_defaulter( $removeuser->id);
			//add_to_log($course->id, 'course', 'unenrol', '../enrol/users.php?id='.$course->id, $course->id); //there should be userid somewhere!
		}

		$potentialuserselector->invalidate_selected_users();
		$currentuserselector->invalidate_selected_users();

		//TODO: log
	}
}
$navlinks[] = array('name' => get_string('defaulters_interface', 'block_custom_reports'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
print_header('DEFAULTERS INTERFACE', 'DEFAULTERS INTERFACE', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);


//echo $OUTPUT->header();
echo $OUTPUT->heading("Fee Defaulters");
//Start of LDAP filter script.

if($school !=0){
	echo "<div style='text-align: center;'>";
	$query = "select distinct user_group from {$CFG->prefix}user where user_group NOT IN ('', 'NULL')";
	$path = $CFG->wwwroot."/blocks/custom_reports/fee_defaulters.php?school=".$school."&";
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
	echo "</div>";
}
?>
<script type="text/javascript">
<!--
	$(document).ready(function(){
		$("select#grp").change(function(){
			if($(this).val() != "")
				document.location = "<?php echo $path; ?>grp="+$(this).val();
		});
		$("select#sgrp").change(function(){
			if($(this).val() != "" && $("select#grp").val() != "")
				document.location = "<?php echo $path; ?>grp="+$("select#grp").val()+"&sgrp="+$(this).val();
		});
		$("select#sort").change(function(){
			            if($(this).val() != "" ){
			                if($("select#sgrp").val() != "" && $("select#grp").val() != ""){
			                    document.location = "<?php echo $path; ?>grp="+$("select#grp").val()+"&sgrp="+$("select#sgrp").val();
			                }
			                if($("select#sgrp").val() == "" && $("select#grp").val() != ""){
			                    document.location = "<?php echo $path; ?>grp="+$("select#grp").val();
			                }
			                
			                
			            }
			        });
	
	});
//-->
</script>

<?php
if($school ==0){

	echo '<form name="myform" action="fee_defaulters.php" method="POST">';


	echo "<br/><b>Select School:</b>";
	$query = "SELECT id,name FROM {course_categories} WHERE parent =0";
	if($groups = $DB->get_records_sql($query)){
		echo "<select name='school' id='school'>";
		foreach ($groups as $group){
			$value= $group->id;
			$selected = ($value == $school_) ? "selected = 'selected'" : "";
			echo "<option value='{$value}' {$selected} >{$group->name}</option>";
		}
		echo "</select><br/>";

	}
	echo '<br/><input type="submit" value="Select" name="defaulter">';

	echo '</form>';
}else{
	?>

<form id="assignform" method="post" action="<?php echo $PAGE->url ?>">

	<div>
		<input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
		<input type="hidden" name="school" value="<?php echo $school; ?>" />

		<table summary=""
			class="roleassigntable generaltable generalbox boxaligncenter"
			cellspacing="0">


			</td>
			<td></td>

			</tr>

			<tr>
				<td id="existingcell">
					<p>
						<label for="removeselect"><?php echo "Fee Defaulters"; ?> </label>
					</p> <?php $currentuserselector->display() ?>
				</td>
				<td id="buttonscell">
					<div id="addcontrols">
						<input name="add" id="add" type="submit"
							value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>"
							title="<?php print_string('add'); ?>" /><br />


					</div>

					<div id="removecontrols">
						<input name="remove" id="remove" type="submit"
							value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>"
							title="<?php print_string('remove'); ?>" />
					</div>
				</td>
				<td id="potentialcell">
					<p>
						<label for="addselect"><?php echo "Non-Fee Defaulters"; ?> </label>
					</p> <?php $potentialuserselector->display() ?>
				</td>
			</tr>
		</table>
	</div>
</form>
	<?php }


	echo $OUTPUT->footer();


