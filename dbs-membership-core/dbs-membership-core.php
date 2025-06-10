<?php
/**
 * Dead Bastard Society Membership Core loader
 *
 * @package Dead Bastard Society
 */
/*
Plugin Name: DBS Membership Core
Description: Handles membership initiation and profile memory for the Dead Bastard Society.
Version: 0.1.0
Author: Lucidus Bastardo
*/

if (!defined('ABSPATH')) {
    exit;
}

// Path constants
define('DBS_MEMBERSHIP_DIR', plugin_dir_path(__FILE__));
define('DBS_MEMBERSHIP_URL', plugin_dir_url(__FILE__));

// Include logic files
require_once DBS_MEMBERSHIP_DIR . 'includes/latin-name-generator.php';
require_once DBS_MEMBERSHIP_DIR . 'includes/rank-logic.php';
require_once DBS_MEMBERSHIP_DIR . 'includes/geo-name-engine.php';
require_once DBS_MEMBERSHIP_DIR . 'includes/profile-writer.php';
require_once DBS_MEMBERSHIP_DIR . 'includes/behavior-tags.php';
require_once DBS_MEMBERSHIP_DIR . 'includes/ai-bridge.php';

// Admin pages
if (is_admin()) {
    require_once DBS_MEMBERSHIP_DIR . 'admin/members.php';
    require_once DBS_MEMBERSHIP_DIR . 'admin/editor.php';
    require_once DBS_MEMBERSHIP_DIR . 'admin/settings.php';
}

// Frontend pages (shortcodes)
require_once DBS_MEMBERSHIP_DIR . 'frontend/initiation-form.php';
require_once DBS_MEMBERSHIP_DIR . 'frontend/confirmation.php';

