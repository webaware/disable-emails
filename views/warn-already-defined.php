<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="notice notice-error">
	<p><?php esc_html_e('Emails are not disabled! Something else has already declared wp_mail(), so Disable Emails cannot stop emails being sent!', 'disable-emails'); ?></p>
	<?php if (!defined('DISABLE_EMAILS_MU_PLUGIN')): ?>
		<p><?php esc_html_e('Try enabling the must-use plugin from the settings page.', 'disable-emails'); ?></p>
	<?php endif; ?>
</div>
