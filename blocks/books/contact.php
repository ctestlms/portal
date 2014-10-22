<?php
/* Set e-mail recipient */
require_once('../../config.php');

require_once($CFG->dirroot . '/lib/formslib.php');
require_login();
$navlinks[] = array('name' => 'Advanced Search', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
if(!$export)
print_header('Advanced Search Form', 'Advanced Search Form', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
require_once('breadcrumb.php');
global $USER;
$toemail = $_SESSION['toemail'];
$bookid =$_SESSION['bookid'];
$to_email=$toemail;
//echo "to".$to_email;
$frmemail = $_SESSION['frmemail'];
$yourname = $_POST['yourname'];
$subject  = "Student Book Bank(LMS)";
$comments = check_input($_POST['message'], "Please write your Message");

$message = "$comments"."<br/><br/>If the book is not available with you click on this link <a href='$CFG->wwwroot/blocks/books/mybooks.php?bookid=$bookid&action=unavailable'>Make Unavailable</a> below to set the status of the book as in active so that you don't recieve any further email for getting this book.<br/>";
$headers  = "From: $frmemail\r\n";
$headers .= "Content-type: text/html\r\n";
/* Send the message using mail() function */
//$user = $DB->get_record("user", array('id'=>$userid), "email");
if (!$mail_results=email_to_user($to_email, $USER, $subject, '',  $messagehtml=$message, $attachment='', $attachname='', $usetrueaddress=true, $replyto=$USER->email, $replytoname=$USER->firstname.$USER->lastname, $wordwrapwidth=79)){
	die("could not send email!");
}else
{
	echo"Mail Sent Successfully!";
}
function check_input($data, $problem='')
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	if ($problem && strlen($data) == 0)
	{
		show_error($problem);
	}
	return $data;
}

function show_error($myError)
{
	?>
<html>
<body>


<?php echo $myError; ?>

</body>
</html>
<?php
exit();
}
?>
