<?php
if (!current_user_can('manage_options')) return;
$config_path = __DIR__ . '/../dbs-library/system.json';
$config = file_exists($config_path) ? json_decode(file_get_contents($config_path), true) : array(
    'scroll_time' => 5,
    'debug_mode' => false,
    'sandbox_mode' => false,
    'archive_enabled' => true,
    'confessions_enabled' => true,
    'archetype_modes' => array()
);

if (isset($_POST['lucidus_settings_nonce']) && wp_verify_nonce($_POST['lucidus_settings_nonce'], 'save_lucidus_settings')) {
    $config['scroll_time'] = intval($_POST['scroll_time']);
    $config['debug_mode'] = isset($_POST['debug_mode']);
    $config['sandbox_mode'] = isset($_POST['sandbox_mode']);
    $config['archive_enabled'] = isset($_POST['archive_enabled']);
    $config['confessions_enabled'] = isset($_POST['confessions_enabled']);
    $config['archetype_modes'] = array_filter(array_map('trim', explode(',', $_POST['archetype_modes'])));
    file_put_contents($config_path, json_encode($config, JSON_PRETTY_PRINT));
    echo '<div class="updated"><p>Settings saved.</p></div>';
}
?>
<div class="wrap">
    <h1>Lucidus Settings</h1>
    <form method="post">
        <?php wp_nonce_field('save_lucidus_settings', 'lucidus_settings_nonce'); ?>
        <table class="form-table">
            <tr><th scope="row">Scroll Time</th><td><input name="scroll_time" type="number" value="<?php echo esc_attr($config['scroll_time']); ?>" /></td></tr>
            <tr><th scope="row">Debug Mode</th><td><input name="debug_mode" type="checkbox" <?php checked($config['debug_mode']); ?> /></td></tr>
            <tr><th scope="row">Sandbox Mode</th><td><input name="sandbox_mode" type="checkbox" <?php checked($config['sandbox_mode']); ?> /></td></tr>
            <tr><th scope="row">Archive Enabled</th><td><input name="archive_enabled" type="checkbox" <?php checked($config['archive_enabled']); ?> /></td></tr>
            <tr><th scope="row">Confessions Enabled</th><td><input name="confessions_enabled" type="checkbox" <?php checked($config['confessions_enabled']); ?> /></td></tr>
            <tr><th scope="row">Archetype Modes</th><td><input name="archetype_modes" type="text" value="<?php echo esc_attr(implode(',', $config['archetype_modes'])); ?>" class="regular-text" /></td></tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
