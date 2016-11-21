<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* class for managing the plugin
*/
class DisableEmailsPlugin {

	public $options;

	protected $wpmailReplaced = false;

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
		$defaults = array (
			'wp_mail'				=> 1,
			'wp_mail_from'			=> 1,
			'wp_mail_from_name'		=> 1,
			'wp_mail_content_type'	=> 1,
			'wp_mail_charset'		=> 1,
			'phpmailer_init'		=> 1,
			'buddypress'			=> 1,
		);

		$this->options = get_option(DISABLE_EMAILS_OPTIONS, $defaults);

		// add hooks
		add_action('init', array($this, 'init'));
		add_action('admin_init', array($this, 'adminInit'));
		add_action('admin_menu', array($this, 'adminMenu'));
		add_action('admin_notices', array($this, 'showWarningAlreadyDefined'));
		add_filter('dashboard_glance_items', array($this, 'dashboardStatus'), 99);
		add_filter('plugin_row_meta', array($this, 'addPluginDetailsLinks'), 10, 2);

		// maybe stop BuddyPress emails too
		if (!empty($options['buddypress'])) {
			add_filter('bp_email_use_wp_mail', '__return_true');
		}
	}

	/**
	* init action
	*/
	public function init() {
		load_plugin_textdomain('disable-emails', false, basename(dirname(DISABLE_EMAILS_PLUGIN_FILE)) . '/languages/');
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
			'buddypress',
		);
		foreach ($hooknames as $name) {
			$output[$name] = empty($input[$name]) ? 0 : 1;
		}

		return $output;
	}

	/**
	* action hook for adding plugin details links
	*/
	public function addPluginDetailsLinks($links, $file) {
		if ($file == DISABLE_EMAILS_PLUGIN_NAME) {
			$links[] = sprintf('<a href="https://wordpress.org/support/plugin/disable-emails" target="_blank">%s</a>', _x('Get help', 'plugin details links', 'disable-emails'));
			$links[] = sprintf('<a href="https://wordpress.org/plugins/disable-emails/" target="_blank">%s</a>', _x('Rating', 'plugin details links', 'disable-emails'));
			$links[] = sprintf('<a href="https://translate.wordpress.org/projects/wp-plugins/disable-emails" target="_blank">%s</a>', _x('Translate', 'plugin details links', 'disable-emails'));
			$links[] = sprintf('<a href="https://shop.webaware.com.au/donations/?donation_for=Disable+Emails" target="_blank">%s</a>', _x('Donate', 'plugin details links', 'disable-emails'));
		}

		return $links;
	}

	/**
	* warn that wp_mail() is defined so emails cannot be disabled!
	*/
	public function showWarningAlreadyDefined() {
		if (!$this->wpmailReplaced) {
			include DISABLE_EMAILS_PLUGIN_ROOT . 'views/warn-already-defined.php';
		}
	}

	/**
	* show on admin dashboard that emails have been disabled
	* @param array $glances
	*/
	public function dashboardStatus($glances) {
		if ($this->wpmailReplaced) {
			$glances[] = sprintf('<li style="float:none"><i class="dashicons dashicons-email" aria-hidden="true"></i> %s</li>', __('Emails are disabled.', 'disable-emails'));
		}

		return $glances;
	}

	/**
	* wp_mail() was successfully replaced, so we can activate disabling emails
	*/
	public static function setActive() {
		include DISABLE_EMAILS_PLUGIN_ROOT . 'includes/class.DisableEmailsPHPMailerMock.php';

		$plugin = self::getInstance();
		$plugin->wpmailReplaced = true;
	}

}
