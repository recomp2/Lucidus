<?php
/**
 * Dead Bastard Society Membership Core
 * Geo name registration engine
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

function dbs_register_geo($city, $state) {
    $geofile = DBS_MEMBERSHIP_DIR . 'memory-archive/geos.json';
    $geos = file_exists($geofile) ? json_decode(file_get_contents($geofile), true) : [];
    $key = strtolower("$city,$state");
    if (!isset($geos[$key])) {
        $geos[$key] = ['name' => $city, 'state' => $state];
        file_put_contents($geofile, json_encode($geos));
        return true; // new town
    }
    return false; // existing
}
