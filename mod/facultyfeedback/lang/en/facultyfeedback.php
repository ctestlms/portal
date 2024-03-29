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
 * Strings for component 'facultyfeedback', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   facultyfeedback
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['add_item'] = 'Add question to activity';
$string['add_items'] = 'Add question to activity';
$string['add_pagebreak'] = 'Add a page break';
$string['adjustment'] = 'Adjustment';
$string['after_submit'] = 'After submitting';
$string['allowfullanonymous'] = 'Allow full anonymous';
$string['analysis'] = 'Analysis';
$string['anonymous'] = 'Anonymous';
$string['anonymous_edit'] = 'Record user names';
$string['anonymous_entries'] = 'Anonymous entries';
$string['anonymous_user'] = 'Anonymous user';
$string['append_new_items'] = 'Append new items';
$string['autonumbering'] = 'Automated numbers';
$string['autonumbering_help'] = 'Enables or disables automated numbers for each question';
$string['average'] = 'Average';
$string['bold'] = 'Bold';
$string['cancel_moving'] = 'Cancel moving';
$string['cannotmapfacultyfeedback'] = 'Database problem, unable to map facultyfeedback to course';
$string['cannotsavetempl'] = 'saving templates is not allowed';
$string['cannotunmap'] = 'Database problem, unable to unmap';
$string['captcha'] = 'Captcha';
$string['captchanotset'] = 'Captcha hasn\'t been set.';
$string['completed'] = 'completed';
$string['completed_facultyfeedbacks'] = 'Submitted answers';
$string['complete_the_form'] = 'Answer the questions...';
$string['completionsubmit'] = 'View as completed if the facultyfeedback is submitted';
$string['configallowfullanonymous'] = 'If this option is set yes so the facultyfeedback can be completed without any preceding logon. It only affects facultyfeedbacks on the homepage.';
$string['confirmdeleteentry'] = 'Are you sure you want to delete this entry?';
$string['confirmdeleteitem'] = 'Are you sure you want to delete this element?';
$string['confirmdeletetemplate'] = 'Are you sure you want to delete this template?';
$string['confirmusetemplate'] = 'Are you sure you want to use this template?';
$string['continue_the_form'] = 'Continue the form';
$string['count_of_nums'] = 'Count of numbers';
$string['courseid'] = 'courseid';
$string['creating_templates'] = 'Save these questions as a new template';
$string['delete_entry'] = 'Delete entry';
$string['delete_item'] = 'Delete question';
$string['delete_old_items'] = 'Delete old items';
$string['delete_template'] = 'Delete template';
$string['delete_templates'] = 'Delete template...';
$string['depending'] = 'Dependencies';
$string['depending_help'] = 'It is possible to show an item depending on the value of another item.<br />
<strong>Here is an example.</strong><br />
<ul>
<li>First, create an item on which another item will depend on.</li>
<li>Next, add a pagebreak.</li>
<li>Then add the items dependant on the value of the item created before. Choose the item from the list labelled "Dependence item" and write the required value in the textbox labelled "Dependence value".</li>
</ul>
<strong>The item structure should look like this.</strong>
<ol>
<li>Item Q: Do you have a car? A: yes/no</li>
<li>Pagebreak</li>
<li>Item Q: What colour is your car?<br />
(this item depends on item 1 with value = yes)</li>
<li>Item Q: Why don\'t you have a car?<br />
(this item depends on item 1 with value = no)</li>
<li> ... other items</li>
</ol>';
$string['dependitem'] = 'Dependence item';
$string['dependvalue'] = 'Dependence value';
$string['description'] = 'Description';
$string['do_not_analyse_empty_submits'] = 'Do not analyse empty submits';
$string['dropdown'] = 'Multiple choice - single answer allowed (dropdownlist)';
$string['dropdownlist'] = 'Multiple choice - single answer (dropdown)';
$string['dropdownrated'] = 'Dropdownlist (rated)';
$string['dropdown_values'] = 'Answers';
$string['drop_facultyfeedback'] = 'Remove from this course';
$string['edit_item'] = 'Edit question';
$string['edit_items'] = 'Edit questions';
$string['email_notification'] = 'Send e-mail notifications';
$string['emailnotification'] = 'emailnotifications';
$string['emailnotification_help'] = 'If enabled, administrators receive email notification of facultyfeedback submissions.';
$string['emailteachermail'] = '{$a->username} has completed facultyfeedback activity : \'{$a->facultyfeedback}\'

