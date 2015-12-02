<?php
// settings form

if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="wrap">
	<h2><?php esc_html_e('Disable Emails settings', 'disable-emails'); ?></h2>

	<form action="<?php echo admin_url('options.php'); ?>" method="POST">
		<?php settings_fields(DISABLE_EMAILS_OPTIONS); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Call WordPress hooks', 'disable-emails'); ?></th>
				<td>
					<em><?php esc_html_e('call WordPress hooks so that listeners can act, e.g. log emails', 'disable-emails'); ?></em>
					<br /><label><input type="checkbox" name="disable_emails[wp_mail]" value="1" <?php checked($options['wp_mail']); ?> /> wp_mail</label>
					<br /><label><input type="checkbox" name="disable_emails[wp_mail_from]" value="1" <?php checked($options['wp_mail_from']); ?> /> wp_mail_from</label>
					<br /><label><input type="checkbox" name="disable_emails[wp_mail_from_name]" value="1" <?php checked($options['wp_mail_from_name']); ?> /> wp_mail_from_name</label>
					<br /><label><input type="checkbox" name="disable_emails[wp_mail_content_type]" value="1" <?php checked($options['wp_mail_content_type']); ?> /> wp_mail_content_type</label>
					<br /><label><input type="checkbox" name="disable_emails[wp_mail_charset]" value="1" <?php checked($options['wp_mail_charset']); ?> /> wp_mail_charset</label>
					<br /><label><input type="checkbox" name="disable_emails[phpmailer_init]" value="1" <?php checked($options['phpmailer_init']); ?> /> phpmailer_init</label>
				</td>
			</tr>

		</table>

		<?php submit_button(); ?>
	</form>
</div>
