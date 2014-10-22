<?php

require_once('../../config.php');
//require_once('get_childcats.php');
 global $CFG,$DB;
 
 //var_dump($_POST);

$sql ="SELECT u.id,u.registrationstatus, u.username, u.firstname, u.lastname,
                          u.email, u.city, u.country, u.picture,
                          u.lang, u.timezone, u.maildisplay, u.imagealt FROM {user} u
                          JOIN {cohort_members} cm ON cm.userid = u.id
                           JOIN {cohort} c ON c.id = cm.cohortid
                           WHERE c.id = $_POST[cohort]";

$totalcount = $DB->get_records_sql($sql);
print_object($totalcount);
 