You can view it here:

{$a->url}';
$string['entered'] = 'Entered';
$string['notentered'] = 'Not Entered';
$string['emailteachermailhtml'] = '{$a->username} has completed facultyfeedback activity : <i>\'{$a->facultyfeedback}\'</i><br /><br />
You can view it <a href="{$a->url}">here</a>.';
$string['entries_saved'] = 'Your answers have been saved. Thank you.';
$string['export_questions'] = 'Export questions';
$string['export_to_excel'] = 'Export to Excel';
$string['facultyfeedback:addinstance'] = 'Add a new facultyfeedback';
$string['facultyfeedbackclose'] = 'Close the facultyfeedback at';
$string['facultyfeedbackcloses'] = 'Faculty Feedback closes';
$string['facultyfeedback:complete'] = 'Complete a facultyfeedback';
$string['facultyfeedback:createprivatetemplate'] = 'Create private template';
$string['facultyfeedback:createpublictemplate'] = 'Create public template';
$string['facultyfeedback:deletesubmissions'] = 'Delete completed submissions';
$string['facultyfeedback:deletetemplate'] = 'Delete template';
$string['facultyfeedback:edititems'] = 'Edit items';
$string['facultyfeedback_is_not_for_anonymous'] = 'facultyfeedback is not for anonymous';
$string['facultyfeedback_is_not_open'] = 'The facultyfeedback is not open';
$string['facultyfeedback:mapcourse'] = 'Map courses to global facultyfeedbacks';
$string['facultyfeedbackopen'] = 'Open the facultyfeedback at';
$string['facultyfeedbackopens'] = ' Faculty Feedback opens';
$string['facultyfeedback_options'] = ' Faculty Feedback options';
$string['facultyfeedback:receivemail'] = 'Receive email notification';
$string['facultyfeedback:view'] = 'View a facultyfeedback';
$string['facultyfeedback:viewanalysepage'] = 'View the analysis page after submit';
$string['facultyfeedback:viewreports'] = 'View reports';
$string['file'] = 'File';
$string['filter_by_course'] = 'Filter by course';
$string['handling_error'] = 'Error occurred in facultyfeedback module action handling';
$string['hide_no_select_option'] = 'Hide the "Not selected" option';
$string['horizontal'] = 'horizontal';
$string['check'] = 'Multiple choice - multiple answers';
$string['checkbox'] = 'Multiple choice - multiple answers allowed (check boxes)';
$string['check_values'] = 'Possible responses';
$string['choosefile'] = 'Choose a file';
$string['chosen_facultyfeedback_response'] = 'chosen facultyfeedback response';
$string['importfromthisfile'] = 'Import from this file';
$string['import_questions'] = 'Import questions';
$string['import_successfully'] = 'Import successfully';
$string['info'] = 'Information';
$string['infotype'] = 'Information-Type';
$string['insufficient_responses_for_this_group'] = 'There are insufficient responses for this group';
$string['insufficient_responses'] = 'insufficient responses';
$string['insufficient_responses_help'] = 'There are insufficient responses for this group.

