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
 * @package   mod-quickfeedback
 * @copyright 2012 Hina Yousuf
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @global int $CHOICE_COLUMN_HEIGHT */

/// Standard functions /////////////////////////////////////////////////////////

/**
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $quickfeedback
 * @return object|null
 */

/**
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $quickfeedback
 * @return string|void
 */


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $quickfeedback
 * @return int
 */
function quickfeedback_add_instance($quickfeedback) {
	global $DB;

	$quickfeedback->timemodified = time();

	if (empty($quickfeedback->timerestrict)) {
		$quickfeedback->timeopen = 0;
		$quickfeedback->timeclose = 0;
	}

	//insert answers
	$quickfeedback->id = $DB->insert_record("quickfeedback", $quickfeedback);
	foreach ($quickfeedback->option as $key => $value) {
		$value = trim($value);
		if (isset($value) && $value <> '') {
			$option = new stdClass();
			$option->text = $value;
			$option->quickfeedbackid = $quickfeedback->id;
			if (isset($quickfeedback->limit[$key])) {
				$option->maxanswers = $quickfeedback->limit[$key];
			}
			$option->timemodified = time();
			$DB->insert_record("quickfeedback_options", $option);
		}
	}

	return $quickfeedback->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $quickfeedback
 * @return bool
 */
function quickfeedback_update_instance($quickfeedback) {
	global $DB;

	$quickfeedback->id = $quickfeedback->instance;
	$quickfeedback->timemodified = time();


	if (empty($quickfeedback->timerestrict)) {
		$quickfeedback->timeopen = 0;
		$quickfeedback->timeclose = 0;
	}

	//update, delete or insert answers
	foreach ($quickfeedback->option as $key => $value) {
		$value = trim($value);
		$option = new stdClass();
		$option->text = $value;
		$option->quickfeedbackid = $quickfeedback->id;
		if (isset($quickfeedback->limit[$key])) {
			$option->maxanswers = $quickfeedback->limit[$key];
		}
		$option->timemodified = time();
		if (isset($quickfeedback->optionid[$key]) && !empty($quickfeedback->optionid[$key])){//existing quickfeedback record
			$option->id=$quickfeedback->optionid[$key];
			if (isset($value) && $value <> '') {
				$DB->update_record("quickfeedback_options", $option);
			} else { //empty old option - needs to be deleted.
				$DB->delete_records("quickfeedback_options", array("id"=>$option->id));
			}
		} else {
			if (isset($value) && $value <> '') {
				$DB->insert_record("quickfeedback_options", $option);
			}
		}
	}

	return $DB->update_record('quickfeedback', $quickfeedback);

}

/**
 * @global object
 * @param object $quickfeedback
 * @param object $user
 * @param object $coursemodule
 * @param array $allresponses
 * @return array
 */

/**
 * @global object
 * @param int $formanswer
 * @param object $quickfeedback
 * @param int $userid
 * @param object $course Course object
 * @param object $cm
 */
function quickfeedback_user_submit_response($course,$data,$userid, $cm) {
	global $DB, $CFG;
	$feedback = new stdClass();
	$feedback->userid = $userid;
	$feedback->timemodified = time();
	$feedback->quickfeedbackid = $cm->id;
	$feedback->response = " ".strip_tags(format_string($data->text,true));
	$DB->insert_record("quickfeedback_response", $feedback);
	
}

function quickfeedback_is_already_submitted($userid, $cm) {

	global $USER, $DB;

	if (!$response = $DB->get_record_sql("select * from {quickfeedback_response} where userid=$userid and quickfeedbackid= $cm->id ")) {
		return false;
	}
	return true;
}

/**
 * @param array $user
 * @param object $cm
 * @return void Output is echo'd
 */

/**
 * @global object
 * @param object $quickfeedback
 * @param object $course
 * @param object $coursemodule
 * @param array $allresponses

 *  * @param bool $allresponses
 * @return object
 */

/**
 * @global object
 * @param array $attemptids
 * @param object $quickfeedback Choice main table row
 * @param object $cm Course-module object
 * @param object $course Course object
 * @return bool
 */


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function quickfeedback_delete_instance($id) {
	global $DB;

	if (! $quickfeedback = $DB->get_record("quickfeedback", array("id"=>"$id"))) {
		return false;
	}

	$result = true;

	if (! $DB->delete_records("quickfeedback", array("id"=>"$quickfeedback->id"))) {
		$result = false;
	}

	return $result;
}


/**
 * Returns text string which is the answer that matches the id
 *
 * @global object
 * @param object $quickfeedback
 * @param int $id
 * @return string
 */

/**
 * Gets a full quickfeedback record
 *
 * @global object
 * @param int $quickfeedbackid
 * @return object|bool The quickfeedback or false
 */


