<?php
/**
 * Integrations - WooCommerce - Functions - Campaigns.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the coupons in WooCommerce.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_woo_get_coupons( $args = array() ) {
	// Get the post type.
	$post_type = psupsellmaster_get_coupon_post_type();

	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'psupsellmaster_group'       => '',
		'order'                      => 'ASC',
		'orderby'                    => 'title',
		'post__in'                   => array(),
		'posts_per_page'             => 20,
		'post_type'                  => $post_type,
		'post_status'                => 'publish',
		'search_title'               => '',
		'suppress_filters'           => false,
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Get the data.
	$data = psupsellmaster_get_posts( $parsed_args );

	// Set the items.
	$items = array();

	// Loop through the items.
	foreach ( $data['items'] as $item_data ) {
		// Get the campaign id.
		$campaign_id = psupsellmaster_get_campaign_id_by_coupon_id( $item_data->ID );

		// Check the group.
		if ( 'standard' === $parsed_args['psupsellmaster_group'] && ! empty( $campaign_id ) ) {
			// Skip this item.
			continue;

			// Check the group.
		} elseif ( 'campaign' === $parsed_args['psupsellmaster_group'] && empty( $campaign_id ) ) {
			// Skip this item.
			continue;
		}

		// Set the item.
		$item = array(
			'id'   => $item_data->ID,
			'code' => $item_data->post_title,
		);

		// Add the item to the list.
		array_push( $items, $item );
	}

	// Set the data.
	$data['items'] = $items;

	// Return the data.
	return $data;
}

/**
 * Get the coupon code in WooCommerce.
 *
 * @param int $coupon_id The coupon id.
 * @return string Return the coupon code.
 */
function psupsellmaster_woo_get_coupon_code( $coupon_id ) {
	// Set the coupon code.
	$coupon_code = '';

	// Get the coupon.
	$coupon = new WC_Coupon( $coupon_id );

	// Check if the coupon exists.
	if ( ! empty( $coupon ) ) {
		// Set the coupon code.
		$coupon_code = $coupon->get_code();
	}

	// Return the coupon code.
	return $coupon_code;
}

/**
 * Check if a WooCommerce coupon code exists.
 *
 * @param string $coupon_code The coupon code.
 * @return bool Return true if the coupon code exists, false otherwise.
 */
function psupsellmaster_woo_coupon_code_exists( $coupon_code ) {
	// Set the exists.
	$exists = false;

	// Get the coupon.
	$coupon = wc_get_coupon_id_by_code( $coupon_code );

	// Check if the coupon exists.
	if ( ! empty( $coupon ) ) {
		// Set the exists.
		$exists = true;
	}

	// Return the exists.
	return $exists;
}

/**
 * Get a coupon ID by code for WooCommerce.
 *
 * @param string $coupon_code The coupon code.
 * @return int|false Return the coupon ID.
 */
function psupsellmaster_woo_get_coupon_id_by_code( $coupon_code ) {
	// Set the coupon id.
	$coupon_id = wc_get_coupon_id_by_code( $coupon_code );

	// Return the coupon id.
	return $coupon_id;
}

/**
 * Get the campaign coupon codes in WooCommerce.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign coupon codes.
 */
function psupsellmaster_woo_get_campaign_coupon_codes( $campaign_id ) {
	// Set the coupons.
	$coupons = array();

	// Get the coupon post type.
	$coupon_post_type = psupsellmaster_get_coupon_post_type();

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`posts`.`post_title`
		FROM
			%i AS `campaign_coupons`
		INNER JOIN
			%i AS `posts`
		ON
			`campaign_coupons`.`coupon_id` = `posts`.`ID`
		WHERE
			`campaign_coupons`.`campaign_id` = %d
		AND
			`posts`.`post_type` = %s
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' ),
		PsUpsellMaster_Database::get_table_name( 'posts' ),
		$campaign_id,
		$coupon_post_type
	);

	// Get the coupons.
	$coupons = PsUpsellMaster_Database::get_col( $sql_query );
	$coupons = is_array( $coupons ) ? $coupons : array();
	$coupons = array_filter( array_unique( $coupons ) );

	// Return the coupons.
	return $coupons;
}

/**
 * Get the coupon statuses in WooCommerce.
 *
 * @return array The coupon statuses.
 */
function psupsellmaster_woo_get_coupon_statuses() {
	// Set the statuses.
	$statuses = array(
		'draft',
		'pending',
		'publish',
	);

	// Return the statuses.
	return $statuses;
}

