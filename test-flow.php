<?php
/**
 * WordPress flow test script for DBS suite
 */
require_once dirname(__FILE__) . '/wp-load.php';

function dbs_run_full_flow() {
    $username = 'dbs_test_user';
    $email    = 'dbs_test_user@example.com';
    $password = 'TempPass123!';

    if (!username_exists($username) && !email_exists($email)) {
        $user_id = wp_create_user($username, $password, $email);
        echo "Created user ID: $user_id\n";
    } else {
        $user   = get_user_by('login', $username);
        $user_id = $user->ID;
        echo "Using existing user ID: $user_id\n";
    }

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    echo "Logged in as $username\n";

    $post_id = wp_insert_post([
        'post_title'   => 'Test Scroll',
        'post_content' => 'This is a test scroll entry.',
        'post_status'  => 'publish',
        'post_author'  => $user_id,
    ]);
    echo "Created scroll post ID: $post_id\n";

    $request = new WP_REST_Request('POST', '/lucidus/v1/chat');
    $request->set_param('message', 'Hello, Lucidus!');
    $response = rest_do_request($request);
    $data = $response->get_data();
    if (!empty($data['reply'])) {
        echo "Lucidus replied: {$data['reply']}\n";
    } else {
        echo "Lucidus chat failed\n";
    }

    $memory_request = new WP_REST_Request('GET', '/lucidus/v1/memory');
    $memory_response = rest_do_request($memory_request);
    $memory = $memory_response->get_data();
    echo "Memory entries: " . count($memory) . "\n";

    update_user_meta($user_id, 'dbs_badge', 'intrepid-adventurer');
    echo "Badge awarded: " . get_user_meta($user_id, 'dbs_badge', true) . "\n";
}

dbs_run_full_flow();
