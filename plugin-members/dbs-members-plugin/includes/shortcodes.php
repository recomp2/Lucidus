<?php
if (!defined('ABSPATH')) exit;

function dbs_login_shortcode() {
    if (is_user_logged_in()) {
        return '<p>' . esc_html__('You are already logged in.', 'dbs-members-plugin') . '</p>';
    }
    $args = ['echo' => false];
    $page = get_page_by_title('DBS Dashboard');
    if ($page) {
        $args['redirect'] = get_permalink($page);
    }
    return wp_login_form($args);
}
add_shortcode('dbs_login', 'dbs_login_shortcode');

function dbs_register_shortcode() {
    if (is_user_logged_in()) {
        return '<p>' . esc_html__('You are already registered.', 'dbs-members-plugin') . '</p>';
    }

    $errors = new WP_Error();
    if ('POST' === strtolower($_SERVER['REQUEST_METHOD']) && isset($_POST['dbs_register_nonce']) && wp_verify_nonce($_POST['dbs_register_nonce'], 'dbs_register')) {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);

        if (username_exists($username) || email_exists($email)) {
            $errors->add('exists', __('User already exists.', 'dbs-members-plugin'));
        } else {
            $uid = wp_create_user($username, $password, $email);
            if (!is_wp_error($uid)) {
                wp_set_current_user($uid);
                wp_set_auth_cookie($uid);
                return '<p>' . esc_html__('Registration successful.', 'dbs-members-plugin') . '</p>';
            } else {
                $errors = $uid;
            }
        }
    }

    ob_start();
    ?>
    <form method="post" id="dbs-register">
        <?php foreach ($errors->get_error_messages() as $msg): ?>
            <p class="error"><?php echo esc_html($msg); ?></p>
        <?php endforeach; ?>
        <p><label>Username<br><input type="text" name="username" required></label></p>
        <p><label>Email<br><input type="email" name="email" required></label></p>
        <p><label>Password<br><input type="password" name="password" required></label></p>
        <?php wp_nonce_field('dbs_register', 'dbs_register_nonce'); ?>
        <p><button type="submit"><?php esc_html_e('Register', 'dbs-members-plugin'); ?></button></p>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('dbs_register', 'dbs_register_shortcode');

function dbs_dashboard_shortcode() {
    if (!is_user_logged_in()) {
        return '<p>' . esc_html__('Please log in.', 'dbs-members-plugin') . '</p>';
    }
    $user = wp_get_current_user();
    $logout = wp_logout_url(get_permalink());
    return '<h2>' . sprintf( esc_html__('Welcome, %s', 'dbs-members-plugin'), esc_html($user->display_name) ) . '</h2><p><a href="' . esc_url($logout) . '">' . esc_html__('Log out', 'dbs-members-plugin') . '</a></p>';
}
add_shortcode('dbs_dashboard', 'dbs_dashboard_shortcode');
?>
