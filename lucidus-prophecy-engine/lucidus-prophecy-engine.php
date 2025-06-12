<?php
/**
 * Plugin Name: Lucidus Prophecy Engine
 * Description: Generates custom prophetic scrolls combining astrology, numerology, tarot, fungal wisdom, AI parables, and Latin seals.
 * Version: 0.1.0
 * Author: Dead Bastard Society
 * License: MIT
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define plugin path constant.
define('LPE_PATH', plugin_dir_path(__FILE__));

define('LPE_URL', plugin_dir_url(__FILE__));

// Include core files.
require_once LPE_PATH . 'includes/prophecy-form.php';
require_once LPE_PATH . 'includes/prophecy-generator.php';

// Enqueue assets.
function lpe_enqueue_assets() {
    wp_enqueue_style('lpe-style', LPE_URL . 'assets/css/prophecy-style.css');
    wp_enqueue_script('lpe-js', LPE_URL . 'assets/js/prophecy-ui.js', ['jquery'], null, true);
    wp_localize_script('lpe-js', 'lpeAjax', [
        'url'   => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('lpe_prophecy')
    ]);
}
add_action('wp_enqueue_scripts', 'lpe_enqueue_assets');

// Ajax handler.
function lpe_handle_ajax() {
    check_ajax_referer('lpe_prophecy', 'nonce');
    $data = [
        'username'  => $_POST['username'] ?? '',
        'dob'       => $_POST['dob'] ?? '',
        'town'      => $_POST['town'] ?? '',
        'archetype' => $_POST['archetype'] ?? '',
        'strain'    => $_POST['strain'] ?? '',
        'question'  => $_POST['question'] ?? ''
    ];
    $prophecy = lpe_generate_prophecy($data);
    wp_send_json_success(['prophecy' => $prophecy]);
}
add_action('wp_ajax_lpe_generate', 'lpe_handle_ajax');
add_action('wp_ajax_nopriv_lpe_generate', 'lpe_handle_ajax');

// Shortcode to render prophecy form.
add_shortcode('lucidus_prophecy_form', 'lpe_render_prophecy_form');
