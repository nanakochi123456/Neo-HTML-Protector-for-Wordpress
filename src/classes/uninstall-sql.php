<?php
// Neo HTML Protector drop sql

function neohp_drop_sql() {
	global $wpdb;
	$neohp_table_name = $wpdb->prefix . 'user_ip_log';
	$neohp_table_name = esc_sql($neohp_table_name);

	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query(
		$wpdb->prepare(
			"DROP TABLE IF EXISTS `$neohp_table_name`"
		)
	);
	// phpcs:enable

	$neohp_table_name = $wpdb->prefix . 'view_source_log';
	$neohp_table_name = esc_sql($neohp_table_name);

	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query(
		$wpdb->prepare(
			"DROP TABLE IF EXISTS `$neohp_table_name`"
		)
	);
	// phpcs:enable
}
