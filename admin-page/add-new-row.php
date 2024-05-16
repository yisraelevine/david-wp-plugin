<?php
if (!isset($_POST['submit']) || !wp_verify_nonce($_POST['add_new_row_nonce'], 'add_new_row_action')) {
    return;
}

global $wpdb;
$table_name = $wpdb->prefix . 'stories';

$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);

if (!$name || !$url) {
    echo '<div class="error"><p>שדות השם והקישור חייבים להיות חוקיים!</p></div>';
    return;
}

$is_new = filter_input(INPUT_POST, 'new', FILTER_VALIDATE_BOOLEAN);
$is_phone = filter_input(INPUT_POST, 'phone', FILTER_VALIDATE_BOOLEAN);

$wpdb->insert(
    $table_name,
    array(
        'name' => $name,
        'url' => $url,
        'new' => $is_new,
        'phone' => $is_phone
    )
);

echo $wpdb->insert_id ?
    '<div class="updated"><p>הסיפור נוסף בהצלחה!</p></div>' :
    '<div class="error"><p>אירעה שגיאה בהוספת הסיפור!</p></div>';
?>
<h2>הוספת סיפור</h2>

<form method="post" class="add-story-form">
    <label for="name">שם:</label>
    <input type="text" id="name" name="name" required>
    
    <label for="url">קישור:</label>
    <input type="url" id="url" name="url" required>
    
    <label for="new">חדש:</label>
    <input type="checkbox" id="new" name="new">
    
    <label for="phone">טלפון:</label>
    <input type="checkbox" id="phone" name="phone">
    
    <?php wp_nonce_field('add_new_row_action', 'add_new_row_nonce'); ?>
    
    <input type="submit" name="submit" value="הוספה">
</form>
