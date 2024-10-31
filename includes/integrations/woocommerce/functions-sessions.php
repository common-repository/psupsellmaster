<?php
/**
 * Integrations - WooCommerce - Functions - Sessions.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a session value by key in WooCommerce.
 *
 * @param string $key The key.
 * @return mixed The value.
 */
function psupsellmaster_woo_session_get( $key ) {
	// Set the value.
	$value = null;

	// Get the data.
	$data = WC()->session->get( 'psupsellmaster' );
	$data = is_array( $data ) ? $data : array();

	// Check if the key exists.
	if ( array_key_exists( $key, $data ) ) {
		// Set the value.
		$value = $data[ $key ];
	}

	// Return the value.
	return $value;
}

/**
 * Set a session value by key in WooCommerce.
 *
 * @param string $key The key.
 * @param mixed  $value The value.
 */
function psupsellmaster_woo_session_set( $key, $value ) {
	// Set the slug.
	$slug = 'psupsellmaster';

	// Get the data.
	$data = WC()->session->get( $slug );

	// Set the value.
	$data[ $key ] = $value;

	// Set the data.
	WC()->session->set( $slug, $data );
}

/**
 * Get the session cart data in WooCommerce.
 *
 * @return array The cart data.
 */
function psupsellmaster_woo_get_session_cart_data() {
	// Get the quantity.
	$quantity = filter_var( array_sum( WC()->cart->get_cart_item_quantities() ), FILTER_VALIDATE_INT );
	$quantity = false !== $quantity ? $quantity : 0;

	// Get the subtotal.
	$subtotal = filter_var( WC()->cart->get_subtotal(), FILTER_VALIDATE_FLOAT );
	$subtotal = false !== $subtotal ? $subtotal : 0;

	// Get the discount.
	$discount = filter_var( WC()->cart->get_discount_total(), FILTER_VALIDATE_FLOAT );
	$discount = false !== $discount ? $discount : 0;

	// Get the tax.
	$tax = filter_var( WC()->cart->get_total_tax(), FILTER_VALIDATE_FLOAT );
	$tax = false !== $tax ? $tax : 0;

	// Get the total.
	$total = filter_var( WC()->cart->get_total( 'edit' ), FILTER_VALIDATE_FLOAT );
	$total = false !== $total ? $total : 0;

	// Set the data.
	$data = array(
		'quantity' => $quantity,
		'subtotal' => $subtotal,
		'discount' => $discount,
		'tax'      => $tax,
		'total'    => $total,
	);

	// Return the data.
	return $data;
}

/**
 * Get the session cart items in WooCommerce.
 *
 * @return array Return the items.
 */
function psupsellmaster_woo_get_session_cart_items() {
	// Set the items.
	$items = array();

	// Check the cart.
	if ( ! empty( WC()->cart ) ) {
		// Get the items.
		$items = WC()->cart->get_cart();
	}

	// Return the items.
	return $items;
}

/**
 * Get the session cart product ids in WooCommerce.
 *
 * @return array Return the product ids.
 */
