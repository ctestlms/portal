<?php

///Added By Hina Yousuf
require_once('../../../../config.php');
 require_once("../../../../lib/datalib.php");
 session_start();
 $course=$_SESSION['course'];
 $cm=$_SESSION['cm'];
 $assignmentid=$_SESSION['assignmentid'];
 $assignmentname=$this->assignment->get_instance()->name;
 add_to_log($course, 'assignment', ' file not attached error','view.php?a='.$assignmentid, $assignmentname, $cm);
 
?>
