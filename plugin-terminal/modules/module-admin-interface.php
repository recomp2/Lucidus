<?php
/*
Plugin Name: Lucidus Module Admin Interface
Description: Admin interface for managing Lucidus modules.
*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

function lucidus_modules_admin_menu() {
    add_submenu_page(
        'lucidus-terminal',
        __('Modules', 'lucidus-terminal-pro'),
        __('Modules', 'lucidus-terminal-pro'),
        'manage_options',
        'lucidus-modules',
        'lucidus_modules_admin_page'
    );
}
add_action('admin_menu', 'lucidus_modules_admin_menu');

function lucidus_modules_admin_page() {
    $dirs = glob(LUCIDUS_MODULES_DIR . '*', GLOB_ONLYDIR);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Lucidus Modules', 'lucidus-terminal-pro'); ?></h1>
        <table class="widefat">
            <thead><tr><th><?php esc_html_e('Module', 'lucidus-terminal-pro'); ?></th><th><?php esc_html_e('Version', 'lucidus-terminal-pro'); ?></th></tr></thead>
            <tbody>
            <?php if ($dirs): foreach ($dirs as $dir): $slug = basename($dir); $config = [];
                $config_file = $dir . '/'. $slug . '-config.json';
                if (file_exists($config_file)) {
                    $config = json_decode(file_get_contents($config_file), true);
                }
                ?>
                <tr>
                    <td><?php echo esc_html($config['name'] ?? $slug); ?></td>
                    <td><?php echo esc_html($config['version'] ?? '1.0'); ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="2"><?php esc_html_e('No modules found.', 'lucidus-terminal-pro'); ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
