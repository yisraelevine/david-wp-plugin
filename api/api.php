<?php
add_action('rest_api_init', 'register_list_endpoint');

function register_list_endpoint()
{
    register_rest_route(
        'stories/v1',
        '/list/',
        array(
            'methods' => 'GET',
            'callback' => 'list_endpoint_callback'
        )
    );
    register_rest_route(
        'stories/v1',
        '/url/',
        array(
            'methods' => 'GET',
            'callback' => 'url_endpoint_callback',
            'permission_callback' => 'user_is_admin'
        )
    );
    register_rest_route(
        'stories/v1',
        '/insert-story/',
        array(
            'methods' => 'POST',
            'callback' => 'insert_story_endpoint_callback',
            'permission_callback' => 'user_is_admin'
        )
    );
}

function user_is_admin()
{
    return current_user_can('administrator');
}

function list_endpoint_callback()
{
    global $wpdb;
    $results = $wpdb->get_results("CALL getStories()", ARRAY_A);

    return $results;
}

function url_endpoint_callback(WP_REST_Request $req)
{
    $id = $req->get_param('id');

    global $wpdb;
    $query = $wpdb->prepare('CALL getStoryUrl(%d)', $id);
    $var = $wpdb->get_var($query);

    return (string) $var;
}

function insert_story_endpoint_callback(WP_REST_Request $req)
{
    $name = $req->get_param('name');
    $url = $req->get_param('url');
    $is_new = $req->get_param('is_new');
    $is_phone = $req->get_param('is_phone');

    global $wpdb;
    $query = $wpdb->prepare(
        'CALL insertStory("%s", "%s", %d, %d)',
        $name,
        $url,
        (bool) $is_new,
        (bool) $is_phone
    );
    $query = $wpdb->query($query);

    return $query;
}
