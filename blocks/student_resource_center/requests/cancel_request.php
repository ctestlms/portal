<?php

require_once('../../../config.php');

global $DB;

$req = new stdClass();
$req->id = $_GET['reqid'];
$req->Status = "Canceled";

$DB->update_record('requests', $req, false);

$redirect_url = new moodle_url('/blocks/student_resource_center/requests/requests_dashboard.php');
    redirect($redirect_url);