<?php
/**
 * Functions - Background Process - Analytics - Orders.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This function (maybe) starts the background process.
 * It will only start if the process is not already running.
 *
 * @param array $args The arguments.
 * @return boolean whether the process was started or not.
 */
function psupsellmaster_bp_analytics_orders_maybe_start( $args = array() ) {
	// Set the started.
	$started = false;

	// Check if the queue is not empty.
	if ( ! PsUpsellMaster::$background['analytics_orders']->is_queue_empty() ) {
		// Return the started.
		return $started;
	}

	// Run some procedures before all batches.
	psupsellmaster_bp_analytics_orders_run_before_batches();

	// Check if the tracking is true.
	if ( isset( $args['tracking'] ) && true === $args['tracking'] ) {
		// Setup the tracking.
		psupsellmaster_bp_analytics_orders_setup_tracking();
	}

	// Set the data.
	$data = array( 'status' => 'starting' );

	// Push to the queue.
	PsUpsellMaster::$background['analytics_orders']->push_to_queue( $data );

	// Save and dispatch.
	PsUpsellMaster::$background['analytics_orders']->save()->dispatch();

	// Set the started.
	$started = true;

	// Return the started.
	return $started;
}

/**
 * This function (maybe) stops the background process.
 * It will only stop if the process is running.
 *
 * @return boolean whether the process was stopped or not.
 */
function psupsellmaster_bp_analytics_orders_maybe_stop() {
	// Set the stopped.
	$stopped = false;

	// Check if the queue is empty.
	if ( PsUpsellMaster::$background['analytics_orders']->is_queue_empty() ) {
		// Return the stopped.
		return $stopped;
	}

	// Set the transient name.
	$transient_name = 'psupsellmaster_bp_analytics_orders_stop';

	// Set the transient.
	psupsellmaster_bp_set_transient( $transient_name, true );

	// Kill the process.
	PsUpsellMaster::$background['analytics_orders']->kill_process();

	// Set the stopped.
	$stopped = true;

	// Return the stopped.
	return $stopped;
}

/**
 * This function runs before all batches, but after the process is started.
 */
function psupsellmaster_bp_analytics_orders_run_before_batches() {
	// Truncate the analytics orders database table.
	psupsellmaster_db_analytics_orders_truncate();

	// Delete the transients.
	psupsellmaster_bp_analytics_orders_delete_transients();

	// Update the status.
	psupsellmaster_bp_analytics_orders_update_status( array( 'action' => 'insert' ) );
}

/**
 * This function runs after all batches, but before the process is finished.
 */
function psupsellmaster_bp_analytics_orders_run_after_batches() {
	// Set the lan run.
	$last_run = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
	$last_run = $last_run->getTimestamp();

	// Update the last run date.
	update_option( 'psupsellmaster_bp_analytics_orders_last_run', $last_run );

	// Delete the queue lock.
	psupsellmaster_bp_delete_queue_lock();

	// Maybe delete the transients.
	psupsellmaster_bp_analytics_orders_maybe_delete_transients();

	// Maybe start the queue.
	psupsellmaster_bp_maybe_run_queue();
}
add_action( 'psupsellmaster_bp_analytics_orders_complete', 'psupsellmaster_bp_analytics_orders_run_after_batches' );

/**
 * Setup the tracking.
 */
function psupsellmaster_bp_analytics_orders_setup_tracking() {
	// Set the transient.
	psupsellmaster_bp_set_transient( 'psupsellmaster_bp_analytics_orders_tracking', true );
}

/**
 * This function runs procedures after trying to kill the background process.
 */
function psupsellmaster_bp_analytics_orders_kill_process() {
	// Truncate the analytics orders database table.
	psupsellmaster_db_analytics_orders_truncate();

	// Delete the queue lock.
	psupsellmaster_bp_delete_queue_lock();
}
add_action( 'psupsellmaster_bp_analytics_orders_kill_process', 'psupsellmaster_bp_analytics_orders_kill_process' );

/**
 * This function deletes the transients of the background process.
 */
