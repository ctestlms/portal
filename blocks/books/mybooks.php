<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<title>Search Books</title>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />

<style type="text/css">
.box {
	background-color: #F4F4F4;
	border: 1px solid #CCC;
	height: 100px;
	width: 200px;
	padding: 5px;
	display: none;
	position: absolute;
}

#footer {
	font-size: 14px;
	font-family: Verdana, Geneva, Arial, sans-serif;
	background-color: #c00;
	text-align: center;
}

#confirm {
	font-size: 14px;
	font-family: Verdana, Geneva, Arial, sans-serif;
	background-color: #ccc;
	text-align: center;
}
</style>

<script type="text/javascript" language="JavaScript"> 


var cX = 0; var cY = 0; var rX = 0; var rY = 0; 
function UpdateCursorPosition(e){ cX = e.pageX; cY = e.pageY;} 
function UpdateCursorPositionDocAll(e){ cX = event.clientX; cY = event.clientY;} 
if(document.all) { document.onmousemove = UpdateCursorPositionDocAll; } 
else { document.onmousemove = UpdateCursorPosition; } 
function AssignPosition(d) { 
if(self.pageYOffset) { 
rX = self.pageXOffset; 
rY = self.pageYOffset; 
} 
else if(document.documentElement && document.documentElement.scrollTop) { 
rX = document.documentElement.scrollLeft; 
rY = document.documentElement.scrollTop; 
} 
else if(document.body) { 
rX = document.body.scrollLeft; 
rY = document.body.scrollTop; 
} 
if(document.all) { 
cX += rX; 
cY += rY; 
} 
d.style.left = (cX+10) + "px"; 
d.style.top = (cY+10) + "px"; 
} 
function HideText(d) { 
if(d.length < 1) { return; } 
document.getElementById(d).style.display = "none"; 
} 
function ShowText(d) { 
if(d.length < 1) { return; } 
var dd = document.getElementById(d); 
AssignPosition(dd); 
dd.style.display = "block"; 
} 
function ReverseContentDisplay(d) { 
if(d.length < 1) { return; } 
var dd = document.getElementById(d); 
AssignPosition(dd); 
if(dd.style.display == "none") { dd.style.display = "block"; } 
else { dd.style.display = "none"; } 
} 

function setVisibility(id, visibility) {
document.getElemenotById(id).style.display = visibility;
}
function confirmation( message) {
	var answer = confirm(message)
	if (answer){
		return(true);
		
	}
	else{
		return(false);
	//	alert("Thanks for sticking around!")
	}
}
</script>
</head>
<body>


