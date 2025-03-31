<?php
/**
 * Plugin Name:	Neo HTML Protector
 * Plugin URI:	https://github.com/nanakochi123456/Neo-Webp-AVIF-Converter-for-Wordpress	
 * Description:	HTML / Image Protect and Copyright Protect
 * Version:	0.0.72
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * Author: Nano Yozakura
 * Author URI: https://773.moe
 * Domain Path: /languages
 * Text Domain: neo-html-protector
 * License: GPLv2 or later
 */

defined('ABSPATH') or die('Oh! No!');

define( 'NEOHP_VERSION', '0.0.72' );
define( 'NEOHP_REQUIRED_WP_VERSION', '6.0' );
define( 'NEOHP_BUILD', '20250401054834+0900' );
define( 'NEOHP_PLUGIN', __FILE__ );
define( 'NEOHP_PLUGIN_DIR', untrailingslashit( dirname( NEOHP_PLUGIN ) ) );
define( 'NEOHP_LANG_DIR', NEOHP_PLUGIN_DIR . '/languages/' );
define( 'NEOHP_JS_DIR', NEOHP_PLUGIN_DIR . '/js/' );
define( 'NEOHP_JS_URL', untrailingslashit( dirname( plugins_url('neo-html-protection.php', __FILE__) ) ) . '/js/' );
define( 'NEOHP_IMG_DIR', NEOHP_PLUGIN_DIR . '/img/' );
define( 'NEOHP_IMG_URL', untrailingslashit( dirname( plugins_url('neo-html-protection.php', __FILE__) ) ) . '/img/' );
define( 'NEOHP_DOMAIN', 'neo-html-protector');

$neohp = new neohp();

class neohp {
	public function __construct() {
		add_action( 'plugins_loaded', array($this, 'neohp_plugin_load_textdomain' ) );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__) , array($this, 'neohp_setting_actions') );
	}

	public function neohp_plugin_load_textdomain() {
	    load_plugin_textdomain( 'neo-html-protector', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	public function neohp_setting_actions( $actions ) {
		$menu_settings_url	= '<a href="options-general.php?page=neohp-settings">' . __('設定', 'neo-html-protector') . '</a>';
		array_unshift( $actions , $menu_settings_url );
		return $actions;
	}
}

require_once NEOHP_PLUGIN_DIR . '/classes/neohp-functions.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-database.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-javascript.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-imageprotect.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-htmlprotect.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-htmlcompress.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-cssprintblock.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-admin.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-admin-iplogreader.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-jskeyredirect.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-jskeyajax.php';
