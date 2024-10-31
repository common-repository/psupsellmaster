<?php
/**
 * LITE - Functions.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Require/include the files.
 */
function psupsellmaster_lite_includes() {
	// Require the files.
	require_once PSUPSELLMASTER_DIR . 'includes/lite/functions-settings.php';
	require_once PSUPSELLMASTER_DIR . 'includes/lite/functions-tracking.php';

	// Check if we are in the admin.
	if ( is_admin() ) {
		// Require the files.
		require_once PSUPSELLMASTER_DIR . 'includes/lite/admin/functions-notices.php';
		require_once PSUPSELLMASTER_DIR . 'includes/lite/admin/functions-wp-plugins.php';
		require_once PSUPSELLMASTER_DIR . 'includes/lite/admin/functions-settings.php';
		require_once PSUPSELLMASTER_DIR . 'includes/lite/admin/functions-campaigns.php';
	}
}
add_action( 'psupsellmaster_includes_after', 'psupsellmaster_lite_includes' );
