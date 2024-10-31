<?php
/**
 * Functions - Background Process - Scores.
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
function psupsellmaster_bp_scores_maybe_start( $args = array() ) {
	// Set the started.
	$started = false;

	// Check if the queue is not empty.
	if ( ! PsUpsellMaster::$background['scores']->is_queue_empty() ) {
		// Return the started.
		return $started;
	}

	// Check if the products argument is set.
	if ( isset( $args['products'] ) ) {
		// Set the option for the background process data.
		update_option( 'psupsellmaster_bp_scores_data', array( 'products' => $args['products'] ) );
	}

	// Run some procedures before all batches.
	psupsellmaster_bp_scores_run_before_batches();

	// Check if the tracking is true.
	if ( isset( $args['tracking'] ) && true === $args['tracking'] ) {
		// Setup the tracking.
		psupsellmaster_bp_scores_setup_tracking();
	}

	// Set the data.
	$data = array( 'status' => 'starting' );

	// Push to the queue.
	PsUpsellMaster::$background['scores']->push_to_queue( $data );

	// Save and dispatch.
	PsUpsellMaster::$background['scores']->save()->dispatch();

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
function psupsellmaster_bp_scores_maybe_stop() {
	// Set the stopped.
	$stopped = false;

	// Check if the queue is empty.
	if ( PsUpsellMaster::$background['scores']->is_queue_empty() ) {
		// Return the stopped.
		return $stopped;
	}

	// Set the transient name.
	$transient_name = 'psupsellmaster_bp_scores_stop';

	// Set the transient.
	psupsellmaster_bp_set_transient( $transient_name, true );

	// Kill the process.
	PsUpsellMaster::$background['scores']->kill_process();

	// Set the stopped.
	$stopped = true;

	// Return the stopped.
	return $stopped;
}

/**
 * This function runs before all batches, but after the process is started.
 */
function psupsellmaster_bp_scores_run_before_batches() {
	// Empty the database records.
	psupsellmaster_bp_scores_empty_records();

	// Delete the meta key for all products.
	psupsellmaster_bp_scores_delete_done_meta_key();

	// Delete the transients.
	psupsellmaster_bp_scores_delete_transients();

	// Drop the temporary database tables.
	psupsellmaster_bp_scores_drop_temporary_db_tables();

	// (Re)create the temporary database tables.
	psupsellmaster_bp_scores_create_temporary_db_tables();

	// Insert the candidates.
	psupsellmaster_bp_scores_insert_candidates();

	// Update the status.
	psupsellmaster_bp_scores_update_status( array( 'action' => 'insert' ) );
}

/**
 * This function runs after all batches, but before the process is finished.
 */
function psupsellmaster_bp_scores_run_after_batches() {
	// Get the background process data.
	$data = get_option( 'psupsellmaster_bp_scores_data', array() );

	// Check if the products argument is empty.
	if ( empty( $data['products'] ) ) {
		// It updates the last run date only if the process is running for all products.

		// Set the lan run.
		$last_run = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		$last_run = $last_run->getTimestamp();

		// Update the last run date.
		update_option( 'psupsellmaster_bp_scores_last_run', $last_run );
	}

	// Delete the meta key for all products.
	psupsellmaster_bp_scores_delete_done_meta_key();

	// Delete the options.
	psupsellmaster_bp_scores_delete_options();

	// Delete the queue lock.
	psupsellmaster_bp_delete_queue_lock();

	// Drop the temporary database tables.
	psupsellmaster_bp_scores_drop_temporary_db_tables();

	// Maybe delete the transients.
	psupsellmaster_bp_scores_maybe_delete_transients();

	// Maybe start the queue.
	psupsellmaster_bp_maybe_run_queue();
}
add_action( 'psupsellmaster_bp_scores_complete', 'psupsellmaster_bp_scores_run_after_batches' );

/**
 * Setup the tracking.
 */
function psupsellmaster_bp_scores_setup_tracking() {
	// Set the transient.
	psupsellmaster_bp_set_transient( 'psupsellmaster_bp_scores_tracking', true );
}

/**
 * This function runs procedures after trying to kill the background process.
 */
function psupsellmaster_bp_scores_kill_process() {
	// Empty the database records.
	psupsellmaster_bp_scores_empty_records();

	// Delete the meta key for all products.
	psupsellmaster_bp_scores_delete_done_meta_key();

	// Delete the options.
	psupsellmaster_bp_scores_delete_options();

	// Delete the queue lock.
	psupsellmaster_bp_delete_queue_lock();

	// Drop the temporary database tables.
	psupsellmaster_bp_scores_drop_temporary_db_tables();
}
add_action( 'psupsellmaster_bp_scores_kill_process', 'psupsellmaster_bp_scores_kill_process' );

/**
 * This function checks if the required resources still exist to continue the background process.
 * eg. the temporary database tables (they are dropped when the process is killed).
 *
 * @return boolean whether the required resources still exist or not.
 */
function psupsellmaster_bp_scores_required_resources_exists() {
	// Set the exists.
	$exists = false;

	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' );

	// Check if the database table products does exist.
	if ( ! empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name ) ) ) ) {
		// Set the exists.
		$exists = true;
	}

	// Return the exists.
	return $exists;
}

/**
 * This function empties the records of the scores database table.
 * It will truncate the database table if the background process
 * is running for all products, or it will delete specific records
 * when the background process is running for specific products.
 */
function psupsellmaster_bp_scores_empty_records() {
	// Get the background process data.
	$data = get_option( 'psupsellmaster_bp_scores_data', array() );

	// Check if the products argument is empty.
	if ( empty( $data['products'] ) ) {
		// Truncate the scores database table.
		psupsellmaster_db_scores_truncate();

		// Otherwise...
	} else {
		// Loop through the products.
		foreach ( $data['products'] as $product_id ) {
			// Delete the scores for specific base products.
			psupsellmaster_db_scores_delete( array( 'base_product_id' => $product_id ) );
		}
	}
}

/**
 * This function creates the temporary database tables.
 */
