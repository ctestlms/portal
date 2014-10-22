<?php
// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
$token = 'ee06311f8c90c13d58d34afb44c3ca9d';
$domainname = 'https://lms.nust.edu.pk/portal';
$functionname = 'local_schools_get_schools';
$restformat = 'json';
/// PARAMETERS
//$welcomemsg = 'Hello, ';
//$params = array('welcomemsg' => $welcomemsg);
///// XML-RPC CALL
header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
require_once('./curl.php');
$curl = new curl;
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$resp = $curl->post($serverurl . $restformat);
print_r($resp);
