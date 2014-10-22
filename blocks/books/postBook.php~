<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<title>Search Books</title>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="" />
<meta name="keywords"    content="" />

<style type="text/css">

.box { 
background-color: #F4F4F4; 
border: 1px solid #CCC; 
height: 100px; 
width: 200px; 
padding: 5px; 
display:none; 
position:absolute; 
} 

  #footer  
{
   
     font-size: 14px;
     font-family: Verdana, Geneva, Arial, sans-serif;
     background-color: #c00;
     text-align: center;


	 
   
     
     }
	 

	   #confirm
{
   
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
</script>
</head>
<body>


<?php
require_once('../../config.php');$navlinks[] = array('name' => 'Sell Books', 'link' => null, 'type' => 'activityinstance');
	$navigation = build_navigation($navlinks);		
	echo $OUTPUT->heading();
	if(!$export)
		print_header('Sell Books Form', 'Sell Books Form', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
  global $USER;


	?>
<?php
	require_once("tabs.php");
	?>
	<html>
	<head>
	
	<div style="width:600px; align: center; margin-left: 300px;
	margin-right: 100px;">
	<?php tabs_header(); ?>
	</head>
	<body>
	<?php 
	#define (MAX_SIZE,'90000000');
?>
	<?php tabs_start(); ?>

	<?php tab( "Books" ); ?>
	
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
if(isset($_POST['Submit'])) 
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
$errormsg .="Unknown extensionof uploaded Image </br>";

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
if(isset($_POST['Submit'])) 
{

 $b=true;
 if(($_POST['book_title']!=null) && ($_POST['auth_name']!=null) && ($_POST['price']!=null)){
 
 if(is_numeric($_POST['auth_name']))
 {

$errormsg .="Author name is not valid <br />";
 
 }
 if(!is_numeric($_POST['price']))
 {
$errormsg .="Price must be an integer ";
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


 if (($_POST['book_title'] && $_POST['price']) && $b){  
    
$imagename = stripslashes($_FILES['image']['name']);
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

	global $USER,$DB;
$newrec= new stdClass();
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
session_start();
$newid = $DB->insert_record('books', $newrec);
$_SESSION['price']=$price;
$_SESSION['isbn']=$isbn;
$_SESSION['edition']=$edition;
$_SESSION['id']=$newid;
if($newid){
global $DB;
$id = $_SESSION['id'];
$price = $_SESSION['price'];
$isbn = $_SESSION['isbn'];

$newrec= new stdClass();
$newrec->price=$price;
$newrec->isbn=$isbn;

$newrec->book_id=$id;
$newid2 = $DB->insert_record('book', $newrec);

$_SESSION['id'] = true;
$_SESSION['book_idss'] = $newid2;

echo "<div id=\"confirm\">Book Posted Successfully</div>";
}


}
else{  

 echo "<div id=\"footer\">$errormsg</div>";  }
 
  }  
     

?>

<!--next comes the form, you must set the enctype to "multipart/frm-data" and use an input type "file" --> 
<center><table border="0">
<h2 align='center'>Post Books</h2>
<form name="mForm" method="post" enctype="multipart/form-data" action="" onsubmit="return validateForm();">
<tr><td><b>Book Title:</b></td><td>*<input type="text" name="book_title"></td></tr>
<tr><td><b>Author Name :</b></td><td>*<input type="text" name="auth_name"></td></tr>
<tr><td><b>Edition  :</b></td><td>&nbsp;<input type="text" name="edition"></td></tr>
<tr><td><b>Price :</b></td><td>*<input type="text" name="price"></td></tr>
<tr><td><b>Keyword :</b></td><td>&nbsp;<input type="text" name="keyword"></td></tr>
<tr><td><b>ISBN:</b></td><td>&nbsp;<input type="text" name="isbn" maxlength="10"><span 
onmouseover="ShowText('Message'); return true;" 
onmouseout="HideText('Message'); return true;" 
href="javascript:ShowText('Message')"> 
<img src="help.png"> 
</span> 
<div 
id="Message" 
class="box" 
> 
ISBN, a ten-digit number assigned to every book before publication, recording such details as language and publisher
 </div></td></tr>
<tr><td><b>Book Status :</b></td><td>&nbsp;<select name="book_status">
<option value="new">New</option>
<option value="old">Old</option>
</select></td></tr>
<tr><td><b>Book Category :</b></td><td>&nbsp;<select name="book_category">
<option value="Entertainment & Leisure">Entertainment & Leisure</option>
<option value="Business & Finance">Business & Finance</option>
<option value="Hobbies">Hobbies</option>
<option value="Law & Order">Law & Order</option>
<option value="Fiction">Fiction</option>
<option value="History">History</option>
<option value="Educational">Educational</option>
</select></td></tr>
<tr><td><b>Book Image:</b></td><td>&nbsp;<input type="file" name="image" value="Book "></td></tr>
<tr><td><input name="Submit" type="submit" value="Submit"></td><td></td></tr>
</form>
</table>	</center>

	<?php tab( "Ebooks" ); ?>
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
$errormsg .="Unknown extensionof uploaded Image </br>";

$errors=1;
}
else
{ 
if ($size > MAX_SIZE*2048)
{
$errormsg .="You have exceeded the size limit avaliable for Images<br />";

$errors=1;
}
else{
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
echo $size;
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

//If no errors registred, print the success message
if(isset($_POST['SubmitEbook'])  && isset($_FILES['book'])  ) 
{


 $b=true;
 if(($_POST['book_title']!=null) && ($_POST['auth_name']!=null)){
 
 if(is_numeric($_POST['auth_name']))
 {

$errormsg .="Author name is not valid <br />";
 
 }

  if(!$_FILES['book']['name'])
 {

$errormsg .="Book Must be uploaded <br />";
 
 }
 }
 else
{
$errormsg="Please Enter Required Values";
}
 if($errormsg){
 $b=false;
 }


 if (($_POST['book_title'] && $_FILES['book']['name']) && $b ){  
    
$bookname = stripslashes($_FILES['book']['name']);
$imagename = stripslashes($_FILES['image']['name']);
//get the extension of the file in a lower case format
$extension = getExtension($bookname);
$extension = strtolower($extension);
$file_name=($_FILES['book']['name']);

	$book_title = $_POST['book_title'];
	$auth_name = $_POST['auth_name'];
	$book_category = $_POST['book_category'];
	$book_status= $_POST['book_status'];
	$verified = $_POST['verified'];
	$edition = $_POST['edition'];
	$keyword = $_POST['keyword'];

	global $USER,$DB;
$newrec= new stdClass();
$newrec->book_name = $book_title;
$newrec->author_name = $auth_name;
$newrec->book_category= $book_category;
$newrec->book_status= $book_status ;
$newrec->verified="";
$newrec->keyword=$keyword;
$newrec->pic=$imagename;
$newrec->edition=$edition;
$newrec->userid = $USER->id;
session_start();
$newid = $DB->insert_record('books', $newrec);

$_SESSION['id']=$newid;
$_SESSION['filename']=$file_name;
if($newid){
global $DB;
$id = $_SESSION['id'];
$upload_file_name = $_SESSION['filename'];
$newrec= new stdClass();
$newrec->upload_file_name=$upload_file_name;
$newrec->book_id=$id;
$newid2 = $DB->insert_record('ebook', $newrec);

echo "<div id=\"confirm\">Book Posted Successfully</div>";
}


}
else{  

 echo "<div id=\"footer\">$errormsg</div>";  }
 
  }
     

?>

<!--next comes the form, you must set the enctype to "multipart/frm-data" and use an input type "file" --> 
<center><table border="0">
<h2 align='center'>Post EBooks</h2>
<form name="mForm" method="post" enctype="multipart/form-data" action="" onsubmit="return validateForm();">
<tr><td><b>Book Title:</b></td><td>*<input type="text" name="book_title"></td></tr>
<tr><td><b>Author Name :</b></td><td>*<input type="text" name="auth_name"></td></tr>
<tr><td><b>Edition  :</b></td><td>&nbsp;<input type="text" name="edition"></td></tr>
<tr><td><b>Keyword :</b></td><td>&nbsp;<input type="text" name="keyword"></td></tr>
<tr><td><b>Book Status :</b></td><td>&nbsp;<select name="book_status">
<option value="new">New</option>
<option value="old">Old</option>
</select></td></tr>
<tr><td><b>Book Category :</b></td><td>&nbsp;<select name="book_category">
<option value="Entertainment & Leisure">Entertainment & Leisure</option>
<option value="Business & Finance">Business & Finance</option>
<option value="Hobbies">Hobbies</option>
<option value="Law & Order">Law & Order</option>
<option value="Fiction">Fiction</option>
<option value="History">History</option>
<option value="Educational">Educational</option>
</select></td></tr>
<tr><td><b>Book Image:</b></td><td>&nbsp;<input type="file" name="image" value="Book "></td></tr>
<tr><td><b>Book:</b></td><td>*<input type="file" name="book" ></td></tr>

<tr><td><input name="SubmitEbook" type="submit" value="Submit"></td><td></td></tr>
</form>
</table>	</center>
	<?php tabs_end(); ?>
	</table>
	</div>
	</body>
	</html>
