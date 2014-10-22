<?php 

require_once('../../../config.php');
/*
$connect = mysql_connect("localhost","testlms24","CDE@456q") or die ("Couldn't Connect!");
 mysql_select_db("testlms24") or die("Couldn't find db");
 * 
 */
 $connect = mysql_connect("10.3.72.51","root","MF1ICs50!") or die ("Couldn't Connect!");
 mysql_select_db("moodle") or die("Couldn't find db");
global $DB;

echo "<h2>BEE AND BEEE</h2>";
$q1 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bee%\""; 
$bee = $DB->get_records_sql($q1);
print_object($bee);
//ELECTRICAL
// BEEE-1
$query = "UPDATE mdl_user SET user_subgroup= 'beee-1-a' WHERE user_subgroup = 'BEE1A' or user_subgroup = 'BEEE-1-A'";

mysql_query($query, $connect ) or die ('request "Could not execute SQL query" '.$query.'beee-1-a');

$query1 = "UPDATE mdl_user SET user_subgroup= 'beee-1-b' WHERE user_subgroup = 'BEE1B' or user_subgroup = 'BEEE-1-B'";

mysql_query($query1, $connect ) or die ('request "Could not execute SQL query" '.$query1.'beee-1-b');

$query2 = "UPDATE mdl_user SET user_subgroup= 'beee-1-c' WHERE user_subgroup = 'BEE1C' or user_subgroup = 'BEEE-1-C'";

mysql_query($query2, $connect ) or die ('request "Could not execute SQL query" '.$query2.'beee-1-c');

// BEEE-2

$query3 = "UPDATE mdl_user SET user_subgroup= 'beee-2-a' WHERE user_subgroup = 'BEE2A' or user_subgroup = 'BEEE-2-A'";

mysql_query($query3, $connect ) or die ('request "Could not execute SQL query" '.$query3.'beee-2-a');

$query4 = "UPDATE mdl_user SET user_subgroup= 'beee-2-b' WHERE user_subgroup = 'BEE2B' or user_subgroup = 'BEEE-2-B'";

mysql_query($query4, $connect ) or die ('request "Could not execute SQL query" '.$query4.'beee-2-b');

$query5 = "UPDATE mdl_user SET user_subgroup= 'beee-2-c' WHERE user_subgroup = 'BEE2C' or user_subgroup = 'BEEE-2-C'";

mysql_query($query5, $connect ) or die ('request "Could not execute SQL query" '.$query5.'beee-2-c');

$query6 = "UPDATE mdl_user SET user_subgroup= 'beee-2-d' WHERE user_subgroup = 'bee2d'";

mysql_query($query6, $connect ) or die ('request "Could not execute SQL query" '.$query6.'beee-2-d');

// BEEE-3

$query7 = "UPDATE mdl_user SET user_subgroup= 'beee-3-a' WHERE user_subgroup = 'beee3a'";

mysql_query($query7, $connect ) or die ('request "Could not execute SQL query" '.$query7.'beee-3-a');

$query8 = "UPDATE mdl_user SET user_subgroup= 'beee-3-b' WHERE user_subgroup = 'beee3b'";

mysql_query($query8, $connect ) or die ('request "Could not execute SQL query" '.$query8.'beee-3-b');

$query9 = "UPDATE mdl_user SET user_subgroup= 'beee-3-c' WHERE user_subgroup = 'Beee3c'";

mysql_query($query9, $connect ) or die ('request "Could not execute SQL query" '.$query9.'beee-3-c');

$query10 = "UPDATE mdl_user SET user_subgroup= 'beee-3-d' WHERE user_subgroup = 'beee3d'";

mysql_query($query10, $connect ) or die ('request "Could not execute SQL query" '.$query10.'beee-3-d');

// BEEE-4

$query11 = "UPDATE mdl_user SET user_subgroup= 'beee-4-a' WHERE user_subgroup = 'beee4a'";

