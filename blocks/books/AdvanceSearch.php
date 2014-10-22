<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<title>Search Books</title>

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<link rel="stylesheet" type="text/css" href="tab-view.css" />
<style type="text/css">
#footer {
	font-size: 14px;
	font-family: Verdana, Geneva, Arial, sans-serif;
	background-color: #c00;
	text-align: center;
	position: absolute;
	TOP: 70px;
	WIDTH: 580px;
}
</style>

<script language="JavaScript">
function confirmation( message) {
        var answer = confirm(message)
        if (answer){
                return(true);
                
        }
        else{
                return(false);
        }
}
/*function setVisibility(id, visibility) {
document.getElemenotById(id).style.display = visibility;
}*/
</script>
</head>
<body>

<?php
require_once('../../config.php');

require_once($CFG->dirroot . '/lib/formslib.php');
require_login();
$navlinks[] = array('name' => 'Advanced Search', 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
//	echo $OUTPUT->heading();
if(!$export)
print_header('Advanced Search Form', 'Advanced Search Form', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
global $USER;
require_once('breadcrumb.php');

?>
<?php
require_once("tabs.php");
?>
	<html>
<head>
<?php tabs_header(); ?>
</head>
<body>
	<div style="width: 600px; ">

	<?php tabs_start(); ?>
	<?php tab( "" ); ?>
		<center> </br>
		<table border="0" align="center">
			</br>
			<h2 align='center'>Search Books</h2>
			<form name="mForm" method="post" enctype="multipart/form-data"
				action="" onsubmit="return validateForm();">
				<tr>
					<td><b>Book Title</b></td>
					<td><input type="text" name="book_title"
						onfocus="if(this.value==this.defaultValue){this.value='';}"
						onblur="if(this.value==''){this.value=this.defaultValue;}">
					
					</td>
				</tr>
				<tr>
					<td><b>Author Name </b></td>
					<td><input type="text" name="auth_name">
					
					</td>
				</tr>
				<tr>
					<td><b>Edition </b></td>
					<td><input type="text" name="edition">
					
					</td>
				</tr>
				<tr>
					<td><b>Keyword </b></td>
					<td><input type="text" name="keyword">
					
					</td>
				</tr>
				<tr>
					<td><b>ISBN</b></td>
					<td><input type="text" name="isbn">
					
					</td>
				</tr>
				<tr>
					<td><b>Book Category </b></td>
					<td>&nbsp;<select name="book_category">
							<option value="Entertainment & Leisure">Entertainment & Leisure</option>
							<option value="Business & Finance">Business & Finance</option>
							<option value="Hobbies">Hobbies</option>
							<option value="Law & Order">Law & Order</option>
							<option value="Fiction">Fiction</option>
							<option value="History">History</option>
							<option value="Educational">Educational</option>
					</select></td>
				</tr>
				<tr>
					<td><input name="submitbutton" type="submit" value="Submit">
					
					</td>
				</tr>
			</form>
		</table>
		</center>

		<?php
		session_start();
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
						$errormsg .="Please enter valid author name<br />";
						// $errormsg .="Author name is not valid <br />";
					}
					if($_POST['edition'] != NULL && !is_numeric($_POST['edition']))
					{$errormsg .="Edition must be a number <br />";
					}
					if(($_POST['edition'] != NULL && $_POST['book_title'] == NULL))
					{$errormsg .="Please enter book name<br />";
					}

					else{
						$sql="SELECT id,userid,book_name,book_category,author_name,price,isbn,edition from mdl_books where
     book_name like '%$book_title%' and author_name LIKE '%$auth_name%' and isbn LIKE '%$isbn%' and edition LIKE '%$edition%' and keyword LIKE '%$keyword%' and book_category LIKE '%$book_category%' and  active=1" ;


						$result = $DB->get_records_sql($sql);

						$_SESSION['userid'] = $userid;
						//$result = mysql_query($sql) or die (mysql_error());
						if($result) {
							echo '<span style="font-size:14px;margin-left:200px"><b>Search Result</b></br></span>';

							echo ' <table width="100%" border="1"  align="center" cellspacing="3" cellpadding="5" >';
							//echo "<tr>  <th>Title</th> <th>Author Name</th> <th>Price</th> <th>ISBN</th> <th>Edition</th> <th>Book Category</th>  </tr>";
							$count=0;
							foreach($result as $row)
							{
								$book_format=$row->author_name;
								if($row->publicationyear!="")
								$book_format.="( ".$row->publicationyear." )";
								$book_format.=" , ".$row->book_name;
								if($row->edition!="")
								$book_format.=" , ".$row->edition;
								if($row->publisher!="")
								$book_format.=" , ".$row->publisher;
								if($row->isbn!="")
								$book_format.=" , ".$row->isbn;

								$userid=$row->userid;
								$booksid=$row->id;
								//echo $userid;
								//echo "$row->userid";
								echo "<tr><td>";

								echo "<b>$book_format</b>";
								echo "</td><td  align=right>";

								echo "<a href='description.php?bookid=".$booksid."'>Description</a>";

								echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
								echo "<a href='contactme.php?userid=".$userid."'>Contact</a>";
								$context = get_context_instance(CONTEXT_USER, $USER->id);
								if( has_capability('block/custom_reports:getfeedbackreport', $context) ||$userid== $USER->id){
									echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
									echo "<a href='postBook.php?bookid=$row->id&action=update'>Update</a>";
									echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
								//	echo "<a href='mybooks.php?bookid=$row->id&action=delete'>Delete</a>";
								//	echo  "&nbsp;&nbsp;&nbsp;&nbsp;";
								//	echo "<a href='mybooks.php?bookid=$row->id&action=soldout'>Sell</a>";
								 ?><a href='mybooks.php?bookid=<?php echo $row->id;?>&action=delete' onclick='return confirmation("Are you sure you want to delete this book?")' >Delete</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;

                        <a href='mybooks.php?bookid=<?php echo $row->id;?>&action=unavailable' onclick='return confirmation("Are you sure you want to make this book unavailable?")' >Make Unavailable</a><?php

								}
								echo "</td></tr>";
								$count++;
							}
							echo '</br>';
							echo $count.'<span style="font-size:14px">&nbsp; Result Found</br></br></span>';
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
	
	

		<?php tabs_end();  ?>
	</div>
	<?php echo $OUTPUT->footer();?>
</body>
	</html>
