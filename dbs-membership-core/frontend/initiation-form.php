<?php
/**
 * Dead Bastard Society Membership Core
 * Public initiation form
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

add_shortcode('dbs_initiation_form', 'dbs_initiation_form');

function dbs_initiation_form() {
    if (is_user_logged_in()) {
        return '<p>You are already a member.</p>';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dbs_init_nonce']) && wp_verify_nonce($_POST['dbs_init_nonce'], 'dbs_init')) {
        $username = sanitize_user($_POST['username']);
        $password = wp_generate_password();
        $user_id = wp_create_user($username, $password, $username.'@example.com');
        if (is_wp_error($user_id)) {
            return '<p>Error: '.esc_html($user_id->get_error_message()).'</p>';
        }
        $first = $username;
        $latin = dbs_generate_latin_name($first, '');
        $rank = dbs_initial_rank();
        $geo = sanitize_text_field($_POST['city']).', '.sanitize_text_field($_POST['state']);
        $tags = dbs_assign_initial_tags();
        update_user_meta($user_id, 'dbs_rank', $rank);
        $profile = [
            'latin_name' => $latin,
            'archetype'  => sanitize_text_field($_POST['archetype']),
            'tags'       => $tags,
            'rank'       => $rank,
            'geo'        => $geo
        ];
        dbs_write_profile($username, $profile);
        $new_town = dbs_register_geo(sanitize_text_field($_POST['city']), sanitize_text_field($_POST['state']));
        dbs_update_ai_memory($username, $profile);
        wp_signon(['user_login'=>$username,'user_password'=>$password,'remember'=>true]);
        wp_redirect(add_query_arg(['user'=>$username,'new_town'=>$new_town?1:0], site_url('/confirmation')));
        exit;
    }

    ob_start();
    ?>
    <form method="post">
        <?php wp_nonce_field('dbs_init', 'dbs_init_nonce'); ?>
        <p><label>Username<br><input type="text" name="username" required></label></p>
        <p><label>Archetype<br><input type="radio" name="archetype" value="chaos" checked> Chaos <input type="radio" name="archetype" value="order"> Order</label></p>
        <p><label>City<br><input type="text" name="city" required></label></p>
        <p><label>State<br><input type="text" name="state" required></label></p>
        <p><button type="submit">Join</button></p>
    </form>
    <?php
    return ob_get_clean();
}