mysql_query($query11, $connect ) or die ('request "Could not execute SQL query" '.$query11.'beee-4-a');

$query12 = "UPDATE mdl_user SET user_subgroup= 'beee-4-b' WHERE user_subgroup = 'beee4b'";

mysql_query($query12, $connect ) or die ('request "Could not execute SQL query" '.$query12.'beee-4-b');

$query13 = "UPDATE mdl_user SET user_subgroup= 'beee-4-c' WHERE user_subgroup = 'beee4c'";

mysql_query($query13, $connect ) or die ('request "Could not execute SQL query" '.$query13.'beee-4-c');

$query14 = "UPDATE mdl_user SET user_subgroup= 'beee-4-d' WHERE user_subgroup = 'beee4d'";

mysql_query($query14, $connect ) or die ('request "Could not execute SQL query" '.$query14.'beee-4-d');

// BEEE-5

$query15 = "UPDATE mdl_user SET user_subgroup= 'beee-5-a' WHERE user_subgroup = 'beee5a'";

mysql_query($query15, $connect ) or die ('request "Could not execute SQL query" '.$query15.'beee-5-a');

$query16 = "UPDATE mdl_user SET user_subgroup= 'beee-5-b' WHERE user_subgroup = 'beee5b'";

mysql_query($query16, $connect ) or die ('request "Could not execute SQL query" '.$query16.'beee-5-b');

$query17 = "UPDATE mdl_user SET user_subgroup= 'beee-5-c' WHERE user_subgroup = 'beee5c'";

mysql_query($query17, $connect ) or die ('request "Could not execute SQL query" '.$query17.'beee-5-c');

$query18 = "UPDATE mdl_user SET user_subgroup= 'beee-5-d' WHERE user_subgroup = 'beee5d'";

mysql_query($query18, $connect ) or die ('request "Could not execute SQL query" '.$query18.'beee-5-d');

//ELECTRONICS
//BEE-2

$query19 = "UPDATE mdl_user SET user_subgroup= 'bee-2-a' WHERE user_subgroup = 'bee2'";

mysql_query($query19, $connect ) or die ('request "Could not execute SQL query" '.$query19.'bee-2-a');

//BEE-3

$query20 = "UPDATE mdl_user SET user_subgroup= 'bee-3-a' WHERE user_subgroup = 'bee3a'";

mysql_query($query20, $connect ) or die ('request "Could not execute SQL query" '.$query20.'bee-3-a');

$query20b = "UPDATE mdl_user SET user_subgroup= 'bee-3-b' WHERE user_subgroup = 'bee3b'";

mysql_query($query20b, $connect ) or die ('request "Could not execute SQL query" '.$query20b.'bee-3-b');

//BEE-4
$query21 = "UPDATE mdl_user SET user_subgroup= 'bee-4-a' WHERE user_subgroup = 'bee4a'";

mysql_query($query21, $connect ) or die ('request "Could not execute SQL query" '.$query21.'bee-4-a');

$query22 = "UPDATE mdl_user SET user_subgroup= 'bee-4-b' WHERE user_subgroup = 'bee4b'";

mysql_query($query22, $connect ) or die ('request "Could not execute SQL query" '.$query22.'bee-4-b');

//BEE-5
$query23 = "UPDATE mdl_user SET user_subgroup= 'bee-5-a' WHERE user_subgroup = 'BEE5A' or user_subgroup = 'BEE-5-A'";

mysql_query($query23, $connect ) or die ('request "Could not execute SQL query" '.$query23.'bee-5-a');

$query24 = "UPDATE mdl_user SET user_subgroup= 'bee-5-b' WHERE user_subgroup = 'BEE5B' or user_subgroup = 'BEE-5-B'";

mysql_query($query24, $connect ) or die ('request "Could not execute SQL query" '.$query24.'bee-5-b');

$query25 = "UPDATE mdl_user SET user_subgroup= 'bee-5-c' WHERE user_subgroup = 'BEE5C' or user_subgroup = 'BEE-5-C'";

