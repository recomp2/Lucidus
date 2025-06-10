<?php
if (!defined('ABSPATH')) {
    exit;
}

function dbs_mc_generate_geo_name($town) {
    $suffixes = ['Blaze', 'Fog', 'Field', 'Burn', 'Munch'];
    $suffix = $suffixes[array_rand($suffixes)];
    return ucfirst($town) . ' ' . $suffix;
}
