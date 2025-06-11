<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: AI-powered terminal with chat and memory features.
Version: 1.0
Author: DBS Devs
License: MIT
*/

if (!defined('ABSPATH')) exit;

define('LUCIDUS_TERMINAL_DIR', plugin_dir_path(__FILE__));

function lucidus_terminal_load_textdomain() {
    load_plugin_textdomain('lucidus-terminal-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'lucidus_terminal_load_textdomain');

require_once LUCIDUS_TERMINAL_DIR . 'includes/admin-menu.php';
require_once LUCIDUS_TERMINAL_DIR . 'includes/rest-api.php';
require_once LUCIDUS_TERMINAL_DIR . 'includes/archive-writer.php';
require_once LUCIDUS_TERMINAL_DIR . 'includes/prophecy.php';
require_once LUCIDUS_TERMINAL_DIR . 'includes/memory.php';
require_once dirname(LUCIDUS_TERMINAL_DIR) . '/modules/module-manager.php';
require_once dirname(LUCIDUS_TERMINAL_DIR) . '/modules/module-admin-interface.php';
require_once LUCIDUS_TERMINAL_DIR . 'config/lore-loader/lucidus-lore-loader.php';
require_once LUCIDUS_TERMINAL_DIR . 'config/lore-loader/gpt-prompt-builder.php';
require_once LUCIDUS_TERMINAL_DIR . 'config/lore-loader/chat-terminal-injector.php';
require_once LUCIDUS_TERMINAL_DIR . 'config/lore-loader/map-mood-handler.php';
require_once LUCIDUS_TERMINAL_DIR . 'config/lore-loader/scroll-type-router.php';
require_once LUCIDUS_TERMINAL_DIR . 'config/lore-loader/denial-response-engine.php';
require_once LUCIDUS_TERMINAL_DIR . 'functions/gptToneFormatter.php';
require_once LUCIDUS_TERMINAL_DIR . 'functions/scrollTypeAssigner.php';
require_once LUCIDUS_TERMINAL_DIR . 'functions/lucidusArchetypeFlavor.php';
require_once LUCIDUS_TERMINAL_DIR . 'functions/dev-lore-log.php';
require_once LUCIDUS_TERMINAL_DIR . 'functions/error-log.php';
require_once LUCIDUS_TERMINAL_DIR . 'includes/upgrade-engine.php';
require_once LUCIDUS_TERMINAL_DIR . 'includes/admin-system-prophecy-panel.php';

function lucidus_register_scroll_cpt() {
    register_post_type('scroll', [
        'labels' => [
            'name' => __('Scrolls', 'lucidus-terminal-pro'),
            'singular_name' => __('Scroll', 'lucidus-terminal-pro'),
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'author'],
    ]);
}
add_action('init', 'lucidus_register_scroll_cpt');

function lucidus_register_prophecy_cpt() {
    register_post_type('prophecy', [
        'labels' => [
            'name' => __('Prophecies', 'lucidus-terminal-pro'),
            'singular_name' => __('Prophecy', 'lucidus-terminal-pro'),
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
    ]);
}
add_action('init', 'lucidus_register_prophecy_cpt');


add_action('lucidus_health_check', function() {
    $user_id = get_current_user_id();
    $file = lucidus_memory_file($user_id);
    if (file_exists($file) && filesize($file) > 1024 * 1024) {
        lucidus_save_memory(lucidus_load_memory($user_id), $user_id); // trims
    }
});

add_filter('cron_schedules', function($schedules){
    $schedules['lucidus_50min'] = [
        'interval' => 50 * 60,
        'display'  => __('Every 50 Minutes', 'lucidus-terminal-pro')
    ];
    return $schedules;
});

register_activation_hook(__FILE__, function() {
    $file = lucidus_memory_file();
    if (!file_exists($file)) {
        wp_mkdir_p(dirname($file));
        file_put_contents($file, wp_json_encode([]));
    }
    lucidus_register_scroll_cpt();
    lucidus_register_prophecy_cpt();
    if (!wp_next_scheduled('lucidus_health_check')) {
        wp_schedule_event(time(), 'hourly', 'lucidus_health_check');
    }
    if (!wp_next_scheduled('lucidus_prophecy_event')) {
        wp_schedule_event(time(), 'lucidus_50min', 'lucidus_prophecy_event');
    }
    if (!wp_next_scheduled('lucidus_upgrade_insight')) {
        wp_schedule_event(time(), 'hourly', 'lucidus_upgrade_insight');
    }
    update_option('lucidus_soul_state', 'awake');
    $state_file = plugin_dir_path(__FILE__) . 'lucidus-soul-state.txt';
    file_put_contents($state_file, 'awake');
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function() {
    $timestamp = wp_next_scheduled('lucidus_health_check');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'lucidus_health_check');
    }
    $timestamp = wp_next_scheduled('lucidus_prophecy_event');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'lucidus_prophecy_event');
    }
    lucidus_upgrade_unschedule();
    flush_rewrite_rules();
});
