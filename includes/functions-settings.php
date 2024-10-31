<?php
/**
 * Functions - Settings.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the max number of priorities to be used.
 *
 * @return int The max number of priorities.
 */
function psupsellmaster_settings_get_max_priorities() {
	// Set the max.
	$max = 3;

	// Allow developers to filter this.
	$max = apply_filters( 'psupsellmaster_settings_max_priorities', $max );

	// Return the max.
	return $max;
}

/**
 * Get the max number of upsells to be calculated.
 *
 * @return int The max number of upsells.
 */
function psupsellmaster_settings_get_max_upsells() {
	// Set the max.
	$max = 3;

	// Allow developers to filter this.
	$max = apply_filters( 'psupsellmaster_settings_max_upsells', $max );

	// Return the max.
	return $max;
}
