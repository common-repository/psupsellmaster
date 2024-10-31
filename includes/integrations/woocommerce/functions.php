<?php
/**
 * Integrations - WooCommerce - Functions.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Require/include the files.
 */
function psupsellmaster_woo_includes() {
	// Require the files.
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/functions-pages.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/functions-sessions.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/functions-campaigns.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/functions-orders.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/functions-receipts.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/functions-tracking.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/functions-popups.php';

	// Check if we are in the admin.
	if ( is_admin() ) {
		// Require the files.
		require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/admin/functions-campaigns.php';
		require_once PSUPSELLMASTER_DIR . 'includes/integrations/woocommerce/admin/functions-edit-product.php';
	}
}
add_action( 'psupsellmaster_includes_after', 'psupsellmaster_woo_includes' );

/**
 * Filter the price to return it without markup in WooCommerce.
 *
 * @param string $html The price html.
 * @param float  $price The price.
 * @return float The price.
 */
function psupsellmaster_woo_filter_wc_price_no_markup( $html, $price ) {
	return $price;
}

/**
 * Get the price without markup in WooCommerce.
 *
 * @param float $price The price.
 * @param array $args The arguments.
 * @return float The price.
 */
function psupsellmaster_woo_wc_price_no_markup( $price, $args = array() ) {
	add_filter( 'wc_price', 'psupsellmaster_woo_filter_wc_price_no_markup', 10, 2 );

	$price = wc_price( $price, $args );

	remove_filter( 'wc_price', 'psupsellmaster_woo_filter_wc_price_no_markup' );

	return $price;
}

/**
 * Format a decimal amount for WooCommerce.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_woo_format_decimal_amount( $amount ) {
	// Get the thousand separator.
	$thousand_separator = get_option( 'woocommerce_price_thousand_sep' );

	// Get the decimal separator.
	$decimal_separator = get_option( 'woocommerce_price_decimal_sep' );

	// Set the amount.
	$amount = number_format( wc_clean( $amount ), wc_get_price_decimals(), $decimal_separator, $thousand_separator );

	// Return the amount.
	return $amount;
}

/**
 * Render the products on the checkout page in WooCommerce.
 */
function psupsellmaster_woo_render_products_on_checkout_page() {
	// Get the is enabled.
	$is_enabled = psupsellmaster_feature_is_active( 'page_checkout' );

	// Check if the is enabled is empty.
	if ( empty( $is_enabled ) ) {
		return false;
	}

	// Remove the filters.
	remove_filter( 'woocommerce_after_cart', 'psupsellmaster_woo_filter_woocommerce_after_cart_from_page_cart' );
	remove_filter( 'woocommerce_after_checkout_form', 'psupsellmaster_woo_render_products_after_checkout_form' );

	// Render the products.
	psupsellmaster_render_products_by_location( 'checkout' );
}
add_action( 'woocommerce_after_cart', 'psupsellmaster_woo_render_products_on_checkout_page' );
add_action( 'woocommerce_after_checkout_form', 'psupsellmaster_woo_render_products_on_checkout_page' );

/**
 * Render the products on the product page in WooCommerce.
 */
