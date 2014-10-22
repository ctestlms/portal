<?php

//add grades by defult
$coursearray=array(6363,6380,6379,6368,6367,6364,6366,6381,6371,6378,6377,6372,6374,6365,6370,6376,6373,6375,6369);
foreach ($coursearray as $courseid) {
    //echo $courseid;
    require_once('../config.php');
    require_once $CFG->dirroot . '/grade/lib.php';
    require_once $CFG->dirroot . '/grade/report/lib.php';
    $grade_item = new grade_item(array('courseid' => $courseid, 'itemtype' => 'manual'), false);
    $item = $grade_item->get_record_data();
    $parent_category = grade_category::fetch_course_category($courseid);
    $item->parentcategory = $parent_category->id;
    $decimalpoints = $grade_item->get_decimals();

    if ($item->hidden > 1) {
        $item->hiddenuntil = $item->hidden;
        $item->hidden = 0;
    } else {
        $item->hiddenuntil = 0;
    }

    $item->locked = !empty($item->locked);

    $item->grademax = format_float($item->grademax, $decimalpoints);
    $item->grademin = format_float($item->grademin, $decimalpoints);
    $item->gradepass = format_float($item->gradepass, $decimalpoints);
    $item->multfactor = format_float($item->multfactor, 4);
    $item->plusfactor = format_float($item->plusfactor, 4);
//    $item->itemname = "Grades";
//    $item->courseid = $courseid;
//    $item->itemtype= "manual";
//    $item->gradetype = 2;
//    $item->grademax = 9;
//    $item->grademin = 1;
//    $item->scaleid = 30;
//    //$item->multfactor = $courseid;
//    $item->display = 1;
//    $item->timecreated = time();
//    $item->timemodified = time();
    if ($parent_category->aggregation == GRADE_AGGREGATE_SUM or $parent_category->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN2) {
        $item->aggregationcoef = $item->aggregationcoef == 0 ? 0 : 1;
    } else {
        $item->aggregationcoef = format_float($item->aggregationcoef, 4);
    }
    $item->cancontrolvisibility = $grade_item->can_control_visibility();
    if (!isset($data->aggregationcoef) || $data->aggregationcoef == '') {
        if ($parent_category->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
            $data->aggregationcoef = 1;
        } else {
            $data->aggregationcoef = 0;
        }
    }
    //  print_r($item);
    $data->itemname = "ABS/I Grade";
    $data->courseid = $courseid;
    $data->itemtype = "manual";
    $data->gradetype = 2;
    $data->grademax = 9;
    $data->grademin = 1;
    $data->scaleid = 31;
    //$data->multfactor = $courseid;
    $data->display = 1;
    $data->timecreated = time();
    $data->timemodified = time();

    if (!isset($data->gradepass) || $data->gradepass == '') {
        $data->gradepass = 0;
    }

    if (!isset($data->grademin) || $data->grademin == '') {
        $data->grademin = 0;
    }

    $hidden = empty($data->hidden) ? 0 : $data->hidden;
    $hiddenuntil = empty($data->hiddenuntil) ? 0 : $data->hiddenuntil;
    unset($data->hidden);
    unset($data->hiddenuntil);

    $locked = empty($data->locked) ? 0 : $data->locked;
    $locktime = empty($data->locktime) ? 0 : $data->locktime;
    unset($data->locked);
    unset($data->locktime);

    $convert = array('grademax', 'grademin', 'gradepass', 'multfactor', 'plusfactor', 'aggregationcoef');
    foreach ($convert as $param) {
        if (property_exists($data, $param)) {
            $data->$param = unformat_float($data->$param);
        }
    }

    $grade_item = new grade_item(array('id' => $id, 'courseid' => $courseid));
    grade_item::set_properties($grade_item, $data);
    $grade_item->outcomeid = null;

    // Handle null decimals value
    if (!property_exists($data, 'decimals') or $data->decimals < 0) {
        $grade_item->decimals = null;
    }

    if (empty($grade_item->id)) {
        $grade_item->itemtype = 'manual'; // all new items to be manual only
        $grade_item->insert();

        // set parent if needed
        if (isset($data->parentcategory)) {
            $grade_item->set_parent($data->parentcategory, 'gradebook');
        }
    }

    // update hiding flag
    if ($hiddenuntil) {
        $grade_item->set_hidden($hiddenuntil, false);
    } else {
        $grade_item->set_hidden($hidden, false);
    }
}
//end
?>