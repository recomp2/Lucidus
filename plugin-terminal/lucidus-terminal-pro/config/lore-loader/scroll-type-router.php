<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/lucidus-lore-loader.php';
require_once dirname(__DIR__,2) . '/functions/scrollTypeAssigner.php';

function lucidus_assign_scroll_type($context = []) {
    return scrollTypeAssigner($context);
}
