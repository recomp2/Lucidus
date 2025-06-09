<?php
if (!current_user_can('manage_options')) return;
$script_files = glob(__DIR__ . '/../scripts/*/*.php');
$log_path = __DIR__ . '/../logs/script-executions.log';
$history = array();
if (file_exists($log_path)) {
    foreach (file($log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $entry = json_decode($line, true);
        if ($entry) {
            $history[$entry['script']] = $entry;
        }
    }
}
?>
<div class="wrap">
    <h1>Scripts</h1>
    <table class="widefat">
        <thead><tr><th>Script</th><th>Folder</th><th>Last Run</th><th>Result</th><th>Tags</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($script_files as $file): $name = basename($file); $folder = basename(dirname($file)); $info = isset($history[$name]) ? $history[$name] : null; $tags = array(); preg_match_all('/@([a-zA-Z_]+):\s*(.+)/', file_get_contents($file), $m); if($m){ for($i=0;$i<count($m[1]);$i++){ $tags[]=$m[1][$i].': '.$m[2][$i];}} ?>
            <tr>
                <td><?php echo esc_html($name); ?></td>
                <td><?php echo esc_html($folder); ?></td>
                <td><?php echo $info?esc_html($info['time']):'—'; ?></td>
                <td><?php echo $info?($info['success']?'success':'failure'):'—'; ?></td>
                <td><?php echo esc_html(implode(', ', $tags)); ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="run_script" value="<?php echo esc_attr($file); ?>" />
                        <?php submit_button('Run', 'secondary', 'submit', false); ?>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
if (isset($_POST['run_script']) && file_exists($_POST['run_script'])) {
    $success = true;
    $output = '';
    try {
        ob_start();
        include $_POST['run_script'];
        $output = ob_get_clean();
    } catch (Throwable $e) {
        $success = false;
        $output = ob_get_clean() . "\n" . $e->getMessage();
    }
    echo '<h2>Output</h2><pre>' . esc_html($output) . '</pre>';
    $entry = array('time' => date('c'), 'script' => basename($_POST['run_script']), 'success' => $success);
    file_put_contents($log_path, json_encode($entry) . PHP_EOL, FILE_APPEND);
}
?>
