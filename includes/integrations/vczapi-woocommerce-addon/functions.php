<?php
/**
 * Integrations - Zoom Integration for WooCommerce - Functions.
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
function psupsellmaster_vczapi_includes() {
	// Check if we are in the admin.
	if ( is_admin() ) {
		// Require the files.
		require_once PSUPSELLMASTER_DIR . 'includes/integrations/vczapi-woocommerce-addon/admin/functions-products.php';
	}
}
add_action( 'psupsellmaster_includes_after', 'psupsellmaster_vczapi_includes' );


/**
 * Check whether the product is a zoom product.
 *
 * @param int $product_id The product ID.
 * @return boolean Whether the product is a zoom product.
 */
function psupsellmaster_vczapi_is_zoom_product_type( $product_id ) {
	// Get the is type.
	$is_type = \Codemanas\ZoomWooCommerceAddon\DataStore::get_zoom_product_type( $product_id );

	// Return the is type.
	return $is_type;
}
