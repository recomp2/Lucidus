<?php
/*
Plugin Name: Lucidus Terminal Pro
Description: Custom post types and quest system integration.
Version: 0.1.0
Author: Lucidus Bastardo
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register custom post types: badge, patch, scroll.
 */
function lucidus_register_cpts() {
    $common = array(
        'public'       => true,
        'show_in_menu' => true,
        'supports'     => array( 'title', 'editor', 'thumbnail' ),
    );

    register_post_type( 'badge', array_merge( $common, array(
        'label' => 'Badges',
    ) ) );

    register_post_type( 'patch', array_merge( $common, array(
        'label' => 'Patches',
    ) ) );

    register_post_type( 'scroll', array_merge( $common, array(
        'label' => 'Scrolls',
    ) ) );
}
add_action( 'init', 'lucidus_register_cpts' );

/**
 * Add meta box for scroll rank requirement.
 */
function lucidus_scroll_meta_box() {
    add_meta_box( 'lucidus-scroll-rank', 'Required Rank', 'lucidus_scroll_meta_box_cb', 'scroll', 'side' );
}
add_action( 'add_meta_boxes', 'lucidus_scroll_meta_box' );

function lucidus_scroll_meta_box_cb( $post ) {
    $value = get_post_meta( $post->ID, '_required_rank', true );
    ?>
    <label for="lucidus_required_rank">Rank needed to unlock</label>
    <input type="number" name="lucidus_required_rank" id="lucidus_required_rank" value="<?php echo esc_attr( $value ); ?>" />
    <?php
}

function lucidus_save_scroll_meta( $post_id ) {
    if ( isset( $_POST['lucidus_required_rank'] ) ) {
        update_post_meta( $post_id, '_required_rank', intval( $_POST['lucidus_required_rank'] ) );
    }
}
add_action( 'save_post_scroll', 'lucidus_save_scroll_meta' );

/**
 * Check if a scroll is unlocked for a user.
 *
 * @param int $scroll_id
 * @param int $user_id
 * @return bool
 */
function lucidus_is_scroll_unlocked( $scroll_id, $user_id = 0 ) {
    $user_id      = $user_id ? $user_id : get_current_user_id();
    $required     = intval( get_post_meta( $scroll_id, '_required_rank', true ) );
    $user_rank    = intval( get_user_meta( $user_id, 'lucidus_rank', true ) );
    return $user_rank >= $required;
}

/**
 * Display earned badges and patches on user profile screen.
 */
function lucidus_show_user_rewards( $user ) {
    $badges  = (array) get_user_meta( $user->ID, 'lucidus_badges', true );
    $patches = (array) get_user_meta( $user->ID, 'lucidus_patches', true );

    echo '<h2>Lucidus Rewards</h2>';
    echo '<h3>Badges</h3><ul>';
    foreach ( $badges as $badge_id ) {
        $post = get_post( $badge_id );
        if ( $post ) {
            echo '<li>' . esc_html( $post->post_title ) . '</li>';
        }
    }
    echo '</ul>';

    echo '<h3>Patches</h3><ul>';
    foreach ( $patches as $patch_id ) {
        $post = get_post( $patch_id );
        if ( $post ) {
            echo '<li>' . esc_html( $post->post_title ) . '</li>';
        }
    }
    echo '</ul>';
}
add_action( 'show_user_profile', 'lucidus_show_user_rewards' );
add_action( 'edit_user_profile', 'lucidus_show_user_rewards' );

