<?php

function create_stories_table()
{
    global $wpdb;
    $table = $wpdb->prefix . 'stories';

    $query = "CREATE TABLE IF NOT EXISTS $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        url VARCHAR(255) NOT NULL,
        is_new BIT NOT NULL,
        is_phone BIT NOT NULL
    )";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($query);
}

function activate_stories_plugin()
{
    create_stories_table();
}
