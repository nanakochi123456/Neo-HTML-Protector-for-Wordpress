<?php
/**
 * Neo HTML Protector neohp_jskeyajax
 */

$neohp_jskeyajax=new neohp_jskeyajax();
class neohp_jskeyajax {
	protected $neohp_database;

	public function __construct() {
		// dbを呼び出し
		$this->neohp_database=new neohp_database();

		add_action('template_redirect', array($this, 'neohp_ajax') , 1); // 最も高い優先度で実行
	}

	protected function get_user_ip() {
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	public function neohp_ajax() {
		// セキュリティチェック: リファラーの確認などを実施する
		if ( ! (isset($_GET['neohp']) && $_GET['neohp'] === 'ajax' ) ) {
			return;
		}

//		if (!isset($_POST['sec']) || $_POST['sec'] !== 'papu') {
//			die('403 Forbidden');
//		}

		// ユーザーのIPアドレスを取得
		$user_ip = $this->get_user_ip();
		$url = $_POST['url'];
		$key = $_POST['key'];
//$key="test";
//$url="test";
		// IPアドレスを保存するテーブルがなければ作成
		$wpdb = $this->neohp_database->create_user_ip();

		$ua = substr(esc_html($_SERVER['HTTP_USER_AGENT']), 0, 4096);	// nginxの最大文字数にUAをカットする

		// IPアドレスをテーブルに保存

		$this->neohp_database->insert_user_ip(
			$user_ip, $url, $key, $ua
		);

		$value = 'other error';

		require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
		// debugmode or console
		if (strpos($key, 'F12') !== false
		 || strpos($key, 'I') !== false
		 || strpos($key, 'J') !== false
		) {
			$value = get_option('neohp_debugmode_message', $neohp_debugmode_default);
		}

		// Copy or Cut
		if (strpos($key, 'Copy') !== false
		 || strpos($key, 'Cut') !== false
		) {
			$value = get_option('neohp_copycut_message', $neohp_copycut_default);
		}

		// Ctrl+U
		if (strpos($key, 'U') !== false	) {
			$value = get_option('neohp_htmlsource_message', $neohp_htmlsource_default);
		}

		// Ctrl+P
		if (strpos($key, 'P') !== false	) {
			$value = get_option('neohp_printout_message', $neohp_printout_default);
		}

		// Right Click
		if (strpos($key, 'R') !== false	) {
			$value = get_option('neohp_rightclick_message', $neohp_rightclick_default);
		}


		$value = str_replace('$IP', $user_ip, $value);
		$value = str_replace('$URL', $url, $value);
		$value = str_replace('$KEY', $key, $value);
		$value = str_replace('$UA', $ua, $value);
		$value = str_replace('\\n', "\n", $value);
		echo esc_js( esc_html (htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ) ); // alert で表示
		exit();
	}
}
