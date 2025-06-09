<?php
if (!current_user_can('manage_options')) return;
$script_files = glob(__DIR__ . '/../scripts/*/*.php');
?>
<div class="wrap">
    <h1>Scripts</h1>
    <table class="widefat">
        <thead><tr><th>Script</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach($script_files as $file): $name = basename($file); ?>
            <tr>
                <td><?php echo esc_html($name); ?></td>
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
    $output = null; $return = null;
    ob_start();
    include $_POST['run_script'];
    $output = ob_get_clean();
    echo '<h2>Output</h2><pre>' . esc_html($output) . '</pre>';
    file_put_contents(__DIR__ . '/../logs/script-executions.log', date('c') . " - " . basename($_POST['run_script']) . PHP_EOL, FILE_APPEND);
}
?>
