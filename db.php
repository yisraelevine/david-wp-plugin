<?php

function create_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'stories';
    $query = "CREATE TABLE IF NOT EXISTS $table (
        id INT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        url VARCHAR(200) NOT NULL,
        is_new BIT NOT NULL,
        is_phone BIT NOT NULL
    )";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($query);
}

function drop_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'stories';
    $query = "DROP TABLE IF EXISTS $table";

    $wpdb->query($query);
}

function clean()
{
    drop_table();
    delete_option('stories-last-update');
}
