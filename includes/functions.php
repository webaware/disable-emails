<?php

namespace webaware\disable_emails;

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
