<?php
require('../../config.php');
include('dbcon.php');

if($_REQUEST)
{

	$id 	= $_REQUEST['parent_id'];
	$id_name = explode("|", $id);
    $id = $id_name[0];         // school id
    $name = $id_name[1];
  /*  $query="SELECT DISTINCT user_subgroup FROM mdl_user u
            JOIN mdl_role_assignments ra ON ra.userid = u.id
            JOIN mdl_role r ON ra.roleid = r.id
            JOIN mdl_context c ON ra.contextid = c.id
            WHERE r.id =3 AND user_group = 'Faculty' AND c.contextlevel =50
            AND c.instanceid IN (
                                SELECT id FROM mdl_course WHERE category
                                                                IN (SELECT id FROM mdl_course_categories
                                                                WHERE path LIKE '/$id%'
                                                                )
                                )";*/
    $query="Select name from {department} where course=$id";
	
	if($sub_groups = $DB->get_records_sql($query)){
		echo "<select name='dept' id='dept'>";
		foreach ($sub_groups as $sub_group){
			$selected = ($sub_group->name == $dept) ? "selected = 'selected'" : "";
			echo "<option value='{$sub_group->name}' {$selected}>{$sub_group->name}</option>";
		}
		echo "</select>";
	}
}
	
?>
