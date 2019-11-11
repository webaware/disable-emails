<?php

namespace webaware\disable_emails;

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
		add_action('admin_print_styles-' . self::SETTINGS_HOOK_SUFFIX, [$this, 'settingsStyles']);
		add_action('plugin_action_links_' . DISABLE_EMAILS_PLUGIN_NAME, [$this, 'pluginActionLinks']);
		add_action('admin_notices', [$this, 'showWarningAlreadyDefined']);
		add_filter('dashboard_glance_items', [$this, 'dashboardStatus'], 99);
		add_filter('plugin_row_meta', [$this, 'addPluginDetailsLinks'], 10, 2);

		$settings = get_plugin_settings();

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
	* settings admin scripts
	* @param string $hook_suffix
	*/
	public function settingsScripts($hook_suffix) {
		if ($hook_suffix === self::SETTINGS_HOOK_SUFFIX) {
			$min = SCRIPT_DEBUG ? '' : '.min';
			$ver = SCRIPT_DEBUG ? time() : DISABLE_EMAILS_VERSION;

			wp_enqueue_script('disable-emails-settings', plugins_url("js/settings$min.js", DISABLE_EMAILS_PLUGIN_FILE), [], $ver, true);
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
	* settings admin styles
	*/
	public function settingsStyles() {
		require DISABLE_EMAILS_PLUGIN_ROOT . 'views/settings-css.php';
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

		require DISABLE_EMAILS_PLUGIN_ROOT . 'views/settings-form.php';
	}

	/**
	* validate settings on save
	* @param array $input
	* @return array
	*/
	public function settingsValidate($input) {
		$output = [];

		$hooknames = [
			'wp_mail',
			'wp_mail_from',
			'wp_mail_from_name',
			'wp_mail_content_type',
			'wp_mail_charset',
			'phpmailer_init',
			'buddypress',
			'events_manager',
		];
		foreach ($hooknames as $name) {
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
			if (defined('DISABLE_EMAILS_MU_PLUGIN') && is_multisite()) {
				/* translators: shown when emails are disabled for all sites in all networks in a multisite, with the must-use plugin */
				$dash_msg = __('Emails are disabled for all sites.', 'disable-emails');
			}
			elseif (is_plugin_active_for_network(DISABLE_EMAILS_PLUGIN_NAME)) {
				/* translators: shown when emails are disabled for all sites in a multisite network, by network-activating the plugin */
				$dash_msg = __('Emails are disabled on this network.', 'disable-emails');
			}
			else {
				/* translators: shown when emails are disabled for the current site */
				$dash_msg = __('Emails are disabled.', 'disable-emails');
			}
			$glances[] = sprintf('<li style="float:none"><i class="dashicons dashicons-email" aria-hidden="true"></i> %s</li>', esc_html($dash_msg));
		}

		return $glances;
	}

	/**
	* force Events Manager to use wp_mail(), so that we can disable it
	* @param string|bool $return
	* @return string
	*/
	public function forceEventsManagerDisable($return) {
		return 'wp_mail';
	}

	/**
	* cancel Events Manager hook forcing wp_mail() because we're on its settings page
	*/
	public function cancelEventsManagerDisable() {
		remove_filter('pre_option_dbem_rsvp_mail_send_method', [$this, 'forceEventsManagerDisable']);
	}

}
