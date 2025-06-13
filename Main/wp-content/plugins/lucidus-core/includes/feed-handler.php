<?php
if(!defined('ABSPATH')) exit;
include_once(ABSPATH . WPINC . "/feed.php");

function lucidus_feed_log($msg){
    $file = WP_CONTENT_DIR.'/dbs-library/logs/feed-log.txt';
    wp_mkdir_p(dirname($file));
    $line = current_time('mysql').' - '.$msg."\n";
    file_put_contents($file, $line, FILE_APPEND);
}

function lucidus_feed_get_settings(){
    return get_option('lucidus_feed_settings', []);
}

function lucidus_feed_fetch_entries(){
    $settings = lucidus_feed_get_settings();
    if(!function_exists('lucidus_has_internet_access') || !lucidus_has_internet_access(null,2)){
        lucidus_feed_log('Feed fetch blocked by rank');
        return [];
    }
    $urls = preg_split('/\r?\n/', isset($settings['feed_urls'])?$settings['feed_urls']:'', -1, PREG_SPLIT_NO_EMPTY);
    $entries = [];
    foreach($urls as $url){
        $feed = fetch_feed(trim($url));
        if(is_wp_error($feed)){
            lucidus_feed_log('Error fetching '.$url.': '. $feed->get_error_message());
            if(function_exists('dbs_memory_logger')){ dbs_memory_logger('Feed error: '.$url); }
            continue;
        }
        foreach($feed->get_items(0,3) as $item){
            $entries[] = [
                'title' => $item->get_title(),
                'summary' => wp_strip_all_tags($item->get_description())
            ];
        }
    }
    lucidus_feed_log('Fetched '.count($entries).' entries');
    return $entries;
}

function lucidus_feed_inject_memory($entries){
    $file = WP_CONTENT_DIR.'/dbs-library/memory-archive/system.json';
    $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    foreach($entries as $e){
        $data[] = ['time'=>current_time('mysql'),'feed'=>$e];
    }
    file_put_contents($file, json_encode($data));
    lucidus_feed_log('Injected '.count($entries).' entries to memory');
    if(function_exists('dbs_memory_logger')){ dbs_memory_logger('Feed injected '.count($entries).' entries'); }
}
