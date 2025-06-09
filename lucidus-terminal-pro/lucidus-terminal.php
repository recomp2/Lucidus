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

require_once LUCIDUS_PRO_PATH . 'core/lucidus-terminal-core.php';
