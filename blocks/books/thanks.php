<html>
<body>
<?php
require_once('../../config.php');

require_once($CFG->dirroot . '/lib/formslib.php');
require_login();
$navlinks[] = array('name' => 'Advanced Search', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);		
//echo $OUTPUT->heading();
if(!$export)
print_header('Advanced Search Form', 'Contact Form', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
require_once('breadcrumb.php');
echo "<p><b>Your message was sent</b></p>

<p>Your message was successfully sent!
Thank you for contacting, You will get reply
to your inquiry as soon as possible!</p>";

echo $OUTPUT->footer();
?>

</body>
</html>
