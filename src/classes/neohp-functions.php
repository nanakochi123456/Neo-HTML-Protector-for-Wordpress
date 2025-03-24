<?php
/**
 * Neo HTML Protector neohp_functions
 */

$neohp_func=new neohp_func();
class neohp_func {
	public function get_user_ip() {
		$ip = '';

		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) );
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {  // ここで isset() を使って明示的に確認
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		if ( filter_var( $ip, FILTER_VALIDATE_IP ) === false ) {
			// 不正なIPアドレスの処理（必要に応じて）
			$ip = __('無効なIPアドレス', 'neo-html-protector');
		}

		return esc_html($ip);
	}

	public function get_user_agent() {
		$ua = '';

		// $_SERVER['HTTP_USER_AGENT'] を最初にサニタイズ
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$ua = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) );
		}

		// ユーザーエージェントが無効かどうかをチェック
		if ( !preg_match( '/^[a-zA-Z0-9;()&=,.\/\s-]+$/', $ua ) ) {
			$ua = __('無効なユーザーエイジェント', 'neo-html-protector');
		}

		return esc_html($ua);
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

	function login() {
		if(get_option('neohp_islogin', 'admin') === 'admin') {
			if ( current_user_can('administrator') ) {
				return is_user_logged_in();
			}
			return false;
		}
		return is_user_logged_in();
}

	function err403() {
		wp_die('403 Forbidden', 'Forbidden', array('response' => 403));
	}

	function head_echo($head) {
		echo wp_kses( $head,
			[
				'link' => [
					'rel' => true,
					'as' => true,
					'type' => true,
					'href' => true,
					'title' => true,
					'id' => true,
					'media' => true,
				],
				'meta' => [
					'name' => true,
					'property' => true,
					'content' => true,
					'http-equiv' => true,
				],
				'style' => [
					'id' => true,
					'type' => true,
					'media' => true,
					'class' => true,
				],
				'script' => [
					'id' => true,
					'src' => true,
					'type' => true,
					'async' => true,
					'defer' => true,
				],
				'title' => true,
				'head' => true,
				'html' => [
					'lang' => true
				],
				'noscript' => true,
				'img' => [
					'src' => true,
					'alt' => true,
					'class' => true,
					'width' => true,
					'height' => true,
					'style' => true,
				],
			]
		);
	}
	function br_die($head) {
		echo wp_kses( $head,
			[
				'br' => true
			]
		);
	}
}
