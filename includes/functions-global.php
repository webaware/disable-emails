<?php
// NB: Minimum PHP version for this file is 5.3! No short array notation, no namespaces!

if (!defined('ABSPATH')) {
	exit;
}

/**
 * maybe show notice of minimum PHP version failure
 */
function disable_emails_fail_php_version() {
	disable_emails_load_text_domain();

	$requires = new DisableEmailsRequires();

	$requires->addNotice(
		disable_emails_external_link(
			/* translators: %1$s: minimum required version number, %2$s: installed version number */
			sprintf(esc_html__('It requires PHP %1$s or higher; your website has PHP %2$s which is {{a}}old, obsolete, and unsupported{{/a}}.', 'disable-emails'),
				esc_html(DISABLE_EMAILS_MIN_PHP), esc_html(PHP_VERSION)),
			'https://www.php.net/supported-versions.php'
		)
	);
	$requires->addNotice(
		/* translators: %s: minimum recommended version number */
		sprintf(esc_html__('Please upgrade your website hosting. At least PHP %s is recommended.', 'disable-emails'), '7.3')
	);
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