/**
 * Update the coupon status in WooCommerce.
 *
 * @param int    $coupon_id The coupon id.
 * @param string $status The coupon status.
 */
function psupsellmaster_woo_update_coupon_status( $coupon_id, $status ) {
	// Get the coupon.
	$coupon = new WC_Coupon( $coupon_id );

	// Set the data.
	$data = array( 'status' => 'pending' );

	// Set the woo statuses.
	$woo_statuses = psupsellmaster_woo_get_coupon_statuses();

	// Check if the status is valid.
	if ( in_array( $status, $woo_statuses, true ) ) {
		// Set the status.
		$data['status'] = $status;
	}

	// Set the coupon data.
	$coupon->set_props( $data );

	// Save the coupon.
	$coupon->save();
}

/**
 * Check if a coupon is valid in WooCommerce.
 *
 * @param string $code The coupon code.
 * @return bool Whether the coupon is valid.
 */
function psupsellmaster_woo_is_coupon_valid( $code ) {
	// Set the is valid.
	$is_valid = false;

	// Get the coupon.
	$coupon = new WC_Coupon( $code );

	// Get the discounts.
	$discounts = new WC_Discounts( WC()->cart );

	// Check if the coupon is valid.
	if ( true === $discounts->is_coupon_valid( $coupon ) ) {
		// Set the is valid.
		$is_valid = true;
	}

	// Return the is valid.
	return $is_valid;
}

/**
 * Check the coupon when a coupon is applied in WooCommerce.
 *
 * @param string $coupon_code The coupon code.
 */
function psupsellmaster_woo_update_coupons_on_apply_coupon( $coupon_code ) {
	// Maybe remove coupons from the cart.
	psupsellmaster_maybe_remove_coupons_from_cart_on_apply_coupon( $coupon_code );
}
add_action( 'woocommerce_applied_coupon', 'psupsellmaster_woo_update_coupons_on_apply_coupon' );

/**
 * Check if using multiple coupons is allowed in WooCommerce.
 *
 * @return bool Whether multiple coupons are allowed or not.
 */
function psupsellmaster_woo_is_multiple_coupons_allowed() {
	// Set the allowed.
	$allowed = true;

	// Allow developers to filter this.
	$allowed = apply_filters( 'psupsellmaster_woo_is_multiple_coupons_allowed', $allowed );

	// Return the allowed.
	return $allowed;
}

/**
 * Maybe apply the coupon to the cart in WooCommerce.
 *
 * @param string $coupon The coupon.
 * @return bool Whether the coupon was applied or not.
 */
function psupsellmaster_woo_maybe_apply_coupon_to_cart( $coupon ) {
	// Set the applied.
	$applied = false;

	// Check if the coupon is already applied.
	if ( ! psupsellmaster_woo_cart_has_coupon( $coupon ) ) {
		// Apply the coupon.
		$applied = psupsellmaster_woo_apply_coupon_to_cart( $coupon );
	}

	// Return the applied.
	return $applied;
}

/**
 * Apply the coupon to the cart in WooCommerce.
 *
 * @param string $coupon The coupon.
 * @return bool Whether the coupon was applied or not.
 */
function psupsellmaster_woo_apply_coupon_to_cart( $coupon ) {
	// Set the applied.
	$applied = false;

	// Get the cart.
	$cart = WC()->cart;

	// Add the filter.
	add_filter( 'woocommerce_coupon_message', '__return_false' );

	// Apply the coupon.
	$applied = $cart->add_discount( $coupon );

	// Remove the filter.
	remove_filter( 'woocommerce_coupon_message', '__return_false' );

	// Return the applied.
	return $applied;
}

/**
 * Check if the cart has the coupon in WooCommerce.
 *
 * @param string $coupon The coupon.
 * @return bool Return true if the cart has the coupon. Otherwise, return false.
 */
function psupsellmaster_woo_cart_has_coupon( $coupon ) {
	// Set the has coupon.
	$has_coupon = false;

	// Get the cart.
	$cart = WC()->cart;

	// Check if the coupon is already applied.
	if ( $cart->has_discount( $coupon ) ) {
		// Set the has coupon.
		$has_coupon = true;
	}

	// Return the has coupon.
	return $has_coupon;
}

/**
 * Remove the coupon from the cart in WooCommerce.
 *
 * @param string $coupon The coupon.
 */
