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

    $table = $wpdb->prefix . 'stories';
    $results = $wpdb->get_results("SELECT id, name, is_new, is_phone FROM $table ORDER BY id DESC", ARRAY_A);

    return $results;
}

function url_endpoint_callback(WP_REST_Request $req)
{
    global $wpdb;
    $id = $req->get_param('id');

    $table = $wpdb->prefix . 'stories';
    $query = $wpdb->prepare("SELECT url FROM $table WHERE id = %d", $id);

    $var = $wpdb->get_var($query);

    return (string) $var;
}

function upload_csv_endpoint_callback()
{
    global $wpdb;
    $table = $wpdb->prefix . 'stories';

    $path = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($path, "r");

    $values = [];
    while ($data = fgetcsv($handle, 500)) {
        if (count($data) != 5) {
            continue;
        }

        $id = $data[0];
        $name = $data[1];
        $url = urldecode($data[2]);
        $is_new = (bool) $data[3];
        $is_phone = (bool) $data[4];
        $value = $wpdb->prepare("(%d, %s, %s, %d, %d)", $id, $name, $url, $is_new, $is_phone);
        $values[] = $value;
    }
    
    fclose($handle);
    
    if (empty($values)) {
        return;
    }

    $wpdb->query("DELETE FROM $table");

    $values = implode(", ", array_reverse($values));
    $query = "INSERT INTO $table (id, name, url, is_new, is_phone) VALUES $values";
    $wpdb->query($query);
}
