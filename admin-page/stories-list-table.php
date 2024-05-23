<?php

class StoriesListTable
{
	public $columns;
	public $rows;
	public $pagination;
	public function __construct()
	{
		global $wpdb;
		$offset = 50;
		$table = $wpdb->prefix . 'stories';
		$current = filter_input(INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT) ?: 1;
		$limit = ($current - 1) * $offset;
		$count = $this->get_count($table);
		$pages = ceil($count / $offset);
		$results = $this->get_results($table, $limit, $offset);

		$this->columns = $this->render_columns();
		$this->rows = $this->render_rows($results);
		$this->pagination = $this->render_pagination($current, $pages, $count);
	}
	private function get_count($table)
	{
		global $wpdb;
		$query = $wpdb->prepare('SELECT COUNT(*) FROM %i', $table);
		$var = $wpdb->get_var($query);
		return $var;
	}
	private function get_results($table, $limit, $offset)
	{
		global $wpdb;
		$query = $wpdb->prepare('SELECT * FROM %i ORDER BY position LIMIT %d, %d', $table, $limit, $offset);
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
			$name = esc_attr($result['name']);
			$url = urldecode($result['url']);
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
	private function render_pagination($current, $pages, $count)
	{
		$is_first = $current == 1;
		$is_last = $current == $pages;

		$first_page = $is_first ? '' : sprintf('href="%s"', add_query_arg('paged', 1));
		$prev_page = $is_first ? '' : sprintf('href="%s"', add_query_arg('paged', $current - 1));
		$last_page = $is_last ? '' : sprintf('href="%s"', add_query_arg('paged', $pages));
		$next_page = $is_last ? '' : sprintf('href="%s"', add_query_arg('paged', $current + 1));

		return <<<END
		<div class="pagination">
			<span>$count סיפורים</span>
			<a $first_page>&lt&lt</a>
			<a $prev_page>&lt</a>
			<input value="$current" />
			<span> מתוך $pages</span>
			<a $next_page>&gt</a>
			<a $last_page>&gt&gt</a>
		</div>
		END;
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
