<?php
function enqueue_nonce_script()
{
    if (!is_front_page()) {
        return;
    }

    ?>
    <script>sessionStorage.setItem('wpRestNonce', '<?php echo wp_create_nonce('wp_rest') ?>')</script>
    <?php
}
add_action('wp_enqueue_scripts', 'enqueue_nonce_script');
