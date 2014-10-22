<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<title>Available Books</title>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<link rel="stylesheet" type="text/css" href="tab-view.css" />
<style type="text/css">
#footer {
	font-size: 14px;
	font-family: Verdana, Geneva, Arial, sans-serif;
	background-color: #c00;
	margin: 0 auto;
	text-align: center;
	WIDTH: 580px;
}

#confirm {
	font-size: 14px;
	font-family: Verdana, Geneva, Arial, sans-serif;
	background-color: #ccc;
	text-align: center;
}

.search {
	font-size: 18px;
	font-family: Verdana, Geneva, Arial, sans-serif;
	text-align: center;
}
</style>

<script language="JavaScript">
/*function setVisibility(id, visibility) {
document.getElemenotById(id).style.display = visibility;
}*/
function confirmation( message) {
        var answer = confirm(message)
        if (answer){
                return(true);
                
        }
        else{
                return(false);
        //      alert("Thanks for sticking around!")
        }
}
</script>
</head>
<body>

<?php
require_once('../../config.php');

require_once($CFG->dirroot . '/lib/formslib.php');
require_login();
$navlinks[] = array('name' => 'Available Books', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
//	echo $OUTPUT->heading();
if(!$export)
print_header('Available Books', 'Available Books', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
require_once('breadcrumb.php');
global $USER;
session_start();
global $books;

//echo "$books";

echo "</br></br>";

$errormsg="";
$sql="SELECT * from mdl_books  where  active=1 order by timemodified";
$result = $DB->get_records_sql($sql);

$_SESSION['userid'] = $userid;
echo '<p style="text-align: center;font-size:18px;">Available Books</p>';
if($result) {
	echo "</br></br>";
	echo ' <table width="100%" border="1" align="center" cellspacing="3" cellpadding="5" >';
	foreach($result as $row)
	{
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
		sort($row->book_name);
		$userid=$row->userid;
		$booksid=$row->id;
		echo "<tr><td>";
		echo "<b>$book_format</b>";
		echo "</td><td>";
		echo "</td><td align=right>";
		echo "<a href='description.php?bookid=".$booksid."'>Description</a>";
		echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='contactme.php?userid=".$userid."'>Contact</a>";
		$context = get_context_instance(CONTEXT_USER, $USER->id);
		if( has_capability('block/custom_reports:getfeedbackreport', $context) ||$userid== $USER->id){
			echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<a href='postBook.php?bookid=$row->id&action=update'>Update</a>";
			echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
			//echo "<a href='mybooks.php?bookid=$row->id&action=delete'>Delete</a>";
			?><a href='mybooks.php?bookid=<?php echo $row->id;?>&action=delete' onclick='return confirmation("Are you sure you want to delete this book?")' >Delete</a>
			&nbsp;&nbsp;&nbsp;&nbsp;
			
			<a href='mybooks.php?bookid=<?php echo $row->id;?>&action=unavailable' onclick='return confirmation("Are you sure you want to make this book unavailable?")' >Make Unavailable</a><?php
		}
		echo "</td></tr>";

	}
	echo "</table>";
	//echo '<span style="padding-left:110px"><a href="download.php">Download</a></br></br></span>';
	echo "</br>";



	//echo $OUTPUT->footer();

	/*$sql2="SELECT mdl_ebook.ebook_id,mdl_books.book_name,mdl_books.book_category,mdl_books.author_name,mdl_books.edition from mdl_books,mdl_ebook where mdl_books.book_id=mdl_ebook.book_id order by mdl_books.book_name";
	 //echo $books;
	 $result2 = $DB->get_records_sql($sql2);

	 $_SESSION['userid'] = $userid;
	 //$result = mysql_query($sql) or die (mysql_error());
	 echo '<p style="text-align: center;font-size:18px;">EBooks</p>';
	 if($result2) {

		echo ' <table width="80%" border="1"  align="center">';
		//echo "<tr>  <th>Title</th> <th>Author Name</th> <th>Price</th> <th>ISBN</th> <th>Edition</th> <th>Book Category</th>  </tr>";

		foreach($result2 as $row)
		{

		$booksid=$row->ebook_id;

		//echo $userid;
		//echo "$row->userid";

		echo "<tr><td>";

		echo "<b>$row->book_name</b>";
		echo "</td><td align=right>";

		echo "<a href='edescription.php?ebookid=".$booksid."'>Description</a>";
		echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='download.php?ebookid=".$booksid."'>Download</a>";
		echo "</td></tr>";

		}
		echo "</table>";
		//echo '<span style="padding-left:110px"><a href="download.php">Download</a></br></br></span>';
		echo "</br>";
		}*/

}
else{
	echo "<div id=\"confirm\">No books are available</div>";

}
if($errormsg){echo "<div id=\"footer\">$errormsg</div>"; }

echo $OUTPUT->footer();


?>
	</div>
</body>
</html>
