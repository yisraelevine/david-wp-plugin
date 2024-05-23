<?php

function create_stories_table_and_procedures()
{
    global $wpdb;
    $table = $wpdb->prefix . 'stories';

    $query = "CREATE TABLE IF NOT EXISTS $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        url VARCHAR(255) NOT NULL,
        is_new BIT NOT NULL,
        is_phone BIT NOT NULL
    );";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($query);

    $queries = [
        "CREATE PROCEDURE insertStory(IN p_name VARCHAR(255), IN p_url VARCHAR(255), IN p_is_new BIT, IN p_is_phone BIT)
        BEGIN
            INSERT INTO $table (name, url, is_new, is_phone)
            VALUES (p_name, p_url, p_is_new, p_is_phone);
        END;",

        "CREATE PROCEDURE getStories()
        BEGIN
            SELECT id, name, is_new, is_phone FROM $table
            ORDER BY id ASC;
        END;",

        "CREATE PROCEDURE getStoryUrl(IN p_id INT)
        BEGIN
            SELECT url FROM $table
            WHERE id = p_id;
        END;"
    ];

    foreach ($queries as $query) {
        $wpdb->query($query);
    }
}
function activate_stories_plugin()
{
    create_stories_table_and_procedures();
}
