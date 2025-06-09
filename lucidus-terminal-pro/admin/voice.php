<?php
if (!current_user_can('manage_options')) return;
$config_path = __DIR__ . '/../dbs-library/system.json';
$config = file_exists($config_path) ? json_decode(file_get_contents($config_path), true) : array('voice' => '');
if (isset($_POST['voice'])) {
    $config['voice'] = sanitize_text_field($_POST['voice']);
    file_put_contents($config_path, json_encode($config, JSON_PRETTY_PRINT));
    echo '<div class="updated"><p>Voice updated.</p></div>';
}
$voices = array('OpenAI', 'ElevenLabs');
?>
<div class="wrap">
    <h1>Voice Settings</h1>
    <form method="post">
        <select name="voice">
            <?php foreach($voices as $v): ?>
                <option value="<?php echo esc_attr($v); ?>" <?php selected($config['voice'], $v); ?>><?php echo esc_html($v); ?></option>
            <?php endforeach; ?>
        </select>
        <?php submit_button('Save'); ?>
    </form>
</div>
