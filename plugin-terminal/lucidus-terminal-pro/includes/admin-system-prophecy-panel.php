<?php
if (!defined('ABSPATH')) exit;

require_once LUCIDUS_TERMINAL_DIR . 'includes/upgrade-engine.php';

function lucidus_system_prophecy_menu() {
    add_submenu_page('lucidus-terminal', __('System Prophecies', 'lucidus-terminal-pro'), __('System Prophecies', 'lucidus-terminal-pro'), 'manage_options', 'lucidus-system-prophecies', 'lucidus_system_prophecy_page');
}
add_action('admin_menu', 'lucidus_system_prophecy_menu');

function lucidus_load_insight_log() {
    $file = lucidus_insight_log_file();
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function lucidus_system_prophecy_page() {
    if (isset($_POST['lucidus_export_insights']) && check_admin_referer('lucidus_export_insights')) {
        $log = lucidus_load_insight_log();
        $file = lucidus_upgrade_log_dir() . 'upgrade-suggestions.json';
        file_put_contents($file, wp_json_encode($log));
        echo '<div class="updated"><p>' . esc_html__('Suggestions exported.', 'lucidus-terminal-pro') . '</p></div>';
    }

    $log = array_reverse(lucidus_load_insight_log());
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('System Prophecies', 'lucidus-terminal-pro'); ?></h1>
        <p><?php esc_html_e('Lucidus monitors system health and offers upgrade suggestions.', 'lucidus-terminal-pro'); ?></p>
        <ol>
            <?php foreach(array_slice($log, 0, 10) as $entry): ?>
                <li>
                    <strong><?php echo esc_html($entry['time']); ?></strong>
                    <ul>
                        <?php foreach($entry['suggestions'] as $s): ?>
                            <li><?php echo esc_html($s); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ol>
        <form method="post">
            <?php wp_nonce_field('lucidus_export_insights'); ?>
            <input type="hidden" name="lucidus_export_insights" value="1" />
            <?php submit_button(__('Export Suggestions', 'lucidus-terminal-pro')); ?>
        </form>
    </div>
    <?php
}
