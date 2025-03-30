<?php
/**
 * Neo HTML Protector neohp_jskeyredirect
 */

$neohp_jskeyredirect=new neohp_jskeyredirect();
class neohp_jskeyredirect {
	protected $neohp_func;

	public function __construct() {
		$this->neohp_func=new neohp_func();
		add_action('template_redirect', array($this, 'custom_redirect_based_on_query') );
	}

	public function custom_redirect_based_on_query() {
		$unixTime = time();

	    if (isset($_GET['neohp']) && $_GET['neohp'] === 'redirect') {
			if(!isset($_GET['neononce'])
			|| !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['neononce'])), 'neohp_action')) {
				$this->neohp_func->err403();
			}

			require NEOHP_PLUGIN_DIR . '/classes/neohp-global.php';
			$redirect_url = esc_url(get_option('neohp_redirect_url', $neohp_redirect_default));

			if($redirect_url == '') {
				if( isset($_GET['page']) ) {
					$redirect_url = sanitize_text_field(wp_unslash($_GET['page']));
				}
			}
			$redirect_url = add_query_arg('tm', $unixTime, $redirect_url);

	        if (filter_var($redirect_url, FILTER_VALIDATE_URL)) {
	            wp_redirect($redirect_url);
				exit;
			}
	    }
	}
}
