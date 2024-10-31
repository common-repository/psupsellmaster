<?php
/**
 * Functions - Database - Campaigns.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//
// Campaigns.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaigns_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'title'      => '',
		'status'     => '',
		'priority'   => 0,
		'start_date' => '',
		'end_date'   => '',
		'created_at' => current_time( 'mysql', true ),
		'updated_at' => current_time( 'mysql', true ),
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaigns_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'title',
		'status',
		'priority',
		'start_date',
		'end_date',
		'coupon_code',
		'coupon_type',
		'coupon_amount',
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
function psupsellmaster_db_campaigns_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `c`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaigns_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaigns_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `c`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `c`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `c`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['title'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `c`.`title` = %s', $where['title'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['status'] ) ) {
			// Check if the data is an array.
			if ( is_array( $where['status'] ) ) {
				// Set the placeholders.
				$placeholders = implode( ', ', array_fill( 0, count( $where['status'] ), '%s' ) );

				// Add conditions to the SQL where.
				array_push( $sql_where, PsUpsellMaster_Database::prepare( "AND `c`.`status` IN ( {$placeholders} )", $where['status'] ) );

				// Otherwise...
			} else  {
				// Add conditions to the SQL where.
				array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `c`.`status` = %s', $where['status'] ) );
			}
		}

		// Check if the data exists.
		if ( isset( $where['priority'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `c`.`priority` = %d', $where['priority'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['coupon_code'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `c`.`coupon_code` = %s', $where['coupon_code'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['coupon_type'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `c`.`coupon_type` = %s', $where['coupon_type'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['coupon_amount'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `c`.`coupon_amount` = %f', $where['coupon_amount'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaigns_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaigns_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaigns_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaigns_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaigns_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaigns_get_columns();

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
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaigns_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaigns_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaigns_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaigns' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaigns_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaigns_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Meta - Helper functions (similar to WordPress meta functions).
//

/**
 * Get a campaign meta data by using the received arguments.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $meta_key    The meta key.
 * @param bool   $single      Whether to return a single value.
 * @return mixed An array of values if $single is false. The value of the meta data field if $single is true. False for an invalid $object_id (non-numeric, zero, or negative value). An empty string if the meta field for the object does not exist. An empty string if a valid but non-existing object ID is passed.
 */
function psupsellmaster_db_campaign_meta_select( $campaign_id, $meta_key = '', $single = false ) {
	return get_metadata( 'psupsellmaster_campaign', $campaign_id, $meta_key, $single );
}

/**
 * Insert a campaign meta data by using the received arguments.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $meta_key    The meta key.
 * @param mixed  $meta_value  The meta value.
 * @param bool   $unique      Whether the same key should not be added.
 * @return int|false The meta ID on success, false on failure.
 */
function psupsellmaster_db_campaign_meta_insert( $campaign_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'psupsellmaster_campaign', $campaign_id, $meta_key, $meta_value, $unique );
}

/**
 * Update a campaign meta data by using the received arguments.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $meta_key    The meta key.
 * @param mixed  $meta_value  The meta value.
 * @param mixed  $prev_value  The previous value.
 * @return int|false The meta ID if the key didn't exist, true on successful update, false on failure.
 */
function psupsellmaster_db_campaign_meta_update( $campaign_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'psupsellmaster_campaign', $campaign_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Delete a campaign meta data by using the received arguments.
 *
 * @param int    $campaign_id The campaign id.
 * @param string $meta_key    The meta key.
 * @param mixed  $meta_value  The meta value.
 * @return bool True on success, false on failure.
 */
function psupsellmaster_db_campaign_meta_delete( $campaign_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'psupsellmaster_campaign', $campaign_id, $meta_key, $meta_value );
}

