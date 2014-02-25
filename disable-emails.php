<?php
/*
Plugin Name: Disable Emails
Description: Stop WordPress from sending any emails. ANY!
Version: 1.1.0
Author: WebAware
Author URI: http://www.webaware.com.au/
*/

/*
copyright (c) 2014 WebAware Pty Ltd (email : rmckay@webaware.com.au)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('DISABLE_EMAILS_PLUGIN_ROOT')) {
	define('DISABLE_EMAILS_PLUGIN_ROOT', dirname(__FILE__) . '/');
	define('DISABLE_EMAILS_PLUGIN_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
	define('DISABLE_EMAILS_PLUGIN_FILE', __FILE__);

	// options
	define('DISABLE_EMAILS_OPTIONS', 'disable_emails');
}

// replace standard WordPress wp_mail() if nobody else has already done it
if (!function_exists('wp_mail')) {

	function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
		$plugin = DisableEmailsPlugin::getInstance();

		// keep hookers happy -- e.g. some may rely on this filter hook for email logging
		if ($plugin->options['callFilterWpMail']) {
			apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );
		}

		// pretend all was well
		return true;
	}

}

/**
* class for managing the plugin
*/
class DisableEmailsPlugin {

	public $options;

	/**
	* static method for getting the instance of this singleton object
	* @return self
	*/
	public static function getInstance() {
		static $instance = null;

		if (is_null($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	* hook into WordPress
	*/
	private function __construct() {
		static $defaults = array (
			'callFilterWpMail' => 1,
		);

		$this->options = (array) get_option(DISABLE_EMAILS_OPTIONS);

		if (count(array_diff(array_keys($defaults), array_keys($this->options))) > 0) {
			$this->options = array_merge($defaults, $this->options);
			unset($this->options[0]);
			update_option(DISABLE_EMAILS_OPTIONS, $this->options);
		}

		add_action('init', array($this, 'init'));
		add_action('admin_init', array($this, 'adminInit'));
		add_action('admin_menu', array($this, 'adminMenu'));
	}

	/**
	* init action
	*/
	public function init() {
		load_plugin_textdomain('disable-emails', false, basename(dirname(__FILE__)) . '/languages/');
	}

	/**
	* admin_init action
	*/
	public function adminInit() {
		add_settings_section(DISABLE_EMAILS_OPTIONS, false, false, DISABLE_EMAILS_OPTIONS);
		register_setting(DISABLE_EMAILS_OPTIONS, DISABLE_EMAILS_OPTIONS, array($this, 'settingsValidate'));
	}

	/**
	* admin menu items
	*/
	public function adminMenu() {
		add_options_page('Disable Emails', 'Disable Emails', 'manage_options', 'disable-emails', array($this, 'settingsPage'));
	}

	/**
	* settings admin
	*/
	public function settingsPage() {
		$options = $this->options;
		require DISABLE_EMAILS_PLUGIN_ROOT . 'views/settings-form.php';
	}

	/**
	* validate settings on save
	* @param array $input
	* @return array
	*/
	public function settingsValidate($input) {
		$output = array();
		$output['callFilterWpMail'] = empty($input['callFilterWpMail']) ? 0 : 1;

		return $output;
	}

}

DisableEmailsPlugin::getInstance();
