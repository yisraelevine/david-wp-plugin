<?php

function admin_page()
{
    add_menu_page(
        'סיפורים',
        'סיפורים',
        'manage_options',
        'stories',
        'page_content'
    );
}

function page_content()
{
    ?>
    <div class="wrap">
        <?php
        include_once ('upload-csv.php');
        include_once ('add-new-row.php');
        include_once ('stories-list-table.php');
        ?>
    </div>
    <?php
    wp_enqueue_style('admin-page-styles', plugins_url('admin-page-styles.css', __FILE__));
}

add_action('admin_menu', 'admin_page');
