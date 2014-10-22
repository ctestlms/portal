<?php

require_once('../../config.php');
require_once("../../mod/feedback/lib.php");
require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/formslib.php');

include('dbcon.php');
$navlinks[] = array('name' =>  get_string('bulk_action', 'block_bulk_action'), 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);

if (isset($_POST['report'])) {
    if ($_POST['type'] == "Feedback Settings") {
        $url = "bulkaction.php";
        header("location:$url");
    }
    if ($_POST['type'] == "Self Enrollment setings") {
        $url = "enrolbulkaction.php";
        header("location:$url");
    }
    
}
print_header(get_string('bulk_action', 'block_bulk_action'), get_string('bulk_action', 'block_bulk_action'), $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);

echo '<form name="myform" action="bulk_actions.php" method="POST">';
echo "<div align='center'><h1>Bulk Settings</h1></div>";
echo "<b>Select :</b>";
echo "<select name='type'>";
echo '<option value="Feedback Settings">Feedback Settings</option>';
echo '<option value="Self Enrollment setings">Self Enrollment setings</option>';

echo '</select>';

echo '<input type="submit" value="Select" name="report">';
echo "</form>";
echo $OUTPUT->footer();
?>
