<?php
/**
 * Neo HTML Protector neohp_jskeyajax
 */

$neohp_jskeyajax=new neohp_jskeyajax();
class neohp_jskeyajax {

	public function __construct() {
		add_action('template_redirect', array($this, 'neohp_ajax') , 1); // 最も高い優先度で実行
	}

	public function neohp_ajax() {
		// セキュリティチェック: リファラーの確認などを実施する
		if ( ! (isset($_GET['neohp']) && $_GET['neohp'] === 'ajax' ) ) {
			return;
		}

		if (!isset($_POST['sec']) || $_POST['sec'] !== 'papu') {
			die('403 Forbidden');
		}

		// ユーザーのIPアドレスを取得
		$user_ip = $_SERVER['REMOTE_ADDR'];

		// データベース接続
		//require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'); // WordPress の読み込み

		global $wpdb;
		$table_name = $wpdb->prefix . 'user_ip_log';
		$key = $_POST['key'];

		// IPアドレスを保存するテーブルがなければ作成
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS $table_name (
				id BIGINT NOT NULL AUTO_INCREMENT,
				ip VARCHAR(128) NOT NULL,
				url VARCHAR(1024) NOT NULL,
				keyb VARCHAR(128) NOT NULL,
				timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			)"
		);

		// IPアドレスをテーブルに保存
		$wpdb->insert($table_name, array(
			'ip' => $user_ip,
			'url' => $_POST['url'],
			'keyb' => $key,
		));

		require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
		// debugmode or console
		if (strpos($key, 'F12') !== false
		 || strpos($key, 'I') !== false
		 || strpos($key, 'J') !== false
		) {
			$value = get_option('neohp_debugmode_message', $neohp_debugmode_default);
		}

		// right click
		if (strpos($key, 'Right') !== false	) {
			$value = get_option('neohp_rightclick_message', $neohp_rightclick_default);
		}

		// Ctrl+U
		if (strpos($key, 'U') !== false	) {
			$value = get_option('neohp_htmlsource_message', $neohp_htmlsource_default);
		}

		// Ctrl+P
		if (strpos($key, 'P') !== false	) {
			$value = get_option('neohp_printout_message', $neohp_printout_default);
		}


		$value = str_replace('$IP', $user_ip, $value);
		$value = str_replace('$URL', $_POST["url"], $value);
		$value = str_replace('$KEY', $key, $value);
		$value = str_replace('\\n', "\n", $value);
		echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // alert で表示
		exit();
	}
}
