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
            'permission_callback' => function () {
                return current_user_can('administrator');
            }
        )
    );
}

function list_endpoint_callback()
{
    global $wpdb;
    $results = $wpdb->get_results("CALL getStories()", ARRAY_A);

    return $results;
}

function url_endpoint_callback($data)
{
    $id = (int) $data->get_param('id');

    global $wpdb;
    $var = $wpdb->get_var("CALL getStoryUrl($id)");

    return (string) $var;
}

function insert_story_endpoint_callback($data)
{
    $name = (string) $data->get_param('name');
    $url = (string) $data->get_param('url');
    $is_new = (bool) $data->get_param('is_new');
    $is_phone = (bool) $data->get_param('is_phone');

    $query = $wpdb->prepere("CALL insertStory('%s', '%s', %d, %d)", $name, $url, $is_new, $is_phone);
    $response = $wpdb->query($query);
    
    return $response;
}