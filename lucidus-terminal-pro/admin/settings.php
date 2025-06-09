<?php
if (!current_user_can('manage_options')) return;
$config_path = __DIR__ . '/../dbs-library/system.json';
$config = file_exists($config_path) ? json_decode(file_get_contents($config_path), true) : array(
    'scroll_time' => 5,
    'debug_mode' => false,
    'archetype_modes' => array()
);

if (isset($_POST['lucidus_settings_nonce']) && wp_verify_nonce($_POST['lucidus_settings_nonce'], 'save_lucidus_settings')) {
    $config['scroll_time'] = intval($_POST['scroll_time']);
    $config['debug_mode'] = isset($_POST['debug_mode']);
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
        </table>
        <?php submit_button(); ?>
    </form>
</div>
