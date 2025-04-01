<?php
// Neo HTML Protector drop sql

defined('ABSPATH') || defined('WP_UNINSTALL_PLUGIN') || die('Oh! No!');

function neohp_drop_transient() {
	global $wpdb;

	// _transient_neohp_ で始まるすべてのオプションを削除
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_neohp_%'");

	// _transient_timeout_neohp_ で始まるすべてのオプションを削除
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_neohp_%'");

	// キャッシュをクリア
	wp_cache_flush();
}
