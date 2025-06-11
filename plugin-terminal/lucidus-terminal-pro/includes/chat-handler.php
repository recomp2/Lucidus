<?php
if (!defined('ABSPATH')) exit;
require_once LUCIDUS_TERMINAL_DIR . 'includes/memory.php';
require_once LUCIDUS_TERMINAL_DIR . 'includes/archive-writer.php';
require_once dirname(LUCIDUS_TERMINAL_DIR) . '/modules/tier-access/tier-access-control.php';

function lucidus_handle_chat($request) {
    if (!current_user_can('read')) {
        return new WP_Error('rest_forbidden', __('Permission denied', 'lucidus-terminal-pro'));
    }

    $user_id = get_current_user_id();
    $rank    = tier_access_get_user_rank($user_id);
    if ($rank === 'initiate') {
        return new WP_Error('forbidden', lucidus_get_denial_message('guest'), ['status' => 403]);
    }
    $message = sanitize_text_field($request['message']);
    $memory  = lucidus_load_memory($user_id);
    $memory[] = ['role' => 'user', 'content' => $message];
    $context = array_merge(lucidus_get_scroll_context(), $memory);
    $context = apply_filters('lucidus_prepare_conversation', $context);
    $response = lucidus_openai_chat($context);
    if ($response) {
        $memory[] = ['role' => 'assistant', 'content' => $response];
        lucidus_save_memory($memory, $user_id);
        lucidus_write_archive([
            'time'    => current_time('mysql'),
            'user'    => $user_id,
            'message' => $message,
            'reply'   => $response
        ]);
        return rest_ensure_response([
            'reply' => $response,
            'quote' => lucidus_fetch_random_quote()
        ]);
    }
    return new WP_Error('chat_error', 'Unable to get response');
}

function lucidus_openai_chat($conversation) {
    $api_key = get_option('lucidus_openai_key');
    if (!$api_key) {
        lucidus_error_log(['type' => 'openai_fallback', 'error' => 'missing_api_key']);
        return getLucidusLore('voice_rituals', 'error_fallback');
    }

    $rank = tier_access_get_user_rank();
    $behavior = tier_access_get_rank_behavior($rank);
    $archetype = tier_access_get_user_archetype();
    if ($behavior) {
        array_unshift($conversation, ['role' => 'system', 'content' => $behavior]);
    }
    $append = tier_access_prompt_append($rank, $archetype, get_user_meta(get_current_user_id(), 'dbs_mood', true));
    if ($append) {
        $conversation[] = ['role' => 'system', 'content' => $append];
    }

    $body = [
        'model' => 'gpt-3.5-turbo',
        'messages' => $conversation
    ];

    $args = [
        'headers' => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        ],
        'body'    => wp_json_encode($body),
        'timeout' => 15
    ];

    $res = wp_remote_post('https://api.openai.com/v1/chat/completions', $args);
    if (is_wp_error($res) || wp_remote_retrieve_response_code($res) !== 200) {
        lucidus_error_log([
            'type'  => 'openai_fallback',
            'error' => is_wp_error($res) ? $res->get_error_message() : wp_remote_retrieve_body($res)
        ]);
        return getLucidusLore('voice_rituals', 'error_fallback');
    }
    $data = json_decode(wp_remote_retrieve_body($res), true);
    return $data['choices'][0]['message']['content'] ?? getLucidusLore('voice_rituals', 'error_fallback');
}

function lucidus_get_scroll_context($limit = 3) {
    $posts = get_posts([
        'post_type' => 'scroll',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    $context = [];
    foreach ($posts as $p) {
        $context[] = [
            'role' => 'system',
            'content' => wp_strip_all_tags($p->post_content)
        ];
    }
    return $context;
}

function lucidus_fetch_random_quote() {
    $res = wp_remote_get('https://api.quotable.io/random', ['timeout' => 5]);
    if (is_wp_error($res)) {
        return '';
    }
    $data = json_decode(wp_remote_retrieve_body($res), true);
    return $data['content'] ?? '';
}
