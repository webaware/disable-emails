<?php

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
		add_action('admin_notices', array($this, 'showWarningAlreadyDefined'));
		add_filter('plugin_row_meta', array($this, 'addPluginDetailsLinks'), 10, 2);
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
			$links[] = '<a href="http://wordpress.org/support/plugin/disable-emails">' . __('Get help', 'disable-emails') . '</a>';
			$links[] = '<a href="http://wordpress.org/plugins/disable-emails/">' . __('Rating', 'disable-emails') . '</a>';
			$links[] = '<a href="http://translate.webaware.com.au/projects/disable-emails">' . _x('Translate', 'translate from English', 'disable-emails') . '</a>';
			$links[] = '<a href="http://shop.webaware.com.au/downloads/disable-emails/">' . __('Donate', 'disable-emails') . '</a>';
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
	* wp_mail() was successfully replaced, so we can activate disabling emails
	*/
	public static function setActive() {
		include DISABLE_EMAILS_PLUGIN_ROOT . 'includes/class.DisableEmailsPHPMailerMock.php';

		$plugin = self::getInstance();
		$plugin->wpmailReplaced = true;
	}

}
