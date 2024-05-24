<?php
function enqueue_nonce_script()
{
    ?>
    <script>sessionStorage.wpRestNonce = '<?php echo wp_create_nonce('wp_rest') ?>'</script>
    <?php
}
add_action('wp_enqueue_scripts', 'enqueue_nonce_script');
add_action('admin_enqueue_scripts', 'enqueue_nonce_script');
