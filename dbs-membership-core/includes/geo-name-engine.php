<?php
if (!defined('ABSPATH')) {
    exit;
}

function dbs_mc_generate_geo_name($town) {
    $suffixes = ['Blaze', 'Fog', 'Field', 'Burn', 'Munch'];
    $suffix = $suffixes[array_rand($suffixes)];
    return ucfirst($town) . ' ' . $suffix;
}

function dbs_mc_store_geo_claim($state, $county, $name) {
    $upload_dir = wp_upload_dir();
    $dir = trailingslashit($upload_dir['basedir']) . "dbs-library/memory-archive/geo/$state/";
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }
    $file = $dir . $county . '.json';
    file_put_contents($file, wp_json_encode(['geo_name' => $name]));
}
