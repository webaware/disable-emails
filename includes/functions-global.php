<?php
// NB: Minimum PHP version for this file is 5.3! No short array notation, no namespaces!

if (!defined('ABSPATH')) {
	exit;
}

/**
* maybe show notice of minimum PHP version failure
*/
function disable_emails_fail_php_version() {
	if (disable_emails_can_show_admin_notices()) {
		disable_emails_load_text_domain();
		include DISABLE_EMAILS_PLUGIN_ROOT . 'views/requires-php.php';
	}
}

/**
* test whether we can show admin-related notices
* @return bool
*/
function disable_emails_can_show_admin_notices() {
	global $pagenow, $hook_suffix;

	// only on specific pages
	if ($pagenow !== 'plugins.php') {
		return false;
	}

	// only bother admins / plugin installers / option setters with this stuff
	if (!current_user_can('activate_plugins') && !current_user_can('manage_options')) {
		return false;
	}

	return true;
}

/**
* load text translations
*/
function disable_emails_load_text_domain() {
	load_plugin_textdomain('disable-emails');
}

/**
* replace link placeholders with an external link
* @param string $template
* @param string $url
* @return string
*/
function disable_emails_external_link($template, $url) {
	$search = array(
		'{{a}}',
		'{{/a}}',
	);
	$replace = array(
		sprintf('<a rel="noopener" target="_blank" href="%s">', esc_url($url)),
		'</a>',
	);
	return str_replace($search, $replace, $template);
}
