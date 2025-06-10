<?php
/*
Plugin Name: DBS Membership Core
Description: Handles member onboarding and memory management for the Dead Bastard Society.
Version: 0.1.0
Author: Dead Bastard Society
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('DBS_MC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DBS_MC_VERSION', '0.1.0');

require_once DBS_MC_PLUGIN_DIR . 'includes/latin-name-generator.php';
require_once DBS_MC_PLUGIN_DIR . 'includes/rank-logic.php';
require_once DBS_MC_PLUGIN_DIR . 'includes/profile-writer.php';
require_once DBS_MC_PLUGIN_DIR . 'includes/geo-name-engine.php';
require_once DBS_MC_PLUGIN_DIR . 'includes/ai-bridge.php';
require_once DBS_MC_PLUGIN_DIR . 'includes/behavior-tags.php';

// Register activation hook.
function dbs_mc_activate() {
    // Setup default options if needed.
}
register_activation_hook(__FILE__, 'dbs_mc_activate');

// Register shortcode for initiation form.
function dbs_mc_register_shortcodes() {
    add_shortcode('dbs_initiation_form', 'dbs_mc_render_initiation_form');
}
add_action('init', 'dbs_mc_register_shortcodes');

// Enqueue frontend assets.
function dbs_mc_enqueue_assets() {
    wp_enqueue_style('dbs-initiation-css', plugins_url('assets/css/initiation.css', __FILE__), [], DBS_MC_VERSION);
    wp_enqueue_script('dbs-initiation-js', plugins_url('assets/js/initiation.js', __FILE__), ['jquery'], DBS_MC_VERSION, true);
}
add_action('wp_enqueue_scripts', 'dbs_mc_enqueue_assets');

// Render initiation form.
function dbs_mc_render_initiation_form() {
    ob_start();
    include DBS_MC_PLUGIN_DIR . 'frontend/initiation-form.php';
    return ob_get_clean();
}

// Admin menus.
function dbs_mc_admin_menu() {
    add_menu_page('DBS Members', 'DBS Members', 'manage_options', 'dbs-members', 'dbs_mc_members_page');
    add_submenu_page('dbs-members', 'Settings', 'Settings', 'manage_options', 'dbs-members-settings', 'dbs_mc_settings_page');
}
add_action('admin_menu', 'dbs_mc_admin_menu');

function dbs_mc_members_page() {
    include DBS_MC_PLUGIN_DIR . 'admin/members.php';
}

function dbs_mc_settings_page() {
    include DBS_MC_PLUGIN_DIR . 'admin/settings.php';
}