function psupsellmaster_woo_remove_coupon_from_cart( $coupon ) {
	// Get the cart.
	$cart = WC()->cart;

	// Remove the coupon.
	$cart->remove_coupon( $coupon );
}

/**
 * Get the applied coupons in WooCommerce.
 *
 * @return array The applied coupons.
 */
function psupsellmaster_woo_get_applied_coupons() {
	// Get the coupons.
	$coupons = WC()->cart->get_applied_coupons();

	// Return the coupons.
	return $coupons;
}

/**
 * Get the applied coupons in WooCommerce.
 *
 * @param string $context The context.
 * @return array The applied coupons.
 */
function psupsellmaster_woo_get_applied_coupons_by_context( $context = 'all' ) {
	// Get the coupons.
	$coupons = psupsellmaster_woo_get_applied_coupons();

	// Check if the coupons is empty.
	if ( empty( $coupons ) ) {
		// Return the coupons.
		return $coupons;
	}

	// Check the context.
	if ( 'all' === $context ) {
		// Return the coupons.
		return $coupons;
	}

	// Check the context.
	if ( 'campaigns' === $context ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $coupons ), '%s' ) );

		// Set the sql coupons.
		$sql_coupons = PsUpsellMaster_Database::prepare( "`posts`.`post_title` IN ( {$placeholders} ) ", $coupons );

		// Get the coupon post type.
		$coupon_post_type = psupsellmaster_get_coupon_post_type();

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare(
			"
			SELECT
				`posts`.`post_title`
			FROM
				%i AS `posts`
			INNER JOIN
				%i AS `campaign_coupons`
			ON
				`campaign_coupons`.`coupon_id` = `posts`.`ID`
			WHERE
				`posts`.`post_type` = %s
			AND
				{$sql_coupons}
			",
			PsUpsellMaster_Database::get_table_name( 'posts' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' ),
			$coupon_post_type
		);

		// Get the coupons.
		$coupons = PsUpsellMaster_Database::get_col( $sql_query );
		$coupons = is_array( $coupons ) ? $coupons : array();
		$coupons = array_filter( array_unique( $coupons ) );

		// Return the coupons.
		return $coupons;
	}

	// Check the context.
	if ( 'standard' === $context ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $coupons ), '%s' ) );

		// Set the sql coupons.
		$sql_coupons = PsUpsellMaster_Database::prepare( "`posts`.`post_title` IN ( {$placeholders} ) ", $coupons );

		// Get the coupon post type.
		$coupon_post_type = psupsellmaster_get_coupon_post_type();

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare(
			"
			SELECT
				`posts`.`post_title`
			FROM
				%i AS `posts`
			WHERE
				`posts`.`post_type` = %s
			AND
				{$sql_coupons}
			AND
				NOT EXISTS (
					SELECT
						1
					FROM
						%i AS `campaign_coupons`
					WHERE
						`campaign_coupons`.`coupon_id` = `posts`.`ID`
				)
			",
			PsUpsellMaster_Database::get_table_name( 'posts' ),
			$coupon_post_type,
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' )
		);

		// Get the coupons.
		$coupons = PsUpsellMaster_Database::get_col( $sql_query );
		$coupons = is_array( $coupons ) ? $coupons : array();
		$coupons = array_filter( array_unique( $coupons ) );

		// Return the coupons.
		return $coupons;
	}

	// Return the coupons.
	return $coupons;
}

/**
 * Fires when an order status is changed to completed in WooCommerce.
 *
 * @param int $order_id The order id.
 */
function psupsellmaster_campaigns_woocommerce_order_status_completed( $order_id ) {
	// Check if the order id is empty.
	if ( empty( $order_id ) ) {
		return false;
	}

	// Set the data.
	$data = array( 'status' => 'completed' );

	// Set the where.
	$where = array( 'order_id' => $order_id );

	// Update the campaign cart.
	psupsellmaster_db_campaign_carts_update( $data, $where );
}
add_action( 'woocommerce_order_status_completed', 'psupsellmaster_campaigns_woocommerce_order_status_completed' );

/**
 * Fires after the cart totals are updated in WooCommerce.
 */
function psupsellmaster_woo_update_campaign_carts_on_update_cart_totals() {
	// Update the campaign cart.
	psupsellmaster_update_current_campaign_cart();
}
add_action( 'woocommerce_after_calculate_totals', 'psupsellmaster_woo_update_campaign_carts_on_update_cart_totals' );

