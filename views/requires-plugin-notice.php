<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<tr class="active paused">
	<th scope='row' class="check-column"></th>
	<td class="paused">
		<i class="dashicons dashicons-warning" aria-hidden="true"></i>
		<?php esc_html_e('This plugin is not fully active.', 'disable-emails'); ?>
	</td>
	<td colspan="<?php echo $wp_list_table->get_column_count() - 2; ?>" class="column-description desc colspanchange">
		<ul style="margin-left: 1em">
			<?php foreach ($notices as $notice): ?>
				<li><?php echo $notice; ?></li>
			<?php endforeach; ?>
		</ul>
	</td>
</tr>
