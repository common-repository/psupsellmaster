<?php
/**
 * Integrations - WooCommerce - Functions - Receipts.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the order id from the purchase receipt page in WooCommerce.
 *
 * @return int|false The order id or false on failure.
 */
function psupsellmaster_woo_get_receipt_order_id() {
	global $wp;

	// Set the order id.
	$order_id = false;

	// Check the current page.
	if ( psupsellmaster_woo_is_page_purchase_receipt() ) {
		// Check the type of the page.
		if ( is_wc_endpoint_url( 'view-order' ) ) {
			// Get the order id.
			$order_id = intval( $wp->query_vars['view-order'] );
		} elseif ( is_wc_endpoint_url( 'order-received' ) ) {
			// Get the order id.
			$order_id = intval( $wp->query_vars['order-received'] );
		}
	}

	// Return the order id.
	return $order_id;
}

/**
 * Get the products from the purchase receipt page in WooCommerce.
 *
 * @return array The product ids.
 */
function psupsellmaster_woo_get_receipt_product_ids() {
	// Set the product ids.
	$product_ids = array();

	// Get the order id.
	$order_id = psupsellmaster_woo_get_receipt_order_id();

	// Check if the order id is not empty.
	if ( ! empty( $order_id ) ) {
		// Get the product ids.
		$product_ids = psupsellmaster_woo_get_order_product_ids( $order_id );
	}

	// Remove empty and duplicate entries.
	$product_ids = array_unique( array_filter( $product_ids ) );

	// Return the product ids.
	return $product_ids;
}

/**
 * Get the authors from the purchase receipt page in WooCommerce.
 *
 * @return array The author ids.
 */
function psupsellmaster_woo_get_receipt_author_ids() {
	// Set the author ids.
	$author_ids = array();

	// Get the order id.
	$order_id = psupsellmaster_woo_get_receipt_order_id();

	// Check if the order id is not empty.
	if ( ! empty( $order_id ) ) {
		// Get the author ids.
		$author_ids = psupsellmaster_woo_get_order_author_ids( $order_id );
	}

	// Return the author ids.
	return $author_ids;
}

/**
 * Get the terms by taxonomy from the purchase receipt page in WooCommerce.
 *
 * @param string $taxonomy The taxonomy.
 * @return array The term ids.
 */
function psupsellmaster_woo_get_receipt_term_ids( $taxonomy ) {
	// Set the term ids.
	$term_ids = array();

	// Get the order id.
	$order_id = psupsellmaster_woo_get_receipt_order_id();

	// Check if the order id is not empty.
	if ( ! empty( $order_id ) ) {
		// Get the term ids.
		$term_ids = psupsellmaster_woo_get_order_term_ids( $order_id, $taxonomy );
	}

	// Return the term ids.
	return $term_ids;
}
