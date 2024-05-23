<?php

function delete_stories_table()
{
    global $wpdb;
    $table = $wpdb->prefix . 'stories';

    $wpdb->query("DROP TABLE IF EXISTS $table");
    $wpdb->query("DROP PROCEDURE IF EXISTS insertStory");
    $wpdb->query("DROP PROCEDURE IF EXISTS getStories");
    $wpdb->query("DROP PROCEDURE IF EXISTS getStoryUrl");
}
function deactivate_stories_plugin()
{
    delete_stories_table();
}