/**
 * Fires after a product is added to the cart in WooCommerce.
 */
function psupsellmaster_woo_on_add_to_cart_update_campaign_events() {
	// Check if the nonce is not set.
	if ( ! isset( $_POST['psupsellmaster'] ) || ! isset( $_POST['psupsellmaster']['nonce'] ) ) {
		return;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_POST['psupsellmaster']['nonce'] ) );

	// Check if the nonce is invalid.
	if ( false === wp_verify_nonce( $nonce, 'psupsellmaster-nonce' ) ) {
		return;
	}

	// Get the args.
	$args = isset( $_POST['psupsellmaster'] ) ? map_deep( wp_unslash( $_POST['psupsellmaster'] ), 'sanitize_text_field' ) : array();
	$args = is_array( $args ) ? $args : json_decode( stripslashes( $args ), true );
	$args = is_array( $args ) ? array_filter( $args ) : array();

	// Check if the args is empty.
	if ( empty( $args ) ) {
		return false;
	}

	// Get the campaign id.
	$campaign_id = isset( $args['campaign_id'] ) ? filter_var( $args['campaign_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		return false;
	}

	// Get the location.
	$location = isset( $args['location'] ) ? $args['location'] : '';

	// Check if the location is empty.
	if ( empty( $location ) ) {
		return false;
	}

	// Set the event date.
	$event_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
	$event_date = $event_date->format( 'Y-m-d' );

	// Set the event data.
	$event_data = array(
		'campaign_id' => $campaign_id,
		'event_date'  => $event_date,
		'event_name'  => 'click',
		'location'    => $location,
	);

	// Increment the campaign events quantity.
	psupsellmaster_increase_campaign_events_quantity( $event_data );

	// Set the event name.
	$event_data['event_name'] = 'add_to_cart';

	// Increment the campaign events quantity.
	psupsellmaster_increase_campaign_events_quantity( $event_data );
}
add_action( 'woocommerce_add_to_cart', 'psupsellmaster_woo_on_add_to_cart_update_campaign_events', 30 );

/**
 * Fires once an order has been created in WooCommerce.
 *
 * @param int $order_id The order id.
 */
function psupsellmaster_campaigns_woo_new_order( $order_id ) {
	// Get the cart key.
	$cart_key = psupsellmaster_session_get( 'cart_key' );

	// Set the data.
	$data = array( 'order_id' => $order_id );

	// Set the where.
	$where = array( 'cart_key' => $cart_key );

	// Update the campaign cart.
	psupsellmaster_db_campaign_carts_update( $data, $where );
}
add_action( 'woocommerce_new_order', 'psupsellmaster_campaigns_woo_new_order' );

/**
 * Clear the campaigns-related cart session in WooCommerce.
 */
function psupsellmaster_campaigns_woocommerce_cart_emptied() {
	// Clear the session data.
	psupsellmaster_campaigns_session_clear();
}
add_action( 'woocommerce_cart_emptied', 'psupsellmaster_campaigns_woocommerce_cart_emptied' );

/**
 * Check if a coupon is valid for a product in WooCommerce.
 *
 * @param bool   $valid Whether the coupon is valid.
 * @param object $product The product.
 * @param object $coupon The coupon.
 * @return bool Whether the coupon is valid.
 */
