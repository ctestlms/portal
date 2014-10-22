<?php

require_once('C:/wamp/www/moodle/config.php');
require_once($CFG->dirroot . '/lib/formslib.php');
$navlinks[] = array('name' => 'Search Books', 'link' => null, 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);		
//echo $OUTPUT->heading();

echo $OUTPUT->header();
//$mform = new advanceSearchForm();
//$mform->display();

session_start(); 
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "sidra";
$dbname = "moodle_new";
//$book_title=$_POST['book_title'];
$auth_name=$_POST['auth_name'];
$isbn=$_POST['isbn'];
$edition=$_POST['edition'];
$keyword=$_POST['keyword'];
$book_title = $_SESSION['book_title'];
$auth_name	= $_SESSION['auth_name'];
$isbn = $_SESSION['isbn'];
$edition = $_SESSION['edition'];
$keyword = $_SESSION['keyword'];

if (($book_title == NULL) || ($auth_name == NULL) || ($isbn == NULL) || ($edition == NULL)){
echo "Fill all the fields!!!";
}
else
{



 $sql="SELECT mdl_books.book_name,mdl_books.book_category,mdl_books.author_name,mdl_book.price,mdl_book.isbn,mdl_book.edition from mdl_books,mdl_book where mdl_books.book_id=mdl_book.book_id
    AND book_name like '%$book_title%' and mdl_books.author_name LIKE '%$auth_name%' and mdl_book.isbn LIKE '%$isbn%' and mdl_book.edition LIKE '%$edition%' and mdl_books.keyword LIKE '%$keyword%'";
	$result = $DB->get_records_sql($sql);
	if($result) {
	echo '<span style="font-size:24px">Related Books</span>';
	echo ' <table width="70%" border="1"  align="center" cellspacing="3" cellpadding="2">';

	echo "<tr>  <th>Title</th> <th>Author Name</th> <th>Price</th> <th>ISBN</th> <th>Edition</th> <th>Book Category</th> </tr>";

foreach($result as $row)
{
	echo "<tr><td>";
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "$row->book_name";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "$row->author_name";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "$row->price";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "$row->isbn";
	echo "</td><td>";
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "$row->edition";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
	echo "$row->book_category";
	echo "</td><td>"; 
	echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
	echo '<a href="download.php">Download</a>';
	echo "</td></tr>"; 
}

echo "</table>";
echo "</br>";
}
else
{
echo "No Book Found!";}


}

echo $OUTPUT->footer();

?>
