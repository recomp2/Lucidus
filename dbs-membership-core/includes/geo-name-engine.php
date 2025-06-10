<?php
/**
 * Dead Bastard Society Membership Core
 * Geo name registration engine
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

function dbs_geo_exists($city, $state) {
    $geofile = DBS_LIBRARY_DIR . 'geos.json';
    $geos = file_exists($geofile) ? json_decode(file_get_contents($geofile), true) : [];
    $key = strtolower("$city,$state");
    return isset($geos[$key]);
}

function dbs_register_geo($city, $state, $chapter) {
    $geofile = DBS_LIBRARY_DIR . 'geos.json';
    $geos = file_exists($geofile) ? json_decode(file_get_contents($geofile), true) : [];
    $key = strtolower("$city,$state");
    if (!isset($geos[$key])) {
        $geos[$key] = ['name' => $chapter, 'city' => $city, 'state' => $state];
        file_put_contents($geofile, json_encode($geos));
        return true; // new town
    }
    return false; // existing
}

function dbs_generate_geo_options($city) {
    $options = [
        $city . ' Bastion',
        'Fort ' . $city,
        'New ' . $city
    ];
    shuffle($options);
    return array_slice($options, 0, 3);
}

function dbs_write_scroll($state, $city, $chapter, $founder) {
    $dir = DBS_LIBRARY_DIR . 'scrolls/' . sanitize_title($state) . '/';
    wp_mkdir_p($dir);
    $path = $dir . sanitize_title($city) . '.json';
    $data = [
        'chapter' => $chapter,
        'city' => $city,
        'state' => $state,
        'founder' => $founder,
        'created' => current_time('mysql')
    ];
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    return $path;
}
