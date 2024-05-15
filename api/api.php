<?php

add_action('rest_api_init', 'register_custom_endpoint');

function register_custom_endpoint() {
    register_rest_route('stories/v1', '/list/', array(
        'methods' => 'GET',
        'callback' => 'list_endpoint_callback',
    ));
}

function list_endpoint_callback() {
    $response = array(
        'message' => 'This is a custom endpoint response'
    );
    return rest_ensure_response($response);
}