<?php
/**
 * Neo HTML Protector neohp_functions
 */

defined('ABSPATH') or die('Oh! No!');

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
//		if ( !preg_match( '/^[a-zA-Z0-9;()&=,.\/\s-]+$/', $ua ) ) {
//			$ua = __('無効なユーザーエイジェント', 'neo-html-protector');
//		}

		return esc_html($ua);
	}

	public function get_current_url() {
		// URLを取得
		$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
		$host = (isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : 'Ignore Hostname');
		$request_uri = $this->get_requiest_uri();
		$current_url = $scheme . '://' . $host . $request_uri;
		return $current_url;
	}

	public function get_requiest_uri() {
		// URLを取得
		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		// URL-encoded strings are also included here and cannot be fully encoded, so
		$request_uri = (isset($_SERVER['REQUEST_URI']) ? htmlspecialchars(wp_unslash($_SERVER['REQUEST_URI'])) : '/Ignore Request URI');
		// phpcs:enable
		return $request_uri;

	}

	// ショートnonce
	function create_short_nonce($action) {
		$expires = time() + 9; // 9秒有効
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
		$language = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_ACCEPT_LANGUAGE'])) : 'Ignore Language');

		// 言語と優先度を分割
		$langs = explode(',', $language);

		// 最初の言語を取得（優先度が高いもの）
		return $langs[0];
	}

	function login() {
		if ( ! function_exists( 'is_user_logged_in' ) ) {
			$wp_load_path = dirname(__FILE__);
			while ( $wp_load_path && !file_exists( $wp_load_path . '/wp-load.php' ) ) {
				$wp_load_path = dirname($wp_load_path);
			}
			if ( file_exists( $wp_load_path . '/wp-load.php' ) ) {
				require_once( $wp_load_path . '/wp-load.php' );
			}
		}
		if(get_option('neohp_islogin', 'admin') === 'admin') {
			if ( current_user_can('administrator') ) {
				return is_user_logged_in();
			}
			return false;
		}
		return is_user_logged_in();
	}

	function user() {
		if ( ! function_exists( 'is_user_logged_in' ) ) {
			$wp_load_path = dirname(__FILE__);
			while ( $wp_load_path && !file_exists( $wp_load_path . '/wp-load.php' ) ) {
				$wp_load_path = dirname($wp_load_path);
			}
			if ( file_exists( $wp_load_path . '/wp-load.php' ) ) {
				require_once( $wp_load_path . '/wp-load.php' );
			}
		}
		return is_user_logged_in();
	}

	function err403() {
		wp_die('403 Forbidden', 'Forbidden', array('response' => 403));
	}

	function head_echo($head) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		// Unknown cause, but the design may be broken even if the content in the HTML head tag is exactly the same.
		echo $head;
		// phpcs:enable
/*
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
					'crossorigin' => true,
					'onload' => true,
					'sizes' => true,
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
*/
	}
	function br_die($head) {
		echo wp_kses( $head,
			[
				'br' => true
			]
		);
	}

	function url_safe_base64_encode($data) {
		return str_replace(['+', '/', '='], ['-', '_', '.'], base64_encode($data));
	}

	function url_safe_base64_decode($data) {
		return base64_decode(str_replace(['-', '_', '.'], ['+', '/', '='], $data) );
	}

	function settransient($name, $value, $expire) {
		$user_id = get_current_user_id();
		if ($user_id == 0) {
			// $user_id = session_id(); // セッションIDを使う
			$user_id = sha1($this->get_user_ip());	// IPアドレスを使用
			// MAP-E等で同じIPアドレスを10～100人共有されたとして、1つのWordpressからしたらそう大きくないと考える、それが嫌ならIPV6のレンタルサーバーを利用すべき
		}
		$transient_key = 'neohp_'
			. $this->url_safe_base64_encode(
				sha1 ( $user_id . '_' . $name )
			);
		set_transient( $transient_key, $value, $expire );
	}

	function gettransient($name) {
		$user_id = get_current_user_id();
		if ($user_id == 0) {
			// $user_id = session_id(); // セッションIDを使う
			$user_id = sha1($this->get_user_ip());	// IPアドレスを使用
		}
		$transient_key = 'neohp_'
			. $this->url_safe_base64_encode(
				sha1 ( $user_id . '_' . $name )
			);
		return get_transient( $transient_key );
	}

	function deletetransient($name) {
		$user_id = get_current_user_id();
		if ($user_id == 0) {
			// $user_id = session_id(); // セッションIDを使う
			$user_id = sha1($this->get_user_ip());	// IPアドレスを使用
		}
		$transient_key = 'neohp_'
			. $this->url_safe_base64_encode(
				sha1 ( $user_id . '_' . $name )
			);
		delete_transient( $transient_key );
	}

	// キャッシュ0のヘッダ出力
	function cachezero() {
		header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );
		header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
		header_remove( 'Last-Modified' );
	}

	// botでないことを確認する
	function is_not_bot() {
		$user_agent = mb_strtolower($this->get_user_agent());
		// https://www.casis-iss.org/ex1911/
		return !preg_match('/bot|crawl|slurp|spider|google|y!j|facebook|baidu|yeti|duckduckgo|daum|steeler|sonic|bubing|barkrowler|megaindex|admantx|proximic|mappy|yak|feedly|wordpress/i', $user_agent);
	}

	function is_image_bot() {
		$user_agent = mb_strtolower($this->get_user_agent());
		return preg_match('/mage/i', $user_agent);
	}

	// https://qiita.com/nanakochi123456/items/229ecadb47f2af7dd47d
	function is_ai_bot() {
		$user_agent = mb_strtolower($this->get_user_agent());
		return preg_match('/gptbot|chatgpt-user|copilotbot|copilot-user|google-extended|ccbot|stabilityai|amazonbot|anthropic-ai/i', $user_agent);
	}

	function is_gyotaku() {
		$user_agent = mb_strtolower($this->get_user_agent());
		return preg_match('/ia_archiver|archive\.org_bot|special_archiver|ia_archiver-web\.archive\.org|megalodon/i', $user_agent);
	}

	function is_injustice_ua() {
		$user_agent = mb_strtolower($this->get_user_agent());
		if ( ! preg_match( '/^[a-zA-Z0-9;()&=,.\/\s-]+$/', $user_agent ) ) {
			return true;
		}

		if($user_agent === '') {
			return true;
		}
		return false;
	}

	function is_ie() {	// 旧edgeも対応
		$user_agent = mb_strtolower($this->get_user_agent());
		return preg_match('/msie|trident|edge/i', $user_agent);
	}

}
