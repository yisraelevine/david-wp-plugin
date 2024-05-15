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

        $total_items = $this->_pagination_args['total_items'];
        $total_pages = $this->_pagination_args['total_pages'];
        $start_item = $this->_pagination_args['start_item'];
        $end_item = $this->_pagination_args['end_item'];
        $infinite_scroll = false;

        if ( 'top' === $which && $total_pages > 1 ) {
            $this->screen->render_screen_reader_content( 'heading_pagination' );
        }

        $pagination_text = sprintf( __( 'Showing %1$s to %2$s of %3$s items', 'textdomain' ),
            $start_item,
            $total_pages,
            $total_items
        );
        ?>
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $pagination_text; ?></span>
            <?php $this->pagination_links( $which ); ?>
        </div>
        <?php
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
