<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;
$neohp_table_name = $wpdb->prefix . 'user_ip_log';
$neohp_table_name = esc_sql($neohp_table_name);

// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query(
	$wpdb->prepare(
		"DROP TABLE IF EXISTS `$neohp_table_name`"
	)
);

$neohp_table_name = $wpdb->prefix . 'view_source_log';
$neohp_table_name = esc_sql($neohp_table_name);

// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query(
	$wpdb->prepare(
		"DROP TABLE IF EXISTS `$neohp_table_name`"
	)
);

delete_option('neohp_debugmode_message');
delete_option('neohp_rightclick_message');
delete_option('neohp_printout_message');
delete_option('neohp_htmlsource_message');
delete_option('neohp_htmlprotect_message');
delete_option('neohp_nonceerror_message');
delete_option('neohp_copycut_message');
delete_option('neohp_redirect_url');
delete_option('neohp_htmlcompress');
delete_option('neohp_htmlprotect');
delete_option('html_protect_head');
delete_option('html_protect_nonce');
delete_option('neohp_alert_f12');
delete_option('neohp_alert_i');
delete_option('neohp_alert_j');
delete_option('neohp_alert_u');
delete_option('neohp_alert_r');
delete_option('neohp_alert_s');
delete_option('neohp_alert_c');
delete_option('neohp_alert_p');
delete_option('neohp_alert_d');
delete_option('neohp_alert_message_lang');
delete_option('neohp_view-source_message_lang');
delete_option('neohp_islogin');
