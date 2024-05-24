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
    <div class="wrap" style="opacity: 0">
        <h2>ניהול סיפורים</h2>
        <?php
        include_once ('upload-csv.html');
        include_once ('add-new-row.html');
        include_once ('stories-list-table.php');
        ?>
    </div>
    <?php
    wp_enqueue_style('admin-page-styles', plugins_url('admin-page-styles.css', __FILE__));
    wp_enqueue_script('admin-page-script', plugins_url('admin-page-script.js', __FILE__));
}

add_action('admin_menu', 'admin_page');
