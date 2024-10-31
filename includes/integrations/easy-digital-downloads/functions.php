<?php
/**
 * Integrations - Easy Digital Downloads - Functions.
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
function psupsellmaster_edd_includes() {
	// Require the files.
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/background/functions-edd-prices.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/functions-pages.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/functions-sessions.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/functions-campaigns.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/functions-orders.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/functions-receipts.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/functions-tracking.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/functions-popups.php';
	require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/functions-edd-prices.php';

	// Check if we are in the admin.
	if ( is_admin() ) {
		// Require the files.
		require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/admin/functions-campaigns.php';
		require_once PSUPSELLMASTER_DIR . 'includes/integrations/easy-digital-downloads/admin/functions-edit-product.php';
	}
}
add_action( 'psupsellmaster_includes_after', 'psupsellmaster_edd_includes' );

/**
 * Filter the single price mode in Easy Digital Downloads.
 *
 * @param bool $mode The mode.
 * @param int  $download_id The download ID.
 * @return bool The mode.
 */
function psupsellmaster_edd_single_price_option_mode( $mode, $download_id ) {

	if ( false !== array_key_exists( 'psupsellmaster_edd_price_mode_' . $download_id, $GLOBALS ) ) {
		$mode = $GLOBALS[ 'psupsellmaster_edd_price_mode_' . $download_id ];
	}

	return $mode;
}
add_filter( 'edd_single_price_option_mode', 'psupsellmaster_edd_single_price_option_mode', 10, 2 );

/**
 * Filter the purchase link args in Easy Digital Downloads.
 *
 * @param array $args The arguments.
 * @return array The args.
 */
function psupsellmaster_edd_purchase_link_args( $args ) {
	// Check the source.
	if ( isset( $args['source'] ) && 'psupsellmaster' === $args['source'] ) {
		// Get the text.
		$text = $args['text'];

		// Get the position.
		$position = strrpos( $text, ';' );

		if ( false !== $position ) {
			++$position;

			$args['text'] = trim( substr( $text, $position, strlen( $text ) - $position ) );
		}
	}

	// Return the args.
	return $args;
}
add_filter( 'edd_purchase_link_args', 'psupsellmaster_edd_purchase_link_args' );

/**
 * Get the highest price ID in Easy Digital Downloads.
 *
 * @param int $product_id The product ID.
 * @return bool|int The price id or false on failure.
 */
function psupsellmaster_edd_get_highest_price_id( $product_id ) {

	$max_price = -1;
	$max_idx   = -1;
	$prices    = get_post_meta( $product_id, 'edd_variable_prices', false );

	if ( ! is_array( $prices ) || ( count( $prices ) <= 0 ) ) {

		return false;

	}

	foreach ( $prices[0] as $data ) {

		if ( (float) $data['amount'] > $max_price ) {
			$max_price = (float) $data['amount'];
			$max_idx   = $data['index'];
		}
	}

	$response = ( $max_idx <= 0 ) ? false : $max_idx;

	return $response;
}

/**
 * Get the product prices in Easy Digital Downloads.
 *
 * TODO: replace this old function with a new higher-quality function.
 *
 * @param int  $product_id The product id.
 * @param bool $sort_prices Whether or not to sort the prices.
 * @return array The prices.
 */
function psupsellmaster_get_edd_price_range( $product_id, $sort_prices = false ) {
	// edd_price.
	$price     = 0;
	$min_price = 0;
	$max_price = 0;
	$prices    = array();
	$sql       = PsUpsellMaster_Database::prepare(
		"SELECT `meta_key`, `meta_value`, CAST(REPLACE(meta_value, ',', '.') AS DECIMAL(10,2)) "
		. PsUpsellMaster_Database::prepare( 'FROM %i ', PsUpsellMaster_Database::get_table_name( 'postmeta' ) )
		. "WHERE (`meta_key` IN ('edd_price','edd_variable_prices')) AND (`post_id` = %d)",
		array(
			$product_id,
		)
	);

	$query = PsUpsellMaster_Database::get_results( $sql, ARRAY_N );

	foreach ( $query as $row ) {

		if ( 'edd_price' === $row[0] ) {
			$price = (float) $row[2];
		} else {
			$price_array = maybe_unserialize( $row[1] );

			foreach ( $price_array as $data ) {

				if ( is_array( $data ) && array_key_exists( 'amount', $data ) && ( (float) $data['amount'] > 0 ) ) {
					$prices[] = (float) $data['amount'];
				}
			}
		}
	}

	if ( count( $prices ) > 1 ) {
		sort( $prices );
	}

	$qty = count( $prices );

	if ( $qty > 0 ) {
		$min_price = $prices[0];
		$max_price = $prices[ $qty - 1 ];
	} else {
		$min_price = $price;
		$max_price = $price;

		if ( $sort_prices ) {
			$prices = array( $price );
		}
	}

	if ( $sort_prices ) {
		$response = array(
			'min'    => $min_price,
			'max'    => $max_price,
			'prices' => $prices,
		);
	} else {
		$response = array(
			'min' => $min_price,
			'max' => $max_price,
		);
	}

	return $response;
}

