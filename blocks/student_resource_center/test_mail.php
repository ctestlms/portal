<?php

require_once('../../config.php');

 global $DB,$CFG,$PAGE,$USER;
require_login();


/*
$sql = "SELECT u.* FROM {user} u JOIN `mdl_user_info_data` ui ON ui.userid = u.id WHERE (u.user_subgroup = \"beee5a\" OR u.user_subgroup = \"beee-5-a\" OR u.user_subgroup = \"beee5b\" OR u.user_subgroup = \"beee-5-b\" OR u.user_subgroup = \"beee5c\" OR u.user_subgroup = \"beee-5-c\" OR u.user_subgroup = \"beee5d\" OR u.user_subgroup = \"beee-5-d\") AND ui.fieldid = 10 AND ( ui.data = \"joined\" OR ui.data = \"pending\") AND u.picture > 0";
//$sql = "SELECT u.id FROM {user} u WHERE (u.user_subgroup = \"beee5a\" OR u.user_subgroup = \"beee-5-a\" OR u.user_subgroup = \"beee5b\" OR u.user_subgroup = \"beee-5-b\" OR u.user_subgroup = \"beee5c\" OR u.user_subgroup = \"beee-5-c\" OR u.user_subgroup = \"beee5d\" OR u.user_subgroup = \"beee-5-d\") AND u.picture > 0";

$users = $DB->get_records_sql($sql);
   echo $CFG->tempdir;
 
echo "Degree: BEEE5"."<br><br>";
 
foreach($users AS $user){
    if(!isset($user->idnumber))
        $name = $user->id;
    else
        $name = $user->idnumber;
   
   
  
  $inPath = "$CFG->wwwroot/user/pix.php?file=/$user->id/f3.jpg";
$outPath = $CFG->tempdir . "/uploads/beee5/$name.jpg";
file_put_contents($outPath, file_get_contents($inPath));
 echo $user->firstname.' '.$user->lastname.'  '.$name."<br>";

}


//bese-4
$sql1 = "SELECT u.* FROM {user} u JOIN `mdl_user_info_data` ui ON ui.userid = u.id WHERE (u.user_subgroup = \"BESE4A\" OR u.user_subgroup = \"BESE4B\" OR u.user_subgroup = \"BESE-4-B\" OR u.user_subgroup = \"BESE-4a\"  OR u.user_subgroup = \"BESE-4-A\") AND ui.fieldid = 10 AND ( ui.data = \"joined\" OR ui.data = \"pending\") AND u.picture > 0";
//$sql1 = "SELECT u.id FROM {user} u  WHERE (u.user_subgroup = \"BESE4A\" OR u.user_subgroup = \"BESE4B\" OR u.user_subgroup = \"BESE-4-B\" OR u.user_subgroup = \"BESE-4a\"  OR u.user_subgroup = \"BESE-4-A\") AND u.picture > 0";

$users1 = $DB->get_records_sql($sql1);

echo "<br><br>Degree: BESE4<br><br><br>";
foreach($users1 AS $user){
    
    if(!isset($user->idnumber))
        $name = $user->id;
    else
        $name = $user->idnumber;
   
  $inPath = "$CFG->wwwroot/user/pix.php?file=/$user->id/f3.jpg";
$outPath = $CFG->tempdir . "/uploads/bese4/$name.jpg";
file_put_contents($outPath, file_get_contents($inPath));
echo $user->firstname.' '.$user->lastname.'  '.$name."<br>";
        
}
echo "<br><br>Degree: BSCS3"."<br><br><br>";
//bscs-3
$sql2 = "SELECT u.* FROM {user} u JOIN `mdl_user_info_data` ui ON ui.userid = u.id WHERE (u.user_subgroup = \"BSCS-3-A\" OR u.user_subgroup = \"BSCS-3-B\" OR u.user_subgroup = \"BSCS3A\" OR u.user_subgroup = \"BSCS3B\") AND ui.fieldid = 10 AND ( ui.data = \"joined\" OR ui.data = \"pending\") AND u.picture > 0";
//$sql2 = "SELECT u.id FROM {user} u WHERE (u.user_subgroup = \"BSCS-3-A\" OR u.user_subgroup = \"BSCS-3-B\" OR u.user_subgroup = \"BSCS3A\" OR u.user_subgroup = \"BSCS3B\") AND u.picture > 0";
$users2 = $DB->get_records_sql($sql2);


foreach($users2 AS $user){
      if(!isset($user->idnumber))
        $name = $user->id;
    else
        $name = $user->idnumber;
    
  $inPath = "$CFG->wwwroot/user/pix.php?file=/$user->id/f3.jpg";
$outPath = $CFG->tempdir . "/uploads/bscs3/$name.jpg";
file_put_contents($outPath, file_get_contents($inPath));
 echo $user->firstname.' '.$user->lastname.'  '.$name."<br>";
   
}
  
 

//msit-14
$sql3 = "SELECT u.* FROM {user} u JOIN `mdl_user_info_data` ui ON ui.userid = u.id WHERE (u.user_subgroup = \"MSIT-14\" OR u.user_subgroup = \"MSIT14\" ) AND ui.fieldid = 10 AND ( ui.data = \"joined\" OR ui.data = \"pending\") AND u.picture > 0";
$users3 = $DB->get_records_sql($sql3);

echo "<br><br>Degree: MSIT14"."<br><br><br>";
foreach($users3 AS $user){
          if(!isset($user->idnumber))
        $name = $user->id;
    else
        $name = $user->idnumber;
  $inPath = "$CFG->wwwroot/user/pix.php?file=/$user->id/f3.jpg";
$outPath = $CFG->tempdir . "/uploads/msit14/$name.jpg";
file_put_contents($outPath, file_get_contents($inPath));
 echo $user->firstname.' '.$user->lastname.'  '.$name."<br>";
}

//msee-5
$sql4 = "SELECT u.* FROM {user} u JOIN `mdl_user_info_data` ui ON ui.userid = u.id WHERE (u.user_subgroup = \"MSEE-5\" OR u.user_subgroup = \"MSEE5\") AND ui.fieldid = 10 AND ( ui.data = \"joined\" OR ui.data = \"pending\") AND u.picture > 0";
$users4 = $DB->get_records_sql($sql4);

echo "<br><br>Degree: MSEE5"."<br><br><br>";
foreach($users4 AS $user){
          if(!isset($user->idnumber))
        $name = $user->id;
    else
        $name = $user->idnumber;
  $inPath = "$CFG->wwwroot/user/pix.php?file=/$user->id/f3.jpg";
$outPath = $CFG->tempdir . "/uploads/msee5/$name.jpg";
file_put_contents($outPath, file_get_contents($inPath));
 echo $user->firstname.' '.$user->lastname.'  '.$name."<br>";
}

//msccs6
$sql5 = "SELECT u.* FROM {user} u JOIN `mdl_user_info_data` ui ON ui.userid = u.id WHERE (u.user_subgroup = \"MSCCS-6\" OR u.user_subgroup = \"MSCCS6\") AND ui.fieldid = 10 AND ( ui.data = \"joined\" OR ui.data = \"pending\") AND u.picture > 0";
$users5 = $DB->get_records_sql($sql5);

echo "<br><br>Degree: MSCCS6"."<br><br><br>";
foreach($users5 AS $user){
          if(!isset($user->idnumber))
        $name = $user->id;
    else
        $name = $user->idnumber;
  $inPath = "$CFG->wwwroot/user/pix.php?file=/$user->id/f3.jpg";
$outPath = $CFG->tempdir . "/uploads/msccs6/$name.jpg";
file_put_contents($outPath, file_get_contents($inPath));
 echo $user->firstname.' '.$user->lastname.'  '.$name."<br>";
}
*/
echo "<br><br>Degree: MSCS3"."<br><br><br>";
$sql6 = "SELECT u.* FROM {user} u JOIN `mdl_user_info_data` ui ON ui.userid = u.id WHERE (u.user_subgroup = \"MSCS-3\" OR u.user_subgroup = \"MSCS3\") AND ui.fieldid = 10 AND ( ui.data = \"joined\" OR ui.data = \"pending\") AND u.picture > 0";
$users6 = $DB->get_records_sql($sql6);


