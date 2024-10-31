<?php
/**
 * Integrations - Easy Digital Downloads - Background Process - Functions - EDD Prices.
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
function psupsellmaster_bp_edd_prices_maybe_start( $args = array() ) {
	// Set the started.
	$started = false;

	// Check if the queue is not empty.
	if ( ! PsUpsellMaster::$background['edd_prices']->is_queue_empty() ) {
		// Return the started.
		return $started;
	}

	// Check if the products argument is set.
	if ( isset( $args['products'] ) ) {
		// Set the option for the background process data.
		update_option( 'psupsellmaster_bp_edd_prices_data', array( 'products' => $args['products'] ) );
	}

	// Run some procedures before all batches.
	psupsellmaster_bp_edd_prices_run_before_batches();

	// Check if the tracking is true.
	if ( isset( $args['tracking'] ) && true === $args['tracking'] ) {
		// Setup the tracking.
		psupsellmaster_bp_edd_prices_setup_tracking();
	}

	// Set the data.
	$data = array( 'status' => 'starting' );

	// Push to the queue.
	PsUpsellMaster::$background['edd_prices']->push_to_queue( $data );

	// Save and dispatch.
	PsUpsellMaster::$background['edd_prices']->save()->dispatch();

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
function psupsellmaster_bp_edd_prices_maybe_stop() {
	// Set the stopped.
	$stopped = false;

	// Check if the queue is empty.
	if ( PsUpsellMaster::$background['edd_prices']->is_queue_empty() ) {
		// Return the stopped.
		return $stopped;
	}

	// Set the transient name.
	$transient_name = 'psupsellmaster_bp_edd_prices_stop';

	// Set the transient.
	psupsellmaster_bp_set_transient( $transient_name, true );

	// Kill the process.
	PsUpsellMaster::$background['edd_prices']->kill_process();

	// Set the stopped.
	$stopped = true;

	// Return the stopped.
	return $stopped;
}

/**
 * This function runs before all batches, but after the process is started.
 */
function psupsellmaster_bp_edd_prices_run_before_batches() {
	// Delete the meta key for all products.
	psupsellmaster_bp_edd_prices_delete_done_meta_key();

	// Delete the transients.
	psupsellmaster_bp_edd_prices_delete_transients();

	// Update the status.
	psupsellmaster_bp_edd_prices_update_status( array( 'action' => 'insert' ) );
}

/**
 * This function runs after all batches, but before the process is finished.
 */
function psupsellmaster_bp_edd_prices_run_after_batches() {
	// Get the background process data.
	$data = get_option( 'psupsellmaster_bp_edd_prices_data', array() );

	// Check if the products argument is empty.
	if ( empty( $data['products'] ) ) {
		// It updates the last run date only if the process is running for all products.

		// Set the lan run.
		$last_run = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		$last_run = $last_run->getTimestamp();

		// Update the last run date.
		update_option( 'psupsellmaster_bp_edd_prices_last_run', $last_run );
	}

	// Delete the meta key for all products.
	psupsellmaster_bp_edd_prices_delete_done_meta_key();

	// Delete the options.
	psupsellmaster_bp_edd_prices_delete_options();

	// Delete the queue lock.
	psupsellmaster_bp_delete_queue_lock();

	// Maybe delete the transients.
	psupsellmaster_bp_edd_prices_maybe_delete_transients();

	// Maybe start the queue.
	psupsellmaster_bp_maybe_run_queue();
}
add_action( 'psupsellmaster_bp_edd_prices_complete', 'psupsellmaster_bp_edd_prices_run_after_batches' );

/**
 * Setup the tracking.
 */
function psupsellmaster_bp_edd_prices_setup_tracking() {
	// Set the transient.
	psupsellmaster_bp_set_transient( 'psupsellmaster_bp_edd_prices_tracking', true );
}

/**
 * This function runs procedures after trying to kill the background process.
 */
function psupsellmaster_bp_edd_prices_kill_process() {
	// Delete the options.
	psupsellmaster_bp_edd_prices_delete_options();

	// Delete the queue lock.
	psupsellmaster_bp_delete_queue_lock();
}
add_action( 'psupsellmaster_bp_edd_prices_kill_process', 'psupsellmaster_bp_edd_prices_kill_process' );

/**
 * This function deletes the transients of the background process.
 */
