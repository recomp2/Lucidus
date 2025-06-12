<?php
if (!defined('ABSPATH')) exit;

function dbs_members_activate() {
    dbs_create_page('DBS Login', '[dbs_login]');
    dbs_create_page('DBS Register', '[dbs_register]');
    dbs_create_page('DBS Dashboard', '[dbs_dashboard]');
    dbs_create_page('Membership Map', '[dbs_members_map]');
}

function dbs_create_page($title, $shortcode) {
    if (!get_page_by_title($title)) {
        wp_insert_post([
            'post_title'   => $title,
            'post_name'    => sanitize_title($title),
            'post_content' => $shortcode,
            'post_status'  => 'publish',
            'post_type'    => 'page'
        ]);
    }
}
