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

function dbs_mc_get_profiles_dir() {
    $upload_dir = wp_upload_dir();
    return trailingslashit($upload_dir['basedir']) . 'dbs-library/memory-archive/profiles/';
}

function dbs_mc_get_archetypes() {
    $list = get_option('dbs_mc_archetypes', 'dub, randall, nasty_p');
    return array_filter(array_map('trim', explode(',', $list)));
}

function dbs_mc_activate() {
    $dir = dbs_mc_get_profiles_dir();
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    if (!get_option('dbs_mc_archetypes')) {
        update_option('dbs_mc_archetypes', 'dub, randall, nasty_p');
    }
    add_role('dbs_member', 'DBS Member', ['read' => true]);
}
register_activation_hook(__FILE__, 'dbs_mc_activate');

function dbs_mc_deactivate() {
    remove_role('dbs_member');
}
register_deactivation_hook(__FILE__, 'dbs_mc_deactivate');

function dbs_mc_register_shortcodes() {
    add_shortcode('dbs_initiation_form', 'dbs_mc_render_initiation_form');
}
add_action('init', 'dbs_mc_register_shortcodes');

function dbs_mc_enqueue_assets() {
    wp_enqueue_style('dbs-initiation-css', plugins_url('assets/css/initiation.css', __FILE__), [], DBS_MC_VERSION);
    wp_enqueue_script('dbs-initiation-js', plugins_url('assets/js/initiation.js', __FILE__), ['jquery'], DBS_MC_VERSION, true);
}
add_action('wp_enqueue_scripts', 'dbs_mc_enqueue_assets');

function dbs_mc_render_initiation_form() {
    ob_start();
    include DBS_MC_PLUGIN_DIR . 'frontend/initiation-form.php';
    return ob_get_clean();
}

function dbs_mc_admin_menu() {
    add_menu_page('DBS Members', 'DBS Members', 'manage_options', 'dbs-members', 'dbs_mc_members_page');
    add_submenu_page('dbs-members', 'Settings', 'Settings', 'manage_options', 'dbs-members-settings', 'dbs_mc_settings_page');
    add_submenu_page(null, 'Edit Member', 'Edit Member', 'manage_options', 'dbs-member-editor', 'dbs_mc_editor_page');
}
add_action('admin_menu', 'dbs_mc_admin_menu');

function dbs_mc_members_page() {
    include DBS_MC_PLUGIN_DIR . 'admin/members.php';
}
function dbs_mc_settings_page() {
    include DBS_MC_PLUGIN_DIR . 'admin/settings.php';
}
function dbs_mc_editor_page() {
    include DBS_MC_PLUGIN_DIR . 'admin/editor.php';
}

// REST API
add_action('rest_api_init', function () {
    register_rest_route('dbs/v1', '/member/(?P<username>[a-zA-Z0-9_-]+)', [
        'methods' => 'GET',
        'callback' => function ($request) {
            $user = get_user_by('login', $request['username']);
            if (!$user) {
                return new WP_Error('not_found', 'User not found', ['status' => 404]);
            }
            $memory = dbs_mc_get_member_memory($user->ID);
            return rest_ensure_response($memory);
        },
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('dbs/v1', '/initiate', [
        'methods' => 'POST',
        'callback' => function ($request) {
            if (!is_user_logged_in()) {
                return new WP_Error('forbidden', 'Login required', ['status' => 403]);
            }
            $params = $request->get_json_params();
            $archetype = sanitize_text_field($params['archetype']);
            $town = sanitize_text_field($params['town']);
            $county = sanitize_text_field($params['county']);
            $user_id = get_current_user_id();
            $latin = dbs_mc_generate_latin_name($archetype);
            $geo = dbs_mc_generate_geo_name($town);
            if (!dbs_mc_geo_claim_exists('default', $county)) {
                dbs_mc_store_geo_claim('default', $county, $geo);
            }
            update_user_meta($user_id, 'dbs_archetype', $archetype);
            update_user_meta($user_id, 'dbs_town', $town);
            update_user_meta($user_id, 'dbs_county', $county);
            update_user_meta($user_id, 'dbs_latin_name', $latin);
            update_user_meta($user_id, 'dbs_geo_name', $geo);
            dbs_mc_assign_rank($user_id);
            dbs_mc_set_behavior_tags($user_id, []);
            dbs_mc_write_profile($user_id);
            dbs_mc_notify_lucidus($user_id);
            return ['success' => true];
        },
        'permission_callback' => '__return_true'
    ]);
});
