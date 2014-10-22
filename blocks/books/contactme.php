
<html>
<body>
<?php
require_once('../../config.php');
$user_id=$_GET['userid'];

global $USER;
//echo $USER->id;
require_once($CFG->dirroot . '/lib/formslib.php');
require_login();
$navlinks[] = array('name' => 'Advanced Search', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
//echo $OUTPUT->heading();
if(!$export)
print_header('Advanced Search Form', 'Contact Form', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
require_once('breadcrumb.php');
global $USER,$DB;

$sql="select *,mdl_books.id as bookid from mdl_user,mdl_books where mdl_books.userid=mdl_user.id AND mdl_user.id='$user_id' ";

$result = $DB->get_record_sql($sql);

echo '<span style="font-size:14px;margin-left:440px"><b>Send Message:</b></br></span>';

echo '<table width="30%" border="1" align="center" cellspacing="3" cellpadding="5">';
//echo "<tr><td>";
//echo "<b>To </b>";
//foreach($result as $row){

	$_SESSION['bookid']=	$result->bookid;
	$toemail=$result->email;
	$id=	$result->userid;
	//echo $id;
//	echo "</td><td>";
//	echo $toemail;
//	echo "</td></tr>";
//}

$sql_usersemail="select mdl_user.email from mdl_user where mdl_user.id='$USER->id'";
$result_email = $DB->get_record_sql($sql_usersemail);
//echo "<tr><td>";
//echo "<b>From </b>";
//foreach($result_email as $row)
//{
	$frmemail=$result_email->email;
//	echo "</td><td>";
//	echo $frmemail;
//	echo "</td></tr>";
//}
$_SESSION['toemail']=$result;//$toemail;
$_SESSION['frmemail']=$frmemail;
$yourname = $_POST['yourname'];
$subject  = $_POST['subject'];
$headers  = "From: $frmemail\r\n";
$headers .= "Content-type: text/html\r\n";
if (isset($_POST['submitbuton'])) {
	echo "Message has been sent";}
	echo "<tr><td>";
	echo '<form name="mForm" method="post" enctype="multipart/form-data" action="contact.php" onsubmit="return validateForm();">';
//	echo '<b>Your Name </b>';
//	echo "</td><td>";
//	echo '<input type="text" name="yourname" />';
//	echo "</td></tr>";
//	echo "<tr><td>";
//	echo '<b>Subject </b>';
//	echo "</td><td>";
//	echo '<input type="text" name="subject" /><br /><br />';
//	echo "</td></tr>";
	//echo "<tr><td>";
	echo '<b>Message </b>*</br></br>';
	//echo "</td></tr>";
	echo "</td><td>";
	echo '<textarea name="message" rows="10" cols="40"></textarea>';
	echo "</td></tr>";
	echo "<tr><td></td>";
	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;";
	echo '<span style="font-size:14px;margin-left:70px"><p><input type="submit" value="Send" name="submitbuton"></p></span>';
	echo "</td></tr>";
	echo '</form>';
	echo '</table></center>';
	echo $OUTPUT->footer();
	?>
</body>
</html>
