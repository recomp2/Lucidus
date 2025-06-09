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