function psupsellmaster_campaigns_woo_coupon_is_valid_for_product( $valid, $product, $coupon ) {
	// Check if the valid is false.
	if ( false === $valid ) {
		// Return the valid.
		return $valid;
	}

	// Get the coupon id.
	$coupon_id = $coupon->get_id();

	// Check if the coupon id is empty.
	if ( empty( $coupon_id ) ) {
		// Return the valid.
		return $valid;
	}

	// Get the coupon code.
	$coupon_code = psupsellmaster_get_coupon_code( $coupon_id );

	// Check if the coupon code is empty.
	if ( empty( $coupon_code ) ) {
		// Return the valid.
		return $valid;
	}

	// Check if the coupon is not from campaigns.
	if ( ! psupsellmaster_is_coupon_from_campaigns( $coupon_code ) ) {
		// Return the valid.
		return $valid;
	}

	// Get the product id.
	$product_id = $product->get_id();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		// Return the valid.
		return $valid;
	}

	// Set the args.
	$args = array(
		'products' => array( $product_id ),
	);

	// Get the campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( $args );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Return the valid.
		return $valid;
	}

	// Get the first campaign: only a single campaign/coupon is valid per product.
	$campaign = array_shift( $campaigns );

	// Check if the campaign is empty.
	if ( empty( $campaign ) ) {
		// Return the valid.
		return $valid;
	}

	// Get the coupons.
	$coupons = isset( $campaign['coupons'] ) ? $campaign['coupons'] : array();

	// Get a single coupon.
	$coupon = array_shift( $coupons );

	// Get the campaign coupon id.
	$campaign_coupon_id = isset( $coupon['coupon_id'] ) ? filter_var( $coupon['coupon_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the campaign coupon id is empty.
	if ( empty( $campaign_coupon_id ) ) {
		// Return the valid.
		return $valid;
	}

	// Check if the coupons don't match.
	if ( $campaign_coupon_id !== $coupon_id ) {
		// Set the valid.
		$valid = false;
	}

	// Return the valid.
	return $valid;
}
add_filter( 'woocommerce_coupon_is_valid_for_product', 'psupsellmaster_campaigns_woo_coupon_is_valid_for_product', 10, 3 );

/**
 * Check if a coupon is valid in WooCommerce.
 *
 * @param bool   $is_valid Whether the coupon is valid.
 * @param object $coupon The coupon.
 * @return bool Whether the coupon is valid.
 */
function psupsellmaster_woo_filter_is_coupon_valid( $is_valid, $coupon ) {
	// Check if the is valid is false.
	if ( false === $is_valid ) {
		// Return the is valid.
		return $is_valid;
	}

	// Get the coupon id.
	$coupon_id = $coupon->get_id();

	// Check if the coupon id is empty.
	if ( empty( $coupon_id ) ) {
		// Return the is valid.
		return $is_valid;
	}

	// Get the campaign id.
	$campaign_id = psupsellmaster_get_campaign_id_by_coupon_id( $coupon_id );

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Return the is valid.
		return $is_valid;
	}

	// Set the args.
	$args = array(
		'campaign_id' => $campaign_id,
		'coupon_id'   => $coupon_id,
	);

	// Validate the coupon.
	$validation = psupsellmaster_validate_coupon( $args );

	// Set the is valid.
	$is_valid = isset( $validation['is_valid'] ) ? $validation['is_valid'] : false;

	// Return the is valid.
	return $is_valid;
}
add_filter( 'woocommerce_coupon_is_valid', 'psupsellmaster_woo_filter_is_coupon_valid', 10, 2 );

/**
 * Filter the coupon products in WooCommerce.
 *
 * @param array  $products The products.
 * @param object $coupon The coupon.
 * @return array The products.
 */
function psupsellmaster_woo_coupon_get_product_ids( $products, $coupon ) {
	// Get the coupon id.
	$coupon_id = $coupon->get_id();

	// Check if the coupon id is empty.
	if ( empty( $coupon_id ) ) {
		// Return the products.
		return $products;
	}

	// Get the campaign id.
	$campaign_id = psupsellmaster_get_campaign_id_by_coupon_id( $coupon_id );

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Return the products.
		return $products;
	}

	// Set the products.
	$products = array();

	// Get the campaign.
	$campaign = psupsellmaster_get_eligible_campaign_by_id( $campaign_id );

	// Check if the campaign is empty.
	if ( empty( $campaign ) ) {
		// Return the products.
		return $products;
	}

	// Set the products.
	$products = isset( $campaign['products'] ) ? $campaign['products'] : array();

	// Return the products.
	return $products;
}
add_filter( 'woocommerce_coupon_get_product_ids', 'psupsellmaster_woo_coupon_get_product_ids', 10, 2 );

/**
 * Filter the campaign-related coupon excluded products in WooCommerce.
 *
 * @param array  $products The products.
 * @param object $coupon The coupon.
 * @return array The products.
 */
function psupsellmaster_woo_campaigns_coupon_get_excluded_product_ids( $products, $coupon ) {
	// Get the coupon id.
	$coupon_id = $coupon->get_id();

	// Check if the coupon id is empty.
	if ( empty( $coupon_id ) ) {
		// Return the products.
		return $products;
	}

	// Get the campaign id.
	$campaign_id = psupsellmaster_get_campaign_id_by_coupon_id( $coupon_id );

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Return the products.
		return $products;
	}

	// Allow developers to filter this.
	$products = apply_filters( 'psupsellmaster_woo_campaigns_coupon_excluded_products', $products, $coupon_id );
	$products = apply_filters( 'psupsellmaster_campaigns_coupon_excluded_products', $products, $coupon_id );

	// Return the products.
	return $products;
}
add_filter( 'woocommerce_coupon_get_excluded_product_ids', 'psupsellmaster_woo_campaigns_coupon_get_excluded_product_ids', 10, 2 );

