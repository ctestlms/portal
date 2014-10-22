<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Attendance module renderering methods
 *
 * @package    mod
 * @subpackage attforblock
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/renderables.php');
require_once(dirname(__FILE__).'/renderhelpers.php');

/**
 * Attendance module renderer class
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attforblock_renderer extends plugin_renderer_base {

    ////////////////////////////////////////////////////////////////////////////
    // External API - methods to render attendance renderable components
    ////////////////////////////////////////////////////////////////////////////
	
	/**
     * Renders tabs for attendance
     *
     * @param atttabs - tabs to display
     * @return string html code
     */
    protected function render_attforblock_tabs(attforblock_tabs $atttabs) {
        return print_tabs($atttabs->get_tabs(), $atttabs->currenttab, NULL, NULL, true);
    }

    /**
     * Renders filter controls for attendance
     *
     * @param fcontrols - filter controls data to display
     * @return string html code
     */
    protected function render_attforblock_filter_controls(attforblock_filter_controls $fcontrols) {
        $filtertable = new html_table();
        $filtertable->attributes['class'] = ' ';
        $filtertable->width = '100%';
        $filtertable->align = array('left', 'center', 'right');

        $filtertable->data[0][] = $this->render_sess_group_selector($fcontrols);

        $filtertable->data[0][] = $this->render_curdate_controls($fcontrols);

        $filtertable->data[0][] = $this->render_view_controls($fcontrols);

        $o = html_writer::table($filtertable);
        $o = $this->output->container($o, 'attfiltercontrols attwidth');

        return $o;
    }

    protected function render_sess_group_selector(attforblock_filter_controls $fcontrols) {
        switch ($fcontrols->pageparams->selectortype) {
            case att_page_with_filter_controls::SELECTOR_SESS_TYPE:
                $sessgroups = $fcontrols->get_sess_groups_list();
                if ($sessgroups) {
                    $select = new single_select($fcontrols->url(), 'group', $sessgroups,
                                                $fcontrols->get_current_sesstype(), null, 'selectgroup');
                    $select->label = get_string('sessions', 'attforblock');
                    $output = $this->output->render($select);

                    return html_writer::tag('div', $output, array('class' => 'groupselector'));
                }
                break;
            case att_page_with_filter_controls::SELECTOR_GROUP:
                return groups_print_activity_menu($fcontrols->cm, $fcontrols->url(), true);
        }

        return '';
    }
    
    protected function render_curdate_controls(attforblock_filter_controls $fcontrols) {
        global $CFG;

        $curdate_controls = '';
        if ($fcontrols->curdatetxt) {
            $this->page->requires->strings_for_js(array('calclose', 'caltoday'), 'attforblock');
            $jsvals = array(
                    'cal_months'    => explode(',', get_string('calmonths','attforblock')),
                    'cal_week_days' => explode(',', get_string('calweekdays','attforblock')),
                    'cal_start_weekday' => $CFG->calendar_startwday,
                    'cal_cur_date'  => $fcontrols->curdate);
            $curdate_controls = html_writer::script(js_writer::set_variable('M.attforblock', $jsvals));

            $this->page->requires->js('/mod/attforblock/calendar.js');

            $curdate_controls .= html_writer::link($fcontrols->url(array('curdate' => $fcontrols->prevcur)), $this->output->larrow());
            $params = array(
                    'title' => get_string('calshow', 'attforblock'),
                    'id'    => 'show',
                    'type'  => 'button');
            $button_form = html_writer::tag('button', $fcontrols->curdatetxt, $params);
            foreach ($fcontrols->url_params(array('curdate' => '')) as $name => $value) {
                $params = array(
                        'type'  => 'hidden',
                        'id'    => $name,
                        'name'  => $name,
                        'value' => $value);
                $button_form .= html_writer::empty_tag('input', $params);
            }
            $params = array(
                    'id'        => 'currentdate',
                    'action'    => $fcontrols->url_path(),
                    'method'    => 'post'
            );
            
            $button_form = html_writer::tag('form', $button_form, $params);
            $curdate_controls .= $button_form;

            $curdate_controls .= html_writer::link($fcontrols->url(array('curdate' => $fcontrols->nextcur)), $this->output->rarrow());
        }

        return $curdate_controls;
    }

    protected function render_view_controls(attforblock_filter_controls $fcontrols) {
        $views[ATT_VIEW_ALL] = get_string('all', 'attforblock');
        $views[ATT_VIEW_ALLPAST] = get_string('allpast', 'attforblock');
        $views[ATT_VIEW_MONTHS] = get_string('months', 'attforblock');
        $views[ATT_VIEW_WEEKS] = get_string('weeks', 'attforblock');
        $views[ATT_VIEW_DAYS] = get_string('days', 'attforblock');
        $viewcontrols = '';
        foreach ($views as $key => $sview) {
            if ($key != $fcontrols->pageparams->view) {
                $link = html_writer::link($fcontrols->url(array('view' => $key)), $sview);
                $viewcontrols .= html_writer::tag('span', $link, array('class' => 'attbtn'));
            }
            else
                $viewcontrols .= html_writer::tag('span', $sview, array('class' => 'attcurbtn'));
        }

        return html_writer::tag('nobr', $viewcontrols);
    }
    
    /**
     * Renders attendance sessions managing table
     *
     * @param attforblock_manage_data $sessdata to display
     * @return string html code
     */
    protected function render_attforblock_manage_data(attforblock_manage_data $sessdata) {
        $o = $this->render_sess_manage_table($sessdata) . $this->render_sess_manage_control($sessdata);
        $o = html_writer::tag('form', $o, array('method' => 'post', 'action' => $sessdata->url_sessions()->out()));
        $o = $this->output->container($o, 'generalbox attwidth');
        $o = $this->output->container($o, 'attsessions_manage_table');

        return $o;
    }

    protected function render_sess_manage_table(attforblock_manage_data $sessdata) {
        $this->page->requires->js_init_call('M.mod_attforblock.init_manage');
		
		// $lockdate = mktime(0, 0, 0, date("m"), 5, date("Y"));

        $table = new html_table();
        $table->width = '100%';
        $table->head = array(
                '#',
                get_string('groupsession', 'attforblock'),
                get_string('date'),
                get_string('time'),
                get_string('description','attforblock'),
                get_string('actions'),
				//"Lock Date Cmparison",
                html_writer::checkbox('cb_selector', 0, false, '', array('id' => 'cb_selector'))
            );
        $table->align = array('center', 'center', 'center', 'center', 'center', 'center', 'center');
        $table->size = array('1px', '', '', '', '', '', '1px');

        $i = 0;
        foreach ($sessdata->sessions as $key => $sess) {
            $i++;
			//echo "Session Date: ".$sess->sessdate."<br/>";
           // $dta = $this->construct_date_time_actions($sessdata, $sess, $lockdate);
            $dta = $this->construct_date_time_actions($sessdata, $sess);

            $table->data[$sess->id][] = $i;
            $table->data[$sess->id][] = $sess->groupid ? $sessdata->groups[$sess->groupid]->name : get_string('commonsession', 'attforblock');
			
            $table->data[$sess->id][] = $dta['date'];
            $table->data[$sess->id][] = $dta['time'];
            //$table->data[$sess->id][] = $sess->description;
			// To show topic name in sessions list
			if($sess->topicname != "")
			{
				$table->data[$sess->id][] = $sess->description." (".$sess->topicname." )";
			}
			else
			{
				$table->data[$sess->id][] = $sess->description;
			}
			$table->data[$sess->id][] = $dta['actions'];
			/* if ($sess->sessdate > $lockdate)
			{
				$table->data[$sess->id][] = $dta['actions'];
			}
			else
			{
				$table->data[$sess->id][] = "Done";
			} */
            $table->data[$sess->id][] = html_writer::checkbox('sessid[]', $sess->id, false);
        }

        return html_writer::table($table);
    }

    private function construct_date_time_actions(attforblock_manage_data $sessdata, $sess) {
		//global $CFG, $cm;
		global $CFG, $context, $cm, $DB, $OUTPUT, $USER, $course;
		
		$category = $course->category;
		//echo $category."<br/>";
		$semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
		$path = explode("/", $semester->path);
		$school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]");
		// echo $category.": ".$school->name."<br/>";
		
		$CFG->pixpath = $CFG->wwwroot."/pix";
		$context = context_module::instance($cm->id);
		$lockdate = mktime(23, 59, 59, date("m"), 5, date("Y"));
		$currentdate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		
		$lastmonth = mktime(0, 0, 0, date("m")-1, 1, date("Y"));
		$lastdayprev = date('t',strtotime('last day of previous month'));
		$lastdayprevmonth = mktime(0, 0, 0, date("m")-1, $lastdayprev, date("Y"));;
		$actions = '';

        $date = userdate($sess->sessdate, get_string('strftimedmyw', 'attforblock'));
        $time = $this->construct_time($sess->sessdate, $sess->duration);
        if($sess->lasttaken > 0)
        {	
			// updated to check the lockdate - Qurrat-ul-ain Babar (26th Dec, 2013)
			if(has_capability('mod/attforblock:unmarkattendances', $context) || $school->name == "College of Aeronautical Engineering (CAE)") {
				if ($sessdata->perm->can_change()) {
					$url = $sessdata->url_take($sess->id, $sess->groupid);
					$title = get_string('changeattendance','attforblock');

					$date = html_writer::link($url, $date, array('title' => $title));
					$time = html_writer::link($url, $time, array('title' => $title));

					$actions = $this->output->action_icon($url, new pix_icon('redo', $title, 'attforblock'));
				} else {
					$date = '<i>' . $date . '</i>';
					$time = '<i>' . $time . '</i>';
				}
			}
			else {
				if ($sessdata->perm->can_change()) {					
					if ($sess->sessdate > $lastmonth && $currentdate <= $lockdate) {
					// if ($sess->sessdate > $lastmonth ) {
						$url = $sessdata->url_take($sess->id, $sess->groupid);
						$title = get_string('changeattendance','attforblock');

						$date = html_writer::link($url, $date, array('title' => $title));
						$time = html_writer::link($url, $time, array('title' => $title));

						$actions = $this->output->action_icon($url, new pix_icon('redo', $title, 'attforblock'));
					}
					else if ($sess->sessdate > $lastdayprevmonth && $currentdate  > $lockdate) {
						$url = $sessdata->url_take($sess->id, $sess->groupid);
						$title = get_string('changeattendance','attforblock');

						$date = html_writer::link($url, $date, array('title' => $title));
						$time = html_writer::link($url, $time, array('title' => $title));

						$actions = $this->output->action_icon($url, new pix_icon('redo', $title, 'attforblock'));
					}
					else {
						$date = '<i>' . $date . '</i>';
						$time = '<i>' . $time . '</i>';
							
						$actions = "<img src=\"{$CFG->pixpath}/i/done.gif\" title=\"Attendance Marked and Closed\" alt=\"Done\" />&nbsp;";
					}
				}
			}
        } else {
			if(has_capability('mod/attforblock:unmarkattendances', $context) || $school->name == "College of Aeronautical Engineering (CAE)") {
				$url = $sessdata->url_take($sess->id, $sess->groupid);
				$title = get_string('takeattendance','attforblock');
				$actions = $this->output->action_icon($url, new pix_icon('t/go', $title));
			}
			else
			{
				if ($sessdata->perm->can_take()) {
					if ($sess->sessdate > $lastmonth && $currentdate  <= $lockdate) {
					// if ($sess->sessdate > $lastmonth) {
						$url = $sessdata->url_take($sess->id, $sess->groupid);
						$title = get_string('takeattendance','attforblock');
						$actions = $this->output->action_icon($url, new pix_icon('t/go', $title));
					}
					else if ($sess->sessdate > $lastdayprevmonth && $currentdate  > $lockdate) {
						$url = $sessdata->url_take($sess->id, $sess->groupid);
						$title = get_string('takeattendance','attforblock');
						$actions = $this->output->action_icon($url, new pix_icon('t/go', $title));
					}
					else {
						$date = '<i>' . $date . '</i>';
						$time = '<i>' . $time . '</i>';
						
						$actions = "&nbsp<img src=\"{$CFG->pixpath}/i/late.gif\" alt=\"late\" />";
					}
				}
			}
            
        }
        if($sessdata->perm->can_manage()) {
			if (has_capability('mod/attforblock:unmarkattendances', $context) || $school->name == "College of Aeronautical Engineering (CAE)")
			{
				$url = $sessdata->url_sessions($sess->id, att_sessions_page_params::ACTION_UPDATE);
				$title = get_string('editsession','attforblock');
				$actions .= $this->output->action_icon($url, new pix_icon('t/edit', $title));

				$url = $sessdata->url_sessions($sess->id, att_sessions_page_params::ACTION_DELETE);
				$title = get_string('deletesession','attforblock');
				$actions .= $this->output->action_icon($url, new pix_icon('t/delete', $title));
			}
			else {
				if ($sess->sessdate > $lastmonth && $currentdate <= $lockdate) {
				// if ($sess->sessdate > $lastmonth) {
					$url = $sessdata->url_sessions($sess->id, att_sessions_page_params::ACTION_UPDATE);
					$title = get_string('editsession','attforblock');
					$actions .= $this->output->action_icon($url, new pix_icon('t/edit', $title));

					$url = $sessdata->url_sessions($sess->id, att_sessions_page_params::ACTION_DELETE);
					$title = get_string('deletesession','attforblock');
					$actions .= $this->output->action_icon($url, new pix_icon('t/delete', $title));
				}
				else if ($sess->sessdate > $lastdayprevmonth && $currentdate > $lockdate) {
					$url = $sessdata->url_sessions($sess->id, att_sessions_page_params::ACTION_UPDATE);
					$title = get_string('editsession','attforblock');
					$actions .= $this->output->action_icon($url, new pix_icon('t/edit', $title));

					$url = $sessdata->url_sessions($sess->id, att_sessions_page_params::ACTION_DELETE);
					$title = get_string('deletesession','attforblock');
					$actions .= $this->output->action_icon($url, new pix_icon('t/delete', $title));
				}
			}
        }

        return array('date' => $date, 'time' => $time, 'actions' => $actions);
    }

    protected function render_sess_manage_control(attforblock_manage_data $sessdata) {
        $table = new html_table();
        $table->attributes['class'] = ' ';
        $table->width = '100%';
        $table->align = array('left', 'right');

        $table->data[0][] = $this->output->help_icon('hiddensessions', 'attforblock',
                get_string('hiddensessions', 'attforblock').': '.$sessdata->hiddensessionscount);

        if ($sessdata->perm->can_manage()) {
            $options = array(
                        att_sessions_page_params::ACTION_DELETE_SELECTED => get_string('delete'),
                        att_sessions_page_params::ACTION_CHANGE_DURATION => get_string('changeduration', 'attforblock'));
            $controls = html_writer::select($options, 'action');
            $attributes = array(
                    'type'  => 'submit',
                    'name'  => 'ok',
                    'value' => get_string('ok'));
            $controls .= html_writer::empty_tag('input', $attributes);
        } else {
            $controls = get_string('youcantdo', 'attforblock'); //You can't do anything
        }
        $table->data[0][] = $controls;

        return html_writer::table($table);
    }

    protected function render_attforblock_take_data(attforblock_take_data $takedata) {
		global $cm;
		$context = context_module::instance($cm->id);
		// check if no student has been left unmarked - added by Qurrat-ul-ain
		$divBy = 2;
		if(has_capability('mod/attforblock:unmarkattendances', $context)) {
			$divBy = 3;
		}
		
        $counting = "<script type='text/javascript'>
			function check_all_selected() {
				var inputs = document.getElementsByTagName('input');
				var pcount  = 0;
				var acount  = 0;
				var umcount = 0;
				var count = 0;
				
				for (var i = 0; i < inputs.length; i++) {
					if (inputs[i].type == 'radio') {
						count++;
					}
					if (inputs[i].type == 'radio' && inputs[i].checked && inputs[i].id == 'present') {
						pcount++;
					}
					if (inputs[i].type == 'radio' && inputs[i].checked && inputs[i].id == 'absent') {
						acount++;
					}
					if (inputs[i].type == 'radio' && inputs[i].checked && inputs[i].id == 'unmarked') {
						umcount++;
					}
				}
				var currenttotal = pcount + acount + umcount;
				
				if (count/".$divBy." == currenttotal)
				{
					return true;
				}
				else
				{
					alert('Some students are still unmarked. Please mark all students.');
					return false;
				}
				
			}
			</script>";
			
		echo $counting;
		
		$controls = $this->render_attforblock_take_controls($takedata);
		$alert = "<script type='text/javascript'>
			// alert('I am loading');
			var delay=1000//1 seconds
			setTimeout(function(){
				var div = document.getElementById('fn');
				if (div.checked) {
					// alert('checked');
				} 
				else {
					// alert('You didn\'t check it! Let me check it for you.');
					var helements = document.getElementsByClassName('header c1');
					for (var i=0;i<helements.length;i++){ 
						var current = helements[i].style.display;
						helements[i].style.display = 'none';
					}
					var elements = document.getElementsByClassName('cell c1');
					for (var i=0;i<elements.length;i++){ 
						var current = elements[i].style.display;
						elements[i].style.display = 'none';
					}
				}
			},delay);
		
		</script>";
		echo $alert;
		
		$toggle = "<script type='text/javascript'>
			function toggleDiv(id)
			{				
				var helements = document.getElementsByClassName('header c1');
				for (var i=0;i<helements.length;i++)
				{ 
					var current = helements[i].style.display;
					// alert(current);
					
					if (current == 'none')
						helements[i].style.display = 'block';
					else
						helements[i].style.display = 'none';
				}
				var elements = document.getElementsByClassName('cell c1');
				for (var i=0;i<elements.length;i++)
				{ 
					var current = elements[i].style.display;
					// alert(current);
					
					if (current == 'none') {
						elements[i].style.display = 'block';
						elements[i].style.height = '35px';
						elements[i].style.paddingTop = '15px';
					}
					else
						elements[i].style.display = 'none';
				}
			}
		</script>";
			
		echo $toggle;
		
		//echo "<input type='checkbox' id='foo' value='1' checked='checked'/>";
		// echo "<div style='display:none;' id='checked-a'>Some content</div>";
		
		$params = array(
                'type'  => 'checkbox',
                'value' => '1',
				// 'checked' => 'checked',
				'id' => 'fn',
				'onclick' => "toggleDiv('forcesno');",
				'onload' => "javascript: alert('forcesno');"				
				);
				
		
        $table .= html_writer::tag('left', html_writer::empty_tag('input', $params)." Show Forces No.");
		$table .= html_writer::empty_tag('br');
		$table .= html_writer::empty_tag('br');

        if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_LIST)
            $table .= $this->render_attforblock_take_list($takedata);
        else
            $table .= $this->render_attforblock_take_grid($takedata);
        $table .= html_writer::input_hidden_params($takedata->url());
		
        $params = array(
                'type'  => 'submit',
                'value' => get_string('save','attforblock'),
				'onclick' => "return check_all_selected();"
				);
        $table .= html_writer::tag('center', html_writer::empty_tag('input', $params));
		
		$table .= html_writer::empty_tag('br');
		
		
		// Added by Qurrat-ul-ain Babar (19th Dec, 2013)
		$params = array(
				'type'  => 'input',
				'id' => 'topicname',
				'name' => 'topicname' );
		$table .= html_writer::tag('center', get_string('topic', 'attforblock').html_writer::empty_tag('input', $params));
				
		$params = array(
				'type'  => 'div',
				'id' => 'status');
		$table .= html_writer::tag('div', "<b>Status: </b>", $params);
		
		$params = array(
				'type'  => 'div',
				'id' => 'presentCount' );
		$table .= html_writer::tag('div', "", $params);
		
		$params = array(
				'type'  => 'div',
				'id' => 'absentCount' );
		$table .= html_writer::tag('div', "", $params);
		
		if(has_capability('mod/attforblock:unmarkattendances', $context)) {
			$params = array(
				'type'  => 'div',
				'id' => 'unmarkedCount' );
			$table .= html_writer::tag('div', "", $params);
		}
		else {
			$params = array(
				'type'  => 'div',
				'id' => 'unmarkedCount',
				'style' => 'display:none');
			$table .= html_writer::tag('div', "", $params);
		}
		
		$params = array(
				'type'  => 'div',
				'id' => 'totalCount' );
		$table .= html_writer::tag('div', "", $params);
		
		// $params = array(
				// 'type'  => 'div',
				// 'id' => 'checked-a',
				// 'style' => 'display:none;' );
		// $table .= html_writer::tag('div', "Some content", $params);
		
		//end
        $table = html_writer::tag('form', $table, array('method' => 'post', 'action' => $takedata->url_path()));
		//echo "<div style='display:none;' id='checked-a'>Some content</div>";
        return $controls.$table;
    }
    
    protected function render_attforblock_take_controls(attforblock_take_data $takedata) {
        $table = new html_table();
        $table->attributes['class'] = ' ';

        $table->data[0][] = $this->construct_take_session_info($takedata);
        $table->data[0][] = $this->construct_take_controls($takedata);

        return $this->output->container(html_writer::table($table), 'generalbox takecontrols');
    }

    private function construct_take_session_info(attforblock_take_data $takedata) {
        $sess = $takedata->sessioninfo;
        $date = userdate($sess->sessdate, get_string('strftimedate'));
        $starttime = userdate($sess->sessdate, get_string('strftimehm', 'attforblock'));
        $endtime = userdate($sess->sessdate + $sess->duration, get_string('strftimehm', 'attforblock'));
        $time = html_writer::tag('nobr', $starttime . ($sess->duration > 0 ? ' - ' . $endtime : ''));
        $sessinfo = $date.' '.$time;
        $sessinfo .= html_writer::empty_tag('br');
        $sessinfo .= html_writer::empty_tag('br');
        $sessinfo .= $sess->description;

        return $sessinfo;
    }

    private function construct_take_controls(attforblock_take_data $takedata) {
        $controls = '';
        if ($takedata->pageparams->grouptype == attforblock::SESSION_COMMON and
                ($takedata->groupmode == VISIBLEGROUPS or
                ($takedata->groupmode and $takedata->perm->can_access_all_groups()))) {
            $controls .= groups_print_activity_menu($takedata->cm, $takedata->url(), true);
        }

        $controls .= html_writer::empty_tag('br');

        $options = array(
                att_take_page_params::SORTED_LIST   => get_string('sortedlist','attforblock'),
                att_take_page_params::SORTED_GRID   => get_string('sortedgrid','attforblock')
				);
        $select = new single_select($takedata->url(), 'viewmode', $options, $takedata->pageparams->viewmode, NULL);
        $select->set_label(get_string('viewmode','attforblock'));
        $select->class = 'singleselect inline';
        $controls .= $this->output->render($select);

        if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_GRID) {
            $options = array (1 => '1 '.get_string('column','attforblock'),'2 '.get_string('columns','attforblock'),'3 '.get_string('columns','attforblock'),
                                   '4 '.get_string('columns','attforblock'),'5 '.get_string('columns','attforblock'),'6 '.get_string('columns','attforblock'),
                                   '7 '.get_string('columns','attforblock'),'8 '.get_string('columns','attforblock'),'9 '.get_string('columns','attforblock'),
                                   '10 '.get_string('columns','attforblock'));
            $select = new single_select($takedata->url(), 'gridcols', $options, $takedata->pageparams->gridcols, NULL);
            $select->class = 'singleselect inline';
            $controls .= $this->output->render($select);
        }

        if (count($takedata->sessions4copy) > 1) {
            $controls .= html_writer::empty_tag('br');
            $controls .= html_writer::empty_tag('br');

            $options = array();
            foreach ($takedata->sessions4copy as $sess) {
                $start = userdate($sess->sessdate, get_string('strftimehm', 'attforblock'));
                $end = $sess->duration ? ' - '.userdate($sess->sessdate + $sess->duration, get_string('strftimehm', 'attforblock')) : '';
                $options[$sess->id] = $start . $end;
            }
            $select = new single_select($takedata->url(array(), array('copyfrom')), 'copyfrom', $options);
            $select->set_label(get_string('copyfrom','attforblock'));
            $select->class = 'singleselect inline';
            $controls .= $this->output->render($select);
        }
        
        return $controls;
    }

    protected function render_attforblock_take_list(attforblock_take_data $takedata) {
		global $cm, $DB;
		global $att;
		global $course;
		global $id;
		
		$present = "";
		$absent = "";
		$unmarked = "";
		$decimalpoints = 2;
		
		$counting = "<script type='text/javascript'>
			//var count;
			function count_all() {

				var inputs = document.getElementsByTagName('input');
				var pcount  = 0;
				var acount  = 0;
				var umcount  = 0;
				for (var i = 0; i < inputs.length; i++) {
					if (inputs[i].type == 'radio' && inputs[i].checked && inputs[i].id == 'present') {
						pcount++;
						//alert(inputs[i].id);
					}
					if (inputs[i].type == 'radio' && inputs[i].checked && inputs[i].id == 'absent') {
						acount++;
						//alert(inputs[i].id);
					}
					if (inputs[i].type == 'radio' && inputs[i].checked && inputs[i].id == 'unmarked') {
						umcount++;
						//alert(inputs[i].id);
					}
				}
		
				document.getElementById('presentCount').innerHTML = 'Present Students: ' + pcount;
				document.getElementById('absentCount').innerHTML = 'Absent Students: ' + acount;
				document.getElementById('unmarkedCount').innerHTML = 'Unmarked Students: ' + umcount;
			}
			
			</script>";
			
		echo $counting;
		
		$forcesno = html_writer::link($takedata->url(array('sort' => ATT_SORT_FN)), get_string('forcesno','attforblock'));
		$registrationno = html_writer::link($takedata->url(array('sort' => ATT_SORT_ID)), get_string('registrationno','attforblock'));
		$subgroup = html_writer::link($takedata->url(array('sort' => ATT_SORT_SUBGROUP)), get_string('subgroup','attforblock'));
			
		//$subgroup  = get_string('subgroup','attforblock');
	
	
        $table = new html_table();
        //$table->width = '0%';
        $table->width = '100%'; // table width updated to fit browser page
        $table->head = array(
                '#',
				$forcesno,
				$registrationno,
                $this->construct_fullname_head($takedata),
				$subgroup
            );
        $table->align = array('center', 'center', 'center', 'left', 'center');
        $table->size = array('20px', '', '', '', '');
        // $table->wrap[1] = 'nowrap';
		
        /* foreach ($takedata->statuses as $st) {
            $table->head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null);", $st->acronym, array('title' => get_string('setallstatusesto', 'attforblock', $st->description)));
            $table->align[] = 'center';
            $table->size[] = '20px';
        }
		*/
		
		$context = context_module::instance($cm->id);
		
		if(has_capability('mod/attforblock:unmarkattendances', $context))
		{
			foreach ($takedata->statuses as $st) {
				$acronym = $st->acronym;
				
				if($acronym == "P") {
					$present = $st->id;
				}	
				
				if($acronym == "A") {
					$absent = $st->id;
				}
				
				if($acronym == "UM") {
					$unmarked = $st->id;
				} 
				
				/* $table->head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null);", $st->acronym,
												   array('title' => get_string('setallstatusesto', 'attforblock', $st->description), 'onclick' => 'updateButCount(event);'));
				$table->align[] = 'center';
				$table->size[] = '20px';
				$table->wrap[1] = 'nowrap'; */
				
				if($acronym == "P") {
					$table->head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null); javascript: count_all();", $st->acronym,
													   array('title' => get_string('setallstatusesto', 'attforblock', $st->description)));
					$table->align[] = 'center';
					$table->size[] = '20px';
					$table->wrap[1] = 'nowrap';
				}
				else if($acronym == "A") {
					$table->head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null); javascript: count_all();", $st->acronym,
													   array('title' => get_string('setallstatusesto', 'attforblock', $st->description)));
					$table->align[] = 'center';
					$table->size[] = '20px';
					$table->wrap[1] = 'nowrap';
				}
				else {
					$table->head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null); javascript: count_all();", $st->acronym,
													   array('title' => get_string('setallstatusesto', 'attforblock', $st->description)));
					$table->align[] = 'center';
					$table->size[] = '20px';
					$table->wrap[1] = 'nowrap';
				}
			}			
		}
		else
		{
			foreach ($takedata->statuses as $st) {
				$acronym = $st->acronym;
				if($acronym == "P") {
					$present = $st->id;
				}	
				
				if($acronym == "A") {
					$absent = $st->id;
				}
				
				if($acronym == "UM") {
					$unmarked = $st->id;
				} 
				if($acronym == "P") {
					$table->head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null); javascript: count_all();", $st->acronym,
													   array('title' => get_string('setallstatusesto', 'attforblock', $st->description)));
					$table->align[] = 'center';
					$table->size[] = '20px';
					$table->wrap[1] = 'nowrap';
				}
				else if($acronym == "A") {
					$table->head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null); javascript: count_all();", $st->acronym,
													   array('title' => get_string('setallstatusesto', 'attforblock', $st->description)));
					$table->align[] = 'center';
					$table->size[] = '20px';
					$table->wrap[1] = 'nowrap';
				}
				
			}
		}
		
        $table->head[] = get_string('remarks', 'attforblock');
        $table->align[] = 'center';
        $table->size[] = '20px';
		
		$table->head[] = get_string('summary', 'attforblock');
        $table->align[] = 'center';
        $table->size[] = '20px';
		
        $table->attributes['class'] = 'generaltable takelist';

        $i = 0;
		// Get school for checking "Not joined" students for SEECS only - added by Qurrat-ul-ain Babar (14th Oct, 2014)
		$category = $course->category;
		$semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
		$path = explode("/", $semester->path);
		$school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]"); 
		
        foreach ($takedata->users as $user) {
			// check if school is SEECS here
			if($school->name == "School of Electrical Engineering and Computer Science (SEECS)" ) {
				if ($user->registrationstatus != 'not joined') {
					$i++;
					$counttotal = $counttotal + 1;
					$this->usersstats[$user->id] = $att->get_user_statuses_stat($user->id);
					$presentcount = $this->usersstats[$user->id][$present]->stcnt;
					$absentcount = $this->usersstats[$user->id][$absent]->stcnt;
					$totalcount = $presentcount + $absentcount;
					if($presentcount)
						$percent = ($presentcount / $totalcount) * 100;
					else {
						$presentcount = 0;
						$percent = ($presentcount / $totalcount) * 100;
					}
					
					if($percent < 75) {
						$row = new html_table_row();
						$row->cells[] = '<font color="red">'.$i.'</font>';
					
						// $fullname = html_writer::link($takedata->url_view(array('studentid' => $user->id)), fullname($user));
						$fullname = "<a style=\"color: red\" href=\"view.php?id=$id&amp;studentid={$user->id}\">".((!$att && $update) ? '<font color="red"><b>' : '').fullname($user).((!$att && $update) ? '</b></font>' : '').'</a>';
						$fullname = $this->output->user_picture($user).'<font color="red"><b>'.$fullname.'</b></font>';
						
						$ucdata = $this->construct_take_user_controls($takedata, $user);
						
						if (array_key_exists('warning', $ucdata)) {
							$fullname = html_writer::link($takedata->url_view(array('studentid' => $user->id)), fullname($user));
							$fullname .= html_writer::empty_tag('br');
							$fullname .= $ucdata['warning'];
						} 
						
						$fn = $DB->get_record_sql("select id from {user_info_field} where shortname='forcesno'");
						$fnvalue = $DB->get_record_sql("select data from {user_info_data} where userid=$user->id AND fieldid=$fn->id");
						
						$row->cells[] = '<font color="red"><b>'.$fnvalue->data.'</b></font>';
						// $row->cells[] = '<font color="red"><b>'.html_writer::tag('div', $fnvalue->data, $params).'</b></font>';
						$row->cells[] = '<font color="red"><b>'.$user->idnumber.'</b></font>';
						$row->cells[] = $fullname;
						$row->cells[] = '<font color="red"><b>'.$user->user_subgroup.'</b></font>';
						
						if (array_key_exists('colspan', $ucdata)) {
							$cell = new html_table_cell('text');
							$cell->colspan = $ucdata['colspan'];
						} else {
							
							$row->cells = array_merge($row->cells, $ucdata['text']);
							$row->cells[] = '<font color="red"><b>'.$presentcount."/".$totalcount."=".sprintf("%0.{$decimalpoints}f", $percent)."%".'</b></font>';
						}
						
						if (array_key_exists('class', $ucdata)) {
							$row->attributes['class'] = $ucdata['class'];
						}
					}
					else {
						$row = new html_table_row();
						$row->cells[] = '<font color="black">'.$i.'</font>';
					
						$fullname = html_writer::link($takedata->url_view(array('studentid' => $user->id)), fullname($user));
						$fullname = $this->output->user_picture($user).$fullname;

						$ucdata = $this->construct_take_user_controls($takedata, $user);
						
						if (array_key_exists('warning', $ucdata)) {
							$fullname .= html_writer::empty_tag('br');
							$fullname .= $ucdata['warning'];
						}
						$fn = $DB->get_record_sql("select id from {user_info_field} where shortname='forcesno'");
						$fnvalue = $DB->get_record_sql("select data from {user_info_data} where userid=$user->id AND fieldid=$fn->id");
						
						$row->cells[] = '<font color="black">'.$fnvalue->data.'</font>';
						// $row->cells[] = '<font color="black">'.html_writer::tag('div', $fnvalue->data, $params).'</font>';
						$row->cells[] = '<font color="black">'.$user->idnumber.'</font>';
						$row->cells[] = $fullname;
						$row->cells[] = '<font color="black">'.$user->user_subgroup.'</font>';

						if (array_key_exists('colspan', $ucdata)) {
							$cell = new html_table_cell($ucdata['text']);
							$cell->colspan = $ucdata['colspan'];
						} else {
							
							$row->cells = array_merge($row->cells, $ucdata['text']);
							$row->cells[] = '<font color="black">'.$presentcount."/".$totalcount."=".sprintf("%0.{$decimalpoints}f", $percent)."%".'</font>';
						}
						if (array_key_exists('class', $ucdata)) {
							$row->attributes['class'] = $ucdata['class'];
						}
					}

					$table->data[] = $row;
				}
			}
			else {
				$i++;
				$counttotal = $counttotal + 1;
				$this->usersstats[$user->id] = $att->get_user_statuses_stat($user->id);
				$presentcount = $this->usersstats[$user->id][$present]->stcnt;
				$absentcount = $this->usersstats[$user->id][$absent]->stcnt;
				$totalcount = $presentcount + $absentcount;
				if($presentcount)
					$percent = ($presentcount / $totalcount) * 100;
				else {
					$presentcount = 0;
					$percent = ($presentcount / $totalcount) * 100;
				}
				
				if($percent < 75) {
					$row = new html_table_row();
					$row->cells[] = '<font color="red">'.$i.'</font>';
				
					// $fullname = html_writer::link($takedata->url_view(array('studentid' => $user->id)), fullname($user));
					$fullname = "<a style=\"color: red\" href=\"view.php?id=$id&amp;studentid={$user->id}\">".((!$att && $update) ? '<font color="red"><b>' : '').fullname($user).((!$att && $update) ? '</b></font>' : '').'</a>';
					$fullname = $this->output->user_picture($user).'<font color="red"><b>'.$fullname.'</b></font>';
					
					$ucdata = $this->construct_take_user_controls($takedata, $user);
					
					if (array_key_exists('warning', $ucdata)) {
						$fullname = html_writer::link($takedata->url_view(array('studentid' => $user->id)), fullname($user));
						$fullname .= html_writer::empty_tag('br');
						$fullname .= $ucdata['warning'];
					} 
					
					$fn = $DB->get_record_sql("select id from {user_info_field} where shortname='forcesno'");
					$fnvalue = $DB->get_record_sql("select data from {user_info_data} where userid=$user->id AND fieldid=$fn->id");
					
					$row->cells[] = '<font color="red"><b>'.$fnvalue->data.'</b></font>';
					// $row->cells[] = '<font color="red"><b>'.html_writer::tag('div', $fnvalue->data, $params).'</b></font>';
					$row->cells[] = '<font color="red"><b>'.$user->idnumber.'</b></font>';
					$row->cells[] = $fullname;
					$row->cells[] = '<font color="red"><b>'.$user->user_subgroup.'</b></font>';
					
					if (array_key_exists('colspan', $ucdata)) {
						$cell = new html_table_cell('text');
						$cell->colspan = $ucdata['colspan'];
					} else {
						
						$row->cells = array_merge($row->cells, $ucdata['text']);
						$row->cells[] = '<font color="red"><b>'.$presentcount."/".$totalcount."=".sprintf("%0.{$decimalpoints}f", $percent)."%".'</b></font>';
					}
					
					if (array_key_exists('class', $ucdata)) {
						$row->attributes['class'] = $ucdata['class'];
					}
				}
				else {
					$row = new html_table_row();
					$row->cells[] = '<font color="black">'.$i.'</font>';
				
					$fullname = html_writer::link($takedata->url_view(array('studentid' => $user->id)), fullname($user));
					$fullname = $this->output->user_picture($user).$fullname;

					$ucdata = $this->construct_take_user_controls($takedata, $user);
					
					if (array_key_exists('warning', $ucdata)) {
						$fullname .= html_writer::empty_tag('br');
						$fullname .= $ucdata['warning'];
					}
					$fn = $DB->get_record_sql("select id from {user_info_field} where shortname='forcesno'");
					$fnvalue = $DB->get_record_sql("select data from {user_info_data} where userid=$user->id AND fieldid=$fn->id");
					
					$row->cells[] = '<font color="black">'.$fnvalue->data.'</font>';
					// $row->cells[] = '<font color="black">'.html_writer::tag('div', $fnvalue->data, $params).'</font>';
					$row->cells[] = '<font color="black">'.$user->idnumber.'</font>';
					$row->cells[] = $fullname;
					$row->cells[] = '<font color="black">'.$user->user_subgroup.'</font>';

					if (array_key_exists('colspan', $ucdata)) {
						$cell = new html_table_cell($ucdata['text']);
						$cell->colspan = $ucdata['colspan'];
					} else {
						
						$row->cells = array_merge($row->cells, $ucdata['text']);
						$row->cells[] = '<font color="black">'.$presentcount."/".$totalcount."=".sprintf("%0.{$decimalpoints}f", $percent)."%".'</font>';
					}
					if (array_key_exists('class', $ucdata)) {
						$row->attributes['class'] = $ucdata['class'];
					}
				}

				$table->data[] = $row;
			}
		}
		
        return html_writer::table($table);
    }

    protected function render_attforblock_take_grid(attforblock_take_data $takedata) {
        global $cm;
		$context = context_module::instance($cm->id);
		
		$table = new html_table();
        for ($i=0; $i < $takedata->pageparams->gridcols; $i++) {
            $table->align[] = 'center';
            $table->size[] = '110px';
        }
        $table->attributes['class'] = 'generaltable takegrid';
        $table->headspan = $takedata->pageparams->gridcols;
        $head = array();
		
		
		if(has_capability('mod/attforblock:unmarkattendances', $context))
		{
			foreach ($takedata->statuses as $st) {
				$head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null);", $st->acronym,
											array('title' => get_string('setallstatusesto', 'attforblock', $st->description)));
			}
			$table->head[] = implode('&nbsp;&nbsp;', $head);
		}
		else
		{
			foreach ($takedata->statuses as $st) {
				$acronym = $st->acronym;
				if($acronym == "P" || $acronym == "A") {
					$head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null);", $st->acronym,
												array('title' => get_string('setallstatusesto', 'attforblock', $st->description)));
				}
			}
			$table->head[] = implode('&nbsp;&nbsp;', $head);
		}
        /* foreach ($takedata->statuses as $st) {
            $head[] = html_writer::link("javascript:select_all_in(null, 'st" . $st->id . "', null);", $st->acronym, array('title' => get_string('setallstatusesto', 'attforblock', $st->description)));
        }
        $table->head[] = implode('&nbsp;&nbsp;', $head); */

        $i = 0;
        $row = new html_table_row();
        foreach($takedata->users as $user) {
            $celltext = $this->output->user_picture($user, array('size' => 100));
            $celltext .= html_writer::empty_tag('br');
            $fullname = html_writer::link($takedata->url_view(array('studentid' => $user->id)), fullname($user));
            $celltext .= html_writer::tag('span', $fullname, array('class' => 'fullname'));
            $celltext .= html_writer::empty_tag('br');
            $ucdata = $this->construct_take_user_controls($takedata, $user);
            $celltext .= is_array($ucdata['text']) ? implode('', $ucdata['text']) : $ucdata['text'];
            if (array_key_exists('warning', $ucdata)) {
                $celltext .= html_writer::empty_tag('br');
                $celltext .= $ucdata['warning'];
            }

            $cell = new html_table_cell($celltext);
            if (array_key_exists('class', $ucdata)) $cell->attributes['class'] = $ucdata['class'];
            $row->cells[] = $cell;

            $i++;
            if ($i % $takedata->pageparams->gridcols == 0) {
                $table->data[] = $row;
                $row = new html_table_row();
            }
        }
        if ($i % $takedata->pageparams->gridcols > 0) $table->data[] = $row;
        
        return html_writer::table($table);
    }

    private function construct_fullname_head($data) {
        global $CFG;
		// updated by Qurrat-ul-ain
        if ($data->pageparams->sort == ATT_SORT_LASTNAME || $data->pageparams->sort == ATT_SORT_ID || $data->pageparams->sort == ATT_SORT_SUBGROUP)
            $firstname = html_writer::link($data->url(array('sort' => ATT_SORT_FIRSTNAME)), get_string('firstname'));
        else
            $firstname = get_string('firstname');

        if ($data->pageparams->sort == ATT_SORT_FIRSTNAME || $data->pageparams->sort == ATT_SORT_ID || $data->pageparams->sort == ATT_SORT_SUBGROUP)
            $lastname = html_writer::link($data->url(array('sort' => ATT_SORT_LASTNAME)), get_string('lastname'));
        else
            $lastname = get_string('lastname');

        if ($CFG->fullnamedisplay == 'lastname firstname') {
            $fullnamehead = "$lastname / $firstname";
        } else {
            $fullnamehead = "$firstname / $lastname";
        }

        return $fullnamehead;
    }
	

    private function construct_take_user_controls(attforblock_take_data $takedata, $user) {
		global $cm;
		$context = context_module::instance($cm->id);
		
		$counting = "<script type='text/javascript'>

			function updateButCount(e)
			{
				// Get event from W3C or IE event model
				var e = e || window.event;
				
				// Test that appropriate features are supported
				if ( !document.getElementsByTagName
				|| !document.getElementById
				) return;

				// Initialise variable for keeping count
				var butCount = {present:0, absent:0, unmarked:0, num:0};
				var butSummary = 'Answers cleared';

				// Event may have come from load, click or reset
				// If event was from 'reset', skip counting yes/no
				if (e){

					// Get a collection of the inputs inside x
					var x = document.getElementsByClassName('generaltable takelist');
					var rButs = document.getElementsByTagName('input');
					var temp;
					//alert('Hello');
					// Loop over all the inputs
					for (var i=0, len=rButs.length; i<len; ++i){
					temp = rButs[i];

						// If the input is a radio button
						if ('radio' == temp.type) {
							if(!('unmarked' == temp.id))
								// Add one to the count of radio buttons
								butCount.num += 1;

							// If the button is checked
							if (temp.checked){

								// Add one to butCount.present or butCount.absent as appropriate
								butCount[temp.id] += 1;
							}
						}
					}
				}
				//alert('Hello');
				//alert('butCount.present' + 'butCount.absent' + 'butCount.num');
				// Write the totals for yes and no and the summary to the page
				document.getElementById('presentCount').innerHTML = 'Present Students: ' + butCount.present;
				document.getElementById('absentCount').innerHTML = 'Absent Students: ' + butCount.absent;
				document.getElementById('unmarkedCount').innerHTML = 'Unmarked Students: ' + butCount.unmarked;
				document.getElementById('totalCount').innerHTML = 'Total Students: ' + butCount.num/2;
			
			}
			window.onload = updateButCount;
			
			</script>";
			
		echo $counting;
			
		$celldata = array();
        if ($user->enrolmentend and $user->enrolmentend < $takedata->sessioninfo->sessdate) {
            $celldata['text'] = get_string('enrolmentend', 'attforblock', userdate($user->enrolmentend, '%d.%m.%Y'));
            $celldata['colspan'] = count($takedata->statuses) + 1;
            $celldata['class'] = 'userwithoutenrol';
        } else if (!$user->enrolmentend and $user->enrolmentstatus == ENROL_USER_SUSPENDED) {
            // no enrolmentend and ENROL_USER_SUSPENDED.
            $celldata['text'] = get_string('enrolmentsuspended', 'attforblock');
            $celldata['colspan'] = count($takedata->statuses) + 1;
            $celldata['class'] = 'userwithoutenrol';
        } else {
            
            //$celldata['text'][] = html_writer::empty_tag('select', $params);
			if($user->enrolmentstart == 0){
				$user->enrolmentstart = $user->enrolmentcreated;
			}
			
			
            if ($user->enrolmentstart > $takedata->sessioninfo->sessdate + $takedata->sessioninfo->duration) {
				$celldata['text'] = get_string('enrolmentstart', 'attforblock', userdate($user->enrolmentstart, '%H:%M %d.%m.%Y'));
				$celldata['colspan'] = 2;
                $celldata['warning'] = get_string('enrolmentstart', 'attforblock', userdate($user->enrolmentstart, '%H:%M %d.%m.%Y'));
                $celldata['class'] = 'userwithoutenrol';
            }
			else
			{
				if ($takedata->updatemode and !array_key_exists($user->id, $takedata->sessionlog)) {
					$celldata['class'] = 'userwithoutdata';
				}
				
				$celldata['text'] = array();
				
				// Check if has the capability to unmark - added by Qurrat-ul-ain Babar
				if(has_capability('mod/attforblock:unmarkattendances', $context))
				{
					//echo "Can Unmark variable: ".$canunmark."<br/>";
					foreach ($takedata->statuses as $st) {
						$acronym = $st->acronym;
						if ($acronym == "UM") {
							$params = array(
									'type'  => 'radio',
									'name'  => 'user'.$user->id,
									'class' => 'st'.$st->id,
									'value' => $st->id,
									'id' => 'unmarked',
									'onclick' => "updateButCount(event);"
									);
							if (array_key_exists($user->id, $takedata->sessionlog) and $st->id == $takedata->sessionlog[$user->id]->statusid) {
								$params['checked'] = '';
							}

							$input = html_writer::empty_tag('input', $params);

							if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_GRID) {
								$input = html_writer::tag('nobr', $input . $st->acronym);
							}
							$celldata['text'][] = $input;
						}
						else if ($acronym == "P"){
							$params = array(
									'type'  => 'radio',
									'name'  => 'user'.$user->id,
									'class' => 'st'.$st->id,
									'value' => $st->id,
									'id' => 'present',
									'onclick' => "updateButCount(event);"
									);
							if (array_key_exists($user->id, $takedata->sessionlog) and $st->id == $takedata->sessionlog[$user->id]->statusid) {
								$params['checked'] = '';
							}

							$input = html_writer::empty_tag('input', $params);

							if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_GRID) {
								$input = html_writer::tag('nobr', $input . $st->acronym);
							}
							$celldata['text'][] = $input;
						}
						else {
							$params = array(
									'type'  => 'radio',
									'name'  => 'user'.$user->id,
									'class' => 'st'.$st->id,
									'value' => $st->id,
									'id' => 'absent',
									'onclick' => "updateButCount(event);"
									);
							if (array_key_exists($user->id, $takedata->sessionlog) and $st->id == $takedata->sessionlog[$user->id]->statusid) {
								$params['checked'] = '';
							}

							$input = html_writer::empty_tag('input', $params);

							if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_GRID) {
								$input = html_writer::tag('nobr', $input . $st->acronym);
							}
							$celldata['text'][] = $input;
						}
					
					}
					
				}
				else
				{
					foreach ($takedata->statuses as $st) {
						$acronym = $st->acronym;
						if($acronym == "P")
						{
							$params = array(
									'type'  => 'radio',
									'name'  => 'user'.$user->id,
									'class' => 'st'.$st->id,
									'value' => $st->id,
									'id' => 'present',
									'onclick' => "updateButCount(event);"
									);
							if (array_key_exists($user->id, $takedata->sessionlog) and $st->id == $takedata->sessionlog[$user->id]->statusid) {
								$params['checked'] = '';
							}

							$input = html_writer::empty_tag('input', $params);

							if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_GRID) {
								$input = html_writer::tag('nobr', $input . $st->acronym);
							}
							$celldata['text'][] = $input;
						}
						else if ($acronym == "A")
						{
							$params = array(
									'type'  => 'radio',
									'name'  => 'user'.$user->id,
									'class' => 'st'.$st->id,
									'value' => $st->id,
									'id' => 'absent',
									'onclick' => "updateButCount(event);"
									);
							if (array_key_exists($user->id, $takedata->sessionlog) and $st->id == $takedata->sessionlog[$user->id]->statusid) {
								$params['checked'] = '';
							}

							$input = html_writer::empty_tag('input', $params);

							if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_GRID) {
								$input = html_writer::tag('nobr', $input . $st->acronym);
							}
							$celldata['text'][] = $input;
						}
					}
				}
				
				$params = array(
						'type'  => 'text',
						'name'  => 'remarks'.$user->id);
				if (array_key_exists($user->id, $takedata->sessionlog)) {
					$remarks = $takedata->sessionlog[$user->id]->remarks;
					$celldata['text'][] = '<select name="remarks'.$user->id.'" >
					<option value=""'.($remarks == "" ? "selected = 'selected'" : "").'>None</option>
					<option value="Medical Leave"'.($remarks == "Medical Leave" ? "selected = 'selected'" : "").'>Medical Leave</option>
					<option value="On Field Duty"'.($remarks == "On Field Duty" ? "selected = 'selected'" : "").'>On Field Duty</option>
					<option value="Left Early"'.($remarks == "Left Early" ? "selected = 'selected'" : "").'>Left Early</option>
					<option value="Came Late"'.($remarks == "Came Late" ? "selected = 'selected'" : "").'>Came Late</option>
					<option value="Advisory Note"'.($remarks == "Advisory Note" ? "selected = 'selected'" : "").'>Advisory Note</option>
					<option value="Reporting Sick"'.($remarks == "Reporting Sick" ? "selected = 'selected'" : "").'>Reporting Sick</option>
					<option value="On Leave"'.($remarks == "On Leave" ? "selected = 'selected'" : "").'>On Leave</option>
					<option value="Other"'.($remarks == "Other" ? "selected = 'selected'" : "").'>Other</option>';
					//$celldata->remarks = array_key_exists('remarks'.$user->id, $takedata->sessionlog[$user->id]->remarks) ? $formarr['remarks'.$user->id] : '';
				}
				else
				{
					$remarks = "";
					$celldata['text'][] = '<select name="remarks'.$user->id.'" >
					<option value=""'.($remarks == "" ? "selected = 'selected'" : "").'>None</option>
					<option value="Medical Leave"'.($remarks == "Medical Leave" ? "selected = 'selected'" : "").'>Medical Leave</option>
					<option value="On Field Duty"'.($remarks == "On Field Duty" ? "selected = 'selected'" : "").'>On Field Duty</option>
					<option value="Left Early"'.($remarks == "Left Early" ? "selected = 'selected'" : "").'>Left Early</option>
					<option value="Came Late"'.($remarks == "Came Late" ? "selected = 'selected'" : "").'>Came Late</option>
					<option value="Advisory Note"'.($remarks == "Advisory Note" ? "selected = 'selected'" : "").'>Advisory Note</option>
					<option value="Reporting Sick"'.($remarks == "Reporting Sick" ? "selected = 'selected'" : "").'>Reporting Sick</option>
					<option value="On Leave"'.($remarks == "On Leave" ? "selected = 'selected'" : "").'>On Leave</option>
					<option value="Other"'.($remarks == "Other" ? "selected = 'selected'" : "").'>Other</option>';
				}
			}
        }
		
        /* $celldata = array();
        if ($user->enrolmentend and $user->enrolmentend < $takedata->sessioninfo->sessdate) {
            $celldata['text'] = get_string('enrolmentend', 'attforblock', userdate($user->enrolmentend, '%d.%m.%Y'));
            $celldata['colspan'] = count($takedata->statuses) + 1;
            $celldata['class'] = 'userwithoutenrol';
        }
        // no enrolmentend and ENROL_USER_SUSPENDED
        elseif (!$user->enrolmentend and $user->enrolmentstatus == ENROL_USER_SUSPENDED) {
            $celldata['text'] = get_string('enrolmentsuspended', 'attforblock');
            $celldata['colspan'] = count($takedata->statuses) + 1;
            $celldata['class'] = 'userwithoutenrol';
        }
        else {
            if ($takedata->updatemode and !array_key_exists($user->id, $takedata->sessionlog))
                $celldata['class'] = 'userwithoutdata';

            $celldata['text'] = array();
            foreach ($takedata->statuses as $st) {
                $params = array(
                        'type'  => 'radio',
                        'name'  => 'user'.$user->id,
                        'class' => 'st'.$st->id,
                        'value' => $st->id);
                if (array_key_exists($user->id, $takedata->sessionlog) and $st->id == $takedata->sessionlog[$user->id]->statusid)
                    $params['checked'] = '';

                $input = html_writer::empty_tag('input', $params);

                if ($takedata->pageparams->viewmode == att_take_page_params::SORTED_GRID)
                    $input = html_writer::tag('nobr', $input . $st->acronym);
                
                $celldata['text'][] = $input;
            }
            $params = array(
                    'type'  => 'text',
                    'name'  => 'remarks'.$user->id);
           
            if (array_key_exists($user->id, $takedata->sessionlog))
                $params['value'] = $takedata->sessionlog[$user->id]->remarks;
				$celldata['text'][] = html_writer::empty_tag('input', $params); 
				
            if ($user->enrolmentstart > $takedata->sessioninfo->sessdate + $takedata->sessioninfo->duration) {
                $celldata['warning'] = get_string('enrolmentstart', 'attforblock', userdate($user->enrolmentstart, '%H:%M %d.%m.%Y'));
                $celldata['class'] = 'userwithoutenrol';
            }
        } */

        return $celldata;
    }

	// correct this
    protected function render_attforblock_user_data(attforblock_user_data $userdata) {
        $o = $this->render_user_report_tabs($userdata);

        $table = new html_table();

        $table->attributes['class'] = 'userinfobox';
        $table->colclasses = array('left side', '');
        $table->data[0][] = $this->output->user_picture($userdata->user, array('size' => 100));
        $table->data[0][] = $this->construct_user_data($userdata);

        $o .= html_writer::table($table);

        return $o;
    }

    protected function render_user_report_tabs(attforblock_user_data $userdata) {
        $tabs = array();

        $tabs[] = new tabobject(att_view_page_params::MODE_THIS_COURSE,
                        $userdata->url()->out(true, array('mode' => att_view_page_params::MODE_THIS_COURSE)),
                        get_string('thiscourse','attforblock'));

        $tabs[] = new tabobject(att_view_page_params::MODE_ALL_COURSES,
                        $userdata->url()->out(true, array('mode' => att_view_page_params::MODE_ALL_COURSES)),
                        get_string('allcourses','attforblock'));

        return print_tabs(array($tabs), $userdata->pageparams->mode, NULL, NULL, true);
    }

    private function construct_user_data(attforblock_user_data $userdata) {
        $o = html_writer::tag('h2', fullname($userdata->user));

        if ($userdata->pageparams->mode == att_view_page_params::MODE_THIS_COURSE) {
            $o .= html_writer::empty_tag('hr');
			
			$o .= construct_user_data_stat($userdata->stat, $userdata->statuses,
                        $userdata->gradable, $userdata->grade, $userdata->maxgrade, $userdata->decimalpoints);

            $o .= $this->render_attforblock_filter_controls($userdata->filtercontrols);

            $o .= $this->construct_user_sessions_log($userdata);
        }
        else {
            $prevcid = 0;
            foreach ($userdata->coursesatts as $ca) {
                if ($prevcid != $ca->courseid) {
                    $o .= html_writer::empty_tag('hr');
                    $prevcid = $ca->courseid;

                    $o .= html_writer::tag('h3', $ca->coursefullname);
                }
                $o .= html_writer::tag('h4', $ca->attname);

                $o .= construct_user_data_stat($userdata->stat[$ca->attid], $userdata->statuses[$ca->attid],
                            $userdata->gradable[$ca->attid], $userdata->grade[$ca->attid],
                            $userdata->maxgrade[$ca->attid], $userdata->decimalpoints);
            }
        }

        return $o;
    }

    private function construct_user_sessions_log(attforblock_user_data $userdata) {
        $topic = "";
		$table = new html_table();
		$table->width = '100%';
        $table->attributes['class'] = 'generaltable attwidth boxaligncenter';
        $table->head = array(
            '#',
            get_string('groupsession', 'attforblock'),
            get_string('date'),
            get_string('time'),
            get_string('description','attforblock'),
            get_string('status','attforblock'),
            get_string('remarks','attforblock')
        );
        $table->align = array('', '', 'center', 'center', 'center', 'center');
        $table->size = array('1px', '1px', '1px', '*', '1px', '1px');
		
        $i = 0;
        foreach ($userdata->sessionslog as $sess) {
            $i++;

            $row = new html_table_row();
            $row->cells[] = $i;
            $row->cells[] = html_writer::tag('nobr', $sess->groupid ? $userdata->groups[$sess->groupid]->name : get_string('commonsession', 'attforblock'));
            $row->cells[] = userdate($sess->sessdate, get_string('strftimedmyw', 'attforblock'));
            $row->cells[] = $this->construct_time($sess->sessdate, $sess->duration);
            //$row->cells[] = $sess->description;
			$topic = $sess->topicname;
			// Show topic name with the session description in sessions list
			if($topic != "" || $topic != null)
			{
				$row->cells[] = $sess->description."(".$topic.")";
			}
			else
			{
				$row->cells[] = $sess->description;
			}
			
			if($userdata->user->enrolmentstart == 0){
				$userdata->user->enrolmentstart = $userdata->user->enrolmentcreated;
			}
		
            if (isset($sess->statusid)) {
				
				/// updated - Qurrat-ul-ain Babar (14th Oct, 2014)
				if ($sess->sessdate < $userdata->user->enrolmentstart) {
					$cell = new html_table_cell(get_string('enrolmentstart', 'attforblock', userdate($userdata->user->enrolmentstart, '%d.%m.%Y')));
					$cell->colspan = 2;
					$row->cells[] = $cell;
				}
				elseif ($userdata->user->enrolmentend and $sess->sessdate > $userdata->user->enrolmentend) {
					$cell = new html_table_cell(get_string('enrolmentend', 'attforblock', userdate($userdata->user->enrolmentend, '%d.%m.%Y')));
					$cell->colspan = 2;
					$row->cells[] = $cell;
				}
				else {
					$row->cells[] = $userdata->statuses[$sess->statusid]->description;
					$row->cells[] = $sess->remarks;
				}
				//end 
                
            }
            elseif ($sess->sessdate < $userdata->user->enrolmentstart) {
                $cell = new html_table_cell(get_string('enrolmentstart', 'attforblock', userdate($userdata->user->enrolmentstart, '%d.%m.%Y')));
                $cell->colspan = 2;
                $row->cells[] = $cell;
            }
            elseif ($userdata->user->enrolmentend and $sess->sessdate > $userdata->user->enrolmentend) {
                $cell = new html_table_cell(get_string('enrolmentend', 'attforblock', userdate($userdata->user->enrolmentend, '%d.%m.%Y')));
                $cell->colspan = 2;
                $row->cells[] = $cell;
            }
            else {
                $row->cells[] = '-'; // '?' replaced by '-'
                $row->cells[] = '-';
            }

            $table->data[] = $row;
        }

        return html_writer::table($table);
    }

    private function construct_time($datetime, $duration) {
        $time = html_writer::tag('nobr', construct_session_time($datetime, $duration));

        return $time;
    }

    protected function render_attforblock_report_data(attforblock_report_data $reportdata) {
        global $cm, $DB, $course;
		$context = context_module::instance($cm->id);
		$registrationno  = get_string('registrationno','attforblock');

		$table = new html_table();
        $table->attributes['class'] = 'generaltable attwidth';
		
		// registration number column - Added by Qurrat-ul-ain Babar (20th Dec, 2013)
		$table->head[] = $registrationno;
        $table->align[] = 'left';
        $table->size[] = '2px';
		
        // user picture
        $table->head[] = '';
        $table->align[] = 'left';
        $table->size[] = '1px';

        $table->head[] = $this->construct_fullname_head($reportdata);
        $table->align[] = 'left';
        $table->size[] = '';

		// $presentcount = 0;
		// $absentcount = 0;
		// $unmarkedcount = 0;
		// $totalcount = 0;
		$decimalpoints = 2;
		
        foreach ($reportdata->sessions as $sess) {
			$sesstext = userdate($sess->sessdate, get_string('strftimedm', 'attforblock'));
            $sesstext .= html_writer::empty_tag('br');
            $sesstext .= userdate($sess->sessdate, '('.get_string('strftimehm', 'attforblock').')');
            if (is_null($sess->lasttaken) and $reportdata->perm->can_take() or $reportdata->perm->can_change())
                $sesstext = html_writer::link($reportdata->url_take($sess->id, $sess->groupid), $sesstext);
            $sesstext .= html_writer::empty_tag('br');
            $sesstext .= $sess->description;  // show class type - added by Qurrat-ul-ain Babar (20th Dec, 2013)
			$sesstext .= html_writer::empty_tag('br');
			$sesstext .= $sess->groupid ? $reportdata->groups[$sess->groupid]->name : get_string('commonsession', 'attforblock');

            $table->head[] = $sesstext;
            $table->align[] = 'center';
            $table->size[] = '1px';
        }
        
		if(has_capability('mod/attforblock:unmarkattendances', $context))
		{
			foreach ($reportdata->statuses as $status) {
				$table->head[] = $status->acronym;
				$table->align[] = 'center';
				$table->size[] = '1px';
			}
		}
		else
		{
			foreach ($reportdata->statuses as $status) {
				if($status->acronym == "P" || $status->acronym == "A") {
						$table->head[] = $status->acronym;
						$table->align[] = 'center';
						$table->size[] = '1px';
					}
			}
		}

        if ($reportdata->gradable) {
            $table->head[] = get_string('grade');
            $table->align[] = 'center';
            $table->size[] = '1px';
        }
		
		$table->head[] = get_string('total','attforblock');
        $table->align[] = 'center';
        $table->size[] = '1px';
		
		$table->head[] = get_string('presentpercentage','attforblock');
        $table->align[] = 'center';
        $table->size[] = '1px';
		
		$table->head[] = get_string('absentpercentage','attforblock');
        $table->align[] = 'center';
        $table->size[] = '1px';
		
		// Get school for checking "Not joined" students for SEECS only - added by Qurrat-ul-ain Babar (14th Oct, 2014)
		$category = $course->category;
		$semester = $DB->get_record_sql("select parent,name,path from {course_categories} where id=$category");
		$path = explode("/", $semester->path);
		$school = $DB->get_record_sql("select id,name from {course_categories} where id=$path[1]"); 
        
		foreach ($reportdata->users as $user) {
			// check if school is SEECS here
			if($school->name == "School of Electrical Engineering and Computer Science (SEECS)" ) {
				if ($user->registrationstatus != 'not joined') {
					$presentcount = 0;
					$absentcount = 0;
					$unmarkedcount = 0;
					$totalcount = 0;
						
					$row = new html_table_row();
					
					foreach ($reportdata->statuses as $status) {
						if (has_capability('mod/attforblock:unmarkattendances', $context)) {
							if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
								if($status->acronym == "P") {
										//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
								elseif($status->acronym == "A") {
										//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
								else {
										//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										$unmarkedcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
							}
						}
						else {
							if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
								if($status->acronym == "P") {
										$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
								elseif($status->acronym == "A") {
										$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
								else {}
							}		
						}
					}
					
					$totalcount = $presentcount + $absentcount;
					$presentpercent = ($presentcount/$totalcount)*100;
					$absentpercent = ($absentcount/$totalcount)*100;
					
					if($presentpercent < 75.00 && $totalcount != 0) {
						$row->cells[] = '<div style="background: red;">'.$user->idnumber.'</div>';
						$row->cells[] = $this->output->user_picture($user);
						$row->cells[] = html_writer::link($reportdata->url_view(array('studentid' => $user->id)), '<div style="background: red;">'.fullname($user).'</div>');
						$cellsgenerator = new user_sessions_cells_html_generator($reportdata, $user);
						$row->cells = array_merge($row->cells, $cellsgenerator->get_cells());
						// print_r($reportdata->usersstats);
						// echo "<br/>";
						// echo "<br/>";
						foreach ($reportdata->statuses as $status) {
							if (has_capability('mod/attforblock:unmarkattendances', $context)) {
								if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
									if($status->acronym == "P") {
											$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
											//$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
									elseif($status->acronym == "A") {
											$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
											//$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
									else {
											$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
											//$unmarkedcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
								}
								else{
									$row->cells[] = '<div style="background: red;">0</div>';
								}
							}
							else {
								if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
									if($status->acronym == "P") {
											$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
											//$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
								elseif($status->acronym == "A") {
											$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
											//$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										}
										else {}
								}
								else
								{
									if($status->acronym == "UM") {
									}
									else
										$row->cells[] = '<div style="background: red;">0</div>';
								}	
								
							}
						}

						if ($reportdata->gradable) {
							$row->cells[] = $reportdata->grades[$user->id].' / '.$reportdata->maxgrades[$user->id];
						}
				
						$row->cells[] = '<div style="background: red;">'.$totalcount.'</div>';
						$row->cells[] = '<div style="background: red;">'.sprintf("%0.{$decimalpoints}f", $presentpercent)."%</div>";
						$row->cells[] = '<div style="background: red;">'.sprintf("%0.{$decimalpoints}f", $absentpercent)."%</div>";
					}
					else {
						$row->cells[] = $user->idnumber;
						$row->cells[] = $this->output->user_picture($user);
						$row->cells[] = html_writer::link($reportdata->url_view(array('studentid' => $user->id)), fullname($user));
						$cellsgenerator = new user_sessions_cells_html_generator($reportdata, $user);
						$row->cells = array_merge($row->cells, $cellsgenerator->get_cells());
						// print_r($reportdata->usersstats);
						// echo "<br/>";
						// echo "<br/>";
						foreach ($reportdata->statuses as $status) {
							if (has_capability('mod/attforblock:unmarkattendances', $context)) {
								if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
									if($status->acronym == "P") {
											$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
											//$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
									elseif($status->acronym == "A") {
											$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
											//$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
									else {
											$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
											//$unmarkedcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
								}
								else
								{
									$row->cells[] = 0;
								}
							}
							else {
								if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
									if($status->acronym == "P") {
											$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
											//$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
								elseif($status->acronym == "A") {
											$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
											//$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										}
										else {}
								}
								else
								{
									if($status->acronym == "UM") {
									}
									else
										$row->cells[] = 0;
								}	
								
							}
						}

						if ($reportdata->gradable) {
							$row->cells[] = $reportdata->grades[$user->id].' / '.$reportdata->maxgrades[$user->id];
						}
				
						$row->cells[] = $totalcount;
						$row->cells[] = sprintf("%0.{$decimalpoints}f", $presentpercent)."%";
						$row->cells[] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
					}

					$table->data[] = $row;
				}
			}
			else {
				$presentcount = 0;
				$absentcount = 0;
				$unmarkedcount = 0;
				$totalcount = 0;
					
				$row = new html_table_row();
				
				foreach ($reportdata->statuses as $status) {
					if (has_capability('mod/attforblock:unmarkattendances', $context)) {
						if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
							if($status->acronym == "P") {
									//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
							}
							elseif($status->acronym == "A") {
									//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
							}
							else {
									//$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									$unmarkedcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
							}
						}
					}
					else {
						if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
							if($status->acronym == "P") {
									$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
							}
							elseif($status->acronym == "A") {
									$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
							}
							else {}
						}		
					}
				}
				
				$totalcount = $presentcount + $absentcount;
				$presentpercent = ($presentcount/$totalcount)*100;
				$absentpercent = ($absentcount/$totalcount)*100;
				
				if($presentpercent < 75.00 && $totalcount != 0) {
					$row->cells[] = '<div style="background: red;">'.$user->idnumber.'</div>';
					$row->cells[] = $this->output->user_picture($user);
					$row->cells[] = html_writer::link($reportdata->url_view(array('studentid' => $user->id)), '<div style="background: red;">'.fullname($user).'</div>');
					$cellsgenerator = new user_sessions_cells_html_generator($reportdata, $user);
					$row->cells = array_merge($row->cells, $cellsgenerator->get_cells());
					// print_r($reportdata->usersstats);
					// echo "<br/>";
					// echo "<br/>";
					foreach ($reportdata->statuses as $status) {
						if (has_capability('mod/attforblock:unmarkattendances', $context)) {
							if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
								if($status->acronym == "P") {
										$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
										//$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
								elseif($status->acronym == "A") {
										$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
										//$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
								else {
										$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
										//$unmarkedcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
							}
							else{
								$row->cells[] = '<div style="background: red;">0</div>';
							}
						}
						else {
							if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
								if($status->acronym == "P") {
										$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
										//$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
							elseif($status->acronym == "A") {
										$row->cells[] = '<div style="background: red;">'.$reportdata->usersstats[$user->id][$status->id]->stcnt.'</div>';
										//$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
									else {}
							}
							else
							{
								if($status->acronym == "UM") {
								}
								else
									$row->cells[] = '<div style="background: red;">0</div>';
							}	
							
						}
					}

					if ($reportdata->gradable) {
						$row->cells[] = $reportdata->grades[$user->id].' / '.$reportdata->maxgrades[$user->id];
					}
			
					$row->cells[] = '<div style="background: red;">'.$totalcount.'</div>';
					$row->cells[] = '<div style="background: red;">'.sprintf("%0.{$decimalpoints}f", $presentpercent)."%</div>";
					$row->cells[] = '<div style="background: red;">'.sprintf("%0.{$decimalpoints}f", $absentpercent)."%</div>";
				}
				else {
					$row->cells[] = $user->idnumber;
					$row->cells[] = $this->output->user_picture($user);
					$row->cells[] = html_writer::link($reportdata->url_view(array('studentid' => $user->id)), fullname($user));
					$cellsgenerator = new user_sessions_cells_html_generator($reportdata, $user);
					$row->cells = array_merge($row->cells, $cellsgenerator->get_cells());
					// print_r($reportdata->usersstats);
					// echo "<br/>";
					// echo "<br/>";
					foreach ($reportdata->statuses as $status) {
						if (has_capability('mod/attforblock:unmarkattendances', $context)) {
							if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
								if($status->acronym == "P") {
										$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										//$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
								elseif($status->acronym == "A") {
										$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										//$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
								else {
										$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										//$unmarkedcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
							}
							else
							{
								$row->cells[] = 0;
							}
						}
						else {
							if (array_key_exists($status->id, $reportdata->usersstats[$user->id])) {
								if($status->acronym == "P") {
										$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										//$presentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
								}
							elseif($status->acronym == "A") {
										$row->cells[] = $reportdata->usersstats[$user->id][$status->id]->stcnt;
										//$absentcount = $reportdata->usersstats[$user->id][$status->id]->stcnt;
									}
									else {}
							}
							else
							{
								if($status->acronym == "UM") {
								}
								else
									$row->cells[] = 0;
							}	
							
						}
					}

					if ($reportdata->gradable) {
						$row->cells[] = $reportdata->grades[$user->id].' / '.$reportdata->maxgrades[$user->id];
					}
			
					$row->cells[] = $totalcount;
					$row->cells[] = sprintf("%0.{$decimalpoints}f", $presentpercent)."%";
					$row->cells[] = sprintf("%0.{$decimalpoints}f", $absentpercent)."%";
				}

				$table->data[] = $row;
			
			}
        }

        return html_writer::table($table);
    }

    protected function render_attforblock_preferences_data(attforblock_preferences_data $prefdata) {
        $this->page->requires->js('/mod/attforblock/module.js');

        $table = new html_table();
        $table->width = '100%';
        $table->head = array('#',
                             get_string('acronym', 'attforblock'),
                             get_string('description'),
                             get_string('grade'),
                             get_string('action'));
        $table->align = array('center', 'center', 'center', 'center', 'center', 'center');

        $i = 1;
        foreach ($prefdata->statuses as $st) {
            $table->data[$i][] = $i;
            $table->data[$i][] = $this->construct_text_input('acronym['.$st->id.']', 2, 2, $st->acronym);
            $table->data[$i][] = $this->construct_text_input('description['.$st->id.']', 30, 30, $st->description);
            $table->data[$i][] = $this->construct_text_input('grade['.$st->id.']', 4, 4, $st->grade);
            $table->data[$i][] = $this->construct_preferences_actions_icons($st, $prefdata);

            $i++;
        }

        $table->data[$i][] = '*';
        $table->data[$i][] = $this->construct_text_input('newacronym', 2, 2);
        $table->data[$i][] = $this->construct_text_input('newdescription', 30, 30);
        $table->data[$i][] = $this->construct_text_input('newgrade', 4, 4);
        $table->data[$i][] = $this->construct_preferences_button(get_string('add', 'attforblock'), att_preferences_page_params::ACTION_ADD);

        $o = html_writer::tag('h1', get_string('myvariables','attforblock'));
        $o .= html_writer::table($table);
        $o .= html_writer::input_hidden_params($prefdata->url(array(), false));
        $o .= $this->construct_preferences_button(get_string('update', 'attforblock'), att_preferences_page_params::ACTION_SAVE);
        $o = html_writer::tag('form', $o, array('id' => 'preferencesform', 'method' => 'post', 'action' => $prefdata->url(array(), false)->out_omit_querystring()));
        $o = $this->output->container($o, 'generalbox attwidth');
        
        return $o;
    }

    private function construct_text_input($name, $size, $maxlength, $value='') {
        $attributes = array(
                'type'      => 'text',
                'name'      => $name,
                'size'      => $size,
                'maxlength' => $maxlength,
                'value'     => $value);
        return html_writer::empty_tag('input', $attributes);
    }

    private function construct_preferences_actions_icons($st, $prefdata) {
        global $OUTPUT;

        if ($st->visible) {
            $params = array(
                    'action' => att_preferences_page_params::ACTION_HIDE,
                    'statusid' => $st->id);
            $showhideicon = $OUTPUT->action_icon(
                    $prefdata->url($params),
                    new pix_icon("t/hide", get_string('hide')));
        }
        else {
            $params = array(
                    'action' => att_preferences_page_params::ACTION_SHOW,
                    'statusid' => $st->id);
            $showhideicon = $OUTPUT->action_icon(
                    $prefdata->url($params),
                    new pix_icon("t/show", get_string('show')));
        }
        if (!$st->haslogs) {
            $params = array(
                    'action' => att_preferences_page_params::ACTION_DELETE,
                    'statusid' => $st->id);
            $deleteicon = $OUTPUT->action_icon(
                    $prefdata->url($params),
                    new pix_icon("t/delete", get_string('delete')));
        }
        else $deleteicon = '';

        return $showhideicon . $deleteicon;
    }

    private function construct_preferences_button($text, $action) {
        $attributes = array(
                'type'      => 'submit',
                'value'     => $text,
                'onclick'   => 'M.mod_attforblock.set_preferences_action('.$action.')');
        return html_writer::empty_tag('input', $attributes);
    }

}
