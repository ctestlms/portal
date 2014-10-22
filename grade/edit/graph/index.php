<?php
require '../../../config.php';
require 'marker_param.php';
require_once '../../lib.php';
$courseid_1 = required_param('cid1', PARAM_INT);
if (!$course = $DB->get_record('course', array('id' => $courseid_1))) {
    echo "No course found for id=" . $courseid_1 . ".";
    print_error('nocourseid');
}

require_login($course);
$coursename = explode(" ", $course->fullname, 2);
$coursecode = $coursename[0];
$semester = $DB->get_record_sql("select id,name,path from {course_categories} where id=$course->category");
$path = explode("/", $semester->path);
if (strstr($semester->name, "Summer") == false) {
    if($path[4]!="")
    $batch = $DB->get_record_sql("select id,name from {course_categories} where id=$path[4]");
}
$strgrades = get_string('grades');
$pagename = get_string('letters', 'grades');
$gpasummary = array('A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D+' => 0, 'D' => 0, 'F' => 0);

$navigation = grade_build_nav(__FILE__, $pagename, $courseid_1);
$navlinks[] = array('name' => "Graph", 'link' => null, 'type' => 'activityinstance');
$navigation = build_navigation($navlinks);
print_header('Grades', 'Grades', $navigation, '', '', true, '', user_login_string($SITE) . $langmenu);

$thiscoursecode = explode(" ", $course->fullname);

////changed
$sql = "SELECT firstname,lastname,u.id, username
FROM mdl_user u
JOIN mdl_role_assignments ra ON ra.userid = u.id
JOIN mdl_role r ON ra.roleid = r.id
JOIN mdl_context c ON ra.contextid = c.id
WHERE r.name = 'Teacher'
AND c.contextlevel =50
AND c.instanceid=$courseid_1 ";
$teacher = $DB->get_records_sql($sql);
if (strstr($semester->name, "Summer") == false) {
    echo "<b>Course Code (Degree Program & Batch):  </b>" . $coursecode . " (" . $batch->name . ")";
} else {
    echo "<b>Course Code :  </b>" . $coursecode;
}
// Display multiple teachers' name in graph - added by Qurrat-ul-ain Babar (18th April, 2014)
$i = 0;
foreach ($teacher as $t) {
	if($i == 0) {
		$teachers = $t->firstname." ".$t->lastname;
		$teacherid = $t->id;
	}
	else
		$teachers = $teachers.", ".$t->firstname." ".$t->lastname;
		
	$i++;
}
echo "<br/><b>Instructor:</b>  " . $teachers;
//end

// Compare the timestamps of each letter grade modified and print the latest - added by Qurrat-ul-ain Babar (23rd April, 2014)
$context = get_context_instance(CONTEXT_COURSE, $course->id);
$lastmodified = $DB->get_records('grade_letters', array('contextid' => $context->id), 'lowerboundary ASC');

 $timemod = "";
 foreach($lastmodified as $lm) {
	$temptimemod = $lm->timemodified;
	// $usermod = $lm->usermodified;
	if($timemod == "") {
		$timemod = $temptimemod;
		$usermod = $DB->get_record('user', array('id' => $lm->usermodified));
		$username= $usermod->firstname." ".$usermod->lastname;
		
	}
	else {
		if ($temptimemod > $timemod) {
			$timemod = $temptimemod;
			$usermod = $DB->get_record('user', array('id' => $lm->usermodified));
			$username= $usermod->firstname." ".$usermod->lastname;
		}
	}
}

if($timemod != "")
	echo "<br/><b>Last Edited By: </b>$username at $timemod <br/> "; 
//end
 

if (isset($teachers)) {
    $sql = "SELECT cc.*
	FROM mdl_user u
	JOIN mdl_role_assignments ra ON ra.userid = u.id
	JOIN mdl_role r ON ra.roleid = r.id
	JOIN mdl_context c ON ra.contextid = c.id
	JOIN mdl_course cc ON c.instanceid = cc.id
	WHERE r.name = 'Teacher'
	and u.id=$teacherid
	AND c.contextlevel =50 and cc.startdate>=$course->startdate and cc.category=$semester->id group by c.id;";
   $courses = $DB->get_records_sql($sql);
  
//   print_r($courses); //Shami

    foreach ($courses as $key => $course) {
        $coursecode = explode(" ", $course->fullname);
		 if ($thiscoursecode[0] != $coursecode[0]) {
            $thiscoursecode[0] . "--" . $coursecode[0] . "==" . $key;
            unset($courses[$key]);
            continue;
        }
        $courseids.=$course->id . ",";
        $contexts = get_context_instance(CONTEXT_COURSE, $course->id);
        $contextids.=$contexts->id . ",";
    }
    $courseids = rtrim($courseids, ",");
    $contextids = rtrim($contextids, ",");
	//print_r("Course ID: " + $courseids); //Shami
} else {
    $courseids = $courseid_1;
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    $contextids = $context->id;
}
//end
//require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

////

