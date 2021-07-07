<?php

use webaware\disable_emails\Plugin;
use webaware\disable_emails\PHPMailerMock;

if (!defined('ABSPATH')) {
	exit;
}

require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/functions.php';

// replace standard WordPress wp_mail() if nobody else has already done it
if (!function_exists('wp_mail')) {

	require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/class.PHPMailerMock.php';

	function wp_mail( $to, $subject, $message, $headers = '', $attachments = [] ) {
		global $phpmailer;

		// create mock PHPMailer object to handle any filter and action hook listeners
		$phpmailer = new PHPMailerMock();
		return $phpmailer->wpmail($to, $subject, $message, $headers, $attachments);
	}

} else {
	// Disable whatever plugin has added its own wp_mail function.

	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	/**
	 * Given a filename, figure out what plugin it is from.
	 *
	 * @param string $filename The file path we're trying to determine the plugin for.
	 *
	 * @return string|null The plugin file name, e.g. "disable-emails/disable-emails.php".
	 */
	 $get_plugin_file_from_path = function ( $filename ) {

		// If the file is outside the plugins dir, whats's up? MU plugins?
		if ( ! stristr( $filename, WP_PLUGIN_DIR ) ) {
			return null;
		}

		// Remove the path/to/wp-content/plugins.
		$plugin_file = trim( substr( $filename, strlen( realpath( WP_PLUGIN_DIR ) ) ), DIRECTORY_SEPARATOR );

		$plugins = get_plugins();

		// This will only work if wp_mail is defined in the plugin's main file.
		if ( array_key_exists( $plugin_file, $plugins ) ) {

			return $plugin_file;
		}

		// Get the first part of the file path to the first /.
		$plugin_slug = substr( $plugin_file, 0, strpos( $plugin_file, DIRECTORY_SEPARATOR ) );

		// If the file is in the plugin's folder.
		foreach ( $plugins as $plugin_file_name => $plugin ) {

			if ( stristr( $plugin_file_name, $plugin_slug ) ) {
				return $plugin_file_name;
			}
		}

		return null;
	};

	$wp_mail_reflector = new \ReflectionFunction( 'wp_mail' );
	$wp_mail_filename  = $wp_mail_reflector->getFileName();

	$built_in_wp_mail_filename = 'wp-includes/pluggable.php';

	// If wp_mail has been overridden.
	if ( substr( $wp_mail_filename, - 1 * strlen( $built_in_wp_mail_filename ) ) !== $built_in_wp_mail_filename ) {

		$plugin_file = $get_plugin_file_from_path( $wp_mail_filename );

		if ( null !== $plugin_file ) {

			deactivate_plugins( $plugin_file );

		}
	}

}

/**
* kick start the plugin
*/
require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/class.Plugin.php';
Plugin::getInstance()->pluginStart();
