<?php
if (!defined('ABSPATH')) exit;
require_once plugin_dir_path(__FILE__) . '../config/lore-loader/lucidus-lore-loader.php';

function lucidusArchetypeFlavor($archetype) {
    $legend = getLucidusLore('archetype_legends', $archetype);
    return $legend['tone'] ?? '';
}
