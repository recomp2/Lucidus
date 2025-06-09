<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'LUCIDUS_MEMORY_DIR' ) ) {
    define( 'LUCIDUS_MEMORY_DIR', WP_CONTENT_DIR . '/dbs-library/memory-archive/' );
}

function lucidus_list_memory_files() {
    $fs = lucidus_get_filesystem();
    if ( ! $fs->exists( LUCIDUS_MEMORY_DIR ) ) {
        $fs->mkdir( LUCIDUS_MEMORY_DIR );
    }
    $dir   = $fs->dirlist( LUCIDUS_MEMORY_DIR );
    $files = array();
    if ( $dir ) {
        foreach ( $dir as $file => $info ) {
            if ( 'f' === $info['type'] ) {
                $files[] = $file;
            }
        }
    }
    return $files;
}

function lucidus_load_memory( $file ) {
    $fs   = lucidus_get_filesystem();
    $path = LUCIDUS_MEMORY_DIR . sanitize_file_name( $file );
    if ( $fs->exists( $path ) ) {
        return $fs->get_contents( $path );
    }
    return '';
}

function lucidus_save_memory( $file, $content ) {
    $fs   = lucidus_get_filesystem();
    $path = LUCIDUS_MEMORY_DIR . sanitize_file_name( $file );
    $fs->put_contents( $path, wp_kses_post( $content ) );
    return $path;
}

function lucidus_clear_memory() {
    $fs    = lucidus_get_filesystem();
    $files = lucidus_list_memory_files();
    foreach ( $files as $file ) {
        $fs->delete( LUCIDUS_MEMORY_DIR . $file );
    }
}

function lucidus_inject_memory() {
    $files   = lucidus_list_memory_files();
    $content = '';
    foreach ( $files as $file ) {
        $content .= "\n" . lucidus_load_memory( $file );
    }
    return $content;
}
