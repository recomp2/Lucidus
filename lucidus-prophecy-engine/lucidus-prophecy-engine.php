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

// Shortcode to render prophecy form.
add_shortcode('lucidus_prophecy_form', 'lpe_render_prophecy_form');