/**
 * Render the products on the product page in Easy Digital Downloads (position 1).
 */
function psupsellmaster_edd_render_products_on_product_page_position1() {
	// Check if this is not the download page.
	if ( ! psupsellmaster_is_page( 'product' ) ) {
		return false;
	}

	// Get the is enabled.
	$is_enabled = psupsellmaster_feature_is_active( 'page_product' );

	// Check if the is enabled is empty.
	if ( empty( $is_enabled ) ) {
		return false;
	}

	// Get the position.
	$position = filter_var( PsUpsellMaster_Settings::get( 'product_page_edd_position' ), FILTER_VALIDATE_INT );

	// Check if the position is not 1.
	if ( 1 !== $position ) {
		return false;
	}

	// Remove the action.
	remove_action( 'edd_after_download_content', 'psupsellmaster_edd_render_products_on_product_page_position1' );

	// Render the products.
	psupsellmaster_render_products_by_location( 'product' );
}
add_action( 'edd_after_download_content', 'psupsellmaster_edd_render_products_on_product_page_position1' );

/**
 * Render the products on the product page in Easy Digital Downloads (position 2).
 *
 * @param string $content The content.
 * @return string The content.
 */
function psupsellmaster_edd_render_products_on_product_page_position2( $content ) {
	// Check if the Easy Digital Downloads plugin is not enabled.
	if ( ! psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Return the content.
		return $content;
	}

	// Check if this is not the download page.
	if ( ! psupsellmaster_is_page( 'product' ) ) {
		// Return the content.
		return $content;
	}

	// Get the is enabled.
	$is_enabled = psupsellmaster_feature_is_active( 'page_product' );

	// Check if the is enabled is empty.
	if ( empty( $is_enabled ) ) {
		// Return the content.
		return $content;
	}

	// Get the position.
	$position = filter_var( PsUpsellMaster_Settings::get( 'product_page_edd_position' ), FILTER_VALIDATE_INT );

	// Check if the position is not 2.
	if ( 2 !== $position ) {
		// Return the content.
		return $content;
	}

	// Remove the filter.
	remove_filter( 'the_content', 'psupsellmaster_edd_render_products_on_product_page_position2' );

	// Start the buffer.
	ob_start();

	// Render the products.
	psupsellmaster_render_products_by_location( 'product' );

	// Set the content.
	$content .= ob_get_clean();

	// Return the content.
	return $content;
}
add_filter( 'the_content', 'psupsellmaster_edd_render_products_on_product_page_position2', 20, 1 );

/**
 * Render the products on the checkout page in Easy Digital Downloads.
 */
function psupsellmaster_edd_render_products_on_checkout_page() {
	// Get the is enabled.
	$is_enabled = psupsellmaster_feature_is_active( 'page_checkout' );

	// Check if the is enabled is empty.
	if ( empty( $is_enabled ) ) {
		return false;
	}

	// Render the products.
	psupsellmaster_render_products_by_location( 'checkout' );
}
add_action( 'edd_before_purchase_form', 'psupsellmaster_edd_render_products_on_checkout_page', 9 );

/**
 * Render the products on the purchase receipt page in Easy Digital Downloads.
 */
function psupsellmaster_edd_render_products_on_purchase_receipt_page() {
	// Render the products.
	psupsellmaster_render_products_by_location( 'purchase_receipt' );
}
add_action( 'edd_order_receipt_after_table', 'psupsellmaster_edd_render_products_on_purchase_receipt_page', 20 );

