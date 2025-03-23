<?php
/**
 * Neo HTML Protector neohp_htmlcompress
 */

$neohp_htmlcompress=new neohp_htmlcompress();
class neohp_htmlcompress {
	protected $neohp_func;

	public function __construct() {
		$this->neohp_func=new neohp_func();

		if(get_option('neohp_deny_imagebot', '0') === '1') {
			if(strpos($this->neohp_func->get_user_agent(), 'mage')) {
				$this->neohp_func->err403();
			}
		}

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

			// 言語を強制する
			if(get_option('neohp_view-source_message_lang', '0') !== '0') {
				if(get_option('neohp_view-source_message_lang', '0') === '1') {
					$lang = $this->neohp_func->getlang();
				} else {
					$lang = get_option('neohp_view-source_message_lang', '0');
				}
				$lang = str_replace('-', '_', $lang);
				switch_to_locale( $lang );
				unload_textdomain('neo-html-protector');
				load_textdomain( 'neo-html-protector', NEOHP_LANG_DIR . 'neo-html-protector-' . $lang . '.mo' );
			}

			// <!doctype html>の前に警告メッセージを表示する
			require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';

			if(get_option('neohp_htmlprotect_message', $neohp_viewsource_default ) !== '') {
				$protectmsg=esc_html(get_option('neohp_htmlprotect_message', $neohp_viewsource_default ) );
				$ua = $this->neohp_func->get_user_agent();
				$current_url = $this->neohp_func->get_current_url();
				$user_ip = $this->neohp_func->get_user_ip();

				$protectmsg = str_replace('$IP', $user_ip, $protectmsg);
				$protectmsg = str_replace('$URL', $current_url, $protectmsg);
				$protectmsg = str_replace('$KEY', "view-source:$current_url", $protectmsg);
				$protectmsg = str_replace('$UA', $ua, $protectmsg);
				$protectmsg = str_replace('\\n', "\n", $protectmsg);

				// アスキーアートの追加
				if(get_option('view_source_alert_asciiart', '0') !== '0') {
					$protectmsg = $warning_ascii_art[get_option('view_source_alert_asciiart', '0')] . "\n\n" . $protectmsg;
				}

				$buffer = "<!--\n\n" . $protectmsg . "\n\n-->" . $buffer;
			}
		}
		return $buffer;
	}
}
