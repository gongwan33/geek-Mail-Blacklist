<?php
//Geek mail monitor class
class GMM {
    public static function deploy_monitor() {
        add_filter( 'login_redirect', array(get_class(), 'login_result'), 10, 3 );
        add_filter( 'login_errors', array(get_class(), 'login_errors'), 10, 3 );
    }

    public static function login_result( $redirect_to, $request, $user ) {
        global $wpdb;

        $table_name = $wpdb->prefix . GMB_DB_NAME_LOGIN_MONITOR; 
        $time = current_time('mysql');

        $res_class = get_class($user); 
        if($res_class == 'WP_User') {
            $sql = $wpdb->prepare("SELECT userid, count FROM $table_name WHERE userid=%d AND result=%d", array($user->data->ID, 1)); 
            $res = $wpdb->get_row($sql);
            
            if(!empty($res)) {
                $sql = $wpdb->prepare("UPDATE $table_name SET time=%s, count=%d WHERE userid=%d AND result=1", array($time, (int) $res->count + 1, $user->data->ID) );
            } else {
                $sql = $wpdb->prepare("INSERT INTO $table_name (time, email, username, userid, result, count) VALUES (%s, %s, %s, %d, %d, %d)", array($time, $user->data->user_email, $user->data->user_login, $user->data->ID, 1, 1) );
            }

            $wpdb->query($sql); 
        } 

        return $redirect_to;
    }

    public static function login_errors($error) {
        global $errors;
        global $wpdb;

        $table_name = $wpdb->prefix . GMB_DB_NAME_LOGIN_MONITOR; 
        $time = current_time('mysql');
        $login = $_POST['log'];

        $err_codes = $errors->get_error_codes();

        if(strpos($login, '@') !== FALSE) {
            $field_name = 'email';
        } else {
            $field_name = 'username';
        }

        $sql = $wpdb->prepare("SELECT userid, count FROM $table_name WHERE $field_name=%s AND result=%d", array($login, 0)); 
        $res = $wpdb->get_row($sql);

        if(!empty($err_codes) && is_array($err_codes)) {
            if(!empty($res)) {
                $sql = $wpdb->prepare("UPDATE $table_name SET time=%s, count=%d WHERE $field_name=%s AND result=0", array($time, (int) $res->count + 1, $login) );
            } else {
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

                $sql = $wpdb->prepare("INSERT INTO $table_name (time, email, username, userid, result, count) VALUES (%s, %s, %s, %d, %d, %d)", array($time, $email, $username, $userid, 0, 1) );
            }

            $wpdb->query($sql);
        }

        return $error;

    }
}
