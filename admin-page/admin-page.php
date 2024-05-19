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
    wp_enqueue_style('admin-page-styles', plugins_url('admin-page-styles.css', __FILE__));
    ?>
    <div class="wrap">
        <h2>ניהול סיפורים</h2>
        <?php
        include_once ('upload-csv.php');
        include_once ('add-new-row.php');
        include_once ('stories-list-table.php');
        ?>
    </div>
    <?php
}

add_action('admin_menu', 'admin_page');