if (isset($_POST['update'])) {

    foreach ($courses as $course) {

        $contexts = get_context_instance(CONTEXT_COURSE, $course->id);
        $contextid = $contexts->id;

        // $contextid = required_param('contextid', PARAM_INT);
        if (!$context = get_context_instance_by_id($contextid)) {
            error('Incorrect context id');
        }

        /* if (!$course = $DB->get_record('course', array('id' => $courseid_1))) {
          echo "No course found for id=" . $courseid_1 . ".";
          print_error('nocourseid');
          } */

        $letters = grade_get_letters($context);
        $cat = $DB->get_record_sql("select path from {course_categories} where id=(SELECT category from {course} where id=$course->id)");
        $path = explode("/", $cat->path);
        $paths = "/" . $path[1] . "/" . $path[2];
        $program = $DB->get_record_sql("select name from {course_categories} where path='$paths'");
        if ($program->name == "Postgraduate Programs") {
            $letters = array_diff($letters, array('D')); //Added By Hina Yousuf
			$letters = array_diff($letters, array('D+')); //Added By Hina Yousuf
        }  

        if ($records = $DB->get_records('grade_letters', array('contextid' => $context->id), 'lowerboundary ASC')) {
            $old_ids = array_keys($records);
        }
        $action_label = "";
        $action_update_count = 0;
        $action_insert_count = 0;
        foreach ($letters as $boundary => $letter) {
            $letter = trim($letter);
            if ($letter == '') {
                continue;
            }



            /* if ($letter == "D") {
              continue;
              } */
            $ori_letter = $letter;
            $letter = str_replace("+", "1", $letter);
            $newboundary = $_POST['textbox_' . $letter]; //required_param('textbox_'.$letter,PARAM_INT);
            $record = new stdClass();
            $record->letter = $ori_letter;
            $record->lowerboundary = $newboundary;
            $record->contextid = $context->id;
			
			
            if ($old_id = array_pop($old_ids)) {
                $record->id = $old_id;
				// To add timestamp when grade boundaries are modified - updated by Qurrat-ul-ain Babar (22nd April, 2014)
				$record->usermodified = $USER->id;
                $action_update_count++;
                $DB->update_record('grade_letters', $record);
            } else {
                $action_insert_count++;
                $DB->insert_record('grade_letters', $record);
            }
        }
    }
   // $letters = grade_get_letters($context);
    $letters = grade_get_letters($context);
    redirect($CFG->wwwroot . '/grade/edit/graph/index.php?cid1=' . $courseid_1);
}


////



$context = get_context_instance(CONTEXT_COURSE, $courseid_1);
$letters = grade_get_letters($context);

$cat = $DB->get_record_sql("select path from {course_categories} where id=(SELECT category from {course} where id=$course->id)");
        $path = explode("/", $cat->path);
        $paths = "/" . $path[1] . "/" . $path[2];
        $program = $DB->get_record_sql("select name from {course_categories} where path='$paths'");
        if ($program->name == "Postgraduate Programs") {
			$letters = array_diff($letters, array('D')); //Added By Hina Yousuf
			$letters = array_diff($letters, array('D+')); //Added By Hina Yousuf
        }
//	print_r($letters);
//echo $context->id;

$where = " WHERE c1.courseid=$courseid_1";
$select = " SELECT min(c1.id) ";
$from = " FROM mdl_grade_items c1 ";
$context = get_context_instance(CONTEXT_COURSE, $courseid_1, MUST_EXIST);
//echo $context->id;
$sql = " SELECT count(ra.userid) FROM mdl_role_assignments ra WHERE ra.roleid =5 and contextid =$context->id ";
$usrs = $DB->count_records_sql($sql);

$cat = $DB->get_record_sql("select path from {course_categories} where id=(SELECT category from {course} where id=$courseid_1)");
$path = explode("/", $cat->path);
$paths = "/" . $path[1] . "/" . $path[2];

//print_r($paths); //Shami

$program = $DB->get_record_sql("select name from {course_categories} where path='$paths'");
//print_r("Program"); //Shami
//print_r($program); //Shami

