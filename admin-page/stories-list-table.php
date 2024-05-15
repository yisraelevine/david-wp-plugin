<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Stories extends WP_List_Table
{
	protected function pagination( $which ) {
		if ( empty( $this->_pagination_args ) ) {
			return;
		}

		$total_items     = $this->_pagination_args['total_items'];
		$total_pages     = $this->_pagination_args['total_pages'];
		$infinite_scroll = false;
		if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			$infinite_scroll = $this->_pagination_args['infinite_scroll'];
		}


		if ( 'top' === $which && $total_pages > 1 ) {
			$this->screen->render_screen_reader_content( 'heading_pagination' );
		}


		$output = '<span class="displaying-num">' . sprintf(
			/* translators: %s: Number of items. */
			_n( '%s item', '%s items', $total_items ),
			number_format_i18n( $total_items )
		) . '</span>';


		$current              = $this->get_pagenum();
		$removable_query_args = wp_removable_query_args();


		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );


		$current_url = remove_query_arg( $removable_query_args, $current_url );


		$page_links = array();


		$total_pages_before = '<span class="paging-input">';
		$total_pages_after  = '</span></span>';


		$disable_first = false;
		$disable_last  = false;
		$disable_prev  = false;
		$disable_next  = false;


		if ( 1 == $current ) {
			$disable_first = true;
			$disable_prev  = true;
		}
		if ( $total_pages == $current ) {
			$disable_last = true;
			$disable_next = true;
		}


		if ( $disable_first ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='first-page button' href='%s'>" .
					"<span class='screen-reader-text'>%s</span>" .
					"<span aria-hidden='true'>%s</span>" .
				'</a>',
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				/* translators: Hidden accessibility text. */
				__( 'First page' ),
				'&laquo;'
			);
		}


		if ( $disable_prev ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='prev-page button' href='%s'>" .
					"<span class='screen-reader-text'>%s</span>" .
					"<span aria-hidden='true'>%s</span>" .
				'</a>',
				esc_url( add_query_arg( 'paged', max( 1, $current - 1 ), $current_url ) ),
				/* translators: Hidden accessibility text. */
				__( 'Previous page' ),
				'&lsaquo;'
			);
		}


		if ( 'bottom' === $which ) {
			$html_current_page  = $current;
			$total_pages_before = sprintf(
				'<span class="screen-reader-text">%s</span>' .
				'<span id="table-paging" class="paging-input">' .
				'<span class="tablenav-paging-text">',
				/* translators: Hidden accessibility text. */
				__( 'Current Page' )
			);
		} else {
			$html_current_page = sprintf(
				'<label for="current-page-selector" class="screen-reader-text">%s</label>' .
				"<input class='current-page' id='current-page-selector' type='text'
					name='paged' value='%s' size='%d' aria-describedby='table-paging' />" .
				"<span class='tablenav-paging-text'>",
				__( 'Current Page' ),
				$current,
				strlen( $total_pages )
			);
		}


		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );


		$page_links[] = $total_pages_before . sprintf(
			_x( '%1$s of %2$s', 'paging' ),
			$html_current_page,
			$html_total_pages
		) . $total_pages_after;


		if ( $disable_next ) {
			$page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
		} else {
			$page_links[] = sprintf(
				"<a class='next-page button' href='%s'>" .
					"<span class='screen-reader-text'>%s</span>" .
					"<span aria-hidden='true'>%s</span>" .
				'</a>',
				esc_url( add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ) ),
				/* translators: Hidden accessibility text. */
				__( 'Next page' ),
				'&rsaquo;'
			);
		}
    }
    
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'stories';

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $start_item = ($current_page - 1) * $per_page + 1;
        $end_item = min($start_item + $per_page - 1, $total_items);

        $this->items = $wpdb->get_results("SELECT * FROM $table_name LIMIT " . (($current_page - 1) * $per_page) . ", $per_page", ARRAY_A);

        $this->set_pagination_args([
            'per_page' => $per_page,
            'total_items' => $total_items,
            'start_item' => $start_item,
            'end_item' => $end_item
        ]);

        $this->_column_headers = array($this->get_columns());
        $this->process_bulk_action();
    }

    function get_columns()
    {
        return [
            'cb' => '<input type="checkbox">',
            'name' => '<div style="text-align: right;">שם</div>',
            'url' => '<div style="text-align: right;">קישור</div>',
            'new' => '<div style="text-align: right;">חדש</div>',
            'phone' => '<div style="text-align: right;">טלפון</div>'
        ];
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'new':
            case 'phone':
                return '<input type="checkbox"' . ($item[$column_name] ? 'checked' : '') . '>';
            default:
                return $item[$column_name];
        }
    }

    function column_cb($item)
    {
        return '<input type="checkbox" name="bulk-delete[]" value="' . $item['id'] . '">';
    }

    function get_bulk_actions()
    {
        return [
            'delete' => 'מחיקה'
        ];
    }

    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'stories';

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['bulk-delete']) ? $_REQUEST['bulk-delete'] : array();
            if (is_array($ids))
                $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
                echo '<script>window.location.reload();</script>';
            }
        }
    }
}

?>

<h2>רשימת סיפורים</h2>
<form method="post" class="view-mode">
    <?php
    $stories = new Stories();
    $stories->prepare_items();
    $stories->display();
    ?>
</form>
