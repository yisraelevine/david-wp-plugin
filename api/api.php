<?php
add_action('rest_api_init', 'register_custom_endpoint');

function register_custom_endpoint() {
    register_rest_route('stories/v1', '/list/', array(
        'methods' => 'GET',
        'callback' => 'list_endpoint_callback',
    ));
}

function list_endpoint_callback($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';
    $results = $wpdb->get_results("SELECT id, name, new, phone FROM $table_name", ARRAY_A);
    $json_string = json_encode($results);
    $decoded_array = json_decode($json_string, true);
    return $decoded_array;
}