/**
 * Filter the price html in WooCommerce.
 *
 * @param string $price The price.
 * @param object $product The product.
 * @return string The price.
 */
function psupsellmaster_campaigns_woo_get_price_html( $price, $product ) {
	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the discount text.
	$discount_text = isset( $settings['prices_discount_text'] ) ? $settings['prices_discount_text'] : '';

	// Check if the discount text is empty.
	if ( empty( $discount_text ) ) {
		// Return the price.
		return $price;
	}

	// Get the product type.
	$product_type = $product->get_type();

	// Check the product type.
	if ( in_array( $product_type, array( 'grouped', 'variable' ), true ) ) {
		// Return the price.
		return $price;
	}

	// Check if the price is empty.
	if ( empty( $price ) ) {
		// Return the price.
		return $price;
	}

	// Get the product id.
	$product_id = $product->get_id();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		// Return the price.
		return $price;
	}

	// Get the eligible campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( array( 'products' => array( $product_id ) ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Return the price.
		return $price;
	}

	// Get a single campaign.
	$campaign = array_shift( $campaigns );

	// Get the coupons.
	$coupons = isset( $campaign['coupons'] ) ? $campaign['coupons'] : array();

	// Get a single coupon.
	$coupon = array_shift( $coupons );

	// Get the coupon type.
	$coupon_type = isset( $coupon['type'] ) ? $coupon['type'] : false;

	// Check if the coupon type is not valid.
	if ( ! in_array( $coupon_type, array( 'discount_percent', 'discount_fixed' ), true ) ) {
		// Return the price.
		return $price;
	}

	// Get the coupon amount.
	$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if the coupon amount is empty.
	if ( empty( $coupon_amount ) ) {
		// Return the price.
		return $price;
	}

	// Get the price amount.
	$price_amount = $product->get_regular_price();

	// Check if the price amount is empty.
	if ( empty( $price_amount ) ) {
		// Return the price.
		return $price;
	}

	// Set the discount amount.
	$discount_amount = 0;

	// Set the formatted discount.
	$formatted_discount = 0;

	// Check the coupon type.
	if ( 'discount_percent' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = $price_amount * ( floatval( $coupon_amount ) / 100 );

		// Set the formatted discount.
		$formatted_discount = "{$coupon_amount}%";

		// Check the coupon type.
	} elseif ( 'discount_fixed' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = $coupon_amount;

		// Set the formatted discount.
		$formatted_discount = wc_price( $coupon_amount ) . $product->get_price_suffix();
	}

	// Check if the discount amount is lower than or equal to zero.
	if ( $discount_amount <= 0 ) {
		// Return the price.
		return $price;
	}

	// Set the discounted price.
	$discounted_price = $price_amount - $discount_amount;
	$discounted_price = $discounted_price > 0 ? $discounted_price : 0;

	// Set the formatted old price.
	$formatted_old_price = wc_get_price_to_display( $product, array( 'price' => $price_amount ) );
	$formatted_old_price = wc_price( $formatted_old_price ) . $product->get_price_suffix();

	// Set the formatted new price.
	$formatted_new_price = wc_get_price_to_display( $product, array( 'price' => $discounted_price ) );
	$formatted_new_price = wc_price( $formatted_new_price ) . $product->get_price_suffix();

	// Set the output.
	$output = wp_kses_post( wpautop( stripslashes( $discount_text ) ) );
	$output = str_replace( '{old_price}', $formatted_old_price, $output );
	$output = str_replace( '{new_price}', $formatted_new_price, $output );
	$output = str_replace( '{discount_amount}', $formatted_discount, $output );
	$output = '<div class="psupsellmaster-product-prices">' . $output . '</div>';

	// Set the price.
	$price = $output;

	// Return the price.
	return $price;
}
add_filter( 'woocommerce_get_price_html', 'psupsellmaster_campaigns_woo_get_price_html', 10, 2 );

/**
 * Filter the price html for variable products in WooCommerce.
 *
 * @param string $price The price.
 * @param object $product The product.
 * @return string The price.
 */
