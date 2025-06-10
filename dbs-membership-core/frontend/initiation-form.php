<?php
/**
 * Dead Bastard Society Membership Core
 * Public initiation form
 *
 * @package Dead Bastard Society
 */
if (!defined('ABSPATH')) { exit; }

function dbs_archetype_dropdown() {
    $raw = get_option('dbs_archetypes', "chaos\norder");
    $archs = array_filter(array_map('trim', explode("\n", $raw)));
    $html = '<select name="archetype">';
    foreach ($archs as $a) {
        $esc = esc_html($a);
        $html .= "<option value='$esc'>$esc</option>";
    }
    $html .= '</select>';
    return $html;
}

add_shortcode('dbs_initiation_form', 'dbs_initiation_form');

function dbs_initiation_form() {
    if (is_user_logged_in()) {
        return '<p>You are already a member.</p>';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dbs_init_nonce']) && wp_verify_nonce($_POST['dbs_init_nonce'], 'dbs_init')) {
        $username = sanitize_user($_POST['username']);
        $email    = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $confirm  = $_POST['password_confirm'];
        if (!is_email($email)) {
            return '<p>Invalid email.</p>';
        }
        if ($password !== $confirm) {
            return '<p>Passwords do not match.</p>';
        }
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            return '<p>Error: '.esc_html($user_id->get_error_message()).'</p>';
        }
        $first = $username;
        $latin = dbs_generate_latin_name($first, '');
        $rank = dbs_initial_rank();
        $city  = sanitize_text_field($_POST['city']);
        $state = sanitize_text_field($_POST['state']);
        if (!preg_match('/^[A-Za-z\s-]{2,50}$/', $city) || !preg_match('/^[A-Za-z\s]{2,20}$/', $state)) {
            return '<p>Invalid city or state.</p>';
        }
        $geo = $city . ', ' . $state;
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
        $new_town = dbs_register_geo_pending($city, $state, $username);
        dbs_update_ai_memory($username, $profile);

        $logdir = DBS_LIBRARY_DIR . 'logs/';
        wp_mkdir_p($logdir);
        $line = $username."\t".$latin."\t".$geo."\t".dbs_rank_label($rank)."\n";
        file_put_contents($logdir.'joins.txt', $line, FILE_APPEND);
        wp_mail(get_option('admin_email'), 'DBS Member Joined', $line);

        wp_signon(['user_login'=>$username,'user_password'=>$password,'remember'=>true]);
        $args = ['user'=>$username,'new_town'=>$new_town?1:0];
        if ($new_town) { $args['city']=$city; $args['state']=$state; }
        wp_redirect(add_query_arg($args, site_url('/confirmation')));
        exit;
    }

    ob_start();
    ?>
    <form method="post">
        <?php wp_nonce_field('dbs_init', 'dbs_init_nonce'); ?>
        <p><label>Username<br><input type="text" name="username" required></label></p>
        <p><label>Email<br><input type="email" name="email" required></label></p>
        <p><label>Password<br><input type="password" name="password" required></label></p>
        <p><label>Confirm Password<br><input type="password" name="password_confirm" required></label></p>
        <p><label>Archetype<br><?php echo dbs_archetype_dropdown(); ?></label></p>
        <p><label>City<br><input type="text" name="city" required></label></p>
        <p><label>State<br><input type="text" name="state" required></label></p>
        <p><button type="submit">Join</button></p>
    </form>
    <?php
    return ob_get_clean();
}
