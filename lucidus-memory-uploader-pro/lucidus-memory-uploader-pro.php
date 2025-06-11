<?php
/*
Plugin Name: Lucidus Memory Uploader PRO (DBS)
Description: Dead Bastard Society memory manager for Lucidus. Upload, edit, delete, and track prophecy files with ease.
Version: 2.2.0
Author: Dr.G
License: MIT
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Lucidus_Memory_Uploader {
    private static $memory_dir;
    private static $context_dir;
    private static $injections_dir;
    private static $userlog_file;
    private static $insights_file;
    private static $filelog_file;
    private static $index_file;
    private static $config_file;

    public static function init() {
        $upload_base = wp_upload_dir()['basedir'];
        self::$memory_dir   = $upload_base . '/lucidus-memory';
        self::$context_dir  = $upload_base . '/lucidus-context';
        self::$injections_dir = ABSPATH . 'wp-content/dbs-library/memory-archive/injections';
        self::$userlog_file = self::$context_dir . '/lucidus-userlog.json';
        self::$insights_file = self::$context_dir . '/memory-insights.json';
        self::$filelog_file = self::$context_dir . '/filelog.json';
        self::$index_file   = self::$context_dir . '/memory-index.json';
        self::$config_file  = plugin_dir_path(__FILE__) . 'memory-paths.json';
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_post_lucidus_memory_upload', [__CLASS__, 'handle_upload']);
        add_action('admin_post_lucidus_memory_delete', [__CLASS__, 'handle_delete']);
        add_action('admin_post_lucidus_memory_save', [__CLASS__, 'handle_save']);
        add_action('wp_ajax_lucidus_memory_check', [__CLASS__, 'ajax_check_memory']);
        add_action('wp_ajax_lucidus_memory_inject', [__CLASS__, 'ajax_inject']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        register_activation_hook(__FILE__, [__CLASS__, 'activate']);
        add_shortcode('lucidus_memory_files', [__CLASS__, 'shortcode_list_files']);
        add_shortcode('lucidus_memory_log', [__CLASS__, 'shortcode_log']);
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function activate() {
        wp_mkdir_p(self::$memory_dir);
        wp_mkdir_p(self::$context_dir);
        if (!file_exists(self::$userlog_file)) file_put_contents(self::$userlog_file, json_encode([]));
        if (!file_exists(self::$filelog_file)) file_put_contents(self::$filelog_file, json_encode([]));
        if (!file_exists(self::$insights_file)) file_put_contents(self::$insights_file, json_encode(['uploads'=>0,'edits'=>0,'deletes'=>0]));
        if (!file_exists(self::$index_file)) file_put_contents(self::$index_file, json_encode([]));
        if (!file_exists(self::$config_file)) {
            $default = [
                'active' => [self::$memory_dir . '/'],
                'priority_order' => ['local']
            ];
            file_put_contents(self::$config_file, json_encode($default, JSON_PRETTY_PRINT));
        }
    }

    public static function add_menu() {
        add_menu_page('Lucidus Memory PRO', 'Lucidus Memory PRO', 'upload_files', 'lucidus-memory-pro', [__CLASS__, 'memory_page']);
        add_submenu_page('lucidus-memory-pro', 'Memory Dashboard', 'Memory Dashboard', 'manage_options', 'lucidus-memory-dashboard', [__CLASS__, 'dashboard_page']);
        add_submenu_page('lucidus-memory-pro', 'Settings', 'Settings', 'manage_options', 'lucidus-memory-settings', [__CLASS__, 'settings_page']);
        add_submenu_page('lucidus-memory-pro', 'Memory Paths', 'Memory Paths', 'manage_options', 'lucidus-memory-paths', [__CLASS__, 'paths_page']);
    }

    public static function memory_page() {
        if (!current_user_can('upload_files')) {
            return;
        }
        $files = self::get_all_memory_files();

        if (isset($_GET['edit'])) {
            $edit = basename($_GET['edit']);
            $path = self::$memory_dir . '/' . $edit;
            if (file_exists($path)) {
                $content = file_get_contents($path);
                echo '<div class="wrap"><h1>Edit Memory File</h1>';
                echo '<form method="post" action="'.admin_url('admin-post.php').'">';
                echo '<input type="hidden" name="action" value="lucidus_memory_save" />';
                echo '<input type="hidden" name="file" value="'.esc_attr($edit).'" />';
                wp_nonce_field('lucidus_memory_save');
                echo '<textarea name="content" rows="20" style="width:100%">'.esc_textarea($content).'</textarea>';
                submit_button('Save Changes');
                echo '</form></div>';
                return;
            }
        }

        $total = 0;
        echo '<div class="wrap"><h1>Lucidus Memory Files</h1>';
        if (isset($_GET["uploaded"])) echo '<div class="updated notice"><p>File uploaded.</p></div>';
        if (isset($_GET["deleted"])) echo '<div class="updated notice"><p>File deleted.</p></div>';
        if (isset($_GET["saved"])) echo '<div class="updated notice"><p>File saved.</p></div>';
        echo '<form method="post" enctype="multipart/form-data" action="'.admin_url('admin-post.php').'">';
        echo '<input type="hidden" name="action" value="lucidus_memory_upload" />';
        wp_nonce_field('lucidus_memory_upload');
        echo '<input type="file" name="memory_file" required />';
        submit_button('Upload');
        echo '</form>';
        echo '<input type="search" id="lucidus-memory-search" placeholder="Search files" style="margin-top:10px;" />';

        if ($files) {
            echo '<h2>Existing Files</h2><table id="lucidus-memory-table" class="widefat lucidus-memory-table"><thead><tr><th>Name</th><th>Size</th><th>Modified</th><th>Origin</th><th>Actions</th></tr></thead><tbody>';
            foreach ($files as $file) {
                $path = $file["path"];
                $size = size_format(filesize($path));
                $time = date_i18n(get_option('date_format').' '.get_option('time_format'), filemtime($path));
                $total += filesize($path);
                $name = basename($path);
                $origin = esc_html($file["origin"]);
                $edit_link = admin_url('admin.php?page=lucidus-memory-pro&edit=' . urlencode($name));
                $delete_url = wp_nonce_url(admin_url('admin-post.php?action=lucidus_memory_delete&file=' . urlencode($name)), 'lucidus_memory_delete');
                $inject_btn = '<a href="#" class="lucidus-inject" data-file="'.esc_attr($name).'">Test Inject</a>';
                $actions = $file["origin"] === self::$memory_dir . '/' ? '<a href="'.$edit_link.'">Edit</a> | <a href="'.$delete_url.'">Delete</a> | '.$inject_btn : '—';
                echo '<tr><td>'.esc_html($name).'</td><td>'.$size.'</td><td>'.$time.'</td><td>'.$origin.'</td><td>'.$actions.'</td></tr>';
            }
            echo '</tbody></table>';
            echo '<p><strong>Total Memory Used: '.size_format($total).'</strong></p>';
        } else {
            echo '<p>No memory files.</p>';
        }

        echo '<h2>Check Memory Awareness</h2>';
        echo '<input type="text" id="lucidus-memory-pro-query" placeholder="Enter filename" />';
        echo '<button class="button" id="lucidus-memory-pro-check">Ask Lucidus</button>';
        echo '<pre id="lucidus-memory-pro-response"></pre>';
        echo '</div>';
    }

    public static function handle_upload() {
        if (!current_user_can('upload_files') || !check_admin_referer('lucidus_memory_upload')) {
            wp_die('Permission denied');
        }
        if (empty($_FILES['memory_file']['name'])) {
            wp_redirect(admin_url('admin.php?page=lucidus-memory-pro')); exit;
        }
        $file = $_FILES['memory_file'];
        $allowed = ['text/plain','application/json'];
        if (!in_array($file['type'], $allowed)) {
            wp_die('Only .txt or .json files allowed');
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            wp_die('File exceeds 5MB limit');
        }
        wp_mkdir_p(self::$memory_dir);
        $destination = self::$memory_dir . '/' . sanitize_file_name($file['name']);
        if (file_exists($destination)) {
            $info = pathinfo($destination);
            $destination = $info['dirname'].'/'.$info['filename'].'-'.time().'.'.$info['extension'];
        }
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            wp_mkdir_p(self::$context_dir);
            $log_file = self::$context_dir . '/upload-log.txt';
            $entry = date_i18n('Y-m-d H:i:s') . "\tuploaded\t" . basename($destination) . "\n";
            file_put_contents($log_file, $entry, FILE_APPEND);
            if (get_option('lucidus_memory_logging', 1)) {
                self::log_user_action('uploaded', basename($destination));
            }
            self::lucidus_log('memory_uploaded', ['file'=>basename($destination)]);
            wp_redirect(admin_url('admin.php?page=lucidus-memory-pro&uploaded=1')); exit;
        }
        wp_die('Upload failed');
    }

    public static function handle_delete() {
        if (!current_user_can('manage_options') || !check_admin_referer('lucidus_memory_delete')) {
            wp_die('Permission denied');
        }
        if (empty($_GET['file'])) {
            wp_redirect(admin_url('admin.php?page=lucidus-memory-pro')); exit;
        }
        $file = basename($_GET['file']);
        $path = self::$memory_dir . '/' . $file;
        if (file_exists($path)) {
            unlink($path);
            wp_mkdir_p(self::$context_dir);
            $log_file = self::$context_dir . '/upload-log.txt';
            $entry = date_i18n('Y-m-d H:i:s') . "\tdeleted\t" . $file . "\n";
            file_put_contents($log_file, $entry, FILE_APPEND);
            if (get_option('lucidus_memory_logging', 1)) {
                self::log_user_action('deleted', $file);
            }
            self::lucidus_log('memory_deleted', ['file'=>$file]);
        }
        wp_redirect(admin_url('admin.php?page=lucidus-memory-pro&deleted=1')); exit;
    }

    public static function handle_save() {
        if (!current_user_can('manage_options') || !check_admin_referer('lucidus_memory_save')) {
            wp_die('Permission denied');
        }
        if (empty($_POST['file'])) {
            wp_redirect(admin_url('admin.php?page=lucidus-memory-pro')); exit;
        }
        $file = basename($_POST['file']);
        $path = self::$memory_dir . '/' . $file;
        $content = isset($_POST['content']) ? wp_unslash($_POST['content']) : '';
        file_put_contents($path, $content);
        wp_mkdir_p(self::$context_dir);
        $log_file = self::$context_dir . '/upload-log.txt';
        $entry = date_i18n('Y-m-d H:i:s') . "\tedited\t" . $file . "\n";
        file_put_contents($log_file, $entry, FILE_APPEND);
        if (get_option('lucidus_memory_logging', 1)) {
            self::log_user_action('edited', $file);
        }
        self::lucidus_log('memory_edited', ['file'=>$file]);
        wp_redirect(admin_url('admin.php?page=lucidus-memory-pro&saved=1')); exit;
    }

    public static function ajax_check_memory() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        $filename = isset($_POST['file']) ? basename($_POST['file']) : '';
        $path = self::$memory_dir . '/' . $filename;
        if (file_exists($path)) {
            wp_send_json_success('Lucidus acknowledges ' . $filename);
        }
        wp_send_json_error('No such memory found');
    }

    public static function ajax_inject() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
        }
        $filename = isset($_POST['file']) ? basename($_POST['file']) : '';
        $path = self::$memory_dir . '/' . $filename;
        if (!file_exists($path)) {
            wp_send_json_error('File not found');
        }
        self::lucidus_log('memory_injected', ['file'=>$filename]);
        wp_send_json_success('Injected preview of ' . $filename);
    }

    public static function get_memory_files() {
        $files = [];
        foreach (self::get_all_memory_files() as $f) {
            $files[] = file_get_contents($f['path']);
        }
        return $files;
    }

    public static function shortcode_list_files() {
        $files = self::get_all_memory_files();
        if (!$files) {
            return '<p>No memory files.</p>';
        }
        $out = '<ul>';
        foreach ($files as $file) {
            $out .= '<li>' . esc_html(basename($file['path'])) . '</li>';
        }
        $out .= '</ul>';
        return $out;
    }

    public static function shortcode_log() {
        $log_file = self::$context_dir . '/upload-log.txt';
        if (!file_exists($log_file)) {
            return '<p>No log entries.</p>';
        }
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return '<p>No log entries.</p>';
        }
        $out = '<ul>';
        foreach (array_reverse($lines) as $line) {
            $out .= '<li>' . esc_html($line) . '</li>';
        }
        $out .= '</ul>';
        return $out;
    }

    public static function register_routes() {
        register_rest_route('lucidus/v1', '/memory', [
            'methods'  => 'GET',
            'callback' => [__CLASS__, 'api_get_memory_files'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('lucidus/v1', '/context', [
            'methods'  => 'GET',
            'callback' => [__CLASS__, 'api_get_context_log'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('lucidus/v1', '/prophecy-status', [
            'methods'  => 'GET',
            'callback' => [__CLASS__, 'api_prophecy_status'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('lucidus/v1', '/index', [
            'methods'  => 'GET',
            'callback' => [__CLASS__, 'api_get_index'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('lucidus/v1', '/filelog', [
            'methods'  => 'GET',
            'callback' => [__CLASS__, 'api_get_filelog'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('lucidus/v1', '/initiate', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'api_initiate_profile'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function api_get_memory_files() {
        $list = [];
        foreach (self::get_all_memory_files() as $file) {
            $list[] = [
                'name' => basename($file['path']),
                'origin' => $file['origin'],
            ];
        }
        return $list;
    }

    public static function api_get_context_log() {
        $log_file = self::$context_dir . '/upload-log.txt';
        if (!file_exists($log_file)) {
            return [];
        }
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return $lines ?: [];
    }

    public static function api_prophecy_status() {
        $usage = self::scan_memory_usage();
        $recent = self::get_recent_activity();
        $summary = [
            'usage' => $usage,
            'recent' => $recent,
        ];
        return $summary;
    }

    public static function api_initiate_profile( WP_REST_Request $req ) {
        $data = $req->get_json_params();
        if (empty($data['user'])) {
            return new WP_Error('no_user', 'User ID required', ['status' => 400]);
        }
        $dir = self::$memory_dir . '/initiate_profiles';
        wp_mkdir_p($dir);
        $file = $dir . '/' . sanitize_file_name($data['user']) . '.json';
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        return ['saved' => basename($file)];
    }

    public static function api_get_index() {
        if (!file_exists(self::$index_file)) return [];
        return json_decode(file_get_contents(self::$index_file), true) ?: [];
    }

    public static function api_get_filelog() {
        if (!file_exists(self::$filelog_file)) return [];
        return json_decode(file_get_contents(self::$filelog_file), true) ?: [];
    }

    public static function enqueue_assets($hook) {
        if (strpos($hook, 'lucidus-memory') === false) {
            return;
        }
        wp_enqueue_script('lucidus-memory-pro-admin', plugins_url('memory-admin.js', __FILE__), ['jquery'], '1.0', true);
        wp_enqueue_style('lucidus-memory-pro-admin', plugins_url('assets/styles.css', __FILE__), [], '1.0');
        wp_localize_script('lucidus-memory-pro-admin', 'LucidusMemory', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('lucidus_memory_check'),
        ]);
    }

    public static function dashboard_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap"><h1>Memory Dashboard</h1>';
        $usage = self::scan_memory_usage();
        if ($usage) {
            echo '<table class="widefat"><thead><tr><th>Folder</th><th>Files</th><th>Size</th></tr></thead><tbody>';
            foreach ($usage as $folder => $data) {
                echo '<tr><td>' . esc_html($folder) . '</td><td>' . intval($data["count"]) . '</td><td>' . size_format($data["size"]) . '</td></tr>';
            }
            echo '</tbody></table>';
        }

        if (file_exists(self::$insights_file)) {
            $ins = json_decode(file_get_contents(self::$insights_file), true);
            echo '<h2>Totals</h2><p>Uploads: '.intval($ins['uploads']).' | Edits: '.intval($ins['edits']).' | Deletes: '.intval($ins['deletes']).'</p>';
        }

        $recent = self::get_recent_activity();
        if ($recent) {
            echo '<h2>Recent Activity</h2><ul>';
            foreach ($recent as $line) {
                echo '<li>' . esc_html($line) . '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';
    }

    public static function settings_page() {
        if (!current_user_can('manage_options')) return;
        if (isset($_POST['lucidus_memory_settings_nonce']) && wp_verify_nonce($_POST['lucidus_memory_settings_nonce'], 'lucidus_memory_settings')) {
            update_option('lucidus_memory_logging', isset($_POST['logging']) ? 1 : 0);
            update_option('lucidus_memory_scan_uploads', isset($_POST['scan_uploads']) ? 1 : 0);
            echo '<div class="updated"><p>Settings saved.</p></div>';
        }
        $logging = get_option('lucidus_memory_logging', 1);
        $scan = get_option('lucidus_memory_scan_uploads', 1);
        echo '<div class="wrap"><h1>Lucidus Memory Settings</h1><form method="post">';
        wp_nonce_field('lucidus_memory_settings','lucidus_memory_settings_nonce');
        echo '<p><label><input type="checkbox" name="logging" '.checked($logging,1,false).'/> Enable logging</label></p>';
        echo '<p><label><input type="checkbox" name="scan_uploads" '.checked($scan,1,false).'/> Scan uploads directory</label></p>';
        submit_button('Save Settings');
        echo '</form></div>';
    }

    public static function paths_page() {
        if (!current_user_can('manage_options')) return;
        $paths = self::get_memory_paths();
        if (isset($_POST['lucidus_paths_nonce']) && wp_verify_nonce($_POST['lucidus_paths_nonce'], 'lucidus_paths')) {
            $new_paths = array_filter(array_map('trim', explode("\n", $_POST['paths'])));
            self::save_memory_paths($new_paths);
            echo '<div class="updated"><p>Paths updated.</p></div>';
            $paths = $new_paths;
        }
        echo '<div class="wrap"><h1>Memory Paths</h1><form method="post">';
        wp_nonce_field('lucidus_paths','lucidus_paths_nonce');
        echo '<p>One path per line:</p>';
        echo '<textarea name="paths" rows="5" style="width:100%">'.esc_textarea(implode("\n", $paths)).'</textarea>';
        submit_button('Save Paths');
        echo '</form></div>';
    }

    private static function scan_memory_usage() {
        $dirs = [];
        if (get_option('lucidus_memory_scan_uploads', 1)) {
            $dirs = array_merge($dirs, self::get_memory_paths());
        }
        $dirs[] = self::$injections_dir;
        $usage = [];
        foreach ($dirs as $base) {
            if (!is_dir($base)) continue;
            foreach (glob($base.'/*') as $folder) {
                if (!is_dir($folder)) continue;
                $size = 0; $count = 0;
                foreach (glob($folder.'/*') as $file) {
                    if (is_file($file)) {
                        $size += filesize($file);
                        $count++;
                    }
                }
                $name = basename($folder);
                if (!isset($usage[$name])) { $usage[$name] = ["size"=>0,"count"=>0]; }
                $usage[$name]["size"] += $size;
                $usage[$name]["count"] += $count;
            }
        }
        return $usage;
    }

    private static function get_recent_activity() {
        $log_file = self::$context_dir . '/upload-log.txt';
        if (!file_exists($log_file)) return [];
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $recent = [];
        $cutoff = time() - WEEK_IN_SECONDS;
        foreach (array_reverse($lines) as $line) {
            list($time) = explode("\t", $line);
            if (strtotime($time) >= $cutoff) {
                $recent[] = $line;
            }
            if (count($recent) >= 20) break;
        }
        return $recent;
    }

    private static function log_user_action($action, $file) {
        $data = [
            'time' => current_time('mysql'),
            'user' => get_current_user_id(),
            'ip'   => $_SERVER['REMOTE_ADDR'] ?? '',
            'action' => $action,
            'file' => $file,
        ];
        $log = [];
        if (file_exists(self::$userlog_file)) {
            $log = json_decode(file_get_contents(self::$userlog_file), true) ?: [];
        }
        $log[] = $data;
        file_put_contents(self::$userlog_file, json_encode($log));
        self::append_filelog($action, $file);
        self::update_insights($action);
        self::update_index();
    }

    private static function update_insights($action) {
        $data = [
            'uploads' => 0,
            'edits'   => 0,
            'deletes' => 0,
        ];
        if (file_exists(self::$insights_file)) {
            $data = json_decode(file_get_contents(self::$insights_file), true) ?: $data;
        }
        if ($action === 'uploaded') $data['uploads']++;
        if ($action === 'edited') $data['edits']++;
        if ($action === 'deleted') $data['deletes']++;
        file_put_contents(self::$insights_file, json_encode($data));
    }

    private static function append_filelog($action, $file) {
        $log = [];
        if (file_exists(self::$filelog_file)) {
            $log = json_decode(file_get_contents(self::$filelog_file), true) ?: [];
        }
        $log[] = [
            'time' => current_time('mysql'),
            'action' => $action,
            'file' => $file
        ];
        file_put_contents(self::$filelog_file, json_encode($log));
    }

    private static function update_index() {
        $index = [];
        foreach (self::get_all_memory_files() as $f) {
            $index[] = [
                'name' => basename($f['path']),
                'origin' => $f['origin'],
                'size' => filesize($f['path']),
                'modified' => filemtime($f['path'])
            ];
        }
        file_put_contents(self::$index_file, json_encode($index));
    }

    private static function get_memory_paths() {
        if (!file_exists(self::$config_file)) {
            self::activate();
        }
        $cfg = json_decode(file_get_contents(self::$config_file), true);
        return $cfg['active'] ?? [self::$memory_dir . '/'];
    }

    public static function lucidus_log($event, $data = []) {
        $log_file = self::$context_dir . '/lucidus-events.log';
        $entry = [
            'time' => current_time('mysql'),
            'event' => $event,
            'data'  => $data
        ];
        $logs = [];
        if (file_exists($log_file)) {
            $logs = json_decode(file_get_contents($log_file), true) ?: [];
        }
        $logs[] = $entry;
        file_put_contents($log_file, json_encode($logs));
    }

    private static function save_memory_paths($paths) {
        $cfg = [
            'active' => array_values($paths),
            'priority_order' => ['local']
        ];
        file_put_contents(self::$config_file, json_encode($cfg, JSON_PRETTY_PRINT));
    }

    private static function get_all_memory_files() {
        $files = [];
        foreach (self::get_memory_paths() as $path) {
            if (!is_dir($path)) continue;
            foreach (glob(rtrim($path, '/')."/*") as $file) {
                if (is_file($file)) {
                    $files[] = [
                        'path' => $file,
                        'origin' => $path
                    ];
                }
            }
        }
        return $files;
    }
}

Lucidus_Memory_Uploader::init();

?>
