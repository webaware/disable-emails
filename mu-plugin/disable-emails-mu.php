<?php
/*
Plugin Name: Disable Emails Must Use
Plugin URI: https://shop.webaware.com.au/downloads/disable-emails/
Description: make the Disable Emails plugin must-use
Version: 1.0.0
Author: WebAware
Author URI: https://shop.webaware.com.au/
*/

if (!defined('WP_PLUGIN_DIR') || !is_dir(WP_PLUGIN_DIR)) {
	exit;
}

$disable_emails_plugin = wp_normalize_path(WP_PLUGIN_DIR . '/disable-emails/disable-emails.php');

if (is_readable($disable_emails_plugin)) {
	define('DISABLE_EMAILS_MU_PLUGIN', '1.0.0');
	include_once $disable_emails_plugin;
}
