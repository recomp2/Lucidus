<?php
if (!defined('ABSPATH')) exit;

function lucidus_load_lore_manifest() {
    static $lore = null;
    if ($lore !== null) return $lore;
    $file = plugin_dir_path(__DIR__) . 'lucidus-lore-manifest.json';
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        if (is_array($data)) {
            $lore = $data;
            return $lore;
        }
    }
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Lucidus lore manifest missing or invalid.');
    }
    $lore = ['tone_presets'=>['oracle'=>['style'=>'cryptic']]];
    return $lore;
}

function getLucidusLore($section = null, $key = null) {
    $lore = lucidus_load_lore_manifest();
    if ($section === null) return $lore;
    if (!isset($lore[$section])) return null;
    if ($key === null) return $lore[$section];
    return $lore[$section][$key] ?? null;
}
