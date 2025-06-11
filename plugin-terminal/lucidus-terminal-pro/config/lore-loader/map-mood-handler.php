<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/lucidus-lore-loader.php';

function lucidus_map_get_style($mood) {
    $effects = getLucidusLore('map_lore', 'mood_effects');
    return $effects[$mood] ?? $effects['neutral'];
}
