<?php
// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
$token = '6004d2b8b5ed28477104442a0a301249';
$domainname = 'https://lms.nust.edu.pk/portal';
$functionname = 'local_programs_get_programs';
$restformat = 'json';
/// PARAMETERS
$schoolid= (int)http_build_query($_GET);
$params = array('schoolid' => $schoolid);
///// XML-RPC CALL
header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
require_once('./curl.php');
$curl = new curl;
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$resp = $curl->post($serverurl . $restformat, $params );
print_r($resp);
