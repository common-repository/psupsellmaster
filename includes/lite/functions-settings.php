<?php
/**
 * LITE - Functions - Settings.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the lite setting value.
 *
 * @param mixed  $value The value.
 * @param string $field The field key.
 * @return mixed The value.
 */
function psupsellmaster_lite_settings_get_value( $value, $field ) {
	// Check the field.
	switch ( $field ) {
		case 'cleandata_interval':
			// Set the value.
			$value = '1-month';

			// Stop.
			break;

		case 'default_upsell_products':
			// Check if the value.
			if ( is_array( $value ) ) {
				// Get the limit.
				$limit = psupsellmaster_get_feature_limit( 'default_upsells' );

				// Set the value.
				$value = array_slice( $value, 0, $limit );
			}

			// Stop.
			break;
	}

	// Return the value.
	return $value;
}
add_filter( 'psupsellmaster_settings_get_value', 'psupsellmaster_lite_settings_get_value', 10, 2 );
