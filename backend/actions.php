<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class GMBActions {
    public static function init() {
        add_action( 'wp_ajax_gmb_enable', array(get_class(), 'ajaxEnableRule'));
        add_action( 'wp_ajax_gmb_del', array(get_class(), 'ajaxDelRule'));
        add_action( 'wp_ajax_gmb_get_rules_page', array(get_class(), 'ajaxGetRulePage'));
    }

    public static function ajaxEnableRule() {
        check_ajax_referer( 'gmb_ajax' );
        self::enableRule();
        wp_die(); // All ajax handlers die when finished
    }

    public static function ajaxDelRule() {
        check_ajax_referer( 'gmb_ajax' );
        self::delRule();
        wp_die(); // All ajax handlers die when finished
    }

    public static function ajaxGetRulePage() {
        check_ajax_referer( 'gmb_ajax' );
        $page = (int) GMBActions::sanitize('num', $_POST['data']); 
        $res = self::getRules(($page - 1)*GMB_DEFAULT_LIMIT, GMB_DEFAULT_LIMIT);
        echo gmb_rules_table($res); 
        wp_die(); // All ajax handlers die when finished
    }

    public static function sanitize($type, $str) {
        switch($type) {
            case 'email':
                return trim(sanitize_email($str));
            case 'regex':
                return sanitize_text_field($str);
            case 'num':
                return trim(preg_replace('/[^\d]/', '', (string)$str));
            case 'boolnum':
                $str = trim(preg_replace('/[^0|1]/', '', (string)$str));

                if(strlen($str) <= 0) {
                    return '1';
                } else {
                    return substr($str, 0, 1);
                }

            default:
                return trim($str);
        }
    }

    public static function addRule() {
        if ( ! isset( $_POST['gmb-form-nonce'] ) 
            || ! wp_verify_nonce( $_POST['gmb-form-nonce'], 'gmb_form' ) 
        ) {
            $err_msg = 'Bad request.';
            return array(
                'res' => false,
                'info' => $err_msg,
            );
        } 

        if(!GMB::isUserValid()) {
            $err_msg = 'Not permitted for current user.';
            return array(
                'res' => false,
                'info' => $err_msg,
            );
        }

        global $wpdb;
        
        $cur_user = wp_get_current_user();
        $userid = $cur_user->ID;
        $time = current_time('mysql');
        $exp = trim($_POST['gmb-bl-rule']);
        $table_name = $wpdb->prefix . GMB_DB_NAME_BLACKLIST; 

        if(substr($exp, 0, 1) == '/' && substr($exp, -1, 1) == '/') {
            $exp = self::sanitize('regex', $exp);
        } else {
            $exp = self::sanitize('email', $exp);
        }

        if(empty($exp)) {
            $err_msg = 'Email format not valid';
            return array(
                'res' => false,
                'info' => $err_msg,
            );
        }

        $sql = $wpdb->prepare("SELECT id FROM $table_name WHERE expression=%s", $exp);
        $res = $wpdb->get_row($sql);

        if(empty($res)) {
            $sql = $wpdb->prepare("INSERT INTO $table_name (expression, time, userid) VALUES (%s, %s, %s)", array($exp, $time, $userid));

            $wpdb->query($sql);
        } else {
            $err_msg = 'Already exist.';
            return array(
                'res' => false,
                'info' => $err_msg,
            );
        }

        return array(
            'res' => true,
            'info' => '',
        );
    }

    public static function getRules($offset = GMB_DEFAULT_OFFSET, $limit = GMB_DEFAULT_LIMIT) {
        if(!GMB::isUserValid()) {
            return;
        }

        global $wpdb;

        $table_name = $wpdb->prefix . GMB_DB_NAME_BLACKLIST; 
        $sql = "SELECT * FROM $table_name ORDER BY time DESC LIMIT $limit OFFSET $offset";
        $res = $wpdb->get_results($sql, ARRAY_A);

        return $res;
    }

    public static function getRuleNum() {
        if(!GMB::isUserValid()) {
            return;
        }

        global $wpdb;

        $table_name = $wpdb->prefix . GMB_DB_NAME_BLACKLIST; 
        $sql = "SELECT COUNT(*) FROM $table_name";
        $res = $wpdb->get_var($sql);

        return $res;
    }

    public static function enableRule() {
        if(!GMB::isUserValid()) {
            return;
        }

        $data = self::sanitize('boolnum', $_POST['data']);

        if($data === '1') {
            update_option('gmb-enabled', 'yes');
        } else if($data === '0') {
            update_option('gmb-enabled', 'no');
        }

        echo json_encode(array(
            'info' => $data,
            'res' => true,
        ));
    }

    public static function delRule() {
        if(!GMB::isUserValid()) {
            return;
        }

        global $wpdb;
        $id = self::sanitize('num', $_POST['data']);

        if(strlen($id) <= 0) {
            echo json_encode(array(
                'info' => 'id not valid',
                'res' => false,
            ));

            return;
        }

        $table_name = $wpdb->prefix . GMB_DB_NAME_BLACKLIST; 
        $sql = $wpdb->prepare("DELETE FROM $table_name WHERE id=%d", $id);
        $response['sql'] = $sql;

        $wpdb->query($sql);

        echo json_encode(array(
            'info' => '',
            'res' => true,
        ));
    }
}

?>
