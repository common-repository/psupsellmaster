<?php
/**
 * Integrations - WooCommerce - Functions - Tracking.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run when a product is added to the cart in WooCommerce.
 *
 * @param string $cart_item_key The cart item key.
 * @param int    $product_id The product id.
 * @param int    $quantity The quantity.
 * @param int    $variation_id The variation id.
 */
function psupsellmaster_woo_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id ) {
	// Check if the nonce is not set.
	if ( ! isset( $_POST['psupsellmaster'] ) || ! isset( $_POST['psupsellmaster_nonce'] ) ) {
		return;
	}

	// Get the nonce.
	$nonce = sanitize_text_field( wp_unslash( $_POST['psupsellmaster_nonce'] ) );

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

	// Set the visitor id.
	$visitor_id = 0;

	// Get the visitor.
	$visitor = psupsellmaster_get_current_visitor();

	// Check if the visitor is not empty.
	if ( ! empty( $visitor ) ) {
		// Sanitize the visitor id.
		$visitor_id = ! empty( $visitor->id ) ? $visitor->id : 0;
	}

	// Set the insert data.
	$insert_data = array(
		'product_id'   => $product_id,
		'type'         => 'direct',
		'variation_id' => $variation_id,
		'visitor_id'   => $visitor_id,
	);

	// Check if the campaign id is not empty.
	if ( ! empty( $args['campaign_id'] ) ) {
		// Set the campaign id.
		$insert_data['campaign_id'] = $args['campaign_id'];
	}

	// Check if the base product id is not empty.
	if ( ! empty( $args['base_product_id'] ) ) {
		// Set the base product id.
		$insert_data['base_product_id'] = $args['base_product_id'];
	}

	// Check if the location is not empty.
	if ( ! empty( $args['location'] ) ) {
		// Set the location.
		$insert_data['location'] = $args['location'];
	}

	// Check if the source is not empty.
	if ( ! empty( $args['source'] ) ) {
		// Set the source.
		$insert_data['source'] = $args['source'];
	}

	// Check if the view is not empty.
	if ( ! empty( $args['view'] ) ) {
		// Set the view.
		$insert_data['view'] = $args['view'];
	}

	// Insert a new interest into the database.
	psupsellmaster_db_interests_insert( $insert_data );
}
add_action( 'woocommerce_add_to_cart', 'psupsellmaster_woo_add_to_cart', 30, 4 );

/**
 * Run when the order status is changed to completed in WooCommerce.
 *
 * @param int $order_id The order id.
 */
