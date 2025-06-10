<?php
/**
 * Dead Bastard Society Membership Core
 * Scroll list shortcode
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_shortcode('dbs_scroll_list', 'dbs_scroll_list_shortcode');

function dbs_scroll_list_shortcode(){
    $base = DBS_LIBRARY_DIR . 'scrolls/';
    $out = '<ul class="dbs-scroll-list">';
    if (is_dir($base)){
        $states = glob($base.'*', GLOB_ONLYDIR);
        foreach ($states as $stateDir){
            $state = basename($stateDir);
            foreach (glob($stateDir.'/*.json') as $file){
                $data = json_decode(file_get_contents($file), true);
                if (!$data) continue;
                $chapter = esc_html($data['chapter']);
                $city = esc_html($data['city']);
                $out .= "<li>$chapter - $city, $state</li>";
            }
        }
    }
    return $out.'</ul>';
}
