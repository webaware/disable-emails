<?php
// settings form

use const		webaware\disable_emails\OPT_SETTINGS;
use function	webaware\disable_emails\has_mu_plugin_permission;

if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="wrap">
	<h1><?php esc_html_e('Disable Emails settings', 'disable-emails'); ?></h1>

	<form action="<?= esc_url(admin_url('options.php')); ?>" method="POST">
		<?php settings_fields(OPT_SETTINGS); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row"><label for="disable_emails_indicator"><?php esc_html_e('Indicator', 'disable-emails'); ?></label></th>
				<td>
					<select name="disable_emails[indicator]" id="disable_emails_indicator">
					<?php foreach($indicators as $value => $label): ?>
						<option value="<?= esc_attr($value); ?>"<?php selected($value, $settings['indicator']); ?>><?= esc_html($label); ?></option>
					<?php endforeach; ?>
					</select>
					<p><em><?php esc_html_e('Select how you would like to indicate in the WordPress admin that emails are disabled.', 'disable-emails'); ?></em></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Call WordPress hooks', 'disable-emails'); ?></th>
				<td>
					<label><input type="checkbox" name="disable_emails[wp_mail]" value="1" <?php checked($settings['wp_mail']); ?> /> wp_mail</label>
					<br /><label><input type="checkbox" name="disable_emails[wp_mail_from]" value="1" <?php checked($settings['wp_mail_from']); ?> /> wp_mail_from</label>
					<br /><label><input type="checkbox" name="disable_emails[wp_mail_from_name]" value="1" <?php checked($settings['wp_mail_from_name']); ?> /> wp_mail_from_name</label>
					<br /><label><input type="checkbox" name="disable_emails[wp_mail_content_type]" value="1" <?php checked($settings['wp_mail_content_type']); ?> /> wp_mail_content_type</label>
					<br /><label><input type="checkbox" name="disable_emails[wp_mail_charset]" value="1" <?php checked($settings['wp_mail_charset']); ?> /> wp_mail_charset</label>
					<br /><label><input type="checkbox" name="disable_emails[phpmailer_init]" value="1" <?php checked($settings['phpmailer_init']); ?> /> phpmailer_init</label>
					<p><em><?php esc_html_e('call WordPress hooks so that listeners can act, e.g. log emails', 'disable-emails'); ?></em></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e('BuddyPress', 'disable-emails'); ?></th>
				<td>
					<input type="checkbox" name="disable_emails[buddypress]" id="disable_emails_buddypress" value="1" <?php checked(!empty($settings['buddypress'])); ?> />
					<label for="disable_emails_buddypress"><?php esc_html_e('force BuddyPress to use WordPress emails so that they can be blocked', 'disable-emails'); ?></label>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Events Manager', 'disable-emails'); ?></th>
				<td>
					<input type="checkbox" name="disable_emails[events_manager]" id="disable_emails_events_manager" value="1" <?php checked(!empty($settings['events_manager'])); ?> />
					<label for="disable_emails_events_manager"><?php esc_html_e('force Events Manager to use WordPress emails so that they can be blocked', 'disable-emails'); ?></label>
				</td>
			</tr>

		</table>

		<?php submit_button(); ?>

		<?php if (has_mu_plugin_permission()): ?>

			<h2><?= esc_html_x('Must-use plugin', 'settings', 'disable-emails'); ?></h2>

			<p><?= esc_html__('When enabled as a must-use plugin, also known as mu-plugin, Disable Emails is always activated. This is recommended for development websites, in which plugins might be deactivated when refreshing the database from a source website. It can also help when another plugin has already declared wp_mail(), preventing Disable Emails from functioning correctly.', 'disable-emails'); ?>

			<div class="disable-emails-mu-buttons">
				<?php if ($has_mu_plugin): ?>

					<p><?= esc_html__('The must-use plugin is currently enabled.', 'disable-emails'); ?></p>

					<?php if (is_multisite() && !is_plugin_active_for_network(DISABLE_EMAILS_PLUGIN_NAME)): ?>
						<p><strong><?= esc_html__('This website is in a multisite network. Disabling the must-use plugin will enable emails for all sites that have not activated the plugin separately.', 'disable-emails'); ?></strong></p>
					<?php endif; ?>

					<button type="button" class="button button-secondary" id="disable-emails-mu-disable"><?= esc_html__('Deactivate must-use plugin', 'disable-emails'); ?></button>

				<?php else: ?>

					<p><?= esc_html__('The must-use plugin is currently disabled.', 'disable-emails'); ?></p>

					<?php if (is_multisite()): ?>
						<p><strong><?= esc_html__('This website is in a multisite network. Enabling the must-use plugin will disable emails for all sites.', 'disable-emails'); ?></strong></p>
					<?php endif; ?>

					<button type="button" class="button button-secondary" id="disable-emails-mu-enable"><?= esc_html__('Activate must-use plugin', 'disable-emails'); ?></button>

				<?php endif; ?>
			</div>

			<noscript>
				<p>
					<?= disable_emails_external_link(
							esc_html__('To activate or deactivate the must-use plugin, please {{a}}enable JavaScript in your browser{{/a}}.', 'disable-emails'),
							'https://enable-javascript.com/'
						); ?>
				</p>
			</noscript>

		<?php endif; ?>

	</form>
</div>
