<?php
/**
 * Neo HTML Protector neohp_htmlcompress
 */

$neohp_htmlcompress=new neohp_htmlcompress();
class neohp_htmlcompress {
	public function __construct() {
		if(get_option('neohp_htmlcompress', '1') == 1) {
			add_action('template_redirect', array($this, 'start_buffer'));
			add_action('shutdown', array($this, 'end_buffer'));
		}
	}

	// 出力バッファを開始
	public function start_buffer() {
		ob_start(array($this, 'sanitize_output'));
	}

	// 出力バッファを終了
	public function end_buffer() {
		if (ob_get_length()) {
			ob_end_flush();
		}
	}

	// HTML圧縮
	protected function sanitize_output($buffer) {
		if(!is_user_logged_in()) {
			$search = array(
				'#\s\/\>#s',			// XMLの /> を圧縮
				'#\>[^\S ]+#s', 		// タグの後の空白を削除
				'#[^\S ]+\<#s', 		// タグの前の空白を削除
				'#(\s)+#s', 			// 連続した空白を削除
				'#(\t)+#s', 			// 連続したタブを削除
				'#<!--[\s\S]*?-->#s',	// コメントを削除
				'#type=\'text/javascript\'#s',   // 今は不要なものを削除
				'#\t#s',				// 連続したタブを削除
			);
			$replace = array(
				'>',
				'>',
				'<',
				'\\1',
				'',
				'\\1',
				' ',
				' ',
				' ',
				'',
				'',
			);
			$buffer = preg_replace($search, $replace, $buffer);
			$buffer = preg_replace($search, $replace, $buffer);

			// <!doctype html>の前に警告メッセージを表示する
			require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';

			if(get_option('neohp_htmlprotect_message', $neohp_viewsource_default ) !== '') {
				$protectmsg=esc_html(get_option('neohp_htmlprotect_message', $neohp_viewsource_default ) );
				$ua = substr(esc_html($_SERVER['HTTP_USER_AGENT']), 0, 4096);	// nginxの最大文字数にUAをカットする

				// ユーザーのIPアドレスを取得
				$user_ip = $this->get_user_ip();

				$protectmsg = str_replace('$IP', $user_ip, $protectmsg);
				$protectmsg = str_replace('$URL', $current_url, $protectmsg);
				$protectmsg = str_replace('$KEY', "view-source:$current_url", $protectmsg);
				$protectmsg = str_replace('$UA', $ua, $protectmsg);
				$protectmsg = str_replace('\\n', "\n", $protectmsg);
				$buffer = "<!--\n\n" . $protectmsg . "\n\n-->" . $buffer;
			}

		}
		return $buffer;
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
}
