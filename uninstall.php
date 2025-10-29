<?php
/**
 * Uninstall Duplicate Order Prevention for WooCommerce
 *
 * @package Duplicate_Order_Prevention
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Clean up session data for all users (WooCommerce sessions are stored in database).
global $wpdb;

// Delete any session data related to this plugin.
// WooCommerce sessions are stored in wp_woocommerce_sessions table.
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->prefix}woocommerce_sessions WHERE session_value LIKE %s",
		'%duplicate_order_prevention_duplicates%'
	)
);

// Note: This plugin doesn't store any options, post meta, or user meta,
// so no additional cleanup is needed. If you add such features in the future,
// add cleanup code here.
