<?php
/**
 * Class - Database.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PsUpsellMaster_Database class.
 */
class PsUpsellMaster_Database {

	/**
	 * Deletes a row in the table.
	 *
	 * @param string          $table        Table name.
	 * @param array           $where        A named array of WHERE clauses (in column => value pairs).
	 *                                      Multiple clauses will be joined with ANDs.
	 *                                      Both $where columns and $where values should be "raw".
	 *                                      Sending a null value will create an IS NULL comparison - the corresponding
	 *                                      format will be ignored in this case.
	 * @param string[]|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
	 *                                      If string, that format will be used for all of the items in $where.
	 *                                      A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                      If omitted, all values in $data will be treated as strings unless otherwise
	 *                                      specified in wpdb::$field_types. Default null.
	 * @return int|false The number of rows deleted, or false on error.
	 */
	public static function delete( $table, $where, $where_format = null ) {
		return call_user_func_array( array( static::get_handler(), 'delete' ), array( $table, $where, $where_format ) );
	}

	/**
	 * First half of escaping for `LIKE` special characters `%` and `_` before preparing for SQL.
	 *
	 * @param string $text The raw text to be escaped. The input typed by the user
	 *                     should have no extra or deleted slashes.
	 * @return string Text in the form of a LIKE phrase. The output is not SQL safe.
	 *                Call wpdb::prepare() or wpdb::_real_escape() next.
	 */
	public static function esc_like( $text ) {
		return call_user_func( array( static::get_handler(), 'esc_like' ), $text );
	}

	/**
	 * Retrieves the database character collate.
	 *
	 * @since 3.5.0
	 *
	 * @return string The database character collate.
	 */
	public static function get_charset_collate() {
		return call_user_func( array( static::get_handler(), 'get_charset_collate' ) );
	}

	/**
	 * Retrieves one column from the database.
	 *
	 * @param string|null $query Optional. SQL query. Defaults to previous query.
	 * @param int         $x     Optional. Column to return. Indexed from 0. Default 0.
	 * @return array Database query result. Array indexed from 0 by SQL result row number.
	 */
	public static function get_col( $query = null, $x = 0 ) {
		return call_user_func( array( static::get_handler(), 'get_col' ), $query, $x );
	}

	/**
	 * Retrieves the database handler.
	 *
	 * @return object The database handler.
	 */
	public static function &get_handler() {
		global $wpdb;

		// Return the handler.
		return $wpdb;
	}

	/**
	 * Retrieves the ID generated for an AUTO_INCREMENT column by the last query (usually INSERT).
	 *
	 * @return int The ID.
	 */
	public static function get_insert_id() {
		// Set the id.
		$id = false;

		// Get the handler.
		$handler = static::get_handler();

		// Check the id.
		if ( isset( $handler->insert_id ) ) {
			// Set the id.
			$id = $handler->insert_id;
		}

		// Return the id.
		return $id;
	}

	/**
	 * Retrieves an entire SQL result set from the database (i.e., many rows).
	 *
	 * @param string $query  SQL query.
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.
	 *                       With one of the first three, return an array of rows indexed
	 *                       from 0 by SQL result row number. Each row is an associative array
	 *                       (column => value, ...), a numerically indexed array (0 => value, ...),
	 *                       or an object ( ->column = value ), respectively. With OBJECT_K,
	 *                       return an associative array of row objects keyed by the value
	 *                       of each row's first column's value. Duplicate keys are discarded.
	 *                       Default OBJECT.
	 * @return array|object|null Database query results.
	 */
	public static function get_results( $query = null, $output = OBJECT ) {
		return call_user_func( array( static::get_handler(), 'get_results' ), $query, $output );
	}

