<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_phrase_forge_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $rules = get_option( 'lucidus_phrase_rules', array() );

    if ( isset( $_POST['new_rule'] ) && check_admin_referer( 'lucidus_new_rule', 'lucidus_new_rule_nonce' ) ) {
        $trigger  = sanitize_text_field( $_POST['trigger'] );
        $response = wp_kses_post( $_POST['response'] );
        $rules[]  = array(
            'trigger'  => $trigger,
            'response' => $response,
        );
        update_option( 'lucidus_phrase_rules', $rules );
    }

    if ( isset( $_GET['delete'] ) ) {
        $index = absint( $_GET['delete'] );
        if ( isset( $rules[ $index ] ) ) {
            unset( $rules[ $index ] );
            update_option( 'lucidus_phrase_rules', $rules );
        }
    }
    ?>
    <div class="wrap">
        <h1>Phrase Forge</h1>
        <form method="post">
            <?php wp_nonce_field( 'lucidus_new_rule', 'lucidus_new_rule_nonce' ); ?>
            <input type="text" name="trigger" placeholder="Trigger" required>
            <input type="text" name="response" placeholder="Response" required>
            <button type="submit" name="new_rule" class="button">Add Rule</button>
        </form>
        <ul>
            <?php foreach ( $rules as $index => $rule ) : ?>
                <li>
                    <strong><?php echo esc_html( $rule['trigger'] ); ?>:</strong>
                    <?php echo esc_html( $rule['response'] ); ?>
                    <a href="<?php echo esc_url( add_query_arg( 'delete', $index ) ); ?>" class="button">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}