function psupsellmaster_bp_analytics_orders_delete_transients() {
	// Delete the stop transient.
	delete_transient( 'psupsellmaster_bp_analytics_orders_stop' );

	// Delete the status transient.
	delete_transient( 'psupsellmaster_bp_analytics_orders_status' );

	// Delete the track transient.
	delete_transient( 'psupsellmaster_bp_analytics_orders_tracking' );
}

/**
 * This function maybe deletes the transients of the background process.
 */
function psupsellmaster_bp_analytics_orders_maybe_delete_transients() {
	// Get the track transient.
	$track = get_transient( 'psupsellmaster_bp_analytics_orders_tracking' );
	$track = filter_var( $track, FILTER_VALIDATE_BOOLEAN );

	// Check if the track transient is empty.
	if ( empty( $track ) ) {
		// Delete the transients.
		psupsellmaster_bp_analytics_orders_delete_transients();
	}
}

/**
 * This function runs for each batch.
 *
 * @return int the number of items processed.
 */
function psupsellmaster_bp_analytics_orders_run_batch() {
	// Get the limit.
	$limit = PsUpsellMaster_Settings::get( 'limit' );
	$limit = filter_var( $limit, FILTER_VALIDATE_INT );
	$limit = false !== $limit ? $limit : 100;

	// Get the sql args.
	$sql_args = psupsellmaster_bp_analytics_orders_get_sql_args();

	// Get the sql from.
	$sql_from = isset( $sql_args['sql_from'] ) ? $sql_args['sql_from'] : '';

	// Get the sql join.
	$sql_join = isset( $sql_args['sql_join'] ) ? $sql_args['sql_join'] : '';

	// Get the sql where.
	$sql_where = isset( $sql_args['sql_where'] ) ? $sql_args['sql_where'] : '';

	// Get the sql groupby.
	$sql_groupby = isset( $sql_args['sql_groupby'] ) ? $sql_args['sql_groupby'] : '';

	// Build the sql limit.
	$sql_limit = "LIMIT {$limit}";

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Build the sql select.
		$sql_select   = array();
		$sql_select[] = '`woimp`.`meta_value` AS `order_product_id`';
		$sql_select[] = '`relatedmp`.`meta_value` AS `related_product_id`';
		$sql_select[] = 'AVG( `relatedmt`.`meta_value` ) AS `related_average_amount`';
		$sql_select[] = 'SUM( `relatedmt`.`meta_value` ) AS `related_total_amount`';
		$sql_select[] = 'COUNT(*) AS `related_total_sales`';
		$sql_select   = implode( ', ', $sql_select );
		$sql_select   = "SELECT {$sql_select}";

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Build the sql select.
		$sql_select   = array();
		$sql_select[] = '`eoi`.`product_id` AS `order_product_id`';
		$sql_select[] = '`related`.`product_id` AS `related_product_id`';
		$sql_select[] = 'AVG( `related`.`amount` ) AS `related_average_amount`';
		$sql_select[] = 'SUM( `related`.`amount` ) AS `related_total_amount`';
		$sql_select[] = 'COUNT(*) AS `related_total_sales`';
		$sql_select   = implode( ', ', $sql_select );
		$sql_select   = "SELECT {$sql_select}";
	}

	// Build the sql query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_join} {$sql_where} {$sql_groupby} {$sql_limit}";

	// Set the count.
	$count = 0;

	// Get the results.
	$results = PsUpsellMaster_Database::get_results( $sql_query );

	// Loop through the results.
	foreach ( $results as $result ) {
		// Set the order product id.
		$order_product_id = isset( $result->order_product_id ) ? filter_var( $result->order_product_id, FILTER_VALIDATE_INT ) : false;
		$order_product_id = ! empty( $order_product_id ) ? $order_product_id : 0;

		// Set the related product id.
		$related_product_id = isset( $result->related_product_id ) ? filter_var( $result->related_product_id, FILTER_VALIDATE_INT ) : false;
		$related_product_id = ! empty( $related_product_id ) ? $related_product_id : 0;

		// Set the average amount.
		$average_amount = isset( $result->related_average_amount ) ? filter_var( $result->related_average_amount, FILTER_VALIDATE_FLOAT ) : false;
		$average_amount = ! empty( $average_amount ) ? $average_amount : 0;

		// Set the total amount.
		$total_amount = isset( $result->related_total_amount ) ? filter_var( $result->related_total_amount, FILTER_VALIDATE_FLOAT ) : false;
		$total_amount = ! empty( $total_amount ) ? $total_amount : 0;

		// Set the total sales.
		$total_sales = isset( $result->related_total_sales ) ? filter_var( $result->related_total_sales, FILTER_VALIDATE_INT ) : false;
		$total_sales = ! empty( $total_sales ) ? $total_sales : 0;

		// Set the args.
		$args = array(
			'order_product_id'   => $order_product_id,
			'related_product_id' => $related_product_id,
			'average_amount'     => $average_amount,
			'total_amount'       => $total_amount,
			'total_sales'        => $total_sales,
		);

		// Run for a single item.
		psupsellmaster_bp_analytics_orders_run_single( $args );

		// Increment the count.
		++$count;
	}

	// Update the status.
	psupsellmaster_bp_analytics_orders_update_status();

	// Return the count.
	return $count;
}

