<?php
/*
Plugin Name: DBS Membership Core
Description: Membership initiation and rank system for Dead Bastard Society.
Version: 1.0
Author: Dr.G
License: MIT
*/

if (!defined('ABSPATH')) {
    exit;
}

class DBS_Membership_Core {
    public function __construct() {
        add_shortcode('dbs_initiate_member', array($this, 'initiation_form'));
        add_action('init', array($this, 'handle_form_submission'));
        add_action('rest_api_init', array($this, 'register_rest_endpoints'));
    }

    public function initiation_form() {
        if (isset($_POST['dbs_initiate_nonce']) && wp_verify_nonce($_POST['dbs_initiate_nonce'], 'dbs_initiate')) {
            // handle_form_submission will pick this up
        }
        ob_start();
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('dbs_initiate', 'dbs_initiate_nonce'); ?>
            <p><input type="text" name="dbs_username" placeholder="Username" required></p>
            <p><input type="email" name="dbs_email" placeholder="Email" required></p>
            <p><button type="submit">Initiate</button></p>
        </form>
        <?php
        return ob_get_clean();
    }

    public function handle_form_submission() {
        if (!empty($_POST['dbs_username']) && !empty($_POST['dbs_email']) && isset($_POST['dbs_initiate_nonce']) && wp_verify_nonce($_POST['dbs_initiate_nonce'], 'dbs_initiate')) {
            $userdata = array(
                'user_login' => sanitize_user($_POST['dbs_username']),
                'user_email' => sanitize_email($_POST['dbs_email']),
                'user_pass'  => wp_generate_password(),
            );
            $user_id = wp_insert_user($userdata);
            if (!is_wp_error($user_id)) {
                update_user_meta($user_id, 'dbs_rank', 'initiate');
            }
        }
    }

    public function register_rest_endpoints() {
        register_rest_route('dbs/v1', '/rank/(?P<id>\\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_rank'),
            'permission_callback' => '__return_true',
        ));
        register_rest_route('dbs/v1', '/rank/(?P<id>\\d+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'update_rank'),
            'permission_callback' => function() { return current_user_can('edit_users'); },
        ));
    }

    public function get_rank($request) {
        $user_id = absint($request['id']);
        $rank = get_user_meta($user_id, 'dbs_rank', true);
        return rest_ensure_response(array('rank' => $rank));
    }

    public function update_rank($request) {
        $user_id = absint($request['id']);
        $rank = sanitize_text_field($request['rank']);
        update_user_meta($user_id, 'dbs_rank', $rank);
        return rest_ensure_response(array('rank' => $rank));
    }
}

new DBS_Membership_Core();
