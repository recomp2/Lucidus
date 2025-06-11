<?php
if (!defined('ABSPATH')) exit;
require_once LUCIDUS_TERMINAL_DIR . 'includes/archive-writer.php';

require_once LUCIDUS_TERMINAL_DIR . 'includes/chat-handler.php';

add_action('rest_api_init', function() {
    register_rest_route('lucidus/v1', '/chat', [
        'methods' => 'POST',
        'callback' => 'lucidus_handle_chat',
        'permission_callback' => function($request) {
            return current_user_can('read') && wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest');
        }
    ]);

    register_rest_route('lucidus/v1', '/memory', [
        'methods' => 'GET',
        'callback' => function($request) {
            if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
                return new WP_Error('invalid_nonce', 'Invalid nonce', ['status' => 403]);
            }
            $user_id = get_current_user_id();
            return rest_ensure_response(lucidus_load_memory($user_id));
        },
        'permission_callback' => function() { return current_user_can('read'); }
    ]);

    register_rest_route('lucidus/v1', '/memory', [
        'methods' => 'DELETE',
        'callback' => function($request) {
            if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
                return new WP_Error('invalid_nonce', 'Invalid nonce', ['status' => 403]);
            }
            $user_id = get_current_user_id();
            lucidus_clear_memory($user_id);
            return rest_ensure_response(['cleared' => true]);
        },
        'permission_callback' => function() { return current_user_can('read'); }
    ]);

    register_rest_route('lucidus/v1', '/scrolls', [
        'methods' => 'GET',
        'callback' => function() {
            $posts = get_posts([
                'post_type' => 'scroll',
                'posts_per_page' => 5,
                'orderby' => 'date',
                'order' => 'DESC'
            ]);
            $data = [];
            foreach ($posts as $p) {
                $data[] = [
                    'id' => $p->ID,
                    'title' => $p->post_title,
                    'content' => apply_filters('the_content', $p->post_content)
                ];
            }
            return rest_ensure_response($data);
        },
        'permission_callback' => '__return_true'
    ]);

    register_rest_route('lucidus/v1', '/prophecies', [
        'methods' => 'GET',
        'callback' => function() {
            $posts = get_posts([
                'post_type' => 'prophecy',
                'posts_per_page' => -1,
                'orderby' => 'date',
                'order' => 'ASC'
            ]);
            $data = [];
            foreach ($posts as $p) {
                $data[] = [
                    'id' => $p->ID,
                    'content' => wp_strip_all_tags($p->post_content)
                ];
            }
            return rest_ensure_response($data);
        },
        'permission_callback' => '__return_true'
    ]);
    register_rest_route("lucidus/v1", "/archive", [
        "methods" => "GET",
        "callback" => function() {
            return rest_ensure_response(lucidus_get_archive_entries());
        },
        "permission_callback" => "__return_true"
    ]);
});
