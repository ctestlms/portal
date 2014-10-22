<?php

#echo phpinfo();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
//require_once('dbConnect.php');

$con = mysql_connect("localhost","root","CDE@456q");

if (!$con) {
	die('Could not connect: ' . mysql_error());
}
else {
	mysql_select_db("moodle24", $con) or die(mysql_error());
	/*$database = "__query";
	echo $database."<br/>";
	
	//$sql = "SELECT Msg_Id, Date from mdl_sms_msg WHERE Date = curdate()";
	$sql = "SELECT * from mdl_sms_msg";
	$result = mysql_query($sql) or die(mysql_error());
	//print_r(mysql_fetch_array($result));
	while ($row = mysql_fetch_object($result)) {
    echo $row->Msg_Id."--";
    echo $row->msg."<br />";
}
	*/
	$msgid = mysql_query("Select Msg_Id, Date from mdl_sms_msg where Date = curdate()");
	//echo $msgid;
	if(!$msgid)
	{ 
		// Do nothing
		echo "Nothing in msgid...\n";
	}
	else
	{
		$msgid_arr = array();
		$i=0;
		
		while($row = mysql_fetch_assoc($msgid)) {
			$msgid_arr[$i] =  $row['Msg_Id'];
			$i++;
		}

		$contacts = array();
		$msg = array();
		for($k = 0; $k<$i; $k++)
		{

			// get contacts list for the specific msg
			$contacts[$k] = mysql_query("Select contacts from mdl_sms_contacts
						where Msg_Id = '$msgid_arr[$k]'
						and Status = 'pending'
						and Retrys < '5'");
			// get message content
			$msg[$k] = mysql_query("Select mdl_sms_msg.msg from mdl_sms_msg
						join mdl_sms_contacts ON mdl_sms_msg.Msg_Id = mdl_sms_contacts.Msg_Id
						where mdl_sms_contacts.Msg_Id = '$msgid_arr[$k]'
						and mdl_sms_contacts.Status = 'pending'
						");

			$arr = array();
			$cor = array();
			$cor_n = array();
			$j=0;
			while($row = mysql_fetch_assoc($contacts[$k]))
			{
				$arr[$j] = array('contacts' => $row['contacts']);
				$j++;
			}

			$row_msg = mysql_fetch_array($msg[$k]);

			if($row_msg['msg'] != null){
				$arr[] = array('msg' => $row_msg['msg']);

				print json_encode($arr);
			}
		}
	}
}
mysql_close($con);

?>
</body>
</html>