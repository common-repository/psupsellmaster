<?php
/**
 * Functions - Database.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the database tables.
 */
function psupsellmaster_register_database_tables() {
	// Set the table names.
	$table_names = array(
		'psupsellmaster_visitors',
		'psupsellmaster_visitormeta',
		'psupsellmaster_interests',
		'psupsellmaster_interestmeta',
		'psupsellmaster_results',
		'psupsellmaster_resultmeta',
		'psupsellmaster_analytics_orders',
		'psupsellmaster_analytics_upsells',
		'psupsellmaster_scores',
		'psupsellmaster_scoremeta',
		'psupsellmaster_campaigns',
		'psupsellmaster_campaignmeta',
		'psupsellmaster_campaign_eligible_products',
		'psupsellmaster_campaign_coupons',
		'psupsellmaster_campaign_products',
		'psupsellmaster_campaign_authors',
		'psupsellmaster_campaign_taxonomies',
		'psupsellmaster_campaign_locations',
		'psupsellmaster_campaign_weekdays',
		'psupsellmaster_campaign_carts',
		'psupsellmaster_campaign_events',
		'psupsellmaster_campaign_display_options',
		'psupsellmaster_campaign_templates',
		'psupsellmaster_campaign_templatemeta',
	);

	// Loop through the table names.
	foreach ( $table_names as $table_name ) {
		// Register the table name.
		PsUpsellMaster_Database::set_table_name( $table_name );
	}
}
add_action( 'plugins_loaded', 'psupsellmaster_register_database_tables', 20 );

/**
 * Get the last inserted id in the database.
 *
 * @return int The inserted id.
 */
function psupsellmaster_db_get_inserted_id() {
	// Set the inserted id.
	$inserted_id = PsUpsellMaster_Database::get_insert_id();

	// Return the inserted id.
	return $inserted_id;
}

/**
 * Truncates a database table.
 *
 * @param string $table_name The table name.
 * @return bool True if the table was truncated, false otherwise.
 */
function psupsellmaster_db_truncate_table( $table_name ) {
	// Delete from the database.
	return PsUpsellMaster_Database::query(
		PsUpsellMaster_Database::prepare(
			'TRUNCATE TABLE %i',
			PsUpsellMaster_Database::get_table_name( $table_name )
		)
	);
}
