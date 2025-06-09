<?php
if (!current_user_can('upload_files')) return;
$dest = isset($_POST['dest']) ? sanitize_text_field($_POST['dest']) : 'uploads';
$dest_dir = __DIR__ . '/../' . $dest;
$log_path = __DIR__ . '/../logs/upload-log.json';
if (!file_exists($log_path)) file_put_contents($log_path, "[]");
$log = json_decode(file_get_contents($log_path), true);
if (!empty($_FILES['file'])) {
    $name = sanitize_file_name($_FILES['file']['name']);
    $target = $dest_dir . '/' . $name;
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        $log[] = array('file' => $name, 'dest' => $dest, 'time' => time());
        file_put_contents($log_path, json_encode($log, JSON_PRETTY_PRINT));
        echo '<div class="updated"><p>Uploaded.</p></div>';
    }
}
?>
<div class="wrap">
    <h1>Upload</h1>
    <form method="post" enctype="multipart/form-data">
        <select name="dest">
            <option value="scripts" <?php selected($dest, 'scripts'); ?>>scripts/</option>
            <option value="gospel" <?php selected($dest, 'gospel'); ?>>gospel/</option>
            <option value="images" <?php selected($dest, 'images'); ?>>images/</option>
        </select>
        <input type="file" name="file" />
        <?php submit_button('Upload'); ?>
    </form>
</div>
