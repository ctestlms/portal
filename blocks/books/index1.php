<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<title>Search Books</title>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="" />
<meta name="keywords"    content="" />
<link rel="stylesheet" type="text/css" href="tab-view.css" />
<style type="text/css">
 #footer  
{
    font-size: 14px;
     font-family: Verdana, Geneva, Arial, sans-serif;
     background-color: #c00;
     text-align: center;
	position:absolute; TOP:45px; WIDTH:580px;
	 }

</style>

<script language="JavaScript">
/*function setVisibility(id, visibility) {
document.getElemenotById(id).style.display = visibility;
}*/
</script>
</head>
<body>

	<?php
require_once('../../config.php');

require_once($CFG->dirroot . '/lib/formslib.php');

$navlinks[] = array('name' => 'Advanced Search', 'link' => null, 'type' => 'activityinstance');
	$navigation = build_navigation($navlinks);		
//	echo $OUTPUT->heading();
	if(!$export)
		print_header('Advanced Search Form', 'Advanced Search Form', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
  global $USER;


	?>
	<?php
	require_once("tabs.php");
	?>
	<html>
	<head>
	<?php tabs_header(); ?>
	</head>
	<body>
	<div style="width:600px; align: center; ">
	<?php tabs_start(); ?>
	<?php tab( "Text Books" ); ?>
<table border="0" align="center">
</br></br>
<form name="mForm" method="post" enctype="multipart/form-data" action="" onsubmit="return validateForm();">
<tr><td><b>Book Title</b></td><td><input type="text" name="book_title" onfocus="if(this.value==this.defaultValue){this.value='';}" onblur="if(this.value==''){this.value=this.defaultValue;}"></td></tr>
<tr><td><b>Author Name </b></td><td><input type="text" name="auth_name"></td></tr>
<tr><td><b>Edition  </b></td><td><input type="text" name="edition"></td></tr>
<tr><td><b>Keyword </b></td><td><input type="text" name="keyword"></td></tr>
<tr><td><b>ISBN</b></td><td><input type="text" name="isbn"></td></tr>
<tr><td><b>Book Category </b></td><td><input type="text" name="book_category"></td></tr>
<tr><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="submitbutton" type="submit" value="Submit"></td></tr>
</form></table>	

	<?php
	session_start();
 /*class advanceSearchForm extends moodleform {
 
    function definition() {
        global $CFG,$OUTPUT;
		//echo '<align=center><a href="">          Text Books</a> | <a href="ebooks.php">                    EBooks</a> ';
	
// echo '<span style="padding-left:110px"><a href="">Text Books</a></span>';
// echo '<span style="padding-left:110px"><a href="ebooks.php">Ebooks</a></br></br></span>';
        $mform =& $this->_form; // Don't forget the underscore! 
		$mform->addElement('text', 'book_title', 'Book Title', 'maxlength="100" size="25" ');
		$mform->addElement('text', 'auth_name', 'Author Name', 'maxlength="100" size="25" ');
		$mform->addElement('text', 'isbn', 'ISBN', 'maxlength="100" size="25" ');
		$mform->addElement('text', 'edition', 'Edition', 'maxlength="100" size="25" ');
		$mform->addElement('text', 'keyword', 'Keyword', 'maxlength="100" size="25" ');
		$options = array('canUseHtmlEditor'=>'detect',
			'rows' => 10,
			'cols' => 65,
			'width' => 0,
			'height'=> 0,
			'course'=> 0,
			);
		$mform->addElement('submit', 'submitbutton', 'Submit');
		}
	 }
	 $mform = new advanceSearchForm();
$mform->display();*/
session_register();
session_start(); 
 $errormsg = ""; //Initialize errors 
$book_title=$_POST['book_title'];
$auth_name=$_POST['auth_name'];
$isbn=$_POST['isbn'];
$edition=$_POST['edition'];
$keyword=$_POST['keyword'];
$book_category=$_POST['book_category'];
$_SESSION['book_title'] = $book_title;
$_SESSION['auth_name'] = $auth_name;
$_SESSION['isbn'] = $isbn;
$_SESSION['edition'] = $edition;
$_SESSION['keyword'] = $keyword;
$_SESSION['book_category'] = $book_category;
if (isset($_POST['submitbutton'])) {
 $errormsg = ""; //Initialize errors 
if ((($_POST['isbn']) == NULL) && (($_POST['book_title']) == NULL) && (($_POST['auth_name']) == NULL) && (($_POST['edition']) == NULL) && (($_POST['keyword']) == NULL) && (($_POST['book_category']) == NULL)){
$errormsg .="Fill any Field<br />";
}
else{
if (($_POST['isbn'] != NULL) || ($_POST['book_title'] != NULL) || ($_POST['auth_name'] != NULL) || ($_POST['keyword'] != NULL) || ($_POST['edition'] != NULL) || ($_POST['book_category'] != NULL)) {


if(is_numeric($_POST['auth_name']) && ($_POST['auth_name'] != NULL))
{
$errormsg .="Please enter vali author name<br />";
    // $errormsg .="Author name is not valid <br />";
	}
 if($_POST['edition'] != NULL && !is_numeric($_POST['edition']))
{$errormsg .="Edition must be a number <br />";
}
if(($_POST['edition'] != NULL && $_POST['book_title'] == NULL))
{$errormsg .="Please enter book name<br />";
}

	else{
	$sql="SELECT mdl_book.books_id,mdl_books.userid,mdl_books.book_name,mdl_books.book_category,mdl_books.author_name,mdl_book.price,mdl_book.isbn,mdl_books.edition from mdl_books,mdl_book where mdl_books.book_id=mdl_book.book_id
    AND book_name like '%$book_title%' and mdl_books.author_name LIKE '%$auth_name%' and mdl_book.isbn LIKE '%$isbn%' and mdl_books.edition LIKE '%$edition%' and mdl_books.keyword LIKE '%$keyword%' and mdl_books.book_category LIKE '%$book_category%'";
//echo $sql;

	$result = $DB->get_records_sql($sql);
	
	$_SESSION['userid'] = $userid;
	//$result = mysql_query($sql) or die (mysql_error());
	if($result) {
	echo '<span style="font-size:14px"><b>Search Result:</b></span>';
	echo ' <table width="80%" border="1"  align="center" cellspacing="3" cellpadding="5" >';
	//echo "<tr>  <th>Title</th> <th>Author Name</th> <th>Price</th> <th>ISBN</th> <th>Edition</th> <th>Book Category</th>  </tr>";

	foreach($result as $row)
	{
	
	$userid=$row->userid;
	$booksid=$row->books_id;
	//echo $userid;
	//echo "$row->userid";
	echo "<tr><td>";
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<b>$row->book_name</b>";
	echo "</td><td>"; 
	/*echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "$row->author_name";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;";
	echo "$row->price";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;";
	echo "$row->isbn";
	echo "</td><td>";
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "$row->edition";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
	echo "$row->book_category";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp";*/
	echo "<a href='description.php?bookid=".$booksid."'>Description</a>";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp";
	echo "<a href='contactme.php?userid=".$userid."'>Contact</a>";
	echo "</td></tr>"; 
	
}
echo "</table>";
//echo '<span style="padding-left:110px"><a href="download.php">Download</a></br></br></span>';
echo "</br>";
}
else
{
$errormsg .="No Book Found<br />";}

}
}
}
if($errormsg){ echo "<div id=\"footer\">$errormsg</div>"; }
}?>
<?php tab( "EBooks" ); ?>
<table border="0" align="center">
</br></br>
<form name="mForm" method="post" enctype="multipart/form-data" action="" onsubmit="return validateForm();">
<tr><td><b>Book Title</b></td><td><input type="text" name="book_title"></td></tr>
<tr><td><b>Author Name </b></td><td><input type="text" name="auth_name"></td></tr>
<tr><td><b>Edition  </b></td><td><input type="text" name="edition"></td></tr>
<tr><td><b>Keyword </b></td><td><input type="text" name="keyword"></td></tr>
<tr><td><b>Book Category </b></td><td><input type="text" name="book_category"></td></tr>
<tr><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="SearchEbook" type="submit" value="Submit"></td></tr>
</form></table>
	<?php
	session_start();
	/*class advanceSearchForm2 extends moodleform {
 
    function definition() {
        global $CFG,$OUTPUT;
		//echo '<align=center><a href="">          Text Books</a> | <a href="ebooks.php">                    EBooks</a> ';
	
// echo '<span style="padding-left:110px"><a href="">Text Books</a></span>';
// echo '<span style="padding-left:110px"><a href="ebooks.php">Ebooks</a></br></br></span>';
        $mform =& $this->_form; // Don't forget the underscore! 
		$mform->addElement('text', 'book_title', 'Book Title', 'maxlength="100" size="25" ');
		$mform->addElement('text', 'auth_name', 'Author Name', 'maxlength="100" size="25" ');
		//$mform->addElement('text', 'isbn', 'ISBN', 'maxlength="100" size="25" ');
		$mform->addElement('text', 'edition', 'Edition', 'maxlength="100" size="25" ');
		$mform->addElement('text', 'keyword', 'Keyword', 'maxlength="100" size="25" ');
		$options = array('canUseHtmlEditor'=>'detect',
			'rows' => 10,
			'cols' => 65,
			'width' => 0,
			'height'=> 0,
			'course'=> 0,
			);
		$mform->addElement('submit', 'SearchEbook', 'Submit');
		}
	 }
	 $mform = new advanceSearchForm2();
$mform->display();*/

//$book_title=$_POST['book_title'];
$errormsg = ""; //Initialize errors 
$auth_name=$_POST['auth_name'];

$edition=$_POST['edition'];
$keyword=$_POST['keyword'];
$book_title = $_SESSION['book_title'];
$auth_name	= $_SESSION['auth_name'];
$isbn = $_SESSION['isbn'];
$edition = $_SESSION['edition'];
$keyword = $_SESSION['keyword'];
$book_category = $_SESSION['book_category'];
if (isset($_POST['SearchEbook'])) {

if ((($_POST['book_title']) == NULL) && (($_POST['auth_name']) == NULL) && (($_POST['edition']) == NULL) && (($_POST['keyword']) == NULL) && (($_POST['book_category']) == NULL)){
$errormsg .="Fill any Field<br />";}
else{
if (($_POST['book_title'] != NULL) || ($_POST['auth_name'] != NULL) || ($_POST['keyword'] != NULL) || ($_POST['edition'] != NULL) || ($_POST['book_category'] != NULL)) {


if(is_numeric($_POST['auth_name']) && ($_POST['auth_name'] != NULL))
{
$errormsg .="Please enter vali author name<br />";
    // $errormsg .="Author name is not valid <br />";
	}
	
 if($_POST['edition'] != NULL && !is_numeric($_POST['edition']))
{$errormsg .="Edition must be a number <br />";
}
if(($_POST['edition'] != NULL && $_POST['book_title'] == NULL))
{$errormsg .="Please enter book name<br />";}
else{
	$sql="SELECT mdl_ebook.ebook_id,mdl_books.book_name,mdl_books.book_category,mdl_books.author_name,mdl_books.edition from mdl_books,mdl_ebook where mdl_books.book_id=mdl_ebook.book_id 
   AND book_name like '%$book_title%' and mdl_books.author_name LIKE '%$auth_name%' and mdl_books.edition LIKE '%$edition%' and mdl_books.keyword LIKE '%$keyword%' and mdl_books.book_category LIKE '%$book_category%'";
	
	
	
	$result = $DB->get_records_sql($sql);
	if($result) {
	echo '<span style="font-size:14px"><b>Search Result:</b></span>';
	echo ' <table width="80%" border="1"  align="center" cellspacing="3" cellpadding="5" >';

	//echo "<tr>  <th>Title</th> <th>Author Name</th> <th>Price</th> <th>ISBN</th> <th>Edition</th> <th>Book Category</th> </tr>";

foreach($result as $row)
{
		
		$booksid=$row->ebook_id;
		//echo $booksid;
		echo "<tr><td>";
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<b>$row->book_name</b>";
	echo "</td><td>"; 
	/*echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "$row->author_name";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;";
	echo "$row->price";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;";
	echo "$row->isbn";
	echo "</td><td>";
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "$row->edition";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
	echo "$row->book_category";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp";*/
	echo "<a href='edescription.php?ebookid=".$booksid."'>Description</a>";
	echo "</td><td>"; 
	
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
	echo "<a href='download.php?ebookid=".$booksid."'>Download</a>";
	echo "</td></tr>"; 
}

echo "</table>";
echo "</br>";
}
else
{
$errormsg .="No Book Found<br />";}

}}
}
 echo "<div id=\"footer\">$errormsg</div>";
}
?>

	<?php tabs_end(); ?>
	</div>
	</body>
	</html>
