<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: AI command center for WordPress.
Version: 0.1
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

define( 'LUCIDUS_DIR', plugin_dir_path( __FILE__ ) );

// Admin menu
add_action( 'admin_menu', 'lucidus_add_admin_menu' );
function lucidus_add_admin_menu() {
    add_menu_page( 'Lucidus Terminal', 'Lucidus Terminal', 'manage_options', 'lucidus-terminal', 'lucidus_voice_settings_page' );
    add_submenu_page( 'lucidus-terminal', 'Voice Settings', 'Voice Settings', 'manage_options', 'lucidus-voice-settings', 'lucidus_voice_settings_page' );
}

function lucidus_voice_settings_page() {
    require_once LUCIDUS_DIR . 'admin/voice-settings.php';
}

// Ajax handler for testing voice
add_action( 'wp_ajax_lucidus_test_voice', 'lucidus_test_voice_handler' );
function lucidus_test_voice_handler() {
    $model  = get_option( 'lucidus_voice_model', 'nova' );
    $speed  = floatval( get_option( 'lucidus_voice_speed', 1.0 ) );
    $pitch  = floatval( get_option( 'lucidus_voice_pitch', 0 ) );
    $text   = get_option( 'lucidus_voice_prompt', 'Lucidus speaks...' );

    $api_key = get_option( 'lucidus_openai_api_key' );
    if ( empty( $api_key ) ) {
        wp_die( 'Missing API key' );
    }

    $response = wp_remote_post( 'https://api.openai.com/v1/audio/speech', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ),
        'body'    => wp_json_encode( array(
            'model' => 'tts-' . $model,
            'input' => $text,
            'voice' => $model,
            'speed' => $speed,
            'pitch' => $pitch,
        ) ),
    ) );

    header( 'Content-Type: audio/mpeg' );
    echo wp_remote_retrieve_body( $response );
    wp_die();
}
