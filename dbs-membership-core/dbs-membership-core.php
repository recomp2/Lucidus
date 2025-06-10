<?php
/*
Plugin Name: DBS Membership Core
Description: Membership features for the Dead Bastard Society.
Version: 0.1.0
Author: Lucidus Bastardo
*/

if (!defined('ABSPATH')) {
    exit;
}

class DBSMembershipCore {
    const REST_NAMESPACE = 'dbs/v1';
    const LIB_DIR = WP_CONTENT_DIR . '/dbs-library';

    public static function init() {
        add_action('init', [__CLASS__, 'maybe_create_lib']);
        add_shortcode('dbs_initiation_form', [__CLASS__, 'shortcode_initiation']);
        add_shortcode('member_profile', [__CLASS__, 'shortcode_profile']);
        add_shortcode('scroll_wall', [__CLASS__, 'shortcode_scroll_wall']);
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function maybe_create_lib() {
        if (!file_exists(self::LIB_DIR)) {
            wp_mkdir_p(self::LIB_DIR);
        }
    }

    public static function get_file($name) {
        $file = self::LIB_DIR . '/' . $name;
        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
        }
        return $file;
    }

    public static function read_json($name) {
        $file = self::get_file($name);
        return json_decode(file_get_contents($file), true) ?: [];
    }

    public static function write_json($name, $data) {
        $file = self::get_file($name);
        file_put_contents($file, json_encode($data));
    }

    public static function register_routes() {
        register_rest_route(self::REST_NAMESPACE, '/initiate', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'rest_initiate'],
            'permission_callback' => function(){ return true; }
        ]);
        register_rest_route(self::REST_NAMESPACE, '/profile/(?P<id>\\d+)', [
            'methods' => ['GET','POST'],
            'callback' => [__CLASS__, 'rest_profile'],
            'permission_callback' => function(){ return true; }
        ]);
        register_rest_route(self::REST_NAMESPACE, '/scroll', [
            'methods' => ['GET','POST'],
            'callback' => [__CLASS__, 'rest_scroll'],
            'permission_callback' => function(){ return true; }
        ]);
    }

    public static function rest_initiate($request) {
        $data = [
            'name' => sanitize_text_field($request->get_param('name')),
            'email' => sanitize_email($request->get_param('email')),
            'time' => current_time('mysql')
        ];
        $initiations = self::read_json('initiations.json');
        $initiations[] = $data;
        self::write_json('initiations.json', $initiations);
        return rest_ensure_response($data);
    }

    public static function rest_profile($request) {
        $id = (int) $request['id'];
        $profiles = self::read_json('members.json');
        if ($request->get_method() === 'POST') {
            $profiles[$id] = [
                'bio' => sanitize_textarea_field($request->get_param('bio')),
                'updated' => current_time('mysql')
            ];
            self::write_json('members.json', $profiles);
        }
        return rest_ensure_response($profiles[$id] ?? []);
    }

    public static function rest_scroll($request) {
        $scrolls = self::read_json('scrolls.json');
        if ($request->get_method() === 'POST') {
            $scrolls[] = [
                'message' => sanitize_text_field($request->get_param('message')),
                'time' => current_time('mysql')
            ];
            self::write_json('scrolls.json', $scrolls);
        }
        return rest_ensure_response($scrolls);
    }

    public static function shortcode_initiation() {
        ob_start();
        ?>
        <form id="dbs-initiation-form">
            <input type="text" name="name" placeholder="Name" required />
            <input type="email" name="email" placeholder="Email" required />
            <button type="submit">Join the Society</button>
        </form>
        <div id="dbs-initiation-result"></div>
        <script>
        (function(){
            const f=document.getElementById('dbs-initiation-form');
            f.addEventListener('submit',function(e){
                e.preventDefault();
                const fd=new FormData(f);
                fetch('<?php echo esc_url_raw(rest_url(self::REST_NAMESPACE . '/initiate')); ?>',{
                    method:'POST',
                    body:fd,
                    credentials:'same-origin',
                    headers:{'X-WP-Nonce':'<?php echo wp_create_nonce('wp_rest'); ?>'}
                }).then(r=>r.json()).then(d=>{
                    document.getElementById('dbs-initiation-result').textContent='Welcome '+d.name+'!';
                });
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    public static function shortcode_profile($atts) {
        $id = get_current_user_id();
        ob_start();
        ?>
        <div id="dbs-profile"></div>
        <form id="dbs-profile-form">
            <textarea name="bio" placeholder="Your bio"></textarea>
            <button type="submit">Save Profile</button>
        </form>
        <script>
        (function(){
            const out=document.getElementById('dbs-profile');
            const f=document.getElementById('dbs-profile-form');
            function load(){
                fetch('<?php echo esc_url_raw(rest_url(self::REST_NAMESPACE . '/profile/'.$id)); ?>',{
                    credentials:'same-origin'
                }).then(r=>r.json()).then(d=>{out.textContent=d.bio||'';});
            }
            f.addEventListener('submit',function(e){
                e.preventDefault();
                const fd=new FormData(f);
                fetch('<?php echo esc_url_raw(rest_url(self::REST_NAMESPACE . '/profile/'.$id)); ?>',{
                    method:'POST',
                    body:fd,
                    credentials:'same-origin',
                    headers:{'X-WP-Nonce':'<?php echo wp_create_nonce('wp_rest'); ?>'}
                }).then(r=>r.json()).then(load);
            });
            load();
        })();
        </script>
        <?php
        return ob_get_clean();
    }

    public static function shortcode_scroll_wall() {
        ob_start();
        ?>
        <div id="dbs-scroll-wall"></div>
        <form id="dbs-scroll-form">
            <input type="text" name="message" placeholder="Speak" required />
            <button type="submit">Post</button>
        </form>
        <script>
        (function(){
            const wall=document.getElementById('dbs-scroll-wall');
            const f=document.getElementById('dbs-scroll-form');
            function load(){
                fetch('<?php echo esc_url_raw(rest_url(self::REST_NAMESPACE . '/scroll')); ?>').then(r=>r.json()).then(d=>{
                    wall.innerHTML='';
                    d.forEach(m=>{const div=document.createElement('div');div.textContent=m.message+' ('+m.time+')';wall.appendChild(div);});
                });
            }
            f.addEventListener('submit',function(e){
                e.preventDefault();
                const fd=new FormData(f);
                fetch('<?php echo esc_url_raw(rest_url(self::REST_NAMESPACE . '/scroll')); ?>',{
                    method:'POST',
                    body:fd,
                    credentials:'same-origin',
                    headers:{'X-WP-Nonce':'<?php echo wp_create_nonce('wp_rest'); ?>'}
                }).then(load);
            });
            load();
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}

DBSMembershipCore::init();
