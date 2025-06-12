<?php
/*
Plugin Name: DBS Membership Core
Description: Membership signup and management for Dead Bastard Society.
Version: 0.1
Author: Dr.G and Lucidus Bastardo
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DBS_MEMBERSHIP_CORE_VERSION', '0.1' );

// Shortcode for signup form
function dbs_membership_form() {
    return '<form id="dbs-signup"><input type="email" name="email" placeholder="Email" /><button type="submit">Join</button></form>';
}
add_shortcode( 'dbs_membership_signup', 'dbs_membership_form' );

// REST endpoint for signup
function dbs_register_endpoints() {
    register_rest_route( 'dbs/v1', '/signup', array(
        'methods'  => 'POST',
        'callback' => 'dbs_handle_signup',
        'permission_callback' => '__return_true',
    ) );
}
add_action( 'rest_api_init', 'dbs_register_endpoints' );

function dbs_handle_signup( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    return rest_ensure_response( array( 'signup' => $params ) );
}
