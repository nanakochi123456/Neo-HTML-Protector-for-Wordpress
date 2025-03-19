<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;
$neohp_table_name = $wpdb->prefix . 'user_ip_log';
$wpdb->query("DELETE FROM $neohp_table_name");

delete_option('neohp_debugmode_message');
delete_option('neohp_rightclick_message');
delete_option('neohp_printout_message');
delete_option('neohp_htmlsource_message');
delete_option('neohp_htmlprotect_message');
delete_option('neohp_redirect_url');
delete_option('neohp_htmlcompress');
delete_option('neohp_htmlprotect');
delete_option('neohp_alert_f12');
delete_option('neohp_alert_i');
delete_option('neohp_alert_j');
delete_option('neohp_alert_u');
delete_option('neohp_alert_r');
delete_option('neohp_alert_s');
delete_option('neohp_alert_p');
delete_option('neohp_alert_d');

/* for old version */
delete_option('neohp_alert_message');
