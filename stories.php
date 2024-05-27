<?php
/*
Plugin Name: Stories
Description: Stories
Version: 1.0
Author: Yisrael Levine
*/

if (!defined('ABSPATH')) {
    exit;
}

require_once ('db.php');
register_activation_hook(__FILE__, 'create');
register_deactivation_hook(__FILE__, 'drop');

include_once ('nonce.php');

include_once ('admin-page/admin-page.php');

include_once ('api/api.php');
