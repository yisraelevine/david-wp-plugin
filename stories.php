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

require_once ('activate-stories.php');
register_activation_hook(__FILE__, 'activate_stories_plugin');

require_once ('deactivate-stories.php');
register_deactivation_hook(__FILE__, 'deactivate_stories_plugin');

include_once ('nonce.php');

include_once ('admin-page/admin-page.php');

include_once ('api/api.php');
