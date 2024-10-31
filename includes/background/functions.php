<?php
/**
 * Functions - Background Process.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check the queue and try to start the background processes.
 */
function psupsellmaster_bp_maybe_run_queue() {
	// Get the lock.
	$lock = psupsellmaster_bp_get_queue_lock();

	// Check if the lock is true.
	if ( true === $lock ) {
		return false;
	}

	// Set the queue option name.
	$option_name = 'psupsellmaster_bp_queue';

	// Get the queue option.
	$queue = get_option( $option_name, array() );

	// Check if the queue is empty.
	if ( empty( $queue ) ) {
		return false;
	}

	// Set the transient.
	psupsellmaster_bp_insert_queue_lock();

	// Get the first item.
	$item = array_shift( $queue );

	// Check if the key is set.
	if ( isset( $item['key'] ) ) {
		// Get the args.
		$args = isset( $item['args'] ) ? $item['args'] : array();

		// Check the item.
		if ( 'edd_prices' === $item['key'] && psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Maybe start the background process.
			psupsellmaster_bp_edd_prices_maybe_start( $args );

			// Check the item.
		} elseif ( 'scores' === $item['key'] ) {
			// Maybe start the background process.
			psupsellmaster_bp_scores_maybe_start( $args );

			// Check the item.
		} elseif ( 'analytics_orders' === $item['key'] ) {
			// Maybe start the background process.
			psupsellmaster_bp_analytics_orders_maybe_start();

			// Check the item.
		} elseif ( 'analytics_upsells' === $item['key'] ) {
			// Maybe start the background process.
			psupsellmaster_bp_analytics_upsells_maybe_start();
		}
	}

	// Check if the queue is empty.
	if ( empty( $queue ) ) {
		// Delete the queue option.
		delete_option( $option_name );
	} else {
		// Set the queue option.
		update_option( $option_name, $queue, false );
	}
}
add_action( 'admin_init', 'psupsellmaster_bp_maybe_run_queue' );

/**
 * Set a transient for a background process.
 *
 * @param string $transient  The transient name.
 * @param mixed  $value      The transient value.
 * @param int    $expiration The expiration time in seconds. Default is 2 hours for background processes.
 */
function psupsellmaster_bp_set_transient( $transient, $value, $expiration = HOUR_IN_SECONDS * 2 ) {
	// Set the transient.
	set_transient( $transient, $value, $expiration );
}

/**
 * Insert the queue lock transient.
 */
function psupsellmaster_bp_insert_queue_lock() {
	// Set the transient.
	psupsellmaster_bp_set_transient( 'psupsellmaster_bp_queue_lock', true );
}

/**
 * Delete the queue lock transient.
 */
function psupsellmaster_bp_delete_queue_lock() {
	// Delete the transient.
	delete_transient( 'psupsellmaster_bp_queue_lock' );
}

/**
 * Get the queue lock transient.
 *
 * @return boolean Whether the queue is locked.
 */
function psupsellmaster_bp_get_queue_lock() {
	// Get the transient.
	$transient = get_transient( 'psupsellmaster_bp_queue_lock' );
	$transient = filter_var( $transient, FILTER_VALIDATE_BOOLEAN );

	// Return the transient.
	return $transient;
}
