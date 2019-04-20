<?php
require_once(dirname(__FILE__).'/../variables.php');
require(GMB_PATH.'/../../../wp-blog-header.php');
require_once(GMB_PATH."/../../../wp-config.php");
require_once(GMB_PATH."/../../../wp-includes/wp-db.php");


if(!GMB::isUserValid()) {
    echo "<h3>Only Administor can access this page.</h3>";
}

$response = array();

if(!empty($_POST['action'])) {
    $action = $_POST['action'];
    $eles = explode('-', $action);

    if(!empty($eles) && count($eles) == 2) {
        $act = $eles[0];
        $data = $eles[1];
        if($act == 'del') {
            $table_name = $wpdb->prefix . GMB_DB_NAME; 
            $sql = $wpdb->prepare("DELETE FROM $table_name WHERE id=%d", $data);
            $response['sql'] = $sql;

            $wpdb->query($sql);
        } else if($act == 'enable') {
            if($data == '1') {
                update_option('gmb-enabled', 'yes');
            } else if($data == '0') {
                update_option('gmb-enabled', 'no');
            }
        }

        $response['act'] = $act;
        $response['data'] = $data;
    }


}

echo json_encode(array(
    'post' => $_POST,
    'action' => $_POST['action'],
    'resp' => $response,
    'res' => 'ok',
));

exit(0);
?>
