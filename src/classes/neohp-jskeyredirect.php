<?php
/**
 * Neo HTML Protector neohp_jskeyredirect
 */

$neohp_jskeyredirect=new neohp_jskeyredirect();
class neohp_jskeyredirect {

	public function __construct() {
		add_action('template_redirect', array($this, 'custom_redirect_based_on_query') );
	}

	public function custom_redirect_based_on_query() {
		$unixTime = time();

	    if (isset($_GET['neohp']) && $_GET['neohp'] === 'redirect') {

			$redirect_url = esc_url(get_option('neohp_redirect_url', NEOHP_REDIRECT_DEFAULT));
			$redirect_url = add_query_arg('tm', $unixTime, $redirect_url);

	        if (filter_var($redirect_url, FILTER_VALIDATE_URL)) {
	            wp_redirect($redirect_url);
				exit;
			}
	    }
	}
}
