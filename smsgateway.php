<?php

//require_once('dbConnect.php');
echo "Testing SMS Gateway<br/>";
$con = mysql_connect('localhost', 'root', 'CDE@567q'); 


if (!$con) {
	die('Could not connect: ' . mysql_error());
}
else {
	echo "Connection Success!!<br/>";
	mysql_select_db('moodle', $con) or die(mysql_error());
	//echo $today = date("m.d.y"); 
	
	$msgid = mysql_query("Select * from mdl_sms_msg where Date = curdate()");
	//echo $msgid["Msg_Id"]."<br/>";
	
	$msgid_arr = array();
	$i=0;
	while($row = mysql_fetch_assoc($msgid)) {
		echo $msgid_arr[$i]->id =  $row['Msg_Id'];
		echo $msgid_arr[$i]->msg = $row['msg'];
		$i++;
	}
	
	//print_r($msgid_arr);
	
	$msg = array();
	$arr = array();
	
	for($k = 0; $k<$i; $k++)
	{
		$id = $msgid_arr[$k]->id;
		echo $id.": ";
		
		// get contacts list for the specific msg
		$contacts = mysql_query("Select * from mdl_sms_contacts
					where Msg_Id = ".$msgid_arr[$k]->id."
					and Status = 'pending'
					and Retrys < '5'");
		
		$j=0;
		while($row_contacts = mysql_fetch_array($contacts))
		{
			//echo $arr[$j]->contact =  $row_contacts['contacts'];
			//echo $arr[$j]->msg =  $msgid_arr[$k]->msg;
			echo $cell_number = $row_contacts['contacts'];
			echo $message = $msgid_arr[$k]->msg;
			echo $retry = $row_contacts['Retrys'];
			
			$message = str_replace(" ","%20",$message);
			$ch = curl_init();
			$url = 'http://10.99.20.160:9090/sendsms?phone='.$cell_number.'&text='.$message;
			//$url = 'http://10.99.27.16:9090/sendsms?phone=03335482902&text=Hello';
			$url = str_replace(" ","%20",$url);
			//echo $url."<br/>";
			curl_setopt($ch, CURLOPT_URL, "$url");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$output = curl_exec($ch);
			curl_close($ch);
			echo "Output: ".$output."<br/>";
			
			if($output == "1")
			{
				/* echo "I am equal to one<br/>";
				echo $cell_number."<br/>";
				echo $id."<br/>"; */
				$updatequery = "UPDATE mdl_sms_contacts SET Status='Sent' WHERE contacts=$cell_number and Msg_Id=$id";
				//echo $updatequery."<br/>";
				$updatestatus = mysql_query($updatequery, $con);
				echo "At the end of output condition<br/>";
			}
			else
			{
				echo $retry."<br/>";
				if($retry < 5)
				{
					$retry++;
					$updatequery = "UPDATE mdl_sms_contacts SET Retrys=$retry WHERE contacts=$cell_number and Msg_Id=$id";
				}
				echo "I am not equal to one<br/>";
			}
			echo "At end of curl <br/>";
			$j++;
		}
	}
}
mysql_close($con); 
?>
