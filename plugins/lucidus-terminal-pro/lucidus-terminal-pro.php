<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: AI terminal interface for DBS.
Version: 1.1
Author: Dr.G
License: MIT
*/

// Shortcode: lucidus terminal
function lucidus_terminal_shortcode() {
    ob_start();
    ?>
    <div id="lucidus-terminal">
        <textarea id="lucidus-input" rows="4" cols="50"></textarea>
        <button id="lucidus-send">Send</button>
        <pre id="lucidus-response"></pre>
    </div>
    <script type="text/javascript">
    jQuery(function($){
        $('#lucidus-send').on('click', function(){
            var msg = $('#lucidus-input').val();
            $.post(ajaxurl, {action:'lucidus_memory_pull', msg:msg}, function(r){
                $('#lucidus-response').text(r);
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('lucidus_terminal','lucidus_terminal_shortcode');

// Include group chat template
function lucidus_terminal_group_shortcode() {
    ob_start();
    include dirname(__FILE__) . '/group-terminal.php';
    return ob_get_clean();
}
add_shortcode('lucidus_group_terminal','lucidus_terminal_group_shortcode');

// AJAX handler
add_action('wp_ajax_lucidus_memory_pull','lucidus_memory_pull');
add_action('wp_ajax_nopriv_lucidus_memory_pull','lucidus_memory_pull');
function lucidus_memory_pull(){
    echo 'Echo: '.sanitize_text_field($_POST['msg']);
    wp_die();
}
?>
