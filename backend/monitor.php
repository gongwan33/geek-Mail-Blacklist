<?php
class GMBMonitor {
    private static $wpdb;
    private static $table_name;

    public static function init() {
        global $wpdb;

        self::$wpdb = $wpdb;
        self::$table_name = $wpdb->prefix . GMB_DB_NAME_LOGIN_MONITOR;

        add_action( 'wp_ajax_gmb_del_records', array(get_class(), 'ajaxDeleteRecords'));
        add_action( 'wp_ajax_gmb_get_monitor_counts_page', array(get_class(), 'ajaxGetCountPage'));
        add_action( 'wp_ajax_gmb_get_monitor_records_page', array(get_class(), 'ajaxGetRecordPage'));
    }

    public static function ajaxDeleteRecords() {
        check_ajax_referer( 'gmb_ajax' );
        self::delRecords();
        wp_die(); // All ajax handlers die when finished
    }

    public static function ajaxGetCountPage() {
        check_ajax_referer( 'gmb_ajax' );
        $page = (int) GMBActions::sanitize('num', $_POST['data']); 
        $res = self::getCounts(($page - 1)*GMB_DEFAULT_LIMIT, GMB_DEFAULT_LIMIT);
        echo gmb_counts_table($res);
        wp_die(); // All ajax handlers die when finished
    }

    public static function ajaxGetRecordPage() {
        check_ajax_referer( 'gmb_ajax' );
        $page = (int) GMBActions::sanitize('num', $_POST['data']); 
        $res = self::getRecords(($page - 1)*GMB_DEFAULT_LIMIT, GMB_DEFAULT_LIMIT);
        echo gmb_records_table($res);
        wp_die(); // All ajax handlers die when finished
    }

    public static function delRecords() {
        if(!GMB::isUserValid()) {
            return;
        }

        self::$wpdb->query("TRUNCATE ".self::$table_name);
    }

    public static function getRecords($offset = GMB_DEFAULT_OFFSET, $limit = GMB_DEFAULT_LIMIT) {
        if(!GMB::isUserValid()) {
            return;
        }

        $sql = self::$wpdb->prepare("SELECT * FROM ".self::$table_name." ORDER BY time DESC limit %d offset %d", array($limit, $offset));
        return self::$wpdb->get_results($sql, ARRAY_A);
    }

    public static function getRecordNum() {
        if(!GMB::isUserValid()) {
            return;
        }

        $sql = "SELECT COUNT(*) FROM ".self::$table_name;
        return self::$wpdb->get_var($sql);
    }

    public static function getCounts($offset = GMB_DEFAULT_OFFSET, $limit = GMB_DEFAULT_LIMIT) {
        if(!GMB::isUserValid()) {
            return;
        }

        $sql = self::$wpdb->prepare("SELECT time, email, username, result, count(id) as count FROM ".self::$table_name." GROUP BY email, username, result ORDER BY count DESC limit %d offset %d", array($limit, $offset));
        return self::$wpdb->get_results($sql, ARRAY_A);
    }

    public static function getCountsNum() {
        if(!GMB::isUserValid()) {
            return;
        }

        $sql = "SELECT COUNT(*) FROM (SELECT time, email, username, result, count(id) as count FROM ".self::$table_name." GROUP BY email, username, result) AS tb";
        return self::$wpdb->get_var($sql);
    }

    public static function getPeriodData($limit = 30) {
        if(!GMB::isUserValid()) {
            return;
        }

        $date = current_time("Y-m-d");

        $sql = "SELECT DATE_FORMAT(time, '%Y-%c-%e') AS date, result, count(*) AS num".self::$wpdb->prepare(" FROM ".self::$table_name." GROUP BY 1, result ORDER BY date DESC LIMIT %d", array($limit));
        return self::$wpdb->get_results($sql);
    }
}
