<?php
/**
 * Functions - Database - Scores.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the default values for the database table columns.
 */
function psupsellmaster_db_scores_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'base_product_id'   => '',
		'upsell_product_id' => '',
		'criteria'          => '',
		'score'             => '',
		'created_at'        => current_time( 'mysql', true ),
		'updated_at'        => current_time( 'mysql', true ),
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Gets the database table columns.
 */
function psupsellmaster_db_scores_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'base_product_id',
		'upsell_product_id',
		'criteria',
		'score',
		'created_at',
		'updated_at',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_scores_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `s`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Gets the records from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The records from the database table.
 */
function psupsellmaster_db_scores_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_scores_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `s`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `s`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_orderby = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the order id is not empty.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `s`.`id` = %d', $where['id'] ) );
		}

		// Check if the base product id is not empty.
		if ( isset( $where['base_product_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `s`.`base_product_id` = %d', $where['base_product_id'] ) );
		}

		// Check if the upsell product id is not empty.
		if ( isset( $where['upsell_product_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `s`.`upsell_product_id` = %d', $where['upsell_product_id'] ) );
		}

		// Check if the criteria is not empty.
		if ( isset( $where['criteria'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `s`.`criteria` = %s', $where['criteria'] ) );
		}

		// Check if the score is not empty.
		if ( isset( $where['score'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `s`.`score` = %f', $where['score'] ) );
		}
	}

	// Check if the order by is not empty.
	if ( ! empty( $order_by ) ) {
		// Set the valid sortings.
		$sortings = array( 'ASC', 'DESC' );

		// Transform a string to uppercase.
		$order_by = array_map( 'strtoupper', $order_by );

		// Remove invalid order type entries.
		$order_by = array_flip( array_intersect_key( array_flip( $order_by ), array_flip( $sortings ) ) );

		// Remove invalid table column entries.
		$order_by = array_intersect_key( $order_by, array_flip( $table_columns ) );

		// Loop through the order by.
		foreach ( $order_by as $column => $sorting ) {
			// Add the order by to the SQL order by list.
			array_push( $sql_orderby, "{$column} {$sorting}" );
		}
	}

	// Build the SQL where.
	$sql_where = implode( ' ', $sql_where );
	$sql_where = "WHERE 1 = 1 {$sql_where}";

	// Build the SQL order by.
	$sql_orderby = implode( ', ', $sql_orderby );
	$sql_orderby = ! empty( $sql_orderby ) ? "ORDER BY {$sql_orderby}" : '';

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_where} {$sql_orderby}";

	// Return the results.
	return PsUpsellMaster_Database::get_results( $sql_query );
}

/**
 * Inserts a record into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_scores_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_scores_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Updates a record in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_scores_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_scores_get_columns();

	// Set the update defaults.
	$update_defaults = array( 'updated_at' => current_time( 'mysql', true ) );

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Deletes a record from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_scores_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_scores' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_scores_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncates the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_scores_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_scores' );
}

/**
 * Gets a record by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The record or null if not found.
 */
function psupsellmaster_db_get_score_by( $column, $value ) {
	// Set the score.
	$score = false;

	// Get the scores.
	$scores = psupsellmaster_db_scores_select( array( $column => $value ) );

	// Check if the scores is an array.
	if ( is_array( $scores ) ) {
		// Set the score.
		$score = array_pop( $scores );
	}

	// Return the score.
	return $score;
}

/**
 * Gets a score meta data by using the received arguments.
 *
 * @param int    $score_id The score id.
 * @param string $meta_key   The meta key.
 * @param bool   $single     Whether to return a single value.
 * @return mixed An array of values if $single is false. The value of the meta data field if $single is true. False for an invalid $object_id (non-numeric, zero, or negative value). An empty string if the meta field for the object does not exist. An empty string if a valid but non-existing object ID is passed.
 */
function psupsellmaster_db_score_meta_select( $score_id, $meta_key = '', $single = false ) {
	return get_metadata( 'psupsellmaster_score', $score_id, $meta_key, $single );
}

/**
 * Inserts a score meta data by using the received arguments.
 *
 * @param int    $score_id The score id.
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @param bool   $unique     Whether the same key should not be added.
 * @return int|false The meta ID on success, false on failure.
 */
function psupsellmaster_db_score_meta_insert( $score_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'psupsellmaster_score', $score_id, $meta_key, $meta_value, $unique );
}

/**
 * Updates a score meta data by using the received arguments.
 *
 * @param int    $score_id The score id.
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @param mixed  $prev_value The previous value.
 * @return int|false The meta ID if the key didn't exist, true on successful update, false on failure.
 */
function psupsellmaster_db_score_meta_update( $score_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'psupsellmaster_score', $score_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Deletes a score meta data by using the received arguments.
 *
 * @param int    $score_id The score id.
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @return bool True on success, false on failure.
 */
function psupsellmaster_db_score_meta_delete( $score_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'psupsellmaster_score', $score_id, $meta_key, $meta_value );
}
