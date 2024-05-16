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
    $id = filter_var($data->get_param('id'), FILTER_SANITIZE_NUMBER_INT);

    if ($id === false || $id === '') {
        return new WP_Error('invalid_id', 'Invalid ID parameter', array('status' => 400));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';
    $query = $wpdb->prepare("SELECT url FROM $table_name WHERE id = %d", $id);
    $url = $wpdb->get_var($query);

    if (!$url) {
        return new WP_Error('story_not_found', 'Story not found', array('status' => 404));
    }

    return array('url' => $url);
}
