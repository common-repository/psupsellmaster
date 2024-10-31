<?php
/**
 * Integrations - Zoom Integration for WooCommerce - Functions - Products.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render additional data for columns.
 *
 * @param string $data The additional data.
 * @param string $column_key The column key.
 * @param int $product_id The product ID.
 * @return string The additional data.
 */
function psupsellmaster_vczapi_admin_products_column_after( $data, $column_key, $product_id ) {
	// Check the column.
	if ( 'product_title' !== $column_key ) {
		// Return the data.
		return $data;
	}

	// Check if the product is not a zoom product.
	if ( ! psupsellmaster_vczapi_is_zoom_product_type( $product_id ) ) {
		// Return the data.
		return $data;
	}

	// Set the data.
	$data .= '&nbsp;<strong>' . esc_html( 'Zoom Product', 'psupsellmaster' ) . '</strong>';

	// Return the data.
	return $data;
}
add_filter( 'psupsellmaster_admin_products_column_after', 'psupsellmaster_vczapi_admin_products_column_after', 10, 3 );
