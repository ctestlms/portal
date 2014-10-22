<?php
 
require_once('C:/wamp/www/moodle/config.php');
require_once($CFG->dirroot . '/lib/formslib.php');
global $DB;
$navlinks[] = array('name' => 'Search Books', 'link' => null, 'type' => 'activityinstance');

$navigation = build_navigation($navlinks);		
//echo $OUTPUT->heading();
$_SESSION['id'] = true;
class advanceSearchForm extends moodleform {
 
    function definition() {
        global $CFG,$OUTPUT;
		echo '<align=center><a href="">          Text Books</a> | <a href="ebooks.php">                    EBooks</a> ';
	
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


echo $OUTPUT->header();
$mform = new advanceSearchForm();
$mform->display();
session_register();
session_start(); 

$book_title=$_POST['book_title'];
$auth_name=$_POST['auth_name'];
$isbn=$_POST['isbn'];
$edition=$_POST['edition'];
$keyword=$_POST['keyword'];

$_SESSION['book_title'] = $book_title;
$_SESSION['auth_name'] = $auth_name;
$_SESSION['isbn'] = $isbn;
$_SESSION['edition'] = $edition;
$_SESSION['keyword'] = $keyword;

if (isset($_POST['submitbutton'])) {
//mysql_connect($dbhost, $dbuser, $dbpass) or die(mysql_error());
//Select Database
//mysql_select_db($dbname) or die(mysql_error());
if ((($_POST['isbn']) == NULL) || (($_POST['book_title']) == NULL) || (($_POST['auth_name']) == NULL) || (($_POST['edition']) == NULL)){
echo "Fill all the fields!!!";
}
else
{
$book_title=$_POST['book_title'];
$isbn=$_POST['isbn'];
$auth_name=$_POST['auth_name'];
$edition=$_POST['edition'];

	$sql="SELECT mdl_books.book_name,mdl_books.book_category,mdl_books.author_name,mdl_book.price,mdl_book.isbn,mdl_book.edition from mdl_books,mdl_book where mdl_books.book_id=mdl_book.book_id
    AND book_name like '%$book_title%' and mdl_books.author_name LIKE '%$auth_name%' and mdl_book.isbn LIKE '%$isbn%' and mdl_book.edition LIKE '%$edition%' and mdl_books.keyword LIKE '%$keyword%'";
	$result = $DB->get_records_sql($sql);
	//$result = mysql_query($sql) or die (mysql_error());
	if($result) {
	echo '<span style="font-size:24px">Related Books</span>';
	echo ' <table width="60%" border="1"  align="center" cellspacing="3" cellpadding="5" padding-right="20px">';
	echo "<tr>  <th>Title</th> <th>Author Name</th> <th>Price</th> <th>ISBN</th> <th>Edition</th> <th>Book Category</th>  </tr>";
echo 'here';
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
	echo "</td></tr>"; 
	
}
echo "</table>";
//echo '<span style="padding-left:110px"><a href="download.php">Download</a></br></br></span>';
echo "</br>";
}
else
{
echo "No Book Found!";}

}
}
echo $OUTPUT->footer();?>

