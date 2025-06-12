<?php
function dbs_theme_setup() {
    add_theme_support('title-tag');
    register_nav_menus([
        'primary' => __('Primary Menu', 'dead-bastard-society-theme')
    ]);
    load_theme_textdomain('dead-bastard-society-theme', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'dbs_theme_setup');

function dbs_enqueue_styles() {
    wp_enqueue_style('dbs-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'dbs_enqueue_styles');

function dbs_enqueue_page_styles() {
    if (is_page_template('page-lucidus-chat.php')) {
        wp_enqueue_style('dbs-lucidus-chat', get_template_directory_uri() . '/style-lucidus-chat.css');
    } elseif (is_page_template('page-420-scrollfeed.php')) {
        wp_enqueue_style('dbs-scrollfeed', get_template_directory_uri() . '/style-420-scrollfeed.css');
    } elseif (is_page_template('page-lucidus-profile.php')) {
        wp_enqueue_style('dbs-profile', get_template_directory_uri() . '/style-lucidus-profile.css');
    } elseif (is_page_template('page-lucidus-chapters.php')) {
        wp_enqueue_style('dbs-chapters', get_template_directory_uri() . '/style-lucidus-chapters.css');
    } elseif (is_page_template('page-lucidus-settings.php')) {
        wp_enqueue_style('dbs-settings', get_template_directory_uri() . '/style-lucidus-settings.css');
    }
}
add_action('wp_enqueue_scripts', 'dbs_enqueue_page_styles');
function dbs_enqueue_pwa() {
    wp_enqueue_script('dbs-pwa', get_template_directory_uri() . '/pwa-wrapper.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts', 'dbs_enqueue_pwa');
