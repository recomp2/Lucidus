<?php
/*
Plugin Name: Lucidus Prophecy Expansion Module
Description: Adds extra prophecy functionality for Lucidus.
*/
if (!defined('ABSPATH')) { exit; }

require_once __DIR__ . '/prophecy-expansion-functions.php';

add_action('lucidus_modules_loaded', function($loaded){
    // maybe log module load
});
?>
