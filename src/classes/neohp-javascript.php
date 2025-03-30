<?php
/**
 * Neo HTML Protector neohp_javascript
 */

// https://github.com/brix/crypto-js/tags
define( 'NEOHP_REQUIRED_CRYPTO_JS_VERSION', '4.2.0' );

$neohp_javascriptexec = 0;
$neohp_javascript=new neohp_javascript();

class neohp_javascript {
	protected $neohp_func;
	public function __construct() {
		$this->neohp_func=new neohp_func();

		// JavaScript挿入
		add_action('wp_enqueue_scripts', array($this, 'neohp_script'), 100 );

		// フラグのJavaScript挿入
		add_action('wp_head', array($this, 'neohp_flagscript'), 101 );
	}

	public function neohp_flagscript() {
		// フラグのJavaScriptの挿入
		$html="";
		if(get_option('neohp_alert_f12', '2') === '1') { $html.="f";}
		if(get_option('neohp_alert_f12', '2') === '2') { $html.="F";}

		if(get_option('neohp_alert_i', '2') === '1')   { $html.="i";}
		if(get_option('neohp_alert_i', '2') === '2')   { $html.="I";}

		if(get_option('neohp_alert_j', '2') === '1')   { $html.="j";}
		if(get_option('neohp_alert_j', '2') === '2')   { $html.="J";}

		if(get_option('neohp_alert_u', '2') === '1')   { $html.="u";}
		if(get_option('neohp_alert_u', '2') === '2')   { $html.="U";}

		if(get_option('neohp_alert_r', '2') === '1')   { $html.="r";}
		if(get_option('neohp_alert_r', '2') === '2')   { $html.="R";}

		if(get_option('neohp_alert_s', '2') === '1')   { $html.="s";}
		if(get_option('neohp_alert_s', '2') === '2')   { $html.="S";}

		if(get_option('neohp_alert_t', '1') === '1')   { $html.="t";}
		if(get_option('neohp_alert_t', '1') === '2')   { $html.="T";}

		if(get_option('neohp_alert_p', '1') === '1')   { $html.="p";}
		if(get_option('neohp_alert_p', '1') === '2')   { $html.="P";}

		if(get_option('neohp_alert_d', '0') === '1')   { $html.="d";}

		if(get_option('neohp_imageprotectjs', '0') === '1') { $html.="z";}
		if(get_option('neohp_imageprotectjs', '0') === '2') { $html.="Z";}

		if(get_option('neohp_alert_printscreen', '2') === '1') { $html.="a";}
		if(get_option('neohp_alert_printscreen', '2') === '2') { $html.="A";}

		$home = home_url();
		$nonce = wp_create_nonce('neohp_action');

		$script = "const 
			NeoHPHome='"  . esc_js($home) . "',
			NeoHPFlg='"   . esc_js($html) . "',
			NeoHPnonce='" . esc_js($nonce) . "';
		";
		$script = preg_replace('/[\r\n\t]+/', '', $script);

		global $neohp_javascriptexec;
		if ($neohp_javascriptexec == 0) {
			wp_add_inline_script('neohp', $script, 'before');
		}
		$neohp_javascriptexec = 1;
	}

	// JavaScript挿入
	public function neohp_script() {
		$script_path = NEOHP_JS_DIR . 'neo-html-protect.js';
		$script_url = NEOHP_JS_URL . 'neo-html-protect.js';
		$version = file_exists($script_path) ? filemtime($script_path) : false;

		$scriptmin_path = NEOHP_JS_DIR . 'neo-html-protect.min.js';
		$scriptmin_url = NEOHP_JS_URL . 'neo-html-protect.min.js';
		$versionmin = file_exists($scriptmin_path) ? filemtime($scriptmin_path) : false;

		// ログインしてないときのみ
		if( ! $this->neohp_func->login() ) {
			$this->add_cryptojs_script();

			if($versionmin < $version) {
				wp_enqueue_script('neohp', $script_url, array('jquery'), $version, true);
			} else {
				wp_enqueue_script('neohp', $scriptmin_url, array('jquery'), $versionmin, true);
			}
		}

	}

	function add_cryptojs_script() {
		if(get_option('neohp_imageprotectjs', '0') !== '0') {
			wp_enqueue_script(
				'crypto-js', // ハンドル名
				'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/' . NEOHP_REQUIRED_CRYPTO_JS_VERSION . '/crypto-js.min.js', // 外部URL
				array(), // 依存するスクリプト（今回は無し）
				NEOHP_VERSION, // バージョン番号（最新を指定する場合はnull）
				true // フッターに追加するかどうか（trueでフッター）
			);
		}
	}
}
