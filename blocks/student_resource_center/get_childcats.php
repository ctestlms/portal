<?php
require_once('../../config.php');

class SelectList
{
/*
public function ShowSchool()
{
    global $CFG,$DB;
    $sql = "SELECT * FROM {course_categories} WHERE parent = 0" ;
    $result = $DB->get_records_sql($sql);
    $school = '<option value="0">choose...</option>';
    foreach($result as $r)     {
        $school .= '<option value="' . $r->id . '">' . $r->name . '</option>';
    }
    return $school;
}
 * 
 */

public function ShowProgram()
{
     global $CFG,$DB;
    $sql = "SELECT * FROM {course_categories} WHERE parent = $_POST[id]" ;
    $result = $DB->get_records_sql($sql);
    $program = '<option value="0">choose...</option>';
    foreach($result as $r)     {
        $program .= '<option value="' . $r->id . '">' . $r->name . '</option>';
    }
    return $program;
}

public function ShowDegree()
{
     global $CFG,$DB;
    $sql = "SELECT * FROM {course_categories} WHERE parent = $_POST[id]" ;
    $result = $DB->get_records_sql($sql);
    $degree = '<option value="0">choose...</option>';
    foreach($result as $r)     {
        $degree .= '<option value="' . $r->id . '">' . $r->name . '</option>';
    }
    return $degree;
}

public function ShowBatch()
{
     global $CFG,$DB;
    $sql = "SELECT * FROM {course_categories} WHERE parent = $_POST[id]" ;
    $result = $DB->get_records_sql($sql);
    $batch = '<option value="0">choose...</option>';
    foreach($result as $r)     {
        $batch .= '<option value="' . $r->id . '">' . $r->name . '</option>';
    }
    return $batch;
}

public function ShowSemester()
{
     global $CFG,$DB;
    $sql = "SELECT * FROM {course_categories} WHERE parent = $_POST[id]" ;
    $result = $DB->get_records_sql($sql);
    $semester = '<option value="0">choose...</option>';
    foreach($result as $r)     {
        $semester .= '<option value="' . $r->id . '">' . $r->name . '</option>';
    }
    return $semester;
}

public function ShowCohort()
{
     global $CFG,$DB;
     $sq = "SELECT coho.id,coho.name FROM `mdl_cohort` AS coho
JOIN `mdl_context` AS con 
     ON con.id = coho.contextid
JOIN `mdl_course_categories` AS cou
     ON con.instanceid = cou.id
WHERE cou.id = $_POST[id]
        OR cou.id = 83";
     
 
 $cohorts = $DB->get_records_sql($sq); 
    $cohort = '<option value="0">choose...</option>';
    foreach($cohorts as $r)     {
        $cohort .= '<option value="' . $r->id . '">' . $r->name . '</option>';
    }
    return $cohort;
}

}

$opt = new SelectList();