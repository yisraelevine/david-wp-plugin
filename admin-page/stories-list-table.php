<?php
function get_results()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'stories';

	$per_page = 50;
	$current_page = filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT) ?: 1;
	$query = "SELECT * FROM $table_name LIMIT " . (($current_page - 1) * $per_page) . ", $per_page";
	$results = $wpdb->get_results($query, ARRAY_A);
	return $results;
}
function render_columns()
{
	?>
	<tr>
		<th>שם</th>
		<th>קישור</th>
		<th>חדש</th>
		<th>טלפון</th>
	</tr>
	<?php
}

?>

<h2>רשימת סיפורים</h2>
<table class="stories-list-table">
	<thead>
		<?php echo render_columns(); ?>
	</thead>
	<tbody>
		<?php
		$results = get_results();
		foreach ($results as $result) {
			?>
			<tr>
				<td>
					<input type="text" value="<?php echo $result['name']; ?>" />
				</td>
				<td>
					<input type="url" value="<?php echo $result['url']; ?>" />
				</td>
				<td>
					<input type="checkbox" <?php echo ($result['is_new'] ? 'checked' : ''); ?> />
				</td>
				<td>
					<input type="checkbox" <?php echo ($result['is_phone'] ? 'checked' : ''); ?> />
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
	<tfoot>
		<?php echo render_columns(); ?>=
	</tfoot>
</table>