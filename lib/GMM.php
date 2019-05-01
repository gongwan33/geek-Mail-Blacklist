<?php
//Geek mail monitor class
class GMM {
    public static function deploy_monitor() {
        add_filter( 'login_redirect', array(get_class(), 'login_result'), 10, 3 );
        add_filter( 'login_errors', array(get_class(), 'login_errors'), 10, 3 );
    }

    public static function records_num_control($wpdb, $table_name) {
        $records_num = $wpdb->get_var("SELECT count(*) FROM $table_name");

        if($records_num >= GMB_RECORDS_MAX) {
            $del_num = $records_num - GMB_RECORDS_MAX + 1;
            $sql = "DELETE FROM $table_name ORDER BY time ASC limit $del_num";
            $wpdb->query($sql);
        }
    }

    public static function login_result( $redirect_to, $request, $user ) {
        global $wpdb;

        $ip = GMU::getClientIp();

        if(empty($ip)) {
            $ip = "0.0.0.0";
        }

        $table_name = $wpdb->prefix . GMB_DB_NAME_LOGIN_MONITOR; 
        $time = current_time('mysql');

        $res_class = get_class($user); 
        if($res_class == 'WP_User') {
            self::records_num_control($wpdb, $table_name);

            $sql = $wpdb->prepare("INSERT INTO $table_name (time, email, username, userid, result, ip) VALUES (%s, %s, %s, %d, %d, %s)", array($time, $user->data->user_email, $user->data->user_login, $user->data->ID, 1, $ip) );

            $wpdb->query($sql); 
        } 

        return $redirect_to;
    }

    public static function login_errors($error) {
        global $errors;
        global $wpdb;

        $ip = GMU::getClientIp();

        if(empty($ip)) {
            $ip = "0.0.0.0";
        }

        $table_name = $wpdb->prefix . GMB_DB_NAME_LOGIN_MONITOR; 
        $time = current_time('mysql');
        $login = $_POST['log'];

        $err_codes = $errors->get_error_codes();

        if(strpos($login, '@') !== FALSE) {
            $field_name = 'email';
        } else {
            $field_name = 'username';
        }

        if(!empty($err_codes) && is_array($err_codes)) {
            if($field_name == 'email') {
                $email = $login;
                $user = get_user_by('email', $email);
            } else if($field_name == 'username') {
                $username = $login;
                $user = get_user_by('login', $username);
            }

            if(!empty($user)) {
                $userid = $user->data->ID;
            } else {
                $userid = -1;
            }

            if(count($err_codes) > 0) {
                $err_info = $err_codes[0];
            } else {
                $err_info = 'Not specified.';
            }

            self::records_num_control($wpdb, $table_name);

            $sql = $wpdb->prepare("INSERT INTO $table_name (time, email, username, userid, result, ip, info) VALUES (%s, %s, %s, %d, %d, %s, %s)", array($time, $email, $username, $userid, 0, $ip, $err_info) );

            $wpdb->query($sql);
        }

        return $error;

    }
}
