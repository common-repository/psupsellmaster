<?php
/**
 * Integrations - Easy Digital Downloads - Admin - Functions - Edit Product.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the meta boxes for the product.
 */
function psupsellmaster_edd_add_meta_boxes() {
	// Check if the Easy Digital Downloads plugin is not enabled.
	if ( ! psupsellmaster_is_plugin_active( 'edd' ) ) {
		return false;
	}

	// Get the product post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the screens.
	$screens = array( $post_type );

	// Loop through the screens.
	foreach ( $screens as $screen ) {
		// Add a meta box.
		add_meta_box(
			'psupsellmaster_edd_product_meta_box_upsells',
			__( 'UpsellMaster - Upsells', 'psupsellmaster' ),
			'psupsellmaster_render_product_meta_box_upsells',
			$screen
		);

		// Add a meta box.
		add_meta_box(
			'psupsellmaster_edd_product_meta_box_campaigns',
			__( 'UpsellMaster - Campaigns', 'psupsellmaster' ),
			'psupsellmaster_render_product_meta_box_campaigns',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'psupsellmaster_edd_add_meta_boxes' );
