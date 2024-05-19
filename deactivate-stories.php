<?php

function delete_stories_table()
{
    global $wpdb;
    $table = $wpdb->prefix . 'stories';

    $query = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($query);
}

function deactivate_stories_plugin()
{
    delete_stories_table();
}
