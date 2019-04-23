<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !defined( 'GMB_NAME' ) ) {
	define( 'GMB_NAME', 'Geek Mail Blacklist' );
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

if ( !defined( 'GMB_DB_NAME' ) ) {
	define( 'GMB_DB_NAME', 'gmb_blacklist' );
}

if ( !defined( 'GMB_PATH' ) ) {
	define( 'GMB_PATH', dirname( __FILE__ ));
}

if ( !defined( 'GMB_URL' ) ) {
	define( 'GMB_URL', plugins_url( '', __FILE__ ) );
}
?>
