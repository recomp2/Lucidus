<?php
if (!defined('ABSPATH')) exit;

function lucidus_core_feed_diagnostics_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('lucidus-core','feed-diagnostics');
    }
    $log_file = WP_CONTENT_DIR.'/dbs-library/logs/feed-log.txt';
    $log = file_exists($log_file) ? file_get_contents($log_file) : '';
    ?>
    <div class="wrap">
        <h1>Feed Logs</h1>
        <pre style="background:#111;color:#0f0;padding:10px;max-height:400px;overflow:auto;">
<?php echo esc_html($log); ?>
        </pre>
    </div>
    <?php
}
