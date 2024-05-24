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
    register_rest_route(
        'stories/v1',
        '/list-admin/',
        array(
            'methods' => 'GET',
            'callback' => 'list_admin_endpoint_callback',
            'permission_callback' => 'user_is_admin'
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
function list_admin_endpoint_callback(WP_REST_Request $req)
{
    $limit = $req->get_param('limit');
    $offset = $req->get_param('offset');

    global $wpdb;
    $query = $wpdb->prepare('CALL getStoriesAdmin(%d, %d)', $limit, $offset);
    $results = $wpdb->get_results($query, ARRAY_A);

    return $results;
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
        if (count($data) != 4) {
            continue;
        }
    
        $values[] = $wpdb->prepare("(%s, %s, %d, %d)", $data[0], urldecode($data[1]), (bool) $data[2], (bool) $data[3]);
    }
    
    fclose($handle);
    
    if (empty($values)) {
        return;
    }
    
    $table = $wpdb->prefix . 'stories';
    $query = "INSERT INTO $table (name, url, is_new, is_phone) VALUES " . implode(", ", array_reverse($values));
    $result = $wpdb->query($query);
    return $result;
}
