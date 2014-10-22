<?php
require_once('../../config.php');

require_once($CFG->dirroot . '/lib/formslib.php');
$booksid=$_GET['ebookid'];

$navlinks[] = array('name' => 'Advanced Search', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);		
//echo $OUTPUT->heading();
if(!$export)
print_header('Advanced Search Form', 'About Book', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
global $USER,$DB;
$book_title = $_SESSION['book_title'];
$auth_name	= $_SESSION['auth_name'];
$isbn = $_SESSION['isbn'];
$edition = $_SESSION['edition'];
$keyword = $_SESSION['keyword'];

//echo $book_title;
	$sql="SELECT mdl_books.book_name,mdl_books.book_category,mdl_books.author_name,mdl_books.edition from mdl_books,mdl_ebook where  mdl_books.book_id=mdl_ebook.book_id
	and  mdl_ebook.ebook_id='$booksid'";
	//and mdl_books.author_name LIKE '%$auth_name%' and mdl_book.ISBN LIKE '%$isbn%' and mdl_books.edition LIKE '%$edition%' and mdl_books.keyword LIKE '%$keyword%'";
	$result = $DB->get_records_sql($sql);
echo '<span style="font-size:14px;margin-left:470px"><b>Description</b></br></br></span>';
	
	echo ' <table width="30%" border="1" align="center" cellspacing="3" cellpadding="5" ';
	$img="select mdl_books.pic from mdl_books,mdl_ebook where mdl_books.book_id=mdl_ebook.book_id and mdl_ebook.ebook_id='$booksid'";

$resultimg= $DB->get_records_sql($img);

foreach($resultimg as $row)
{
$pic=$row->pic;

}

if($pic){
$img_folder = 'images/'.$pic;
echo "<tr><td>";
 echo "<img src='$img_folder' width=100px height=100px alt= '$img_folder'><br /></td><td></td>";}
else
{
echo "<tr><td>";
//echo "<b> No Image</b>";
echo "<img src='images/p1.jpg' width=100 height=100 margin-left='60' float='left' alt= '$img_folder'><br />";
 
echo "</td><td></td></tr>";
}


	foreach($result as $row)
	{
	echo "<tr><td>";
	echo "<b>Book Name :</b>";
	echo "</td><td>";
	echo "$row->book_name</br>";
	echo "</td></tr>";
	
	echo "<tr><td>";
	echo "<b>Author Name :</b>";
	echo "</td><td>";
	echo "$row->author_name</br>";
	echo "</td></tr>";
	

	
	echo "<tr><td>";
	
	
	echo "<b>Edition :</b>";
	echo "</td><td>";
	echo "$row->edition</br>";
	echo "</td></tr>";
	
	echo "<tr><td>";
	echo "<b>Book Category :</b>";
	echo "</td><td>";
	echo "$row->book_category</br>";
	echo "</td></tr>";
	
	
	}
	
/*$img="select mdl_books.pic from mdl_books,mdl_ebook where mdl_books.book_id=mdl_ebook.book_id and mdl_ebook.ebook_id='$booksid'";

$resultimg= $DB->get_records_sql($img);

foreach($resultimg as $row)
{
$pic=$row->pic;

}

if($pic){
$img_folder = 'images/'.$pic;
echo "<tr><td>";
 echo "<img src=$img_folder width=100px height=100px alt= '$img_folder'><br />";}
else 
{
echo "</td><td>";
echo "<b> No Image</b>";
echo "</td></tr>";
}
*/
echo "</table>";





echo $OUTPUT->footer();?>
