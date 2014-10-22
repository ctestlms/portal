<?php 

require_once('config.php');

 // Connects to Our Database 
$con = mysql_connect($host, $username, $password);
mysql_select_db($database, $con);
 
 ?> 