mysql_query($query25, $connect ) or die ('request "Could not execute SQL query" '.$query25.'bee-5-c');

$query26 = "UPDATE mdl_user SET user_subgroup= 'bee-5-d' WHERE user_subgroup = 'BEE5D' or user_subgroup = 'BEE-5-D'";

mysql_query($query26, $connect ) or die ('request "Could not execute SQL query" '.$query26.'bee-5-d');

$query26e = "UPDATE mdl_user SET user_subgroup= 'bee-5-e' WHERE user_subgroup = 'BEE5E' or user_subgroup = 'BEE-5-E'";

mysql_query($query26e, $connect ) or die ('request "Could not execute SQL query" '.$query26e.'bee-5-e');

$q2 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bee%\""; 
$bee1 = $DB->get_records_sql($q2);
print_object($bee1);

//BIT
echo "<h2>BIT</h2>";
$q3 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bit%\""; 
$bit = $DB->get_records_sql($q3);
print_object($bit);

$query27 = "UPDATE mdl_user SET user_subgroup= 'bit-7' WHERE user_subgroup = 'bit7'";

mysql_query($query27, $connect ) or die ('request "Could not execute SQL query" '.$query27.'bit-7');

$query28 = "UPDATE mdl_user SET user_subgroup= 'bit-8' WHERE user_subgroup = 'bit8'";

mysql_query($query28, $connect ) or die ('request "Could not execute SQL query" '.$query28.'bit-8');

$query29 = "UPDATE mdl_user SET user_subgroup= 'bit-9-a' WHERE user_subgroup = 'bit9a'";

mysql_query($query29, $connect ) or die ('request "Could not execute SQL query" '.$query29.'bit-9-a');

$query30 = "UPDATE mdl_user SET user_subgroup= 'bit-9-b' WHERE user_subgroup = 'bit9B'";

mysql_query($query30, $connect ) or die ('request "Could not execute SQL query" '.$query30.'bit-9-b');

$query31 = "UPDATE mdl_user SET user_subgroup= 'bit-9-c' WHERE user_subgroup = 'bit9c'";

mysql_query($query31, $connect ) or die ('request "Could not execute SQL query" '.$query31.'bit-9-c');

$query32 = "UPDATE mdl_user SET user_subgroup= 'bit-10-a' WHERE user_subgroup = 'BIT10A' or user_subgroup = 'Bit-10-a'";

mysql_query($query32, $connect ) or die ('request "Could not execute SQL query" '.$query32.'bit-10-a');

$query33 = "UPDATE mdl_user SET user_subgroup= 'bit-10-b' WHERE user_subgroup = 'BIT10B' or user_subgroup = 'BIT-10-B'";

mysql_query($query33, $connect ) or die ('request "Could not execute SQL query" '.$query33.'bit-10-b');

$query34 = "UPDATE mdl_user SET user_subgroup= 'bit-10-c' WHERE user_subgroup = 'BIT10C' or user_subgroup = 'BIT-10-C'";

mysql_query($query34, $connect ) or die ('request "Could not execute SQL query" '.$query34.'bit-10-c');

$query35 = "UPDATE mdl_user SET user_subgroup= 'bit-10-c' WHERE user_subgroup = 'BIT10D' or user_subgroup = 'BIT-10-D'";

mysql_query($query35, $connect ) or die ('request "Could not execute SQL query" '.$query35.'bit-10-d');

$query36 = "UPDATE mdl_user SET user_subgroup= 'bit-11-a' WHERE user_subgroup = 'BIT11A' or user_subgroup = 'BIT-11-A'";

mysql_query($query36, $connect ) or die ('request "Could not execute SQL query" '.$query36.'bit-11-a');

$query37 = "UPDATE mdl_user SET user_subgroup= 'bit-11-b' WHERE user_subgroup = 'BIT11B' or user_subgroup = 'BIT-11-B'";