<?php
require_once('../../config.php');$navlinks[] = array('name' => 'Sell Books', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
//	echo $OUTPUT->heading();
require_login();
if(!$export)
print_header('Sell Books Form', 'Sell Books Form', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
require_once('breadcrumb.php');
global $USER;


?>
<?php

require_once("tabs.php");
?>
	<html>
<head>

<!--<div
	style="width: 600px; align: center; margin-left: 300px; margin-right: 100px;">
-->	

</head>
<body>
<?php
#define (MAX_SIZE,'90000000');
?>


<?php tab( "" ); ?>

<?php
$errormsg = ""; //Initialize errors


session_start();



//This function reads the extension of the file. It is used to determine if the file is an image by checking the extension.
function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}

//This variable is used as a flag. The value is initialized with 0 (meaning no error found) and it will be changed to 1 if an errro occures. If the error occures the file will not be uploaded.
$errors=0;
//checks if the form has been submitted
$action		  = optional_param('action', "", PARAM_ALPHANUM);//get action
$book_id	  = optional_param('bookid', "", PARAM_ALPHANUM);//get book id
if ($action=="delete"){
	$conditions = array('id'=>$book_id);
	$sql="SELECT  * from mdl_books where id =$book_id";
	$result=$DB->get_record_sql($sql);
	if($result){
		$DB->delete_records("books", $conditions) ;

		echo "<div id=\"confirm\">Book Deleted Successfully</div>";
	}

}

if ($action=="unavailable"){

	$newrec= new stdClass();
	$newrec->id=$book_id;
	$newrec->timemodified=time();
	$newrec->active=0;
	$sql="SELECT  * from mdl_books where id =$book_id";
	$result=$DB->get_record_sql($sql);
	if($result){
		$DB->update_record("books", $newrec);
		echo "<div id=\"confirm\">Book has been made unavailable successfully</div>";
	}

}
if ($action=="available"){

        $newrec= new stdClass();
        $newrec->id=$book_id;
        $newrec->timemodified=time();
        $newrec->active=1;
        $sql="SELECT  * from mdl_books where id =$book_id";
        $result=$DB->get_record_sql($sql);
        if($result){
                $DB->update_record("books", $newrec);
                echo "<div id=\"confirm\">Book has been made available successfully</div>";
        }

}


if(isset($_POST['Submit']) || isset($_POST['Update']) )
{

	$image=$_FILES['image']['name'];
	$size=filesize($_FILES['image']['tmp_name']);

	if ($image)
	{

		//get the original name of the file from the clients machine
		$filename = stripslashes($_FILES['image']['name']);
		//get the extension of the file in a lower case format
		$extension = getExtension($filename);
		$extension = strtolower($extension);
		//if it is not a known extension, we will suppose it is an error and will not upload the file, otherwize we will do more tests
		if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
		{
			//print error message
			$errormsg .="Unknown extension of uploaded Image </br>";

			$errors=1;
		}
		else
		{

			$target = "images/";
			$target = $target . basename( $_FILES['image']['name']) ;

			$ok=1;
			if(move_uploaded_file($_FILES['image']['tmp_name'], $target))
			{
				// echo "The file ". basename( $_FILES['image']['name']). " has been uploaded";
			}
			else {
				$errormsg .="Sorry, there was a problem uploading your file.";
			}


		}
	}


}

//If no errors registred, print the success message
if(isset($_POST['Submit']) || isset($_POST['Update']) )
{

	$b=true;
	if(($_POST['book_title']!=null) && ($_POST['auth_name']!=null) ){

		if(is_numeric($_POST['auth_name']))
		{
			$errormsg .="Author name is not valid <br />";
		}
		if($_POST['price']!=null){
			if(!is_numeric($_POST['price']))
			{
				$errormsg .="Price must be an integer ";
			}
		}
		if($_POST['isbn']!=null){
			if(strlen($_POST['isbn'])<10 || strlen($_POST['isbn'])>10)
			{
				$errormsg .="ISBN length must be 10 ";
			}
		}
	}
	else
	{
		$errormsg="Please Enter Required Values";
	}
	if($errormsg){
		$b=false;
	}


	if (($_POST['book_title'] ) && $b){

		$imagename = stripslashes($_FILES['image']['name']);
		if($imagename=="" && $_POST['imagefile']){
			$imagename = $_POST['imagefile'];
			
		}
		//get the extension of the file in a lower case format
		$extension = getExtension($bookname);
		$extension = strtolower($extension);
		$file_name=($_FILES['book']['name']);
		$book_title = $_POST['book_title'];
		$auth_name = $_POST['auth_name'];
		$book_category = $_POST['book_category'];
		$book_status= $_POST['book_status'];
		$verified = $_POST['verified'];
		$isbn = $_POST['isbn'];
		$edition = $_POST['edition'];
		$price = $_POST['price'];
		$keyword = $_POST['keyword'];
		$publisher = $_POST['publisher'];
		$publicationyear = $_POST['publicationyear'];

		if( isset($_POST['Update']) ){
			$books_id = $_POST['books_id'];
			$book_id = $_POST['book_id'];
		}

		global $USER,$DB;
		$newrec= new stdClass();
		if( isset($_POST['Update']) ){
			$newrec->id = $books_id;

		}
		//$newrec->id = 65;/////cah
		$newrec->book_name = $book_title;
		$newrec->author_name = $auth_name;
		$newrec->book_category= $book_category;
		$newrec->book_status= $book_status ;
		$newrec->verified="";
		$newrec->keyword=$keyword;
		$newrec->pic=$imagename;
		$newrec->price=$price;
		$newrec->edition=$edition;
		$newrec->userid = $USER->id;
		$newrec->price=$price;
		$newrec->isbn=$isbn;
		$newrec->publisher=$publisher;
		$newrec->publicationyear=$publicationyear;
		$newrec->active=1;
		session_start();
		if(isset($_POST['Submit'])  ){
			$newrec->time=time();
			$newrec->timemodified=time();
			$newid = $DB->insert_record('books', $newrec);

		}

		if(isset($_POST['Update']) ){
			$sql="SELECT  * from mdl_books where id =$newrec->id   ";
			$result=$DB->get_record_sql($sql);
			if($result){
				$newrec->timemodified=time();
				$update=$DB->update_record("books", $newrec);
			}

		}


		if($newid){
			echo "<div align='center' id=\"confirm\">Book Posted Successfully</div>";
			//echo "<br/><div align='center'><b>Book Uploaded: </b>".$book_format."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='mybooks.php?bookid=".$newid."'>Description</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='postBook.php?bookid=".$newid."&action=update'>Update</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='mybooks.php?bookid=".$newid."&action=delete'>Delete</a></div>";
		}
		if($update==1){
			echo "<div id=\"confirm\">Book Updated Successfully</div>";
			//echo "<br/><div align='center'><b>Book Updated: </b>".$book_format."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='mybooks.php?bookid=".$books_id."'>Description</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='postBook.php?bookid=".$books_id."&action=update'>Update</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='description.php?bookid=".$books_id."'>Delete</a></div>";
		}

	}
	else{
		echo "<div id=\"footer\">$errormsg</div>";
	}
}
echo '<br/><p style="text-align: center;font-size:18px;">My Books</p>';
//echo "<br/><div align='center'><b>My Books</b></div>";
$sql="SELECT  * from mdl_books where userid =$USER->id   order by timemodified desc";
$result=$DB->get_records_sql($sql);
if($result){
	echo '<br/><div align="center"><table style="width: 80%;" border="1"   cellspacing="3" cellpadding="5">';
	foreach($result as $row){
		$book_format=$row->author_name;
		if($row->publicationyear!="")
		$book_format.="( ".$row->publicationyear." )";
		$book_format.=$row->book_name;
		if($row->edition!="")
		$book_format.=" , ".$row->edition;
		if($row->publisher!="")
		$book_format.=" , ".$row->publisher;
		if($row->isbn!="")
		$book_format.=" , ".$row->isbn;
		echo "<tr><td>";
		
		echo "<b>$book_format</b>";
		echo "</td><td align='right'>";
		echo "<a href='description.php?bookid=$row->id'>Description</a></td>";
	//	echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
		$context = get_context_instance(CONTEXT_USER, $USER->id);
		if( has_capability('block/custom_reports:getfeedbackreport', $context) ||$row->userid== $USER->id){
	//		echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<td align='right'><a href='postBook.php?bookid=$row->id&action=update'>Update</a></td>";
			//echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
?>			<td align='right'><a href='mybooks.php?bookid=<?php echo $row->id;?>&action=delete' onclick='return confirmation("Are you sure you want to delete this book?")' >Delete</a></td>
<?php		//	echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
			if($row->active==0){
				?><td align='right'><a href='mybooks.php?bookid=<?php echo $row->id;?>&action=available' onclick='return confirmation("Are you sure you want to make this book available?")' >Make available</a></td></tr>
<?php

				//echo "<td align='right'>Sold out</td></tr>";
			}
			else{
				?><td align='right'><a href='mybooks.php?bookid=<?php echo $row->id;?>&action=unavailable' onclick='return confirmation("Are you sure you want to make this book unavailable?")' >Make Unavailable</a></td></tr>
<?php
			}
		}

	}
	echo "</table></div>";
}
else{
	echo "<div id=\"confirm\">No Books Available</div>";

}




?>


<?php

session_start();


//This variable is used as a flag. The value is initialized with 0 (meaning no error found) and it will be changed to 1 if an errro occures. If the error occures the file will not be uploaded.
$errors=0;
//checks if the form has been submitted
if(isset($_POST['SubmitEbook']))
{
	$errormsg = ""; //Initialize errors

	//reads the name of the file the user submitted for uploading
	$image=$_FILES['image']['name'];
	$book=$_FILES['book']['name'];
	//if it is not empty
	if ($image)
	{
		$size=filesize($_FILES['image']['tmp_name']);

		//get the original name of the file from the clients machine
		$filename = stripslashes($_FILES['image']['name']);
		//get the extension of the file in a lower case format
		$extension = getExtension($filename);
		$extension = strtolower($extension);
		//if it is not a known extension, we will suppose it is an error and will not upload the file, otherwize we will do more tests
		if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
		{
			//print error message
			$errormsg .="Unknown extension of uploaded Image </br>";

			$errors=1;
		}
		else
		{

			$target = "images/";
			$target = $target . basename( $_FILES['image']['name']) ;
			$ok=1;
			if(move_uploaded_file($_FILES['image']['tmp_name'], $target))
			{
				//echo "The file ". basename( $_FILES['image']['name']). " has been uploaded";
			}
			else {
				$errormsg .="Sorry, there was a problem uploading your file.";
			}


		}
	}




	if ($book)
	{
		$size=filesize($_FILES['book']['tmp_name']);

		//get the original name of the file from the clients machine
		$bookname = stripslashes($_FILES['book']['name']);
		//get the extension of the file in a lower case format
		$extension = getExtension($bookname);
		$extension = strtolower($extension);
		//if it is not a known extension, we will suppose it is an error and will not upload the file, otherwize we will do more tests
		if (($extension == "jpg") || ($extension == "jpeg") || ($extension == "png") || ($extension == "gif") ||  ($extension == "zip") ||  ($extension == "rar"))
		{
			//print error message
			$errormsg .="Unknown extension of uploaded book<br />";


			$errors=1;
		}
		else
		{
			//get the size of the image in bytes
			//$_FILES['image']['name'] is the temporary filename of the file in which the uploaded file was stored on the server
			$size=filesize($_FILES['book']['tmp_name']);

			//compare the size with the maxim size we defined and print error if bigger


			$target = "EBooks/";

			$target = $target . basename( $_FILES['book']['name']) ;

			$ok=1;

			if(move_uploaded_file($_FILES['book']['tmp_name'], $target))

			{
				//echo "The file ". basename( $_FILES['book']['name']). " has been uploaded";
			}
			else {

				$errormsg .="Sorry, there was a problem uploading your file.";

			}
		}
	}

}


?>


<?php echo $OUTPUT->footer();?>
</body>
	</html>
