<?php
require('../../config.php');
$proxyHost="10.3.3.3";
$proxyPort="8080";
$username = "Qurrat";
$password = "lmsteam";
//$date = date('Y_m_d(h.i.s)');
$date = date('Y_m_d');
//Append assignment instance ID
$submissionTitle = "Result_Assignment_ID_$date";
//echo $submissionTitle;
//$aid = $_GET['id'];
//echo "cvfvu".$aid;
 global $CFG,$DB;

$pathtoJarFile = "../../mod/assign/jplagTutorial/dist/exampleClient.jar";
//echo $pathtoJarFile;

if($_SERVER['REQUEST_METHOD'] == "POST") 
{

	$language = $_POST['language'];
	
	//change the path of the submission directory to be the one that you get from the assignment instance.
	//the post block will be removed then 
	//$submissionDir = $_POST['submissionDir']; 
	$aid = $_POST['id'];	
//echo $aid;
	$submissionDir = $_POST['subDir'];
//echo  $submissionDir;
	//echo "java -Dhttps.proxyHost=$proxyHost -Dhttps.proxyPort=$proxyPort -jar $pathtoJarFile -user $username -pass $password -l $language -r $submissionTitle -s $submissionDir";
	//echo "<br>".$submissionTitle."";
	//exec("java -Dhttps.proxyHost=$proxyHost -Dhttps.proxyPort=$proxyPort -jar $pathtoJarFile -user $username -pass $password -l $language -r $submissionTitle -s $submissionDir", $output);
	
	// Remove proxy from command as NUST removed proxy - updated by Qurrat-ul-ain Babar (16th May, 2014)
	exec("java -jar $pathtoJarFile -user $username -pass $password -l $language -r $submissionTitle -s $submissionDir", $output);
}

?>



<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/lib/formslib.php');
class JPlag_form extends moodleform {
function definition() {
global $DB;
$mform =& $this->_form;
$attributes = array("class" => "newclass");
$mform->addElement('html', '<br /><div align="left" style="color: black;">Options</div>');

$options = array('java12' => 'java12',
'java15' => 'java15',
'java15dm' => 'java15dm',
'scheme' => 'scheme',
'c/c++' => 'c/c++',
'text'=> 'text',
'char' => 'char',
'c#-1.2' => 'c#-1.2'
);
$submissionDir = $_GET['subDir'];
$aid = $_GET['id'];
if($aid!="")
$res=$DB->get_record_sql("Select * from {assign} where id =$aid");
 $filepath=$res->folder;
if($filepath!="")
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=$filepath target='_blank'>View Last Result<a>";

$mform->addElement('select', 'language', "Language", $options,$attributes);
$mform->addElement('hidden', 'subDir',$submissionDir );
$mform->addElement('hidden', 'id',$aid );

$mform->addElement('submit', 'Submit',"Sumbit");

}
}
$navlinks[] = array('name' => 'JPlag Form', 'link' => null, 'type' => 'activityinstance');
	$navigation = build_navigation($navlinks);		
	
	if(!$export)
		print_header('JPlag Form', 'JPlag Form', $navigation, '', '', true, '', user_login_string($SITE).$langmenu);
  		
$mform = new JPlag_form();
$mform->display();	

$val;			

if($_SERVER['REQUEST_METHOD'] == "POST") 
			{
				//print "<pre>";
			
				foreach($output as $value)
				{
				//echo 'there';
				echo $value."<br />";
				$val=$value;
				}
				
$value_path = explode('"', $val);
if($value_path[1]){
/*
$sourcePath = realpath($value_path[1]);
$archiv = new ZipArchive();
$archiv->open($value_path[1].'.zip', ZipArchive::CREATE);
$dirIter = new RecursiveDirectoryIterator($sourcePath);
$iter = new RecursiveIteratorIterator($dirIter);

foreach($iter as $element) {
     @var $element SplFileInfo 
    $dir = str_replace($sourcePath, '', $element->getPath()) . '/';
    if ($element->isDir()) {
        $archiv->addEmptyDir($dir);
    } elseif ($element->isFile()) {
        $file         = $element->getPath() .
                        '/' . $element->getFilename();
        $fileInArchiv = $dir . $element->getFilename();
        // add file to archive 
        $archiv->addFile($file, $fileInArchiv);
    }
}

// Save a comment
//$archiv->setArchiveComment('Backup ' . $absolutePath);
// save and close 
$archiv->close();

// to extract

$destinationPath = realpath('tmp/');
$archiv = new ZipArchive();
$archiv->open($value_path[1].'.zip');
$archiv->extractTo($destinationPath);
$navigation_variable = $value_path[1].'.zip';
//echo $navigation_variable;
echo "<a href=$navigation_variable>Download As Zip</a>";
*/
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=$value_path[1] target='_blank'>View Result<a>";
$record=new stdClass();
			$record->id=$aid;
			$record->folder=$CFG->wwwroot."/mod/assign/".$value_path[1];
			$DB->update_record('assign', $record);
//echo $value_path[1];
	}
}
echo $OUTPUT->footer();
?>
