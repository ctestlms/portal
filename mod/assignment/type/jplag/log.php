<?php

///Added By Hina Yousuf
require_once('../../../../config.php');
 require_once("../../../../lib/datalib.php");
 session_start();
 $course=$_SESSION['course'];
 $cm=$_SESSION['cm'];
 $assignment=$_SESSION['assignment'];
 add_to_log($course, 'assignment', 'file not attached arror','view.php?a='.$assignment->id, $assignment->name, $cm);
 
?>