<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_track_user( $tag ) {
    $user_id  = get_current_user_id();
    $ip       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    $location = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';

    lucidus_log( 'user_interaction', array(
        'user_id'  => $user_id,
        'ip'       => $ip,
        'location' => $location,
        'tag'      => sanitize_text_field( $tag ),
    ) );
}