function psupsellmaster_bp_edd_prices_delete_transients() {
	// Delete the stop transient.
	delete_transient( 'psupsellmaster_bp_edd_prices_stop' );

	// Delete the status transient.
	delete_transient( 'psupsellmaster_bp_edd_prices_status' );

	// Delete the track transient.
	delete_transient( 'psupsellmaster_bp_edd_prices_tracking' );
}

/**
 * This function maybe deletes the transients of the background process.
 */
function psupsellmaster_bp_edd_prices_maybe_delete_transients() {
	// Get the track transient.
	$track = get_transient( 'psupsellmaster_bp_edd_prices_tracking' );
	$track = filter_var( $track, FILTER_VALIDATE_BOOLEAN );

	// Check if the track transient is empty.
	if ( empty( $track ) ) {
		// Delete the transients.
		psupsellmaster_bp_edd_prices_delete_transients();
	}
}

/**
 * This function deletes the options of the background process.
 */
function psupsellmaster_bp_edd_prices_delete_options() {
	// Delete the data option.
	delete_option( 'psupsellmaster_bp_edd_prices_data' );
}

/**
 * This function runs for each batch.
 *
 * @return int the number of items processed.
 */
function psupsellmaster_bp_edd_prices_run_batch() {
	// Get the background process data.
	$data = get_option( 'psupsellmaster_bp_edd_prices_data', array() );

	// Set the post type.
	$post_type = 'download';

	// Set the post status.
	$post_status = 'publish';

	// Set the meta key.
	$meta_key = '_psupsellmaster_bp_edd_prices_done';

	// Get the limit.
	$limit = PsUpsellMaster_Settings::get( 'limit' );
	$limit = filter_var( $limit, FILTER_VALIDATE_INT );
	$limit = false !== $limit ? $limit : 100;

	// Set the sql products.
	$sql_products = '';

	// Check if the products argument is not empty.
	if ( ! empty( $data['products'] ) ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $data['products'] ), '%d' ) );

		// Set the sql products.
		$sql_products = PsUpsellMaster_Database::prepare( "AND `products`.`ID` IN ( {$placeholders} )", $data['products'] );
	}

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			`products`.`ID` as `product_id`
		FROM
			%i AS `products`
		WHERE
			1 = 1
		{$sql_products}
		AND
			`products`.`post_type` = %s
		AND
			`products`.`post_status` = %s
		AND
			NOT EXISTS (
				SELECT
					1
				FROM
					%i AS `postmeta`
				WHERE
					1 = 1
				AND
					`postmeta`.`post_id` = `products`.`ID`
				AND
					`postmeta`.`meta_key` = %s
			)
		",
		PsUpsellMaster_Database::get_table_name( 'posts' ),
		$post_type,
		$post_status,
		PsUpsellMaster_Database::get_table_name( 'postmeta' ),
		$meta_key
	);

	// Check if the limit is not false.
	if ( false !== $limit ) {
		// Set the sql query.
		$sql_query = "{$sql_query} LIMIT {$limit}";
	}

	// Set the count.
	$count = 0;

	// Get the products.
	$products = PsUpsellMaster_Database::get_col( $sql_query );

	// Loop through the products.
	foreach ( $products as $product_id ) {
		// Run for a single product.
		psupsellmaster_bp_edd_prices_run_single( $product_id );

		// Set the post meta key.
		update_post_meta( $product_id, $meta_key, true );

		// Increment the count.
		++$count;
	}

	// Update the status.
	psupsellmaster_bp_edd_prices_update_status();

	// Return the count.
	return $count;
}

/**
 * This function deletes the _psupsellmaster_bp_edd_prices_done meta key for all products.
 * The reason why we use this meta key in our algorithm is to prevent infinite loops.
 * eg. when no data is inserted into the database.
 * Therefore we can safely stop the background process once everything is processed.
 */
