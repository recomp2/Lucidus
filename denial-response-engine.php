<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/lucidus-lore-loader.php';

function lucidus_get_denial_message($reason = 'default') {
    $messages = getLucidusLore('denial_messages');
    return $messages[$reason] ?? $messages['default'];
}
