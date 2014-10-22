<?php

//  Display the course home page.

    require_once('../config.php');
    require_once('lib.php');
//    require_once($CFG->dirroot.'/mod/forum/lib.php');
  //  require_once($CFG->libdir.'/conditionlib.php');
    //require_once($CFG->libdir.'/completionlib.php');
    require_once($CFG->dirroot.'/mod/assignment/lib.php');//Added By Hina Yousuf
    require_once($CFG->libdir.'/filelib.php');//@Hina 
    require_once("$CFG->libdir/filestorage/stored_file.php");//Hina

    $id          = optional_param('id', 0, PARAM_INT);
    $name        = optional_param('name', '', PARAM_RAW);
    $edit        = optional_param('edit', -1, PARAM_BOOL);
    $hide        = optional_param('hide', 0, PARAM_INT);
    $show        = optional_param('show', 0, PARAM_INT);
    $idnumber    = optional_param('idnumber', '', PARAM_RAW);
    $sectionid   = optional_param('sectionid', 0, PARAM_INT);
    $section     = optional_param('section', 0, PARAM_INT);
    $move        = optional_param('move', 0, PARAM_INT);
    $marker      = optional_param('marker',-1 , PARAM_INT);
    $switchrole  = optional_param('switchrole',-1, PARAM_INT);
    $modchooser  = optional_param('modchooser', -1, PARAM_BOOL);
    $return      = optional_param('return', 0, PARAM_LOCALURL);
    $download = optional_param('download',false, PARAM_BOOL);


    $params = array();
    if (!empty($name)) {
        $params = array('shortname' => $name);
    } else if (!empty($idnumber)) {
        $params = array('idnumber' => $idnumber);
    } else if (!empty($id)) {
        $params = array('id' => $id);
    }else {
        print_error('unspecifycourseid', 'error');
    }

    $course = $DB->get_record('course', $params, '*', MUST_EXIST);

    $urlparams = array('id' => $course->id);

    // Sectionid should get priority over section number
    if ($sectionid) {
        $section = $DB->get_field('course_sections', 'section', array('id' => $sectionid, 'course' => $course->id), MUST_EXIST);
    }
    if ($section) {
        $urlparams['section'] = $section;
    }

    $PAGE->set_url('/course/view.php', $urlparams); // Defined here to avoid notices on errors etc

    // Prevent caching of this page to stop confusion when changing page after making AJAX changes
    $PAGE->set_cacheable(false);

    preload_course_contexts($course->id);
    $context = context_course::instance($course->id, MUST_EXIST);

    // Remove any switched roles before checking login
    if ($switchrole == 0 && confirm_sesskey()) {
        role_switch($switchrole, $context);
    }

    require_login($course);
     //Added By Hina Yousuf
        if($download){
                if(has_capability('moodle/course:downloadmaterial', $context)) {
                        set_time_limit ( 300 );
                         course_material($course);
                }
        }
    require_once('lib.php');
    require_once($CFG->dirroot.'/mod/forum/lib.php');
    require_once($CFG->libdir.'/completionlib.php');
    require_once('../mod/attforblock/locallib.php');
    require_once($CFG->dirroot.'/mod/assignment/lib.php');//Added By Hina Yousuf
    //end

    // Switchrole - sanity check in cost-order...
    $reset_user_allowed_editing = false;
    if ($switchrole > 0 && confirm_sesskey() &&
        has_capability('moodle/role:switchroles', $context)) {
        // is this role assignable in this context?
        // inquiring minds want to know...
        $aroles = get_switchable_roles($context);
        if (is_array($aroles) && isset($aroles[$switchrole])) {
            role_switch($switchrole, $context);
            // Double check that this role is allowed here
            require_login($course);
        }
        // reset course page state - this prevents some weird problems ;-)
        $USER->activitycopy = false;
        $USER->activitycopycourse = NULL;
        unset($USER->activitycopyname);
        unset($SESSION->modform);
        $USER->editing = 0;
        $reset_user_allowed_editing = true;
    }

    //If course is hosted on an external server, redirect to corresponding
    //url with appropriate authentication attached as parameter
    if (file_exists($CFG->dirroot .'/course/externservercourse.php')) {
        include $CFG->dirroot .'/course/externservercourse.php';
        if (function_exists('extern_server_course')) {
            if ($extern_url = extern_server_course($course)) {
                redirect($extern_url);
            }
        }
    }


    require_once($CFG->dirroot.'/calendar/lib.php');    /// This is after login because it needs $USER

    $logparam = 'id='. $course->id;
    $loglabel = 'view';
    $infoid = $course->id;
    if ($section and $section > 0) {
        $loglabel = 'view section';

        // Get section details and check it exists.
        $modinfo = get_fast_modinfo($course);
        $coursesections = $modinfo->get_section_info($section, MUST_EXIST);

        // Check user is allowed to see it.
        if (!$coursesections->uservisible) {
            // Note: We actually already know they don't have this capability
            // or uservisible would have been true; this is just to get the
            // correct error message shown.
            require_capability('moodle/course:viewhiddensections', $context);
        }
        $infoid = $coursesections->id;
        $logparam .= '&sectionid='. $infoid;
    }
    add_to_log($course->id, 'course', $loglabel, "view.php?". $logparam, $infoid);

    $course->format = clean_param($course->format, PARAM_ALPHA);
    if (!file_exists($CFG->dirroot.'/course/format/'.$course->format.'/format.php')) {
        $course->format = 'weeks';  // Default format is weeks
    }

    $PAGE->set_pagelayout('course');
    $PAGE->set_pagetype('course-view-' . $course->format);
    $PAGE->set_other_editing_capability('moodle/course:manageactivities');

    // Preload course format renderer before output starts.
    // This is a little hacky but necessary since
    // format.php is not included until after output starts
    if (file_exists($CFG->dirroot.'/course/format/'.$course->format.'/renderer.php')) {
        require_once($CFG->dirroot.'/course/format/'.$course->format.'/renderer.php');
        if (class_exists('format_'.$course->format.'_renderer')) {
            // call get_renderer only if renderer is defined in format plugin
            // otherwise an exception would be thrown
            $PAGE->get_renderer('format_'. $course->format);
        }
    }

    if ($reset_user_allowed_editing) {
        // ugly hack
        unset($PAGE->_user_allowed_editing);
    }

    if (!isset($USER->editing)) {
        $USER->editing = 0;
    }
    if ($PAGE->user_allowed_editing()) {
        if (($edit == 1) and confirm_sesskey()) {
            $USER->editing = 1;
            // Redirect to site root if Editing is toggled on frontpage
            if ($course->id == SITEID) {
                redirect($CFG->wwwroot .'/?redirect=0');
            } else if (!empty($return)) {
                redirect($CFG->wwwroot . $return);
            } else {
                $url = new moodle_url($PAGE->url, array('notifyeditingon' => 1));
                redirect($url);
            }
        } else if (($edit == 0) and confirm_sesskey()) {
            $USER->editing = 0;
            if(!empty($USER->activitycopy) && $USER->activitycopycourse == $course->id) {
                $USER->activitycopy       = false;
                $USER->activitycopycourse = NULL;
            }
            // Redirect to site root if Editing is toggled on frontpage
            if ($course->id == SITEID) {
                redirect($CFG->wwwroot .'/?redirect=0');
            } else if (!empty($return)) {
                redirect($CFG->wwwroot . $return);
            } else {
                redirect($PAGE->url);
            }
        }
        if (($modchooser == 1) && confirm_sesskey()) {
            set_user_preference('usemodchooser', $modchooser);
        } else if (($modchooser == 0) && confirm_sesskey()) {
            set_user_preference('usemodchooser', $modchooser);
        }

        if (has_capability('moodle/course:sectionvisibility', $context)) {
            if ($hide && confirm_sesskey()) {
                set_section_visible($course->id, $hide, '0');
                redirect($PAGE->url);
            }

            if ($show && confirm_sesskey()) {
                set_section_visible($course->id, $show, '1');
                redirect($PAGE->url);
            }
        }

        if (has_capability('moodle/course:update', $context)) {
            if (!empty($section)) {
                if (!empty($move) and has_capability('moodle/course:movesections', $context) and confirm_sesskey()) {
                    $destsection = $section + $move;
                    if (move_section_to($course, $section, $destsection)) {
                        if ($course->id == SITEID) {
                            redirect($CFG->wwwroot . '/?redirect=0');
                        } else {
                            redirect(course_get_url($course));
                        }
                    } else {
                        echo $OUTPUT->notification('An error occurred while moving a section');
                    }
                }
            }
        }
    } else {
        $USER->editing = 0;
    }

    $SESSION->fromdiscussion = $PAGE->url->out(false);


    if ($course->id == SITEID) {
        // This course is not a real course.
        redirect($CFG->wwwroot .'/');
    }

    $completion = new completion_info($course);
    if ($completion->is_enabled() && ajaxenabled()) {
        $PAGE->requires->string_for_js('completion-title-manual-y', 'completion');
        $PAGE->requires->string_for_js('completion-title-manual-n', 'completion');
        $PAGE->requires->string_for_js('completion-alt-manual-y', 'completion');
        $PAGE->requires->string_for_js('completion-alt-manual-n', 'completion');

        $PAGE->requires->js_init_call('M.core_completion.init');
    }

    // We are currently keeping the button here from 1.x to help new teachers figure out
    // what to do, even though the link also appears in the course admin block.  It also
    // means you can back out of a situation where you removed the admin block. :)
    if ($PAGE->user_allowed_editing()) {
        $buttons = $OUTPUT->edit_button($PAGE->url);
        $PAGE->set_button($buttons);
    }

    $PAGE->set_title(get_string('course') . ': ' . $course->fullname);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    if ($completion->is_enabled() && ajaxenabled()) {
        // This value tracks whether there has been a dynamic change to the page.
        // It is used so that if a user does this - (a) set some tickmarks, (b)
        // go to another page, (c) clicks Back button - the page will
        // automatically reload. Otherwise it would start with the wrong tick
        // values.
        echo html_writer::start_tag('form', array('action'=>'.', 'method'=>'get'));
        echo html_writer::start_tag('div');
        echo html_writer::empty_tag('input', array('type'=>'hidden', 'id'=>'completion_dynamic_change', 'name'=>'completion_dynamic_change', 'value'=>'0'));
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('form');
    }

    // Course wrapper start.
    echo html_writer::start_tag('div', array('class'=>'course-content'));

    // make sure that section 0 exists (this function will create one if it is missing)
    course_create_sections_if_missing($course, 0);

	// attendance short notice - Added By Hina Yousuf - updated by Qurrat-ul-ain Babar (27th Dec, 2013)
	$sql="select parent,name from mdl_course_categories where id=(SELECT category FROM `mdl_course` WHERE id=$course->id)";
	$parent=$DB->get_record_sql($sql);

    if($parent->parent!=0) {
		do {
			$sql="select id, parent,name from mdl_course_categories where id=$parent->parent";
			$parent=$DB->get_record_sql($sql);
		}
        while($parent->parent!=0);
	}
	
	$module = "SELECT * FROM {modules} WHERE name='attforblock'";
	$moduledata = $DB->get_record_sql($module);
	$moduleid = $moduledata->id;
	
    $sql="SELECT * FROM {course_modules} WHERE course =$course->id and module=$moduleid";
	 // $sql="SELECT * FROM {course_modules} WHERE course =$course->id and module=31";
	
	$attforblock=$DB->get_record_sql($sql);
	$role=$DB->get_records_sql("SELECT roleid FROM {role_assignments} ra WHERE ra.userid =$USER->id AND contextid =$context->id"); 
	//print_r($role);
	foreach($role as $id)
	{
		$check = $id->roleid;
		if ($check == 5)
			$roleid = 5;
		else
			$roleid = 0;
	}
	
	if($parent->name=="Workshops"|| $parent->name=="Miscellaneous"){ // Do nothing 
	}
	else {
		if(isset($attforblock->module)) {	
		//echo $attforblock->instance."<br/>";
			if($roleid == 5) { // show only for student
				$presentqry="select * from {attendance_statuses} where attendanceid=".$attforblock->instance." AND description='Present'";
				$present=$DB->get_record_sql($presentqry);
				//print_r($present->id);
				$absentqry="select * from {attendance_statuses} where attendanceid=".$attforblock->instance." AND description='Absent'";
				$absent=$DB->get_record_sql($absentqry);
				//print_r($absent->id);
				$qry = "SELECT ats.id, al.statusid, ats.description, count(al.statusid) AS stcnt
                      FROM {attendance_log} al
                      JOIN {attendance_sessions} ats
                        ON al.sessionid = ats.id
                     WHERE ats.attendanceid = ".$attforblock->instance." AND ats.sessdate >= ".$course->startdate." AND al.studentid = ".$USER->id." 
					 GROUP BY ats.id";
			
					$data = $DB->get_records_sql($qry);
						
					if(!$data) {
						// dont show attendance alert
					}
					else {
						$statsarray = array();
					
						foreach ($data as $status) {
							//echo "<br/> $status->statusid: Status count: $status->stcnt<br/>";
							$classtype = $status->description;
							switch ($classtype) {
								case "90-Mins Lecture":
									if (array_key_exists($status->statusid, $statsarray))
										$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + ($status->stcnt * 1.5);
									else {
										$statsarray[$status->statusid]->statusid = $status->statusid;
										$statsarray[$status->statusid]->stcnt = $status->stcnt * 1.5;
									}
									break;
								case "Two Hours Lecture":
									if (array_key_exists($status->statusid, $statsarray))
										$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + ($status->stcnt * 2);
									else{
										$statsarray[$status->statusid]->statusid = $status->statusid;
										$statsarray[$status->statusid]->stcnt = $status->stcnt * 2;
									}
									break;
								case "Three Hours Lecture":
									if (array_key_exists($status->statusid, $statsarray))
										$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + ($status->stcnt * 3);
									else {
										$statsarray[$status->statusid]->statusid = $status->statusid;
										$statsarray[$status->statusid]->stcnt = $status->stcnt * 3;
									}
									break;
								case "Three Hours Studio":
									if (array_key_exists($status->statusid, $statsarray))
										$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + ($status->stcnt * 1.5);
									else {
										$statsarray[$status->statusid]->statusid = $status->statusid;
										$statsarray[$status->statusid]->stcnt = $status->stcnt * 1.5;
									}
									break;
								case "Two Hours Ward":
									if (array_key_exists($status->statusid, $statsarray))
										$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + ($status->stcnt * 2);
									else {
										$statsarray[$status->statusid]->statusid = $status->statusid;
										$statsarray[$status->statusid]->stcnt = $status->stcnt * 2;
									}
									break;
								case "Four Hours Ward":
									if (array_key_exists($status->statusid, $statsarray))
										$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + ($status->stcnt * 4);
									else {
										$statsarray[$status->statusid]->statusid = $status->statusid;
										$statsarray[$status->statusid]->stcnt = $status->stcnt * 4;
									}
									break;
								default:
									if (array_key_exists($status->statusid, $statsarray))
										$statsarray[$status->statusid]->stcnt = $statsarray[$status->statusid]->stcnt + $status->stcnt;
									else {
										$statsarray[$status->statusid]->statusid = $status->statusid;
										$statsarray[$status->statusid]->stcnt = $status->stcnt;
									}
							}
						}
						
						//print_r($statsarray);
						 
						$presentcount = $statsarray[$present->id]->stcnt;
						//echo "Present count: ".$presentcount;
						
						$absentcount = $statsarray[$absent->id]->stcnt;
						//echo "ABsent count: ".$absentcount;
						
						$totalcount = $presentcount + $absentcount;
						//echo "Total count: ".$totalcount;
						
						 if($presentcount)
							$percent = ($presentcount / $totalcount) * 100;
						else {
							$presentcount = 0;
							$percent = ($presentcount / $totalcount) * 100;
						}	
						if($percent < 75) { 
							echo "<b><div style='width:100%;position:relative;'>
										<div style='width:90%; height:50px;overflow:hidden; margin: auto;background-color:red;'>
											<div style='width:100%; height:100%; float:left; display: inline-block; font-style:bold; 
											font-size:110%;margin: auto;margin-top:4px;padding-top:10px;text-align:center; color:white;'>
											Your attendance is short in this course. You need minimum 75%. Your current attendance is ".($percent)."%.
											</div>
										</div>
									</div></b><br/>";
						}
						if($percent >= 75 && $percent <= 80) {

							echo "<b><div style='width:100%;position:relative;'>
										<div style='width:90%; height:50px;overflow:hidden; margin: auto;background-color:#FF9900;'>
											<div style='width:100%; height:100%; float:left; display: inline-block; font-style:bold; 
											font-size:110%;margin: auto;margin-top:4px;padding-top:10px;text-align:center; color:white;'>
											Your attendance is low in this course. You may fall short of the required attendance (i.e. below 75%). Your  attendance is ".($percent)."%.
											</div>
										</div>
									</div></b><br/>";
						}
					}
			}
		}
	}  // end of attendance short notice

	// AddedBy Hina Yousuf
	// Feedback Notification
	if($roleid == 5) { // show only for student
		$flag=false;
		$sql="SELECT * from {feedback} f WHERE course =$course->id and name like '%Student Feedback%'";
		$feedbacks = $DB->get_records_sql($sql);
		foreach($feedbacks as $feedback ) {
            $timeAfterOneWeek = strtotime("+7 days",  $feedback->timeopen);
			if($feedback && time()>$timeAfterOneWeek && time()<= $feedback->timeclose) {
				$coursemodule=$DB->get_record_sql("SELECT * from {course_modules} cm WHERE course =$course->id and instance=$feedback->id and module=23");
                
				if(!groups_course_module_visible($coursemodule)) {
                            continue;
                }
				//echo "SELECT id from {course_modules} cm WHERE course =$course->id and instance=$feedback->id";
				$sql="select * from {feedback_completed} f where feedback=$feedback->id and userid=$USER->id ";
				echo "&nbsp;";

                if(!$completed = $DB->get_record_sql($sql)) {
					$flag=true;
					$link="<a href='{$CFG->wwwroot}/mod/feedback/view.php?id={$coursemodule->id}' target='_blank'>".$course->fullname."</a>";
					$url="{$CFG->wwwroot}/mod/feedback/complete.php?id={$coursemodule->id}&courseid=&gopage=0&feedback=1";               
					redirect($url);
				}
			}
		}
	}
	//End of Feedback Notification
    
	// Download Course material
	//Added By Hina Yousuf
	if(has_capability('moodle/course:downloadmaterial', $context)) {
			echo '<form method="post" style="display: inline; margin: 0; padding: 0;">';
			echo '<input type="hidden" name="download" value="true" /><input type="submit" value="Download Course Material" /></form>';
	}
	//end

    // get information about course modules and existing module types
    // format.php in course formats may rely on presence of these variables
    $modinfo = get_fast_modinfo($course);
    $modnames = get_module_types_names();
    $modnamesplural = get_module_types_names(true);
    $modnamesused = $modinfo->get_used_module_names();
    $mods = $modinfo->get_cms();
    $sections = $modinfo->get_section_info_all();

    // CAUTION, hacky fundamental variable defintion to follow!
    // Note that because of the way course fromats are constructed though
    // inclusion we pass parameters around this way..
    $displaysection = $section;

    // Include the actual course format.
    require($CFG->dirroot .'/course/format/'. $course->format .'/format.php');
    // Content wrapper end.

    echo html_writer::end_tag('div');

    // Include course AJAX
    if (include_course_ajax($course, $modnamesused)) {
        // Add the module chooser
        $renderer = $PAGE->get_renderer('core', 'course');
        echo $renderer->course_modchooser(get_module_metadata($course, $modnames, $displaysection), $course);
    }

    echo $OUTPUT->footer();
