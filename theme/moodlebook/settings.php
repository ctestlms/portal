<?php

/**
 * Settings for the moodlebook theme
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
	// logo image setting
	$name = 'theme_moodlebook/logo';
	$title = get_string('logo','theme_moodlebook');
	$description = get_string('logodesc', 'theme_moodlebook');
	$setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
	$settings->add($setting);   

	// Background colour setting
    $name = 'theme_moodlebook/backgroundcolor';
    $title = get_string('backgroundcolor','theme_moodlebook');
    $description = get_string('backgroundcolordesc', 'theme_moodlebook');
    $default = '#fff';
    $previewconfig = array('selector'=>'html', 'style'=>'backgroundColor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

	// Header colour setting
    $name = 'theme_moodlebook/headercolor';
    $title = get_string('headercolor','theme_moodlebook');
    $description = get_string('headercolordesc', 'theme_moodlebook');
    $default = '#3E509C';
    $previewconfig = array('selector'=>'html', 'style'=>'headercolor');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default, $previewconfig);
    $settings->add($setting);

    // Foot note setting
    $name = 'theme_moodlebook/footnote';
    $title = get_string('footnote','theme_moodlebook');
    $description = get_string('footnotedesc', 'theme_moodlebook');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_moodlebook/customcss';
    $title = get_string('customcss','theme_moodlebook');
    $description = get_string('customcssdesc', 'theme_moodlebook');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $settings->add($setting);

    // Hide Settings block
    $name = 'theme_moodlebook/hidesettingsblock';
    $title = get_string('hidesettingsblock','theme_moodlebook');
    $description = get_string('hidesettingsblockdesc', 'theme_moodlebook');
    $default = 1;
    $choices = array(
	1=> get_string('yes', 'theme_moodlebook'),
	0=> get_string('no', 'theme_moodlebook')
	);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);

    // Hide Navigation block
    $name = 'theme_moodlebook/hidenavigationblock';
    $title = get_string('hidenavigationblock','theme_moodlebook');
    $description = get_string('hidenavigationblockdesc', 'theme_moodlebook');
    $default = 0;
    $choices = array(
	1=> get_string('yes', 'theme_moodlebook'),
	0=> get_string('no', 'theme_moodlebook')
	);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);
}