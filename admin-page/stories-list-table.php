<?php
$per_page = 50;
$current_page = filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT) ?: 1;
$limit = ($current_page - 1) * $per_page;
echo "---$limit---";
$limit = 0;
function get_results()
{
	global $wpdb, $per_page, $limit;
	$table = $wpdb->prefix . 'stories';

	$query = "SELECT * FROM $table LIMIT $limit, $per_page";
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
function render_rows()
{
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
}
function render_pagination()
{
	global $wpdb, $per_page, $limit;
	$table = $wpdb->prefix . 'stories';

	$query = "SELECT COUNT(*) FROM $table";
	$var = $wpdb->get_var($query);
	$till = $limit + $per_page;

	return "$limit עד $till מתוך $var";
}

?>

<h2>רשימת סיפורים</h2>
<?php echo render_pagination(); ?>
<table class="stories-list-table">
	<thead>
		<?php echo render_columns(); ?>
	</thead>
	<tbody>
		<?php echo render_rows(); ?>
	</tbody>
	<tfoot>
		<?php echo render_columns(); ?>
	</tfoot>
</table>
<?php echo render_pagination(); ?>