function psupsellmaster_bp_scores_create_temporary_db_tables() {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' );

	// Check if the database table scores does not exist.
	if ( empty( PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( 'SHOW TABLES LIKE %s', $table_name ) ) ) ) {
		// Set the query.
		$query = (
			"
			CREATE TABLE IF NOT EXISTS `{$table_name}` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`product_id` bigint(20) NOT NULL DEFAULT 0,
				PRIMARY KEY  (`id`),
				KEY `product_id` (`product_id`)
			)
			"
		);

		// Requires the upgrade.php file.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Creates the database table.
		dbDelta( $query );
	}
}

/**
 * This function inserts the candidates (products that can be chosen as upsells, as per the settings).
 *
 * @return int The number of rows inserted.
 */
function psupsellmaster_bp_scores_insert_candidates() {
	// Set the rows.
	$rows = 0;

	// Set the sql insert.
	$sql_insert = '';

	// Get the product post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the post status.
	$post_status = 'publish';

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			DISTINCT `products`.`ID` AS `product_id`
		FROM
			%i AS `products`
		WHERE
			1 = 1
		AND
			`products`.`post_type` = %s
		AND
			`products`.`post_status` = %s
		',
		PsUpsellMaster_Database::get_table_name( 'posts' ),
		$post_type,
		$post_status
	);

	// Get the sql query for the bundles only filter.
	$sql_query .= psupsellmaster_bp_scores_get_sql_bundles_only();

	// Get the sql query for the price range filter.
	$sql_query .= psupsellmaster_bp_scores_get_sql_price_range();

	// Get the sql query for the excluded taxonomies filter.
	$sql_query .= psupsellmaster_bp_scores_get_sql_excluded_taxonomies_from_settings();

	// Get the products.
	$products = PsUpsellMaster_Database::get_col( $sql_query );

	// Check if the products is empty.
	if ( ! empty( $products ) ) {
		// Set the placeholders. At the end, this will create a string format like '( %d ), ( %d ), ( %d ), ( %d ), ( %d )'...
		$placeholders = implode( ' ), ( ', array_fill( 0, count( $products ), '%d' ) );

		// Set the sql values.
		$sql_values = PsUpsellMaster_Database::prepare( $placeholders, $products );

		// Set the sql insert.
		$sql_insert = PsUpsellMaster_Database::prepare(
			"
			INSERT INTO %i
				( `product_id` )
			VALUES
				( {$sql_values} )
			",
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' )
		);

		// Insert the candidates.
		$rows = PsUpsellMaster_Database::query( $sql_insert );
	}

	// Return the rows.
	return $rows;
}

/**
 * This function deletes the transients of the background process.
 */
function psupsellmaster_bp_scores_delete_transients() {
	// Delete the stop transient.
	delete_transient( 'psupsellmaster_bp_scores_stop' );

	// Delete the status transient.
	delete_transient( 'psupsellmaster_bp_scores_status' );

	// Delete the track transient.
	delete_transient( 'psupsellmaster_bp_scores_tracking' );
}

/**
 * This function maybe deletes the transients of the background process.
 */
function psupsellmaster_bp_scores_maybe_delete_transients() {
	// Get the track transient.
	$track = get_transient( 'psupsellmaster_bp_scores_tracking' );
	$track = filter_var( $track, FILTER_VALIDATE_BOOLEAN );

	// Check if the track transient is empty.
	if ( empty( $track ) ) {
		// Delete the transients.
		psupsellmaster_bp_scores_delete_transients();
	}
}

/**
 * This function deletes the options of the background process.
 */
function psupsellmaster_bp_scores_delete_options() {
	// Delete the data option.
	delete_option( 'psupsellmaster_bp_scores_data' );
}

/**
 * This function drops the temporary database tables.
 */