function psupsellmaster_woo_order_status_completed( $order_id ) {
	// Check if the order id is empty.
	if ( empty( $order_id ) ) {
		return false;
	}

	// Get the order.
	$order = wc_get_order( $order_id );

	// Check if the order is empty.
	if ( empty( $order ) ) {
		return false;
	}

	// Get the order type.
	$type = $order->get_type();

	// Check if the order type is not valid.
	if ( 'shop_order' !== $type ) {
		return false;
	}

	// Set the where data.
	$where_data = array(
		'order_id' => $order_id,
	);

	// Get the interests.
	$interests = psupsellmaster_db_interests_select( $where_data );

	// Check if the interests is empty.
	if ( empty( $interests ) ) {
		return false;
	}

	// Get the store.
	$store = psupsellmaster_is_plugin_active( 'woo' ) ? 'woo' : '';

	// Check if the store is empty.
	if ( empty( $store ) ) {
		// Get the store.
		$store = psupsellmaster_is_plugin_active( 'edd' ) ? 'edd' : '';
	}

	// Get the customer id.
	$customer_id = filter_var( $order->get_customer_id(), FILTER_VALIDATE_INT );
	$customer_id = ! empty( $customer_id ) ? $customer_id : 0;

	// Get the order items.
	$items = $order->get_items();

	// Run actions before inserting tracking results.
	do_action( 'psupsellmaster_tracking_insert_results_before' );

	// Loop through the order items.
	foreach ( $items as $item_id => $item ) {

		// Check if the item id is empty.
		if ( empty( $item_id ) ) {
			continue;
		}

		// Get the product id.
		$product_id = filter_var( $item->get_product_id(), FILTER_VALIDATE_INT );
		$product_id = ! empty( $product_id ) ? $product_id : 0;

		// Check if the product id is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Get the amount.
		$amount = filter_var( $item->get_total(), FILTER_VALIDATE_FLOAT );
		$amount = ! empty( $amount ) ? $amount : 0.00;

		// Check if the amount is empty.
		if ( empty( $amount ) ) {
			continue;
		}

		// Get the variation id.
		$variation_id = filter_var( $item->get_variation_id(), FILTER_VALIDATE_INT );
		$variation_id = ! empty( $variation_id ) ? $variation_id : 0;

		// Set the where data.
		$where_data = array(
			'product_id'   => $product_id,
			'variation_id' => $variation_id,
			'order_id'     => $order_id,
			'type'         => 'direct',
		);

		// Set the orderby data.
		$orderby_data = array( 'created_at' => 'DESC' );

		// Get the interests.
		$interests = psupsellmaster_db_interests_select( $where_data, $orderby_data );

		// Set the interest.
		$interest = false;

		// Check if the interests is an array.
		if ( is_array( $interests ) ) {
			// Set the interest.
			$interest = array_pop( $interests );
		}

		// Check if the interest is empty.
		if ( empty( $interest ) ) {
			// Set the where data.
			$where_data['type'] = 'indirect';

			// Unset the variation id (there is no variation when the type is indirect).
			unset( $where_data['variation_id'] );

			// Get the interests.
			$interests = psupsellmaster_db_interests_select( $where_data, $orderby_data );

			// Set the interest.
			$interest = false;

			// Check if the interests is an array.
			if ( is_array( $interests ) ) {
				// Set the interest.
				$interest = array_pop( $interests );
			}
		}

		// Check if the interest is empty.
		if ( empty( $interest ) ) {
			continue;
		}

		// Get the base product id.
		$base_product_id = filter_var( $interest->base_product_id, FILTER_VALIDATE_INT );
		$base_product_id = ! empty( $base_product_id ) ? $base_product_id : 0;

		// Get the campaign id.
		$campaign_id = filter_var( $interest->campaign_id, FILTER_VALIDATE_INT );
		$campaign_id = ! empty( $campaign_id ) ? $campaign_id : 0;

		// Get the location.
		$location = ! empty( $interest->location ) ? $interest->location : '';

		// Get the source.
		$source = ! empty( $interest->source ) ? $interest->source : '';

		// Get the type.
		$type = ! empty( $interest->type ) ? $interest->type : '';

		// Get the view.
		$view = ! empty( $interest->view ) ? $interest->view : '';

		// Set the variation id.
		$variation_id = 'direct' === $type ? filter_var( $interest->variation_id, FILTER_VALIDATE_INT ) : $variation_id;
		$variation_id = ! empty( $variation_id ) ? $variation_id : 0;

		// Set the insert data.
		$insert_data = array(
			'order_id'        => $order_id,
			'order_item_id'   => $item_id,
			'customer_id'     => $customer_id,
			'product_id'      => $product_id,
			'variation_id'    => $variation_id,
			'base_product_id' => $base_product_id,
			'campaign_id'     => $campaign_id,
			'amount'          => $amount,
			'location'        => $location,
			'source'          => $source,
			'type'            => $type,
			'view'            => $view,
			'store'           => $store,
		);

		// Insert a new result into the database.
		psupsellmaster_db_results_insert( $insert_data );
	}

	// Run actions after inserting tracking results.
	do_action( 'psupsellmaster_tracking_insert_results_after' );

	// Set the delete where.
	$delete_where = array( 'order_id' => $order_id );

	// Delete interests from the database.
	psupsellmaster_db_interests_delete( $delete_where );
}
add_action( 'woocommerce_order_status_completed', 'psupsellmaster_woo_order_status_completed' );

/**
 * Run when an order is created in WooCommerce.
 *
 * @param int $order_id The order ID.
 */
function psupsellmaster_woo_new_order( $order_id ) {
	// Set the update data.
	$update_data = array( 'order_id' => $order_id );

	// Set the update where.
	$update_where = array( 'visitor_id' => psupsellmaster_get_current_visitor_id() );

	// Update an existing interest in the database.
	psupsellmaster_db_interests_update( $update_data, $update_where );
}
add_action( 'woocommerce_new_order', 'psupsellmaster_woo_new_order' );