//
// Campaignmeta.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaignmeta_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'psupsellmaster_campaign_id' => '',
		'meta_key'                   => '',
		'meta_value'                 => '',
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaignmeta_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'psupsellmaster_campaign_id',
		'meta_key',
		'meta_value',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaignmeta_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cm`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaignmeta_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaignmeta_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `cm`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cm`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cm`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cm`.`psupsellmaster_campaign_id` = %d', $where['psupsellmaster_campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['psupsellmaster_campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cm`.`psupsellmaster_campaign_id` = %d', $where['psupsellmaster_campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['meta_key'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cm`.`meta_key` = %s', $where['meta_key'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['meta_value'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cm`.`meta_value` = %s', $where['meta_value'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaignmeta_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaignmeta_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaignmeta_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaignmeta_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaignmeta_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaignmeta_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaignmeta_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaignmeta' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaignmeta_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaignmeta_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaignmeta' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaignmeta_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaignmeta_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Eligible Products.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_eligible_products_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id' => '',
		'product_id'  => '',
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_eligible_products_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'product_id',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_eligible_products_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cep`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_eligible_products_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_eligible_products_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `cep`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cep`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cep`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cep`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['product_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cep`.`product_id` = %d', $where['product_id'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_eligible_products_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_eligible_products_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_eligible_products_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_eligible_products_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_eligible_products_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_eligible_products_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_eligible_products_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_eligible_products' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_eligible_products_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_eligible_products_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_eligible_products' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_eligible_products_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_eligible_products_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Coupons.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_coupons_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id' => '',
		'coupon_id'   => '',
		'code'        => '',
		'type'        => '',
		'amount'      => 0,
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_coupons_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'coupon_id',
		'code',
		'type',
		'amount',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_coupons_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cc`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_coupons_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_coupons_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `cc`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cc`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['coupon_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`coupon_id` = %d', $where['coupon_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['code'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`code` = %s', $where['code'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['type'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`type` = %s', $where['type'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['amount'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`amount` = %d', $where['amount'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_coupons_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_coupons_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_coupons_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_coupons_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_coupons_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_coupons_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_coupons_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_coupons' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_coupons_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_coupons_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_coupons' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_coupons_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_coupons_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Products.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_products_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id' => '',
		'product_id'  => '',
		'type'        => '',
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_products_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'product_id',
		'type',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_products_count() {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_products' );

	// Set the table alias.
	$table_alias = 'cp';

	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = "FROM `{$table_name}` AS `cp`";

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_products_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_products_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `cp`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cp`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cp`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cp`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['product_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cp`.`product_id` = %d', $where['product_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['type'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cp`.`type` = %s', $where['type'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_products_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_products_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_products_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_products' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_products_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_products_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_products' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_products_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_products_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_products' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_products_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_products_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_products' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_products_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_products_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Authors.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_authors_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id' => '',
		'author_id'   => '',
		'type'        => '',
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_authors_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'author_id',
		'type',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_authors_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `ca`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_authors_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_authors_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `ca`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `ca`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ca`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ca`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['author_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ca`.`author_id` = %d', $where['author_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['type'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ca`.`type` = %s', $where['type'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_authors_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_authors_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_authors_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_authors' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_authors_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_authors_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_authors' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_authors_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_authors_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_authors' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_authors_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_authors_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_authors' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_authors_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_authors_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Taxonomies.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_taxonomies_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id' => '',
		'term_id'     => '',
		'taxonomy'    => '',
		'type'        => '',
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_taxonomies_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'term_id',
		'taxonomy',
		'type',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_taxonomies_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `ct`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_taxonomies_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_taxonomies_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `ct`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `ct`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ct`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ct`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['term_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ct`.`term_id` = %d', $where['term_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['taxonomy'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ct`.`taxonomy` = %s', $where['taxonomy'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['type'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ct`.`type` = %s', $where['type'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_taxonomies_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_taxonomies_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_taxonomies_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_taxonomies' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_taxonomies_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_taxonomies_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_taxonomies' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_taxonomies_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_taxonomies_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_taxonomies' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_taxonomies_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_taxonomies_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_taxonomies' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_taxonomies_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_taxonomies_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Locations.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_locations_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id' => '',
		'location'    => '',
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_locations_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'location',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_locations_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cl`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_locations_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_locations_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `cl`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cl`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cl`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cl`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['location'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cl`.`location` = %s', $where['location'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_locations_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_locations_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_locations_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_locations' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_locations_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_locations_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_locations' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_locations_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_locations_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_locations' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_locations_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_locations_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_locations' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_locations_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_locations_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Weekdays.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_weekdays_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id' => '',
		'weekday'     => '',
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_weekdays_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'weekday',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_weekdays_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cw`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_weekdays_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_weekdays_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `cw`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cw`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cw`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cw`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['weekday'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cw`.`weekday` = %s', $where['weekday'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_weekdays_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_weekdays_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_weekdays_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_weekdays_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_weekdays_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_weekdays_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_weekdays_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_weekdays' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_weekdays_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_weekdays_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_weekdays' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_weekdays_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_weekdays_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Carts.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_carts_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id'   => '',
		'cart_key'      => '',
		'last_modified' => '',
		'order_id'      => '',
		'status'        => '',
		'quantity'      => 0,
		'subtotal'      => 0,
		'discount'      => 0,
		'tax'           => 0,
		'total'         => 0,
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_carts_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'cart_key',
		'last_modified',
		'order_id',
		'status',
		'quantity',
		'subtotal',
		'discount',
		'tax',
		'total',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_carts_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cc`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_carts_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_carts_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `cc`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cc`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['cart_key'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`cart_key` = %s', $where['cart_key'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['last_modified'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`last_modified` = %s', $where['last_modified'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['order_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`order_id` = %d', $where['order_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['status'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`status` = %s', $where['status'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['quantity'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`quantity` = %d', $where['quantity'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['subtotal'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`subtotal` = %f', $where['subtotal'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['discount'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`discount` = %f', $where['discount'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['tax'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`tax` = %f', $where['tax'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['total'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cc`.`total` = %f', $where['total'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_carts_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_carts_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_carts_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_carts_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_carts_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_carts_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_carts_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_carts' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_carts_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_carts_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_carts' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_carts_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_carts_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Events.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_events_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id' => '',
		'event_date'  => '',
		'event_name'  => '',
		'location'    => '',
		'quantity'    => 0,
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_events_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'event_date',
		'event_name',
		'location',
		'quantity',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_events_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `ce`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_events_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_events_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `ce`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `ce`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ce`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ce`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['event_date'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ce`.`event_date` = %s', $where['event_date'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['event_name'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ce`.`event_name` = %s', $where['event_name'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['location'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ce`.`location` = %s', $where['location'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['quantity'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ce`.`quantity` = %d', $where['quantity'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_events_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_events_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_events_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_events_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_events_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_events_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_events_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_events' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_events_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_events_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_events' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_events_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_events_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Display Options.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_display_options_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'campaign_id'  => '',
		'location'     => '',
		'option_name'  => '',
		'option_value' => '',
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_display_options_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'campaign_id',
		'location',
		'option_name',
		'option_value',
	);

	// Return the columns.
	return $columns;
}

/**
 * Gets the count of records from the database table.
 *
 * @return int The count of records from the database table.
 */
function psupsellmaster_db_campaign_display_options_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cdo`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_display_options_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_display_options_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `cdo`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `cdo`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cdo`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['campaign_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cdo`.`campaign_id` = %d', $where['campaign_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['location'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cdo`.`location` = %s', $where['location'] ) );
		}
		// Check if the data exists.
		if ( isset( $where['option_name'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cdo`.`option_name` = %s', $where['option_name'] ) );
		}
		// Check if the data exists.
		if ( isset( $where['option_value'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `cdo`.`option_value` = %s', $where['option_value'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_display_options_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_display_options_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_display_options_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_display_options' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_display_options_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_display_options_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_display_options' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_display_options_get_columns();

	// Set the update defaults.
	$update_defaults = array();

	// Set the table data.
	$table_data = array_intersect_key( $data, array_flip( $table_columns ) );
	$table_data = array_merge( $update_defaults, $table_data );

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Update into the database.
	return PsUpsellMaster_Database::update( $table_name, $table_data, $table_where );
}

/**
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_display_options_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_display_options' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_display_options_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_display_options_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_display_options' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_display_options_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_display_options_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Templates.
//

/**
 * Get the default values for the database table columns.
 */
function psupsellmaster_db_campaign_templates_get_defaults() {
	// Set the defaults.
	$defaults = array(
		'user_id'    => '',
		'title'      => '',
		'created_at' => current_time( 'mysql', true ),
		'updated_at' => current_time( 'mysql', true ),
	);

	// Return the defaults.
	return $defaults;
}

/**
 * Get the database table columns.
 */
function psupsellmaster_db_campaign_templates_get_columns() {
	// Set the columns.
	$columns = array(
		'id',
		'user_id',
		'title',
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
function psupsellmaster_db_campaign_templates_count() {
	// Set the SQL select.
	$sql_select = 'SELECT COUNT(*)';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `ct`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ) );

	// Build the SQL query.
	$sql_query = "{$sql_select} {$sql_from}";

	// Return the count.
	return PsUpsellMaster_Database::get_var( $sql_query );
}

/**
 * Get the rows from the database table based on the parameters.
 *
 * @param array $where    The where conditions.
 * @param array $order_by The order by conditions.
 * @return array The rows from the database table.
 */
function psupsellmaster_db_campaign_templates_select( $where = array(), $order_by = array() ) {
	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_templates_get_columns();

	// Set the SQL select.
	$sql_select = 'SELECT `ct`.*';

	// Set the SQL from.
	$sql_from = PsUpsellMaster_Database::prepare( 'FROM %i AS `ct`', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templates' ) );

	// Set the SQL where.
	$sql_where = array();

	// Set the SQL order by.
	$sql_order_by = array();

	// Check if the where is not empty.
	if ( ! empty( $where ) ) {
		// Check if the data exists.
		if ( isset( $where['id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ct`.`id` = %d', $where['id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['user_id'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ct`.`user_id` = %d', $where['user_id'] ) );
		}

		// Check if the data exists.
		if ( isset( $where['title'] ) ) {
			// Add conditions to the SQL where.
			array_push( $sql_where, PsUpsellMaster_Database::prepare( 'AND `ct`.`title` = %s', $where['title'] ) );
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
 * Check if a row from the database table exists based on the parameters.
 *
 * @param array $where The where conditions.
 * @return bool True if the row exists, false otherwise.
 */
function psupsellmaster_db_campaign_templates_exists( $where ) {
	// Set the exists.
	$exists = ! empty( psupsellmaster_db_campaign_templates_select( $where ) );

	// Return the exists.
	return $exists;
}

/**
 * Insert a row into the database table.
 *
 * @param array $data The data to insert.
 * @return int The number of rows inserted.
 */
function psupsellmaster_db_campaign_templates_insert( $data ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templates' );

	// Get the table defaults.
	$table_defaults = psupsellmaster_db_campaign_templates_get_defaults();

	// Set the table data.
	$table_data = wp_parse_args( $data, $table_defaults );

	// Insert into the database.
	return PsUpsellMaster_Database::insert( $table_name, $table_data );
}

/**
 * Update a row in the database table.
 *
 * @param array $data  The data to update.
 * @param array $where The where conditions.
 * @return int The number of rows updated.
 */
function psupsellmaster_db_campaign_templates_update( $data, $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templates' );

	// Get the table columns.
	$table_columns = psupsellmaster_db_campaign_templates_get_columns();

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
 * Delete a row from the database table.
 *
 * @param array $where The where conditions.
 * @return int The number of rows deleted.
 */
function psupsellmaster_db_campaign_templates_delete( $where ) {
	// Set the table name.
	$table_name = PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaign_templates' );

	// Set the table columns.
	$table_columns = psupsellmaster_db_campaign_templates_get_columns();

	// Set the table where.
	$table_where = array_intersect_key( $where, array_flip( $table_columns ) );

	// Delete from the database.
	return PsUpsellMaster_Database::delete( $table_name, $table_where );
}

/**
 * Truncate the database table.
 *
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_campaign_templates_truncate() {
	// Truncate the database table.
	return psupsellmaster_db_truncate_table( 'psupsellmaster_campaign_templates' );
}

/**
 * Get a row by a column and a value.
 *
 * @param string $column The column name.
 * @param mixed  $value  The column value.
 * @return object|null The row or null if not found.
 */
function psupsellmaster_db_campaign_templates_get_row_by( $column, $value ) {
	// Set the row.
	$row = false;

	// Get the rows.
	$rows = psupsellmaster_db_campaign_templates_select( array( $column => $value ) );

	// Check if the rows is an array.
	if ( is_array( $rows ) ) {
		// Set the row.
		$row = array_pop( $rows );
	}

	// Return the row.
	return $row;
}

//
// Campaign Template Meta - Helper functions (similar to WordPress meta functions).
//

/**
 * Get a campaign template meta data by using the received arguments.
 *
 * @param int    $template_id The template id.
 * @param string $meta_key    The meta key.
 * @param bool   $single      Whether to return a single value.
 * @return mixed An array of values if $single is false. The value of the meta data field if $single is true. False for an invalid $object_id (non-numeric, zero, or negative value). An empty string if the meta field for the object does not exist. An empty string if a valid but non-existing object ID is passed.
 */
function psupsellmaster_db_campaign_template_meta_select( $template_id, $meta_key = '', $single = false ) {
	return get_metadata( 'psupsellmaster_campaign_template', $template_id, $meta_key, $single );
}

/**
 * Insert a campaign template meta data by using the received arguments.
 *
 * @param int    $template_id The template id.
 * @param string $meta_key    The meta key.
 * @param mixed  $meta_value  The meta value.
 * @param bool   $unique      Whether the same key should not be added.
 * @return int|false The meta ID on success, false on failure.
 */
function psupsellmaster_db_campaign_template_meta_insert( $template_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'psupsellmaster_campaign_template', $template_id, $meta_key, $meta_value, $unique );
}

/**
 * Update a campaign template meta data by using the received arguments.
 *
 * @param int    $template_id The template id.
 * @param string $meta_key    The meta key.
 * @param mixed  $meta_value  The meta value.
 * @param mixed  $prev_value  The previous value.
 * @return int|false The meta ID if the key didn't exist, true on successful update, false on failure.
 */
function psupsellmaster_db_campaign_template_meta_update( $template_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'psupsellmaster_campaign_template', $template_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Delete a campaign template meta data by using the received arguments.
 *
 * @param int    $template_id The template id.
 * @param string $meta_key    The meta key.
 * @param mixed  $meta_value  The meta value.
 * @return bool True on success, false on failure.
 */
function psupsellmaster_db_campaign_template_meta_delete( $template_id, $meta_key, $meta_value = '' ) {
	return delete_metadata( 'psupsellmaster_campaign_template', $template_id, $meta_key, $meta_value );
}
