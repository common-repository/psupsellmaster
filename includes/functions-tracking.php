<?php
/**
 * Functions - Tracking.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds an indirect tracking record to the database.
 */
function psupsellmaster_wp_add_indirect_tracking() {
	// Check if it is a ajax request.
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	// Check if it is a cron request.
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		return false;
	}

	// Get the post type.
	$post_type = psupsellmaster_get_product_post_type();

	// Check if the main query is not for a product.
	if ( ! is_singular( $post_type ) ) {
		return false;
	}

	// Get the product id.
	$product_id = get_the_ID();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		return false;
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
	$insert_data = array();

	// Set the prefix.
	$prefix = 'psupsellmaster_';

	// Check if the campaign id is not empty.
	if ( ! empty( $_GET[ "{$prefix}campaign_id" ] ) ) {
		// Get the campaign id.
		$campaign_id = sanitize_text_field( wp_unslash( $_GET[ "{$prefix}campaign_id" ] ) );
		$campaign_id = filter_var( $campaign_id, FILTER_VALIDATE_INT );

		// Check if the campaign id is not empty.
		if ( ! empty( $campaign_id ) ) {
			// Set the campaign id.
			$insert_data['campaign_id'] = $campaign_id;
		}
	}

	// Check if the base product id is not empty.
	if ( ! empty( $_GET[ "{$prefix}base_product_id" ] ) ) {
		// Get the base product id.
		$base_product_id = sanitize_text_field( wp_unslash( $_GET[ "{$prefix}base_product_id" ] ) );
		$base_product_id = filter_var( $base_product_id, FILTER_VALIDATE_INT );

		// Check if the base product id is not empty.
		if ( ! empty( $base_product_id ) ) {
			// Set the base product id.
			$insert_data['base_product_id'] = $base_product_id;
		}
	}

	// Check if the location is not empty.
	if ( ! empty( $_GET[ "{$prefix}location" ] ) ) {
		// Set the location.
		$insert_data['location'] = sanitize_text_field( wp_unslash( $_GET[ "{$prefix}location" ] ) );
	}

	// Check if the source is not empty.
	if ( ! empty( $_GET[ "{$prefix}source" ] ) ) {
		// Set the source.
		$insert_data['source'] = sanitize_text_field( wp_unslash( $_GET[ "{$prefix}source" ] ) );
	}

	// Check if the view is not empty.
	if ( ! empty( $_GET[ "{$prefix}view" ] ) ) {
		// Set the view.
		$insert_data['view'] = sanitize_text_field( wp_unslash( $_GET[ "{$prefix}view" ] ) );
	}

	// Check if the insert data is empty.
	if ( empty( $insert_data ) ) {
		return false;
	}

	// Set the visitor id.
	$insert_data['visitor_id'] = $visitor_id;

	// Set the type.
	$insert_data['type'] = 'indirect';

	// Set the product id.
	$insert_data['product_id'] = $product_id;

	// Set the variation id (zero because at this moment we don't have the variation for indirect tracking).
	$insert_data['variation_id'] = 0;

	// Insert a new interest into the database.
	psupsellmaster_db_interests_insert( $insert_data );
}
add_action( 'wp', 'psupsellmaster_wp_add_indirect_tracking' );

/**
 * Deletes old records in case they are older than 30 days.
 *
 * @return bool|int False on failure, number of rows deleted on success.
 */
function psupsellmaster_maybe_delete_old_interests() {
	// Set the deleted.
	$deleted = false;

	// Build the SQL to delete old interests.
	$sql_delete  = 'DELETE';
	$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_interests' ) );
	$sql_where   = array();
	$sql_where[] = 'WHERE 1 = 1';
	$sql_where[] = 'AND updated_at < DATE_SUB( NOW(), INTERVAL 30 DAY )';
	$sql_where   = implode( ' ', $sql_where );
	$sql_query   = "{$sql_delete} {$sql_from} {$sql_where}";
	$deleted     = PsUpsellMaster_Database::query( $sql_query );

	// Check if the deleted is not empty.
	if ( ! empty( $deleted ) ) {
		// Build the SQL to delete old interestmeta.
		$sql_delete  = 'DELETE';
		$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_interestmeta' ) );
		$sql_where   = array();
		$sql_where[] = 'WHERE 1 = 1';
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND NOT EXISTS ( SELECT 1 FROM %i AS `i` WHERE `psupsellmaster_interest_id` = `i`.`id` )', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_interests' ) );
		$sql_where   = implode( ' ', $sql_where );
		$sql_query   = "{$sql_delete} {$sql_from} {$sql_where}";

		// Delete the meta records.
		PsUpsellMaster_Database::query( $sql_query );
	}

	// Return the deleted.
	return $deleted;
}

/**
 * Deletes old records in case they are older than 30 days.
 *
 * @return bool|int False on failure, number of rows deleted on success.
 */
function psupsellmaster_maybe_delete_old_visitors() {
	// Set the deleted.
	$deleted = false;

	// Build the SQL to delete old visitors.
	$sql_delete  = 'DELETE';
	$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' ) );
	$sql_where   = array();
	$sql_where[] = 'WHERE 1 = 1';
	$sql_where[] = 'AND updated_at < DATE_SUB( NOW(), INTERVAL 30 DAY )';
	$sql_where   = implode( ' ', $sql_where );
	$sql_query   = "{$sql_delete} {$sql_from} {$sql_where}";
	$deleted     = PsUpsellMaster_Database::query( $sql_query );

	// Check if the deleted is not empty.
	if ( ! empty( $deleted ) ) {
		// Build the SQL to delete old visitormeta.
		$sql_delete  = 'DELETE';
		$sql_from    = PsUpsellMaster_Database::prepare( 'FROM %i', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitormeta' ) );
		$sql_where   = array();
		$sql_where[] = 'WHERE 1 = 1';
		$sql_where[] = PsUpsellMaster_Database::prepare( 'AND NOT EXISTS ( SELECT 1 FROM %i AS `v` WHERE `psupsellmaster_visitor_id` = `v`.`id` )', PsUpsellMaster_Database::get_table_name( 'psupsellmaster_visitors' ) );
		$sql_where   = implode( ' ', $sql_where );
		$sql_query   = "{$sql_delete} {$sql_from} {$sql_where}";

		// Delete the meta records.
		PsUpsellMaster_Database::query( $sql_query );
	}

	// Return the deleted.
	return $deleted;
}
