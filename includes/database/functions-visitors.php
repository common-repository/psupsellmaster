<?php
/**
 * Functions - Database - Visitors.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the default values for the visitors table columns.
 */
function psupsellmaster_db_visitors_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'user_id'    => '',
		'cookie'     => '',
		'ip'         => '',
		'visits'     => '',
		'created_at' => current_time( 'mysql', true ),
		'updated_at' => current_time( 'mysql', true ),
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Gets the visitors table columns.
 */
function psupsellmaster_db_visitors_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'user_id',
		'cookie',
		'ip',
		'visits',
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
function psupsellmaster_db_visitors_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `v`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' ) );

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
function psupsellmaster_db_visitors_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_visitors_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `v`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `v`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {

		// Check if the order id is not empty.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `v`.`id` = %d', $where['id'] ) );
		}

		// Check if the user id is not empty.
		if ( isset( $where['user_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `v`.`user_id` = %d', $where['user_id'] ) );
		}

		// Check if the cookie is not empty.
		if ( isset( $where['cookie'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `v`.`cookie` = %s', $where['cookie'] ) );
		}

		// Check if the ip is not empty.
		if ( isset( $where['ip'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `v`.`ip` = %s', $where['ip'] ) );
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
function psupsellmaster_db_visitors_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_visitors_get_defaults();

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
function psupsellmaster_db_visitors_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_visitors_get_columns();

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
function psupsellmaster_db_visitors_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_visitors_get_columns();

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
function psupsellmaster_db_visitors_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_visitors' );
}

/**
 * Gets a record by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The record or null if not found.
 */
function psupsellmaster_db_get_visitor_by( $column, $value ) {
	// Set the visitor.
	$visitor = false;

	// Get the visitors.
	$visitors = psupsellmaster_db_visitors_select( array( $column => $value ) );

	// Check if the visitors is an array.
	if ( is_array( $visitors ) ) {
		// Set the visitor.
		$visitor = array_pop( $visitors );
	}

	// Return the visitor.
	return $visitor;
}

/**
 * Gets a visitor meta data by using the received arguments.
 *
 * @param int    $visitor_id The visitor id.
 * @param string $meta_key   The meta key.
 * @param bool   $single     Whether to return a single value.
 * @return mixed An array of values if $single is false. The value of the meta data field if $single is true. False for an invalid $object_id (non-numeric, zero, or negative value). An empty string if the meta field for the object does not exist. An empty string if a valid but non-existing object ID is passed.
 */
function psupsellmaster_db_visitor_meta_select( $visitor_id, $meta_key = '', $single = false ) {
	return get_metadata( 'psupsellmaster_visitor', $visitor_id, $meta_key, $single );
}

/**
 * Inserts a visitor meta data by using the received arguments.
 *
 * @param int    $visitor_id The visitor id.
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @param bool   $unique     Whether the same key should not be added.
 * @return int|false The meta ID on success, false on failure.
 */
function psupsellmaster_db_visitor_meta_insert( $visitor_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'psupsellmaster_visitor', $visitor_id, $meta_key, $meta_value, $unique );
}

/**
 * Updates a visitor meta data by using the received arguments.
 *
 * @param int    $visitor_id The visitor id.
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @param mixed  $prev_value The previous value.
 * @return int|false The meta ID if the key didn't exist, true on successful update, false on failure.
 */
function psupsellmaster_db_visitor_meta_update( $visitor_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'psupsellmaster_visitor', $visitor_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Deletes a visitor meta data by using the received arguments.
 *
 * @param int    $visitor_id The visitor id.
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @return bool True on success, false on failure.
 */
function psupsellmaster_db_visitor_meta_delete( $visitor_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'psupsellmaster_visitor', $visitor_id, $meta_key, $meta_value );
}

/**
 * Gets the current visitor's meta data by using the received arguments.
 *
 * @param string $meta_key   The meta key.
 * @param bool   $single     Whether to return a single value.
 * @return mixed An array of values if $single is false. The value of the meta data field if $single is true. False for an invalid $object_id (non-numeric, zero, or negative value). An empty string if the meta field for the object does not exist. An empty string if a valid but non-existing object ID is passed.
 */
function psupsellmaster_db_current_visitor_meta_select( $meta_key = '', $single = false ) {
	// Get the visitor id.
	$visitor_id = psupsellmaster_get_current_visitor_id();

	// Return the select function call.
	return psupsellmaster_db_visitor_meta_select( $visitor_id, $meta_key, $single );
}

/**
 * Inserts a current visitor's meta data by using the received arguments.
 *
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @param bool   $unique     Whether the same key should not be added.
 * @return int|false The meta ID on success, false on failure.
 */
function psupsellmaster_db_current_visitor_meta_insert( $meta_key, $meta_value, $unique = false ) {
	// Get the visitor id.
	$visitor_id = psupsellmaster_get_current_visitor_id();

	// Return the insert function call.
	return psupsellmaster_db_visitor_meta_insert( $visitor_id, $meta_key, $meta_value, $unique );
}

/**
 * Updates a current visitor's meta data by using the received arguments.
 *
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @param mixed  $prev_value The previous value.
 * @return int|false The meta ID if the key didn't exist, true on successful update, false on failure.
 */
function psupsellmaster_db_current_visitor_meta_update( $meta_key, $meta_value, $prev_value = '' ) {
	// Get the visitor id.
	$visitor_id = psupsellmaster_get_current_visitor_id();

	// Return the update function call.
	return psupsellmaster_db_visitor_meta_update( $visitor_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Deletes the current visitor's meta data by using the received arguments.
 *
 * @param string $meta_key   The meta key.
 * @param mixed  $meta_value The meta value.
 * @return bool True on success, false on failure.
 */
function psupsellmaster_db_current_visitor_meta_delete( $meta_key, $meta_value = '' ) {
	// Get the visitor id.
	$visitor_id = psupsellmaster_get_current_visitor_id();

	// Return the delete function call.
	return psupsellmaster_db_visitor_meta_delete( $visitor_id, $meta_key, $meta_value );
}
