<?php
/*
Plugin Name: Stories
Description: Stories
Version: 1.0
Author: Yisrael Levine
*/

require_once ('activate-stories.php');
register_activation_hook(__FILE__, 'activate_stories_plugin');

require_once ('deactivate-stories.php');
register_deactivation_hook(__FILE__, 'deactivate_stories_plugin');

include_once ('admin-page/admin-page.php');

include_once ('api/api.php');

add_action('rest_api_init', 'register_custom_endpoint');

function register_custom_endpoint() {
    register_rest_route('stories/v1', '/list/', array(
        'methods' => 'GET',
        'callback' => 'list_endpoint_callback',
    ));
}

function list_endpoint_callback($data) {
    $response = array(
        'message' => 'This is a custom endpoint response',
        'data_received' => $data,
    );
    return rest_ensure_response($response);
}