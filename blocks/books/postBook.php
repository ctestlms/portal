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
</script>
</head>
<body>


<?php

require_once('../../config.php');
$navlinks[] = array('name' => 'Sell Books', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
require_login();
//	echo $OUTPUT->heading();
if(!$export)
print_header('Sell Books Form', 'Sell Books Form', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
require_once('breadcrumb.php');
global $USER;
$action		  = optional_param('action', "", PARAM_ALPHANUM);//get action
$book_id	  = optional_param('bookid', "", PARAM_ALPHANUM);//get book id


?>
<?php
require_once("tabs.php");
?>
	<html>
<head>

<div
	style="width: 600px;  ">
	<?php tabs_header(); ?>

</head>
<body>
<?php
#define (MAX_SIZE,'90000000');
?>
<?php tabs_start(); ?>

<?php tab(""  ); ?>

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
/*
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


 if (($_POST['book_title'] ) && $b){

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
 $id =    $_SESSION['id'];
 $price = $_SESSION['price'];
 $isbn =  $_SESSION['isbn'];
 $newrec= new stdClass();
 $newrec->price=$price;
 $newrec->isbn=$isbn;
 $newrec->book_id=$id;
 $newid2 = $DB->insert_record('book', $newrec);
 $_SESSION['id'] = true;
 $_SESSION['book_idss'] = $newid2;
 redirect( $CFG->wwwroot."/blocks/books/mybooks.php") ;
 echo "<div id=\"confirm\">Book Posted Successfully</div>";
 $book_format=$auth_name." , ".$book_title;
 if($edition!="")
 $book_format.=" , ".$edition;
 if($isbn!="")
 $book_format.=" , ".$isbn;
 echo "<br/><div align='center'><b>Book Uploaded</b>".$book_format."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='description.php?bookid=".$newid2."'>Description</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='description.php?bookid=".$newid2."'>Update</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='description.php?bookid=".$newid2."'>Delete</a></div>";



 }


 }
 else{

 echo "<div id=\"footer\">$errormsg</div>";  }



 }
 */
if ($action=="update"){
	$sql="SELECT  * from  mdl_books WHERE id =$book_id;";
	$result=$DB->get_record_sql($sql);
}



?>

	<!--next comes the form, you must set the enctype to "multipart/frm-data" and use an input type "file" -->
	<center>
	<table border="0">
	<?php if ($action=="update"){
		echo "<h2 align='center'>Update Book</h2>";}else{
			echo "<h2 align='center'>Post Book</h2> ";}?>

		<form name="mForm" method="post" enctype="multipart/form-data"
			action="<?php echo  $CFG->wwwroot?>/blocks/books/mybooks.php"
			onsubmit="return validateForm();">
			<?php if ($action=="update"){
				
				echo '<input type="hidden" name="books_id" value= '.$result->id.'>';
			}?>
		
		
		<tr>
			<td><b>Book Title:</b></td>
			<td>*<input type="text" name="book_title"
				value="<?php echo  $result->book_name;?>">
			
			</td>
		</tr>
		<tr>
			<td><b>Author Name :</b></td>
			<td>*<input type="text" name="auth_name"
				value="<?php echo  $result->author_name ;?>">
			
			</td>
		</tr>
		<tr>
			<td><b>Edition :</b></td>
			<td>&nbsp;<input type="text" name="edition"
				value="<?php echo  $result->edition;?>">
			
			</td>
		</tr>
		<tr>
			<td><b>Price :</b></td>
			<td>&nbsp;<input type="text"  name="price"
				value="<?php echo  $result->price;?>">
			
			</td>
		</tr>
		<tr>
			<td><b>Keyword :</b></td>
			<td>&nbsp;<input type="text" name="keyword"
				value="<?php echo  $result->keyword;?>">
			
			</td>
		</tr>
		<tr>
			<td><b>Publisher :</b></td>
			<td>&nbsp;<input type="text" name="publisher"
				value="<?php echo  $result->publisher;?>">
			
			</td>
		</tr>
		<tr>
			<td><b>Year of Publication :</b></td>
			<td>&nbsp; <select name="publicationyear">
			<?PHP
			$selected = ($result->publicationyear == '' ) ? "selected = 'selected'" : "";
				echo "<option value='' $selected>None </option>";
			for ($year = 1900; $year <= 2060; $year++) {
				$selected = ($result->publicationyear == $year ) ? "selected = 'selected'" : "";
				echo "<option value=$year $selected>$year </option>";
			}
					
				?>
				</select>			
			</td>
		</tr>
		<tr>
			<td><b>ISBN:</b></td>

			<td>&nbsp;<input type="text" name="isbn" maxlength="10"
				value="<?php echo  $result->isbn;?>">
					
			
			</td>
		</tr>
		<tr>
			<td><b>Book Status :</b></td>
			<td>&nbsp;<select name="book_status">
			<?php $selected = ($result->book_status == "new" ) ? "selected = 'selected'" : "";?>

					<option value="new" <?php echo  $selected ?>>New</option>
					<?php $selected = ($result->book_status == "old" ) ? "selected = 'selected'" : "";?>
					<option value="old" <?php echo  $selected ?>>Old</option>
			</select></td>
		</tr>
		<tr>
			<td><b>Book Category :</b></td>
			<td>&nbsp;<select name="book_category">
			<?php $selected = ($result->book_category  == "Entertainment & Leisure" ) ? "selected = 'selected'" : "";?>
					<option value="Entertainment & Leisure" <?php echo  $selected ?>>Entertainment
						& Leisure</option>
						<?php $selected = ($result->book_category == "Business & Finance" ) ? "selected = 'selected'" : "";?>
					<option value="Business & Finance" <?php echo  $selected ?>>Business
						& Finance</option>
						<?php $selected = ($result->book_category == "Hobbies" ) ? "selected = 'selected'" : "";?>
					<option value="Hobbies" <?php echo  $selected ?>>Hobbies</option>
					<?php $selected = ($result->book_category == "Law & Order" ) ? "selected = 'selected'" : "";?>
					<option value="Law & Order" <?php echo  $selected ?>>Law & Order</option>
					<?php $selected = ($result->book_category == "Fiction" ) ? "selected = 'selected'" : "";?>
					<option value="Fiction" <?php echo  $selected ?>>Fiction</option>
					<?php $selected = ($result->book_category == "History" ) ? "selected = 'selected'" : "";?>
					<option value="History" <?php echo  $selected ?>>History</option>
					<?php $selected = ($result->book_category == "Educational" ) ? "selected = 'selected'" : "";?>
					<option value="Educational" <?php echo  $selected ?>>Educational</option>
			</select></td>
		</tr>
		<?php if ($action=="update"){?>
		<tr>
			<td><b>Current Image:</b></td>
			<td>&nbsp;<img width='30px' height='30px'src='<?php echo 'images/'.$result->pic; ?> '></img>
			
			</td>
		</tr>
		 <?php }?>
		<tr>
			<?php if ($action=="update"){
				echo "<input type='hidden' name='imagefile' value='".$result->pic."'.>";
			echo "<td><b>New Image:</b></td>";
			  }
			  else {
			echo "<td><b>Book Image:</b></td>";
			 }?>
			<td>&nbsp;<input type="file" name="image" value="Book ">
			
			</td>
		</tr>
		<tr>
			<td><?php if ($action=="update"){
				echo '<input name="Update" type="submit" value="Update">';}else{
					echo '<input name="Submit" type="submit" value="Submit">';}?>
			</td>
			<td></td>
		</tr>
		</form>
	</table>
	</center>


	<?php tabs_end(); ?>	

	<!--next comes the form, you must set the enctype to "multipart/frm-data" and use an input type "file" -->
	
	</div>
	<?php echo $OUTPUT->footer();?>
</body>
	</html>
