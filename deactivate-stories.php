<?php

function delete_stories_table()
{
    global $wpdb;
    $table = $wpdb->prefix . 'stories';

    $wpdb->query("DROP TABLE IF EXISTS $table");

    $procedures = [
        'insertStory',
        'getStories',
        'getStoryUrl',
        'getStoriesAdmin'
    ];
    foreach ($procedures as $procedure) {
        $wpdb->query("DROP PROCEDURE IF EXISTS $procedure");
    }
}
function deactivate_stories_plugin()
{
    delete_stories_table();
}
