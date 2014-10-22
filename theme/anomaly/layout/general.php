<?php
global $DB;
$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hasheader = (empty($PAGE->layout_options['noheader']));
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$showsidepre = $hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT);
$showsidepost = $hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT);
$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();
if ($showsidepre && !$showsidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($showsidepost && !$showsidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$showsidepost && !$showsidepre) {
    $bodyclasses[] = 'content-only';
}
if ($hascustommenu) {
    $bodyclasses[] = 'has_custom_menu';
}
if ($hasnavbar) {
    $bodyclasses[] = 'hasnavbar';
}

$courseheader = $coursecontentheader = $coursecontentfooter = $coursefooter = '';
if (empty($PAGE->layout_options['nocourseheaderfooter'])) {
    $courseheader = $OUTPUT->course_header();
    $coursecontentheader = $OUTPUT->course_content_header();
    if (empty($PAGE->layout_options['nocoursefooter'])) {
        $coursecontentfooter = $OUTPUT->course_content_footer();
        $coursefooter = $OUTPUT->course_footer();
    }
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>

<div id="page">
<?php if ($hasheading || $hasnavbar || !empty($courseheader)) { ?>
    <div id="page-header">
        <div class="rounded-corner top-left"></div>
        <div class="rounded-corner top-right"></div>
        <?php if ($hasheading) { ?>
        <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
        <div class="headermenu"><?php
            echo $OUTPUT->login_info();
            if (!empty($PAGE->layout_options['langmenu'])) {
                echo $OUTPUT->lang_menu();
            }
            echo $PAGE->headingmenu
        ?></div><?php } ?>

        <?php if (!empty($courseheader)) { ?>
            <div id="course-header"><?php echo $courseheader; ?></div>
        <?php } ?>
        <?php if ($hascustommenu) { ?>
            <div id="custommenu"><?php echo $custommenu; ?></div>
		<?php } ?>

        <?php if ($hasnavbar) { ?>
            <div class="navbar clearfix">
                <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
                <div class="navbutton"><?php echo $PAGE->button; ?></div>
        <?php
	//Added By Hina Yousuf //Feedback Notification
	$month = (int)  date('m');
        if($month==6 || $month==7 || $month==8 ){

                $currentSemester=strtotime("-3 months", time());
        }elseif($month==9){

                $currentSemester=strtotime("-1 months", time());
        }
        elseif($month==10){

                $currentSemester=strtotime("-2 months", time());
        }
        elseif($month==11){

                $currentSemester=strtotime("-3 months", time());
        }
        elseif($month==12){

                $currentSemester=strtotime("-5 months", time());
        }else{

                $currentSemester=strtotime("-6 months", time());
        }

                                        $sql="SELECT e.courseid as courseid, fullname,e.timecreated as timecreated
                                            FROM mdl_user_enrolments ue
                                            JOIN mdl_enrol e ON ( e.id = ue.enrolid )
                                            JOIN mdl_course c ON ( c.id = e.courseid )
                                            AND ue.userid =$USER->id AND c.startdate>$currentSemester";
                                            $courses =  $DB->get_records_sql($sql);
                                            $flag=false;
                                            foreach($courses as $course){
						 $context = get_context_instance(CONTEXT_COURSE, $course->courseid);
                                        $roleid=$DB->get_record_sql("SELECT roleid
                                                                                                                FROM {role_assignments} ra
                                                                                                                WHERE ra.userid =$USER->id AND contextid =$context->id");
                                        if($roleid->roleid==5){


                                                        $sql="SELECT * from {feedback} f WHERE course =$course->courseid and name like '%Student Feedback%'";

                                                                    $feedbacks    =$DB->get_records_sql($sql);
                                                                foreach( $feedbacks as $feedback){

                                                                if($feedback  && time()>$feedback->timeopen && time()<= $feedback->timeclose){
                                                                        $coursemodule=$DB->get_record_sql("SELECT id from {course_modules} cm WHERE course =$course->courseid and instance=$feedback->id and module=23");
$sql="select * from {feedback_completed} f where feedback=$feedback->id and userid=$USER->id ";
                                                                        echo "&nbsp;";
                                                                        if(!$completed = $DB->get_record_sql($sql)) {
                                                                                $flag=true;
                                                                                $faculty = explode("(", $feedback->name);
                                                                                $facultyname=rtrim($faculty[1],")");
$link="<a href='{$CFG->wwwroot}/mod/feedback/complete.php?id={$coursemodule->id}&courseid=&gopage=0' target='_blank'>".$course->fullname."(".$facultyname.")</a>";

            if($nofeedback!=""){
                                                                                        $nofeedback.=" , ".$link;
                                                                                }
                                                                                else{
                                                                                        $nofeedback.=$link;
                                                                                }
										     }
                                                                }
                                                        } 
                                        
                                            }
}

                                            if($flag==true){
                                                echo "<b><div style='width:100%;position:relative;'><br/>
                                                                                                        <div style='width:80%;
height:70px;overflow:hidden; margin: auto;background-color:red;'>
                                                                                                                <div style='width:100%; height:100%; float:left; display: inline-block;
                                                                                                                font-style:bold; font-size:110%;margin: auto;margin-top:4px;padding-top:5px;text-align:center;
                                                                                                                color:white;'>Feedback for the course &quot;".$nofeedback."&quot is not completed.
                                                                                                                </div>
                                                                                                </div>
                                                                                        </div></b>";

                                            }

//end
 } ?>
    </div>
<?php } ?>
<!-- END OF HEADER -->

    <div id="page-content">
        <div id="region-main-box">
            <div id="region-post-box">

                <div id="region-main-wrap">
                    <div id="region-main">
                        <div class="region-content">
                            <?php echo $coursecontentheader; ?>
                            <?php echo $OUTPUT->main_content() ?>
                            <?php echo $coursecontentfooter; ?>
                        </div>
                    </div>
                </div>

                <?php if ($hassidepre) { ?>
                <div id="region-pre" class="block-region">
                    <div class="region-content">
                        <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                    </div>
                </div>
                <?php } ?>

                <?php if ($hassidepost) { ?>
                <div id="region-post" class="block-region">
                    <div class="region-content">
                        <?php echo $OUTPUT->blocks_for_region('side-post') ?>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>

<!-- START OF FOOTER -->
    <?php if (!empty($coursefooter)) { ?>
        <div id="course-footer"><?php echo $coursefooter; ?></div>
    <?php } ?>
    <?php if ($hasfooter) { ?>
    <div id="page-footer" class="clearfix">
        <p class="helplink"><?php echo page_doc_link(get_string('moodledocslink')) ?></p>
        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
        <div class="rounded-corner bottom-left"></div>
        <div class="rounded-corner bottom-right"></div>
    </div>
    <?php } ?>
  <div class="clearfix"></div>
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
