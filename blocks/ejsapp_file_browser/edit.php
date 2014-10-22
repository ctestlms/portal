<?php

// This file is part of the Moodle block "EJSApp File Browser"
//
// EJSApp File Browser is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// EJSApp File Browser is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// The GNU General Public License is available on <http://www.gnu.org/licenses/>
//
// EJSApp File Browser has been developed by:
//  - Luis de la Torre: ldelatorre@dia.uned.es
//	- Ruben Heradio: rheradio@issi.uned.es
//
//  at the Computer Science and Automatic Control, Spanish Open University
//  (UNED), Madrid, Spain


/**
 * Manage files in folder in private area
 *     
 * @package    block
 * @subpackage ejsapp_file_browser
 * @copyright  2012 Luis de la Torre and Ruben Heradio
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once("$CFG->dirroot/blocks/ejsapp_file_browser/edit_form.php");
require_once("$CFG->dirroot/repository/lib.php");

global $USER, $PAGE, $OUTPUT;

require_login();
if (isguestuser()) {
    die();
}

$context = context_user::instance($USER->id);
$title = get_string('privatefiles', 'block_ejsapp_file_browser');
$struser = get_string('user');

$PAGE->set_url('/blocks/ejsapp_file_browser/edit.php');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('user-private-files');

$data = new stdClass();
$options = array('subdirs'=>1, 'maxbytes'=>$CFG->userquota, 'maxfiles'=>-1, 'accepted_types'=>'*', 'return_types'=>FILE_INTERNAL);
file_prepare_standard_filemanager($data, 'files', $options, $context, 'user', 'private', 0);

$mform = new block_ejsapp_file_browser_form(null, array('data'=>$data, 'options'=>$options));

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/my/'));
} else if ($formdata = $mform->get_data()) {
    $formdata = file_postupdate_standard_filemanager($formdata, 'files', $options, $context, 'user', 'private', 0);
    redirect(new moodle_url('/my/'));
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();