<?php
/**
 * Functions - Database - Analytics - Upsells.
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
function psupsellmaster_db_analytics_upsells_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'base_product_id'   => 0,
		'upsell_product_id' => 0,
		'average_amount'    => 0.00,
		'total_amount'      => 0.00,
		'total_sales'       => 0,
		'created_at'        => current_time( 'mysql', true ),
		'updated_at'        => current_time( 'mysql', true ),
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Gets the database table columns.
 */
function psupsellmaster_db_analytics_upsells_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'base_product_id',
		'upsell_product_id',
		'average_amount',
		'total_amount',
		'total_sales',
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
function psupsellmaster_db_analytics_upsells_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `au`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' ) );

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
function psupsellmaster_db_analytics_upsells_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_analytics_upsells_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `od`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `od`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' ) );

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

		// Check if the base product id is not empty.
		if ( isset( $where['base_product_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`base_product_id` = %d', $where['base_product_id'] ) );
		}

		// Check if the upsell product id is not empty.
		if ( isset( $where['upsell_product_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`upsell_product_id` = %d', $where['upsell_product_id'] ) );
		}

		// Check if the average amount is not empty.
		if ( isset( $where['average_amount'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`average_amount` = %f', $where['average_amount'] ) );
		}

		// Check if the total amount is not empty.
		if ( isset( $where['total_amount'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`total_amount` = %f', $where['total_amount'] ) );
		}

		// Check if the total sales is not empty.
		if ( isset( $where['total_sales'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `od`.`total_sales` = %d', $where['total_sales'] ) );
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
function psupsellmaster_db_analytics_upsells_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_analytics_upsells_get_defaults();

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
function psupsellmaster_db_analytics_upsells_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_analytics_upsells_get_columns();

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
function psupsellmaster_db_analytics_upsells_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_analytics_upsells' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_analytics_upsells_get_columns();

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
function psupsellmaster_db_analytics_upsells_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_analytics_upsells' );
}

/**
 * Gets a record by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The record or null if not found.
 */
function psupsellmaster_db_get_analytics_upsell_by( $column, $value ) {
	// Set the analytics upsell.
	$analytics_upsell = false;

	// Get the analytics upsells.
	$analytics_upsells = psupsellmaster_db_analytics_upsells_select( array( $column => $value ) );

	// Check if the analytics upsells is an array.
	if ( is_array( $analytics_upsells ) ) {
		// Set the analytics upsell.
		$analytics_upsell = array_pop( $analytics_upsells );
	}

	// Return the analytics upsell.
	return $analytics_upsell;
}