function psupsellmaster_bp_scores_drop_temporary_db_tables() {
	// Drop the database tables.
	PsUpsellMaster_Database::query( PsUpsellMaster_Database::prepare( 'DROP TABLE IF EXISTS %i', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' ) ) );
}

/**
 * This function runs for each batch.
 *
 * @return int the number of items processed.
 */
function psupsellmaster_bp_scores_run_batch() {
	// Get the background process data.
	$data = get_option( 'psupsellmaster_bp_scores_data', array() );

	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the post status.
	$post_status = 'publish';

	// Set the meta key.
	$meta_key = '_psupsellmaster_bp_scores_done';

	// Get the limit.
	$limit = PsUpsellMaster_Settings::get( 'limit' );
	$limit = filter_var( $limit, FILTER_VALIDATE_INT );
	$limit = false !== $limit ? $limit : 100;

	// Check if this is the lite version.
	if ( psupsellmaster_is_lite() ) {
		// Get the base products limit.
		$base_products_limit = psupsellmaster_get_feature_limit( 'base_products_count' );

		// Check if the limit has been reached.
		if ( $limit > $base_products_limit ) {
			// Set the limit.
			$limit = $base_products_limit;
		}
	}

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

	// Set the sql insert.
	$sql_insert = '';

	// Get the products.
	$products = PsUpsellMaster_Database::get_col( $sql_query );

	// Check if the products is not empty.
	if ( ! empty( $products ) ) {
		// Loop through the products.
		foreach ( $products as $product_id ) {
			// Check if this is the lite version.
			if ( psupsellmaster_is_lite() ) {
				// Check the limit.
				if ( psupsellmaster_has_reached_feature_limit( 'base_products_count' ) ) {
					// Stop the loop.
					break;
				}
			}

			// Set the sql data.
			$sql_data = psupsellmaster_bp_scores_run_single( $product_id );

			// Check if the sql data is not empty.
			if ( ! empty( $sql_data ) ) {
				// Set the sql insert.
				$sql_insert .= "{$sql_data}, ";
			}

			// Set the post meta key.
			update_post_meta( $product_id, $meta_key, true );

			// Increment the count.
			++$count;
		}
	}

	// Check if the sql insert is not empty.
	if ( ! empty( $sql_insert ) ) {
		// Set the sql insert (remove the two last characters).
		$sql_insert = substr( $sql_insert, 0, strlen( $sql_insert ) - 2 );

		// Set the sql insert.
		$sql_insert = PsUpsellMaster_Database::prepare(
			"
			INSERT INTO %i
				( `base_product_id`, `upsell_product_id`, `criteria`, `score`, `created_at`, `updated_at` )
			VALUES
				{$sql_insert}
			",
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' )
		);

		// Insert the scores.
		PsUpsellMaster_Database::query( $sql_insert );
	}

	// Update the status.
	psupsellmaster_bp_scores_update_status();

	// Return the count.
	return $count;
}

/**
 * This function deletes the _psupsellmaster_bp_scores_done meta key for all products.
 * The reason why we use this meta key in our algorithm is to prevent infinite loops.
 * eg. when no data is inserted into the database.
 * Therefore we can safely stop the background process once everything is processed.
 */
function psupsellmaster_bp_scores_delete_done_meta_key() {
	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the meta key.
	$meta_key = '_psupsellmaster_bp_scores_done';

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
 * This function runs for a single base product.
 *
 * @param int $base_product_id the base product id.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_run_single( $base_product_id ) {
	// Set the sql insert.
	$sql_insert = '';

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'algorithm_logic' );

	// Get the priority only.
	$priority_only = isset( $settings['priority_only'] ) ? filter_var( $settings['priority_only'], FILTER_VALIDATE_BOOLEAN ) : false;

	// Get the max upsells.
	$max_upsells = psupsellmaster_settings_get_max_upsells();

	// Get the limit.
	$limit = PsUpsellMaster_Settings::get( 'number_of_upsell_products' );
	$limit = filter_var( $limit, FILTER_VALIDATE_INT );
	$limit = ! empty( $limit ) && $limit <= $max_upsells ? $limit : $max_upsells;

	// Set the args.
	$args = array(
		'product_id' => $base_product_id,
	);

	// Get the sql scores.
	$sql_scores = psupsellmaster_bp_scores_get_sql_scores( $args );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			`scores`.`product_id` AS `upsell_product_id`,
			SUM( `scores`.`score` ) AS `total_score`,
			GROUP_CONCAT(
				CONCAT( `scores`.`criteria`, ':', `scores`.`score` )
				ORDER BY
					`scores`.`score` DESC
				SEPARATOR ';'
			) AS `criteria_scores`
		FROM
			( {$sql_scores} ) AS `scores`
		GROUP BY
			`scores`.`product_id`
		ORDER BY
			`total_score` DESC,
			`scores`.`product_id` DESC
		LIMIT
			%d
		",
		$limit
	);

	// Check if the resources does not exists.
	if ( ! psupsellmaster_bp_scores_required_resources_exists() ) {
		// Return the sql insert.
		return $sql_insert;
	}

	// Get the products.
	$products = PsUpsellMaster_Database::get_results( $sql_query );

	// Check if the products is empty.
	if ( empty( $products ) ) {
		// Return the sql insert.
		return $sql_insert;
	}

	// Set the current time.
	$current_time = current_time( 'mysql' );

	// Loop through the products.
	foreach ( $products as $product ) {
		// Get the upsell product id.
		$upsell_product_id = isset( $product->upsell_product_id ) ? filter_var( $product->upsell_product_id, FILTER_VALIDATE_INT ) : false;

		// Get the criteria scores.
		$criteria_scores = isset( $product->criteria_scores ) ? $product->criteria_scores : '';
		$criteria_scores = explode( ';', $criteria_scores );

		// Loop through the criteria scores.
		foreach ( $criteria_scores as $criteria_score ) {
			// Get the criteria data.
			$criteria_data = explode( ':', $criteria_score );

			// Get the criteria.
			$criteria = array_shift( $criteria_data );

			// Get the score.
			$score = array_shift( $criteria_data );

			// Check if the score is lower or equal to 0 and if the priority only is true.
			if ( $score <= 0 && true === $priority_only ) {
				// Continue the loop.
				continue;
			}

			// Set the sql insert.
			$sql_insert .= PsUpsellMaster_Database::prepare(
				'( %d, %d, %s, %f, %s, %s ), ',
				$base_product_id,
				$upsell_product_id,
				$criteria,
				$score,
				$current_time,
				$current_time
			);
		}
	}

	// Set the sql insert (remove the two last characters).
	$sql_insert = substr( $sql_insert, 0, strlen( $sql_insert ) - 2 );

	// Return the sql insert.
	return $sql_insert;
}

/**
 * This function gets the sql query for the criteria filters (from product settings).
 *
 * @param array $args the arguments.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_criteria_filters( $args = array() ) {
	// Get the product id.
	$product_id = $args['product_id'];

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare( ' AND `candidates`.`product_id` <> %d ', $product_id );

	// Get the sql query for the excluded products filter.
	$sql_query .= psupsellmaster_bp_scores_get_sql_excluded_products_from_product( $args );

	// Get the sql query for the excluded taxonomies filter.
	$sql_query .= psupsellmaster_bp_scores_get_sql_excluded_taxonomies_from_product( $args );

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the bundles only filter.
 *
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_bundles_only() {
	// Set the sql query.
	$sql_query = '';

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Return the sql query.
		return $sql_query;
	}

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'algorithm_logic' );

	// Get the bundles only.
	$bundles_only = isset( $settings['bundles_only'] ) ? filter_var( $settings['bundles_only'], FILTER_VALIDATE_BOOLEAN ) : false;

	// Check if the bundles only is false.
	if ( false === $bundles_only ) {
		// Return the sql query.
		return $sql_query;
	}

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		AND
			EXISTS (
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
				AND
					`postmeta`.`meta_value` = %s
			)
		',
		PsUpsellMaster_Database::get_table_name( 'postmeta' ),
		'_edd_product_type',
		'bundle'
	);

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the excluded products filter.
 *
 * @param array $args the arguments.
 */
function psupsellmaster_bp_scores_get_sql_excluded_products_from_product( $args = array() ) {
	// Set the sql query.
	$sql_query = '';

	// Get the base product id.
	$base_product_id = isset( $args['product_id'] ) ? filter_var( $args['product_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the base product id is empty.
	if ( empty( $base_product_id ) ) {
		// Return the sql query.
		return $sql_query;
	}

	// Set the sql products.
	$sql_products = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`postmeta`.`meta_value`
		FROM
			%i AS `postmeta`
		WHERE
			1 = 1
		AND
			`postmeta`.`post_id` = %d
		AND
			`postmeta`.`meta_key` = %s
		',
		PsUpsellMaster_Database::get_table_name( 'postmeta' ),
		$base_product_id,
		'psupsellmaster_excluded_products'
	);

	// Get the excluded products.
	$excluded_products = PsUpsellMaster_Database::get_col( $sql_products );

	// Check if the excluded products is empty.
	if ( empty( $excluded_products ) ) {
		// Return the sql query.
		return $sql_query;
	}

	// Set the placeholders.
	$placeholders = implode( ', ', array_fill( 0, count( $excluded_products ), '%d' ) );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare( " AND `candidates`.`product_id` NOT IN ( {$placeholders} ) ", $excluded_products );

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the excluded taxonomies filter.
 *
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_excluded_taxonomies_from_settings() {
	// Set the sql query.
	$sql_query = '';

	// Set the excluded taxonomies.
	$excluded_taxonomies = psupsellmaster_get_excluded_taxonomies_from_settings();

	// Check if the excluded taxonomies is empty.
	if ( empty( $excluded_taxonomies ) ) {
		// Return the sql query.
		return $sql_query;
	}

	// Loop through the excluded taxonomies.
	foreach ( $excluded_taxonomies as $taxonomy_key => $taxonomy_terms ) {
		// Get the terms.
		$terms = is_array( $taxonomy_terms ) ? $taxonomy_terms : array();

		// Remove duplicate entries.
		$terms = array_unique( $terms );

		// Remove empty entries.
		$terms = array_filter( $terms );

		// Check if the terms is empty.
		if ( empty( $terms ) ) {
			// Continue the loop.
			continue;
		}

		// Set the taxonomy.
		$taxonomy = ! empty( $taxonomy_key ) ? $taxonomy_key : '';

		// Check if the taxonomy is empty.
		if ( empty( $taxonomy ) ) {
			// Continue the loop.
			continue;
		}

		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $terms ), '%d' ) );

		// Set the sql terms.
		$sql_terms = PsUpsellMaster_Database::prepare( "`term_taxonomy`.`term_id` IN ( {$placeholders} )", $terms );

		// Set the sql query.
		$sql_query .= PsUpsellMaster_Database::prepare(
			"
			AND
				NOT EXISTS (
					SELECT
						1
					FROM
						%i AS `term_relationships`
					INNER JOIN
						%i AS `term_taxonomy`
					ON
						`term_taxonomy`.`term_taxonomy_id` = `term_relationships`.`term_taxonomy_id`
					WHERE
						1 = 1
					AND
						`term_relationships`.`object_id` = `products`.`ID`
					AND
						`term_taxonomy`.`taxonomy` = %s
					AND
						{$sql_terms}
				)
			",
			PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
			PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
			$taxonomy
		);
	}

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the excluded taxonomies filter.
 *
 * @param array $args the arguments.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_excluded_taxonomies_from_product( $args = array() ) {
	// Set the sql query.
	$sql_query = '';

	// Set the excluded taxonomies.
	$excluded_taxonomies = array();

	// Get the product id.
	$product_id = isset( $args['product_id'] ) ? filter_var( $args['product_id'], FILTER_VALIDATE_INT ) : false;

	// Check if the product id is not empty.
	if ( ! empty( $product_id ) ) {
		// Get the excluded taxonomies from the product.
		$product_excluded_taxonomies = psupsellmaster_get_excluded_taxonomies_from_product( $product_id );

		// Check if the product excluded taxonomies is not empty.
		if ( ! empty( $product_excluded_taxonomies ) ) {
			// Loop through the product excluded taxonomies.
			foreach ( $product_excluded_taxonomies as $taxonomy_key => $taxonomy_terms ) {
				// Check if the taxonomy key is empty.
				if ( empty( $taxonomy_terms ) ) {
					// Continue the loop.
					continue;
				}

				// Set the terms.
				$terms = is_array( $taxonomy_terms ) ? $taxonomy_terms : array();

				// Remove duplicate entries.
				$terms = array_unique( $terms );

				// Remove empty entries.
				$terms = array_filter( $terms );

				// Set the terms.
				$excluded_taxonomies[ $taxonomy_key ] = $terms;
			}
		}
	}

	// Check if the excluded taxonomies is empty.
	if ( empty( $excluded_taxonomies ) ) {
		// Return the sql query.
		return $sql_query;
	}

	// Loop through the excluded taxonomies.
	foreach ( $excluded_taxonomies as $taxonomy_key => $taxonomy_terms ) {
		// Get the terms.
		$terms = is_array( $taxonomy_terms ) ? $taxonomy_terms : array();

		// Remove duplicate entries.
		$terms = array_unique( $terms );

		// Remove empty entries.
		$terms = array_filter( $terms );

		// Check if the terms is empty.
		if ( empty( $terms ) ) {
			// Continue the loop.
			continue;
		}

		// Set the taxonomy.
		$taxonomy = ! empty( $taxonomy_key ) ? $taxonomy_key : '';

		// Check if the taxonomy is empty.
		if ( empty( $taxonomy ) ) {
			// Continue the loop.
			continue;
		}

		// Set the placeholders.
		$placeholders = implode( ', ', array_fill( 0, count( $terms ), '%d' ) );

		// Set the sql terms.
		$sql_terms = PsUpsellMaster_Database::prepare( "`term_taxonomy`.`term_id` IN ( {$placeholders} )", $terms );

		// Set the sql query.
		$sql_query .= PsUpsellMaster_Database::prepare(
			"
			AND
				NOT EXISTS (
					SELECT
						1
					FROM
						%i AS `term_relationships`
					INNER JOIN
						%i AS `term_taxonomy`
					ON
						`term_taxonomy`.`term_taxonomy_id` = `term_relationships`.`term_taxonomy_id`
					WHERE
						1 = 1
					AND
						`term_relationships`.`object_id` = `candidates`.`product_id`
					AND
						`term_taxonomy`.`taxonomy` = %s
					AND
						{$sql_terms}
				)
			",
			PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
			PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
			$taxonomy
		);
	}

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the price range filter.
 *
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_price_range() {
	// Set the sql query.
	$sql_query = '';

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'algorithm_logic' );

	// Get the range.
	$range = isset( $settings['price_range'] ) ? $settings['price_range'] : array();

	// Get the min.
	$min = isset( $range['from'] ) ? filter_var( $range['from'], FILTER_VALIDATE_FLOAT ) : false;

	// Get the max.
	$max = isset( $range['to'] ) ? filter_var( $range['to'], FILTER_VALIDATE_FLOAT ) : false;

	// Check if min and max are false.
	if ( false === $min && false === $max ) {
		// Return the sql query.
		return $sql_query;
	}

	// Set the meta key.
	$meta_key = '';

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the meta key.
		$meta_key = '_price';

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the meta key.
		$meta_key = '_psupsellmaster_price';
	}

	// Set the sql min.
	$sql_min = '';

	// Set the sql max.
	$sql_max = '';

	// Check if the min is not false.
	if ( false !== $min ) {
		// Set the sql sub query.
		$sql_sub_query = PsUpsellMaster_Database::prepare(
			'
			SELECT
				MAX( CAST( `postmeta`.`meta_value` AS DOUBLE ) )
			FROM
				%i AS `postmeta`
			WHERE
				1 = 1
			AND
				`postmeta`.`post_id` = `products`.`ID`
			AND
				`postmeta`.`meta_key` = %s
			',
			PsUpsellMaster_Database::get_table_name( 'postmeta' ),
			$meta_key
		);

		// Set the sql min.
		$sql_min = PsUpsellMaster_Database::prepare( " AND COALESCE( ( {$sql_sub_query} ), 0 ) >= %f ", $min );
	}

	// Check if the max is not false.
	if ( false !== $max ) {
		// Set the sql sub query.
		$sql_sub_query = PsUpsellMaster_Database::prepare(
			'
			SELECT
				MIN( CAST( `postmeta`.`meta_value` AS DOUBLE ) )
			FROM
				%i AS `postmeta`
			WHERE
				1 = 1
			AND
				`postmeta`.`post_id` = `products`.`ID`
			AND
				`postmeta`.`meta_key` = %s
			',
			PsUpsellMaster_Database::get_table_name( 'postmeta' ),
			$meta_key
		);

		// Check if the max is not zero.
		if ( 0 !== $max ) {
			// Set the sql max.
			$sql_sub_query .= PsUpsellMaster_Database::prepare( ' AND CAST( `postmeta`.`meta_value` AS DOUBLE ) > %f ', 0 );
		}

		// Set the sql max.
		$sql_max = PsUpsellMaster_Database::prepare( " AND COALESCE( ( {$sql_sub_query} ), 0 ) <= %f ", $max );
	}

	// Set the sql query.
	$sql_query = "{$sql_min} {$sql_max}";

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the scores.
 *
 * @param array $args the arguments.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_scores( $args ) {
	// Set the sql query.
	$sql_query = array();

	// Get the product taxonomies.
	$product_taxonomies = psupsellmaster_get_product_taxonomies();

	// Get the settings.
	$settings = PsUpsellMaster_Settings::get( 'algorithm_logic' );

	// Get the weights.
	$weights = isset( $settings['weight_factor'] ) ? $settings['weight_factor'] : array();

	// Add the preferred weight to the list.
	array_push( $weights, 1000000000000000 ); // 1 quadrillion.

	// Get the priorities.
	$priorities = isset( $settings['priority'] ) ? $settings['priority'] : array();

	// Add the preferred priority to the list.
	array_push( $priorities, 'preferred' );

	// Loop through the priorities.
	foreach ( $priorities as $priority_index => $priority ) {
		// Get the priority weight.
		$priority_weight = isset( $weights[ $priority_index ] ) ? $weights[ $priority_index ] : 0;

		// Check if the priority weight is empty.
		if ( empty( $priority_weight ) ) {
			// Continue the loop.
			continue;
		}

		// Set the args.
		$args['weight'] = $priority_weight;

		// Check the priority.
		if ( 'preferred' === $priority ) {
			// Get the sql.
			$sql_criteria = psupsellmaster_bp_scores_get_sql_criteria_preferred( $args );

			// Add it to the sql query.
			array_push( $sql_query, $sql_criteria );

			// Check the priority.
		} elseif ( 'vendor' === $priority ) {
			// Get the sql.
			$sql_criteria = psupsellmaster_bp_scores_get_sql_criteria_vendor( $args );

			// Add it to the sql query.
			array_push( $sql_query, $sql_criteria );

			// Check the priority.
		} elseif ( 'lifetime-sales' === $priority ) {
			// Get the sql.
			$sql_criteria = psupsellmaster_bp_scores_get_sql_criteria_lifetime_sales( $args );

			// Add it to the sql query.
			array_push( $sql_query, $sql_criteria );

			// Check the priority.
		} elseif ( 'order-results' === $priority ) {
			// Get the sql.
			$sql_criteria = psupsellmaster_bp_scores_get_sql_criteria_order_results( $args );

			// Add it to the sql query.
			array_push( $sql_query, $sql_criteria );

			// Check the priority.
		} elseif ( 'upsell-results' === $priority ) {
			// Get the sql.
			$sql_criteria = psupsellmaster_bp_scores_get_sql_criteria_upsell_results( $args );

			// Add it to the sql query.
			array_push( $sql_query, $sql_criteria );

			// Otherwise....
		} else {
			// Get the taxonomy from the priority.
			$taxonomy = str_replace( 'taxonomy_', '', $priority );

			// Check the priority.
			if ( 'category' === $priority ) {
				// Set the taxonomy.
				$taxonomy = psupsellmaster_get_product_category_taxonomy();

				// Check the priority.
			} elseif ( 'tag' === $priority ) {
				// Set the taxonomy.
				$taxonomy = psupsellmaster_get_product_tag_taxonomy();
			}

			// Check the taxonomy.
			if ( in_array( $taxonomy, $product_taxonomies, true ) ) {
				// Set the args.
				$args['taxonomy'] = $taxonomy;

				// Get the sql.
				$sql_criteria = psupsellmaster_bp_scores_get_sql_criteria_taxonomy( $args );

				// Add it to the sql query.
				array_push( $sql_query, $sql_criteria );
			}
		}
	}

	// Get the priority only.
	$priority_only = isset( $settings['priority_only'] ) ? filter_var( $settings['priority_only'], FILTER_VALIDATE_BOOLEAN ) : false;

	// Check if the priority only is false (meaning that we should include other random products to fill the upsells up to the limit).
	if ( false === $priority_only ) {
		// Get the sql filters.
		$sql_filters = psupsellmaster_bp_scores_get_sql_criteria_filters( $args );

		// Set the sql.
		$sql_criteria = PsUpsellMaster_Database::prepare(
			"
			SELECT
				'random' AS `criteria`,
				`candidates`.`product_id`,
				0.00 AS `score`
			FROM
				%i AS `candidates`
			WHERE
				1 = 1
			{$sql_filters}
			",
			PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' )
		);

		// Add it to the sql query.
		array_push( $sql_query, $sql_criteria );
	}

	// Check if the sql query is empty.
	if ( empty( $sql_query ) ) {
		// Set the sql.
		$sql_criteria = PsUpsellMaster_Database::prepare(
			"
			SELECT
				'none' AS `criteria`,
				0 AS `product_id`,
				0.00 AS `score`
			FROM
				%i AS `products`
			WHERE
				1 <> 1
			",
			PsUpsellMaster_Database::get_table_name( 'posts' )
		);

		// Add it to the sql query.
		array_push( $sql_query, $sql_criteria );
	}

	// Set the sql query.
	$sql_query = implode( ' UNION ALL ', array_filter( $sql_query ) );

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the criteria preferred.
 *
 * @param array $args the arguments.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_criteria_preferred( $args ) {
	// Set the sql query.
	$sql_query = '';

	// Set the criteria.
	$criteria = 'preferred';

	// Get the product id.
	$product_id = $args['product_id'];

	// Get the weight.
	$weight = $args['weight'];

	// Get the weight.
	$weight = $args['weight'];

	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the post status.
	$post_status = 'publish';

	// Set the sql products.
	$sql_products = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`postmeta`.`meta_value`
		FROM
			%i AS `postmeta`
		WHERE
			1 = 1
		AND
			`postmeta`.`post_id` = %d
		AND
			`postmeta`.`meta_key` = %s
		',
		PsUpsellMaster_Database::get_table_name( 'postmeta' ),
		$product_id,
		'psupsellmaster_preferred_products'
	);

	// Get the preferred products.
	$preferred_products = PsUpsellMaster_Database::get_col( $sql_products );

	// Check if the preferred products is empty.
	if ( empty( $preferred_products ) ) {
		// Return the sql query.
		return $sql_query;
	}

	// Set the placeholders.
	$placeholders = implode( ', ', array_fill( 0, count( $preferred_products ), '%d' ) );

	// Set the sql products.
	$sql_products = PsUpsellMaster_Database::prepare( "`products`.`ID` IN ( {$placeholders} )", $preferred_products );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			%s AS `criteria`,
			`products`.`ID` AS `product_id`,
			%d AS `score`
		FROM
			%i AS `products`
		WHERE
			1 = 1
		AND
			`products`.`ID` <> %d
		AND
			`products`.`post_type` = %s
		AND
			`products`.`post_status` = %s
		AND
			{$sql_products}
		GROUP BY
			`criteria`,
			`product_id`
		",
		$criteria,
		$weight,
		PsUpsellMaster_Database::get_table_name( 'posts' ),
		$product_id,
		$post_type,
		$post_status
	);

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the criteria vendor.
 *
 * @param array $args the arguments.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_criteria_vendor( $args ) {
	// Set the criteria.
	$criteria = 'vendor';

	// Get the product id.
	$product_id = $args['product_id'];

	// Get the weight.
	$weight = $args['weight'];

	// Get the author id.
	$author_id = get_post_field( 'post_author', $product_id, 'raw' );

	// Get the sql filters.
	$sql_filters = psupsellmaster_bp_scores_get_sql_criteria_filters( $args );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			%s AS `criteria`,
			`candidates`.`product_id`,
			%d AS `score`
		FROM
			%i AS `candidates`
		WHERE
			1 = 1
		{$sql_filters}
		AND
			EXISTS (
				SELECT
					1
				FROM
					%i AS `posts`
				WHERE
					1 = 1
				AND
					`posts`.`ID` = `candidates`.`product_id`
				AND
					`posts`.`post_author` = %d
			)
		GROUP BY
			`criteria`,
			`product_id`
		",
		$criteria,
		$weight,
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' ),
		PsUpsellMaster_Database::get_table_name( 'posts' ),
		$author_id
	);

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the criteria lifetime sales.
 *
 * @param array $args the arguments.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_criteria_lifetime_sales( $args ) {
	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the criteria.
	$criteria = 'lifetime-sales';

	// Get the weight.
	$weight = $args['weight'];

	// Set the post status.
	$post_status = 'publish';

	// Set the meta key.
	$meta_key = '';

	// Check if the WooCommerce plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'woo' ) ) {
		// Set the meta key.
		$meta_key = 'total_sales';

		// Check if the Easy Digital Downloads plugin is enabled.
	} elseif ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Set the meta key.
		$meta_key = '_edd_download_earnings';
	}

	// Get the sql filters.
	$sql_filters = psupsellmaster_bp_scores_get_sql_criteria_filters( $args );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( CAST( `postmeta`.`meta_value` AS DOUBLE ) ) AS `total`
		FROM
			%i AS `postmeta`
		INNER JOIN
			%i AS `posts`
		ON
			`postmeta`.`post_id` = `posts`.`ID`
		WHERE
			1 = 1
		AND
			`posts`.`post_type` = %s
		AND
			`posts`.`post_status` = %s
		AND
			`postmeta`.`meta_key` = %s
		',
		PsUpsellMaster_Database::get_table_name( 'postmeta' ),
		PsUpsellMaster_Database::get_table_name( 'posts' ),
		$post_type,
		$post_status,
		$meta_key
	);

	// Get the store sales.
	$store_sales = PsUpsellMaster_Database::get_var( $sql_query );

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			%s AS `criteria`,
			`candidates`.`product_id`,
			( %d * SUM( CAST( `postmeta`.`meta_value` AS DOUBLE ) / %f * 100 ) ) AS `score`
		FROM
			%i AS `candidates`
		INNER JOIN
			%i AS `postmeta`
		ON
			`candidates`.`product_id` = `postmeta`.`post_id`
		WHERE
			1 = 1
		{$sql_filters}
		AND
			`postmeta`.`meta_key` = %s
		GROUP BY
			`criteria`,
			`product_id`
		",
		$criteria,
		$weight,
		$store_sales,
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' ),
		PsUpsellMaster_Database::get_table_name( 'postmeta' ),
		$meta_key
	);

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the criteria order results.
 *
 * @param array $args the arguments.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_criteria_order_results( $args ) {
	// Set the criteria.
	$criteria = 'order-results';

	// Get the product id.
	$product_id = $args['product_id'];

	// Get the weight.
	$weight = $args['weight'];

	// Get the sql filters.
	$sql_filters = psupsellmaster_bp_scores_get_sql_criteria_filters( $args );

	// Set the sql sub query.
	$sql_sub_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `analytics`.`total_amount` ) AS `sum_total_amount`
		FROM
			%i AS `analytics`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_orders' )
	);

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			%s AS `criteria`,
			`candidates`.`product_id`,
			( %d * ( SUM( `analytics_orders`.`total_amount` ) / ( {$sql_sub_query} ) * 100 ) ) AS `score`
		FROM
			%i AS `candidates`
		INNER JOIN
			%i AS `analytics_orders`
		ON
			`candidates`.`product_id` = `analytics_orders`.`related_product_id`
		WHERE
			1 = 1
		{$sql_filters}
		AND
			`analytics_orders`.`order_product_id` = %d
		GROUP BY
			`criteria`,
			`product_id`
		",
		$criteria,
		$weight,
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' ),
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_orders' ),
		$product_id
	);

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the criteria upsell results.
 *
 * @param array $args the arguments.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_criteria_upsell_results( $args ) {
	// Set the criteria.
	$criteria = 'upsell-results';

	// Get the product id.
	$product_id = $args['product_id'];

	// Get the weight.
	$weight = $args['weight'];

	// Get the sql filters.
	$sql_filters = psupsellmaster_bp_scores_get_sql_criteria_filters( $args );

	// Set the sql sub query.
	$sql_sub_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			SUM( `analytics`.`total_amount` ) AS `sum_total_amount`
		FROM
			%i AS `analytics`
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' )
	);

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			%s AS `criteria`,
			`candidates`.`product_id`,
			( %d * ( SUM( `analytics_upsells`.`total_amount` ) / ( {$sql_sub_query} ) * 100 ) ) AS `score`
		FROM
			%i AS `candidates`
		INNER JOIN
			%i AS `analytics_upsells`
		ON
			`candidates`.`product_id` = `analytics_upsells`.`upsell_product_id`
		WHERE
			1 = 1
		{$sql_filters}
		AND
			`analytics_upsells`.`base_product_id` = %d
		GROUP BY
			`criteria`,
			`product_id`
		",
		$criteria,
		$weight,
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' ),
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' ),
		$product_id
	);

	// Return the sql query.
	return $sql_query;
}

