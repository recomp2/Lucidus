<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_chat_terminal_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    wp_enqueue_script( 'lucidus-chat', plugins_url( 'assets/lucidus-chat.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.0', true );
    wp_localize_script( 'lucidus-chat', 'lucidus_chat', array( 'nonce' => wp_create_nonce( 'lucidus_chat' ) ) );
    wp_enqueue_style( 'lucidus-chat', plugins_url( 'assets/lucidus-chat.css', dirname( __FILE__ ) ) );
    ?>
    <div class="wrap">
        <h1>Lucidus Chat Terminal</h1>
        <div id="lucidus-chat-log"></div>
        <form id="lucidus-chat-form" method="post">
            <?php wp_nonce_field( 'lucidus_chat', 'lucidus_chat_nonce' ); ?>
            <input type="text" id="lucidus-chat-input" />
            <button type="submit" class="button button-primary">Send</button>
        </form>
    </div>
    <?php
}
