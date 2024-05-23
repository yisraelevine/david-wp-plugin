<?php

global $wpdb;
$table = $wpdb->prefix . 'stories';

$name = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);
$url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);

if (!$name || !$url) {
    echo '<div class="error"><p>שדות השם והקישור חייבים להיות חוקיים!</p></div>';
    return;
}

$is_new = filter_input(INPUT_POST, 'is_new', FILTER_VALIDATE_BOOLEAN) ?? false;
$is_phone = filter_input(INPUT_POST, 'is_phone', FILTER_VALIDATE_BOOLEAN) ?? false;

$query = $wpdb->prepare("CALL insertRowStories(%s, %s, %d, %d)", $name, $url, $is_new, $is_phone);
$is_inserted = $wpdb->query($query);
