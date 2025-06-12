<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/gpt-prompt-builder.php';

add_filter('lucidus_prepare_conversation', function($conversation){
    $archetype = tier_access_get_user_archetype();
    $mood = get_user_meta(get_current_user_id(), 'dbs_mood', true);
    return lucidus_build_prompt($conversation, $archetype, $mood ?: 'neutral');
});
