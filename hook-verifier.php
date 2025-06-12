<?php
/**
 * Simple hook verification script.
 * Run within WordPress context to ensure actions are registered.
 */

if (!defined('ABSPATH')) {
    exit("Run within WordPress.");
}

$required_hooks = [
    'wp_login',
    'wp_logout',
    'user_register',
    'init',
    'admin_init',
    'save_post_scroll',
    'comment_post',
    'profile_update',
    'template_redirect',
    'rest_api_init',
    'lucidus_scroll_submitted',
    'lucidus_mood_logged',
    'lucidus_420_session_joined',
    'lucidus_tag_assigned',
    'lucidus_user_promoted',
    'shutdown',
    'wp_footer',
    'heartbeat_received',
    'lucidus_module_loaded'
];

$missing = [];
foreach ($required_hooks as $hook) {
    if (!has_action($hook)) {
        $missing[] = $hook;
    }
}

if ($missing) {
    echo 'Missing hooks: ' . implode(', ', $missing);
} else {
    echo 'All hooks registered.';
}
?>
