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
// We use a more specific pattern to avoid matching unintended data.
$session_key = 'duplicate_order_prevention_duplicates';

// Get the table name with proper prefix.
$sessions_table = $wpdb->prefix . 'woocommerce_sessions';

// Check if the table exists before attempting cleanup.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$table_exists = $wpdb->get_var(
	$wpdb->prepare(
		'SHOW TABLES LIKE %s',
		$sessions_table
	)
);

if ( $table_exists ) {
	// Clean up session data containing our specific key.
	// Using a specific serialized pattern to avoid false positives.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}woocommerce_sessions WHERE session_value LIKE %s",
			'%s:' . strlen( $session_key ) . ':"' . $session_key . '"%'
		)
	);
}

// Note: This plugin doesn't store any options, post meta, or user meta,
// so no additional cleanup is needed. If you add such features in the future,
// add cleanup code here.
