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
    include_once ('add-new-row.php');
    include_once ('stories-list-table.php');
}

add_action('admin_menu', 'admin_page');