mysql_query($query37, $connect ) or die ('request "Could not execute SQL query" '.$query37.'bit-11-b');

$query38 = "UPDATE mdl_user SET user_subgroup= 'bit-12' WHERE user_subgroup = 'bit12' or user_subgroup = 'BIT-12'";

mysql_query($query38, $connect ) or die ('request "Could not execute SQL query" '.$query38.'bit-12');

$q4 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bit%\""; 
$bit1 = $DB->get_records_sql($q4);
print_object($bit1);

//BICSE
echo "<h2>BICSE</h2>";
$q5 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bicse%\""; 
$bicse = $DB->get_records_sql($q5);
print_object($bicse);

$query39 = "UPDATE mdl_user SET user_subgroup= 'bicse-3' WHERE user_subgroup = 'bicse3'";

mysql_query($query39, $connect ) or die ('request "Could not execute SQL query" '.$query39.'bicse-3');

$query40 = "UPDATE mdl_user SET user_subgroup= 'bicse-4' WHERE user_subgroup = 'bicse4' OR user_subgroup = 'bicse4b' OR user_subgroup = 'bicse4a'";

mysql_query($query40, $connect ) or die ('request "Could not execute SQL query" '.$query40.'bicse-4');

$query41 = "UPDATE mdl_user SET user_subgroup= 'bicse-5-a' WHERE user_subgroup = 'bicse5a'";

mysql_query($query41, $connect ) or die ('request "Could not execute SQL query" '.$query41.'bicse-5-a');

$query42 = "UPDATE mdl_user SET user_subgroup= 'bicse-5-b' WHERE user_subgroup = 'bicse5b'";

mysql_query($query42, $connect ) or die ('request "Could not execute SQL query" '.$query42.'bicse-5-b');

$query43 = "UPDATE mdl_user SET user_subgroup= 'bicse-6-c' WHERE user_subgroup = 'BICSE6C' OR user_subgroup = 'BICSE-6C' OR user_subgroup = 'BICSE-6-C'";

mysql_query($query43, $connect ) or die ('request "Could not execute SQL query" '.$query43.'bicse-6-c');

$query44 = "UPDATE mdl_user SET user_subgroup= 'bicse-6-a' WHERE user_subgroup = 'BICSE6A' OR user_subgroup = 'BICSE-6-A'";

mysql_query($query44, $connect ) or die ('request "Could not execute SQL query" '.$query44.'bicse-6-a');

$query45 = "UPDATE mdl_user SET user_subgroup= 'bicse-6-b' WHERE user_subgroup = 'BICSE6B' OR user_subgroup = 'BICSE-6-B'";

mysql_query($query45, $connect ) or die ('request "Could not execute SQL query" '.$query45.'bicse-6-b');

$query46 = "UPDATE mdl_user SET user_subgroup= 'bicse-7-a' WHERE user_subgroup = 'BICSE7A' OR user_subgroup = 'BICSE-7-A'";

mysql_query($query46, $connect ) or die ('request "Could not execute SQL query" '.$query46.'bicse-7-a');

$query47 = "UPDATE mdl_user SET user_subgroup= 'bicse-7-b' WHERE user_subgroup = 'BICSE7B' OR user_subgroup = 'BICSE-7-B'";

mysql_query($query47, $connect ) or die ('request "Could not execute SQL query" '.$query47.'bicse-7-b');

$query48 = "UPDATE mdl_user SET user_subgroup= 'bicse-8' WHERE user_subgroup = 'BICSE8' OR user_subgroup = 'BICSE-8'";

mysql_query($query48, $connect ) or die ('request "Could not execute SQL query" '.$query48.'bicse-8');

$q6 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bicse%\""; 
$bicse1 = $DB->get_records_sql($q6);
print_object($bicse1);

//BESE
echo "<h2>BESE</h2>";
$q7 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bese%\""; 
$bese = $DB->get_records_sql($q7);
print_object($bese);
$query49 = "UPDATE mdl_user SET user_subgroup= 'BESE-1-a' WHERE user_subgroup = 'BESE1A' or user_subgroup = 'BESE-1-A'";

