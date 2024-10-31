<?php
/**
 * Functions - Database - Results.
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
function psupsellmaster_db_results_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'order_id'        => 0,
		'order_item_id'   => 0,
		'customer_id'     => 0,
		'product_id'      => 0,
		'variation_id'    => 0,
		'base_product_id' => 0,
		'campaign_id'     => 0,
		'amount'          => 0.00,
		'location'        => '',
		'source'          => '',
		'type'            => '',
		'view'            => '',
		'store'           => '',
		'created_at'      => current_time( 'mysql', true ),
		'updated_at'      => current_time( 'mysql', true ),
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Gets the database table columns.
 */
function psupsellmaster_db_results_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'order_id',
		'order_item_id',
		'customer_id',
		'product_id',
		'variation_id',
		'base_product_id',
		'campaign_id',
		'amount',
		'location',
		'source',
		'type',
		'view',
		'store',
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
function psupsellmaster_db_results_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `r`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) );

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
function psupsellmaster_db_results_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_results_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `od`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `od`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {

		// Check if the id is not empty.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`id` = %d', $where['id'] ) );
		}

		// Check if the order id is not empty.
		if ( isset( $where['order_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`order_id` = %d', $where['order_id'] ) );
		}

		// Check if the order item id is not empty.
		if ( isset( $where['order_item_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`order_item_id` = %d', $where['order_item_id'] ) );
		}

		// Check if the customer id is not empty.
		if ( isset( $where['customer_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`customer_id` = %d', $where['customer_id'] ) );
		}

		// Check if the product id is not empty.
		if ( isset( $where['product_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`product_id` = %d', $where['product_id'] ) );
		}

		// Check if the variation id is not empty.
		if ( isset( $where['variation_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`variation_id` = %d', $where['variation_id'] ) );
		}

		// Check if the base product id is not empty.
		if ( isset( $where['base_product_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`base_product_id` = %d', $where['base_product_id'] ) );
		}

		// Check if the campaign id is not empty.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the amount is not empty.
		if ( isset( $where['amount'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`amount` = %f', $where['amount'] ) );
		}

		// Check if the location is not empty.
		if ( isset( $where['location'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`location` = %s', $where['location'] ) );
		}

		// Check if the source is not empty.
		if ( isset( $where['source'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`source` = %s', $where['source'] ) );
		}

		// Check if the type is not empty.
		if ( isset( $where['type'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`type` = %s', $where['type'] ) );
		}

		// Check if the view is not empty.
		if ( isset( $where['view'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`view` = %s', $where['view'] ) );
		}

		// Check if the store is not empty.
		if ( isset( $where['store'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`store` = %s', $where['store'] ) );
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
			array_push( $sql_order_by, "{$column} {$sorting}" );
		}
	}

	// Build the SQL where.
	$sql_where = implode( ' ', $sql_where );
	$sql_where = "WHERE 1 = 1 {$sql_where}";

	// Build the SQL order by.
	$sql_order_by = implode( ', ', $sql_order_by );
	$sql_order_by = ! empty( $sql_order_by ) ? "ORDER BY {$sql_order_by}" : '';

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from} {$sql_where} {$sql_order_by}";

	// Return the results.
	return PsUpsellMaster_Database::get_results( $sql_query );
}

/**
 * Inserts a record into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_results_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_results_get_defaults();

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
function psupsellmaster_db_results_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_results_get_columns();

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
function psupsellmaster_db_results_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_results_get_columns();

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
function psupsellmaster_db_results_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_results' );
}

/**
 * Gets a record by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The record or null if not found.
 */
function psupsellmaster_db_get_result_by( $column, $value ) {
	// Set the result.
	$result = false;

	// Get the results.
	$results = psupsellmaster_db_results_select( array( $column => $value ) );

	// Check if the results is an array.
	if ( is_array( $results ) ) {
		// Set the result.
		$result = array_pop( $results );
	}

	// Return the result.
	return $result;
}

/**
 * Gets a result meta data by using the received arguments.
 *
 * @param int    $result_id  The result id.
 * @param string $meta_key   The meta key.
 * @param bool   $single     Whether to return a single value.
 * @return mixed An array of values if $single is false. The value of the meta data field if $single is true. False for an invalid $object_id (non-numeric, zero, or negative value). An empty string if the meta field for the object does not exist. An empty string if a valid but non-existing object ID is passed.
 */
function psupsellmaster_db_result_meta_select( $result_id, $meta_key = '', $single = false ) {
	return get_metadata( 'psupsellmaster_result', $result_id, $meta_key, $single );
}

/**
 * Inserts a result meta data by using the received arguments.
 *
 * @param int    $result_id  The result id.
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @param bool   $unique     Whether the same key should not be added.
 * @return int|false The meta ID on success, false on failure.
 */
function psupsellmaster_db_result_meta_insert( $result_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'psupsellmaster_result', $result_id, $meta_key, $meta_value, $unique );
}

/**
 * Updates a result meta data by using the received arguments.
 *
 * @param int    $result_id  The result id.
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @param mixed  $prev_value The previous value.
 * @return int|false The meta ID if the key didn't exist, true on successful update, false on failure.
 */
function psupsellmaster_db_result_meta_update( $result_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'psupsellmaster_result', $result_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Deletes a result meta data by using the received arguments.
 *
 * @param int    $result_id  The result id.
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @return bool True on success, false on failure.
 */
function psupsellmaster_db_result_meta_delete( $result_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'psupsellmaster_result', $result_id, $meta_key, $meta_value );
}
