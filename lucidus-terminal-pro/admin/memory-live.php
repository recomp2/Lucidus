<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_memory_live_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $files = lucidus_list_memory_files();
    ?>
    <div class="wrap">
        <h1>Live Memory</h1>
        <form method="post">
            <?php wp_nonce_field( 'lucidus_clear_memory', 'lucidus_clear_memory_nonce' ); ?>
            <p><input type="submit" name="clear_memory" class="button" value="Clear All Memory"></p>
        </form>
        <ul>
            <?php foreach ( $files as $file ) : ?>
                <li><a href="<?php echo esc_url( add_query_arg( 'view', $file ) ); ?>"><?php echo esc_html( $file ); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php
        if ( isset( $_POST['clear_memory'] ) && check_admin_referer( 'lucidus_clear_memory', 'lucidus_clear_memory_nonce' ) ) {
            lucidus_clear_memory();
            echo '<p>Memory Cleared.</p>';
        }
        if ( isset( $_GET['view'] ) ) {
            echo '<pre>' . esc_html( lucidus_load_memory( $_GET['view'] ) ) . '</pre>';
        }
        ?>
    </div>
    <?php
}
