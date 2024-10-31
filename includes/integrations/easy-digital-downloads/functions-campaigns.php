<?php
/**
 * Integrations - Easy Digital Downloads - Functions - Campaigns.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the coupons in Easy Digital Downloads.
 *
 * @param array $args The arguments.
 * @return array The data.
 */
function psupsellmaster_edd_get_coupons( $args = array() ) {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_page'        => 1,
		'psupsellmaster_search_text' => '',
		'psupsellmaster_group'       => '',
		'in__in'                     => array(),
		'number'                     => 20,
		'order'                      => 'ASC',
		'orderby'                    => 'code',
		'type'                       => 'discount',
	);

	// Parse the args.
	$parsed_args = wp_parse_args( $args, $defaults );

	// Check the posts per page.
	if ( isset( $parsed_args['posts_per_page'] ) ) {
		// Set the number.
		$parsed_args['number'] = $parsed_args['posts_per_page'];
	}

	// Check the posts per page.
	if ( isset( $parsed_args['post__in'] ) ) {
		// Set the number.
		$parsed_args['id__in'] = $parsed_args['post__in'];
	}
	
	// Set the offset.
	$parsed_args['offset'] = ( $parsed_args['psupsellmaster_page'] - 1 ) * $parsed_args['number'];

	// Set the offset.
	$parsed_args['search'] = $parsed_args['psupsellmaster_search_text'];

	// Get the items.
	$items = edd_get_adjustments( $parsed_args );

	// Get the filtered count.
	$count_filtered = count( $items );

	// Set the count.
	$parsed_args['count'] = true;

	// Get the total count.
	$count_total = absint( edd_get_adjustments( $parsed_args ) );

	// Set the total pages.
	$total_pages = ceil( $count_total / max( $parsed_args['number'], 1 ) );

	// Set the meta.
	$meta = array(
		'count_filtered' => $count_filtered,
		'count_total'    => $count_total,
		'total_pages'    => $total_pages,
	);

	// Set the data.
	$data = array(
		'items' => $items,
		'meta'  => $meta,
	);

	// Set the items.
	$items = array();

	// Loop through the items.
	foreach ( $data['items'] as $item_data ) {
		// Get the campaign id.
		$campaign_id = psupsellmaster_get_campaign_id_by_coupon_id( $item_data->id );

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
			'id'   => $item_data->id,
			'code' => $item_data->code,
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
 * Get the coupon code in Easy Digital Downloads.
 *
 * @param int $coupon_id The coupon id.
 * @return string Return the coupon code.
 */
function psupsellmaster_edd_get_coupon_code( $coupon_id ) {
	// Set the coupon code.
	$coupon_code = edd_get_discount_code( $coupon_id );

	// Return the coupon code.
	return $coupon_code;
}

/**
 * Check if an Easy Digital Downloads coupon code exists.
 *
 * @param string $coupon_code The coupon code.
 * @return bool Return true if the coupon code exists, false otherwise.
 */
function psupsellmaster_edd_coupon_code_exists( $coupon_code ) {
	// Set the exists.
	$exists = false;

	// Get the coupon.
	$coupon = edd_get_discount_id_by_code( $coupon_code );

	// Check if the coupon exists.
	if ( ! empty( $coupon ) ) {
		// Set the exists.
		$exists = true;
	}

	// Return the exists.
	return $exists;
}

/**
 * Get a coupon ID by code for Easy Digital Downloads.
 *
 * @param string $coupon_code The coupon code.
 * @return int|false Return the coupon ID.
 */
function psupsellmaster_edd_get_coupon_id_by_code( $coupon_code ) {
	// Get the coupon id.
	$coupon_id = edd_get_discount_id_by_code( $coupon_code );

	// Return the coupon id.
	return $coupon_id;
}

/**
 * Get the campaign coupon codes in Easy Digital Downloads.
 *
 * @param int $campaign_id The campaign id.
 * @return array Return the campaign coupon codes.
 */
function psupsellmaster_edd_get_campaign_coupon_codes( $campaign_id ) {
	// Set the coupons.
	$coupons = array();

	// Set the adjustment type.
	$adjustment_type = 'discount';

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`edd_adjustments`.`code`
		FROM
			%i AS `campaign_coupons`
		INNER JOIN
			%i AS `edd_adjustments`
		ON
			`campaign_coupons`.`coupon_id` = `edd_adjustments`.`id`
		WHERE
			`campaign_coupons`.`campaign_id` = %d
		AND
			`edd_adjustments`.`type` = %s
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' ),
		PsUpsellMaster_Database::get_table_name( 'edd_adjustments' ),
		$campaign_id,
		$adjustment_type
	);

	// Get the coupons.
	$coupons = PsUpsellMaster_Database::get_col( $sql_query );
	$coupons = is_array( $coupons ) ? $coupons : array();
	$coupons = array_filter( array_unique( $coupons ) );

	// Return the coupons.
	return $coupons;
}

/**
 * Get the coupon statuses in Easy Digital Downloads.
 *
 * @return array The coupon statuses.
 */
function psupsellmaster_edd_get_coupon_statuses() {
	// Set the statuses.
	$statuses = array(
		'active',
		'archived',
		'expired',
		'inactive',
	);

	// Return the statuses.
	return $statuses;
}

/**
 * Update the coupon status in Easy Digital Downloads.
 *
 * @param int    $coupon_id The coupon id.
 * @param string $status The coupon status.
 */
