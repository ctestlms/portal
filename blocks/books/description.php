<?php
require_once('../../config.php');

require_once($CFG->dirroot . '/lib/formslib.php');
$books_id=$_GET['bookid'];
require_login();
//echo $books_id;
$navlinks[] = array('name' => 'Advanced Search', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
//echo $OUTPUT->heading();
if(!$export)
print_header('Advanced Search Form', 'About Book', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
require_once('breadcrumb.php');
global $USER,$DB;
$book_title = $_SESSION['book_title'];
$auth_name	= $_SESSION['auth_name'];
$isbn = $_SESSION['isbn'];
$edition = $_SESSION['edition'];
$keyword = $_SESSION['keyword'];


///hina
echo '<span style="font-size:14px;"><b>Description</b></br></br></span>';

echo ' <table width="100%" border="1" align="center" cellspacing="3" cellpadding="5" ';

$img="select pic from mdl_books where id='$books_id'";
$resultimg= $DB->get_records_sql($img);

foreach($resultimg as $row)
{
	$pic=$row->pic;

}

if($pic){
	$img_folder = 'images/'.$pic;
	echo "<tr><td>";
	echo "<img src='$img_folder' width=100 height=100 margin-left='60' float='left' alt= '$img_folder'><br/></td><td></td>";
}
else
{
	echo "<tr><td>";
	//echo "<b> No Image</b>";
	echo "<img src='images/p1.jpg' width=100 height=100 margin-left='60' float='left' alt= '$img_folder'><br />";
	echo "</td><td></td></tr>";
}


///
//echo $book_title;
$sql="SELECT * from mdl_books where id='$books_id'";
//and mdl_books.author_name LIKE '%$auth_name%' and mdl_book.ISBN LIKE '%$isbn%' and mdl_books.edition LIKE '%$edition%' and mdl_books.keyword LIKE '%$keyword%'";
$result = $DB->get_records_sql($sql);
//	echo '<span style="font-size:14px;margin-left:470px"><b>Description</b></br></br></span>';

//	echo ' <table width="30%" border="1" align="center" cellspacing="3" cellpadding="5" ';
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
	echo "<b>Price :</b>";
	echo "</td><td>";
	echo "$row->price</br>";
	echo "</td></tr>";

	echo "<tr><td>";
	echo "<b>ISBN :</b>";
	echo "</td><td>";
	echo "$row->isbn</br>";
	echo "</td></tr>";

	echo "<tr><td>";
	echo "<b>Edition :</b>";
	echo "</td><td>";
	echo "$row->edition</br>";
	echo "</td></tr>";

	echo "<tr><td>";
	echo "<b>Publisher :</b>";
	echo "</td><td>";
	echo "$row->publisher</br>";
	echo "</td></tr>";

	echo "<tr><td>";
	echo "<b>Year of publication :</b>";
	echo "</td><td>";
	echo "$row->publicationyear</br>";
	echo "</td></tr>";

	echo "<tr><td>";
	echo "<b>Book Category :</b>";
	echo "</td><td>";
	echo "$row->book_category</br>";
	echo "</td></tr>";

	echo "<tr><td>";
	echo "<b>Book Posted on  :</b>";
	echo "</td><td>";
	echo date('d M Y', $row->time)."</br>";
	echo "</td></tr>";


}

/*$img="select mdl_books.pic from mdl_books,mdl_book where mdl_books.book_id=mdl_book.book_id and mdl_book.books_id='$books_id'";

$resultimg= $DB->get_records_sql($img);

foreach($resultimg as $row)
{
$pic=$row->pic;

}

if($pic){
$img_folder = 'images/'.$pic;
echo "<tr><td>";
echo "<img src=$img_folder width=100 height=100 margin-left='60' float='left' alt= '$img_folder'><br />";
}
else
{
echo "</td><td>";
echo "<b> No Image</b>";
echo "</td></tr>";
}*/
echo "</tr></td>";
echo "</table>";
$img_folder = 'images/$pic';
//echo "<img src='$img_folder' alt= 'img_folder'><br />";


echo $OUTPUT->footer();?>