/**
 * This function gets the sql query for the criteria taxonomy.
 *
 * @param array $args the arguments.
 * @return string the sql query.
 */
function psupsellmaster_bp_scores_get_sql_criteria_taxonomy( $args ) {
	// Get the product id.
	$product_id = $args['product_id'];

	// Get the taxonomy.
	$taxonomy = $args['taxonomy'];

	// Get the weight.
	$weight = $args['weight'];

	// Get the sql filters.
	$sql_filters = psupsellmaster_bp_scores_get_sql_criteria_filters( $args );

	// Set the sql sub query.
	$sql_sub_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`term_taxonomy`.`term_taxonomy_id`
		FROM
			%i AS `term_relationships`
		INNER JOIN
			%i AS `term_taxonomy`
		ON
			`term_taxonomy`.`term_taxonomy_id` = `term_relationships`.`term_taxonomy_id`
		WHERE
			1 = 1
		AND
			`term_relationships`.`object_id` = %d
		AND
			`term_taxonomy`.`taxonomy` = %s
		',
		PsUpsellMaster_Database::get_table_name( 'term_relationships' ),
		PsUpsellMaster_Database::get_table_name( 'term_taxonomy' ),
		$product_id,
		$taxonomy
	);

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		"
		SELECT
			%s AS `criteria`,
			`candidates`.`product_id`,
			( %d * COUNT( DISTINCT `term_relationships`.`term_taxonomy_id` ) ) AS `score`
		FROM
			%i AS `candidates`
		INNER JOIN
			%i AS `term_relationships`
		ON
			`term_relationships`.`object_id` = `candidates`.`product_id`
		WHERE
			1 = 1
		{$sql_filters}
		AND
			`term_relationships`.`term_taxonomy_id` IN ( {$sql_sub_query} )
		GROUP BY
			`criteria`,
			`product_id`
		",
		"taxonomy_{$taxonomy}",
		$weight,
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_temp_candidates' ),
		PsUpsellMaster_Database::get_table_name( 'term_relationships' )
	);

	// Return the sql query.
	return $sql_query;
}

