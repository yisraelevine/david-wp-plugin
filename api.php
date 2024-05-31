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
            'permission_callback' => 'user_is_logged_in'
        )
    );
    register_rest_route(
        'stories/v1',
        '/upload-csv/',
        array(
            'methods' => 'POST',
            'callback' => 'upload_csv_endpoint_callback',
            'permission_callback' => 'user_is_admin'
        )
    );
}

function user_is_logged_in()
{
    if (is_user_logged_in()) {
        return true;
    } else {
        return new WP_Error('rest_not_logged_in', 'You must be logged in to access this endpoint', array('status' => 401));
    }
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

function upload_csv_endpoint_callback()
{
    if (!isset($_FILES['csv_file']['tmp_name']) || empty($_FILES['csv_file']['tmp_name'])) {
        return;
    }

    $csv_file_path = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($csv_file_path, "r");

    if (!$handle) {
        return;
    }

    $values = [];
    global $wpdb;
    while ($data = fgetcsv($handle, 500)) {
        if (count($data) != 5) {
            continue;
        }

        $values[] = $wpdb->prepare("(%d, %s, %s, %d, %d)", $data[0], $data[1], urldecode($data[2]), (bool) $data[3], (bool) $data[4]);
    }

    fclose($handle);

    if (empty($values)) {
        return;
    }

    $table = $wpdb->prefix . 'stories';
    $result = $wpdb->query("DELETE FROM $table");
    $query = "INSERT INTO $table (id, name, url, is_new, is_phone) VALUES " . implode(", ", array_reverse($values));
    $result = $wpdb->query($query);
    return $result;
}