/**
 * Get products by order id in Easy Digital Downloads.
 *
 * @deprecated 1.7.46 Use psupsellmaster_woo_get_order_product_ids().
 * @param int $order_id The order id.
 * @return array The products id list.
 */
function psupsellmaster_edd_get_products_by_order_id( $order_id ) {
	return psupsellmaster_edd_get_order_product_ids( $order_id );
}

/**
 * Get the product prices in Easy Digital Downloads.
 *
 * @param int $product_id The product id.
 * @return array The product prices.
 */
function psupsellmaster_edd_get_product_prices( $product_id ) {
	// Set product prices.
	$product_prices = array();

	// Check if the product has variable prices.
	if ( edd_has_variable_prices( $product_id ) ) {
		// Get the prices.
		$prices = edd_get_variable_prices( $product_id );
		$prices = is_array( $prices ) ? $prices : array();

		// Loop through the prices.
		foreach ( $prices as $price_id => $price_data ) {
			// Get the price amount.
			$price_amount = isset( $price_data['amount'] ) ? filter_var( $price_data['amount'], FILTER_VALIDATE_FLOAT ) : false;

			// Set the product prices.
			$product_prices[ $price_id ] = array( 'amount' => $price_amount );
		}

		// Otherwise...
	} else {
		// Set the price amount.
		$price_amount = filter_var( edd_get_download_price( $product_id ), FILTER_VALIDATE_FLOAT );
		$price_amount = false !== $price_amount ? $price_amount : 0;

		// Set the product prices.
		$product_prices[0] = array( 'amount' => $price_amount );
	}

	// Return the product prices.
	return $product_prices;
}

/**
 * Get the order id from the purchase receipt page in Easy Digital Downloads.
 *
 * @deprecated 1.7.46 Use psupsellmaster_edd_get_receipt_order_id().
 * @return int|false The order id or false on failure.
 */
function psupsellmaster_edd_get_order_id_from_page_purchase_receipt() {
	return psupsellmaster_edd_get_receipt_order_id();
}

/**
 * Get the user id from an order in Easy Digital Downloads.
 *
 * @param int $order_id The order id.
 * @return int|false The user id or false on failure.
 */
function psupsellmaster_edd_get_user_id_by_order_id( $order_id ) {
	// Set the user id.
	$user_id = false;

	// Check if the order id is empty.
	if ( empty( $order_id ) ) {
		// Return the user id.
		return $user_id;
	}

	// Get the order.
	$order = edd_get_order( $order_id );

	// Check if the order is empty.
	if ( empty( $order ) ) {
		// Return the user id.
		return $user_id;
	}

	// Set the user id.
	$user_id = intval( $order->user_id );

	// Check if the user id is empty.
	if ( empty( $user_id ) ) {
		// Set the user id.
		$user_id = false;
	}

	// Return the user id.
	return $user_id;
}

/**
 * Get the admin customer url by user id for the Easy Digital Downloads plugin.
 *
 * @param int $user_id The user id.
 * @return string The customer url.
 */
function psupsellmaster_edd_get_admin_customer_url_by_user_id( $user_id ) {
	// Set the customer url.
	$customer_url = false;

	// Check if the user id is not empty.
	if ( ! empty( $user_id ) ) {
		// Set the customer url.
		$customer_url = admin_url( 'edit.php?post_type=download&page=edd-customers&view=overview&id=' . $user_id );
	}

	// Return the customer url.
	return $customer_url;
}

/**
 * Get the admin customer name by order id for the Easy Digital Downloads plugin.
 *
 * @param int $order_id The order id.
 * @return string The customer name.
 */
function psupsellmaster_edd_get_customer_name_by_order_id( $order_id ) {
	// Set the customer name.
	$customer_name = false;

	// Check if the order id is empty.
	if ( empty( $order_id ) ) {
		// Return the customer name.
		return $customer_name;
	}

	// Get the order.
	$order = edd_get_order( $order_id );

	// Check if the order is empty.
	if ( empty( $order ) ) {
		// Return the customer name.
		return $customer_name;
	}

	// Get the customer id.
	$customer_id = filter_var( $order->customer_id, FILTER_VALIDATE_INT );

	// Check if the customer id is empty.
	if ( empty( $customer_id ) ) {
		// Return the customer name.
		return $customer_name;
	}

	// Get the customer name.
	$customer_name = edd_get_customer_field( $customer_id, 'name' );

	// Return the customer name.
	return $customer_name;
}

