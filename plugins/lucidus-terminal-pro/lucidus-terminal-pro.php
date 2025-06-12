<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: AI-powered terminal and chat for Dead Bastard Society.
Version: 0.1
Author: Dr.G and Lucidus Bastardo
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'LUCIDUS_TERMINAL_PRO_VERSION', '0.1' );

defaults();
function defaults() {
    // placeholder for core initialization
}

// Shortcode to output initiation form
function ltp_initiation_form() {
    ob_start();
    ?>
    <form id="ltp-initiation">
        <input type="text" name="message" placeholder="Speak to Lucidus" />
        <button type="submit">Send</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'lucidus_terminal', 'ltp_initiation_form' );

// REST endpoint
function ltp_register_endpoints() {
    register_rest_route( 'lucidus/v1', '/initiate', array(
        'methods'  => 'POST',
        'callback' => 'ltp_handle_initiate',
        'permission_callback' => '__return_true',
    ) );
}
add_action( 'rest_api_init', 'ltp_register_endpoints' );

function ltp_handle_initiate( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    return rest_ensure_response( array( 'received' => $params ) );
}
