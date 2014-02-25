<?php
// settings form

global $wp_version;
?>

<div class="wrap">
	<?php if (version_compare($wp_version, '3.8', '<')) screen_icon('options-general'); ?>
	<h2><?php _e('Disable Emails settings', 'disable-emails'); ?></h2>

	<form action="<?php echo admin_url('options.php'); ?>" method="POST">
		<?php settings_fields(DISABLE_EMAILS_OPTIONS); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row"><?php _e('Call filter wp_mail', 'disable-emails'); ?></th>
				<td>
					<label>
						<input type="checkbox" name="disable_emails[callFilterWpMail]" value="1" <?php checked($options['callFilterWpMail']); ?> />
						<em><?php _e('call the wp_mail filter so that listeners can act, e.g. log emails', 'disable-emails'); ?></em>
					</label>
				</td>
			</tr>

		</table>

		<?php submit_button(); ?>
	</form>
</div>
