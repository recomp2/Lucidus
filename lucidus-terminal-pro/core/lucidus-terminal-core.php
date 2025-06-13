<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Register admin menus for Lucidus Terminal.
 */
function lucidus_register_admin_menu() {
    add_menu_page(
        'Lucidus Terminal',
        'Lucidus Terminal',
        'manage_options',
        'lucidus-terminal',
        'lucidus_render_status_page',
        'dashicons-editor-code',
        81
    );

    add_submenu_page(
        'lucidus-terminal',
        'Status',
        'Status',
        'manage_options',
        'lucidus-terminal-status',
        'lucidus_render_status_page'
    );

    add_submenu_page(
        'lucidus-terminal',
        'Scripts',
        'Scripts',
        'manage_options',
        'lucidus-terminal-scripts',
        'lucidus_render_scripts_page'
    );

    add_submenu_page(
        'lucidus-terminal',
        'Settings',
        'Settings',
        'manage_options',
        'lucidus-terminal-settings',
        'lucidus_render_settings_page'
    );
}
add_action( 'admin_menu', 'lucidus_register_admin_menu' );

/**
 * Render the Status page.
 */
function lucidus_render_status_page() {
    include LUCIDUS_PRO_PATH . 'admin/templates/status.php';
}

/**
 * Render the Scripts page.
 */
function lucidus_render_scripts_page() {
    include LUCIDUS_PRO_PATH . 'admin/templates/scripts.php';
}

/**
 * Render the Settings page.
 */
function lucidus_render_settings_page() {
    include LUCIDUS_PRO_PATH . 'admin/templates/settings.php';
}

/**
 * Handle settings form submission.
 */
function lucidus_handle_settings_save() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized user' );
    }

    check_admin_referer( 'lucidus_save_settings' );

    $openai_key   = isset( $_POST['lucidus_openai_key'] ) ? sanitize_text_field( $_POST['lucidus_openai_key'] ) : '';
    $eleven_key   = isset( $_POST['lucidus_elevenlabs_key'] ) ? sanitize_text_field( $_POST['lucidus_elevenlabs_key'] ) : '';

    update_option( 'lucidus_openai_key', $openai_key );
    update_option( 'lucidus_elevenlabs_key', $eleven_key );

    wp_redirect( add_query_arg( 'updated', 'true', admin_url( 'admin.php?page=lucidus-terminal-settings' ) ) );
    exit;
}
add_action( 'admin_post_lucidus_save_settings', 'lucidus_handle_settings_save' );

/**
 * Handle script execution form submission.
 */
function lucidus_handle_run_script() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized user' );
    }

    check_admin_referer( 'lucidus_run_script' );

    $command = isset( $_POST['lucidus_script_command'] ) ? sanitize_text_field( $_POST['lucidus_script_command'] ) : '';

    set_transient( 'lucidus_last_command', $command, MINUTE_IN_SECONDS );

    wp_redirect( add_query_arg( 'command', urlencode( $command ), admin_url( 'admin.php?page=lucidus-terminal-scripts' ) ) );
    exit;
}
add_action( 'admin_post_lucidus_run_script', 'lucidus_handle_run_script' );
