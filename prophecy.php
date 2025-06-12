<?php
if (!defined('ABSPATH')) exit;
require_once LUCIDUS_TERMINAL_DIR . 'includes/archive-writer.php';

function lucidus_fetch_weather() {
    $res = wp_remote_get('https://wttr.in/?format=j1');
    if (is_wp_error($res)) return '';
    $data = json_decode(wp_remote_retrieve_body($res), true);
    if (!$data || empty($data['current_condition'][0])) return '';
    $cond = $data['current_condition'][0];
    $desc = $cond['weatherDesc'][0]['value'] ?? '';
    $temp = $cond['temp_C'] ?? '';
    return sprintf('%s°C and %s', $temp, $desc);
}

function lucidus_fetch_news() {
    include_once ABSPATH . WPINC . '/feed.php';
    $feed = fetch_feed('https://hnrss.org/frontpage');
    if (is_wp_error($feed)) return '';
    $items = $feed->get_items(0, 1);
    if (!$items) return '';
    return $items[0]->get_title();
}

function lucidus_astrology_sign() {
    $month = (int) date('n');
    $day   = (int) date('j');
    $signs = [
        ['Capricorn', 1, 19], ['Aquarius', 2, 18], ['Pisces', 3, 20],
        ['Aries', 4, 19], ['Taurus', 5, 20], ['Gemini', 6, 20],
        ['Cancer', 7, 22], ['Leo', 8, 22], ['Virgo', 9, 22],
        ['Libra', 10, 22], ['Scorpio', 11, 21], ['Sagittarius', 12, 21],
        ['Capricorn', 12, 31]
    ];
    foreach ($signs as $s) {
        list($name, $m, $d) = $s;
        if ($month == $m && $day <= $d) return $name;
        if ($month == $m - 1 && $day > $d) return $name;
    }
    return 'Capricorn';
}

function lucidus_generate_prophecy() {
    $weather = lucidus_fetch_weather();
    $news    = lucidus_fetch_news();
    $sign    = lucidus_astrology_sign();
    $content = sprintf('The skies speak of %s. News of "%s" looms large. Under the sign of %s, a new path emerges.', $weather, $news, $sign);
    $scroll_type = lucidus_assign_scroll_type(['weather' => $weather, 'news' => $news]);

    $post_id = wp_insert_post([
        'post_type'    => 'prophecy',
        'post_title'   => 'Prophecy ' . current_time('Y-m-d H:i'),
        'post_content' => $content,
        'post_status'  => 'publish'
    ]);
    if ($post_id) {
        update_post_meta($post_id, 'scroll_type', $scroll_type);
    }

    if ((int) current_time('i') < 5) {
        // reset at the top of the hour
        $old = get_posts([
            'post_type' => 'prophecy',
            'numberposts' => -1,
            'post__not_in' => [$post_id]
        ]);
        foreach ($old as $o) {
            wp_delete_post($o->ID, true);
        }
    }

    // cleanup
    $query = new WP_Query([
        'post_type'      => 'prophecy',
        'posts_per_page' => -1,
        'date_query'     => [
            'before' => gmdate('Y-m-d H:i:s', time() - (4 * 3600 + 20 * 60))
        ]
    ]);
    foreach ($query->posts as $old) {
        wp_delete_post($old->ID, true);
    }
    lucidus_write_archive(['time'=>current_time('mysql'),'prophecy'=>wp_strip_all_tags($content)]);
    do_action('lucidus_prophecy_generated', $post_id, $content);
    return $post_id;
}

add_action('lucidus_prophecy_event', 'lucidus_generate_prophecy');

function lucidus_prophecy_feed() {
    wp_enqueue_script('lucidus-prophecy-feed', plugin_dir_url(dirname(__DIR__)) . 'assets/js/prophecy-feed.js', ['jquery'], '1.0', true);
    wp_enqueue_style('lucidus-chat'); // reuse chat styles
    ob_start();
    echo '<div id="prophecy-feed" style="max-height:300px;overflow-y:auto"></div>';
    do_action('lucidus_prophecy_display');
    return ob_get_clean();
}
add_shortcode('lucidus_prophecy_feed', 'lucidus_prophecy_feed');
?>
