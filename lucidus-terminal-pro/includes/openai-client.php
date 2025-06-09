<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function lucidus_openai_chat( $messages, $api_key ) {
    $endpoint = 'https://api.openai.com/v1/chat/completions';
    $args     = array(
        'body'    => wp_json_encode( array(
            'model'    => 'gpt-3.5-turbo',
            'messages' => $messages,
        ) ),
        'headers' => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
        'timeout' => 20,
    );
    $response = wp_remote_post( $endpoint, $args );
    if ( is_wp_error( $response ) ) {
        lucidus_log( 'openai_error', $response->get_error_message() );
        return $response;
    }
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    if ( isset( $data['choices'][0]['message']['content'] ) ) {
        return $data['choices'][0]['message']['content'];
    }
    return '';
}

function lucidus_openai_tts( $text, $voice, $api_key ) {
    $endpoint = 'https://api.openai.com/v1/audio/speech';
    $args     = array(
        'body'    => wp_json_encode( array(
            'model' => 'tts-1',
            'input' => $text,
            'voice' => $voice,
        ) ),
        'headers' => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
        'timeout' => 20,
    );
    $response = wp_remote_post( $endpoint, $args );
    if ( is_wp_error( $response ) ) {
        lucidus_log( 'openai_tts_error', $response->get_error_message() );
        return $response;
    }
    return $response;
}