/**
 * Get the admin order url by order id for the Easy Digital Downloads plugin.
 *
 * @param int $order_id The order id.
 * @return string The order url.
 */
function psupsellmaster_edd_get_admin_order_url_by_order_id( $order_id ) {
	// Set the order url.
	$order_url = false;

	// Check if the order id is not empty.
	if ( ! empty( $order_id ) ) {
		// Set the order url.
		$order_url = admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $order_id );
	}

	// Return the order url.
	return $order_url;
}

/**
 * Get the products from the purchase receipt page in Easy Digital Downloads.
 *
 * @deprecated 1.7.49 Use psupsellmaster_edd_get_receipt_product_ids().
 * @return array The products.
 */
function psupsellmaster_edd_get_products_from_page_purchase_receipt() {
	return psupsellmaster_edd_get_receipt_product_ids();
}

/**
 * Get the products from the store cart in Easy Digital Downloads.
 *
 * @deprecated 1.7.46 Use psupsellmaster_edd_get_session_cart_product_ids().
 * @return array The products.
 */
function psupsellmaster_edd_get_products_from_store_cart() {
	return psupsellmaster_edd_get_session_cart_product_ids();
}

/**
 * Get the EDD product price range.
 *
 * @param int $product_id the product id.
 * @return array the price range.
 */
function psupsellmaster_edd_get_price_range( $product_id ) {
	// Set the range.
	$range = array(
		'min' => false,
		'max' => false,
	);

	// Check if the product has variable prices.
	if ( edd_has_variable_prices( $product_id ) ) {
		// Get the prices.
		$prices = edd_get_variable_prices( $product_id );
		$prices = is_array( $prices ) ? $prices : array();

		// Loop through the prices.
		foreach ( $prices as $price ) {
			// Get the amount.
			$amount = isset( $price['amount'] ) ? filter_var( $price['amount'], FILTER_VALIDATE_FLOAT ) : false;

			// Check if the amount is false or less than zero.
			if ( false === $amount || $amount < 0 ) {
				// Continue the loop.
				continue;
			}

			// Check if the amount is less than the min.
			if ( false === $range['min'] || $amount < $range['min'] ) {
				// Set the min.
				$range['min'] = $amount;
			}

			// Check if the amount is greater than the max.
			if ( false === $range['max'] || $amount > $range['max'] ) {
				// Set the max.
				$range['max'] = $amount;
			}
		}

		// Otherwise...
	} else {
		// Set the amount.
		$amount = filter_var( get_post_meta( $product_id, 'edd_price', true ), FILTER_VALIDATE_FLOAT );

		// Check if the amount is not false and is greater than zero.
		if ( false !== $amount && $amount > 0 ) {
			// Set the min.
			$range['min'] = $amount;

			// Set the max.
			$range['max'] = $amount;
		}
	}

	// Set the range.
	$range = array(
		'min' => false !== $range['min'] ? $range['min'] : 0,
		'max' => false !== $range['max'] ? $range['max'] : 0,
	);

	// Return the range.
	return $range;
}

/**
 * Format an integer amount.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_edd_format_integer_amount( $amount ) {
	// Set the amount.
	$amount = edd_format_amount( edd_sanitize_amount( $amount ), false );

	// Return the amount.
	return $amount;
}

/**
 * Format a decimal amount.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_edd_format_decimal_amount( $amount ) {
	// Set the amount.
	$amount = edd_format_amount( edd_sanitize_amount( $amount ) );

	// Return the amount.
	return $amount;
}

/**
 * Format a currency amount.
 *
 * @param int $amount The amount.
 * @return string The formatted amount.
 */
function psupsellmaster_edd_format_currency_amount( $amount ) {
	// Set the amount.
	$amount = edd_currency_filter( edd_format_amount( edd_sanitize_amount( $amount ) ) );

	// Return the amount.
	return $amount;
}

/**
 * Render the campaign condition notices in Easy Digital Downloads.
 */
function psupsellmaster_edd_render_campaign_condition_notices() {
	// Render the notices.
	psupsellmaster_render_campaign_condition_notices();
}
add_action( 'edd_cart_items_after', 'psupsellmaster_edd_render_campaign_condition_notices' );
