<?php
/**
 * Neo HTML Protector neohp_jskeyajax
 */

defined('ABSPATH') or die('Oh! No!');

$neohp_jskeyajax=new neohp_jskeyajax();
class neohp_jskeyajax {
	protected $neohp_database;
	protected $neohp_func;

	public function __construct() {
		$this->neohp_func=new neohp_func();
		$this->neohp_database=new neohp_database();

		add_action('template_redirect', array($this, 'neohp_ajax') , 1); // 最も高い優先度で実行
	}

	public function neohp_ajax() {
		// セキュリティチェック: リファラーの確認などを実施する
		if ( ! (isset($_GET['neohp']) && $_GET['neohp'] === 'ajax' ) ) {
			return;
		}

		if ( ! (isset($_GET['neohpnonce']) && $_GET['neohpnonce'] !== '' ) ) {
			return;
		}

		if ( ! wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['neohpnonce'])), 'neohp_action') ) {
			return;
		}

		for($i = 0; $i < 99; $i++) {
			if (ob_get_level() > 0) {
				ob_end_clean();  // バッファがあれば終了
			}
		}

		if (!isset($_POST['sec']) || $_POST['sec'] !== 'none') {
			$this->neohp_func->err403();
		}

		// ユーザーのIPアドレスを取得
		$user_ip = $this->neohp_func->get_user_ip();
		$url = isset($_POST['url']) ? sanitize_text_field(wp_unslash($_POST['url'])) : 'Unknown';
		$key = isset($_POST['key']) ? sanitize_text_field(wp_unslash($_POST['key'])) : 'Unknown';
		$realua = isset($_POST['ua']) ? sanitize_text_field(wp_unslash($_POST['ua'])) : 'Unknown';

		// IPアドレスを保存するテーブルがなければ作成
		$wpdb = $this->neohp_database->create_user_ip();

		$ua = $realua . '<br>' . $this->neohp_func->get_user_agent();

		$this->neohp_database->insert_user_ip(
			$user_ip, $url, $key, $ua
		);

		$value = 'other error';

		// 言語を強制する
		if(get_option('neohp_alert_message_lang', '0') !== '0') {
			if(get_option('neohp_alert_message_lang', '0') === '1') {
				$lang = $this->neohp_func->getlang();
			} else {
				$lang = get_option('neohp_alert_message_lang', '0');
			}
			$lang = str_replace('-', '_', $lang);
			switch_to_locale( $lang );
			unload_textdomain('neo-html-protector');
			load_textdomain( 'neo-html-protector', NEOHP_LANG_DIR . 'neo-html-protector-' . $lang . '.mo' );
		}
		require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';

		// なにもかもページの保存になるのを修正
		$key = str_replace('Shift', 'shift', $key);

		// debugmode or console
		if (strpos($key, 'F12') !== false
		 || strpos($key, 'I') !== false
		 || strpos($key, 'J') !== false
		 || strpos($key, 'K') !== false
		 || strpos($key, 'C') !== false
		) {
			$value = get_option('neohp_debugmode_message', $neohp_debugmode_default);
		}

		// Copy or Cut
		if (strpos($key, 'Copy') !== false
		 || strpos($key, 'Cut') !== false
		) {
			$value = get_option('neohp_copycut_message', $neohp_copycut_default);
		}

		// Ctrl+A
		if (strpos($key, 'A') !== false	) {
			$value = get_option('neohp_textselect_message', $neohp_textselect_default);
		}

		// Ctrl+U
		if (strpos($key, 'U') !== false	) {
			$value = get_option('neohp_htmlsource_message', $neohp_htmlsource_default);
		}

		// Ctrl+P
		if (strpos($key, 'P') !== false ) {
			$value = get_option('neohp_printout_message', $neohp_printout_default);
		}

		// Ctrl+S
		if (strpos($key, 'S') !== false	) {
			$value = get_option('neohp_save_message', $neohp_save_default);
		}

		// Right Click
		if (strpos($key, 'R') !== false	) {
			$value = get_option('neohp_rightclick_message', $neohp_rightclick_default);
$value=$neohp_rightclick_default;
		}

		// PrintScreen
		if (strpos($key, 'PrintScreen') !== false
		 || strpos($key, 'Win') !== false
		 || strpos($key, 'Command') !== false
		 || strpos($key, 'F5') !== false 
		 || strpos($key, 'blur') !== false ) {
			$value = get_option('neohp_printscreen_message', $neohp_printscreen_default);
		}

		// Ctrl+Shift スクリーンショットの疑い
		if (strpos($key, '?') !== false) {
			$value = get_option('neohp_ctrlshift_message', $neohp_ctrlshift_default);
		}

		$key = str_replace('shift', 'Shift', $key);

		$value = str_replace('$IP', $user_ip, $value);
		$value = str_replace('$URL', $url, $value);
		$value = str_replace('$KEY', $key, $value);
		$value = str_replace('$UA', $ua, $value);
		$value = str_replace('\\n', "\n", $value);
		$value = str_replace('<br>', "\n", $value);
		echo esc_js( esc_html ( htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ) ); // alert で表示
		exit();
	}
}