function psupsellmaster_woo_get_session_cart_product_ids() {
	// Set the product ids.
	$product_ids = array();

	// Get the cart items.
	$cart_items = psupsellmaster_woo_get_session_cart_items();

	// Loop through the cart items.
	foreach ( $cart_items as $cart_item ) {
		// Get the product id.
		$product_id = isset( $cart_item['product_id'] ) ? filter_var( $cart_item['product_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Check if the product id is already in the product ids.
		if ( in_array( $product_id, $product_ids, true ) ) {
			continue;
		}

		// Add the product id to the product ids.
		array_push( $product_ids, $product_id );
	}

	// Remove empty and duplicate entries.
	$product_ids = array_unique( array_filter( $product_ids ) );

	// Return the product ids.
	return $product_ids;
}

/**
 * Get the session cart author ids in WooCommerce.
 *
 * @return array Return the author ids.
 */
function psupsellmaster_woo_get_session_cart_author_ids() {
	// Set the author ids.
	$author_ids = array();

	// Get the cart items.
	$cart_items = psupsellmaster_woo_get_session_cart_items();

	// Loop through the cart items.
	foreach ( $cart_items as $cart_item ) {
		// Get the product id.
		$product_id = isset( $cart_item['product_id'] ) ? filter_var( $cart_item['product_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

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

	// Remove empty and duplicate entries.
	$author_ids = array_unique( array_filter( $author_ids ) );

	// Return the author ids.
	return $author_ids;
}

/**
 * Get the session cart term ids by taxonomy in WooCommerce.
 *
 * @param string $taxonomy The taxonomy.
 * @return array Return the term ids.
 */
function psupsellmaster_woo_get_session_cart_term_ids( $taxonomy ) {
	// Set the term ids.
	$term_ids = array();

	// Get the cart items.
	$cart_items = psupsellmaster_woo_get_session_cart_items();

	// Loop through the cart items.
	foreach ( $cart_items as $cart_item ) {
		// Get the product id.
		$product_id = isset( $cart_item['product_id'] ) ? filter_var( $cart_item['product_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Get the item term ids.
		$item_term_ids = wp_get_post_terms( $product_id, $taxonomy, array( 'fields' => 'ids' ) );

		// Loop through the item term ids.
		foreach ( $item_term_ids as $item_term_id ) {
			// Get the term id.
			$term_id = isset( $item_term_id ) ? filter_var( $item_term_id, FILTER_VALIDATE_INT ) : false;

			// Check if the item term id is empty.
			if ( empty( $item_term_id ) ) {
				continue;
			}

			// Check if the term id is already in the term ids.
			if ( in_array( $term_id, $term_ids, true ) ) {
				continue;
			}

			// Add the item term id to the term ids.
			array_push( $term_ids, $item_term_id );
		}
	}

	// Remove empty and duplicate entries.
	$term_ids = array_unique( array_filter( $term_ids ) );

	// Return the term ids.
	return $term_ids;
}

/**
 * Get the session cart quantity in WooCommerce.
 *
 * @return float Return the cart quantity.
 */
function psupsellmaster_woo_get_session_cart_quantity() {
	// Get the cart quantity.
	$cart_quantity = filter_var( WC()->cart->get_cart_contents_count(), FILTER_VALIDATE_FLOAT );
	$cart_quantity = false !== $cart_quantity ? $cart_quantity : 0;

	// Return the cart quantity.
	return $cart_quantity;
}

/**
 * Get the session cart subtotal in WooCommerce.
 *
 * @return float Return the cart subtotal.
 */
function psupsellmaster_woo_get_session_cart_subtotal() {
	// Get the cart subtotal.
	$cart_subtotal = filter_var( WC()->cart->subtotal, FILTER_VALIDATE_FLOAT );
	$cart_subtotal = false !== $cart_subtotal ? $cart_subtotal : 0;

	// Return the cart subtotal.
	return $cart_subtotal;
}

/**
 * Get the session cart subtotal by filters in WooCommerce.
 *
 * @param array $args The arguments.
 * @return float Return the cart subtotal.
 */
function psupsellmaster_woo_get_session_cart_subtotal_by_filters( $args = array() ) {
	// Set the cart subtotal.
	$cart_subtotal = 0;

	// Set the filters.
	$filters = array();

	// Check the products.
	if ( isset( $args['products'] ) && is_array( $args['products'] ) ) {
		// Set the filters.
		$filters['products'] = $args['products'];
	}

	// Get the cart items.
	$cart_items = psupsellmaster_woo_get_session_cart_items();

	// Loop through the cart items.
	foreach ( $cart_items as $cart_item ) {
		// Get the subtotal.
		$subtotal = isset( $cart_item['line_subtotal'] ) ? filter_var( $cart_item['line_subtotal'], FILTER_VALIDATE_FLOAT ) : false;

		// Check if the subtotal is empty.
		if ( empty( $subtotal ) ) {
			continue;
		}

		// Get the product id.
		$product_id = isset( $cart_item['product_id'] ) ? filter_var( $cart_item['product_id'], FILTER_VALIDATE_INT ) : false;

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Check if the products filter is set.
		if ( isset( $filters['products'] ) ) {
			// Check if the product id is not in the products.
			if ( ! in_array( $product_id, $filters['products'], true ) ) {
				continue;
			}
		}

		// Sum the subtotal.
		$cart_subtotal += $subtotal;
	}

	// Return the cart subtotal.
	return $cart_subtotal;
}