function psupsellmaster_edd_update_coupon_status( $coupon_id, $status ) {
	// Set the data.
	$data = array( 'status' => 'inactive' );

	// Set the edd statuses.
	$edd_statuses = psupsellmaster_edd_get_coupon_statuses();

	// Check if the status is valid.
	if ( in_array( $status, $edd_statuses, true ) ) {
		// Set the status.
		$data['status'] = $status;
	}

	// Update the coupon.
	edd_update_discount( $coupon_id, $data );
}

/**
 * Check if a coupon is valid in Easy Digital Downloads.
 *
 * @param string $code The coupon code.
 * @return bool Whether the coupon is valid.
 */
function psupsellmaster_edd_is_coupon_valid( $code ) {
	// Set the is valid.
	$is_valid = false;

	// Check if the coupon is valid.
	if ( true === edd_is_discount_valid( $code, '', false ) ) {
		// Set the is valid.
		$is_valid = true;
	}

	// Return the is valid.
	return $is_valid;
}

/**
 * Check the coupon when a coupon is applied in Easy Digital Downloads.
 *
 * @param string $coupon_code The coupon code.
 */
function psupsellmaster_edd_update_coupons_on_apply_coupon( $coupon_code ) {
	// Maybe remove coupons from the cart.
	psupsellmaster_maybe_remove_coupons_from_cart_on_apply_coupon( $coupon_code );
}
add_action( 'edd_cart_discount_set', 'psupsellmaster_edd_update_coupons_on_apply_coupon' );

/**
 * Fix the AJAX discount response in Easy Digital Downloads.
 * Since applying coupons to the cart might also remove other coupons,
 * the returned data would not be correct (would still show excluded coupons).
 * Therefore we try to fix the response with this function.
 *
 * @param array $response The response.
 */
function psupsellmaster_edd_fix_ajax_discount_response( $response ) {
	// Check if the msg is not set.
	if ( ! isset( $response['msg'] ) ) {
		// Return the response.
		return $response;
	}

	// Check if the msg is not valid.
	if ( 'valid' !== $response['msg'] ) {
		// Return the response.
		return $response;
	}

	// Get the discounts.
	$discounts = edd_get_cart_discounts();

	// Get the total.
	$total = edd_get_cart_total( $discounts );

	// Set the total.
	$response['total'] = html_entity_decode( edd_currency_filter( edd_format_amount( $total ) ), ENT_COMPAT, 'UTF-8' );

	// Set the html.
	$response['html'] = edd_get_cart_discounts_html( $discounts );

	// Return the response.
	return $response;
}
add_filter( 'edd_ajax_discount_response', 'psupsellmaster_edd_fix_ajax_discount_response' );

/**
 * Check if using multiple coupons is allowed in Easy Digital Downloads.
 *
 * @return bool Whether multiple coupons are allowed or not.
 */
function psupsellmaster_edd_is_multiple_coupons_allowed() {
	// Set the allowed.
	$allowed = edd_multiple_discounts_allowed();

	// Allow developers to filter this.
	$allowed = apply_filters( 'psupsellmaster_edd_is_multiple_coupons_allowed', $allowed );

	// Return the allowed.
	return $allowed;
}

/**
 * Maybe apply the coupon to the cart in Easy Digital Downloads.
 *
 * @param string $coupon The coupon.
 */
function psupsellmaster_edd_maybe_apply_coupon_to_cart( $coupon ) {
	// Set the applied.
	$applied = false;

	// Check if the coupon is already applied.
	if ( ! psupsellmaster_edd_cart_has_coupon( $coupon ) ) {
		// Apply the coupon.
		$applied = psupsellmaster_edd_apply_coupon_to_cart( $coupon );
	}

	// Return the applied.
	return $applied;
}

/**
 * Apply the coupon to the cart in Easy Digital Downloads.
 *
 * @param string $coupon The coupon.
 * @return bool Whether the coupon was applied or not.
 */
function psupsellmaster_edd_apply_coupon_to_cart( $coupon ) {
	// Apply the coupon.
	$coupons = edd_set_cart_discount( $coupon );

	// Set the applied.
	$applied = in_array( $coupon, $coupons, true );

	// Return the applied.
	return $applied;
}

/**
 * Check if the cart has the coupon in Easy Digital Downloads.
 *
 * @param string $coupon The coupon.
 * @return bool Return true if the cart has the coupon. Otherwise, return false.
 */
function psupsellmaster_edd_cart_has_coupon( $coupon ) {
	// Set the has coupon.
	$has_coupon = false;

	// Get the coupons.
	$coupons = edd_get_cart_discounts();

	// Get the keys.
	$key = array_search( strtolower( $coupon ), array_map( 'strtolower', $coupons ), true );

	// Can't set the same discount more than once.
	if ( false !== $key ) {
		// Set the has coupon.
		$has_coupon = true;
	}

	// Return the has coupon.
	return $has_coupon;
}

/**
 * Remove the coupon from the cart -Easy Digital Downloads.
 *
 * @param string $coupon The coupon.
 */
function psupsellmaster_edd_remove_coupon_from_cart( $coupon ) {
	// Remove the coupon.
	edd_unset_cart_discount( $coupon );
}

/**
 * Get the applied coupons in Easy Digital Downloads.
 *
 * @return array The applied coupons.
 */
function psupsellmaster_edd_get_applied_coupons() {
	// Get the coupons.
	$coupons = edd_get_cart_discounts();

	// Return the coupons.
	return $coupons;
}

/**
 * Get the applied coupons in Easy Digital Downloads.
 *
 * @param string $context The context.
 * @return array The applied coupons.
 */
