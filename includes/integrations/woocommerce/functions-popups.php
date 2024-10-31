<?php
/**
 * Integrations - WooCommerce - Functions - Popups.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Process the add to cart action for the popup in WooCommerce.
 *
 * @param string $cart_item_key The cart item key.
 * @param int    $product_id The product ID.
 * @param float  $quantity The quantity.
 * @param int    $variation_id The variation ID.
 */
function psupsellmaster_popup_woocommerce_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id ) {
	// Get the is enabled.
	$is_enabled = psupsellmaster_feature_is_active( 'popup_add_to_cart' );

	// Check if the is enabled is false.
	if ( ! $is_enabled ) {
		return false;
	}

	// Get the popup meta flag.
	$popup_meta_flag = psupsellmaster_db_current_visitor_meta_select( 'popup_add_to_cart_done', true );
	$popup_meta_flag = filter_var( $popup_meta_flag, FILTER_VALIDATE_BOOLEAN );

	// Check if there is no flag.
	if ( false === $popup_meta_flag ) {
		// Set the variations.
		$variations = ! empty( $variation_id ) ? array( $variation_id ) : array();

		// Set the product.
		$product = array(
			'id'         => $product_id,
			'variations' => $variations,
		);

		// Get the popup meta.
		$popup_meta = psupsellmaster_db_current_visitor_meta_select( 'popup_add_to_cart', true );

		// Get the stored products.
		$stored_products = isset( $popup_meta['products'] ) ? $popup_meta['products'] : array();
		$stored_products = is_array( $stored_products ) ? $stored_products : array();

		// Set the stored found.
		$stored_found = false;

		// Loop through the stored products.
		foreach ( $stored_products as $stored_key => $stored_product ) {
			// Check if the id is empty.
			if ( empty( $stored_product['id'] ) ) {
				continue;
			}

			// Check if the id does not match.
			if ( $product_id !== $stored_product['id'] ) {
				continue;
			}

			// Set the variations.
			$stored_variations = array();

			// Check the variations.
			if ( ! empty( $stored_product['variations'] ) && is_array( $stored_product['variations'] ) ) {
				// Set the variations.
				$stored_variations = $stored_product['variations'];
			}

			// Set the stored data.
			$stored_products[ $stored_key ]['variations'] = array_merge( $stored_variations, $variations );

			// Set the stored found.
			$stored_found = true;

			// Stop the loop.
			break;
		}

		// Check the stored found.
		if ( false === $stored_found ) {
			// Add the product to the products list.
			array_push( $stored_products, $product );
		}

		// Set the popup meta.
		psupsellmaster_db_current_visitor_meta_update( 'popup_add_to_cart', array( 'products' => $stored_products ) );

		// Otherwise, should not insert but delete instead - because the popup was already shown.
	} else {
		// Delete the popup meta.
		psupsellmaster_db_current_visitor_meta_delete( 'popup_add_to_cart' );
	}

	// Delete the popup meta flag.
	psupsellmaster_db_current_visitor_meta_delete( 'popup_add_to_cart_done' );

	// Remove the shutdown action.
	remove_action( 'shutdown', 'psupsellmaster_popup_shutdown' );
}
add_action( 'woocommerce_add_to_cart', 'psupsellmaster_popup_woocommerce_add_to_cart', 30, 4 );
