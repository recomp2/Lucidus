<?php
/**
 * Plugin Name: Lucidus Terminal Pro
 * Description: Core features for the DBS universe including custom post types and quest logic.
 * Version: 0.1.0
 * Author: Lucidus Bastardo
 * License: MIT
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register custom post types.
 */
function lucidus_register_custom_post_types() {
    // Badge post type.
    register_post_type( 'badge', array(
        'labels' => array(
            'name' => 'Badges',
            'singular_name' => 'Badge'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
    ) );

    // Patch post type.
    register_post_type( 'patch', array(
        'labels' => array(
            'name' => 'Patches',
            'singular_name' => 'Patch'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array( 'title', 'editor', 'thumbnail' ),
    ) );

    // Scroll post type.
    register_post_type( 'scroll', array(
        'labels' => array(
            'name' => 'Scrolls',
            'singular_name' => 'Scroll'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
    ) );
}
add_action( 'init', 'lucidus_register_custom_post_types' );

/**
 * Template loader for custom post types.
 */
function lucidus_custom_templates( $template ) {
    if ( is_singular( 'badge' ) ) {
        $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/single-badge.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }

    if ( is_singular( 'patch' ) ) {
        $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/single-patch.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }

    if ( is_singular( 'scroll' ) ) {
        $plugin_template = plugin_dir_path( __FILE__ ) . 'templates/single-scroll.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }

    return $template;
}
add_filter( 'template_include', 'lucidus_custom_templates' );

/**
 * Simple quest logic: when a scroll is marked complete, increase user rank and
 * persist progress to /wp-content/dbs-library/.
 */
function lucidus_handle_scroll_completion( $post_id, $post, $update ) {
    if ( $post->post_type !== 'scroll' ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check if scroll was marked as complete via custom field 'scroll_completed'.
    $completed = get_post_meta( $post_id, 'scroll_completed', true );
    if ( $completed ) {
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return;
        }

        $current_rank = (int) get_user_meta( $user_id, 'lucidus_rank', true );
        $new_rank = $current_rank + 1;
        update_user_meta( $user_id, 'lucidus_rank', $new_rank );

        $progress = array(
            'user_id' => $user_id,
            'scroll_id' => $post_id,
            'rank' => $new_rank,
            'completed_at' => current_time( 'mysql' ),
        );
        lucidus_store_progress_json( $progress );
    }
}
add_action( 'save_post', 'lucidus_handle_scroll_completion', 10, 3 );

/**
 * Store progress as JSON in wp-content/dbs-library
 */
function lucidus_store_progress_json( $data ) {
    $dir = trailingslashit( ABSPATH . 'wp-content/dbs-library' );
    if ( ! wp_mkdir_p( $dir ) ) {
        return;
    }

    $filename = 'progress-' . time() . '-' . wp_generate_uuid4() . '.json';
    $filepath = $dir . $filename;
    $json = wp_json_encode( $data, JSON_PRETTY_PRINT );
    file_put_contents( $filepath, $json );
}

/**
 * Process scroll completion form submission.
 */
function lucidus_process_scroll_completion() {
    if ( isset( $_POST['complete_scroll'] ) && isset( $_POST['complete_scroll_nonce'] ) && wp_verify_nonce( $_POST['complete_scroll_nonce'], 'complete_scroll' ) ) {
        if ( is_singular( 'scroll' ) ) {
            $post_id = get_the_ID();
            update_post_meta( $post_id, 'scroll_completed', 1 );
        }
    }
}
add_action( 'template_redirect', 'lucidus_process_scroll_completion' );
