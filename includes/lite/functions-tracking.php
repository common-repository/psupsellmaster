<?php
/**
 * LITE - Functions - Tracking.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run tracking-related downgrade procedures.
 */
function psupsellmaster_lite_tracking_downgrade() {
	// Get the limit.
	$limit = psupsellmaster_get_feature_limit( 'results_count' );

	// Set the query.
	$query = PsUpsellMaster_Database::prepare(
		'
		DELETE
			`old`
		FROM
			%i AS `old`
		LEFT JOIN (
			SELECT
				`id`
			FROM
				%i
			ORDER BY
				`id` DESC
			LIMIT
				%d
		) AS `new`
		ON
			`old`.`id` = `new`.`id`
		WHERE
			`new`.`id` IS NULL
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ),
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_results' ),
		$limit
	);

	// Run the query.
	PsUpsellMaster_Database::query( $query );
}
add_action( 'psupsellmaster_tracking_insert_results_after', 'psupsellmaster_lite_tracking_downgrade' );

/**
 * Run campaign-related downgrade procedures.
 */
function psupsellmaster_lite_campaigns_downgrade() {
	// Set the status.
	$status = 'active';

	// Set the sql query.
	$sql_query = PsUpsellMaster_Database::prepare(
		'
		SELECT
			`campaigns`.`id`
		FROM
			%i AS `campaigns`
		WHERE
			`campaigns`.`status` = %s
		ORDER BY
			`campaigns`.`priority`,
			`campaigns`.`end_date` DESC,
			`campaigns`.`id` DESC
		',
		PsUpsellMaster_Database::get_table_name( 'psupsellmaster_campaigns' ),
		$status
	);

	// Get the id of the single campaign that will remain active - if any.
	$id = filter_var( PsUpsellMaster_Database::get_var( $sql_query ), FILTER_VALIDATE_INT );

	// Check if the id is empty.
	if ( empty( $id ) ) {
		return false;
	}

	// Set the campaign status.
	psupsellmaster_set_campaigns_status( array( $id ), 'active' );
}

/**
 * Run downgrade (from PRO to Lite) procedures.
 */
function psupsellmaster_lite_downgrade() {
	// Run campaign-related downgrade procedures.
	psupsellmaster_lite_campaigns_downgrade();
}
add_action( 'psupsellmaster_type_init_downgrade', 'psupsellmaster_lite_downgrade' );
