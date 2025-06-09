<?php
if (!current_user_can('manage_options')) return;
require_once ABSPATH . "wp-admin/includes/plugin.php";
$config = array();
$config_path = __DIR__ . '/../dbs-library/system.json';
if (file_exists($config_path)) {
    $config = json_decode(file_get_contents($config_path), true);
}
$log = array();
$log_path = __DIR__ . '/../logs/script-executions.log';
if (file_exists($log_path)) {
    $lines = array_slice(file($log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), -5);
    $log = $lines;
}
?>
<div class="wrap">
    <h1>Lucidus Status</h1>
    <h2>Version: <?php echo esc_html(get_plugin_data(__DIR__ . '/../lucidus-terminal.php')['Version']); ?></h2>
    <h3>Config</h3>
    <pre><?php echo esc_html(json_encode($config, JSON_PRETTY_PRINT)); ?></pre>
    <h3>Last Script Loads</h3>
    <ul>
        <?php foreach($log as $line): ?>
            <li><?php echo esc_html($line); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