foreach($users6 AS $user){
          if(!isset($user->idnumber))
        $name = $user->id;
    else
        $name = $user->idnumber;
  $inPath = "$CFG->wwwroot/user/pix.php?file=/$user->id/f3.jpg";
$outPath = $CFG->tempdir . "/uploads/mscs3/$name.jpg";
file_put_contents($outPath, file_get_contents($inPath));
 echo $user->firstname.' '.$user->lastname.'  '.$name."<br>";
}


echo "<br><br>Degree: MSITE-1"."<br><br><br>";
$sql6 = "SELECT u.* FROM {user} u JOIN `mdl_user_info_data` ui ON ui.userid = u.id WHERE (u.user_subgroup = \"MSITE-1\" OR u.user_subgroup = \"MSITE1\") AND ui.fieldid = 10 AND ( ui.data = \"joined\" OR ui.data = \"pending\") AND u.picture > 0";
$users6 = $DB->get_records_sql($sql6);

   echo $CFG->tempdir;
   echo $users6;

foreach($users6 AS $user){
          if(!isset($user->idnumber))
        $name = $user->id;
    else
        $name = $user->idnumber;
  $inPath = "$CFG->wwwroot/user/pix.php?file=/$user->id/f3.jpg";
$outPath = $CFG->tempdir . "/uploads/msite1/$name.jpg";
file_put_contents($outPath, file_get_contents($inPath));
 echo $user->firstname.' '.$user->lastname.'  '.$name."<br>";
}


 echo "done";
 