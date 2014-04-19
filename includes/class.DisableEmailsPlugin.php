<?php

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
			'wp_mail' => 1,
			'wp_mail_from' => 1,
			'wp_mail_from_name' => 1,
			'wp_mail_content_type' => 1,
			'wp_mail_charset' => 1,
			'phpmailer_init' => 1,
		);

		$this->options = (array) get_option(DISABLE_EMAILS_OPTIONS);

		if (isset($this->options['callFilterWpMail'])) {
			// upgrade old wp_mail option to one option per hook name, matching old setting
			foreach ($defaults as $key => $value) {
				$this->options[$key] = $this->options['callFilterWpMail'];
			}
			unset($this->options['callFilterWpMail']);
		}

		if (count(array_diff(array_keys($defaults), array_keys($this->options))) > 0) {
			// options not yet saved, or new options added; need to update and save
			$this->options = array_merge($defaults, $this->options);
			unset($this->options[0]);
			update_option(DISABLE_EMAILS_OPTIONS, $this->options);
		}

		// add hooks
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

		$hooknames = array(
			'wp_mail',
			'wp_mail_from',
			'wp_mail_from_name',
			'wp_mail_content_type',
			'wp_mail_charset',
			'phpmailer_init',
		);
		foreach ($hooknames as $name) {
			$output[$name] = empty($input[$name]) ? 0 : 1;
		}

		return $output;
	}

}


