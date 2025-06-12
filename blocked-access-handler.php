<?php
if (!defined('ABSPATH')) exit;

function tier_access_denied_message($feature, $user_id = null) {
    $msg = tier_access_get_deny_response($user_id);
    return '<p>' . esc_html($msg) . '</p>';
}
