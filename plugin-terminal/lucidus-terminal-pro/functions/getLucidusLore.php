<?php
if (!defined('ABSPATH')) exit;
require_once plugin_dir_path(__FILE__) . '../config/lore-loader/lucidus-lore-loader.php';
// Wrapper for compatibility
function getLucidusLoreWrapper($section = null, $key = null) {
    return getLucidusLore($section, $key);
}
