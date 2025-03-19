<?php
/**
 * Plugin Name:	Neo HTML Protector
 * Plugin URI:	https://github.com/nanakochi123456/Neo-Webp-AVIF-Converter-for-Wordpress	
 * Description:	HTML Protect and Copyright Protect
 * Version:	0.0.1
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * Author: Nano Yozakura
 * Author URI: https://773.moe
 * Domain Path: /languages
 * Text Domain: neo-html-protector
 * License: GPLv2 or later
 */

defined('ABSPATH') or die('Oh! No!');

define( 'NEOHP_VERSION', '0.0.1' );
define( 'NEOHP_REQUIRED_WP_VERSION', '6.0' );
define( 'NEOHP_PLUGIN', __FILE__ );
define( 'NEOHP_PLUGIN_DIR', untrailingslashit( dirname( NEOHP_PLUGIN ) ) );
define( 'NEOHP_JS_DIR', NEOHP_PLUGIN_DIR . '/js/' );
define( 'NEOHP_CSS_DIR', NEOHP_PLUGIN_DIR . '/css/' );
define( 'NEOHP_JS_URL', untrailingslashit( dirname( plugins_url('neo-html-protection.php', __FILE__) ) ) . '/js/' );
define( 'NEOHP_CSS_URL', untrailingslashit( dirname( plugins_url('neo-html-protection.php', __FILE__) ) ) . '/css/' );
define( 'NEOHP_REDIRECT_DEFAULT', _('https://google.co.jp') );

define( 'NEOHP_DEBUGMODE_DEFAULT', 
	  _('デバッグモード、コンソールの起動は禁止されています') . '\n'
	. _('以下の情報がサーバーに送信されました') . '\n'
	. _('あなたのIPアドレス:$IP') . '\n'
	. _('URL:$URL') . '\n'
	. _('あなたの押下したキー:$KEY') . '\n'
	. _('リダイレクトをします')
	);

define( 'NEOHP_RIGHTCLICK_DEFAULT', 
	  _('右クリックは禁止されています') . '\n'
	. _('以下の情報がサーバーに送信されました') . '\n'
	. _('あなたのIPアドレス:$IP') . '\n'
	. _('URL:$URL') . '\n'
	. _('あなたの押下したキー:$KEY') . '\n'
	. _('リダイレクトをします')
	);

define( 'NEOHP_PRINTOUT_DEFAULT', 
	  _('印刷、PDFへの保存は禁止されています') . '\n'
	. _('以下の情報がサーバーに送信されました') . '\n'
	. _('あなたのIPアドレス:$IP') . '\n'
	. _('URL:$URL') . '\n'
	. _('あなたの押下したキー:$KEY') . '\n'
	. _('リダイレクトをします')
	);

define( 'NEOHP_HTMLSOURCE_DEFAULT', 
	  _('HTMLソース表示は禁止されています') . '\n'
	. _('以下の情報がサーバーに送信されました') . '\n'
	. _('あなたのIPアドレス:$IP') . '\n'
	. _('URL:$URL') . '\n'
	. _('あなたの押下したキー:$KEY') . '\n'
	. _('リダイレクトをします')
	);

define( 'NEOHP_VIEWSOURCE_DEFAULT', 
	  _('HTMLソース表示は禁止されています') . '\n'
	. _('以下の情報がサーバーに送信されました') . '\n'
	. _('あなたのIPアドレス:$IP') . '\n'
	. _('URL:$URL') . '\n'
	. _('あなたが起こしたイベント:view-source:$URL')
	);

require_once NEOHP_PLUGIN_DIR . '/classes/neohp-htmlprotect.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-htmlcompress.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-cssprintblock.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-jskeyblock.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-admin.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-admin-iplogreader.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-jskeyredirect.php';
require_once NEOHP_PLUGIN_DIR . '/classes/neohp-jskeyajax.php';
