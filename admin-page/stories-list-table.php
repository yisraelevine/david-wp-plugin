<?php
function get_results()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'stories';

	$per_page = 50;
	$current_page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT) ?: 1;
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
			echo "<tr>";
			foreach ($result as $key => $value) {
				echo "<td>$value</td>";
			}
			echo "</tr>";
		}
		?>
	</tbody>
</table>