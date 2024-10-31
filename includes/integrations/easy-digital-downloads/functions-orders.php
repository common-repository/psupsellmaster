<?php
/**
 * Integrations - Easy Digital Downloads - Functions - Orders.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the products from an order in Easy Digital Downloads.
 *
 * @param int $order_id The order id.
 * @return array The product ids.
 */
function psupsellmaster_edd_get_order_product_ids( $order_id ) {
	// Set the product ids.
	$product_ids = array();

	// Check if the order id is not empty.
	if ( ! empty( $order_id ) ) {
		// Set the args.
		$edd_get_order_items_args = array(
			'fields'   => 'product_id',
			'number'   => 0,
			'order_id' => $order_id,
		);

		// Get the order product ids.
		$product_ids = edd_get_order_items( $edd_get_order_items_args );

		// Remove empty and duplicate product ids.
		$product_ids = array_unique( array_filter( $product_ids ) );

		// Reindex the product ids.
		$product_ids = array_values( $product_ids );
	}

	// Return the product ids.
	return $product_ids;
}

/**
 * Get the authors from an order in Easy Digital Downloads.
 *
 * @param int $order_id The order id.
 * @return array The author ids.
 */
function psupsellmaster_edd_get_order_author_ids( $order_id ) {
	// Set the author ids.
	$author_ids = array();

	// Check if the order id is not empty.
	if ( ! empty( $order_id ) ) {
		// Get the product ids.
		$product_ids = psupsellmaster_edd_get_order_product_ids( $order_id );

		// Loop through the product ids.
		foreach ( $product_ids as $product_id ) {
			// Get the author id.
			$author_id = get_post_field( 'post_author', $product_id );
			$author_id = isset( $author_id ) ? filter_var( $author_id, FILTER_VALIDATE_INT ) : false;

			// Check if the author id is empty.
			if ( empty( $author_id ) ) {
				continue;
			}

			// Check if the author id is already in the author ids.
			if ( in_array( $author_id, $author_ids, true ) ) {
				continue;
			}

			// Add the author id to the author ids.
			array_push( $author_ids, $author_id );
		}
	}

	// Remove empty and duplicate entries.
	$author_ids = array_unique( array_filter( $author_ids ) );

	// Return the author ids.
	return $author_ids;
}

/**
 * Get the terms by taxonomy from an order in Easy Digital Downloads.
 *
 * @param int    $order_id The order id.
 * @param string $taxonomy The taxonomy.
 * @return array The term ids.
 */
function psupsellmaster_edd_get_order_term_ids( $order_id, $taxonomy ) {
	// Set the term ids.
	$term_ids = array();

	// Check if the order id is not empty.
	if ( ! empty( $order_id ) ) {
		// Get the product ids.
		$product_ids = psupsellmaster_edd_get_order_product_ids( $order_id );

		// Loop through the product ids.
		foreach ( $product_ids as $product_id ) {
			// Get the product term ids.
			$product_term_ids = wp_get_post_terms( $product_id, $taxonomy, array( 'fields' => 'ids' ) );

			// Loop through the product term ids.
			foreach ( $product_term_ids as $product_term_id ) {
				// Get the term id.
				$term_id = isset( $product_term_id ) ? filter_var( $product_term_id, FILTER_VALIDATE_INT ) : false;

				// Check if the product term id is empty.
				if ( empty( $product_term_id ) ) {
					continue;
				}

				// Check if the term id is already in the term ids.
				if ( in_array( $term_id, $term_ids, true ) ) {
					continue;
				}

				// Add the product term id to the term ids.
				array_push( $term_ids, $product_term_id );
			}
		}
	}

	// Remove empty and duplicate entries.
	$term_ids = array_unique( array_filter( $term_ids ) );

	// Return the term ids.
	return $term_ids;
}
