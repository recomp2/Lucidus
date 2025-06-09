<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_ftp_list( $endpoint ) {
    $response = wp_remote_get( $endpoint );
    if ( is_wp_error( $response ) ) {
        lucidus_log( 'ftp_list_error', $response->get_error_message() );
        return array();
    }
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    return is_array( $data ) ? $data : array();
}

function lucidus_ftp_get( $endpoint, $file ) {
    $response = wp_remote_get( add_query_arg( 'file', $file, $endpoint ) );
    if ( is_wp_error( $response ) ) {
        lucidus_log( 'ftp_get_error', $response->get_error_message() );
        return '';
    }
    return wp_remote_retrieve_body( $response );
}

function lucidus_ftp_put( $endpoint, $file, $content ) {
    $args = array(
        'body'    => array(
            'file'    => $file,
            'content' => $content,
        ),
        'timeout' => 20,
    );
    $response = wp_remote_post( $endpoint, $args );
    if ( is_wp_error( $response ) ) {
        lucidus_log( 'ftp_put_error', $response->get_error_message() );
        return false;
    }
    return true;
}
