<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !defined( 'GMB_NAME' ) ) {
	define( 'GMB_NAME', 'Geek Mail Blacklist' );
}

if ( !defined( 'GMB_RECORDS_MAX' ) ) {
	define( 'GMB_RECORDS_MAX', 1000000 );
}

if ( !defined( 'GMB_PLUGIN_FILE' ) ) {
	define( 'GMB_PLUGIN_FILE', __FILE__ );
}

if ( !defined( 'GMB_SLUG_NAME' ) ) {
	define( 'GMB_SLUG_NAME', 'geek-mail-blacklist' );
}

if ( !defined( 'GMB_MENU_ITEM' ) ) {
	define( 'GMB_MENU_ITEM', 'gmb_menu' );
}

if ( !defined( 'GMB_DB_NAME_BLACKLIST' ) ) {
	define( 'GMB_DB_NAME_BLACKLIST', 'gmb_blacklist' );
}

if ( !defined( 'GMB_DB_NAME_LOGIN_MONITOR' ) ) {
	define( 'GMB_DB_NAME_LOGIN_MONITOR', 'gmb_monitor' );
}

if ( !defined( 'GMB_PATH' ) ) {
	define( 'GMB_PATH', dirname( __FILE__ ));
}

if ( !defined( 'GMB_URL' ) ) {
	define( 'GMB_URL', plugins_url( '', __FILE__ ) );
}

if(!defined('GMB_DEFAULT_OFFSET')) {
    define('GMB_DEFAULT_OFFSET', 0);
}

if(!defined('GMB_DEFAULT_LIMIT')) {
    define('GMB_DEFAULT_LIMIT', 15);
}

?>
