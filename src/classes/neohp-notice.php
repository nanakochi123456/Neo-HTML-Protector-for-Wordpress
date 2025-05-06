<?php
/**
 * Neo HTML Protector neohp_notice
 */

defined('ABSPATH') or die('Oh! No!');

$neohp_notice=new neohp_notice();
class neohp_notice {

	public function __construct() {
		// footerの一番下に要素を加える
		add_action('wp_footer', function() {
			echo '<div id="NeoHPFooter"></div>';
		}, PHP_INT_MAX);
	}

}
