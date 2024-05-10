<?php

function delete_stories_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';

    $sql = "DROP TABLE IF EXISTS $table_name";

    $wpdb->query($sql);
}

function deactivate_stories_plugin()
{
    delete_stories_table();
}