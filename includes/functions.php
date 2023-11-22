<?php

namespace webaware\disable_emails;

use Exception;

if (!defined('ABSPATH')) {
	exit;
}

const OPT_SETTINGS				= 'disable_emails';

const INDICATOR_NONE			= 'none';
const INDICATOR_TOOLBAR			= 'toolbar';
const INDICATOR_NOTICE			= 'notice';
const INDICATOR_NOTICE_AND_TB	= 'notice_toolbar';

/**
 * get current plugin settings, use defaults if settings not yet saved
 * @return array
 */
function get_plugin_settings() {
	$defaults = [
		'wp_mail'				=> 1,
		'wp_mail_from'			=> 1,
		'wp_mail_from_name'		=> 1,
		'wp_mail_content_type'	=> 1,
		'wp_mail_charset'		=> 1,
		'phpmailer_init'		=> 1,
		'buddypress'			=> 1,
		'events_manager'		=> 1,
		'indicator'				=> INDICATOR_TOOLBAR,
	];

	return wp_parse_args(get_option(OPT_SETTINGS, []), $defaults);
}

/**
 * can current user activate/deactivate the must-use plugin?
 * @return bool
 */
function has_mu_plugin_permission() {
	return current_user_can(is_multisite() ? 'manage_network_plugins' : 'activate_plugins');
}

/**
 * install, update, or remove the must-use plugin
 * @param string $action
 * @return bool
 * @throws Exception
 */
function mu_plugin_manage($action) {
	if (!has_mu_plugin_permission()) {
		throw new Exception(__('No permission to manage Disable Emails must-use plugin.', 'disable-emails'));
	}

	$has_mu_plugin = defined('DISABLE_EMAILS_MU_PLUGIN');

	$wpmu_plugin_dir = rtrim(wp_normalize_path(WPMU_PLUGIN_DIR), '/');
	if (!is_dir($wpmu_plugin_dir)) {
		// folder does not exist, create it now
		wp_mkdir_p($wpmu_plugin_dir);
		if (!is_dir($wpmu_plugin_dir)) {
			throw new Exception(__('Unable to create folder for Disable Emails must-use plugin.', 'disable-emails'));
		}
	}

	$source = wp_normalize_path(DISABLE_EMAILS_PLUGIN_ROOT . 'mu-plugin/disable-emails-mu.php');
	$target = "$wpmu_plugin_dir/disable-emails-mu.php";

	switch ($action) {

		case 'activate':
		case 'update':
			if (!copy($source, $target)) {
				throw new Exception(__('Unable to install Disable Emails must-use plugin.', 'disable-emails'));
			}
			$has_mu_plugin = true;
			break;

		case 'deactivate':
			if (!unlink($target)) {
				throw new Exception(__('Unable to uninstall Disable Emails must-use plugin.', 'disable-emails'));
			}
			$has_mu_plugin = false;
			break;

		default:
			throw new Exception(__('Invalid action for Disable Emails must-use plugin.', 'disable-emails'));

	}

	return $has_mu_plugin;
}

/**
 * get message for current active status
 * @return string
 */
function get_status_message() {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';

	if (defined('DISABLE_EMAILS_MU_PLUGIN') && is_multisite()) {
		/* translators: shown when emails are disabled for all sites in all networks in a multisite, with the must-use plugin */
		$msg = __('Emails are disabled for all sites.', 'disable-emails');
	}
	elseif (is_plugin_active_for_network(DISABLE_EMAILS_PLUGIN_NAME)) {
		/* translators: shown when emails are disabled for all sites in a multisite network, by network-activating the plugin */
		$msg = __('Emails are disabled on this network.', 'disable-emails');
	}
	else {
		/* translators: shown when emails are disabled for the current site */
		$msg = __('Emails are disabled.', 'disable-emails');
	}

	return $msg;
}
