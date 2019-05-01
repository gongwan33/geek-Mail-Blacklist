<?php

if(!defined('GMB_DEFAULT_OFFSET')) {
    define('GMB_DEFAULT_OFFSET', 0);
}

if(!defined('GMB_DEFAULT_LIMIT')) {
    define('GMB_DEFAULT_LIMIT', 30);
}

class GMBMonitor {
    private static $wpdb;
    private static $table_name;

    public static function init() {
        global $wpdb;

        self::$wpdb = $wpdb;
        self::$table_name = $wpdb->prefix . GMB_DB_NAME_LOGIN_MONITOR;

        add_action( 'wp_ajax_gmb_del_records', array(get_class(), 'ajaxDeleteRecords'));
    }

    public static function ajaxDeleteRecords() {
        check_ajax_referer( 'gmb_ajax' );
        self::delRecords();
        wp_die(); // All ajax handlers die when finished
    }

    public static function delRecords() {
        self::$wpdb->query("TRUNCATE ".self::$table_name);
    }

    public static function getRecords($offset = GMB_DEFAULT_OFFSET, $limit = GMB_DEFAULT_LIMIT) {
        $sql = self::$wpdb->prepare("SELECT * FROM ".self::$table_name." ORDER BY time DESC limit %d offset %d", array($limit, $offset));
        return self::$wpdb->get_results($sql, ARRAY_A);
    }

    public static function getCounts($offset = GMB_DEFAULT_OFFSET, $limit = GMB_DEFAULT_LIMIT) {
        $sql = self::$wpdb->prepare("SELECT time, email, username, result, count(id) as count FROM ".self::$table_name." GROUP BY email, username, result ORDER BY count DESC limit %d offset %d", array($limit, $offset));
        return self::$wpdb->get_results($sql, ARRAY_A);
    }
}
