<?php
/**
 * Functions - Sessions.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a session value by key.
 *
 * @param string $key The key.
 * @return mixed The value.
 */
function psupsellmaster_session_get( $key ) {
	// Set the value.
	$value = null;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the value.
		$value = psupsellmaster_woo_session_get( $key );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the value.
		$value = psupsellmaster_edd_session_get( $key );
	}

	// Return the value.
	return $value;
}

/**
 * Set a session value by key.
 *
 * @param string $key The key.
 * @param mixed  $value The value.
 */
function psupsellmaster_session_set( $key, $value ) {
	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the value.
		psupsellmaster_woo_session_set( $key, $value );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the value.
		psupsellmaster_edd_session_set( $key, $value );
	}
}

/**
 * Get the session cart data.
 *
 * @return array The cart data.
 */
function psupsellmaster_get_session_cart_data() {
	// Set the data.
	$data = array(
		'quantity' => 0,
		'subtotal' => 0,
		'discount' => 0,
		'tax'      => 0,
		'total'    => 0,
	);

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the data.
		$data = psupsellmaster_woo_get_session_cart_data();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the data.
		$data = psupsellmaster_edd_get_session_cart_data();
	}

	// Return the data.
	return $data;
}

/**
 * Get the session cart items.
 *
 * @return array The cart items.
 */
function psupsellmaster_get_session_cart_items() {
	// Set the items.
	$items = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the items.
		$items = psupsellmaster_woo_get_session_cart_items();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the items.
		$items = psupsellmaster_edd_get_session_cart_items();
	}

	// Return the items.
	return $items;
}

/**
 * Get the session cart product ids.
 *
 * @return array Return the product ids.
 */
function psupsellmaster_get_session_cart_product_ids() {
	// Set the product ids.
	$product_ids = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the product ids.
		$product_ids = psupsellmaster_woo_get_session_cart_product_ids();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the product ids.
		$product_ids = psupsellmaster_edd_get_session_cart_product_ids();
	}

	// Return the product ids.
	return $product_ids;
}

/**
 * Get the session cart author ids.
 *
 * @return array Return the author ids.
 */
function psupsellmaster_get_session_cart_author_ids() {
	// Set the author ids.
	$author_ids = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the author ids.
		$author_ids = psupsellmaster_woo_get_session_cart_author_ids();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the author ids.
		$author_ids = psupsellmaster_edd_get_session_cart_author_ids();
	}

	// Return the author ids.
	return $author_ids;
}

/**
 * Get the session cart term ids by taxonomy.
 *
 * @param string $taxonomy The taxonomy.
 * @return array Return the term ids.
 */
function psupsellmaster_get_session_cart_term_ids( $taxonomy ) {
	// Set the term ids.
	$term_ids = array();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the term ids.
		$term_ids = psupsellmaster_woo_get_session_cart_term_ids( $taxonomy );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the term ids.
		$term_ids = psupsellmaster_edd_get_session_cart_term_ids( $taxonomy );
	}

	// Return the term ids.
	return $term_ids;
}

/**
 * Get the session cart quantity.
 *
 * @return float Return the cart quantity.
 */
function psupsellmaster_get_session_cart_quantity() {
	// Set the cart quantity.
	$cart_quantity = 0;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the cart quantity.
		$cart_quantity = psupsellmaster_woo_get_session_cart_quantity();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the cart quantity.
		$cart_quantity = psupsellmaster_edd_get_session_cart_quantity();
	}

	// Return the cart quantity.
	return $cart_quantity;
}

/**
 * Get the session cart subtotal.
 *
 * @return float Return the cart subtotal.
 */
function psupsellmaster_get_session_cart_subtotal() {
	// Set the cart subtotal.
	$cart_subtotal = 0;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the cart subtotal.
		$cart_subtotal = psupsellmaster_woo_get_session_cart_subtotal();

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the cart subtotal.
		$cart_subtotal = psupsellmaster_edd_get_session_cart_subtotal();
	}

	// Return the cart subtotal.
	return $cart_subtotal;
}

/**
 * Get the session cart subtotal by filters.
 *
 * @param array $args The arguments.
 * @return float Return the cart subtotal.
 */
function psupsellmaster_get_session_cart_subtotal_by_filters( $args = array() ) {
	// Set the cart subtotal.
	$cart_subtotal = 0;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Get the cart subtotal.
		$cart_subtotal = psupsellmaster_woo_get_session_cart_subtotal_by_filters( $args );

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Get the cart subtotal.
		$cart_subtotal = psupsellmaster_edd_get_session_cart_subtotal_by_filters( $args );
	}

	// Return the cart subtotal.
	return $cart_subtotal;
}