/**
 * This function runs for a single item.
 *
 * @param array $args the arguments.
 */
function psupsellmaster_bp_analytics_orders_run_single( $args = array() ) {
	// Get the order product id.
	$order_product_id = isset( $args['order_product_id'] ) ? $args['order_product_id'] : 0;

	// Get the related product id.
	$related_product_id = isset( $args['related_product_id'] ) ? $args['related_product_id'] : 0;

	// Get the average amount.
	$average_amount = isset( $args['average_amount'] ) ? $args['average_amount'] : 0;

	// Get the total amount.
	$total_amount = isset( $args['total_amount'] ) ? $args['total_amount'] : 0;

	// Get the total sales.
	$total_sales = isset( $args['total_sales'] ) ? $args['total_sales'] : 0;

	// Set the insert data.
	$insert_data = array(
		'order_product_id'   => $order_product_id,
		'related_product_id' => $related_product_id,
		'average_amount'     => $average_amount,
		'total_amount'       => $total_amount,
		'total_sales'        => $total_sales,
	);

	// Insert a new analytics order record into the database.
	psupsellmaster_db_analytics_orders_insert( $insert_data );
}

/**
 * This function returns the sql query args for the background process.
 */
function psupsellmaster_bp_analytics_orders_get_sql_args() {
	// Set the sql select.
	$sql_select = '';

	// Set the sql from.
	$sql_from = '';

	// Set the sql join.
	$sql_join = '';

	// Set the sql where.
	$sql_where = '';

	// Set the sql groupby.
	$sql_groupby = '';

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Build the sql select.
		$sql_select = 'SELECT *';

		// Build the sql from.
		$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `woi`', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_items' ) );

		// Build the sql join.
		$sql_join   = array();
		$sql_join[] = PsUpsellMaster_Database::prepare( 'INNER JOIN %i AS `woimp`', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_itemmeta' ) );
		$sql_join[] = 'ON `woimp`.`order_item_id` = `woi`.`order_item_id`';
		$sql_join[] = PsUpsellMaster_Database::prepare( 'INNER JOIN %i AS `woima`', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_itemmeta' ) );
		$sql_join[] = 'ON `woima`.`order_item_id` = `woi`.`order_item_id`';
		$sql_join[] = PsUpsellMaster_Database::prepare( 'INNER JOIN %i AS `related`', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_items' ) );
		$sql_join[] = 'ON `related`.`order_id` = `woi`.`order_id`';
		$sql_join[] = PsUpsellMaster_Database::prepare( 'INNER JOIN %i AS `relatedmp`', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_itemmeta' ) );
		$sql_join[] = 'ON `relatedmp`.`order_item_id` = `related`.`order_item_id`';
		$sql_join[] = PsUpsellMaster_Database::prepare( 'INNER JOIN %i AS `relatedmt`', PsUpsellMaster_Database::get_table_name( 'woocommerce_order_itemmeta' ) );
		$sql_join[] = 'ON `relatedmt`.`order_item_id` = `related`.`order_item_id`';
		$sql_join   = implode( ' ', $sql_join );

		// Build the sql where.
		$sql_where   = array();
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `woi`.`order_item_type` = %s', 'line_item' );
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `woimp`.`meta_key` = %s', '_product_id' );
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `woima`.`meta_key` = %s', '_line_total' );
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `related`.`order_item_type` = %s', 'line_item' );
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `relatedmp`.`meta_key` = %s', '_product_id' );
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `relatedmt`.`meta_key` = %s', '_line_total' );
		$sql_where[] = 'AND `woimp`.`meta_value` <> `relatedmp`.`meta_value`';
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `woima`.`meta_value` > %d', 0 );
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `relatedmt`.`meta_value` > %d', 0 );

		// Build the sub sql where.
		$sub_sql_where   = array();
		$sub_sql_where[] = 'AND `a`.`order_product_id` = `woimp`.`meta_value`';
		$sub_sql_where[] = 'AND `a`.`related_product_id` = `relatedmp`.`meta_value`';
		$sub_sql_where   = implode( ' ', $sub_sql_where );
		$sub_sql_where   = "WHERE 1 = 1 {$sub_sql_where}";

		// Build the sql where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND NOT EXISTS ( SELECT 1 FROM %i AS `a` {$sub_sql_where} )", PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_orders' ) );

		// Build the sub sql where.
		$sub_sql_where   = array();
		$sub_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `p`.`post_type` = %s', $product_post_type );
		$sub_sql_where[] = 'AND `p`.`ID` = `woimp`.`meta_value`';
		$sub_sql_where   = implode( ' ', $sub_sql_where );
		$sub_sql_where   = "WHERE 1 = 1 {$sub_sql_where}";

		// Build the sql where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND EXISTS ( SELECT 1 FROM %i AS `p` {$sub_sql_where} )", PsUpsellMaster_Database::get_table_name( 'posts' ) );

		// Build the sub sql where.
		$sub_sql_where   = array();
		$sub_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `p`.`post_type` = %s', $product_post_type );
		$sub_sql_where[] = 'AND `p`.`ID` = `relatedmp`.`meta_value`';
		$sub_sql_where   = implode( ' ', $sub_sql_where );
		$sub_sql_where   = "WHERE 1 = 1 {$sub_sql_where}";

		// Build the sql where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND EXISTS ( SELECT 1 FROM %i AS `p` {$sub_sql_where} )", PsUpsellMaster_Database::get_table_name( 'posts' ) );

		// Build the sql where.
		$sql_where = implode( ' ', $sql_where );
		$sql_where = "WHERE 1 = 1 {$sql_where}";

		// Build the sql groupby.
		$sql_groupby   = array();
		$sql_groupby[] = '`woimp`.`meta_value`';
		$sql_groupby[] = '`relatedmp`.`meta_value`';
		$sql_groupby   = implode( ', ', $sql_groupby );
		$sql_groupby   = "GROUP BY {$sql_groupby}";

		// Otherwise, check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Build the sql select.
		$sql_select = 'SELECT *';

		// Build the sql from.
		$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `eoi`', PsUpsellMaster_Database::get_table_name( 'edd_order_items' ) );

		// Build the sql join.
		$sql_join   = array();
		$sql_join[] = PsUpsellMaster_Database::prepare( 'INNER JOIN %i AS `related`', PsUpsellMaster_Database::get_table_name( 'edd_order_items' ) );
		$sql_join[] = 'ON `related`.`order_id` = `eoi`.`order_id`';
		$sql_join   = implode( ' ', $sql_join );

		// Build the sql where.
		$sql_where   = array();
		$sql_where[] = 'AND `eoi`.`product_id` <> `related`.`product_id`';
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `eoi`.`amount` > %d', 0 );
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND `related`.`amount` > %d', 0 );

		// Build the sub sql where.
		$sub_sql_where   = array();
		$sub_sql_where[] = 'AND `a`.`order_product_id` = `eoi`.`product_id`';
		$sub_sql_where[] = 'AND `a`.`related_product_id` = `related`.`product_id`';
		$sub_sql_where   = implode( ' ', $sub_sql_where );
		$sub_sql_where   = "WHERE 1 = 1 {$sub_sql_where}";

		// Build the sql where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND NOT EXISTS ( SELECT 1 FROM %i AS `a` {$sub_sql_where} )", PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_orders' ) );

		// Build the sub sql where.
		$sub_sql_where   = array();
		$sub_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `p`.`post_type` = %s', $product_post_type );
		$sub_sql_where[] = 'AND `p`.`ID` = `eoi`.`product_id`';
		$sub_sql_where   = implode( ' ', $sub_sql_where );
		$sub_sql_where   = "WHERE 1 = 1 {$sub_sql_where}";

		// Build the sql where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND EXISTS ( SELECT 1 FROM %i AS `p` {$sub_sql_where} )", PsUpsellMaster_Database::get_table_name( 'posts' ) );

		// Build the sub sql where.
		$sub_sql_where   = array();
		$sub_sql_where[] = PsUpsellMaster_Database::prepare( 'AND `p`.`post_type` = %s', $product_post_type );
		$sub_sql_where[] = 'AND `p`.`ID` = `related`.`product_id`';
		$sub_sql_where   = implode( ' ', $sub_sql_where );
		$sub_sql_where   = "WHERE 1 = 1 {$sub_sql_where}";

		// Build the sql where.
		$sql_where[] = PsUpsellMaster_Database::prepare( "AND EXISTS ( SELECT 1 FROM %i AS `p` {$sub_sql_where} )", PsUpsellMaster_Database::get_table_name( 'posts' ) );

		// Build the sql where.
		$sql_where = implode( ' ', $sql_where );
		$sql_where = "WHERE 1 = 1 {$sql_where}";

		// Build the sql groupby.
		$sql_groupby   = array();
		$sql_groupby[] = '`eoi`.`product_id`';
		$sql_groupby[] = '`related`.`product_id`';
		$sql_groupby   = implode( ', ', $sql_groupby );
		$sql_groupby   = "GROUP BY {$sql_groupby}";
	}

	// Build the sql args.
	$sql_args = array(
		'sql_select'  => $sql_select,
		'sql_from'    => $sql_from,
		'sql_join'    => $sql_join,
		'sql_where'   => $sql_where,
		'sql_groupby' => $sql_groupby,
	);

	// Return the sql args.
	return $sql_args;
}

/**
 * This function gets the count of the remaining products to process.
 *
 * @return int the count.
 */
function psupsellmaster_bp_analytics_orders_get_count() {
	// Get the sql args.
	$sql_args = psupsellmaster_bp_analytics_orders_get_sql_args();

	// Build the sql select.
	$sql_select = 'SELECT COUNT( `count`.`pending` ) AS `count_pending`';

	// Get the sql from.
	$sql_from = isset( $sql_args['sql_from'] ) ? $sql_args['sql_from'] : '';

	// Get the sql join.
	$sql_join = isset( $sql_args['sql_join'] ) ? $sql_args['sql_join'] : '';

	// Get the sql where.
	$sql_where = isset( $sql_args['sql_where'] ) ? $sql_args['sql_where'] : '';

	// Get the sql groupby.
	$sql_groupby = isset( $sql_args['sql_groupby'] ) ? $sql_args['sql_groupby'] : '';

	// Set the sql sub query.
	$sql_sub_query = "SELECT 1 AS `pending` {$sql_from} {$sql_join} {$sql_where} {$sql_groupby}";

	// Set the sql query.
	$sql_query = "{$sql_select} FROM ( {$sql_sub_query } ) AS `count`";

	// Get the count.
	$count = PsUpsellMaster_Database::get_var( $sql_query );
	$count = filter_var( $count, FILTER_VALIDATE_INT );
	$count = false !== $count ? $count : 0;

	// Return the count.
	return $count;
}

/**
 * This function updates the status data for the background process.
 *
 * @param array $args the arguments.
 */
function psupsellmaster_bp_analytics_orders_update_status( $args = array() ) {
	// Get the action.
	$action = isset( $args['action'] ) ? $args['action'] : false;
	$action = in_array( $action, array( 'insert', 'update' ), true ) ? $action : 'update';

	// Get the count.
	$count = psupsellmaster_bp_analytics_orders_get_count();

	// Set the transient name.
	$transient_name = 'psupsellmaster_bp_analytics_orders_status';

	// Get the data.
	$data = get_transient( $transient_name );
	$data = is_array( $data ) ? $data : array();

	// Check if the action is insert.
	if ( 'insert' === $action ) {
		// Set the total.
		$data['total'] = $count;
	}

	// Set the pending.
	$data['pending'] = $count;

	// Set the status.
	$data['status'] = 0 === $count ? 'done' : 'running';

	// Set the transient.
	psupsellmaster_bp_set_transient( $transient_name, $data );
}

/**
 * This function sends the background process status through ajax.
 */
function psupsellmaster_bp_ajax_get_analytics_orders_status() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the transient name.
	$transient_name = 'psupsellmaster_bp_analytics_orders_status';

	// Get the data.
	$data = get_transient( $transient_name );
	$data = is_array( $data ) ? $data : array();

	// Get the pending.
	$pending = isset( $data['pending'] ) ? $data['pending'] : null;
	$pending = filter_var( $pending, FILTER_VALIDATE_INT );

	// Get the total.
	$total = isset( $data['total'] ) ? $data['total'] : 0;
	$total = filter_var( $total, FILTER_VALIDATE_INT );

	// Get the status.
	$status = isset( $data['status'] ) ? $data['status'] : 'none';

	// Set the precentage.
	$percentage = ! empty( $total ) ? round( ( $total - $pending ) / $total * 100, 2 ) : 100;
	$percentage = ( $percentage >= 0 ) ? $percentage : 0;
	$percentage = ( $percentage <= 100 ) ? $percentage : 100;

	// Set the is done.
	$is_done = 100 === intval( $percentage );

	// Set the message.
	$message = $is_done ? "{$percentage}%" : sprintf( '%s... %d%%', __( 'Processing', 'psupsellmaster' ), $percentage );

	// Set the transient stop.
	$transient_stop = 'psupsellmaster_bp_analytics_orders_stop';

	// Get the is stopping.
	$is_stopping = get_transient( $transient_stop );
	$is_stopping = filter_var( $is_stopping, FILTER_VALIDATE_BOOLEAN );

	// Check if the process is stopping.
	if ( $is_stopping ) {
		// Set the message.
		$message = sprintf( '%s...', __( 'Aborting', 'psupsellmaster' ) );

		// Set the status.
		$status = 'stopping';
	}

	// Check if the status is done or stopping.
	if ( in_array( $status, array( 'done', 'stopping' ), true ) ) {
		// Delete the transients.
		psupsellmaster_bp_analytics_orders_delete_transients();
	}

	// Start the buffer.
	ob_start();

	// Check if the status is not none.
	if ( 'none' !== $status ) :
		?>
		<div class="psupsellmaster-bp-analytics-orders-progress">
			<div class="psupsellmaster-progress">
				<div class="psupsellmaster-progress-bar" style="width:<?php echo esc_attr( $percentage ); ?>%"></div>
				<div class="psupsellmaster-progress-message"><?php echo esc_html( $message ); ?></div>
			</div>
		</div>
		<?php
	endif;

	// Set the response.
	$response = array( 'success' => false );

	// Set the response html.
	$response['html'] = ob_get_clean();

	// Set the response success.
	$response['success'] = true;

	// Set the response status.
	$response['status'] = $status;

	// Send the response.
	wp_send_json( $response );
}
add_action( 'wp_ajax_psupsellmaster_bp_ajax_get_analytics_orders_status', 'psupsellmaster_bp_ajax_get_analytics_orders_status' );

/**
 * This function triggers another function to try to start the background process.
 */
function psupsellmaster_bp_ajax_analytics_orders_start() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Maybe start the process.
	psupsellmaster_bp_analytics_orders_maybe_start( array( 'tracking' => true ) );

	// Send the response.
	wp_send_json( array() );
}
add_action( 'wp_ajax_psupsellmaster_bp_ajax_analytics_orders_start', 'psupsellmaster_bp_ajax_analytics_orders_start' );

/**
 * This function triggers another function to try to stop the background process.
 */
function psupsellmaster_bp_ajax_analytics_orders_stop() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Maybe stop the process.
	psupsellmaster_bp_analytics_orders_maybe_stop();

	// Send the response.
	wp_send_json( array() );
}
add_action( 'wp_ajax_psupsellmaster_bp_ajax_analytics_orders_stop', 'psupsellmaster_bp_ajax_analytics_orders_stop' );