// added the gg.excluded=0 in query -- Updated by Qurrat-ul-ain (6th Jan, 2014)
$grades_result = $DB->get_records_sql("SELECT u.id,  u.idnumber as idnumber,u.firstname, u.lastname,ROUND(gg.finalgrade,2) AS finalgrade, gg.excluded FROM {user} u,{role_assignments} ra , {role} r,{grade_grades} gg,{grade_items} gi
                 WHERE u.id=gg.userid AND
                 ra.userid = u.id
                 and ra.roleid = r.id and
                 gg.itemid=gi.id AND gg.excluded=0 AND    
                 itemtype='course' and gi.courseid IN($courseids)
				 AND gg.finalgrade IS NOT NULL
                 and ra.roleid =5 and contextid IN($contextids) order by idnumber");
				 // gg.itemid=gi.id AND 
				 

// print_r($courseids); //Shami
// print_r($contextids); //Shami
//print_r($grades_result); //Shami
$usrs = 0;
$gradez = $DB->get_record_sql("Select * from {grade_items} where itemtype='course' and  courseid=$courseid_1 ");
foreach ($grades_result as $row) {
    $subjgrade = grade_format_gradevalue_letter($row->finalgrade, $gradez);
    switch ($subjgrade) {
        case ($subjgrade == "A" ):
            $gpasummary['A']++;
            break;
        case ($subjgrade == "B+" ):
            $gpasummary['B+']++;
            break;
        case ($subjgrade == "B" ):
            $gpasummary['B']++;
            break;
        case ($subjgrade == "C+" ):

            $gpasummary['C+']++;
            break;
        case ($subjgrade == "C" ):

            $gpasummary['C']++;
            break;
        case ($subjgrade == "D" ):
            $gpasummary['D']++;
            break;
        case ($subjgrade == "D+" ):
            $gpasummary['D+']++;
            break;
        case ($subjgrade == "F" ):
            $gpasummary['F']++;
            break;
    }
    $sum+= ROUND($row->finalgrade);
    if ($row->finalgrade != "") {
        $usrs++;
    }
}
$average = $sum / ($usrs);
$average = number_format(($average), 2);
//if($average=="0")
//$average="0.01";
//echo "avg".$average;
$strgrades = get_string('grades');
$pagename = get_string('letters', 'grades');

if ($admin) {
    
} else {
    /*  $navigation = grade_build_nav(__FILE__, $pagename, $courseid_1);
      $navlinks[] = array('name' => "Graph", 'link' => null, 'type' => 'activityinstance');
      $navigation = build_navigation($navlinks);
      print_header('Grades', 'Grades', $navigation, '', '', true, '', user_login_string($SITE) . $langmenu); */
}
$sql = "select * from mdl_grade_items where courseid=$courseid_1 AND itemtype = 'course' ";
$grade_ids = $DB->get_record_sql($sql);

if ($grade_ids->locked == 1) {
    echo "<div style='font-size:15px'><b>Sorry, The Gradebook is locked,the grade boundaries cannot be changed now.</b></div>";

    // return;
}
?>
<!-- <link href="layout.css" rel="stylesheet" type="text/css"></link> -->
<link rel="stylesheet" type="text/css" href="print.css" media="print" />
<script
language="javascript" type="text/javascript" src="js/excanvas.min.js"></script>
<script
language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script
language="javascript" type="text/javascript" src="js/jquery.flot.js"></script>
<script language="javascript"
type="text/javascript" src="js/jquery.flot.selection.js"></script>
<script language="javascript" type="text/javascript" src="js/canvas2image.js"></script>
<script language="javascript" type="text/javascript">
function test() {
    
    var oCanvas = document.getElementById("placeholder").childNodes[0];

Canvas2Image.saveAsPNG(oCanvas);  // will prompt the user to save the image as PNG.

//Canvas2Image.saveAsJPEG(oCanvas); // will prompt the user to save the image as JPEG. 
                                  // Only supported by Firefox.

//Canvas2Image.saveAsBMP(oCanvas);  // will prompt the user to save the image as BMP.


// returns an <img> element containing the converted PNG image
var oImgPNG = Canvas2Image.saveAsPNG(oCanvas, true); 

    //alert("hina");
//var oCanvas = $("#placeholder");//document.getElementById("placeholder");
//img=document.createElement("img");
////Save
//img.src=oCanvas.toDataUrl();
////Restore
//oCanvas.drawImage(img, 0, 0);
//var oCanvas = $("#placeholder");//document.getElementById("placeholder");
//document.write(document.getElementById('placeholder').toDataUrl());
//  // Canvas2Image.saveAsPNG(document.getElementById('placeholder'));
//Canvas2Image.saveAsPNG(oCanvas, true);
}</script>
<?php
require_once $CFG->libdir . '/gradelib.php';
rebuild_course_cache($course->id);
$letters = grade_get_letters($context);

if ($program->name == "Postgraduate Programs") {
	$letters = array_diff($letters, array('D')); //Added By Hina Yousuf
	$letters = array_diff($letters, array('D+')); //Added By Hina Yousuf
}
//print_r($letters);
$letters[$average] = 'Average';
$data = array();
?>

<script id="source" language="javascript" type="text/javascript">
    var plotHeight = 463;
    var plotTop = 3;
    var pointersLeft = 560;
	

    var yaxisFrom = 3;
    var yaxisTo = 100;
    var grade_markers = new Array();

<?php
$max = 100;
$marker_count = 0;
$marker_obj = "";
foreach ($letters as $boundary => $letter) {

    $letter = str_replace("+", "1", $letter);
    if (in_array("D", $letters, true) == false) {
        if ($upperLetter[$letter] == "D") {
            $upperLetter[$letter] = "C";
        }
        if ($bottomLetter[$letter] == "D") {
            $bottomLetter[$letter] = "F";
        }
		
		if ($upperLetter[$letter] == "D1") {
            $upperLetter[$letter] = "C";
        }
        if ($bottomLetter[$letter] == "D1") {
            $bottomLetter[$letter] = "F";
        }
		
    } else {
        if ($letter == "D1") {
            $upperLetter[$letter] = "C";
            $bottomLetter[$letter] = "D";
        }

        if ($letter == "D") {
            $upperLetter[$letter] = "D1";
            $bottomLetter[$letter] = "F";
        }
        if ($letter == "C") {
            $bottomLetter[$letter] = "D1";
        }
    }

    if ($letter == "Average") {
        $max = $boundary;
        echo "var marker" . $letter . " = {  yaxis: {
                                       from: " . format_float($max, 0) . ",
                                       to: " . format_float($boundary, 0) . "
                                     },
                                     color:\"rgba(" . $marker_color[$marker_count][0] . "," . $marker_color[$marker_count][1] . "," . $marker_color[$marker_count][2] . ",0.5)\",
				     
                                   };\n";
        echo "var marker" . $letter . "_e = {  yaxis: {
                                       from: " . format_float($max, 0) . ",
                                       to: " . format_float($max, 0) . "
                                     },
                                  color:\"rgba(255,0,0,1)\",

                                   };\n";
        if ($marker_obj == "") {
            $marker_obj .= "marker$letter";
            $marker_obj .= ",marker" . $letter . "_e";
        } else {
            $marker_obj .= ",marker$letter";
            $marker_obj .= ",marker" . $letter . "_e";
        }
    } else {

        if ($upperLetter[$letter] != null) {
            echo "var marker" . $upperLetter[$letter] . " = {  yaxis: {
                                       from: " . format_float($max, 0) . ",
                                       to: " . format_float($boundary, 0) . "
                                     },
                                     color:\"rgba(" . $marker_color[$marker_count][0] . "," . $marker_color[$marker_count][1] . "," . $marker_color[$marker_count][2] . ",0.5)\",
				     
                                   };\n";
            echo "var marker" . $upperLetter[$letter] . "_e = {  yaxis: {
                                       from: " . format_float($max, 0) . ",
                                       to: " . format_float($max, 0) . "
                                     },
                                  color:\"rgba(0,0,0,0.8)\",

                                   };\n";
        }
        if ($bottomLetter[$letter] == null) {
            echo "var marker" . $letter . " = {  yaxis: {
                                       from: " . format_float($boundary, 0) . ",
                                       to: " . format_float(0, 0) . "
                                     },
                                     color:\"rgba(" . $marker_color[$marker_count][0] . "," . $marker_color[$marker_count][1] . "," . $marker_color[$marker_count][2] . ",0.5)\",
				     
                                   };\n";

            echo "var marker" . $letter . "_e = {  yaxis: {
                                       from: " . format_float($boundary, 0) . ",
                                       to: " . format_float($boundary, 0) . "
                                     },
                                    color:\"rgba(0,0,0 ,0)\",
                                   };\n";
        }

        if ($upperLetter[$letter] != null) {
            if ($marker_obj == "") {
                $marker_obj .= "marker$upperLetter[$letter]";
                $marker_obj .= ",marker" . $upperLetter[$letter] . "_e";
            } else {
                $marker_obj .= ",marker$upperLetter[$letter]";
                $marker_obj .= ",marker" . $upperLetter[$letter] . "_e";
            }
        }
        if ($bottomLetter[$letter] == null) {
            if ($marker_obj == "") {
                $marker_obj .= "marker$letter";
                $marker_obj .= ",marker" . $letter . "_e";
            } else {
                $marker_obj .= ",marker$letter";
                $marker_obj .= ",marker" . $letter . "_e";
            }
        }
    }
    $max = $boundary;

    $marker_count++;
}
?>

    function markers(axes) {
    
        return [<?php echo $marker_obj; ?>];

    }

    function insidePlot(posY) {
        return (posY>plotTop && posY<(plotTop+plotHeight));
    }
    function changeGrades(letter){
        var newVal = document.getElementById('textbox_m_'+letter).value;

        var totalTicks = yaxisTo - yaxisFrom;
        var tickRatio = plotHeight/totalTicks;
        calVal = (yaxisTo - newVal) * tickRatio;
        var obj = { title: letter };
        updateMarkers(obj,calVal);

    }
    function updateMarkers(obj, posY) {

        var totalTicks = yaxisTo - yaxisFrom;

        var tickRatio = plotHeight/totalTicks;
        var yaxisnew = (posY-plotTop)/tickRatio;
        move_diff = yaxisTo-yaxisnew;
<?php
$marker_count = 0;
foreach ($letters as $boundary => $letter) {
    $letter = str_replace("+", "1", $letter);
    if (in_array("D", $letters, true) == false) {
        if ($upperLetter[$letter] == "D") {
            $upperLetter[$letter] = "C";
        }
        if ($bottomLetter[$letter] == "D") {
            $bottomLetter[$letter] = "F";
        }
		
		if ($upperLetter[$letter] == "D1") {
            $upperLetter[$letter] = "C";
        }
        if ($bottomLetter[$letter] == "D1") {
            $bottomLetter[$letter] = "F";
        }
		
		
    } else {
        if ($letter == "D1") {
            $upperLetter[$letter] = "C";
            $bottomLetter[$letter] = "D";
        }

        if ($letter == "D") {
            $upperLetter[$letter] = "D1";
            $bottomLetter[$letter] = "F";
        }
        if ($letter == "C") {
            $bottomLetter[$letter] = "D1";
        }
    }
    //$upperLetter[$letter]=$temp;//hina
    echo "
    if(obj.title.match(/" . $letter . "$/)) {";
    ?>
                                     
    <?php
    echo "var yaxisUpper = 100;\n
              var yaxisLower = 0;\n
              var yaxis_val = yaxisTo-yaxisnew;\n";

    if ($upperLetter[$letter] != null) {
        echo "yaxisUpper = parseInt(marker" . $upperLetter[$letter] . ".yaxis.from)\n";
    }
    if ($bottomLetter[$letter] != null) {

        echo "yaxisLower = parseInt(marker" . $bottomLetter[$letter] . ".yaxis.from)\n";
    }
    echo"
        if(yaxis_val>(yaxisUpper-2.9)){\n
          yaxis_val=yaxisUpper-1;\n
        }\n
        if(yaxis_val<(yaxisLower+2.9)){\n
          yaxis_val=yaxisLower+1;\n
        }\n
        marker" . $letter . ".yaxis.from = yaxis_val;\n";

    echo "var newTop = posY;\n";

    echo "DHTMLAPI.moveTo('pointerGrade" . $letter . "',pointersLeft,newTop);\n";

    if ($letter == 'A') {
        echo "
          marker" . $letter . ".yaxis.to =marker" . $bottomLetter[$letter] . ".yaxis.from;\n
          
        ";
		
    } else {
        echo "
                 marker" . $letter . ".yaxis.to =marker" . $bottomLetter[$letter] . ".yaxis.from;\n
          
        
         marker" . $upperLetter[$letter] . ".yaxis.to =marker" . $letter . ".yaxis.from;\n
          ";
    }
    echo "
            marker" . $letter . "_e.yaxis.to = marker" . $letter . ".yaxis.from;\n
            marker" . $letter . "_e.yaxis.from = marker" . $letter . ".yaxis.from;\n
          
          
            ";
			

    //echo "alert('".$upperLetter[$letter]."');\n";

    echo "document.getElementById('Grade_" . $letter . "_value').innerHTML='('+(yaxis_val.toFixed(0))+')';\n";


    echo "document.getElementById('textbox_m_" . $bottomLetter[$letter] . "').value=''+((yaxis_val-1).toFixed(0))+'';\n";
    echo "document.getElementById('textbox_" . $letter . "').value=''+((yaxis_val).toFixed(0))+'';\n";

    echo "}\n";

    /* echo "

      alert('".$letter."== to ='+marker".$letter.".yaxis.to+'--from='+marker".$letter.".yaxis.from);\n
      alert('".$upperLetter[$letter]."upper== to ='+marker".$upperLetter[$letter].".yaxis.to+'--from='+marker".$upperLetter[$letter].".yaxis.from);\n
      alert('".$bottomLetter[$letter]."lower== to ='+marker".$bottomLetter[$letter].".yaxis.to+'--from='+marker".$bottomLetter[$letter].".yaxis.from);\n

      "; */
    $max = $boundary;

    $marker_count++;
}
?>

        plot.draw();
    }
    
    // Function added by Junaid Malik
    // Ends at line 508
    function getMarkerLimit(obj) {
        var totalTicks = yaxisTo - yaxisFrom;
        
        var multip = plotHeight/100;
        
        var markId = obj.id;
        var splitStr = markId.split('pointerGrade');
        var markLetter = splitStr[1];
		// javascript
		var elements = document.getElementsByClassName("draggable");
		var ids = '';
		for(var i=0; i<elements.length; i++) {
			ids += elements[i].id;
		}
		var D = ids.indexOf("pointerGradeD");
		/* if(D == -1)
			alert('D doesnot exist');
		else
			alert('D....... exist'); */
		
		
        switch (markLetter) {
            case 'D':
                return [plotHeight,plotHeight-((markerD1.yaxis.from)*multip)];
                
                
            case 'D1':
                return [plotHeight-((markerD.yaxis.from)*multip),plotHeight-((markerC.yaxis.from)*multip)];
                
                
            case 'C':
				if(D==-1)
                return [plotHeight-((markerF.yaxis.from)*multip),plotHeight-((markerC1.yaxis.from)*multip)];
				else
				return [plotHeight-((markerD1.yaxis.from)*multip),plotHeight-((markerC1.yaxis.from)*multip)];
                
                
            case 'C1':
                return [plotHeight-((markerC.yaxis.from)*multip),plotHeight-((markerB.yaxis.from)*multip)];
                
                
            case 'B':
                return [plotHeight-((markerC1.yaxis.from)*multip),plotHeight-((markerB1.yaxis.from)*multip)];
                
                
            case 'B1':
                return [plotHeight-((markerB.yaxis.from)*multip),plotHeight-((markerA.yaxis.from)*multip)];
                
                
            default:
                return [plotHeight-((markerB1.yaxis.from)*multip),0];
        }       
    }
    /* Function to update the graph when an area is chosen */
    function updateMarkerImgs(yaxisTo_new,yaxisFrom_new) {


        yaxisTo = yaxisTo_new; yaxisFrom = yaxisFrom_new;
        var totalTicks = yaxisTo - yaxisFrom;
        var tickRatio = plotHeight/totalTicks;

<?php
$marker_count = 0;
foreach ($letters as $boundary => $letter) {
    if ($letter == "Average")
        $max = $boundary;
    $letter = str_replace("+", "1", $letter);
    echo "
    if(yaxisTo>=marker" . $letter . ".yaxis.from && yaxisFrom<=marker" . $letter . ".yaxis.from) {
        var ticksToTop = yaxisTo - marker" . $letter . ".yaxis.from;
        var newY = ticksToTop*tickRatio;
        DHTMLAPI.moveTo('pointerGrade" . $letter . "',pointersLeft,newY+plotTop);
        DHTMLAPI.show('pointerGrade" . $letter . "');
    } else {
        DHTMLAPI.hide('pointerGrade" . $letter . "');
    }
    ";
    $max = $boundary;
    $marker_count++;
}
?>


    }


    function loadXMLDoc(uri_Local,query,div_name,method)
    {
        var xmlhttp;
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                if(xmlhttp.responseText.match(/error/i)){
                    document.getElementById(div_name).innerHTML="ERROR!!! can't update your grades.";
                }
                else{
                    document.getElementById(div_name).innerHTML=xmlhttp.responseText;
                }
            }
            else{
                if(document.getElementById('update-status')!=null){
                    document.getElementById('update-status').innerHTML="<img src='<?php echo $CFG->pixpath . '/i/ajaxloader.gif' ?>' />";
                }
            }
        }
        if(method.toLowerCase()=='get'){
            uri_Local = uri_Local+"?"+query;
        }
        xmlhttp.open(method,uri_Local,true);
        xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        xmlhttp.send(query);
    }
    var Post = new Object();
    Post.Send = function(form)
    {
	
        var query = Post.buildQuery(form);
        loadXMLDoc(form.action,query,"update-status",form.method);
    }

    Post.OnResponse = function(xml)
    {
        var results = document.createElement('div');
        document.getElementsByTagName('body')[0].appendChild(results)
        results.innerHTML = xml.firstChild.nodeValue;
    }
    Post.buildQuery = function(form)
    {
        var query = "";
        for(var i=0; i<form.elements.length; i++)
        {
            var key = form.elements[i].name;
            var value = Post.getElementValue(form.elements[i]);
            if(key && value)
            {
                query += key +"="+ value +"&";
            }
        }
        return query;
    }
    Post.getElementValue = function(formElement)
    {
        if(formElement.length != null) var type = formElement[0].type;
        if((typeof(type) == 'undefined') || (type == 0)) var type = formElement.type;

        switch(type)
        {
            case 'undefined': return;

            case 'radio':
                for(var x=0; x < formElement.length; x++)
                    if(formElement[x].checked == true)
                        return formElement[x].value;

                    case 'select-multiple':
                var myArray = new Array();
                for(var x=0; x < formElement.length; x++)
                    if(formElement[x].selected == true)
                        myArray[myArray.length] = formElement[x].value;
                return myArray;

            case 'checkbox': return formElement.checked;

            default: return formElement.value;
        }
    }

    function toggle_grade_editor(){
        if(document.getElementById('letter_grades').style.display==""){
            document.getElementById('letter_grades').style.display="none";
        }
        else{
            document.getElementById('letter_grades').style.display="";
        }
    }