function psupsellmaster_campaigns_woo_get_price_html_variable( $price, $product ) {
	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the discount text.
	$discount_text = isset( $settings['prices_discount_text'] ) ? $settings['prices_discount_text'] : '';

	// Check if the discount text is empty.
	if ( empty( $discount_text ) ) {
		// Return the price.
		return $price;
	}

	// Get the product type.
	$product_type = $product->get_type();

	// Check the product type.
	if ( 'variable' !== $product_type ) {
		// Return the price.
		return $price;
	}

	// Check if the price is empty.
	if ( empty( $price ) ) {
		// Return the price.
		return $price;
	}

	// Get the product id.
	$product_id = $product->get_id();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		// Return the price.
		return $price;
	}

	// Get the eligible campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( array( 'products' => array( $product_id ) ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Return the price.
		return $price;
	}

	// Get a single campaign.
	$campaign = array_shift( $campaigns );

	// Get the coupons.
	$coupons = isset( $campaign['coupons'] ) ? $campaign['coupons'] : array();

	// Get a single coupon.
	$coupon = array_shift( $coupons );

	// Get the coupon type.
	$coupon_type = isset( $coupon['type'] ) ? $coupon['type'] : false;

	// Check if the coupon type is not valid.
	if ( ! in_array( $coupon_type, array( 'discount_percent', 'discount_fixed' ), true ) ) {
		// Return the price.
		return $price;
	}

	// Get the coupon amount.
	$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if the coupon amount is empty.
	if ( empty( $coupon_amount ) ) {
		// Return the price.
		return $price;
	}

	// Get the variation prices.
	$variation_prices = $product->get_variation_prices( false );

	// Get the price amount (min).
	$price_amount = current( $variation_prices['regular_price'] );

	// Check if the price amount is empty.
	if ( empty( $price_amount ) ) {
		// Return the price.
		return $price;
	}

	// Set the discount amount.
	$discount_amount = 0;

	// Set the formatted discount.
	$formatted_discount = 0;

	// Check the coupon type.
	if ( 'discount_percent' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = $price_amount * ( floatval( $coupon_amount ) / 100 );

		// Set the formatted discount.
		$formatted_discount = "{$coupon_amount}%";

		// Check the coupon type.
	} elseif ( 'discount_fixed' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = $coupon_amount;

		// Set the formatted discount.
		$formatted_discount = wc_price( $coupon_amount ) . $product->get_price_suffix();
	}

	// Check if the discount amount is lower than or equal to zero.
	if ( $discount_amount <= 0 ) {
		// Return the price.
		return $price;
	}

	// Set the discounted price.
	$discounted_price = $price_amount - $discount_amount;
	$discounted_price = $discounted_price > 0 ? $discounted_price : 0;

	// Set the old price.
	$old_price = wc_get_price_to_display( $product, array( 'price' => $price_amount ) );
	$old_price = wc_price( $old_price ) . $product->get_price_suffix();

	// Set the new price.
	$new_price = wc_get_price_to_display( $product, array( 'price' => $discounted_price ) );
	$new_price = wc_price( $new_price ) . $product->get_price_suffix();

	// Set the formatted old price.
	$formatted_old_price = wc_get_price_to_display( $product, array( 'price' => $price_amount ) );
	$formatted_old_price = wc_price( $formatted_old_price ) . $product->get_price_suffix();

	// Set the formatted new price.
	$formatted_new_price = wc_get_price_to_display( $product, array( 'price' => $discounted_price ) );
	$formatted_new_price = wc_price( $formatted_new_price ) . $product->get_price_suffix();

	// Set the output.
	$output = wp_kses_post( wpautop( stripslashes( $discount_text ) ) );
	$output = str_replace( '{old_price}', $formatted_old_price, $output );
	$output = str_replace( '{new_price}', $formatted_new_price, $output );
	$output = str_replace( '{discount_amount}', $formatted_discount, $output );
	$output = '<div class="psupsellmaster-product-prices">' . $output . '</div>';

	// Set the price.
	$price = $output;

	// Return the price.
	return $price;
}
add_filter( 'woocommerce_get_price_html', 'psupsellmaster_campaigns_woo_get_price_html_variable', 10, 2 );

/**
 * Filter the price html for grouped products in WooCommerce.
 *
 * @param string $price The price.
 * @param object $product The product.
 * @return string The price.
 */
