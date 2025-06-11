<?php
if (!defined('ABSPATH')) exit;

function dbs_members_locations() {
    $users = get_users([
        'meta_key' => 'town_lat',
        'meta_compare' => 'EXISTS',
        'fields' => ['ID', 'display_name']
    ]);
    $data = [];
    foreach ($users as $u) {
        $lat = get_user_meta($u->ID, 'town_lat', true);
        $lng = get_user_meta($u->ID, 'town_lng', true);
        if ($lat && $lng) {
            $data[] = [
                'id' => $u->ID,
                'name' => $u->display_name,
                'lat' => (float) $lat,
                'lng' => (float) $lng
            ];
        }
    }
    return rest_ensure_response($data);
}

function dbs_members_map_shortcode() {
    wp_enqueue_style('leaflet');
    wp_enqueue_style('dbs-members-map');
    wp_enqueue_script('leaflet');
    wp_enqueue_script('dbs-members-map');
    return '<div id="dbs-members-map" style="height:400px"></div>';
}
add_shortcode('dbs_members_map', 'dbs_members_map_shortcode');

add_action('rest_api_init', function(){
    register_rest_route('dbs-members/v1', '/locations', [
        'methods'  => 'GET',
        'callback' => 'dbs_members_locations',
        'permission_callback' => function() {
            return current_user_can('read');
        }
    ]);
});

add_action('wp_enqueue_scripts', function(){
    if (has_shortcode(get_post()->post_content ?? '', 'dbs_members_map')) {
        wp_register_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        wp_register_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);
        wp_register_style('dbs-members-map', plugins_url('assets/css/map.css', __FILE__));
        wp_register_script('dbs-members-map', plugins_url('assets/js/map.js', __FILE__), ['leaflet'], '1.0', true);
    }
});
