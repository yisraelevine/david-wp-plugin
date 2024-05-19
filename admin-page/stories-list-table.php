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
<table>
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
			$name = $results['name'];
			$url = $results['url'];
			$is_new = $results['is_new'];
			$is_phone = $results['is_phone'];
			echo "<tr>
				<td>$name</td>
				<td>$url</td>
				<td>$is_new</td>
				<td>$is_phone</td>
				</tr>";
		}
		?>
	</tbody>
</table>