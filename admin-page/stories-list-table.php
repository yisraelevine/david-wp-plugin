<?php

class Stories extends WP_List_Table
{
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'stories';

        $per_page = 10;
        $current_page = $this->get_pagenum();

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        $this->items = $wpdb->get_results("SELECT * FROM $table_name LIMIT " . (($current_page - 1) * $per_page) . ", $per_page", ARRAY_A);

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
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
            case 'name':
                return '<div>' . $item[$column_name] . '</div>' . $this->add_quick_edit_button($item);
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
    function add_quick_edit_button($item)
    {
        return '<button class="quick-edit-button" data-item-id="' . $item->id . '">Quick Edit</button>';
    }
}

?>

<div class="wrap" style="direction: rtl;">
    <style>
        .quick-edit-button {
            display: none;
        }

        tr:hover .quick-edit-button {
            display: inline-block;
        }
    </style>
    <h2>רשימת סיפורים</h2>
    <form method="post">
        <?php
        $stories = new Stories();
        $stories->prepare_items();
        $stories->display();
        ?>
    </form>
</div>