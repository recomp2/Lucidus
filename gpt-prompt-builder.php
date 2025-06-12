<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/lucidus-lore-loader.php';
require_once dirname(__DIR__,2) . '/functions/gptToneFormatter.php';
require_once dirname(__DIR__,2) . '/functions/lucidusArchetypeFlavor.php';

function lucidus_build_prompt($conversation, $archetype, $mood) {
    $style = gptToneFormatter($archetype, $mood);
    array_unshift($conversation, ['role' => 'system', 'content' => $style]);
    return $conversation;
}
