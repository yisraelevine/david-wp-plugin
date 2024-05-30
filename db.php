<?php

function create_table($table)
{
    $query = "CREATE TABLE IF NOT EXISTS $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        url VARCHAR(200) NOT NULL,
        is_new BIT NOT NULL,
        is_phone BIT NOT NULL
    )";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($query);
}

function drop_table($table, $wpdb)
{
    $query = "DROP TABLE IF EXISTS $table";

    $wpdb->query($query);
}

function create_procedure($procedure, $wpdb)
{
    [$name, $params, $body] = $procedure;
    $query = "CREATE PROCEDURE $name($params) BEGIN $body; END";

    $wpdb->query($query);
}

function drop_procedure($procedure, $wpdb)
{
    [$name] = $procedure;
    $query = "DROP PROCEDURE IF EXISTS $name";

    $wpdb->query($query);
}

global $wpdb, $table, $procedures;

$table = $wpdb->prefix . 'stories';
$procedures = [
    [
        'insertStory',
        'IN p_name VARCHAR(200), IN p_url VARCHAR(200), IN p_is_new BIT, IN p_is_phone BIT',
        "INSERT INTO $table (name, url, is_new, is_phone) VALUES (p_name, p_url, p_is_new, p_is_phone)"
    ],
    [
        'updateStory',
        'IN p_id INT, IN p_name VARCHAR(200), IN p_url VARCHAR(200), IN p_is_new BIT, IN p_is_phone BIT',
        "UPDATE $table SET name = p_name, url = p_url, is_new = p_is_new, is_phone = p_is_phone WHERE id = p_id"
    ],
    [
        'getStories',
        '',
        "SELECT id, name, is_new, is_phone FROM $table ORDER BY id DESC"
    ],
    [
        'getStoryUrl',
        'IN p_id INT',
        "SELECT url FROM $table WHERE id = p_id"
    ],
    [
        'getStoriesAdmin',
        'IN p_offset INT, IN p_limit INT',
        "SELECT * FROM $table ORDER BY id DESC LIMIT p_offset, p_limit"
    ],
    [
        'getStoriesCount',
        '',
        "SELECT COUNT(*) FROM $table"
    ]
];

function create()
{
    global $wpdb, $table, $procedures;

    create_table($table);
    foreach ($procedures as $procedure) {
        create_procedure($procedure, $wpdb);
    }
}

function drop()
{
    global $wpdb, $table, $procedures;

    drop_table($table, $wpdb);
    foreach ($procedures as $procedure) {
        drop_procedure($procedure, $wpdb);
    }
}
