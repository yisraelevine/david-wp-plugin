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
    <div class="wrap" dir="rtl">
        <h2>ניהול סיפורים</h2>
        <?php
        include_once ('upload-csv.html');
        include_once ('add-new-row.html');
        include_once ('stories-list-table.html');
        ?>
    </div>
    <?php
}

add_action('admin_menu', 'admin_page');