mysql_query($query49, $connect ) or die ('request "Could not execute SQL query" '.$query49.'BESE-1-a');

$query50 = "UPDATE mdl_user SET user_subgroup= 'BESE-1-b' WHERE user_subgroup = 'BESE1B' or user_subgroup = 'BESE-1-B'";

mysql_query($query50, $connect ) or die ('request "Could not execute SQL query" '.$query50A.'BESE-1-b');

$query51 = "UPDATE mdl_user SET user_subgroup= 'BESE-2-a' WHERE user_subgroup = 'BESE2A' or user_subgroup = 'BESE-2-A'";

mysql_query($query51, $connect ) or die ('request "Could not execute SQL query" '.$query51.'BESE-2-a');

$query52 = "UPDATE mdl_user SET user_subgroup= 'BESE-2-b' WHERE user_subgroup = 'BESE2b' or user_subgroup = 'BESE-2-B'";

mysql_query($query52, $connect ) or die ('request "Could not execute SQL query" '.$query52.'BESE-2-b');

$query53 = "UPDATE mdl_user SET user_subgroup= 'BESE-3' WHERE user_subgroup = 'BESE3'";

mysql_query($query53, $connect ) or die ('request "Could not execute SQL query" '.$query53.'BESE-3');

$query54 = "UPDATE mdl_user SET user_subgroup= 'BESE-4-a' WHERE user_subgroup = 'BESE4A' or user_subgroup = 'BESE-4-A'";

mysql_query($query54, $connect ) or die ('request "Could not execute SQL query" '.$query54.'BESE-4-a');

$query55 = "UPDATE mdl_user SET user_subgroup= 'BESE-4-b' WHERE user_subgroup = 'BESE4B' or user_subgroup = 'BESE-4-B'";

mysql_query($query55, $connect ) or die ('request "Could not execute SQL query" '.$query55.'BESE-4-b');

$q8 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bese%\""; 
$bese1 = $DB->get_records_sql($q8);
print_object($bese1);
//BSCS

echo "<h2>BSCS</h2>";
$q9 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bscs%\""; 
$bscs = $DB->get_records_sql($q9);
print_object($bscs);

$query56 = "UPDATE mdl_user SET user_subgroup= 'BSCS-1' WHERE user_subgroup = 'BSCS1'";

mysql_query($query56, $connect ) or die ('request "Could not execute SQL query" '.$query56.'BSCS-1');

$query57 = "UPDATE mdl_user SET user_subgroup= 'BSCS-2-a' WHERE user_subgroup = 'BSCS2a'";

mysql_query($query57, $connect ) or die ('request "Could not execute SQL query" '.$query57.'BSCS-2-a');

$query58 = "UPDATE mdl_user SET user_subgroup= 'BSCS-2-b' WHERE user_subgroup = 'BSCS2b'";

mysql_query($query58, $connect ) or die ('request "Could not execute SQL query" '.$query58.'BSCS-2-b');

$query59 = "UPDATE mdl_user SET user_subgroup= 'BSCS-3-a' WHERE user_subgroup = 'BSCS3A' or user_subgroup = 'BSCS-3-A'";

mysql_query($query59, $connect ) or die ('request "Could not execute SQL query" '.$query59.'BSCS-3-a');

$query60 = "UPDATE mdl_user SET user_subgroup= 'BSCS-3-b' WHERE user_subgroup = 'BSCS3B' or user_subgroup = 'BSCS-3-B'";

mysql_query($query60, $connect ) or die ('request "Could not execute SQL query" '.$query60.'BSCS-3-b');

$q10 = "SELECT distinct user_subgroup FROM {user} WHERE user_subgroup LIKE \"%bscs%\""; 
$bscs1 = $DB->get_records_sql($q10);
print_object($bscs1);
//


