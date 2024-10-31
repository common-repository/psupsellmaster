<?php
/**
 * LITE - Admin - Functions - Settings.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the lite settings tabs.
 *
 * @param array $tabs The tabs.
 * @return array The tabs.
 */
function psupsellmaster_lite_admin_get_settings_tabs( $tabs ) {
	// Set the extra.
	$extra = array(
		'upgrade' => array(
			'label' => __( 'Upgrade to PRO!', 'psupsellmaster' ),
			'url'   => PSUPSELLMASTER_PRODUCT_URL,
			'slug'  => 'upgrade',
		),
	);

	// Merge the extra tabs with the tabs.
	$tabs = array_merge( $tabs, $extra );

	// Allow developers to filter this.
	$tabs = apply_filters( 'psupsellmaster_lite_admin_settings_tabs', $tabs );

	// Return the tabs.
	return $tabs;
}
add_filter( 'psupsellmaster_admin_settings_tabs', 'psupsellmaster_lite_admin_get_settings_tabs' );
