<?php
if (!defined('ABSPATH')) exit;

function prophecy_expansion_render(){
    wp_enqueue_style('prophecy-expansion-styles', plugins_url('prophecy-expansion-styles.css', __FILE__));
    wp_enqueue_script('prophecy-expansion-scripts', plugins_url('prophecy-expansion-scripts.js', __FILE__), [], '1.0', true);
    echo '<div id="prophecy-expansion"></div>';
}
add_action('lucidus_prophecy_display', 'prophecy_expansion_render');
