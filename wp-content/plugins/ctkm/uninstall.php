<?php
if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove plugin options or custom tables.
// Example:
// delete_option('ctkm_version');
// global $wpdb;
// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ctkm_campaigns");
