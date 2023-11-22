<?php

namespace webaware\disable_emails;

use DisableEmailsRequires as Requires;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * class for managing the plugin
 */
class Plugin {

	protected $wpmailReplaced = false;

	const SETTINGS_HOOK_SUFFIX = 'settings_page_disable-emails';

	/**
	 * static method for getting the instance of this singleton object
	 * @return self
	 */
	public static function getInstance() {
		static $instance = null;

		if ($instance === null) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * hide constructor
	 */
	private function __construct() {}

	/**
	 * initialise plugin
	 */
	public function pluginStart() {
		$this->wpmailReplaced = class_exists(__NAMESPACE__ . '\\PHPMailerMock', false);

		// add hooks
		add_action('init', 'disable_emails_load_text_domain');
		add_action('admin_init', [$this, 'adminInit']);
		add_action('admin_menu', [$this, 'adminMenu']);
		add_action('admin_enqueue_scripts', [$this, 'settingsScripts']);
		add_action('admin_print_styles-' . self::SETTINGS_HOOK_SUFFIX, [$this, 'adminStyles']);
		add_action('plugin_action_links_' . DISABLE_EMAILS_PLUGIN_NAME, [$this, 'pluginActionLinks']);
		add_filter('dashboard_glance_items', [$this, 'dashboardStatus'], 99);
		add_filter('plugin_row_meta', [$this, 'addPluginDetailsLinks'], 10, 2);

		$settings = get_plugin_settings();

		// maybe add an indicator that emails are disabled
		if ($this->wpmailReplaced) {
			switch (apply_filters('disable_emails_indicator', $settings['indicator'])) {

				case INDICATOR_TOOLBAR:
					add_action('admin_bar_menu', [$this, 'showIndicatorToolbar'], 500);
					add_action('admin_print_styles', [$this, 'adminStyles']);
					break;

				case INDICATOR_NOTICE:
					add_action('admin_notices', [$this, 'showIndicatorNotice']);
					break;

				case INDICATOR_NOTICE_AND_TB:
					add_action('admin_notices', [$this, 'showIndicatorNotice']);
					add_action('admin_bar_menu', [$this, 'showIndicatorToolbar'], 500);
					add_action('admin_print_styles', [$this, 'adminStyles']);
					break;

			}
		}
		else {
			$this->showWarningAlreadyDefined();
		}

		// maybe stop BuddyPress emails too
		if (!empty($settings['buddypress'])) {
			add_filter('bp_email_use_wp_mail', '__return_true');
		}

		// maybe stop Events Manager emails too
		if (!empty($settings['events_manager'])) {
			add_filter('pre_option_dbem_rsvp_mail_send_method', [$this, 'forceEventsManagerDisable']);
			add_action('load-event_page_events-manager-options', [$this, 'cancelEventsManagerDisable']);
		}
	}

	/**
	 * admin_init action
	 */
	public function adminInit() {
		add_settings_section(OPT_SETTINGS, false, false, OPT_SETTINGS);
		register_setting(OPT_SETTINGS, OPT_SETTINGS, [$this, 'settingsValidate']);
	}

	/**
	 * admin menu items
	 */
	public function adminMenu() {
		add_options_page('Disable Emails', 'Disable Emails', 'manage_options', 'disable-emails', [$this, 'settingsPage']);
	}

	/**
	 * enqueue styles for admin
	 */
	public function adminStyles() {
		$ver = SCRIPT_DEBUG ? time() : DISABLE_EMAILS_VERSION;
		wp_enqueue_style('disable-emails', plugins_url('static/css/admin.css', DISABLE_EMAILS_PLUGIN_FILE), [], $ver);
	}

	/**
	 * settings admin scripts
	 * @param string $hook_suffix
	 */
	public function settingsScripts($hook_suffix) {
		if ($hook_suffix === self::SETTINGS_HOOK_SUFFIX) {
			$min = SCRIPT_DEBUG ? '' : '.min';
			$ver = SCRIPT_DEBUG ? time() : DISABLE_EMAILS_VERSION;

			wp_enqueue_script('disable-emails-settings', plugins_url("static/js/settings$min.js", DISABLE_EMAILS_PLUGIN_FILE), [], $ver, true);
			wp_localize_script('disable-emails-settings', 'disable_emails_settings', [
				'mu_url'		=> wp_nonce_url(admin_url('options-general.php?page=disable-emails'), 'disable-emails-mu'),
				'msg'			=> [
					'mu_activate'		=> _x('Activate the must-use plugin?', 'settings', 'disable-emails'),
					'mu_deactivate'		=> _x('Deactivate the must-use plugin?', 'settings', 'disable-emails'),
				],
			]);
		}
	}

	/**
	 * settings admin
	 */
	public function settingsPage() {
		$settings = get_plugin_settings();
		$has_mu_plugin = defined('DISABLE_EMAILS_MU_PLUGIN');

		// check for enable/disable mu-plugin
		if (isset($_GET['action'])) {
			check_admin_referer('disable-emails-mu');
			$has_mu_plugin = mu_plugin_manage($_GET['action']);
		}

		$indicators = [
			INDICATOR_TOOLBAR		=> _x('Toolbar indicator', 'admin indicator setting', 'disable-emails'),
			INDICATOR_NOTICE		=> _x('notice on all admin pages', 'admin indicator setting', 'disable-emails'),
			INDICATOR_NOTICE_AND_TB	=> _x('notice and Toolbar indicator', 'admin indicator setting', 'disable-emails'),
			INDICATOR_NONE			=> _x('no indicator', 'admin indicator setting', 'disable-emails'),
		];

		require DISABLE_EMAILS_PLUGIN_ROOT . 'views/settings-form.php';
	}

	/**
	 * validate settings on save
	 * @param array $input
	 * @return array
	 */
	public function settingsValidate($input) {
		$output = [];

		$output['indicator'] = isset($input['indicator']) ? $input['indicator'] : INDICATOR_TOOLBAR;
		if (!in_array($output['indicator'], [INDICATOR_NONE, INDICATOR_TOOLBAR, INDICATOR_NOTICE, INDICATOR_NOTICE_AND_TB])) {
			add_settings_error(OPT_SETTINGS, 'indicator', _x('Indicator is invalid', 'settings error', 'disable-emails'));
		}

		$checkboxes = [
			'wp_mail',
			'wp_mail_from',
			'wp_mail_from_name',
			'wp_mail_content_type',
			'wp_mail_charset',
			'phpmailer_init',
			'buddypress',
			'events_manager',
		];
		foreach ($checkboxes as $name) {
			$output[$name] = empty($input[$name]) ? 0 : 1;
		}

		return $output;
	}

	/**
	 * add plugin action links on plugins page
	 * @param array $links
	 * @return array
	 */
	public function pluginActionLinks($links) {
		if (current_user_can('manage_options')) {
			// add settings link
			$url = admin_url('options-general.php?page=disable-emails');
			$settings_link = sprintf('<a href="%s">%s</a>', esc_url($url), esc_html_x('Settings', 'plugin details links', 'disable-emails'));
			array_unshift($links, $settings_link);
		}

		return $links;
	}

	/**
	 * action hook for adding plugin details links
	 */
	public function addPluginDetailsLinks($links, $file) {
		if ($file === DISABLE_EMAILS_PLUGIN_NAME) {
			$links[] = sprintf('<a href="https://wordpress.org/support/plugin/disable-emails" target="_blank" rel="noopener">%s</a>', _x('Get help', 'plugin details links', 'disable-emails'));
			$links[] = sprintf('<a href="https://wordpress.org/plugins/disable-emails/" target="_blank" rel="noopener">%s</a>', _x('Rating', 'plugin details links', 'disable-emails'));
			$links[] = sprintf('<a href="https://translate.wordpress.org/projects/wp-plugins/disable-emails" target="_blank" rel="noopener">%s</a>', _x('Translate', 'plugin details links', 'disable-emails'));
			$links[] = sprintf('<a href="https://shop.webaware.com.au/donations/?donation_for=Disable+Emails" target="_blank" rel="noopener">%s</a>', _x('Donate', 'plugin details links', 'disable-emails'));
		}

		return $links;
	}

	/**
	 * warn that wp_mail() is defined so emails cannot be disabled!
	 */
	public function showWarningAlreadyDefined() {
		$requires = new Requires();

		$requires->addNotice(
			esc_html__('Emails are not disabled! Something else has already declared wp_mail(), so Disable Emails cannot stop emails being sent!', 'disable-emails')
		);

		if (!defined('DISABLE_EMAILS_MU_PLUGIN')) {
			$requires->addNotice(
				esc_html__('Try enabling the must-use plugin from the settings page.', 'disable-emails')
			);
		}
	}

	/**
	 * admin notice for indicator of disabled emails status
	 */
	public function showIndicatorNotice() {
		if (current_user_can('activate_plugins') && current_user_can('manage_options')) {
			include DISABLE_EMAILS_PLUGIN_ROOT . 'views/indicator-notice.php';
		}
	}

	/**
	 * Toolbar indicator of disabled emails status
	 * @param WP_Admin_Bar $admin_bar
	 */
	public function showIndicatorToolbar($admin_bar) {
		if (current_user_can('activate_plugins') && current_user_can('manage_options')) {
			$admin_bar->add_node([
				'id'		=> 'disable-emails-indicator',
				'title'		=> sprintf('<span class="ab-icon"></span><span class="screen-reader-text">%s</span>', __('Disable Emails', 'disable-emails')),
				'href'		=> admin_url('options-general.php?page=disable-emails'),
				'meta'		=> [
					'title' => get_status_message(),
				],
			]);
		}
	}

	/**
	 * show on admin dashboard that emails have been disabled
	 * @param array $glances
	 */
	public function dashboardStatus($glances) {
		if ($this->wpmailReplaced) {
			$dash_msg = get_status_message();
			$glances[] = sprintf('<li style="float:none"><i class="dashicons dashicons-email" aria-hidden="true"></i> %s</li>', esc_html($dash_msg));
		}

		return $glances;
	}

	/**
	 * force Events Manager to use wp_mail(), so that we can disable it
	 * @param string|bool $return
	 * @return string
	 */
	public function forceEventsManagerDisable($return_value) {
		return 'wp_mail';
	}

	/**
	 * cancel Events Manager hook forcing wp_mail() because we're on its settings page
	 */
	public function cancelEventsManagerDisable() {
		remove_filter('pre_option_dbem_rsvp_mail_send_method', [$this, 'forceEventsManagerDisable']);
	}

}
