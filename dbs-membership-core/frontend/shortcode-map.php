<?php
/**
 * Dead Bastard Society Membership Core
 * Geo map shortcode
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_shortcode('dbs_geo_map', 'dbs_geo_map_shortcode');

function dbs_geo_map_shortcode(){
    $geofile = DBS_LIBRARY_DIR . 'geos.json';
    $geos = file_exists($geofile) ? json_decode(file_get_contents($geofile), true) : [];
    $data = array_values($geos);
    wp_enqueue_script('dbs-map-generator', DBS_MEMBERSHIP_URL.'assets/map-generator.js', array(), '1.0', true);
    wp_localize_script('dbs-map-generator', 'dbsGeoData', $data);
    $google = 'https://maps.googleapis.com/maps/api/js?key='.apply_filters('dbs_maps_key','');
    wp_enqueue_script('google-maps', $google, array(), null, true);
    return '<div id="dbs-map" style="height:400px;"></div><script>google.maps.event.addDomListener(window, "load", function(){initDbsMap(dbsGeoData, "dbs-map");});</script>';
}
