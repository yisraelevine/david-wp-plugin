<h2>הוספת סיפור</h2>
<form method="post">
    <div>
        <label for="name">שם:</label>
        <input type="text" id="name" name="name">
    </div>
    <div>
        <label for="url">קישור:</label>
        <input type="text" id="url" name="url">
    </div>
    <div>
        <label for="new">חדש:</label>
        <input type="checkbox" id="new" name="new">
    </div>
    <div>
        <label for="phone">טלפון:</label>
        <input type="checkbox" id="phone" name="phone">
    </div>
    <div>
        <?php wp_nonce_field('add_new_row_action', 'add_new_row_nonce') ?>
        <input type="submit" name="submit" value="הוספה">
    </div>
</form>

<?php

if (isset($_POST['submit']) && wp_verify_nonce($_POST['add_new_row_nonce'], 'add_new_row_action')) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'stories';

    $wpdb->insert(
        $table_name,
        array(
            'name' => $_POST['name'],
            'url' => $_POST['url'],
            'new' => $_POST['new'] === 'on',
            'phone' => $_POST['phone'] === 'on'
        )
    );

    if ($wpdb->insert_id) {
        echo '<div class="updated"><p>הסיפור נוסף בהצלחה!</p></div>';
    } else {
        echo '<div class="error"><p>אירע שגיאה בהוספת הסיפור!</p></div>';
    }
}