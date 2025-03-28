<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

require "classes/uninstall-getoptions.php";
neohp_delete_options();

require "classes/uninstall_sql.php";
neohp_drop_sql();
