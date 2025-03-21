<?php
/**
 * Neo HTML Protector neohp_jskeyblock
 */

$neohp_jskeyblock=new neohp_jskeyblock();
class neohp_jskeyblock {
	public function __construct() {
		// JavaScript挿入
		add_action('wp_enqueue_scripts', array($this, 'neohp_script') );

		// フラグのJavaScript挿入
		add_action('wp_head', array($this, 'neohp_flagscript'), 99 );
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

	    if(get_option('neohp_alert_p', '1') === '1')   { $html.="p";}
	    if(get_option('neohp_alert_p', '1') === '2')   { $html.="P";}

	    if(get_option('neohp_alert_d', '0') === '1')   { $html.="d";}
		$home = home_url();
		$nonce = wp_create_nonce('neohp_action');
	    ?>
<script id='neohp'>var NeoHPHome="<?php echo esc_js($home) ?>",NeoHPFlg="<?php echo esc_js($html) ?>",NeoHPnonce="<?php echo esc_js($nonce) ?>"</script>
		<?php
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
	    if(!is_user_logged_in()) {
			if($versionmin < $version) {
		        wp_enqueue_script('neohp-script', $script_url, array('jquery'), $version, true);
			} else {
		        wp_enqueue_script('neohp-script', $scriptmin_url, array('jquery'), $versionmin, true);
			}
	    }
	}
}
