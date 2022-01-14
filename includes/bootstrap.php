<?php

use webaware\disable_emails\Plugin;
use webaware\disable_emails\PHPMailerMock;

if (!defined('ABSPATH')) {
	exit;
}

require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/functions.php';

// replace standard WordPress wp_mail() if nobody else has already done it
if (!function_exists('wp_mail')) {

	require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/class.EmailAddress.php';
	require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/class.PHPMailerMock.php';

	function wp_mail( $to, $subject, $message, $headers = '', $attachments = [] ) {
		global $phpmailer;

		// create mock PHPMailer object to handle any filter and action hook listeners
		$phpmailer = new PHPMailerMock();
		return $phpmailer->wpmail($to, $subject, $message, $headers, $attachments);
	}

}

/**
 * kick start the plugin
 */
require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/class.Plugin.php';
Plugin::getInstance()->pluginStart();
