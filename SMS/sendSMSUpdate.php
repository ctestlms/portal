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

	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}

	else
	{
		// echo "Connection was successful";
		mysql_select_db("moodle24", $con) or die(mysql_error());
		//	$lat = $_GET['status'];

		$rev = $_REQUEST['action'];

		$arr = array($rev);
		//$cont =  implode("-",$arr);
		//echo $cont;
		// echo "this is returned: " . $reversed;
		

		// mysql_query("insert into mdl_sms_contacts (Msg_Id,sender_id,contacts,Date,Status,Retrys) values ('20000','2323','0323232',NOW(), 'sent', '0')");


		mysql_query("UPDATE mdl_sms_contacts
				SET Status = 'Now_Sent'
				WHERE contacts =".$rev."");

		$result = mysql_query("SELECT Retrys
				from 	mdl_sms_contacts
				where  Date = curdate()
				and 	Status = 'pending'");
		if($result == null)
		{
			//echo "no records to show";

		}
		else
		{

			//	echo "showing ...";

			while($row = mysql_fetch_array($result))
			{
				$r = $row['Retrys'];
				if($r<5)
				{
					$r++;
					mysql_query("UPDATE mdl_sms_contacts
							SET Retrys = ".$r."
							where contacts <> ".$rev."");
				}
				/*		else
				 {
				$to = "rabia.iqbal@seecs.edu.pk";
				$subject = "Test mail";
				$message = "Hello! This is a simple email message.";
				$from = "rabia.iqbal@seecs.edu.pk";
				$headers = "From:" . $from;
				mail($to,$subject,$message,$headers);
				echo "Mail Sent.";
				}
				*/
			}


		}
	}
	mysql_close($con);
	?>