function psupsellmaster_edd_get_applied_coupons_by_context( $context = 'all' ) {
	// Get the coupons.
	$coupons = psupsellmaster_edd_get_applied_coupons();

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
		$sql_coupons = PsUpsellMaster_Database::prepare( "`edd_adjustments`.`code` IN ( {$placeholders} )", $coupons );

		// Set the adjustment type.
		$adjustment_type = 'discount';

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare(
			"
			SELECT
				`edd_adjustments`.`code`
			FROM
				%i AS `edd_adjustments`
			INNER JOIN
				%i AS `campaign_coupons`
			ON
				`campaign_coupons`.`coupon_id` = `edd_adjustments`.`id`
			WHERE
				`edd_adjustments`.`type` = %s
			AND
				{$sql_coupons}
			",
			PsUpsellMaster_Database::get_table_name( 'edd_adjustments' ),
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' ),
			$adjustment_type
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
		$sql_coupons = PsUpsellMaster_Database::prepare( "`edd_adjustments`.`code` IN ( {$placeholders} )", $coupons );

		// Set the adjustment type.
		$adjustment_type = 'discount';

		// Set the sql query.
		$sql_query = PsUpsellMaster_Database::prepare(
			"
			SELECT
				`edd_adjustments`.`code`
			FROM
				%i AS `edd_adjustments`
			WHERE
				`edd_adjustments`.`type` = %s
			AND
				{$sql_coupons}
			AND
				NOT EXISTS (
					SELECT
						1
					FROM
						%i AS `campaign_coupons`
					WHERE
						`campaign_coupons`.`coupon_id` = `edd_adjustments`.`id`
				)
			",
			PsUpsellMaster_Database::get_table_name( 'edd_adjustments' ),
			$adjustment_type,
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
 * Update the campaign carts when the session cart is updated in Easy Digital Downloads.
 */
function psupsellmaster_edd_update_campaign_carts_on_update_cart() {
	// Update the campaign cart.
	psupsellmaster_update_current_campaign_cart();
}
add_action( 'edd_empty_cart', 'psupsellmaster_edd_update_campaign_carts_on_update_cart' );
add_action( 'edd_post_add_to_cart', 'psupsellmaster_edd_update_campaign_carts_on_update_cart' );
add_action( 'edd_post_remove_from_cart', 'psupsellmaster_edd_update_campaign_carts_on_update_cart' );
add_action( 'edd_cart_discounts_updated', 'psupsellmaster_edd_update_campaign_carts_on_update_cart' );
add_action( 'edd_cart_discount_removed', 'psupsellmaster_edd_update_campaign_carts_on_update_cart' );
add_action( 'edd_after_set_cart_item_quantity', 'psupsellmaster_edd_update_campaign_carts_on_update_cart' );
add_action( 'edd_pre_process_purchase', 'psupsellmaster_edd_update_campaign_carts_on_update_cart' );

/**
 * Fires after an item has been added to the cart in Easy Digital Downloads.
 */
function psupsellmaster_edd_on_add_to_cart_update_campaign_events() {
	// Check if the download is not set.
	if ( ! isset( $_POST['download_id'] ) ) {
		return;
	}

	// Check if the nonce is not set.
	if ( ! isset( $_POST['nonce'] ) ) {
		return;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

	// Check if the nonce is invalid.
	if ( false === wp_verify_nonce( $nonce, 'edd-add-to-cart-' . sanitize_text_field( wp_unslash( $_POST['download_id'] ) ) ) ) {
		return;
	}

	// Get the post data.
	$post_data = isset( $_POST['post_data'] ) ? sanitize_url( wp_unslash( $_POST['post_data'] ) ) : '';

	// Set the args.
	$args = array();

	// Check if the post data is a string and if it is not empty.
	if ( is_string( $post_data ) && ! empty( $post_data ) ) {
		// Parse the post data and set the args.
		parse_str( $post_data, $args );

		// Get the args.
		$args = isset( $args['psupsellmaster'] ) ? $args['psupsellmaster'] : array();
	}

	// Set the args (remove empty entries).
	$args = array_filter( $args );

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
add_action( 'edd_post_add_to_cart', 'psupsellmaster_edd_on_add_to_cart_update_campaign_events' );

/**
 * Fires after an order is created in Easy Digital Downloads.
 *
 * @param int $order_id   The order id.
 */
function psupsellmaster_campaigns_edd_new_order( $order_id ) {
	// Get the cart key.
	$cart_key = psupsellmaster_session_get( 'cart_key' );

	// Set the data.
	$data = array( 'order_id' => $order_id );

	// Set the where.
	$where = array( 'cart_key' => $cart_key );

	// Update the campaign cart.
	psupsellmaster_db_campaign_carts_update( $data, $where );
}
add_action( 'edd_built_order', 'psupsellmaster_campaigns_edd_new_order' );

/**
 * Fires when an order status is changed in Easy Digital Downloads.
 *
 * @param string $old_status The old status.
 * @param string $new_status The new status.
 * @param int    $order_id   The order id.
 */
function psupsellmaster_campaigns_edd_transition_order_status( $old_status, $new_status, $order_id ) {
	// Check if the order id is empty.
	if ( empty( $order_id ) ) {
		return false;
	}

	// Check if the new status is not valid.
	if ( 'complete' !== $new_status ) {
		return false;
	}

	// Set the data.
	$data = array( 'status' => 'completed' );

	// Set the where.
	$where = array( 'order_id' => $order_id );

	// Update the campaign cart.
	psupsellmaster_db_campaign_carts_update( $data, $where );
}
add_action( 'edd_transition_order_status', 'psupsellmaster_campaigns_edd_transition_order_status', 10, 3 );

/**
 * Clear the campaigns-related cart session in Easy Digital Downloads.
 */
function psupsellmaster_campaigns_edd_empty_cart() {
	// Clear the session data.
	psupsellmaster_campaigns_session_clear();
}
add_action( 'edd_empty_cart', 'psupsellmaster_campaigns_edd_empty_cart' );

/**
 * Check if a coupon is valid in Easy Digital Downloads.
 *
 * @param bool   $is_valid Whether the discount is valid.
 * @param int    $coupon_id The coupon id.
 * @param string $coupon_code The coupon code.
 * @param object $user The user object.
 * @param bool   $set_error Whether to set an error.
 */
function psupsellmaster_edd_filter_is_coupon_valid( $is_valid, $coupon_id, $coupon_code, $user, $set_error ) {
	// Check if the is valid is false.
	if ( false === $is_valid ) {
		// Return the is valid.
		return $is_valid;
	}

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

	// Check if the set error is true, the is valid is false, and there is a reason.
	if ( $set_error && false === $is_valid && isset( $validation['reason'] ) ) {
		// Set the error.
		edd_set_error( 'edd-discount-error', $validation['reason'] );
	}

	// Return the is valid.
	return $is_valid;
}
add_action( 'edd_is_discount_valid', 'psupsellmaster_edd_filter_is_coupon_valid', 10, 5 );

/**
 * Filter the coupon products in Easy Digital Downloads.
 *
 * @param array $products The products.
 * @param int   $coupon_id The coupon id.
 */
function psupsellmaster_edd_get_discount_product_reqs( $products, $coupon_id ) {
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
add_filter( 'edd_get_discount_product_reqs', 'psupsellmaster_edd_get_discount_product_reqs', 10, 2 );

/**
 * Filter the coupon excluded products in Easy Digital Downloads.
 * Set global variable data based on the shopping cart.
 * This function does not change the received filter value.
 *
 * @param array $products The products.
 * @param int   $coupon_id The coupon id.
 */
function psupsellmaster_edd_campaigns_set_invalid_for_products( $products, $coupon_id ) {
	// Set the invalid.
	$invalid = array();

	// Get the cart product ids.
	$cart_product_ids = psupsellmaster_get_session_cart_product_ids();

	// Loop through the cart product ids.
	foreach ( $cart_product_ids as $product_id ) {
		// Check if the coupon is valid for the product.
		if ( psupsellmaster_campaigns_edd_coupon_is_valid_for_product( $coupon_id, $product_id ) ) {
			continue;
		}

		// Add the product to the list.
		array_push( $invalid, $product_id );
	}

	// Check if the invalid is empty.
	if ( empty( $invalid ) ) {
		// Return the products.
		return $products;
	}

	global $psupsellmaster_campaigns;

	// Get the data.
	$data = is_array( $psupsellmaster_campaigns ) ? $psupsellmaster_campaigns : array();

	// Get the edd.
	$edd = isset( $data['edd'] ) ? $data['edd'] : array();

	// Get the coupons.
	$coupons = isset( $edd['coupons'] ) ? $edd['coupons'] : array();

	// Get the coupon.
	$coupon = isset( $coupons[ $coupon_id ] ) ? $coupons[ $coupon_id ] : array();

	// Set the coupon.
	$coupon['invalid_products'] = $invalid;

	// Set the coupons.
	$coupons[ $coupon_id ] = $coupon;

	// Set the edd.
	$edd['coupons'] = $coupons;

	// Set the data.
	$data['edd'] = $edd;

	// Set the global.
	$psupsellmaster_campaigns = $data;

	// Return the products.
	return $products;
}
add_filter( 'psupsellmaster_edd_campaigns_coupon_excluded_products', 'psupsellmaster_edd_campaigns_set_invalid_for_products', 6, 2 );

/**
 * Filter the coupon excluded products in Easy Digital Downloads.
 * Set the coupon excluded products in case the coupon is not valid for some products.
 * This solution is needed since Easy Digital Downloads does not have a specific hook for this.
 *
 * @param array $products The products.
 * @param int   $coupon_id The coupon id.
 */
function psupsellmaster_edd_campaigns_coupon_invalid_for_products( $products, $coupon_id ) {
	global $psupsellmaster_campaigns;

	// Get the data.
	$data = is_array( $psupsellmaster_campaigns ) ? $psupsellmaster_campaigns : array();

	// Get the edd.
	$edd = isset( $data['edd'] ) ? $data['edd'] : array();

	// Get the coupons.
	$coupons = isset( $edd['coupons'] ) ? $edd['coupons'] : array();

	// Get the coupon.
	$coupon = isset( $coupons[ $coupon_id ] ) ? $coupons[ $coupon_id ] : array();

	// Get the invalid.
	$invalid = ! empty( $coupon['invalid_products'] ) ? $coupon['invalid_products'] : array();

	// Check if the invalid is empty.
	if ( empty( $invalid ) ) {
		// Return the products.
		return $products;
	}

	// Set the products.
	$products = array_merge( $products, $invalid );

	// Return the products.
	return $products;
}
add_filter( 'psupsellmaster_edd_campaigns_coupon_excluded_products', 'psupsellmaster_edd_campaigns_coupon_invalid_for_products', 10, 2 );

/**
 * Check if a coupon is valid for a product in Easy Digital Downloads.
 *
 * @param object $coupon_id The coupon id.
 * @param object $product_id The product id.
 * @return bool Whether the coupon is valid.
 */
function psupsellmaster_campaigns_edd_coupon_is_valid_for_product( $coupon_id, $product_id ) {
	// Set the valid.
	$valid = false;

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
		// Return the valid.
		return $valid;
	}

	// Set the valid.
	$valid = true;

	// Return the valid.
	return $valid;
}

/**
 * Filter the discount amount in Easy Digital Downloads.
 *
 * @param  null          $discount_amount The discount amount.
 * @param  \EDD_Discount $discount        The discount instance.
 * @param  array         $item            The cart item.
 * @return null|float    $discount_amount The discount amount.
 */
function psupsellmaster_edd_discounts_item_amount_loop( $discount_amount, $discount, $item ) {
	// Get the product id.
	$product_id = isset( $item['id'] ) ? filter_var( $item['id'], FILTER_VALIDATE_INT ) : false;

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		// Return the discount amount.
		return $discount_amount;
	}

	// Get the discount id.
	$discount_id = $discount->get_id();

	// Check if the discount id is empty.
	if ( empty( $discount_id ) ) {
		// Return the discount amount.
		return $discount_amount;
	}

	// Set the args.
	$args = array(
		'products' => array( $product_id ),
	);

	// Get the campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( $args );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Return the discount amount.
		return $discount_amount;
	}

	// Get the first campaign: only a single campaign/coupon is valid per product.
	$campaign = array_shift( $campaigns );

	// Check if the campaign is empty.
	if ( empty( $campaign ) ) {
		// Return the discount amount.
		return $discount_amount;
	}

	// Get the coupons.
	$coupons = isset( $campaign['coupons'] ) ? $campaign['coupons'] : array();

	// Get a single coupon.
	$coupon = array_shift( $coupons );

	// Get the coupon id.
	$coupon_id = isset( $coupon['coupon_id'] ) ? filter_var( $coupon['coupon_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the coupon id is empty.
	if ( empty( $coupon_id ) ) {
		// Return the discount amount.
		return $discount_amount;
	}

	// Check if the coupons don't match.
	if ( $coupon_id !== $discount_id ) {
		// Set the discount amount to zero.
		$discount_amount = 0;

		// Return the discount amount.
		return $discount_amount;
	}

	// Return the discount amount.
	return $discount_amount;
}
add_filter( 'edd_discounts_item_amount_loop', 'psupsellmaster_edd_discounts_item_amount_loop', 10, 3 );

/**
 * Filter the campaign-related coupon products in Easy Digital Downloads.
 *
 * @param array $products The products.
 * @param int   $coupon_id The coupon id.
 */
function psupsellmaster_edd_campaigns_get_discount_excluded_products( $products, $coupon_id ) {
	// Get the campaign id.
	$campaign_id = psupsellmaster_get_campaign_id_by_coupon_id( $coupon_id );

	// Check if the campaign id is empty.
	if ( empty( $campaign_id ) ) {
		// Return the products.
		return $products;
	}

	// Allow developers to filter this.
	$products = apply_filters( 'psupsellmaster_edd_campaigns_coupon_excluded_products', $products, $coupon_id );
	$products = apply_filters( 'psupsellmaster_campaigns_coupon_excluded_products', $products, $coupon_id );

	// Return the products.
	return $products;
}
add_filter( 'edd_get_discount_excluded_products', 'psupsellmaster_edd_campaigns_get_discount_excluded_products', 10, 2 );

/**
 * Filter the purchase link args in Easy Digital Downloads.
 *
 * @param array $args The arguments.
 * @return array The args.
 */
function psupsellmaster_campaigns_edd_purchase_link_args( $args ) {
	// Get the show price.
	$show_price = isset( $args['price'] ) ? filter_var( $args['price'], FILTER_VALIDATE_BOOLEAN ) : false;

	// Check if the show price is empty.
	if ( empty( $show_price ) ) {
		// Return the args.
		return $args;
	}

	// Get the product id.
	$product_id = isset( $args['download_id'] ) ? filter_var( $args['download_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		// Return the args.
		return $args;
	}

	// Get the download.
	$download = new EDD_Download( $product_id );

	// Check if the product was not found.
	if ( empty( $download->ID ) ) {
		// Return the args.
		return $args;
	}

	// Set the price amount.
	$price_amount = false;

	// Get the has variable prices.
	$has_variable_prices = $download->has_variable_prices();

	// Get the price id.
	$price_id = isset( $args['price_id'] ) ? filter_var( $args['price_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the product has variable prices.
	if ( $has_variable_prices && false !== $price_id ) {
		// Get the prices.
		$prices = $download->get_prices();

		// Get the price amount.
		$price_amount = isset( $prices[ $price_id ] ) && isset( $prices[ $price_id ]['amount'] ) ? $prices[ $price_id ]['amount'] : false;

		// Check if the product does not have variable prices.
	} elseif ( ! $has_variable_prices ) {
		// Get the price amount.
		$price_amount = $download->get_price();
	}

	// Set the price amount.
	$price_amount = filter_var( $price_amount, FILTER_VALIDATE_FLOAT );

	// Check if the price amount is empty.
	if ( empty( $price_amount ) ) {
		// Return the args.
		return $args;
	}

	// Get the eligible campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( array( 'products' => array( $product_id ) ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Return the args.
		return $args;
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
		// Return the args.
		return $args;
	}

	// Get the coupon amount.
	$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if the coupon amount is empty.
	if ( empty( $coupon_amount ) ) {
		// Return the args.
		return $args;
	}

	// Set the discount amount.
	$discount_amount = 0;

	// Check the coupon type.
	if ( 'discount_percent' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $price_amount * ( floatval( $coupon_amount ) / 100 ), true, edd_get_currency(), 'typed' );

		// Check the coupon type.
	} elseif ( 'discount_fixed' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $coupon_amount, true, edd_get_currency(), 'typed' );
	}

	// Check if the discount amount is lower than or equal to zero.
	if ( $discount_amount <= 0 ) {
		// Return the args.
		return $args;
	}

	// Set the discounted price.
	$discounted_price = $price_amount - $discount_amount;
	$discounted_price = $discounted_price > 0 ? $discounted_price : 0;

	// Get the button behavior.
	$button_behavior = edd_get_download_button_behavior( $product_id );

	// Get the button text.
	$button_text = 'direct' === $button_behavior ? edd_get_option( 'buy_now_text', __( 'Buy Now', 'psupsellmaster' ) ) : edd_get_option( 'add_to_cart_text', __( 'Purchase', 'psupsellmaster' ) );
	$button_text = ! empty( $button_text ) ? '&nbsp;&ndash;&nbsp;' . $button_text : '';

	// Set the price output.
	$price_output = edd_currency_filter( edd_format_amount( $discounted_price ) );

	// Set the price output.
	$price_output = $price_output . $button_text;

	// Set the text.
	$args['text'] = $price_output;

	// Return the args.
	return $args;
}
add_filter( 'edd_purchase_link_args', 'psupsellmaster_campaigns_edd_purchase_link_args' );

/**
 * Add a discount message in the purchase link in Easy Digital Downloads.
 *
 * @param int   $download_id The download id.
 * @param array $args The arguments.
 */
function psupsellmaster_campaigns_edd_purchase_link_end( $download_id, $args ) {
	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the discount text.
	$discount_text = isset( $settings['prices_discount_text'] ) ? $settings['prices_discount_text'] : '';

	// Check if the discount text is empty.
	if ( empty( $discount_text ) ) {
		return false;
	}

	// Get the download.
	$download = new EDD_Download( $download_id );

	// Check if the product was not found.
	if ( empty( $download->ID ) ) {
		return false;
	}

	// Get the eligible campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( array( 'products' => array( $download_id ) ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		return false;
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
		return false;
	}

	// Get the coupon amount.
	$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if the coupon amount is empty.
	if ( empty( $coupon_amount ) ) {
		return false;
	}

	// Set the price amount.
	$price_amount = false;

	// Get the has variable prices.
	$has_variable_prices = $download->has_variable_prices();

	// Get the price id.
	$price_id = isset( $args['price_id'] ) ? filter_var( $args['price_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the product has variable prices.
	if ( $has_variable_prices && false !== $price_id ) {
		// Get the prices.
		$prices = $download->get_prices();

		// Get the price amount.
		$price_amount = isset( $prices[ $price_id ] ) && isset( $prices[ $price_id ]['amount'] ) ? $prices[ $price_id ]['amount'] : false;

		// Check if the product does not have variable prices.
	} elseif ( ! $has_variable_prices ) {
		// Get the price amount.
		$price_amount = $download->get_price();
	}

	// Set the price amount.
	$price_amount = filter_var( $price_amount, FILTER_VALIDATE_FLOAT );

	// Check if the price amount is empty.
	if ( empty( $price_amount ) ) {
		return false;
	}

	// Set the discount amount.
	$discount_amount = 0;

	// Set the formatted discount.
	$formatted_discount = 0;

	// Check the coupon type.
	if ( 'discount_percent' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $price_amount * ( floatval( $coupon_amount ) / 100 ), true, edd_get_currency(), 'typed' );

		// Set the formatted discount.
		$formatted_discount = edd_format_amount( $coupon_amount ) . '%';

		// Check the coupon type.
	} elseif ( 'discount_fixed' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $coupon_amount, true, edd_get_currency(), 'typed' );

		// Set the formatted discount.
		$formatted_discount = edd_currency_filter( edd_format_amount( $coupon_amount ) );
	}

	// Check if the discount amount is lower than or equal to zero.
	if ( $discount_amount <= 0 ) {
		return false;
	}

	// Set the discounted price.
	$discounted_price = $price_amount - $discount_amount;
	$discounted_price = $discounted_price > 0 ? $discounted_price : 0;

	// Set the formatted price.
	$formatted_price_amount = edd_currency_filter( edd_format_amount( $price_amount ) );

	// Set the formatted discounted price.
	$formatted_discounted_price = edd_currency_filter( edd_format_amount( $discounted_price ) );

	// Set the output.
	$output = wp_kses_post( wpautop( stripslashes( $discount_text ) ) );
	$output = str_replace( '{old_price}', $formatted_price_amount, $output );
	$output = str_replace( '{new_price}', $formatted_discounted_price, $output );
	$output = str_replace( '{discount_amount}', $formatted_discount, $output );
	$output = '<div class="psupsellmaster-product-prices">' . $output . '</div>';

	// Echo the output.
	echo wp_kses_post( $output );
}
add_action( 'edd_purchase_link_end', 'psupsellmaster_campaigns_edd_purchase_link_end', 10, 2 );

/**
 * Filter the price option output in Easy Digital Downloads.
 *
 * @param string $price_output The price output.
 * @param int    $download_id The download id.
 * @param int    $key The key.
 * @param array  $price The price.
 * @return string The price output.
 */
function psupsellmaster_edd_price_option_output( $price_output, $download_id, $key, $price ) {
	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the discount text.
	$discount_text = isset( $settings['prices_discount_text'] ) ? $settings['prices_discount_text'] : '';

	// Check if the discount text is empty.
	if ( empty( $discount_text ) ) {
		// Return the price output.
		return $price_output;
	}

	// Get the price amount.
	$price_amount = isset( $price['amount'] ) ? filter_var( $price['amount'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if the price amount is empty.
	if ( empty( $price_amount ) ) {
		// Return the price output.
		return $price_output;
	}

	// Get the eligible campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( array( 'products' => array( $download_id ) ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Return the price output.
		return $price_output;
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
		// Return the price output.
		return $price_output;
	}

	// Get the coupon amount.
	$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if the coupon amount is empty.
	if ( empty( $coupon_amount ) ) {
		// Return the price output.
		return $price_output;
	}

	// Set the discount amount.
	$discount_amount = 0;

	// Set the formatted discount.
	$formatted_discount = 0;

	// Check the coupon type.
	if ( 'discount_percent' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $price_amount * ( floatval( $coupon_amount ) / 100 ), true, edd_get_currency(), 'typed' );

		// Set the formatted discount.
		$formatted_discount = edd_format_amount( $coupon_amount ) . '%';

		// Check the coupon type.
	} elseif ( 'discount_fixed' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $coupon_amount, true, edd_get_currency(), 'typed' );

		// Set the formatted discount.
		$formatted_discount = edd_currency_filter( edd_format_amount( $coupon_amount ) );
	}

	// Check if the discount amount is lower than or equal to zero.
	if ( $discount_amount <= 0 ) {
		// Return the price output.
		return $price_output;
	}

	// Set the discounted price.
	$discounted_price = $price_amount - $discount_amount;
	$discounted_price = $discounted_price > 0 ? $discounted_price : 0;

	// Set the formatted price.
	$formatted_price_amount = edd_currency_filter( edd_format_amount( $price_amount ) );

	// Set the formatted discounted price.
	$formatted_discounted_price = edd_currency_filter( edd_format_amount( $discounted_price ) );

	// Get the price name.
	$price_name = isset( $price['name'] ) ? $price['name'] : '';

	// Reset the price output.
	$price_output = '';

	// Set the price output.
	$price_output .= '<span class="edd_price_option_name">' . esc_html( $price_name ) . '</span>';
	$price_output .= '<span class="edd_price_option_sep">&nbsp;&ndash;&nbsp;</span>';
	$price_output .= $discount_text;

	// Set the price output.
	$price_output = str_replace( '{old_price}', $formatted_price_amount, $price_output );
	$price_output = str_replace( '{new_price}', $formatted_discounted_price, $price_output );
	$price_output = str_replace( '{discount_amount}', $formatted_discount, $price_output );
	$price_output = '<div class="psupsellmaster-product-prices psupsellmaster-product-price-options">' . $price_output . '</div>';

	// Return the price output.
	return $price_output;
}
add_filter( 'edd_price_option_output', 'psupsellmaster_edd_price_option_output', 10, 4 );

/**
 * Filter the price html in Easy Digital Downloads.
 *
 * @param string $formatted_price The formatted price.
 * @param int    $download_id The download id.
 * @param float  $price The price.
 * @param int    $price_id The price id.
 * @return string The formatted price.
 */
function psupsellmaster_edd_download_price_after_html( $formatted_price, $download_id, $price, $price_id ) {
	// Set the output.
	$output = $formatted_price;

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the discount text.
	$discount_text = isset( $settings['prices_discount_text'] ) ? $settings['prices_discount_text'] : '';

	// Check if the discount text is empty.
	if ( empty( $discount_text ) ) {
		// Return the output.
		return $output;
	}

	// Check if the download id is empty.
	if ( empty( $download_id ) ) {
		// Return the output.
		return $output;
	}

	// Get the download.
	$download = new EDD_Download( $download_id );

	// Check if the download was not found.
	if ( empty( $download->ID ) ) {
		// Return the output.
		return $output;
	}

	// Set the price amount.
	$price_amount = false;

	// Get the has variable prices.
	$has_variable_prices = $download->has_variable_prices();

	// Check if the product has variable prices.
	if ( $has_variable_prices && false !== $price_id ) {
		// Get the prices.
		$prices = $download->get_prices();

		// Get the price amount.
		$price_amount = isset( $prices[ $price_id ] ) && isset( $prices[ $price_id ]['amount'] ) ? $prices[ $price_id ]['amount'] : false;

		// Check if the product does not have variable prices.
	} elseif ( ! $has_variable_prices ) {
		// Get the price amount.
		$price_amount = $download->get_price();
	}

	// Set the price amount.
	$price_amount = filter_var( $price_amount, FILTER_VALIDATE_FLOAT );

	// Check if the price amount is empty.
	if ( empty( $price_amount ) ) {
		// Return the output.
		return $output;
	}

	// Get the eligible campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( array( 'products' => array( $download_id ) ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		// Return the output.
		return $output;
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
		// Return the output.
		return $output;
	}

	// Get the coupon amount.
	$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if the coupon amount is empty.
	if ( empty( $coupon_amount ) ) {
		// Return the output.
		return $output;
	}

	// Set the discount amount.
	$discount_amount = 0;

	// Set the formatted discount.
	$formatted_discount = 0;

	// Check the coupon type.
	if ( 'discount_percent' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $price_amount * ( floatval( $coupon_amount ) / 100 ), true, edd_get_currency(), 'typed' );

		// Set the formatted discount.
		$formatted_discount = edd_format_amount( $coupon_amount ) . '%';

		// Check the coupon type.
	} elseif ( 'discount_fixed' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $coupon_amount, true, edd_get_currency(), 'typed' );

		// Set the formatted discount.
		$formatted_discount = edd_currency_filter( edd_format_amount( $coupon_amount ) );
	}

	// Check if the discount amount is lower than or equal to zero.
	if ( $discount_amount <= 0 ) {
		// Return the output.
		return $output;
	}

	// Set the discounted price.
	$discounted_price = $price_amount - $discount_amount;
	$discounted_price = $discounted_price > 0 ? $discounted_price : 0;

	// Set the formatted price.
	$formatted_price_amount = edd_currency_filter( edd_format_amount( $price_amount ) );

	// Set the formatted discounted price.
	$formatted_discounted_price = edd_currency_filter( edd_format_amount( $discounted_price ) );

	// Set the output.
	$output = wp_kses_post( wpautop( stripslashes( $discount_text ) ) );
	$output = str_replace( '{old_price}', $formatted_price_amount, $output );
	$output = str_replace( '{new_price}', $formatted_discounted_price, $output );
	$output = str_replace( '{discount_amount}', $formatted_discount, $output );
	$output = '<div class="psupsellmaster-product-prices">' . $output . '</div>';

	// Return the output.
	return $output;
}
add_filter( 'edd_download_price_after_html', 'psupsellmaster_edd_download_price_after_html', 10, 4 );

