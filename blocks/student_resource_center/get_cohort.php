<?php

require_once('../../config.php');
require_once('get_childcats.php');
 global $CFG,$DB;
$opt = new SelectList();
 echo $opt->ShowCohort();

