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

function create_procedures($procedures)
{
    global $wpdb;

    foreach ($procedures as $p) {
        [$name, $params, $body] = $p;
        $query = "CREATE PROCEDURE $name($params) BEGIN $body; END";

        $wpdb->query($query);
    }
}

function drop_table($table)
{
    global $wpdb;

    $query = "DROP TABLE IF EXISTS $table";

    $wpdb->query($query);
}

function drop_procedures($procedures)
{
    global $wpdb;

    foreach ($procedures as $p) {
        $query = "DROP PROCEDURE IF EXISTS $p";

        $wpdb->query($query);
    }
}

function create()
{
    global $wpdb;

    $table = $wpdb->prefix . 'stories';
    $procedures = [
        [
            'insertStory',
            'IN p_name VARCHAR(200), IN p_url VARCHAR(200), IN p_is_new BIT, IN p_is_phone BIT',
            "INSERT INTO $table (name, url, is_new, is_phone) VALUES (p_name, p_url, p_is_new, p_is_phone)"
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

    create_table($table);
    create_procedures($procedures);
}

function drop()
{
    global $wpdb;
    $table = $wpdb->prefix . 'stories';

    $procedures = ['insertStory', 'getStories', 'getStoryUrl', 'getStoriesAdmin', 'getStoriesCount'];

    drop_table($table);
    drop_procedures($procedures);
}