To keep the facultyfeedback anonymous, a minimum of 2 responses must be done.';
$string['item_label'] = 'Label';
$string['item_name'] = 'Question';
$string['items_are_required'] = 'Answers are required to starred questions.';
$string['label'] = 'Label';
$string['line_values'] = 'Rating';
$string['mapcourseinfo'] = 'This is a site-wide facultyfeedback that is available to all courses using the facultyfeedback block. You can however limit the courses to which it will appear by mapping them. Search the course and map it to this facultyfeedback.';
$string['mapcoursenone'] = 'No courses mapped. Faculty Feedback available to all courses';
$string['mapcourse'] = 'Map facultyfeedback to courses';
$string['mapcourse_help'] = 'By default, facultyfeedback forms created on your homepage are available site-wide
and will appear in all courses using the facultyfeedback block. You can force the facultyfeedback form to appear by making it a sticky block or limit the courses in which a facultyfeedback form will appear by mapping it to specific courses.';
$string['mapcourses'] = 'Map facultyfeedback to courses';
$string['mapcourses_help'] = 'Once you have selected the relevant course(s) from your search,
you can associate them with this facultyfeedback using map course(s). Multiple courses may be selected by holding down the Apple or Ctrl key whilst clicking on the course names. A course may be disassociated from a facultyfeedback at any time.';
$string['mappedcourses'] = 'Mapped courses';
$string['max_args_exceeded'] = 'Max 6 arguments can be handled, too many arguments for';
$string['maximal'] = 'maximal';
$string['messageprovider:message'] = ' Faculty Feedback reminder';
$string['messageprovider:submission'] = ' Faculty Feedback notifications';
$string['mode'] = 'Mode';
$string['modulename'] = ' Faculty Feedback';
$string['modulename_help'] = 'The facultyfeedback activity module enables a teacher to create a custom survey for collecting facultyfeedback from participants using a variety of question types including multiple choice, yes/no or text input.

Faculty Feedback responses may be anonymous if desired, and results may be shown to all participants or restricted to teachers only. Any facultyfeedback activities on the site front page may also be completed by non-logged-in users.

Faculty Feedback activities may be used

