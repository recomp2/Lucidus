<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: The AI-powered command center of the Dead Bastard Society Universe.
Version: 0.1.0
Author: Lucidus Bastardo
*/

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Activation hook to create privacy policy and terms of service pages.
 */
function lucidus_activation() {
    // Privacy Policy page
    $privacy_id = wp_insert_post( array(
        'post_title'     => 'Privacy Policy',
        'post_content'   => "<h2>Privacy Policy</h2><p>Your privacy matters.</p>",
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_name'      => 'privacy-policy',
    ) );
    if ( $privacy_id ) {
        update_option( 'lucidus_privacy_page_id', $privacy_id );
    }

    // Terms of Service page
    $terms_id = wp_insert_post( array(
        'post_title'     => 'Terms of Service',
        'post_content'   => "<h2>Terms of Service</h2><p>The rules that bind us all.</p>",
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'post_name'      => 'terms-of-service',
    ) );
    if ( $terms_id ) {
        update_option( 'lucidus_terms_page_id', $terms_id );
    }
}
register_activation_hook( __FILE__, 'lucidus_activation' );

/**
 * Shortcode for initiation form with consent checkbox.
 */
function lucidus_render_initiation_form() {
    $privacy_id = get_option( 'lucidus_privacy_page_id' );
    $terms_id   = get_option( 'lucidus_terms_page_id' );
    $privacy_url = $privacy_id ? get_permalink( $privacy_id ) : '#';
    $terms_url   = $terms_id ? get_permalink( $terms_id ) : '#';

    ob_start();
    ?>
    <form class="lucidus-initiation-form" method="post">
        <label>
            <input type="checkbox" name="lucidus_consent" required>
            I agree to the
            <a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank">Privacy Policy</a>
            and
            <a href="<?php echo esc_url( $terms_url ); ?>" target="_blank">Terms of Service</a>.
        </label>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'lucidus_initiation_form', 'lucidus_render_initiation_form' );
