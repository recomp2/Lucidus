<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_diagnostics_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $info = array(
        'PHP Version'  => phpversion(),
        'WP Version'   => get_bloginfo( 'version' ),
        'Memory Limit' => ini_get( 'memory_limit' ),
    );
    ?>
    <div class="wrap">
        <h1>Lucidus Diagnostics</h1>
        <table class="widefat">
            <tbody>
                <?php foreach ( $info as $k => $v ) : ?>
                    <tr>
                        <th><?php echo esc_html( $k ); ?></th>
                        <td><?php echo esc_html( $v ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="post">
            <?php wp_nonce_field( 'lucidus_test_openai', 'lucidus_test_openai_nonce' ); ?>
            <p><input type="submit" name="lucidus_test_openai" class="button" value="Test OpenAI" /></p>
        </form>
        <p><a href="<?php echo esc_url( add_query_arg( 'lucidus_export_log', '1' ) ); ?>" class="button">Export Log</a></p>
        <?php
        if ( isset( $_POST['lucidus_test_openai'] ) && check_admin_referer( 'lucidus_test_openai', 'lucidus_test_openai_nonce' ) ) {
            $api_key = get_option( 'lucidus_openai_key', '' );
            if ( $api_key ) {
                $response = lucidus_openai_chat( array( array( 'role' => 'user', 'content' => 'ping' ) ), $api_key );
                echo '<p>Response: ' . esc_html( $response ) . '</p>';
            } else {
                echo '<p>No API key set.</p>';
            }
        }
        if ( isset( $_GET['lucidus_export_log'] ) ) {
            $fs     = lucidus_get_filesystem();
            $upload = wp_upload_dir();
            $path   = trailingslashit( $upload['basedir'] ) . 'lucidus-log.json';
            if ( $fs->exists( $path ) ) {
                header( 'Content-Type: application/json' );
                header( 'Content-Disposition: attachment; filename="lucidus-log.json"' );
                echo $fs->get_contents( $path );
                exit;
            }
        }
        ?>
    </div>
    <?php
}
