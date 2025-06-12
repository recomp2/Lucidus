<?php
/*
Plugin Name: Lucidus Tier Access Settings
Description: Admin interface for managing feature access by tier.
*/
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/tier-access-control.php';

function tier_access_admin_menu() {
    add_submenu_page(
        'lucidus-terminal',
        __('Tier Settings', 'lucidus-terminal-pro'),
        __('Tier Settings', 'lucidus-terminal-pro'),
        'manage_options',
        'lucidus-tier-settings',
        'tier_access_settings_page'
    );
}
add_action('admin_menu', 'tier_access_admin_menu');

function tier_access_settings_page() {
    if (!current_user_can('manage_options')) return;

    $settings = tier_access_get_settings();
    if (isset($_POST['tier_settings']) && check_admin_referer('save_tier_settings')) {
        $new = [];
        foreach ($settings as $tier => $features) {
            $new[$tier] = [];
            foreach ($features as $feature => $val) {
                $new[$tier][$feature] = isset($_POST[$tier.'_'.$feature]);
            }
        }
        update_option('lucidus_tier_settings', $new);
        $settings = $new;
        echo '<div class="updated"><p>'.esc_html__('Settings saved.', 'lucidus-terminal-pro').'</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Tier Settings', 'lucidus-terminal-pro'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('save_tier_settings'); ?>
            <table class="widefat">
                <thead>
                    <tr><th><?php esc_html_e('Tier','lucidus-terminal-pro'); ?></th><th><?php esc_html_e('Feature','lucidus-terminal-pro'); ?></th><th><?php esc_html_e('Allowed','lucidus-terminal-pro'); ?></th></tr>
                </thead>
                <tbody>
                <?php foreach ($settings as $tier => $features): ?>
                    <?php foreach ($features as $feature => $val): ?>
                    <tr>
                        <td><?php echo esc_html($tier); ?></td>
                        <td><?php echo esc_html($feature); ?></td>
                        <td><input type="checkbox" name="<?php echo esc_attr($tier.'_'.$feature); ?>" <?php checked($val); ?>></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p><button type="submit" class="button-primary"><?php esc_html_e('Save','lucidus-terminal-pro'); ?></button></p>
        </form>
    </div>
    <?php
}
?>
