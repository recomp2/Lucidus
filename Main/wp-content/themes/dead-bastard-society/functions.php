<?php
function dbs_theme_setup(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menu('primary', __('Primary Menu'));
}
add_action('after_setup_theme', 'dbs_theme_setup');

function dbs_theme_scripts(){
    wp_enqueue_style('dbs-style', get_stylesheet_uri());
    wp_enqueue_style('dbs-dark', get_template_directory_uri() . '/assets/css/style.css');
}
add_action('wp_enqueue_scripts','dbs_theme_scripts');
