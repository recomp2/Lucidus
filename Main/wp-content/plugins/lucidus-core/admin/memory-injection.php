<?php
if (!defined('ABSPATH')) exit;
require_once plugin_dir_path(__FILE__).'../includes/feed-handler.php';

function lucidus_core_memory_injection_page(){
    if(function_exists('dbs_update_user_memory')){
        dbs_update_user_memory('lucidus-core','memory-injection');
    }
    $entries = lucidus_feed_fetch_entries();
    if(isset($_POST['lucidus_feed_inject_now'])){
        check_admin_referer('lucidus_feed_inject');
        lucidus_feed_inject_memory($entries);
        echo '<div class="updated notice"><p>Feeds injected into memory.</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Memory Injection</h1>
        <form method="post">
            <?php wp_nonce_field('lucidus_feed_inject'); ?>
            <p><input type="submit" class="button button-primary" name="lucidus_feed_inject_now" value="Inject Now"></p>
        </form>
        <h2>Latest Entries</h2>
        <ul>
        <?php foreach(array_slice($entries,0,5) as $e): ?>
            <li><?php echo esc_html($e['title']); ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php
}
