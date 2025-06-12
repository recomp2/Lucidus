<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/tier-check.php';
require_once __DIR__ . '/access-log.php';
require_once __DIR__ . '/blocked-access-handler.php';

// Intercept REST requests
add_filter('rest_pre_dispatch', function($result, $server, $request){
    $route = $request->get_route();
    $user_id = get_current_user_id();
    if (0 === strpos($route, '/lucidus/v1/chat')) {
        if (!tier_access_has_feature('chat', $user_id)) {
            tier_access_log($user_id, 'chat', false);
            return new WP_Error('forbidden', tier_access_get_deny_response($user_id), ['status'=>403]);
        }
        if (tier_access_get_user_rank($user_id) === 'initiate') {
            tier_access_log($user_id, 'chat', false);
            return new WP_Error('forbidden', tier_access_get_deny_response($user_id), ['status'=>403]);
        }
        tier_access_log($user_id, 'chat', true);
    } elseif (0 === strpos($route, '/lucidus/v1/prophecies')) {
        if (!tier_access_has_feature('prophecy', $user_id)) {
            tier_access_log($user_id, 'prophecy', false);
            return new WP_Error('forbidden', tier_access_get_deny_response($user_id), ['status'=>403]);
        }
        tier_access_log($user_id, 'prophecy', true);
    }
    return $result;
}, 10, 3);

// Override prophecy feed shortcode
add_action('init', function(){
    remove_shortcode('lucidus_prophecy_feed');
    add_shortcode('lucidus_prophecy_feed', function($atts){
        $user_id = get_current_user_id();
        if (!tier_access_has_feature('prophecy', $user_id)) {
            tier_access_log($user_id, 'prophecy', false);
            return tier_access_denied_message('prophecy', $user_id);
        }
        tier_access_log($user_id, 'prophecy', true);
        return lucidus_prophecy_feed($atts);
    });

    remove_shortcode('lucidus_terminal');
    add_shortcode('lucidus_terminal', function($atts){
        $user_id = get_current_user_id();
        if (!tier_access_has_feature('chat', $user_id)) {
            tier_access_log($user_id, 'chat', false);
            return tier_access_denied_message('chat', $user_id);
        }
        tier_access_log($user_id, 'chat', true);
        return lucidus_terminal_shortcode($atts);
    });

    remove_shortcode('dbs_members_map');
    add_shortcode('dbs_members_map', function($atts){
        $user_id = get_current_user_id();
        if (!tier_access_has_feature('map', $user_id)) {
            tier_access_log($user_id, 'map', false);
            return tier_access_denied_message('map', $user_id);
        }
        tier_access_log($user_id, 'map', true);
        return dbs_members_map_shortcode($atts);
    });
});
