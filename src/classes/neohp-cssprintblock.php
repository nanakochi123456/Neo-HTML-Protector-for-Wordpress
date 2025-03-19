<?php
/**
 * Neo HTML Protector neohp_cssprintblock
 */

$neohp_cssprintblock=new neohp_cssprintblock();
class neohp_cssprintblock {

	public function __construct() {
		// �t�����g�G���h��CSS��ǂݍ���
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
	}

	// CSS��o�^���Ĉ����j�~
	public function enqueue_styles() {
		if(get_option('neohp_alert_p', '0') === '1'
		|| get_option('neohp_alert_p', '0') === '2') {
			wp_register_style('neohp-style', false);
			wp_enqueue_style('neohp-style');
			// �C�����C��CSS��ǉ�
			wp_add_inline_style('neohp-style', '@media print{body{display:none !important}}');
		}

	}

/*
			@media print {
				body {
					display: none !important;
				}
			}
*/
}
