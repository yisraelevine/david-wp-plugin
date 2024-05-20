<?php

class StoriesListTable
{
	public $columns;
	public $rows;
	public $pagination;
	public function __construct()
	{
		global $wpdb;
		$per_page = 50;
		$table = $wpdb->prefix . 'stories';
		$current_page = filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT) ?: 1;
		$limit = ($current_page - 1) * $per_page;
		$count = $this->get_count($table);
		$results = $this->get_results($table, $limit, $per_page);

		$this->columns = $this->render_columns();
		$this->rows = $this->render_rows($results);
		$this->pagination = $this->render_pagination($limit, $per_page, $count);
	}
	private function get_count($table)
	{
		global $wpdb;
		$query = $wpdb->prepare('SELECT COUNT(*) FROM %i', $table);
		$var = $wpdb->get_var($query);
		return $var;
	}
	private function get_results($table, $limit, $per_page)
	{
		global $wpdb;
		$query = $wpdb->prepare('SELECT * FROM %i LIMIT %d, %d', $table, $limit, $per_page);
		$results = $wpdb->get_results($query, ARRAY_A);
		return $results;
	}
	private function render_columns()
	{
		return <<<END
		<tr>
			<th>שם</th>
			<th>קישור</th>
			<th>חדש</th>
			<th>טלפון</th>
		</tr>
		END;
	}
	private function render_rows($results)
	{
		$html = '';
		foreach ($results as $result) {
			$name = $result['name'];
			$url = $result['url'];
			$is_new = $result['is_new'] ? 'checked' : '';
			$is_phone = $result['is_phone'] ? 'checked' : '';

			$html .= <<<END
			<tr>
				<td>
					<input type="text" value="$name" />
				</td>
				<td>
					<input type="url" value="$url" />
				</td>
				<td>
					<input type="checkbox" $is_new />
				</td>
				<td>
					<input type="checkbox" $is_phone />
				</td>
			</tr>
			END;
		}
		return $html;
	}
	private function render_pagination($limit, $per_page, $count)
	{
		$start = $limit + 1;
		$end = min(($limit + $per_page), $count);
		return sprintf('%d עד %d מתוך %d', $start, $end, $count);
	}
}
$Stories = new StoriesListTable();
?>

<h2>רשימת סיפורים</h2>
<?php echo $Stories->pagination; ?>
<table class="stories-list-table">
	<thead>
		<?php echo $Stories->columns; ?>
	</thead>
	<tbody>
		<?php echo $Stories->rows; ?>
	</tbody>
	<tfoot>
		<?php echo $Stories->columns; ?>
	</tfoot>
</table>
<?php echo $Stories->pagination; ?>
