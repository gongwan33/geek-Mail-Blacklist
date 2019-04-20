<?php

/*
  Plugin Name: Geek Mail Blacklist
  Plugin URI: http://joybin.cn/geek-mail-blacklist
  Description: Block users with certain emails from registering
  Author: Wagner
  Version: 1.0.0

/*

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
require_once(dirname(__FILE__).'/variables.php');

if ( !defined( 'GMB_URL' ) ) {
	define( 'GMB_URL', plugins_url( '', __FILE__ ) );
}

require_once GMB_PATH . '/lib/GMB.php';
register_activation_hook( __FILE__, array( 'GMB', 'install' ) );
register_deactivation_hook( __FILE__, array( 'GMB', 'uninstall' ) );
GMB::init();

