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
 * Multiple plugin authentication Support library
 *
 * 2006-08-28  File created, AUTH return values defined.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returned when the login was successful.
 */
define('AUTH_OK',     0);

/**
 * Returned when the login was unsuccessful.
 */
define('AUTH_FAIL',   1);

/**
 * Returned when the login was denied (a reason for AUTH_FAIL).
 */
define('AUTH_DENIED', 2);

/**
 * Returned when some error occurred (a reason for AUTH_FAIL).
 */
define('AUTH_ERROR',  4);

/**
 * Authentication - error codes for user confirm
 */
define('AUTH_CONFIRM_FAIL', 0);
define('AUTH_CONFIRM_OK', 1);
define('AUTH_CONFIRM_ALREADY', 2);
define('AUTH_CONFIRM_ERROR', 3);

# MDL-14055
define('AUTH_REMOVEUSER_KEEP', 0);
define('AUTH_REMOVEUSER_SUSPEND', 1);
define('AUTH_REMOVEUSER_FULLDELETE', 2);

/**
 * Abstract authentication plugin.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class auth_plugin_base {

    /**
     * The configuration details for the plugin.
     * @var object
     */
    var $config;

    /**
     * Authentication plugin type - the same as db field.
     * @var string
     */
    var $authtype;
    /*
     * The fields we can lock and update from/to external authentication backends
     * @var array
     */
    var $userfields = array(
        'firstname',
        'lastname',
        'user_group',
	'user_subgroup',
        'email',
        'city',
        'country',
        'lang',
        'description',
        'url',
        'idnumber',
        'institution',
        'department',
        'phone1',
        'phone2',
        'address'
    );

    /**

     * This is the primary method that is used by the authenticate_user_login()
     * function in moodlelib.php.
     *
     * This method should return a boolean indicating
     * whether or not the username and password authenticate successfully.
     *
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     *
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        print_error('mustbeoveride', 'debug', '', 'user_login()' );
    }

    /**
     * Returns true if this authentication plugin can change the users'
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        //override if needed
        return false;
    }

    /**
     * Returns the URL for changing the users' passwords, or empty if the default
     * URL can be used.
     *
     * This method is used if can_change_password() returns true.
     * This method is called only when user is logged in, it may use global $USER.
     *
     * @return moodle_url url of the profile page or null if standard used
     */
    function change_password_url() {
        //override if needed
        return null;
    }

    /**
     * Returns true if this authentication plugin can edit the users'
     * profile.
     *
     * @return bool
     */
    function can_edit_profile() {
        //override if needed
        return true;
    }

    /**
     * Returns the URL for editing the users' profile, or empty if the default
     * URL can be used.
     *
     * This method is used if can_edit_profile() returns true.
     * This method is called only when user is logged in, it may use global $USER.
     *
     * @return moodle_url url of the profile page or null if standard used
     */
    function edit_profile_url() {
        //override if needed
        return null;
    }

    /**
     * Returns true if this authentication plugin is "internal".
     *
     * Internal plugins use password hashes from Moodle user table for authentication.
     *
     * @return bool
     */
    function is_internal() {
        //override if needed
        return true;
    }

    /**
     * Indicates if password hashes should be stored in local moodle database.
     * @return bool true means md5 password hash stored in user table, false means flag 'not_cached' stored there instead
     */
    function prevent_local_passwords() {
        return !$this->is_internal();
    }

    /**
     * Indicates if moodle should automatically update internal user
     * records with data from external sources using the information
     * from get_userinfo() method.
     *
     * @return bool true means automatically copy data from ext to user table
     */
    function is_synchronised_with_external() {
        return !$this->is_internal();
    }

    /**
     * Updates the user's password.
     *
     * In previous versions of Moodle, the function
     * auth_user_update_password accepted a username as the first parameter. The
     * revised function expects a user object.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
     *
     * @return bool                  True on success
     */
    function user_update_password($user, $newpassword) {
        //override if needed
        return true;
    }

    /**
     * Called when the user record is updated.
     * Modifies user in external database. It takes olduser (before changes) and newuser (after changes)
     * compares information saved modified information to external db.
     *
     * @param mixed $olduser     Userobject before modifications    (without system magic quotes)
     * @param mixed $newuser     Userobject new modified userobject (without system magic quotes)
     * @return boolean true if updated or update ignored; false if error
     *
     */
    function user_update($olduser, $newuser) {
        //override if needed
        return true;
    }

    /**
     * User delete requested - internal user record is mared as deleted already, username not present anymore.
     *
     * Do any action in external database.
     *
     * @param object $user       Userobject before delete    (without system magic quotes)
     * @return void
     */
    function user_delete($olduser) {
        //override if needed
        return;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        //override if needed
        return false;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_signup() {
        //override if needed
        return false;
    }

    /**
     * Sign up a new user ready for confirmation.
     * Password is passed in plaintext.
     *
     * @param object $user new user object
     * @param boolean $notify print notice with link and terminate
     */
    function user_signup($user, $notify=true) {
        //override when can signup
        print_error('mustbeoveride', 'debug', '', 'user_signup()' );
    }

    /**
     * Return a form to capture user details for account creation.
     * This is used in /login/signup.php.
     * @return moodle_form A form which edits a record from the user table.
     */
    function signup_form() {
        global $CFG;

        require_once($CFG->dirroot.'/login/signup_form.php');
        return new login_signup_form(null, null, 'post', '', array('autocomplete'=>'on'));
    }

    /**
     * Returns true if plugin allows confirming of new users.
     *
     * @return bool
     */
    function can_confirm() {
        //override if needed
        return false;
    }

    /**
     * Confirm the new user as registered.
     *
     * @param string $username
     * @param string $confirmsecret
     */
    function user_confirm($username, $confirmsecret) {
        //override when can confirm
        print_error('mustbeoveride', 'debug', '', 'user_confirm()' );
    }

    /**
     * Checks if user exists in external db
     *
     * @param string $username (with system magic quotes)
     * @return bool
     */
    function user_exists($username) {
        //override if needed
        return false;
    }

    /**
     * return number of days to user password expires
     *
     * If userpassword does not expire it should return 0. If password is already expired
     * it should return negative value.
     *
     * @param mixed $username username (with system magic quotes)
     * @return integer
     */
    function password_expire($username) {
        return 0;
    }
    /**
     * Sync roles for this user - usually creator
     *
     * @param $user object user object (without system magic quotes)
     */
    function sync_roles($user) {
        //override if needed
    }

    /**
     * Read user information from external database and returns it as array().
     * Function should return all information available. If you are saving
     * this information to moodle user-table you should honour synchronisation flags
     *
     * @param string $username username
     *
     * @return mixed array with no magic quotes or false on error
     */
    function get_userinfo($username) {
        //override if needed
        return array();
    }

    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param object $config
     * @param object $err
     * @param array $user_fields
     */
    function config_form($config, $err, $user_fields) {
        //override if needed
    }

    /**
     * A chance to validate form data, and last chance to
     * do stuff before it is inserted in config_plugin
     * @param object object with submitted configuration settings (without system magic quotes)
     * @param array $err array of error messages
     */
     function validate_form($form, &$err) {
        //override if needed
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     *
     * @param object object with submitted configuration settings (without system magic quotes)
     */
    function process_config($config) {
        //override if needed
        return true;
    }

    /**
     * Hook for overriding behaviour of login page.
     * This method is called from login/index.php page for all enabled auth plugins.
     *
     * @global object
     * @global object
     */
    function loginpage_hook() {
        global $frm;  // can be used to override submitted login form
        global $user; // can be used to replace authenticate_user_login()

        //override if needed
    }

    /**
     * Post authentication hook.
     * This method is called from authenticate_user_login() for all enabled auth plugins.
     *
     * @param object $user user object, later used for $USER
     * @param string $username (with system magic quotes)
     * @param string $password plain text password (with system magic quotes)
     */
    function user_authenticated_hook(&$user, $username, $password) {
        //override if needed
    }

    /**
     * Pre logout hook.
     * This method is called from require_logout() for all enabled auth plugins,
     *
     * @global object
     */
    function prelogout_hook() {
        global $USER; // use $USER->auth to find the plugin used for login

        //override if needed
    }

    /**
     * Hook for overriding behaviour of logout page.
     * This method is called from login/logout.php page for all enabled auth plugins.
     *
     * @global object
     * @global string
     */
    function logoutpage_hook() {
        global $USER;     // use $USER->auth to find the plugin used for login
        global $redirect; // can be used to override redirect after logout

        //override if needed
    }

    /**
     * Hook called before timing out of database session.
     * This is useful for SSO and MNET.
     *
     * @param object $user
     * @param string $sid session id
     * @param int $timecreated start of session
     * @param int $timemodified user last seen
     * @return bool true means do not timeout session yet
     */
    function ignore_timeout_hook($user, $sid, $timecreated, $timemodified) {
        return false;
    }

    /**
     * Return the properly translated human-friendly title of this auth plugin
     *
     * @todo Document this function
     */
    function get_title() {
        return get_string('pluginname', "auth_{$this->authtype}");
    }

    /**
     * Get the auth description (from core or own auth lang files)
     *
     * @return string The description
     */
    function get_description() {
        $authdescription = get_string("auth_{$this->authtype}description", "auth_{$this->authtype}");
        return $authdescription;
    }

    /**
     * Returns whether or not the captcha element is enabled, and the admin settings fulfil its requirements.
     *
     * @abstract Implement in child classes
     * @return bool
     */
    function is_captcha_enabled() {
        return false;
    }

    /**
     * Returns a list of potential IdPs that this authentication plugin supports.
     * This is used to provide links on the login page.
     *
     * @param string $wantsurl the relative url fragment the user wants to get to.  You can use this to compose a returnurl, for example
     *
     * @return array like:
     *              array(
     *                  array(
     *                      'url' => 'http://someurl',
     *                      'icon' => new pix_icon(...),
     *                      'name' => get_string('somename', 'auth_yourplugin'),
     *                 ),
     *             )
     */
    function loginpage_idp_list($wantsurl) {
        return array();
    }

}