/**
 * Filter the price html in Easy Digital Downloads.
 *
 * @param array $item The item.
 */
function psupsellmaster_edd_checkout_cart_item_price_after( $item ) {
	// Get the product id.
	$product_id = isset( $item['id'] ) ? filter_var( $item['id'], FILTER_VALIDATE_INT ) : false;

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		return;
	}

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'campaigns' );

	// Get the discount text.
	$discount_text = isset( $settings['page_checkout_discount_text'] ) ? $settings['page_checkout_discount_text'] : '';

	// Check if the discount text is empty.
	if ( empty( $discount_text ) ) {
		return;
	}

	// Get the download.
	$download = new EDD_Download( $product_id );

	// Check if the download was not found.
	if ( empty( $download->ID ) ) {
		return;
	}

	// Get the options.
	$options = isset( $item['options'] ) ? $item['options'] : array();

	// Get the price id.
	$price_id = isset( $options['price_id'] ) ? filter_var( $options['price_id'], FILTER_VALIDATE_INT ) : false;

	// Set the price amount.
	$price_amount = false;

	// Get the has variable prices.
	$has_variable_prices = $download->has_variable_prices();

	// Check if the product has variable prices.
	if ( $has_variable_prices && false !== $price_id ) {
		// Get the prices.
		$prices = $download->get_prices();

		// Get the price amount.
		$price_amount = isset( $prices[ $price_id ] ) && isset( $prices[ $price_id ]['amount'] ) ? $prices[ $price_id ]['amount'] : false;

		// Check if the product does not have variable prices.
	} elseif ( ! $has_variable_prices ) {
		// Get the price amount.
		$price_amount = $download->get_price();
	}

	// Set the price amount.
	$price_amount = filter_var( $price_amount, FILTER_VALIDATE_FLOAT );

	// Check if the price amount is empty.
	if ( empty( $price_amount ) ) {
		return;
	}

	// Get the eligible campaigns.
	$campaigns = psupsellmaster_get_eligible_campaigns_by_filters( array( 'products' => array( $product_id ) ) );

	// Check if the campaigns is empty.
	if ( empty( $campaigns ) ) {
		return;
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
		return;
	}

	// Get the coupon amount.
	$coupon_amount = isset( $coupon['amount'] ) ? filter_var( $coupon['amount'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if the coupon amount is empty.
	if ( empty( $coupon_amount ) ) {
		return;
	}

	// Set the discount amount.
	$discount_amount = 0;

	// Set the formatted discount.
	$formatted_discount = 0;

	// Check the coupon type.
	if ( 'discount_percent' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $price_amount * ( floatval( $coupon_amount ) / 100 ), true, edd_get_currency(), 'typed' );

		// Set the formatted discount.
		$formatted_discount = edd_format_amount( $coupon_amount ) . '%';

		// Check the coupon type.
	} elseif ( 'discount_fixed' === $coupon_type ) {
		// Set the discount amount.
		$discount_amount = edd_format_amount( $coupon_amount, true, edd_get_currency(), 'typed' );

		// Set the formatted discount.
		$formatted_discount = edd_currency_filter( edd_format_amount( $coupon_amount ) );
	}

	// Check if the discount amount is lower than or equal to zero.
	if ( $discount_amount <= 0 ) {
		return;
	}

	// Set the discounted price.
	$discounted_price = $price_amount - $discount_amount;
	$discounted_price = $discounted_price > 0 ? $discounted_price : 0;

	// Set the formatted price.
	$formatted_price_amount = edd_currency_filter( edd_format_amount( $price_amount ) );

	// Set the formatted discounted price.
	$formatted_discounted_price = edd_currency_filter( edd_format_amount( $discounted_price ) );

	// Set the output.
	$output = wp_kses_post( wpautop( stripslashes( $discount_text ) ) );
	$output = str_replace( '{old_price}', $formatted_price_amount, $output );
	$output = str_replace( '{new_price}', $formatted_discounted_price, $output );
	$output = str_replace( '{discount_amount}', $formatted_discount, $output );
	$output = '<div>' . $output . '</div>';

	// Echo the output.
	echo wp_kses_post( $output );
}
add_action( 'edd_checkout_cart_item_price_after', 'psupsellmaster_edd_checkout_cart_item_price_after' );
