<?php
/**
 * Integrations - Easy Digital Downloads - Functions - EDD Prices.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This function updates the product price meta keys.
 *
 * @param int $product_id the product id.
 */
function psupsellmaster_edd_update_price_meta_keys( $product_id ) {
	// Set the meta values.
	$meta_values = array();

	// Check if the product has variable prices.
	if ( edd_has_variable_prices( $product_id ) ) {
		// Get the prices.
		$prices = edd_get_variable_prices( $product_id );
		$prices = is_array( $prices ) ? $prices : array();

		// Loop through the prices.
		foreach ( $prices as $price ) {
			// Get the amount.
			$amount = isset( $price['amount'] ) ? filter_var( $price['amount'], FILTER_VALIDATE_FLOAT ) : false;

			// Check if the amount is false.
			if ( false === $amount ) {
				// Continue the loop.
				continue;
			}

			// Add the amount to the meta values list.
			array_push( $meta_values, $amount );
		}

		// Otherwise...
	} else {
		// Set the amount.
		$amount = filter_var( get_post_meta( $product_id, 'edd_price', true ), FILTER_VALIDATE_FLOAT );

		// Check if the amount is not false.
		if ( false !== $amount ) {
			// Add the amount to the meta values list.
			array_push( $meta_values, $amount );
		}
	}

	// Remove duplicate entries.
	$meta_values = array_unique( $meta_values );

	// Check if the meta values is empty.
	if ( empty( $meta_values ) ) {
		// Set the amount (zero, because the product has no prices).
		$amount = 0;

		// Add the amount to the meta values list.
		array_push( $meta_values, $amount );
	}

	// Delete the existing meta key entries.
	delete_post_meta( $product_id, '_psupsellmaster_price' );

	// Loop through the meta values.
	foreach ( $meta_values as $meta_value ) {
		// Add the product price meta key.
		add_post_meta( $product_id, '_psupsellmaster_price', $meta_value );
	}
}

/**
 * This function (maybe) updates the product price meta keys.
 * It depends on the received arguments.
 *
 * @param int    $product_id the product id.
 * @param string $meta_key   the meta key.
 */
function psupsellmaster_edd_maybe_update_price_meta_keys( $product_id, $meta_key ) {
	// Get the post type.
	$post_type = get_post_type( $product_id );

	// Check if the post type is not download.
	if ( 'download' !== $post_type ) {
		return false;
	}

	// Set the keys.
	$keys = array(
		'edd_variable_prices',
		'edd_price',
		'_variable_pricing',
	);

	// Check if meta key is not among monitored keys.
	if ( ! in_array( $meta_key, $keys, true ) ) {
		return false;
	}

	// Update the product price meta keys.
	psupsellmaster_edd_update_price_meta_keys( $product_id );
}

/**
 * This function is triggered when a post is inserted or updated.
 * It will run the tasks only when it refers to a new product.
 *
 * @param int     $post_id the post id.
 * @param WP_Post $post    the post object.
 * @param bool    $update  whether it is an update or not.
 */
function psupsellmaster_edd_wp_insert_post( $post_id, $post, $update ) {
	// Check if it is an update.
	if ( true === $update ) {
		return false;
	}

	// Get the post type.
	$post_type = get_post_type( $post_id );

	// Check if the post type is not download.
	if ( 'download' !== $post_type ) {
		return false;
	}

	// Update the product price meta keys.
	psupsellmaster_edd_update_price_meta_keys( $post_id );
}
add_action( 'wp_insert_post', 'psupsellmaster_edd_wp_insert_post', 10, 3 );

/**
 * This function is triggered when a post meta is added.
 *
 * @param int    $meta_id    the meta id.
 * @param int    $object_id  the object id.
 * @param string $meta_key   the meta key.
 */
function psupsellmaster_edd_added_postmeta( $meta_id, $object_id, $meta_key ) {
	// Maybe update the product price meta keys.
	psupsellmaster_edd_maybe_update_price_meta_keys( $object_id, $meta_key );
}
add_action( 'added_post_meta', 'psupsellmaster_edd_added_postmeta', 10, 4 );

/**
 * This function is triggered when a post meta is updated.
 *
 * @param int    $meta_id    the meta id.
 * @param int    $object_id  the object id.
 * @param string $meta_key   the meta key.
 */
function psupsellmaster_edd_updated_postmeta( $meta_id, $object_id, $meta_key ) {
	// Maybe update the product price meta keys.
	psupsellmaster_edd_maybe_update_price_meta_keys( $object_id, $meta_key );
}
add_action( 'updated_postmeta', 'psupsellmaster_edd_updated_postmeta', 10, 4 );

/**
 * This function is triggered when a post meta is deleted.
 *
 * @param int    $meta_ids   the meta ids.
 * @param int    $object_id  the object id.
 * @param string $meta_key   the meta key.
 */
function psupsellmaster_edd_deleted_post_meta( $meta_ids, $object_id, $meta_key ) {
	// Maybe update the product price meta keys.
	psupsellmaster_edd_maybe_update_price_meta_keys( $object_id, $meta_key );
}
add_action( 'deleted_post_meta', 'psupsellmaster_edd_deleted_post_meta', 10, 4 );