* For course evaluations, helping improve the content for later participants
* To enable participants to sign up for course modules, events etc.
* For guest surveys of course choices, school policies etc.
* For anti-bullying surveys in which students can report incidents anonymously';
$string['modulename_link'] = 'mod/facultyfeedback/view';
$string['modulenameplural'] = 'Faculty Feedback';
$string['movedown_item'] = 'Move this question down';
$string['move_here'] = 'Move here';
$string['move_item'] = 'Move this question';
$string['moveup_item'] = 'Move this question up';
$string['multichoice'] = 'Multiple choice';
$string['multichoicerated'] = 'Multiple choice (rated)';
$string['multichoicetype'] = 'Multiple choice type';
$string['multichoice_values'] = 'Multiple choice values';
$string['multiple_submit'] = 'Multiple submissions';
$string['multiplesubmit'] = 'Multiple submissions';
$string['multiplesubmit_help'] = 'If enabled for anonymous surveys, users can submit facultyfeedback an unlimited number of times.';
$string['name'] = 'Name';
$string['name_required'] = 'Name required';
$string['next_page'] = 'Next page';
$string['no_handler'] = 'No action handler exists for';
$string['no_itemlabel'] = 'No label';
$string['no_itemname'] = 'No itemname';
$string['no_items_available_yet'] = 'No questions have been set up yet';
$string['non_anonymous'] = 'User\'s name will be logged and shown with answers';
$string['non_anonymous_entries'] = 'non anonymous entries';
$string['non_respondents_students'] = 'non respondents students';
$string['notavailable'] = 'this facultyfeedback is not available';
$string['not_completed_yet'] = 'Not completed yet';
$string['not_started'] = 'not started';
$string['no_templates_available_yet'] = 'No templates available yet';
$string['not_selected'] = 'Not selected';
$string['numeric'] = 'Numeric answer';
$string['numeric_range_from'] = 'Range from';
$string['numeric_range_to'] = 'Range to';
$string['of'] = 'of';
$string['oldvaluespreserved'] = 'All old questions and the assigned values will be preserved';
$string['oldvalueswillbedeleted'] = 'The current questions and all your user\'s responses will be deleted';
$string['only_one_captcha_allowed'] = 'Only one captcha is allowed in a facultyfeedback';
$string['overview'] = 'Overview';
$string['page'] = 'Page';
$string['page-mod-facultyfeedback-x'] = 'Any facultyfeedback module page';
$string['page_after_submit'] = 'Page after submit';
$string['pagebreak'] = 'Page break';
$string['parameters_missing'] = 'Parameters missing from';
$string['picture'] = 'Picture';
$string['picture_file_list'] = 'List of pictures';
$string['picture_values'] = 'Choose one or more<br />picture files from the list:';
$string['pluginadministration'] = 'Faculty Feedback administration';
$string['pluginname'] = 'Faculty Feedback';
$string['position'] = 'Position';
$string['preview'] = 'Preview';
$string['preview_help'] = 'In the preview you can change the order of questions.';
$string['previous_page'] = 'Previous page';
$string['public'] = 'Public';
$string['question'] = 'Question';
$string['questions'] = 'Questions';
$string['radio'] = 'Multiple choice - single answer';
$string['radiobutton'] = 'Multiple choice - single answer allowed (radio buttons)';
$string['radiobutton_rated'] = 'Radiobutton (rated)';
$string['radiorated'] = 'Radiobutton (rated)';
$string['radio_values'] = 'Responses';
$string['ready_facultyfeedbacks'] = 'Ready facultyfeedbacks';
$string['relateditemsdeleted'] = 'All your user\'s responses for this question will also be deleted';
$string['required'] = 'Required';
$string['resetting_data'] = 'Reset facultyfeedback responses';
$string['resetting_facultyfeedbacks'] = 'Resetting facultyfeedbacks';
$string['response_nr'] = 'Response number';
$string['responses'] = 'Responses';
$string['responsetime'] = 'Responsestime';
$string['save_as_new_item'] = 'Save as new question';
$string['save_as_new_template'] = 'Save as new template';
$string['save_entries'] = 'Submit your answers';
$string['save_item'] = 'Save question';
$string['saving_failed'] = 'Saving failed';
$string['saving_failed_because_missing_or_false_values'] = 'Saving failed because missing or false values';
$string['search_course'] = 'Search course';
$string['searchcourses'] = 'Search courses';
$string['searchcourses_help'] = 'Search for the code or name of the course(s) that you wish to associate with this facultyfeedback.';
$string['selected_dump'] = 'Selected indexes of $SESSION variable are dumped below:';
$string['send'] = 'send';
$string['send_message'] = 'send message';
$string['separator_decimal'] = '.';
$string['separator_thousand'] = ',';
$string['show_all'] = 'Show all';
$string['show_analysepage_after_submit'] = 'Show analysis page after submit';
$string['show_entries'] = 'Show responses';
$string['show_entry'] = 'Show response';
$string['show_nonrespondents'] = 'Show non-respondents';
$string['site_after_submit'] = 'Site after submit';
$string['sort_by_course'] = 'Sort by course';
$string['start'] = 'Start';
$string['started'] = 'started';
$string['stop'] = 'End';
$string['subject'] = 'Subject';
$string['switch_group'] = 'Switch group';
$string['switch_item_to_not_required'] = 'switch to: answer not required';
$string['switch_item_to_required'] = 'switch to: answer required';
$string['template'] = 'Template';
$string['templates'] = 'Templates';
$string['template_saved'] = 'Template saved';
$string['textarea'] = 'Longer text answer';
$string['textarea_height'] = 'Number of lines';
$string['textarea_width'] = 'Width';
$string['textfield'] = 'Short text answer';
$string['textfield_maxlength'] = 'Maximum characters accepted';
$string['textfield_size'] = 'Textfield width';
$string['there_are_no_settings_for_recaptcha'] = 'There are no settings for captcha';
$string['this_facultyfeedback_is_already_submitted'] = 'You\'ve already completed this activity.';
$string['timeclose'] = 'Time to close';
$string['timeclose_help'] = 'You can specify times when the facultyfeedback is accessible for people to answer the questions. If the checkbox is not ticked there is no limit defined.';
$string['timeopen'] = 'Time to open';
$string['timeopen_help'] = 'You can specify times when the facultyfeedback is accessible for people to answer the questions. If the checkbox is not ticked there is no limit defined.';
$string['typemissing'] = 'missing value "type"';
$string['update_item'] = 'Save changes to question';
$string['url_for_continue'] = 'URL for continue-button';
$string['url_for_continue_help'] = 'By default after a facultyfeedback is submitted the target of the continue button is the course page. You can define here another target URL for this continue button.';
$string['url_for_continue_button'] = 'URL for continue button';
$string['use_one_line_for_each_value'] = '<br />Use one line for each answer!';
$string['use_this_template'] = 'Use this template';
$string['using_templates'] = 'Use a template';
$string['vertical'] = 'vertical';
$string['viewcompleted'] = 'completed facultyfeedbacks';
$string['viewcompleted_help'] = 'You may view completed facultyfeedback forms, searchable by course and/or by question.
Faculty Feedback responses may be exported to Excel.';
