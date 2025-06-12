<?php
/*
Plugin Name: DBS Members Plugin
Description: Handles member login, registration and dashboard pages.
Version: 1.0
Author: DBS Devs
License: MIT
*/

if (!defined('ABSPATH')) exit;

define('DBS_MEMBERS_DIR', plugin_dir_path(__FILE__));

function dbs_members_load_textdomain() {
    load_plugin_textdomain('dbs-members-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'dbs_members_load_textdomain');

require_once DBS_MEMBERS_DIR . 'includes/shortcodes.php';
require_once DBS_MEMBERS_DIR . 'includes/pages.php';
require_once DBS_MEMBERS_DIR . 'includes/map.php';

register_activation_hook(__FILE__, function() {
    dbs_members_activate();
    flush_rewrite_rules();
});

function dbs_members_enqueue_styles() {
    if (!is_page()) {
        return;
    }
    $post = get_post();
    $content = $post ? $post->post_content : '';
    if (has_shortcode($content, 'dbs_login') || has_shortcode($content, 'dbs_register') || has_shortcode($content, 'dbs_dashboard')) {
        wp_enqueue_style('dbs-members', plugins_url('assets/css/members.css', __FILE__));
    }
    if (has_shortcode($content, 'dbs_members_map')) {
        wp_enqueue_style('leaflet');
        wp_enqueue_style('dbs-members-map');
        wp_enqueue_script('leaflet');
        wp_enqueue_script('dbs-members-map');
        wp_localize_script('dbs-members-map', 'dbsMembersMap', [
            'rest_url' => rest_url('dbs-members/v1/locations')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'dbs_members_enqueue_styles');
?>
