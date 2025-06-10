<?php
// Theme setup
function dbs_theme_setup() {
    register_nav_menus([
        'primary' => __('Primary Menu', 'dead-bastard-society')
    ]);
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'dbs_theme_setup');

// Enqueue styles and scripts
function dbs_theme_assets() {
    wp_enqueue_style('dbs-style', get_stylesheet_uri());
    wp_enqueue_script('dbs-main', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], '1.0', true);
}
add_action('wp_enqueue_scripts', 'dbs_theme_assets');
?>
