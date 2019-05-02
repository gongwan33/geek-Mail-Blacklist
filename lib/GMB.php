<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class GMB {

    public static $cssPath = null;
    public static $class = null;

    const MENU = 'gmb_menu';
    const MENU_ABOUT = 'gmb_menu_about';
    const MENU_SETTINGS = 'gmb_menu_settings';

    public static function init() {
        add_action('admin_menu', array(get_class(), 'registerAdminPages'));
        add_action('admin_enqueue_scripts', array(get_class(), 'enqueueScripts'));
        self::deployBlacklist();
    }

    public static function enqueueScripts($hook) {
        if($hook != 'toplevel_page_gmb_menu') {
            return;
        }
        wp_enqueue_style( 'custom_wp_admin_css_chart', GMB_URL.'/backend/css/chart.min.css' );
        wp_enqueue_style( 'custom_wp_admin_css_gmb', GMB_URL.'/backend/css/gmb.css' );
        wp_enqueue_script( 'custom_wp_admin_js_chart', GMB_URL.'/backend/js/chart.min.js' );
        wp_enqueue_script( 'custom_wp_admin_js_gmb', GMB_URL.'/backend/js/gmb.js', array(), false, true );

        $monitor_chart_data = GMBMonitor::getPeriodData();
        wp_localize_script( 'custom_wp_admin_js_gmb', 'ajaxobject',
            array(
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'ajaxnonce' => wp_create_nonce( 'gmb_ajax' ),
                'monitorchartdata' => $monitor_chart_data,
                'today' => current_time('Y-m-d'),
            )
        );
    }

    public static function check_database_exists($name) {
        global $wpdb;
        $tname = $wpdb->prefix . $name;
        $sql = $wpdb->prepare("SELECT COUNT(1) FROM information_schema.tables WHERE table_schema=%s AND table_name=%s", array($wpdb->dbname, $tname));

        return (int)$wpdb->get_var($sql);
    }

    public static function create_database($sql, $tbname) {
        global $wpdb;
        $table_name = $wpdb->prefix . $tbname; 

        $sql = "CREATE TABLE $table_name ".$sql." $charset_collate;";

        $charset_collate = $wpdb->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function install() {
        $sql = "(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            expression text NOT NULL,
            userid mediumint(9) NOT NULL,
            PRIMARY KEY  (id)
        )";

        self::create_database($sql, GMB_DB_NAME_BLACKLIST);

        $sql = "(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            email varchar(100) NOT NULL,
            username varchar(100) NOT NULL,
            userid mediumint(9) NOT NULL DEFAULT -1,
            result tinyint(1) NOT NULL DEFAULT 0,
            ip varchar(200) NOT NULL DEFAULT '0.0.0.0',
            info varchar(200),
            PRIMARY KEY  (id),
            INDEX (email),
            INDEX (username),
            INDEX (userid),
            INDEX (time),
            INDEX (result),
            INDEX (ip)
        )";

        self::create_database($sql, GMB_DB_NAME_LOGIN_MONITOR);

        $GMB_enabled = get_option('gmb-enabled');
        if(empty($GMB_enabled)) {
            add_option('gmb-enabled', 'no',  '', 'yes');
        }
    }

    public static function uninstall() {
        global $wpdb;
        $table_name = $wpdb->prefix . GMB_DB_NAME_BLACKLIST; 
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query( $sql );

        $table_name = $wpdb->prefix . GMB_DB_NAME_LOGIN_MONITOR; 
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query( $sql );

        delete_option('gmb-enabled');
    }

    public static function isUserValid() {
        if(current_user_can('administrator')) {
            return true;
        } else {
            return false;
        }
    }

    public static function registerAdminPages() {
        if(self::isUserValid()) {
            add_menu_page('Settings', 'Geek Mail Blacklist', 'manage_options', GMB_MENU_ITEM, array(get_class(), 'displayAdminOptions'));
        }
    }

    public static function displayAdminOptions() {
        require_once GMB_PATH . '/backend/settings.php';
    }

    public static function deployBlacklist() {
        $enabled = get_option('gmb-enabled');
        if($enabled == 'yes') {
            add_filter( 'registration_errors', array(get_class(), 'GMB_check_fields'), 10, 3 );
        }
    } 

    public static function GMB_check_fields( $errors, $sanitized_user_login, $user_email ) { 
        global $wpdb;

        $table_name = $wpdb->prefix . GMB_DB_NAME_BLACKLIST; 
        $rules = $wpdb->get_results("SELECT expression FROM $table_name", ARRAY_A);

        if(!empty($rules)) {
            foreach($rules as $rule) {
                $exp = trim($rule['expression']);

                if(substr($exp, 0, 1) == '/' && substr($exp, -1, 1) == '/') {
                    $match_flag = preg_match($exp, $user_email, $matches);

                    if($match_flag == 1) {
                        $errors->add( 'demo_error', '<strong>ERROR</strong>: Sorry! Your Email is not valid or filtered.');
                        return $errors;
                    }
                } else {
                    if($user_email == $exp) {
                        $errors->add( 'demo_error', '<strong>ERROR</strong>: Sorry! Your Email is not valid or filtered.');
                        return $errors;
                    }
                }
            }
        }

        return $errors;
    }
}
?>
