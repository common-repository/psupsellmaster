<?php
/**
 * Admin - Functions - WP Plugins.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run on admin_init.
 * Redirect to setup wizard on activate.
 */
function psupsellmaster_on_activate() {
	// Get the transient.
	$transient = get_transient( 'psupsellmaster_activate' );

	// Check the transient.
	if ( ! $transient ) {
		return;
	}

	// Delete the transient.
	delete_transient( 'psupsellmaster_activate' );

	// Get the status.
	$status = get_option( 'psupsellmaster_admin_setup_wizard_status' );

	// Check the status.
	if ( 'completed' === $status ) {
		return;
	}

	// Redirect.
	wp_safe_redirect( admin_url( 'admin.php?page=psupsellmaster-wizard' ) );

	// Exit.
	exit;
}
add_action( 'admin_init', 'psupsellmaster_on_activate' );
