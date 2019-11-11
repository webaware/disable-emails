<?php

namespace webaware\disable_emails;

use \Exception;

if (!defined('ABSPATH')) {
	exit;
}

const OPT_SETTINGS				= 'disable_emails';

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
	];

	return get_option(OPT_SETTINGS, $defaults);
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

	$source = wp_normalize_path(DISABLE_EMAILS_PLUGIN_ROOT . '/mu-plugin/disable-emails-mu.php');
	$target = wp_normalize_path(WPMU_PLUGIN_DIR . '/disable-emails-mu.php');

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
