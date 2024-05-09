<?php
/*
Plugin Name: Stories
Description: Stories
Version: 1.0
Author: Yisrael Levine
*/

require_once ('activate.php');
require_once ('deactivate.php');

function admin_page()
{
    add_menu_page(
        'סיפורים', // Page title
        'סיפורים',       // Menu title
        'manage_options',    // Capability required to access the page
        'stories', // Menu slug
        'page_content' // Callback function to display page content
    );
}

function page_content()
{
    echo '<div class="wrap" style="direction: rtl;">';
    echo '<style>
        .quick-edit-button {
            display: none;
        }

        tr:hover .quick-edit-button {
            display: inline-block;
        }
        </style>';
    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';

    echo '<h1>סיפורים</h1>
    <h2>הוספת סיפור</h2>
    <form method="post" action="">
        <label for="name">שם:</label>
        <input type="text" id="name" name="name">
        <br>
        <br>
        <label for="url">קישור:</label>
        <input type="text" id="url" name="url">
        <br>
        <br>
        <label for="new">חדש:</label>
        <input type="checkbox" id="new" name="new">
        <br>
        <br>
        <label for="phone">טלפון:</label>
        <input type="checkbox" id="phone" name="phone">
        <br>
        <br>
        ' . wp_nonce_field('add_new_row_action', 'add_new_row_nonce') . '
        <input type="submit" name="submit" value="הוספה">
    </form>';

    if (isset($_POST['submit']) && wp_verify_nonce($_POST['add_new_row_nonce'], 'add_new_row_action')) {
        $wpdb->insert(
            $table_name,
            array(
                'name' => $_POST['name'],
                'url' => $_POST['url'],
                'new' => $_POST['new'] === 'on',
                'phone' => $_POST['phone'] === 'on'
            )
        );

        if ($wpdb->insert_id) {
            echo '<div class="updated"><p>הסיפור נוסף בהצלחה!</p></div>';
        } else {
            echo '<div class="error"><p>אירע שגיאה בהוספת הסיפור!</p></div>';
        }
    }

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

    $stories = new Stories();
    $stories->prepare_items();

    echo '<h2>רשימת סיפורים</h2>';
    echo '<form method="post">';
    $stories->display();
    echo '</form>';

    echo '</div>';
}

add_action('admin_menu', 'admin_page');