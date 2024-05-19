<?php
function get_results()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'stories';

	$per_page = 50;
	$current_page = 1;
	$query = "SELECT * FROM $table_name LIMIT " . (($current_page - 1) * $per_page) . ", $per_page";
	echo $query;
	$results = $wpdb->get_results($query, ARRAY_A);
	return $results;
}

?>

<h2>רשימת סיפורים</h2>
<table class="stories-list-table">
	<thead>
		<tr>
			<th>שם</th>
			<th>קישור</th>
			<th>חדש</th>
			<th>טלפון</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$results = get_results();
		foreach ($results as $result) {
			$name = $result['name'];
			$url = $result['url'];
			$is_new = $result['is_new'];
			$is_phone = $result['is_phone'];
			?>
			<tr>
				<td><?php echo $name ?></td>
				<td><?php echo $url ?></td>
				<td>
					<input type="checkbox" <?php echo ($is_new ? 'selected' : ''); ?> />
				</td>
				<td>
					<input type="checkbox" <?php echo ($is_phone ? 'selected' : ''); ?> />
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>