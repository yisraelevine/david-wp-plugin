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
        '/upload-list/',
        array(
            'methods' => 'POST',
            'callback' => 'upload_list_endpoint_callback',
            'permission_callback' => 'user_is_admin'
        )
    );
    register_rest_route(
        'stories/v1',
        '/update-last-update/',
        array(
            'methods' => 'POST',
            'callback' => 'update_date_endpoint_callback',
            'permission_callback' => 'user_is_admin'
        )
    );
    register_rest_route(
        'stories/v1',
        '/get-last-update/',
        array(
            'methods' => 'GET',
            'callback' => 'get_date_endpoint_callback',
            'permission_callback' => 'user_is_admin'
        )
    );
    register_rest_route(
        'stories/v1',
        '/admin-list/',
        array(
            'methods' => 'GET',
            'callback' => 'admin_list_endpoint_callback',
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
    $results = $wpdb->get_results("SELECT id, name, is_new, is_phone, seq FROM $table", ARRAY_A);

    $option = get_option('stories-last-update', '');
    $clean = stripslashes($option);

    return array(
        'last_update' => $clean,
        'list' => $results
    );
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

function upload_list_endpoint_callback()
{
    global $wpdb;
    $table = $wpdb->prefix . 'stories';

    $rows = json_decode(file_get_contents("php://input"));

    $rows = array_map(function($row) {
        global $wpdb;
        $id = $row->id;
        $name = $row->name;
        $url = urldecode($row->url);
        $is_new = (bool) $row->is_new;
        $is_phone = (bool) $row->is_phone;
        $seq = $row->seq;
        $value = $wpdb->prepare("(%d, %s, %s, %d, %d, %d)", $id, $name, $url, $is_new, $is_phone, $seq);
        return $value;
    }, $rows);

    $wpdb->query("DELETE FROM $table");

    $rows = implode(", ", $rows);
    $query = "INSERT INTO $table (id, name, url, is_new, is_phone, seq) VALUES $rows";
    $wpdb->query($query);
}

function update_date_endpoint_callback()
{
    $last_update = $_POST['last-update'];

    update_option('stories-last-update', $last_update);
}

function get_date_endpoint_callback()
{
    $option = get_option('stories-last-update', '');
    $clean = stripslashes($option);

    return $clean;
}

function admin_list_endpoint_callback()
{
    global $wpdb;

    $table = $wpdb->prefix . 'stories';
    $results = $wpdb->get_results("SELECT id, name, url, is_new, is_phone, seq FROM $table", ARRAY_A);

    return $results;
}
