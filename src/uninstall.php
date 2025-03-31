<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

require plugin_dir_path(__FILE__) . "classes/uninstall-getoptions.php";
neohp_delete_options();

require plugin_dir_path(__FILE__) . "classes/uninstall-sql.php";
neohp_drop_sql();
