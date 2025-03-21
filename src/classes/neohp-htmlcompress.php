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
		}
		return $buffer;
	}
}