function psupsellmaster_campaigns_woo_get_price_html_grouped( $price, $product ) {
	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the discount text.
	$discount_text = isset( $settings['prices_discount_text'] ) ? $settings['prices_discount_text'] : '';

	// Check if the discount text is empty.
	if ( empty( $discount_text ) ) {
		// Return the price.
		return $price;
	}

	// Get the product type.
	$product_type = $product->get_type();

	// Check the product type.
	if ( 'grouped' !== $product_type ) {
		// Return the price.
		return $price;
	}

	// Check if the price is empty.
	if ( empty( $price ) ) {
		// Return the price.
		return $price;
	}

	// Get the product id.
	$product_id = $product->get_id();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		// Return the price.
		return $price;
	}

	// Get the eligible campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( array( 'products' => array( $product_id ) ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Return the price.
		return $price;
	}

	// Get a single campaign.
	$campaign = array_shift( $campaigns );

	// Get the coupons.
	$coupons = isset( $campaign['coupons'] ) ? $campaign['coupons'] : array();

	// Get a single coupon.
	$coupon = array_shift( $coupons );

	// Get the coupon type.
	$coupon_type = isset( $coupon['type'] ) ? $coupon['type'] : false;

	// Check if the coupon type is not valid.
	if ( ! in_array( $coupon_type, array( 'discount_percent', 'discount_fixed' ), true ) ) {
		// Return the price.
		return $price;
	}

	// Get the coupon amount.
	$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if the coupon amount is empty.
	if ( empty( $coupon_amount ) ) {
		// Return the price.
		return $price;
	}

	// Set the child prices.
	$child_prices = array();

	// Get the children.
	$children = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );

	// Loop through the children.
	foreach ( $children as $child ) {
		// Get the child price.
		$child_price = $child->get_regular_price();

		// Check if the child price is not empty.
		if ( '' !== $child_price ) {
			// Add the child price to the list.
			array_push( $child_prices, $child_price );
		}
	}

	// Set the price amount.
	$price_amount = ! empty( $child_prices ) ? min( $child_prices ) : 0;

	// Check if the price amount is empty.
	if ( empty( $price_amount ) ) {
		// Return the price.
		return $price;
	}

	// Set the discount amount.
	$discount_amount = 0;

	// Set the formatted discount.
	$formatted_discount = 0;

	// Check the coupon type.
	if ( 'discount_percent' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = $price_amount * ( floatval( $coupon_amount ) / 100 );

		// Set the formatted discount.
		$formatted_discount = "{$coupon_amount}%";

		// Check the coupon type.
	} elseif ( 'discount_fixed' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = $coupon_amount;

		// Set the formatted discount.
		$formatted_discount = wc_price( $coupon_amount ) . $product->get_price_suffix();
	}

	// Check if the discount amount is lower than or equal to zero.
	if ( $discount_amount <= 0 ) {
		// Return the price.
		return $price;
	}

	// Set the discounted price.
	$discounted_price = $price_amount - $discount_amount;
	$discounted_price = $discounted_price > 0 ? $discounted_price : 0;

	// Set the old price.
	$old_price = wc_get_price_to_display( $product, array( 'price' => $price_amount ) );
	$old_price = wc_price( $old_price ) . $product->get_price_suffix();

	// Set the new price.
	$new_price = wc_get_price_to_display( $product, array( 'price' => $discounted_price ) );
	$new_price = wc_price( $new_price ) . $product->get_price_suffix();

	// Set the formatted old price.
	$formatted_old_price = wc_get_price_to_display( $product, array( 'price' => $price_amount ) );
	$formatted_old_price = wc_price( $formatted_old_price ) . $product->get_price_suffix();

	// Set the formatted new price.
	$formatted_new_price = wc_get_price_to_display( $product, array( 'price' => $discounted_price ) );
	$formatted_new_price = wc_price( $formatted_new_price ) . $product->get_price_suffix();

	// Set the output.
	$output = wp_kses_post( wpautop( stripslashes( $discount_text ) ) );
	$output = str_replace( '{old_price}', $formatted_old_price, $output );
	$output = str_replace( '{new_price}', $formatted_new_price, $output );
	$output = str_replace( '{discount_amount}', $formatted_discount, $output );
	$output = '<div class="psupsellmaster-product-prices">' . $output . '</div>';

	// Set the price.
	$price = $output;

	// Return the price.
	return $price;
}
add_filter( 'woocommerce_get_price_html', 'psupsellmaster_campaigns_woo_get_price_html_grouped', 10, 2 );
