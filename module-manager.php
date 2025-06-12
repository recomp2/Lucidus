<?php
if (!defined('ABSPATH')) exit;

if (!defined('LUCIDUS_MODULES_DIR')) {
    define('LUCIDUS_MODULES_DIR', dirname(LUCIDUS_TERMINAL_DIR) . '/modules/');
}

function lucidus_modules_loaded() {
    $dirs = glob(LUCIDUS_MODULES_DIR . '*', GLOB_ONLYDIR);
    $loaded = [];
    if ($dirs) {
        foreach ($dirs as $dir) {
            $slug = basename($dir);
            $init = trailingslashit($dir) . $slug . '-init.php';
            if (file_exists($init)) {
                include_once $init;
                $loaded[] = $slug;
            }
        }
    }
    do_action('lucidus_modules_loaded', $loaded);
}
add_action('plugins_loaded', 'lucidus_modules_loaded', 15);
