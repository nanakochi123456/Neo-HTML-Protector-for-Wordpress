<?php
/**
 * Neo HTML Protector neohp_functions
 */

$neohp_func=new neohp_func();
class neohp_func {
	public function get_user_ip() {
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return esc_html($ip);
	}

	public function get_user_agent() {
		return substr(esc_html($_SERVER['HTTP_USER_AGENT']), 0, 4096);
	}

	public function get_current_url() {
		// URLを取得
		$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
		$host = $_SERVER['HTTP_HOST'];
		$request_uri = $_SERVER['REQUEST_URI'];
		$current_url = $scheme . '://' . $host . $request_uri;
		return $current_url;
	}

	// ショートnonce
	function create_short_nonce($action) {
		$expires = time() + 10; // 10秒有効
		$nonce = hash_hmac('sha256', $action . '|' . $expires, wp_salt());
		return base64_encode(json_encode([$nonce, $expires]));
	}

	// ショートnonceの検証
	function verify_short_nonce($nonce, $action) {
		$data = json_decode(base64_decode($nonce), true);
		if (!$data || count($data) !== 2) {
			return false;
		}

		list($expected_nonce, $expires) = $data;

		// 有効期限チェック
		if ($expires < time()) {
			return false;
		}

		// HMAC チェック
		$calculated_nonce = hash_hmac('sha256', $action . '|' . $expires, wp_salt());

		return hash_equals($expected_nonce, $calculated_nonce);
	}

	function getlang() {
		// ブラウザの優先言語を取得
		$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

		// 言語と優先度を分割
		$langs = explode(',', $language);

		// 最初の言語を取得（優先度が高いもの）
		return $langs[0];
	}

	function err403() {
		wp_die('403 Forbidden', 'Forbidden', array('response' => 403));
	}
}
