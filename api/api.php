<?php
add_action('rest_api_init', 'register_list_endpoint');

function register_list_endpoint() {
    register_rest_route('stories/v1', '/list/', array(
        'methods' => 'GET',
        'callback' => 'list_endpoint_callback',
    ));
   register_rest_route('stories/v1', '/url/', array(
        'methods' => 'GET',
        'callback' => 'url_endpoint_callback',
    ));
}

function list_endpoint_callback($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';
    $results = $wpdb->get_results("SELECT id, name, new, phone FROM $table_name", ARRAY_A);
    return $results;
}

function url_endpoint_callback($data) {
    $id = $_GET['id'];
    $sanitized_id = filter_var($id, FILTER_VALIDATE_INT);

    if ($sanitized_id === false) {
        return [];
    } else {
        global $wpdb;
        $table_name = $wpdb->prefix . 'stories';
        $query = $wpdb->prepare("SELECT url FROM $table_name WHERE id = %d", $sanitized_id);
        $results = $wpdb->get_results($query, ARRAY_A);
        return $results;
    }
}