</script>

<script src="eventsManager.js"></script>
<script src="DHTML3API.js"></script>
<script src="dragManager.js"></script>



<!--div id="pointerGradeA" style="cursor:n-resize;position:absolute;left:643px;top:254px;" class="draggable" title="A"><img src="btn.png" width="16" height="16" border="0" alt="A"/>A</div>

    <div id="pointerGradeB" style="cursor:n-resize;position:absolute;left:643px;top:294px;" class="draggable" title="B"><img src="btn.png" width="16" height="16" border="0" alt="B"/>B</div>
    <div id="pointerGradeC" style="cursor:n-resize;position:absolute;left:643px;top:334px;" class="draggable" title="C"><img src="btn.png" width="16" height="16" border="0" alt="C"/>C</div>
    <div id="pointerGradeD" style="cursor:n-resize;position:absolute;left:643px;top:374px;" class="draggable" title="D"><img src="btn.png" width="16" height="16" border="0" alt="D"/>D</div-->
<!-- <div style='width: 100%'>
	<div style='float: left; width: 70%'>
	left div
	<div style="width: 90%; ">hhhhhhhhhhhhhh</div>
	</div>
	<div style='float: right; width: 30%'>
	right div
	</div>
</div> -->


<div  style=' width: 85%; position: relative;'>
    <div  style='float: right; position: relative; width: 20%'>
        <form id="editing" method="POST" action="index.php"
              onsubmit="Post.Send(this); ">
            <input type='hidden' id='contextid' name='contextid'
                   value='<?php echo $context->id ?>'> <input type='hidden' id='cid1'
                   name='cid1' value='<?php echo $courseid_1 ?>'>
            <?php
			echo "<table align='center' style='width:20px' class='edit_grade_table'>";
            echo "<th>Grade</th>";    
				echo "<th>From</th>";
                echo "<th>To</th>";
                echo "<th>No of Students</th>";
                $max = 100;
				$total = 0;
				$i = 0;
                foreach ($letters as $boundary => $letter) {
                    $letter = trim($letter);
					$i=$i+2;
                    if ($letter != "Average") {
                        if ($letter == '') {
                            continue;
                        }
                        $ori_letter = $letter;
                        $letter = str_replace("+", "1", $letter);
						
                        echo '<tr>';
						
                        echo '<td align="center">';
                        echo '<label for="label_' . $letter . '">' . $ori_letter . '</label>';
                        echo '</td>';
						
                        if (has_capability('moodle/grade:manage', $context) && $grade_ids->locked != 1) {
                            $frm_textbox = '<input type="textbox" id="textbox_' . $letter . '" name="textbox_' . $letter . '" value="' . format_float($boundary, 0) . '"  size=2 maxlength=2 tabindex="'.$i.'" readonly />';;
                        } elseif (has_capability('gradereport/grader:viewlettergrades', $context) && $grade_ids->locked == 1) {
                           $frm_textbox = format_float($boundary, 0);
                        }
						
						echo '<td align="center">';
                        echo $frm_textbox;
                        echo '</td>';
						
						if (has_capability('moodle/grade:manage', $context) && $grade_ids->locked != 1) {
							//echo '<input type="textbox" id="textbox_m_' . $letter . '" name="textbox_m_' . $letter . '" onblur="changeGrades(\'' . $letter . '\')" value="' . format_float($max, 0) . '" size=5 maxlength=5 readonly="readonly"';
							// onblur function removed to resolve the ambiguous grade boundary change in graph - Updated by Qurrat-ul-ain (20th Jan, 2014)
                            $to_textbox = '<input type="textbox" id="textbox_m_' . $letter . '" name="textbox_m_' . $letter . '" value="' . format_float($max, 0) . '" size=3 maxlength=3 tabindex="'.--$i.'" ';

							if ($upperLetter[$letter] == null) {
                                $to_textbox .= '  ';
                            }
                            $to_textbox .= 'readonly />';
                        } elseif (has_capability('gradereport/grader:viewlettergrades', $context) && $grade_ids->locked == 1) {
							$to_textbox = format_float($max, 0);
                        }
						
						echo '<td align="center">';
                        echo $to_textbox;
                        echo '</td>';
						
						$total = $total + $gpasummary["$ori_letter"];
                        //echo $gpasummary["$ori_letter"];
						
						echo '<td align="center">';
                        echo $gpasummary["$ori_letter"];
                        echo '</td>';
						
                        /*
                        echo '<td align="center">';
						
                        echo '</td>'; */

                        echo '</tr>';

                        $max = $boundary - 1;
                    }
					$i++;
                }
				echo '<tr>';
					echo '<td align="right" colspan="3">';
					echo '<label><span style="font-weight:bold">Total </span></label>';
					echo '</td>';
					echo '<td align="center">';
					echo '<span style="font-weight:bold">'.$total.'</span>';
					echo '</td>';
				echo '</tr>';
            echo '</table>';
                echo '<div style="text-align:center;padding:1px;margin-top:0px;">';
               // echo '<div id="update-status" style="overflow:auto;width:300px;"></div>';
                if ($canviewgraph = has_capability('moodle/grade:manage', $context) && $grade_ids->locked != 1) {

                    echo '<input  name="update" id="update" type="submit" value="Update Grades" />';
                }
                echo '</div>';
                echo '</form>';
                ?>

                </div>
                <div style='float: left; width: 65%; '>
					<div id="placeholder" style="width: 82%; height: 480px; position: relative;"></div>
					
					
                    <?php
                    $max = 100;
                    $marker_count = 0;
                    foreach ($letters as $boundary => $letter) {

                        $ori_letter = $letter;
                        $letter = str_replace("+", "1", $letter);
                        if ($letter == "D1") {
                            $upperLetter[$letter] = "C";
                            $bottomLetter[$letter] = "D";
                        }
                        //echo $upperLetter[$letter] . "" . $letter . "" . $bottomLetter[$letter];
                        //$upperLetter[$letter]=$temp;
                        if ($letter == "Average") {
                            $ori_letter = "<b>Average</b>";
                            $max = $boundary;
                        }
                        $top = (463 - (ceil($boundary) * 4.6));
                        if ($letter == "Average") {
                            $str_div = "<div   style='position:absolute;left:565px;top:" . $top . "px;' class='average'";
                        } else {
                            $str_div = "<div  id='pointerGrade" . $letter . "' style='cursor:n-resize;overview:hidden;position:absolute;left:560px;top:" . $top . "px;' ";
                        }

                        if ($bottomLetter[$letter] != null) {
                            $str_div .= " class='draggable' ";
                        }
						else {
							$str_div .= " class='noprint' ";
						}
                        if ($letter == "Average") {
                            $str_div .= " title='" . $letter . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; " . $ori_letter;
                        } else {
                            $str_div .= " title='" . $letter . "'><img src='btn.png'  width='16' height='16' border='0' alt='" . $ori_letter . "'/>" . $ori_letter;
                        }

                        echo $str_div;
                        if ($letter == "Average") {
                            echo "<div id='Grade_" . $letter . "_value' style='float:right;'>(" . format_float($boundary, 2) . ")</div></div>";
                        } else {
                            echo "<div id='Grade_" . $letter . "_value' style='float:right; position:relative;'>(" . format_float($boundary, 0) . ")</div></div>";
                        }
                        $max = $boundary - 1;


                        $marker_count++;
                    }
               
                    function compare($x, $y) {
                        if ($x->finalgrade == $y->finalgrade)
                            return 0;
                        return ( $x->finalgrade < $y->finalgrade ) ? 1 : -1;
                    }

                    uasort($grades_result, 'compare');

                    $user_count = 0;

                    foreach ($grades_result as $row) {

                        $row->finalgrade = ROUND(trim($row->finalgrade));
                        $row->firstname = trim($row->firstname);
                        $row->lastname = trim($row->lastname);
                        $row->idnumber = trim($row->idnumber);
                        echo "<input id='d" . $user_count . "' type='hidden' value='" . $row->finalgrade . "'>";

                        echo "<input id='name" . $user_count . "' type='hidden' value='" . $row->firstname . "'>";

                        echo "<input id='lname" . $user_count . "' type='hidden' value='" . $row->lastname . "'>";
                        echo "<input id='idnum" . $user_count . "' type='hidden' value='" . $row->idnumber . "'>";
                        $user_count++;
                    }

                    echo "<input id='no_of_scores' type=hidden value=" . $user_count . ">";
                    ?>

                    <script id="source" language="javascript" type="text/javascript">
                        var stu_records = document.getElementById("no_of_scores").value;

                        var options = {
                            yaxis: { min: 0, max: 100 },
                            xaxis: { ticks: 0, min: -1, max: stu_records}, // change the location of y-axis line - Added by Qurrat-ul-ain Babar (8th Jan, 2014)
                            //xaxis: { ticks: 0, min: 0, max: stu_records},
                            lines: { show: false},
                            points: { show: true, fill: 1},
                            selection: { mode: "xy" },
                            grid: {
                                hoverable: true, clickable: true,
                                markings: markers
                            },
							colors: ["#000000", "#000000"]  // change the color of the markers from yellow to black - Added by Qurrat-ul-ain Babar (8th Jan, 2014)
							
                        };

                        var plot = null;
                        var sdata = new Array();
                        var score_count = stu_records;

                        for(i=1;i<=score_count;i++)
                        {

                            var  fnm="name"+(i-1);

                            var lnm="lname"+(i-1);
                            var idnum="idnum"+(i-1);

                            var p="d"+(i-1);
                            sdata[i] = new Array();
                            sdata [i] [0]=i-1;

                            sdata [i] [1]=parseFloat((document.getElementById(p)).value);

                            sdata [i] [2]=''+(document.getElementById(fnm)).value;

                            sdata [i] [3]=''+(document.getElementById(lnm)).value;
                            sdata [i] [4]=''+(document.getElementById(idnum)).value;


                        }


                        plot = $.plot($("#placeholder"), [sdata], options);


                        $(function () {

                            function showTooltip(x, y, reg,name,marks,regno) {
                                $('<div id="tooltip">' + reg + ' '+name+'<br/>'+regno+'<br/>'+marks+'</div>').css( {
                                    position: 'absolute',
                                    display: 'none',
                                    top: y + 10,
                                    left: x + 10,
                                    border: '1px solid #faa',
                                    padding: '2px',
                                    'background-color': '#fee',
                                    opacity: 0.80
                                }).appendTo("body").fadeIn(200);
                            }

                            var previousPoint = null;

                            $("#placeholder").bind("plothover", function (event, pos, item) {

                                if (item) {
                                    if (previousPoint != item.datapoint) {
                                        previousPoint = item.datapoint;
                                        $("#tooltip").remove();
                                        var x = item.datapoint[0].toFixed(0),
                                        y = item.datapoint[1].toFixed(2);
                                        var x_int = parseInt(x)+1;
                                        var y_int = parseInt(y)+1;
                                        showTooltip(item.pageX, item.pageY,sdata[x_int][2], sdata[x_int][3], sdata[x_int][1],sdata [x_int] [4]);
                                    }
                                }
                                else {
                                    $("#tooltip").remove();
                                    previousPoint = null;
                                }
                            });

                            $("#placeholder").bind("plotclick", function (event, pos, item) {
                                if (item) {
                                } else {

                                    plot.setSelection({ xaxis: { from: 0, to: stu_records }, yaxis: { from: 0, to: 100 } });
                                }
                            });

                            $("#placeholder").bind("plotselected", function (event, ranges) {
                                // do the zooming
                                plot = $.plot($("#placeholder"), [sdata], $.extend(true, {}, options, {
                                    xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
                                    yaxis: { min: ranges.yaxis.from, max: ranges.yaxis.to }
                                }));
     
                                updateMarkerImgs(ranges.yaxis.to, ranges.yaxis.from);
                            });

  

                        });
                    </script>
                    <div>
						<br/>
                        <h3>Instructions</h3>
                        <ul>
                            <li>This graph shows the starting boundaries.</li>
                            <li>Select an area within the graph to zoom in. </li>
							<li>Click on any empty space within graph to zoom out.</li>
                            <li>Drag the adjustment images on the right side of the graph to
                                change grade marker lines.</li>
                        </ul>
                    </div>     
                    <?php //echo $OUTPUT->footer();  ?>
                    <script type="text/javascript">
//                        $("#region-main-box").css('left','0px');
                         $("#region-main-box").css('right','0px');
                    </script>
				</div>

</div>

