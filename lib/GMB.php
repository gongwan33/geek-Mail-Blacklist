<?php
class GMB {

    public static $cssPath = null;
    public static $class = null;

    const MENU = 'gmb_menu';
    const MENU_ABOUT = 'gmb_menu_about';
    const MENU_SETTINGS = 'gmb_menu_settings';

    public static function init() {
        add_action('admin_menu', array(get_class(), 'registerAdminPages'));
        self::deployBlacklist();
    }

    public static function install() {
        global $wpdb;
        $table_name = $wpdb->prefix . GMB_DB_NAME; 

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            expression text NOT NULL,
            userid mediumint(9) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        $GMB_enabled = get_option('gmb-enabled');
        if(empty($GMB_enabled)) {
            add_option('gmb-enabled', 'no',  '', 'yes');
        }
    }

    public static function uninstall() {
        global $wpdb;
        $table_name = $wpdb->prefix . GMB_DB_NAME; 
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query( $sql );

        delete_option('gmb-enabled');
    }

    public static function isUserValid() {
        $cur_user = wp_get_current_user();

        if(!empty($cur_user) && in_array('administrator', $cur_user->roles)) {
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

        $table_name = $wpdb->prefix . GMB_DB_NAME; 
        $rules = $wpdb->get_results("SELECT expression FROM $table_name", ARRAY_A);

        if(!empty($rules)) {
            foreach($rules as $rule) {
                $exp = trim($rule['expression']);

                if(substr($exp, 0, 1) == '/' && substr($exp, -1, 1)) {
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
