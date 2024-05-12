<h2>הוספת סיפור</h2>

<form method="post" action="" class="form-grid">
    <label for="name">שם:</label>
    <input type="text" id="name" name="name" required>
    
    <label for="url">קישור:</label>
    <input type="url" id="url" name="url" required>
    
    <label for="new">חדש:</label>
    <input type="checkbox" id="new" name="new">
    
    <label for="phone">טלפון:</label>
    <input type="checkbox" id="phone" name="phone">
    
    <?php wp_nonce_field('add_new_row_action', 'add_new_row_nonce') ?>
    
    <input type="submit" name="submit" value="הוספה">
</form>

<?php
if (isset($_POST['submit']) && wp_verify_nonce($_POST['add_new_row_nonce'], 'add_new_row_action')) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';

    $name = sanitize_text_field($_POST['name']);
    $url = esc_url($_POST['url']);
    $is_new = isset($_POST['new']) ? 1 : 0;
    $is_phone = isset($_POST['phone']) ? 1 : 0;

    $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'url' => $url,
            'new' => $is_new,
            'phone' => $is_phone
        )
    );

    if ($wpdb->insert_id) {
        echo '<div class="updated"><p>הסיפור נוסף בהצלחה!</p></div>';
    } else {
        echo '<div class="error"><p>אירעה שגיאה בהוספת הסיפור!</p></div>';
    }
}
?>