function psupsellmaster_bp_edd_prices_delete_done_meta_key() {
	// Set the post type.
	$post_type = 'download';

	// Set the meta key.
	$meta_key = '_psupsellmaster_bp_edd_prices_done';

	// Set the query.
	$query = PsUpsellMaster_Database::prepare(
		'
		DELETE
			`postmeta`
		FROM
			%i AS `postmeta`
		WHERE
			1 = 1
		AND
			`postmeta`.`meta_key` = %s
		AND
			EXISTS (
				SELECT
					1
				FROM
					%i AS `posts`
				WHERE
					1 = 1
				AND
					`posts`.`ID` = `postmeta`.`post_id`
				AND
					`posts`.`post_type` = %s
			)
		',
		PsUpsellMaster_Database::get_table_name( 'postmeta' ),
		$meta_key,
		PsUpsellMaster_Database::get_table_name( 'posts' ),
		$post_type
	);

	// Run the query.
	PsUpsellMaster_Database::query( $query );
}

/**
 * This function runs for a single product.
 *
 * @param int $product_id the product id.
 */
function psupsellmaster_bp_edd_prices_run_single( $product_id ) {
	// Update the price meta keys.
	psupsellmaster_edd_update_price_meta_keys( $product_id );
}

/**
 * This function gets the count of the remaining products to process.
 *
 * @return int the count.
 */
function psupsellmaster_bp_edd_prices_get_count() {
	// Get the background process data.
	$data = get_option( 'psupsellmaster_bp_edd_prices_data', array() );

	// Set the post type.
	$post_type = 'download';

	// Set the post status.
	$post_status = 'publish';

	// Set the meta key.
	$meta_key = '_psupsellmaster_bp_edd_prices_done';

	// Set the sql products.
	$sql_products = '';

	// Check if the products argument is not empty.
	if ( ! empty( $data['products'] ) ) {
		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $data['products'] ), '%d' ) );

		// Set the sql products.
		$sql_products = PsUpsellMaster_Database::prepare( "AND `products`.`ID` IN ( {$placeholders} )", $data['products'] );
	}

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			`products`.`ID` as `product_id`
		FROM
			%i AS `products`
		WHERE
			1 = 1
		{$sql_products}
		AND
			`products`.`post_type` = %s
		AND
			`products`.`post_status` = %s
		AND
			NOT EXISTS (
				SELECT
					1
				FROM
					%i AS `postmeta`
				WHERE
					1 = 1
				AND
					`postmeta`.`post_id` = `products`.`ID`
				AND
					`postmeta`.`meta_key` = %s
			)
		",
		PsUpsellMaster_Database::get_table_name( 'posts' ),
		$post_type,
		$post_status,
		PsUpsellMaster_Database::get_table_name( 'postmeta' ),
		$meta_key
	);

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
function psupsellmaster_bp_edd_prices_update_status( $args = array() ) {
	// Get the action.
	$action = isset( $args['action'] ) ? $args['action'] : false;
	$action = in_array( $action, array( 'insert', 'update' ), true ) ? $action : 'update';

	// Get the count.
	$count = psupsellmaster_bp_edd_prices_get_count();

	// Set the transient name.
	$transient_name = 'psupsellmaster_bp_edd_prices_status';

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
function psupsellmaster_bp_ajax_get_edd_prices_status() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the transient name.
	$transient_name = 'psupsellmaster_bp_edd_prices_status';

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
	$transient_stop = 'psupsellmaster_bp_edd_prices_stop';

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
		psupsellmaster_bp_edd_prices_delete_transients();
	}

	// Start the buffer.
	ob_start();

	// Check if the status is not none.
	if ( 'none' !== $status ) :
		?>
		<div class="psupsellmaster-bp-edd-prices-progress">
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
add_action( 'wp_ajax_psupsellmaster_bp_ajax_get_edd_prices_status', 'psupsellmaster_bp_ajax_get_edd_prices_status' );

/**
 * This function triggers another function to try to start the background process.
 */
function psupsellmaster_bp_ajax_edd_prices_start() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Maybe start the process.
	psupsellmaster_bp_edd_prices_maybe_start( array( 'tracking' => true ) );

	// Send the response.
	wp_send_json( array() );
}
add_action( 'wp_ajax_psupsellmaster_bp_ajax_edd_prices_start', 'psupsellmaster_bp_ajax_edd_prices_start' );

/**
 * This function triggers another function to try to stop the background process.
 */
function psupsellmaster_bp_ajax_edd_prices_stop() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Maybe stop the process.
	psupsellmaster_bp_edd_prices_maybe_stop();

	// Send the response.
	wp_send_json( array() );
}
add_action( 'wp_ajax_psupsellmaster_bp_ajax_edd_prices_stop', 'psupsellmaster_bp_ajax_edd_prices_stop' );
