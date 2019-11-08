<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="notice notice-error">
	<p>
		<?php echo disable_emails_external_link(
				sprintf(esc_html__('Disable Emails requires PHP %1$s or higher; your website has PHP %2$s which is {{a}}old, obsolete, and unsupported{{/a}}.', 'disable-emails'),
					esc_html(DISABLE_EMAILS_MIN_PHP), esc_html(PHP_VERSION)),
				'https://www.php.net/supported-versions.php'
			); ?>
	</p>
	<p><?php printf(esc_html__('Please upgrade your website hosting. At least PHP %s is recommended.', 'disable-emails'), '7.3'); ?></p>
</div>