/**
 * Get the count of how many products are done.
 *
 * @return int the count.
 */
function psupsellmaster_bp_scores_get_done_count() {
	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the post status.
	$post_status = 'publish';

	// Set the meta key.
	$meta_key = '_psupsellmaster_bp_scores_done';

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			COUNT( * ) AS `count`
		FROM
			%i AS `products`
		WHERE
			1 = 1
		AND
			`products`.`post_type` = %s
		AND
			`products`.`post_status` = %s
		AND
			EXISTS (
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
		',
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
 * Get the count of how many products are pending.
 *
 * @return int the count.
 */
function psupsellmaster_bp_scores_get_pending_count() {
	// Get the background process data.
	$data = get_option( 'psupsellmaster_bp_scores_data', array() );

	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Set the post status.
	$post_status = 'publish';

	// Set the meta key.
	$meta_key = '_psupsellmaster_bp_scores_done';

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
			COUNT( * ) AS `count`
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

	// Check if the resources does not exists.
	if ( ! psupsellmaster_bp_scores_required_resources_exists() ) {
		// Set the count to zero.
		$count = 0;
	}

	// Return the count.
	return $count;
}

/**
 * This function updates the status data for the background process.
 *
 * @param array $args the arguments.
 */
function psupsellmaster_bp_scores_update_status( $args = array() ) {
	// Get the action.
	$action = isset( $args['action'] ) ? $args['action'] : false;
	$action = in_array( $action, array( 'insert', 'update' ), true ) ? $action : 'update';

	// Get the count done.
	$count_done = psupsellmaster_bp_scores_get_done_count();

	// Get the count.
	$count = psupsellmaster_bp_scores_get_pending_count();

	// Check if this is the lite version.
	if ( psupsellmaster_is_lite() ) {
		// Get the base product limit.
		$base_product_limit = psupsellmaster_get_feature_limit( 'base_products_count' );

		// Check if the count of products pending has been reached.
		if ( $count > $base_product_limit ) {
			// Set the count.
			$count = $base_product_limit;
		}

		// Check if the count of products done has been reached.
		if ( $count_done >= $base_product_limit ) {
			// Set the count.
			$count = 0;
		}
	}

	// Set the transient name.
	$transient_name = 'psupsellmaster_bp_scores_status';

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
function psupsellmaster_bp_ajax_get_scores_status() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the message.
	$message = '0%';

	// Set the percentage.
	$percentage = 0;

	// Set the status.
	$status = 'none';

	// Get the tracking transient.
	$tracking = get_transient( 'psupsellmaster_bp_scores_tracking' );
	$tracking = filter_var( $tracking, FILTER_VALIDATE_BOOLEAN );

	// Check the tracking.
	if ( true === $tracking ) {
		// Check if the Easy Digital Downloads plugin is enabled.
		if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
			// Get the data.
			$data = get_transient( 'psupsellmaster_bp_edd_prices_status' );
			$data = is_array( $data ) ? $data : array();

			// Set the status.
			$status = ! empty( $data ) ? 'starting' : 'none';
		}

		// Get the data.
		$data = get_transient( 'psupsellmaster_bp_scores_status' );
		$data = is_array( $data ) ? $data : array();

		// Check the data.
		if ( ! empty( $data ) ) {
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
			$message = $is_done ? "{$percentage}%" : sprintf(
				/* translators: %1$d processing label, %2$d percentage, %3$d processed products, %4$d total products, %5$s products label. */
				'%1$s... %2$d%% (%3$d/%4$d %5$s)',
				__( 'Processing', 'psupsellmaster' ),
				$percentage,
				( $total - $pending ),
				$total,
				__( 'products', 'psupsellmaster' )
			);

			// Get the is stopping.
			$is_stopping = get_transient( 'psupsellmaster_bp_scores_stop' );
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
				psupsellmaster_bp_scores_delete_transients();
			}
		}
	}

	// Start the buffer.
	ob_start();

	// Check if the status is not none.
	if ( 'none' !== $status ) :
		?>
		<div class="psupsellmaster-bp-scores-progress">
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
add_action( 'wp_ajax_psupsellmaster_bp_ajax_get_scores_status', 'psupsellmaster_bp_ajax_get_scores_status' );

/**
 * This function triggers another function to try to start the background process.
 */
function psupsellmaster_bp_ajax_scores_start() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the products.
	$products = array();

	// Check if the products is set and is an array.
	if ( isset( $_POST['products'] ) && is_array( $_POST['products'] ) ) {
		// Get the products.
		$products = array_map( 'sanitize_text_field', wp_unslash( $_POST['products'] ) );
		$products = array_filter( array_unique( $products ) );
	}

	// Set the args.
	$args = array( 'tracking' => true );

	// Check if the products is not empty.
	if ( ! empty( $products ) ) {
		// Set the args.
		$args['products'] = $products;
	}

	// Maybe start the process.
	psupsellmaster_bp_scores_maybe_start( $args );

	// Send the response.
	wp_send_json( array() );
}
add_action( 'wp_ajax_psupsellmaster_bp_ajax_scores_start', 'psupsellmaster_bp_ajax_scores_start' );

