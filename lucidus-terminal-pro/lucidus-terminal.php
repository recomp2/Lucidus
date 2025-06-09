<?php
/**
 * Plugin Name: Lucidus Terminal Pro
 * Description: AI-powered command center for WordPress.
 * Version: 1.0.0
 * Author: Dr.G and Lucidus Bastardo
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin path
if ( ! defined( 'LUCIDUS_PRO_PATH' ) ) {
    define( 'LUCIDUS_PRO_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'LUCIDUS_PRO_URL' ) ) {
    define( 'LUCIDUS_PRO_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'LUCIDUS_PRO_VERSION' ) ) {
    define( 'LUCIDUS_PRO_VERSION', '1.0.0' );
}

register_activation_hook( __FILE__, 'lucidus_pro_activate' );
function lucidus_pro_activate() {
    add_option( 'lucidus_openai_key', '' );
    add_option( 'lucidus_elevenlabs_key', '' );
}

require_once LUCIDUS_PRO_PATH . 'core/lucidus-terminal-core.php';
