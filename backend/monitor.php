<?php
class GMBMonitor {
    public static function getRecords($offset = 0, $limit = 30) {
        global $wpdb;

        $table_name = $wpdb->prefix . GMB_DB_NAME_LOGIN_MONITOR; 
        $sql = $wpdb->prepare("SELECT * FROM $table_name limit %d offset %d", array($limit, $offset));
        return $wpdb->get_results($sql, ARRAY_A);
    }
}
