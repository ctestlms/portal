<?php
require('../../config.php');
include('dbcon.php');

if ($_REQUEST) {

    $id = $_REQUEST['parent_id'];
    $status = $_REQUEST['status'];

    $id_name = explode("|", $id);
    $id = $id_name[0];         // school id
    $name = $id_name[1];
    $query = "select distinct user_subgroup from {user} ";
    $sub_groups = $DB->get_records_sql($query);

//    echo $sql;
    // echo "<div style='text-align: center; font-weight: bold;'>Faculty Feedback Record </div>";

    echo "<form name='school_report' method='post' action='std_reg_rept.php'>";
    echo "<label><b>Select Batch:</b>&nbsp;</label>&nbsp;</td><td>";

    echo '<input type="hidden" name="export" value="false" />';

    echo "<input type='hidden' name='id' id='id' value=2>";
    echo "<input type='hidden' name='school' id='school' value='$id'>";
    echo "<input type='hidden' name='status' id='status' value='$status'>";

    echo "<select name='batch'>";
    foreach ($sub_groups as $sub_group) {
        $value = $sub_group->user_subgroup;
        $selected = ($name == $sub_group->user_subgroup) ? "selected = 'selected'" : "";
        ?>
        <option   value="<?php echo $value; ?>" <?php echo $selected; ?>>
            <?php echo $sub_group->user_subgroup; ?>
        </option>
        <?php
    }
    echo "</select>";
    ?>


    <input type="submit" name="view" value="View">
    <?php
}
?>