function psupsellmaster_woo_render_products_on_product_page() {
	// Get the is enabled.
	$is_enabled = psupsellmaster_feature_is_active( 'page_product' );

	// Check if the is enabled is empty.
	if ( empty( $is_enabled ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_location( 'product' );
}

/**
 * Toggle hooks to render products on the product page in WooCommerce.
 */
function psupsellmaster_woo_init_render_products() {
	// Get the is enabled.
	$is_enabled = psupsellmaster_feature_is_active( 'page_product' );

	// Check if the is enabled is empty.
	if ( empty( $is_enabled ) ) {
		return false;
	}

	// Remove the WooCommerce action.
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

	// Add the action.
	add_action( 'woocommerce_after_single_product_summary', 'psupsellmaster_woo_render_products_on_product_page', 20 );
}
add_action( 'init', 'psupsellmaster_woo_init_render_products' );

/**
 * Get products by order id in WooCommerce.
 *
 * @deprecated 1.7.46 Use psupsellmaster_woo_get_order_product_ids().
 * @param int $order_id The order id.
 * @return array The products id list.
 */
function psupsellmaster_woo_get_products_by_order_id( $order_id ) {
	return psupsellmaster_woo_get_order_product_ids( $order_id );
}

/**
 * Get the product prices in WooCommerce.
 *
 * @param int $product_id The product id.
 * @return array The product prices.
 */
function psupsellmaster_woo_get_product_prices( $product_id ) {
	// Set product prices.
	$product_prices = array();

	// Get the product.
	$product = wc_get_product( $product_id );

	// Check if the product is empty.
	if ( empty( $product ) ) {
		// Return the product prices.
		return $product_prices;
	}

	// Check the product type.
	if ( $product->is_type( 'simple' ) ) {
		// Set the price amount.
		$price_amount = filter_var( $product->get_price(), FILTER_VALIDATE_FLOAT );
		$price_amount = false !== $price_amount ? $price_amount : 0;

		// Set the product prices.
		$product_prices[0] = array( 'amount' => $price_amount );

		// Otherwise...
	} else {
		// Get the products.
		$products = $product->get_children();

		// Loop through the products.
		foreach ( $products as $product_id ) {
			// Get the product.
			$product = wc_get_product( $product_id );

			// Check if the product is empty.
			if ( empty( $product ) ) {
				// Continue the loop.
				continue;
			}

			// Set the price amount.
			$price_amount = filter_var( $product->get_price(), FILTER_VALIDATE_FLOAT );
			$price_amount = false !== $price_amount ? $price_amount : 0;

			// Set the product prices.
			$product_prices[ $product_id ] = array( 'amount' => $price_amount );
		}
	}

	// Return the product prices.
	return $product_prices;
}

/**
 * Get the user id from an order in WooCommerce.
 *
 * @param int $order_id The order id.
 * @return int|false The user id or false on failure.
 */
function psupsellmaster_woo_get_user_id_by_order_id( $order_id ) {
	// Set the user id.
	$user_id = false;

	// Check if the order id is empty.
	if ( empty( $order_id ) ) {
		// Return the user id.
		return $user_id;
	}

	// Get the order.
	$order = wc_get_order( $order_id );

	// Check if the order is empty.
	if ( empty( $order ) ) {
		// Return the user id.
		return $user_id;
	}

	// Set the user id.
	$user_id = intval( $order->get_user_id() );

	// Check if the user id is empty.
	if ( empty( $user_id ) ) {
		// Set the user id.
		$user_id = false;
	}

	// Return the user id.
	return $user_id;
}

/**
 * Get the admin customer url by user id for the WooCommerce plugin.
 *
 * @param int $user_id The user id.
 * @return string The customer url.
 */
function psupsellmaster_woo_get_admin_customer_url_by_user_id( $user_id ) {
	// Set the customer url.
	$customer_url = false;

	// Check if the user id is not empty.
	if ( ! empty( $user_id ) ) {
		// Set the customer url.
		$customer_url = admin_url( 'user-edit.php?user_id=' . $user_id );
	}

	// Return the customer url.
	return $customer_url;
}

/**
 * Get the admin customer name by order id for the WooCommerce plugin.
 *
 * @param int $order_id The order id.
 * @return string The customer name.
 */
function psupsellmaster_woo_get_customer_name_by_order_id( $order_id ) {
	// Set the customer name.
	$customer_name = false;

	// Check if the order id is empty.
	if ( empty( $order_id ) ) {
		// Return the customer name.
		return $customer_name;
	}

	// Get the order.
	$order = wc_get_order( $order_id );

	// Check if the order is empty.
	if ( empty( $order ) ) {
		// Return the customer name.
		return $customer_name;
	}

	// Get the customer name.
	$customer_name = $order->get_formatted_billing_full_name();

	// Return the customer name.
	return $customer_name;
}

/**
 * Get the admin order url by order id for the WooCommerce plugin.
 *
 * @param int $order_id The order id.
 * @return string The order url.
 */
function psupsellmaster_woo_get_admin_order_url_by_order_id( $order_id ) {
	// Set the order url.
	$order_url = false;

	// Check if the order id is not empty.
	if ( ! empty( $order_id ) ) {
		// Set the order url.
		$order_url = admin_url( 'post.php?post=' . $order_id . '&action=edit' );
	}

	// Return the order url.
	return $order_url;
}

/**
 * Get the products from the store cart in WooCommerce.
 *
 * @deprecated 1.7.46 Use psupsellmaster_woo_get_session_cart_product_ids().
 * @return array The products.
 */
function psupsellmaster_woo_get_products_from_store_cart() {
	return psupsellmaster_woo_get_session_cart_product_ids();
}

/**
 * Get the WOO product price range.
 *
 * @param int $product_id the product id.
 * @return array the price range.
 */
function psupsellmaster_woo_get_price_range( $product_id ) {
	// Set the range.
	$range = array(
		'min' => 0,
		'max' => 0,
	);

	// Get the product.
	$product = wc_get_product( $product_id );

	// Check if the product is empty.
	if ( empty( $product ) ) {
		// Return the range.
		return $range;
	}

	// Check the product type.
	if ( $product->is_type( 'simple' ) ) {
		// Set the amount.
		$amount = filter_var( $product->get_price(), FILTER_VALIDATE_FLOAT );

		// Check if the amount is not false and is greater than zero.
		if ( false !== $amount && $amount > 0 ) {
			// Set the min.
			$range['min'] = $amount;

			// Set the max.
			$range['max'] = $amount;
		}

		// Otherwise...
	} else {
		// Get the products.
		$products = $product->get_children();

		// Loop through the products.
		foreach ( $products as $product_id ) {
			// Get the product.
			$product = wc_get_product( $product_id );

			// Check if the product is empty.
			if ( empty( $product ) ) {
				// Continue the loop.
				continue;
			}

			// Set the amount.
			$amount = filter_var( $product->get_price(), FILTER_VALIDATE_FLOAT );

			// Check if the amount is not false and is greater than zero.
			if ( false !== $amount && $amount > 0 ) {
				// Check if the amount is greater than the max.
				if ( $amount > $range['max'] ) {
					// Set the max.
					$range['max'] = $amount;
				}

				// Check if the amount is less than the min.
				if ( $amount < $range['min'] ) {
					// Set the min.
					$range['min'] = $amount;
				}
			}
		}
	}

	// Return the range.
	return $range;
}

/**
 * Format an integer amount for WooCommerce.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_woo_format_integer_amount( $amount ) {
	// Get the thousand separator.
	$thousand_separator = get_option( 'woocommerce_price_thousand_sep' );

	// Get the decimal separator.
	$decimal_separator = get_option( 'woocommerce_price_decimal_sep' );

	// Set the amount.
	$amount = number_format( wc_clean( $amount ), 0, $decimal_separator, $thousand_separator );

	// Return the amount.
	return $amount;
}

/**
 * Format a currency amount for WooCommerce.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_woo_format_currency_amount( $amount ) {
	// Set the amount.
	$amount = psupsellmaster_woo_wc_price_no_markup( wc_clean( $amount ) );
	$amount = psupsellmaster_get_currency_symbol() . $amount;

	// Return the amount.
	return $amount;
}

/**
 * Render the campaign condition notices in WooCommerce.
 */
function psupsellmaster_woo_render_campaign_condition_notices() {
	// Render the notices.
	psupsellmaster_render_campaign_condition_notices();
}
add_action( 'woocommerce_after_cart_table', 'psupsellmaster_woo_render_campaign_condition_notices' );
