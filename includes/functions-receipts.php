<?php
/**
 * Functions - Receipts.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the order id from the purchase receipt page.
 *
 * @return int|false The order id or false on failure.
 */
function psupsellmaster_get_receipt_order_id() {
	// Set the order id.
	$order_id = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		$order_id = psupsellmaster_woo_get_receipt_order_id();

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		$order_id = psupsellmaster_edd_get_receipt_order_id();
	}

	// Return the order id.
	return $order_id;
}

/**
 * Get the products from the purchase receipt page.
 *
 * @return array The product ids.
 */
function psupsellmaster_get_receipt_product_ids() {
	// Set the product ids.
	$product_ids = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the product ids.
		$product_ids = psupsellmaster_woo_get_receipt_product_ids();

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the product ids.
		$product_ids = psupsellmaster_edd_get_receipt_product_ids();
	}

	// Return the product ids.
	return $product_ids;
}

/**
 * Get the authors from the purchase receipt page.
 *
 * @return array The author ids.
 */
function psupsellmaster_get_receipt_author_ids() {
	// Set the author ids.
	$author_ids = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the author ids.
		$author_ids = psupsellmaster_woo_get_receipt_author_ids();

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the author ids.
		$author_ids = psupsellmaster_edd_get_receipt_author_ids();
	}

	// Return the author ids.
	return $author_ids;
}

/**
 * Get the terms by taxonomy from the purchase receipt page.
 *
 * @param string $taxonomy The taxonomy.
 * @return array The term ids.
 */
function psupsellmaster_get_receipt_term_ids( $taxonomy ) {
	// Set the term ids.
	$term_ids = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the term ids.
		$term_ids = psupsellmaster_woo_get_receipt_term_ids( $taxonomy );

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the term ids.
		$term_ids = psupsellmaster_edd_get_receipt_term_ids( $taxonomy );
	}

	// Return the term ids.
	return $term_ids;
}
