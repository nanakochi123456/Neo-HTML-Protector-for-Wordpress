<?php
/**
 * Neo HTML Protector neohp_database
 */

defined('ABSPATH') or die('Oh! No!');

$neohp_database=new neohp_database();
class neohp_database {
	public function create_user_ip() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'user_ip_log';

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `$table_name` (
				id BIGINT NOT NULL AUTO_INCREMENT,
				ip VARCHAR(128) NOT NULL,
				url VARCHAR(1024) NOT NULL,
				keyb VARCHAR(128) NOT NULL,
				ua VARCHAR(4096) NOT NULL,
				timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			)"
		);
		// phpcs:enable
		return $wpdb;
	}

	public function insert_user_ip($ip, $url, $key, $ua) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'user_ip_log';

		$wpdb->insert($table_name, array(
			'ip' => $ip,
			'url' => $url,
			'keyb' => $key,
			'ua' => $ua
		));
	}

	public function drop_user_ip() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'user_ip_log';
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		// phpcs:enable
	}

	public function create_view_source() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'view_source_log';

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS `$table_name` (
				id BIGINT NOT NULL AUTO_INCREMENT,
				ip VARCHAR(128) NOT NULL,
				url VARCHAR(1024) NOT NULL,
				keyb VARCHAR(128) NOT NULL,
				ua VARCHAR(4096) NOT NULL,
				timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			)"
		);
		// phpcs:enable
		return $wpdb;
	}

	public function insert_view_source($ip, $url, $key, $ua) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'view_source_log';

		$wpdb->insert($table_name, array(
			'ip' => $ip,
			'url' => $url,
			'keyb' => $key,
			'ua' => $ua
		));
	}

	public function delete_view_source($key) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'view_source_log';

		$wpdb->delete(
			$table_name, // テーブル名
			$key, // 条件
			array( '%s' ) // プレースホルダ（'%s'は文字列の型）
		);
	}
	
	public function drop_view_source() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'view_source_log';
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
		// phpcs:enable
	}
}
