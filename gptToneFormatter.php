<?php
if (!defined('ABSPATH')) exit;
require_once plugin_dir_path(__FILE__) . '../config/lore-loader/lucidus-lore-loader.php';

function gptToneFormatter($archetype, $mood = 'neutral', $preset = 'oracle') {
    $tone = getLucidusLore('tone_presets', $preset);
    if (!$tone) {
        $tone = ['style' => 'cryptic'];
    }
    $legend = getLucidusLore('archetype_legends', $archetype);
    $style = $tone['style'];
    if ($legend && isset($legend['tone'])) {
        $style .= ', ' . $legend['tone'];
    }
    $style .= ', mood:' . $mood;
    return 'Respond in a ' . $style . ' style.';
}
