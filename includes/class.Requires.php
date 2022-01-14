<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * collect and display admin notices for failed prerequisites
 */
class DisableEmailsRequires {

	private $notices;

	/**
	 * set up the notices container, and hook actions for displaying notices
	 * only called when a notice is being added
	 */
	protected function init() {
		if (!is_array($this->notices)) {
			$this->notices = array();

			// hook admin_notices, again! so need to hook later than 10
			add_action('admin_notices', array($this, 'maybeShowAdminNotices'), 20);

			// show Requires notices before update information, so hook earlier than 10
			add_action('after_plugin_row_' . DISABLE_EMAILS_PLUGIN_NAME, array($this, 'showPluginRowNotices'), 9, 2);
		}
	}

	/**
	 * add a Requires notices
	 * @param string $notice
	 */
	public function addNotice($notice) {
		$this->init();
		$this->notices[] = $notice;
	}

	/**
	 * maybe show admin notices, if on an appropriate admin page with admin or similar logged in
	 */
	public function maybeShowAdminNotices() {
		if ($this->canShowAdminNotices()) {
			$notices = $this->notices;
			require DISABLE_EMAILS_PLUGIN_ROOT . 'views/requires-admin-notice.php';
		}
	}

	/**
	 * show plugin page row with requires notices
	 */
	public function showPluginRowNotices() {
		global $wp_list_table;

		if (empty($wp_list_table)) {
			return;
		}

		$notices = $this->notices;
		require DISABLE_EMAILS_PLUGIN_ROOT . 'views/requires-plugin-notice.php';
	}

	/**
	 * test whether we can show admin-related notices
	 * @return bool
	 */
	protected function canShowAdminNotices() {
		$current_screen = get_current_screen();

		// only on specific pages
		if (!$current_screen || $current_screen->id !== 'dashboard') {
			return false;
		}

		// only bother admins / plugin installers / option setters with this stuff
		if (!current_user_can('activate_plugins') && !current_user_can('manage_options')) {
			return false;
		}

		return true;
	}

}
