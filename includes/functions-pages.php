<?php
/**
 * Functions - Pages.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if this is the product page.
 *
 * @return bool Whether or not this is the product page.
 */
function psupsellmaster_is_page_product() {
	// Set the is page.
	$is_page = false;

	// Check if the query is for an existing single post.
	if ( is_single() ) {
		// Get the current post id.
		$current_post_id = get_the_ID();

		// Check if the current post has a valid product post type.
		if ( psupsellmaster_is_valid_product_post_type_by_post_id( $current_post_id ) ) {
			$is_page = true;
		}
	}

	// Return the result.
	return $is_page;
}

/**
 * Check if this is the cart page.
 *
 * @return bool Whether or not this is the cart page.
 */
function psupsellmaster_is_page_cart() {
	// Set the is page.
	$is_page = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the is page.
		$is_page = is_cart() && ! psupsellmaster_woo_is_page_purchase_receipt();

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the is page (there is no cart page for this plugin).
		$is_page = false;
	}

	// Return the result.
	return $is_page;
}

/**
 * Check if this is the checkout page.
 *
 * @return bool Whether or not this is the checkout page.
 */
function psupsellmaster_is_page_checkout() {
	// Set the is page.
	$is_page = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		$is_page = is_checkout() && ! psupsellmaster_woo_is_page_purchase_receipt();

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		$is_page = edd_is_checkout();
	}

	// Return the result.
	return $is_page;
}

/**
 * Check if this is the purchase receipt page.
 *
 * @return bool Whether or not this is the purchase receipt page.
 */
function psupsellmaster_is_page_purchase_receipt() {
	// Set the is page.
	$is_page = false;

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		$is_page = psupsellmaster_woo_is_page_purchase_receipt();

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		$is_page = psupsellmaster_edd_is_page_purchase_receipt();
	}

	// Return the result.
	return $is_page;
}

/**
 * Check if this is the purchase history page.
 *
 * @return bool Whether or not this is the purchase history page.
 */
function psupsellmaster_is_page_purchase_history() {
	// Set the is page.
	$is_page = false;

	// Check if the Easy Digital Downloads plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		$is_page = psupsellmaster_edd_is_page_purchase_history();
	}

	// Return the result.
	return $is_page;
}

/**
 * Check the current page by a key.
 *
 * @param string $page_key The page key.
 * @return bool Whether $page_key is the current page.
 */
function psupsellmaster_is_page( $page_key ) {
	// Set the is page.
	$is_page = false;

	// Check the page key.
	if ( 'checkout' === $page_key ) {
		// Set the is page.
		$is_page = psupsellmaster_is_page_checkout();

		// Check the page key.
	} elseif ( 'cart' === $page_key ) {
		// Set the is page.
		$is_page = psupsellmaster_is_page_cart();

		// Check the page key.
	} elseif ( 'product' === $page_key ) {
		// Set the is page.
		$is_page = psupsellmaster_is_page_product();

		// Check the page key.
	} elseif ( 'receipt' === $page_key ) {
		// Set the is page.
		$is_page = psupsellmaster_is_page_purchase_receipt();

		// Check the page key.
	} elseif ( 'history' === $page_key ) {
		// Set the is page.
		$is_page = psupsellmaster_is_page_purchase_history();
	}

	// Allow developers to filter this.
	$is_page = apply_filters( 'psupsellmaster_is_page', $is_page, $page_key );

	// Return the is page.
	return $is_page;
}
