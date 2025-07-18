<?php
/**
 * Neo HTML Protector neohp_javascript
 */

defined('ABSPATH') or die('Oh! No!');

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

		if(get_option('neohp_alert_a', '2') === '1')   { $html.="a";}
		if(get_option('neohp_alert_a', '2') === '2')   { $html.="A";}

		if(get_option('neohp_alert_k', '2') === '1')   { $html.="k";}
		if(get_option('neohp_alert_k', '2') === '2')   { $html.="K";}

		if(get_option('neohp_alert_c', '2') === '1')   { $html.="c";}
		if(get_option('neohp_alert_c', '2') === '2')   { $html.="C";}

		if(get_option('neohp_alert_copycut', '2') === '1')   { $html.="y";}
		if(get_option('neohp_alert_copycut', '2') === '2')   { $html.="Y";}

		if(get_option('neohp_alert_d', '2') === '1')   { $html.="d";}
		if(get_option('neohp_alert_d', '2') === '2')   { $html.="D";}

		if(get_option('neohp_imageprotectjs', '0') === '1') { $html.="z";}
		if(get_option('neohp_imageprotectjs', '0') === '2') { $html.="Z";}

		if(get_option('neohp_alert_printscreen', '2') === '1') { $html.="n";}
		if(get_option('neohp_alert_printscreen', '2') === '2') { $html.="N";}

		if(get_option('neohp_alert_blur', '1') === '1') { $html.="w";}
		if(get_option('neohp_alert_blur', '1') === '2') { $html.="W";}

		if(get_option('neohp_alert_ctrlshift', '2') === '1') { $html.="x";}
		if(get_option('neohp_alert_ctrlshift', '2') === '2') { $html.="X";}

		if(get_option('neohp_alert_design', '0') === '1') { $html.="b";}
		if(get_option('neohp_alert_design', '0') === '2') { $html.="B";}

		if(get_option('neohp_alert_beep', '0') === '1') {$html.="e";}
		if(get_option('neohp_alert_beep', '0') === '2') {$html.="E";}

		if(get_option('neohp_alert_mouse', '1') === '1') {$html.="m";}

		$home = home_url();
		$nonce = wp_create_nonce('neohp_action');
		$time = (int)get_option('neohp_redirect_times', '5');
		$plugin = NEOHP_AUDIO_URL;

		// サウンド
		if(get_option('neohp_alert_sound', '0') !== '0') {
			$plugin .= get_option('neohp_alert_sound', '0') . '.m4a';
		} else {
			$plugin = '';
		}

		// 一時使用トークンの有効期限
		$nonce_expire = get_option('neohp_nonce_expire', '20');

		// プライバシーポリシーのページ
		$privacy_page_id = get_option( 'wp_page_for_privacy_policy' );
		$privacy_page_url = get_permalink( $privacy_page_id );

		// 利用規約のページ
		$teams_page_id = get_option('neohp_teams_page');
		$teams_page_url = '';
		if($teams_page_id) {
			$teams_page_url = get_permalink( $teams_page_id );
		}

		// 言語を強制する
		if(get_option('neohp_agree_message_lang', '0') !== '0') {
			if(get_option('neohp_agree_message_lang', '0') === '1') {
				$lang = $this->neohp_func->getlang();
			} else {
				$lang = get_option('neohp_agree_message_lang', '0');
			}
			$lang = str_replace('-', '_', $lang);
			switch_to_locale( $lang );
			unload_textdomain('neo-html-protector');
			load_textdomain( 'neo-html-protector', NEOHP_LANG_DIR . 'neo-html-protector-' . $lang . '.mo' );
		}
		require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';

		$searchengine=get_option('neohp_searchengine', $neohp_google_default);
		$cookie1=get_option('neohp_cookie1', $neohp_cookie1_default);
		$cookie2=get_option('neohp_cookie2', $neohp_cookie2_default);
		$cookie3=get_option('neohp_cookie3', $neohp_cookie3_default);
		$agree=get_option('neohp_agree', $neohp_cookie_agree_default);
		$noagree=get_option('neohp_noagree', $neohp_cookie_noagree_default);
		$confirm=get_option('neohp_confirm', $neohp_confirm_default);
		$p3p=get_option('neohp_p3p', $neohp_p3p_default);
		$teams=get_option('neohp_teams', $neohp_teams_default);
		$gdpr=get_option('neohp_gdpr', "0");
		$blur=get_option('neohp_blur', "5");
		$dark=get_option('neohp_dark', "0");
		$transmission=get_option('neohp_transmission', '0.9');

		$script = "const 
			NeoHPHome='"  . esc_js($home) . "',";
		if($plugin !== '') {
			$script .= "
				NeoHPPlag='"  . esc_js($plugin)."',";
		}

		$script .= "
			NeoHPExpire='". esc_js($nonce_expire) . "',
			NeoHPPage='"  . esc_js($this->neohp_func->get_current_url()) . "',
			NeoHPSite='"  . esc_js(get_site_url()) . "',
			NeoHPFlg='"   . esc_js($html) . "',
			NeoHPTime="   . esc_js($time) . ",
			NeoHPnonce='" . esc_js($nonce) . "',
			NeoHPpp='"	  . esc_js($privacy_page_url) . "',
			NeoHPtt='"	  . esc_js($teams_page_url) . "',
			NeoHPppstr='" . esc_js($p3p) . "',
			NeoHPttstr='" . esc_js($teams) . "',
			NeoHPSearch='". esc_js($searchengine) . "',
			NeoHPCook1='" . esc_js($cookie1) . "',
			NeoHPCook2='" . esc_js($cookie2) . "',
			NeoHPCook3='" . esc_js($cookie3) . "',
			NeoHPAgree='" . esc_js($agree) . "',
			NeoHPNoAgree='".esc_js($noagree) . "',
			NeoHPConfirm='".esc_js($confirm) . "',
			NeoHPGDPR="	  . esc_js($gdpr) . ",
			NeoHPDARK="	  . esc_js($dark) . ",
			NeoHPTRAN="	  . esc_js($transmission) . ",
			NeoHPBLUR="	  . esc_js($blur) . ";
		";

		// forlocal.min.js
		$script .= '(()=>{location.href.startsWith(NeoHPSite)||(document.querySelectorAll("*").forEach(a=>{a.style.backgroundColor="black";a.style.color="black"}),document.querySelectorAll("div,svg,canvas,img,video,audio,iframe").forEach(a=>{a.style.display="none"}))})();';

		$script = preg_replace('/[\r\n\t]+/', '', $script);

		// 言語を戻す
		$lang = $this->neohp_func->getlang();
		unload_textdomain('neo-html-protector');
		load_textdomain( 'neo-html-protector', NEOHP_LANG_DIR . 'neo-html-protector-' . $lang . '.mo' );

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
			$script_path = NEOHP_JS_DIR . 'crypto-js.js';
			$script_url = NEOHP_JS_URL . 'crypto-js.js';
			$version = file_exists($script_path) ? filemtime($script_path) : false;

			$scriptmin_path = NEOHP_JS_DIR . 'crypto-js.min.js';
			$scriptmin_url = NEOHP_JS_URL . 'crypto-js.min.js';
			$versionmin = file_exists($scriptmin_path) ? filemtime($scriptmin_path) : false;

			if($versionmin < $version) {
				wp_enqueue_script('crypto-js', $script_url, array(), $version, true);
			} else {
				wp_enqueue_script('crypto-js', $scriptmin_url, array(), $versionmin, true);
			}
		}
	}
}
