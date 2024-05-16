<?php
// Registering REST API endpoints
add_action('rest_api_init', 'register_list_endpoint');

// Function to register list endpoint
function register_list_endpoint() {
    // Registering the /list/ endpoint
    register_rest_route('stories/v1', '/list/', array(
        'methods' => 'GET',
        'callback' => 'list_endpoint_callback',
        'permission_callback' => 'list_endpoint_permissions_check', // Permission callback function
    ));

    // Registering the /url/ endpoint
    register_rest_route('stories/v1', '/url/', array(
        'methods' => 'GET',
        'callback' => 'url_endpoint_callback',
        'permission_callback' => 'url_endpoint_permissions_check', // Permission callback function
    ));
}

// Callback function for the /list/ endpoint
function list_endpoint_callback($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';
    // Query to fetch stories data from the database
    $results = $wpdb->get_results("SELECT id, name, new, phone FROM $table_name", ARRAY_A);
    // Check if no stories found
    if (empty($results)) {
        // Return WP_Error if no stories found
        return new WP_Error('no_stories', 'No stories found', array('status' => 404));
    }
    // Return the response with fetched stories data
    return rest_ensure_response($results);
}

// Permission callback function for the /list/ endpoint
function list_endpoint_permissions_check($request) {
    // Return true to allow access to the endpoint
    return true;
}

// Callback function for the /url/ endpoint
function url_endpoint_callback($data) {
    // Get the 'id' parameter from the request
    $id = $data->get_param('id');

    // Check if the 'id' parameter is empty or not a valid digit
    if (empty($id) || !ctype_digit($id)) {
        // Return WP_Error if 'id' parameter is invalid
        return new WP_Error('invalid_id', 'Invalid ID parameter', array('status' => 400));
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';
    // Prepare SQL query to fetch URL based on 'id'
    $query = $wpdb->prepare("SELECT url FROM $table_name WHERE id = %d", $id);
    // Execute query and get the URL
    $url = $wpdb->get_var($query);

    // Check if URL is not found
    if (!$url) {
        // Return WP_Error if story not found for given 'id'
        return new WP_Error('story_not_found', 'Story not found', array('status' => 404));
    }

    // Return the response with fetched URL
    return rest_ensure_response(array('url' => $url));
}

// Permission callback function for the /url/ endpoint
function url_endpoint_permissions_check($request) {
    // Return true to allow access to the endpoint
    return true;
}
