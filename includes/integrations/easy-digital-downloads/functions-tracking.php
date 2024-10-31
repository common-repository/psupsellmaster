<?php
/**
 * Integrations - Easy Digital Downloads - Functions - Tracking.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run when a product is added to the cart in Easy Digital Downloads.
 *
 * @param int   $download_id The product id.
 * @param array $options The product options.
 */
function psupsellmaster_edd_post_add_to_cart( $download_id, $options ) {
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
		return;
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
		'product_id' => $download_id,
		'type'       => 'direct',
		'visitor_id' => $visitor_id,
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

	// Get the prices.
	$prices = isset( $options['price_id'] ) ? $options['price_id'] : 0;
	$prices = is_array( $prices ) ? $prices : array( $prices );

	// Loop through the prices.
	foreach ( $prices as $price_id ) {
		// Set the variation id.
		$insert_data['variation_id'] = $price_id;

		// Insert a new interest into the database.
		psupsellmaster_db_interests_insert( $insert_data );
	}
}
add_action( 'edd_post_add_to_cart', 'psupsellmaster_edd_post_add_to_cart', 10, 2 );

/**
 * Run when an order is built in Easy Digital Downloads.
 *
 * @param int $order_id The order id.
 */
function psupsellmaster_edd_built_order( $order_id ) {
	// Set the update data.
	$update_data = array( 'order_id' => $order_id );

	// Set the update where.
	$update_where = array( 'visitor_id' => psupsellmaster_get_current_visitor_id() );

	// Update an existing interest in the database.
	psupsellmaster_db_interests_update( $update_data, $update_where );
}
add_action( 'edd_built_order', 'psupsellmaster_edd_built_order', 10, 1 );

/**
 * Run when the order status changes in Easy Digital Downloads.
 *
 * @param string $old_status The old status.
 * @param string $new_status The new status.
 * @param int    $order_id The order id.
 */
function psupsellmaster_edd_transition_order_status( $old_status, $new_status, $order_id ) {
	// Check if the order id is empty.
	if ( empty( $order_id ) ) {
		return false;
	}

	// Check if the new status is not valid.
	if ( 'complete' !== $new_status ) {
		return false;
	}

	// Get the order.
	$order = edd_get_order( $order_id );

	// Check if the order is empty.
	if ( empty( $order ) ) {
		return false;
	}

	// Get the order type.
	$order_type = ! empty( $order->type ) ? $order->type : false;

	// Check if the order type is not valid.
	if ( 'sale' !== $order_type ) {
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
	$customer_id = isset( $order->customer_id ) ? filter_var( $order->customer_id, FILTER_VALIDATE_INT ) : false;
	$customer_id = ! empty( $customer_id ) ? $customer_id : 0;

	// Set the order items arguments.
	$edd_get_order_items_args = array(
		'fields'   => array( 'amount', 'id', 'price_id', 'product_id' ),
		'number'   => 0,
		'order'    => 'ASC',
		'orderby'  => 'id',
		'order_id' => $order_id,
	);

	// Get the order items.
	$order_items = edd_get_order_items( $edd_get_order_items_args );

	// Check if the order items is empty.
	if ( empty( $order_items ) ) {
		return false;
	}

	// Run actions before inserting tracking results.
	do_action( 'psupsellmaster_tracking_insert_results_before' );

	// Loop through the order items.
	foreach ( $order_items as $order_item ) {
		// Get the item id.
		$item_id = isset( $order_item->id ) ? filter_var( $order_item->id, FILTER_VALIDATE_INT ) : false;
		$item_id = ! empty( $item_id ) ? $item_id : 0;

		// Check if the item id is empty.
		if ( empty( $item_id ) ) {
			continue;
		}

		// Get the amount.
		$amount = isset( $order_item->amount ) ? filter_var( $order_item->amount, FILTER_VALIDATE_FLOAT ) : false;
		$amount = ! empty( $amount ) ? $amount : 0.00;

		// Check if the amount is empty.
		if ( empty( $amount ) ) {
			continue;
		}

		// Get the product id.
		$product_id = isset( $order_item->product_id ) ? filter_var( $order_item->product_id, FILTER_VALIDATE_INT ) : false;
		$product_id = ! empty( $product_id ) ? $product_id : 0;

		// Check if the product is empty.
		if ( empty( $product_id ) ) {
			continue;
		}

		// Get the price id.
		$price_id = isset( $order_item->price_id ) ? filter_var( $order_item->price_id, FILTER_VALIDATE_INT ) : false;
		$price_id = ! empty( $price_id ) ? $price_id : 0;

		// Set the where data.
		$where_data = array(
			'product_id'   => $product_id,
			'variation_id' => $price_id,
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

		// Get the interest id.
		$interest_id = filter_var( $interest->id, FILTER_VALIDATE_INT );
		$interest_id = ! empty( $interest_id ) ? $interest_id : 0;

		// Get the variation id.
		$variation_id = filter_var( $interest->variation_id, FILTER_VALIDATE_INT );
		$variation_id = ! empty( $variation_id ) ? $variation_id : 0;

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
		$variation_id = 'direct' === $type ? $variation_id : $price_id;

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
add_action( 'edd_transition_order_status', 'psupsellmaster_edd_transition_order_status', 10, 3 );