	/**
	 * Retrieves one row from the database.
	 *
	 * @param string|null $query  SQL query.
	 * @param string      $output Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                            correspond to an stdClass object, an associative array, or a numeric array,
	 *                            respectively. Default OBJECT.
	 * @param int         $y      Optional. Row to return. Indexed from 0. Default 0.
	 * @return array|object|null|void Database query result in format specified by $output or null on failure.
	 */
	public static function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		return call_user_func( array( static::get_handler(), 'get_row' ), $query, $output, $y );
	}

	/**
	 * Retrieves the table name based on the table key.
	 *
	 * @param string $key The table key.
	 * @return string The table name.
	 */
	public static function get_table_name( $key ) {
		// Get the handler.
		$handler = static::get_handler();

		// Set the table name.
		$table_name = "{$handler->prefix}{$key}";

		// Check the key.
		if ( isset( $handler->$key ) ) {
			// Set the table name.
			$table_name = $handler->$key;
		}

		// Return the table name.
		return $table_name;
	}

	/**
	 * Retrieves one value from the database.
	 *
	 * @param string|null $query Optional. SQL query. Defaults to null, use the result from the previous query.
	 * @param int         $x     Optional. Column of value to return. Indexed from 0. Default 0.
	 * @param int         $y     Optional. Row of value to return. Indexed from 0. Default 0.
	 * @return string|null Database query result (as string), or null on failure.
	 */
	public static function get_var( $query = null, $x = 0, $y = 0 ) {
		return call_user_func( array( static::get_handler(), 'get_var' ), $query, $x, $y );
	}

	/**
	 * Determines whether the database or WPDB supports a particular feature.
	 *
	 * @param string $db_cap The feature to check for. Accepts 'collation', 'group_concat',
	 *                       'subqueries', 'set_charset', 'utf8mb4', 'utf8mb4_520',
	 *                       or 'identifier_placeholders'.
	 * @return bool True when the database feature is supported, false otherwise.
	 */
	public static function has_cap( $db_cap ) {
		return call_user_func( array( static::get_handler(), 'has_cap' ), $db_cap );
	}

	/**
	 * Inserts a row into the table.
	 *
	 * @param string          $table  Table name.
	 * @param array           $data   Data to insert (in column => value pairs).
	 *                                Both `$data` columns and `$data` values should be "raw" (neither should be SQL escaped).
	 *                                Sending a null value will cause the column to be set to NULL - the corresponding
	 *                                format is ignored in this case.
	 * @param string[]|string $format Optional. An array of formats to be mapped to each of the value in `$data`.
	 *                                If string, that format will be used for all of the values in `$data`.
	 *                                A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                If omitted, all values in `$data` will be treated as strings unless otherwise
	 *                                specified in wpdb::$field_types. Default null.
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public static function insert( $table, $data, $format = null ) {
		return call_user_func_array( array( static::get_handler(), 'insert' ), array( $table, $data, $format ) );
	}

	/**
	 * Prepares a SQL query for safe execution.
	 *
	 * @param string $query   Query statement with `sprintf()`-like placeholders.
	 * @param mixed  ...$args Further variables to substitute into the query's placeholders
	 *                        if being called with individual arguments.
	 * @return string|void Sanitized query string, if there is a query to prepare.
	 */
	public static function prepare( $query, ...$args ) {
		return call_user_func_array( array( static::get_handler(), 'prepare' ), array_merge( array( $query ), $args ) );
	}

	/**
	 * Performs a database query, using current database connection.
	 *
	 * @param string $query Database query.
	 * @return int|bool Boolean true for CREATE, ALTER, TRUNCATE and DROP queries. Number of rows
	 *                  affected/selected for all other queries. Boolean false on error.
	 */
	public static function query( $query ) {
		return call_user_func( array( static::get_handler(), 'query' ), $query );
	}

	/**
	 * Sets the table name based on the table key.
	 *
	 * @param string $key The table key.
	 */
	public static function set_table_name( $key ) {
		// Get the handler.
		$handler = static::get_handler();

		// Set the key.
		$handler->{$key} = "{$handler->prefix}{$key}";
	}

	/**
	 * Updates a row in the table.
	 *
	 * @param string          $table           Table name.
	 * @param array           $data            Data to update (in column => value pairs).
	 *                                         Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 *                                         Sending a null value will cause the column to be set to NULL - the corresponding
	 *                                         format is ignored in this case.
	 * @param array           $where           A named array of WHERE clauses (in column => value pairs).
	 *                                         Multiple clauses will be joined with ANDs.
	 *                                         Both $where columns and $where values should be "raw".
	 *                                         Sending a null value will create an IS NULL comparison - the corresponding
	 *                                         format will be ignored in this case.
	 * @param string[]|string $format       Optional. An array of formats to be mapped to each of the values in $data.
	 *                                      If string, that format will be used for all of the values in $data.
	 *                                      A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                      If omitted, all values in $data will be treated as strings unless otherwise
	 *                                      specified in wpdb::$field_types. Default null.
	 * @param string[]|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
	 *                                      If string, that format will be used for all of the items in $where.
	 *                                      A format is one of '%d', '%f', '%s' (integer, float, string).
	 *                                      If omitted, all values in $where will be treated as strings unless otherwise
	 *                                      specified in wpdb::$field_types. Default null.
	 * @return int|false The number of rows updated, or false on error.
	 */
	public static function update( $table, $data, $where, $format = null, $where_format = null ) {
		return call_user_func_array( array( static::get_handler(), 'update' ), array( $table, $data, $where, $format, $where_format ) );
	}
}