/**
 * This function triggers another function to try to stop the background process.
 */
function psupsellmaster_bp_ajax_scores_stop() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Check if the Easy Digital Downloads plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Maybe stop the process.
		psupsellmaster_bp_edd_prices_maybe_stop();
	}

	// Maybe stop the process.
	psupsellmaster_bp_scores_maybe_stop();

	// Send the response.
	wp_send_json( array() );
}
add_action( 'wp_ajax_psupsellmaster_bp_ajax_scores_stop', 'psupsellmaster_bp_ajax_scores_stop' );

/**
 * Add the score-related background processes to the queue.
 * It uses the products argument to enqueue only specific products.
 *
 * @param array $args The arguments.
 */
function psupsellmaster_enqueue_scores_by_products( $args = array() ) {
	// Get the products.
	$products = isset( $args['products'] ) ? $args['products'] : array();

	// Set the queue args.
	$queue_args = array( 'products' => $products );

	// Get the queue.
	$queue = get_option( 'psupsellmaster_bp_queue', array() );
	$queue = is_array( $queue ) ? $queue : array();

	// Check if the Easy Digital Downloads plugin is enabled.
	if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
		// Add the background process to the queue.
		array_push(
			$queue,
			array(
				'key'  => 'edd_prices',
				'args' => $queue_args,
			)
		);
	}

	// Check the tracking.
	if ( isset( $args['tracking'] ) ) {
		// Set the queue args.
		$queue_args['tracking'] = $args['tracking'];
	}

	// Add the background process to the queue.
	array_push(
		$queue,
		array(
			'key'  => 'scores',
			'args' => $queue_args,
		)
	);

	// Update the option.
	update_option( 'psupsellmaster_bp_queue', $queue, false );

	// Maybe start the queue.
	psupsellmaster_bp_maybe_run_queue();
}

/**
 * Add the EDD Prices and the Scores background processes to the queue.
 * It uses the input through ajax to enqueue only specific products.
 */
function psupsellmaster_bp_ajax_enqueue_scores() {
	// Check the nonce.
	check_ajax_referer( 'psupsellmaster-ajax-nonce', 'nonce' );

	// Set the products.
	$products = array();

	// Check if the products is set and is an array.
	if ( isset( $_POST['products'] ) && is_array( $_POST['products'] ) ) {
		// Get the products.
		$products = array_map( 'sanitize_text_field', wp_unslash( $_POST['products'] ) );
		$products = array_filter( array_unique( $products ) );
	}

	// Set the track transient.
	psupsellmaster_bp_set_transient( 'psupsellmaster_bp_scores_tracking', true );

	// Set the args.
	$args = array(
		'products' => $products,
		'tracking' => true,
	);

	// Enqueue the background processes.
	psupsellmaster_enqueue_scores_by_products( $args );

	// Send the response.
	wp_send_json( array() );
}
add_action( 'wp_ajax_psupsellmaster_bp_ajax_enqueue_scores', 'psupsellmaster_bp_ajax_enqueue_scores' );
