<?php

function create_stories_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        url VARCHAR(255) NOT NULL,
        new BIT NOT NULL,
        phone BIT NOT NULL
    )";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);
}

function activate_stories_plugin()
{
    create_stories_table();
}
register_activation_hook(__FILE__, 'activate_stories_plugin');