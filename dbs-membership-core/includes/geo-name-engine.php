<?php
/**
 * Dead Bastard Society Membership Core
 * Geo name registration engine
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

function dbs_load_geos(){
    $file = DBS_LIBRARY_DIR . 'geos.json';
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function dbs_save_geos($geos){
    $file = DBS_LIBRARY_DIR . 'geos.json';
    file_put_contents($file, json_encode($geos));
}

function dbs_geo_exists($city, $state) {
    $geos = dbs_load_geos();
    $key = strtolower("$city,$state");
    return isset($geos[$key]);
}

function dbs_register_geo_pending($city, $state, $username){
    $geos = dbs_load_geos();
    $key = strtolower("$city,$state");
    if (!isset($geos[$key]) || (isset($geos[$key]['status']) && $geos[$key]['status']==='pending' && time()-$geos[$key]['timestamp']>DAY_IN_SECONDS)){
        $geos[$key] = [
            'city'=>$city,
            'state'=>$state,
            'status'=>'pending',
            'pending_user'=>$username,
            'timestamp'=>time()
        ];
        dbs_save_geos($geos);
        return true;
    }
    return false;
}

function dbs_claim_geo($city,$state,$chapter,$username){
    $geos = dbs_load_geos();
    $key = strtolower("$city,$state");
    $geos[$key] = [
        'name'=>$chapter,
        'city'=>$city,
        'state'=>$state,
        'founder'=>$username,
        'status'=>'final',
        'timestamp'=>time()
    ];
    dbs_save_geos($geos);
}

function dbs_update_geo($city,$state,$chapter){
    $geos = dbs_load_geos();
    $key = strtolower("$city,$state");
    if(isset($geos[$key])){
        $geos[$key]['name']=$chapter;
        dbs_save_geos($geos);
    }
}

function dbs_remove_geo($city,$state){
    $geos = dbs_load_geos();
    $key = strtolower("$city,$state");
    if(isset($geos[$key])){
        unset($geos[$key]);
        dbs_save_geos($geos);
    }
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
