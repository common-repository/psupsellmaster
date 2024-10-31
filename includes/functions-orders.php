<?php
/**
 * Functions - Orders.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the products from an order.
 *
 * @param int $order_id The order id.
 * @return array The product ids.
 */
function psupsellmaster_get_order_product_ids( $order_id ) {
	// Set the product ids.
	$product_ids = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the product ids.
		$product_ids = psupsellmaster_woo_get_order_product_ids( $order_id );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the product ids.
		$product_ids = psupsellmaster_edd_get_order_product_ids( $order_id );
	}

	// Return the product ids.
	return $product_ids;
}

/**
 * Get the authors from an order.
 *
 * @param int $order_id The order id.
 * @return array The author ids.
 */
function psupsellmaster_get_order_author_ids( $order_id ) {
	// Set the author ids.
	$author_ids = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the author ids.
		$author_ids = psupsellmaster_woo_get_order_author_ids( $order_id );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the author ids.
		$author_ids = psupsellmaster_edd_get_order_author_ids( $order_id );
	}

	// Return the author ids.
	return $author_ids;
}

/**
 * Get the terms by taxonomy from an order.
 *
 * @param int    $order_id The order id.
 * @param string $taxonomy The taxonomy.
 * @return array The term ids.
 */
function psupsellmaster_get_order_term_ids( $order_id, $taxonomy ) {
	// Set the term ids.
	$term_ids = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the term ids.
		$term_ids = psupsellmaster_woo_get_order_term_ids( $order_id, $taxonomy );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the term ids.
		$term_ids = psupsellmaster_edd_get_order_term_ids( $order_id, $taxonomy );
	}

	// Return the term ids.
	return $term_ids;
}
