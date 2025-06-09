<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_get_filesystem() {
    global $wp_filesystem;
    if ( ! $wp_filesystem ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }
    return $wp_filesystem;
}

function lucidus_log( $action, $data = array() ) {
    $fs = lucidus_get_filesystem();
    if ( ! $fs ) {
        return;
    }
    $upload = wp_upload_dir();
    $path   = trailingslashit( $upload['basedir'] ) . 'lucidus-log.json';
    $log    = array();
    if ( $fs->exists( $path ) ) {
        $content = $fs->get_contents( $path );
        if ( $content ) {
            $log = json_decode( $content, true );
            if ( ! is_array( $log ) ) {
                $log = array();
            }
        }
    }
    $log[] = array(
        'time'   => current_time( 'mysql' ),
        'action' => $action,
        'data'   => $data,
    );
    $fs->put_contents( $path, wp_json_encode( $log ) );
}
