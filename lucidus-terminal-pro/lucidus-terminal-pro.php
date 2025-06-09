<?php
/**
 * Plugin Name: Lucidus Terminal Pro
 * Description: Voice + memory + GPT integrated AI panel for the DBS Universe.
 * Version: 1.0.0
 * Author: Dr.G & Lucidus Bastardo
 * License: MIT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'LUCIDUS_TERMINAL_PRO_PATH', plugin_dir_path( __FILE__ ) );
define( 'LUCIDUS_TERMINAL_PRO_URL', plugin_dir_url( __FILE__ ) );

require_once LUCIDUS_TERMINAL_PRO_PATH . 'includes/logger.php';
require_once LUCIDUS_TERMINAL_PRO_PATH . 'includes/openai-client.php';
require_once LUCIDUS_TERMINAL_PRO_PATH . 'includes/ftp-manager.php';
require_once LUCIDUS_TERMINAL_PRO_PATH . 'includes/memory-manager.php';
require_once LUCIDUS_TERMINAL_PRO_PATH . 'includes/user-tracker.php';

function lucidus_terminal_admin_menu() {
    add_menu_page( 'Lucidus Terminal', 'Lucidus Terminal', 'manage_options', 'lucidus-dashboard', 'lucidus_dashboard_page', 'dashicons-terminal' );
    add_submenu_page( 'lucidus-dashboard', 'Dashboard', 'Dashboard', 'manage_options', 'lucidus-dashboard', 'lucidus_dashboard_page' );
    add_submenu_page( 'lucidus-dashboard', 'Chat Terminal', 'Chat Terminal', 'manage_options', 'lucidus-chat-terminal', 'lucidus_chat_terminal_page' );
    add_submenu_page( 'lucidus-dashboard', 'Diagnostics', 'Diagnostics', 'manage_options', 'lucidus-diagnostics', 'lucidus_diagnostics_page' );
    add_submenu_page( 'lucidus-dashboard', 'File Sync', 'File Sync', 'manage_options', 'lucidus-file-sync', 'lucidus_file_sync_page' );
    add_submenu_page( 'lucidus-dashboard', 'Memory Live', 'Memory Live', 'manage_options', 'lucidus-memory-live', 'lucidus_memory_live_page' );
    add_submenu_page( 'lucidus-dashboard', 'Phrase Forge', 'Phrase Forge', 'manage_options', 'lucidus-phrase-forge', 'lucidus_phrase_forge_page' );
    add_submenu_page( 'lucidus-dashboard', 'Voice Settings', 'Voice Settings', 'manage_options', 'lucidus-voice-settings', 'lucidus_voice_settings_page' );
}
add_action( 'admin_menu', 'lucidus_terminal_admin_menu' );

function lucidus_dashboard_page() { require_once LUCIDUS_TERMINAL_PRO_PATH . 'admin/dashboard.php'; }
function lucidus_chat_terminal_page() { require_once LUCIDUS_TERMINAL_PRO_PATH . 'admin/chat-terminal.php'; }
function lucidus_diagnostics_page() { require_once LUCIDUS_TERMINAL_PRO_PATH . 'admin/diagnostics.php'; }
function lucidus_file_sync_page() { require_once LUCIDUS_TERMINAL_PRO_PATH . 'admin/file-sync.php'; }
function lucidus_memory_live_page() { require_once LUCIDUS_TERMINAL_PRO_PATH . 'admin/memory-live.php'; }
function lucidus_phrase_forge_page() { require_once LUCIDUS_TERMINAL_PRO_PATH . 'admin/phrase-forge.php'; }
function lucidus_voice_settings_page() { require_once LUCIDUS_TERMINAL_PRO_PATH . 'admin/voice-settings.php'; }

function lucidus_chat_ajax() {
    check_ajax_referer( 'lucidus_chat', 'nonce' );
    $message = sanitize_text_field( $_POST['message'] );
    lucidus_track_user( 'chat' );
    $api_key = get_option( 'lucidus_openai_key', '' );
    $memory = lucidus_inject_memory();
    $messages = [
        [ 'role' => 'system', 'content' => $memory ],
        [ 'role' => 'user', 'content' => $message ],
    ];
    $reply = lucidus_openai_chat( $messages, $api_key );
    wp_send_json_success( $reply );
}
add_action( 'wp_ajax_lucidus_chat', 'lucidus_chat_ajax' );
