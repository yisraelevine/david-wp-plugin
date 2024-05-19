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
    $table = $wpdb->prefix . 'stories';
    $query = "SELECT id, name, is_new, is_phone FROM $table";
    $results = $wpdb->get_results($query, ARRAY_A);
    return $results;
}

function url_endpoint_callback($data) {
    $id = filter_var($data->get_param('id'), FILTER_SANITIZE_NUMBER_INT);

    if ($id === false || $id === '') {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'stories';
    $query = $wpdb->prepare("SELECT url FROM $table WHERE id = %d", $id);
    $var = $wpdb->get_var($query);

    return $var ?: null;
}
