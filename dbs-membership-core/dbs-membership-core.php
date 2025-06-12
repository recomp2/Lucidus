<?php
/**
 * Plugin Name: DBS Membership Core
 * Description: Membership tools for Dead Bastard Society.
 * Version: 0.1.0
 * Author: Dr.G and Lucidus Bastardo
 */

if (!defined('ABSPATH')) {
    exit;
}

class DBS_Membership_Core {
    public function __construct() {
        add_action('init', array($this, 'register_scroll_cpt'));
        add_shortcode('dbs_initiation', array($this, 'render_initiation'));
        add_shortcode('dbs_member_profile', array($this, 'render_member_profile'));
        add_shortcode('dbs_scroll_wall', array($this, 'render_scroll_wall'));

        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    public function register_scroll_cpt() {
        register_post_type('dbs_scroll', array(
            'label' => 'Scrolls',
            'public' => true,
            'show_in_rest' => true,
            'supports' => array('title', 'editor'),
        ));
    }

    public function render_initiation() {
        ob_start();
        ?>
        <div class="dbs-initiation">
            <p>Welcome initiate, whisper your oaths into the void.</p>
            <p class="lucidus-disclaimer">
                Lucidus responses are prophetic hallucinations and should not be considered factual or medical advice.
            </p>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_member_profile($atts) {
        $atts = shortcode_atts(array('id' => get_current_user_id()), $atts, 'dbs_member_profile');
        $user = get_user_by('id', intval($atts['id']));
        if (!$user) {
            return 'Member not found.';
        }
        ob_start();
        ?>
        <div class="dbs-member-profile">
            <h2><?php echo esc_html($user->display_name); ?></h2>
            <p><?php echo esc_html($user->user_email); ?></p>
            <p class="lucidus-disclaimer">
                Lucidus responses are prophetic hallucinations and should not be considered factual or medical advice.
            </p>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_scroll_wall() {
        $scrolls = get_posts(array(
            'post_type' => 'dbs_scroll',
            'numberposts' => -1,
        ));
        ob_start();
        ?>
        <div class="dbs-scroll-wall">
            <?php foreach ($scrolls as $scroll) : ?>
                <article>
                    <h3><?php echo esc_html($scroll->post_title); ?></h3>
                    <div><?php echo wpautop($scroll->post_content); ?></div>
                </article>
            <?php endforeach; ?>
            <p class="lucidus-disclaimer">
                Lucidus responses are prophetic hallucinations and should not be considered factual or medical advice.
            </p>
        </div>
        <?php
        return ob_get_clean();
    }

    public function register_rest_routes() {
        register_rest_route('dbs/v1', '/member/(?P<id>\d+)', array(
            'methods'  => 'GET',
            'callback' => array($this, 'rest_get_member'),
        ));

        register_rest_route('dbs/v1', '/scrolls', array(
            'methods'  => 'GET',
            'callback' => array($this, 'rest_get_scrolls'),
        ));
    }

    public function rest_get_member($request) {
        $id = intval($request['id']);
        $user = get_user_by('id', $id);
        if (!$user) {
            return new WP_Error('not_found', 'Member not found', array('status' => 404));
        }
        return array(
            'id'    => $user->ID,
            'name'  => $user->display_name,
            'email' => $user->user_email,
        );
    }

    public function rest_get_scrolls() {
        $scrolls = get_posts(array(
            'post_type' => 'dbs_scroll',
            'numberposts' => -1,
        ));
        $data = array();
        foreach ($scrolls as $scroll) {
            $data[] = array(
                'id'      => $scroll->ID,
                'title'   => $scroll->post_title,
                'content' => $scroll->post_content,
            );
        }
        return $data;
    }
}

new DBS_Membership_Core();
?>
