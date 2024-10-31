<?php
/**
 * Functions - Products.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the current product id from the product page.
 *
 * @return int|false The product id.
 */
function psupsellmaster_get_current_product_id() {
	// Set the product id.
	$product_id = false;

	// Get the post type.
	$post_type = get_post_type();

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Check if the post type does not match.
	if ( $post_type !== $product_post_type ) {
		// Return the product id.
		return $product_id;
	}

	// Get the product id.
	$product_id = get_the_ID();

	// Return the product id.
	return $product_id;
}

/**
 * Get the current product author id from the product page.
 *
 * @return int|false The author id.
 */
function psupsellmaster_get_current_product_author_id() {
	// Set the author id.
	$author_id = false;

	// Get the product id.
	$product_id = psupsellmaster_get_current_product_id();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		// Return the author id.
		return $author_id;
	}

	// Get the author id.
	$author_id = get_post_field( 'post_author', $product_id );
	$author_id = isset( $author_id ) ? filter_var( $author_id, FILTER_VALIDATE_INT ) : false;

	// Return the author id.
	return $author_id;
}

/**
 * Get the current product terms by taxonomy from the product page.
 *
 * @param string $taxonomy The taxonomy.
 * @return array The term ids.
 */
function psupsellmaster_get_current_product_term_ids( $taxonomy ) {
	// Set the term ids.
	$term_ids = array();

	// Get the product id.
	$product_id = psupsellmaster_get_current_product_id();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		// Return the term ids.
		return $term_ids;
	}

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

	// Return the term ids.
	return $term_ids;
}
