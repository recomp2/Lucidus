<div class="wrap">
    <h1>Lucidus Terminal Scripts</h1>
    <?php if ( isset( $_GET['command'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php printf( esc_html__( 'Command "%s" executed.', 'lucidus' ), esc_html( $_GET['command'] ) ); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'lucidus_run_script' ); ?>
        <input type="hidden" name="action" value="lucidus_run_script">
        <p>
            <label for="lucidus_script_command">Command</label><br/>
            <textarea name="lucidus_script_command" id="lucidus_script_command" rows="4" cols="60" class="large-text"></textarea>
        </p>
        <?php submit_button( __( 'Run Script', 'lucidus' ) ); ?>
    </form>
</div>
