<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_file_sync_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $files = lucidus_list_memory_files();
    ?>
    <div class="wrap">
        <h1>Lucidus File Sync</h1>
        <ul>
            <?php foreach ( $files as $file ) : ?>
                <li>
                    <?php echo esc_html( $file ); ?>
                    <a href="<?php echo esc_url( add_query_arg( 'load', $file ) ); ?>" class="button">Load</a>
                    <a href="<?php echo esc_url( add_query_arg( 'sync', $file ) ); ?>" class="button">Sync</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        if ( isset( $_GET['load'] ) ) {
            $content = lucidus_load_memory( $_GET['load'] );
            echo '<pre>' . esc_html( $content ) . '</pre>';
        }
        if ( isset( $_GET['sync'] ) ) {
            lucidus_log( 'file_sync', $_GET['sync'] );
            echo '<p>Synced ' . esc_html( $_GET['sync'] ) . '</p>';
        }
        ?>
    </div>
    <?php
}
