<?php
/**
 * Functions - WP Cron.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Schedules events on the init hook.
 * The daily event does run daily, but the tasks might run periodically.
 * This is due to a WordPress Cron limitation:
 * It does not allow us to specify eg. run a task every first day of the month.
 * So we have to run this daily and check things manually
 * to decide whether or not some tasks should run.
 */
function psupsellmaster_schedule_events() {

	// Check if there are no next scheduled event.
	if ( ! wp_next_scheduled( 'psupsellmaster_wp_cron_daily' ) ) {
		// Define the date.
		$date = new DateTime( 'tomorrow', psupsellmaster_get_timezone() );

		// Define the timestamp.
		$timestamp = $date->getTimestamp();

		// Schedule the next event.
		wp_schedule_event( $timestamp, 'daily', 'psupsellmaster_wp_cron_daily' );
	}
}
add_action( 'init', 'psupsellmaster_schedule_events' );

/**
 * Checks if the tasks should run.
 * It will check if the interval is invalid and if so, it will return false.
 * Otherwise, it will check if the tasks should run based on the received arguments.
 *
 * @param  int    $timestamp_utc The timestamp in UTC.
 * @param  string $interval   The interval. Can be 'monthly', 'weekly' or 'daily'. Default 'monthly'.
 * @return bool               True if the tasks should run, false otherwise.
 */
function psupsellmaster_should_run_tasks( $timestamp_utc, $interval = 'monthly' ) {
	// Set the run tasks.
	$run_tasks = false;

	// Check if the interval is invalid.
	if ( ! in_array( $interval, array( 'monthly', 'weekly', 'daily' ), true ) ) {
		// Return the run tasks.
		return $run_tasks;
	}

	// Set the format.
	$format = 'Y-m';

	// Check the interval.
	if ( 'weekly' === $interval ) {
		// Set the format.
		$format = 'Y-W';

		// Check the interval.
	} elseif ( 'daily' === $interval ) {
		// Set the format.
		$format = 'Y-m-d';
	}

	// Set the datetime format.
	$datetime_format = ! empty( $timestamp_utc ) ? "@{$timestamp_utc}" : 'first day of previous month';

	// Get the datetime of the last run date.
	$datetime_input_date = new DateTime( $datetime_format, psupsellmaster_get_timezone() );

	// Get the datetime of the current date by using the site timezone.
	$datetime_current_date = new DateTime( 'now', psupsellmaster_get_timezone() );

	// Set the run tasks.
	$run_tasks = $datetime_input_date->format( $format ) !== $datetime_current_date->format( $format );

	// Return the run tasks.
	return $run_tasks;
}

/**
 * Runs when the WP Cron daily hook is triggered.
 * It checks if the background processes tasks should run.
 * If so, it sets the flags to run them.
 */
function psupsellmaster_wp_cron_daily_maybe_run_bps() {
	// Check if this is the lite version.
	if ( psupsellmaster_is_lite() ) {
		return false;
	}

	// Get the interval.
	$interval = PsUpsellMaster_Settings::get( 'recalculation_interval' );

	// Set the should run.
	$should_run = array();

	// ------------------------------------------
	// Analytics - Orders.
	// ------------------------------------------

	// Get the last run date.
	$last_run = get_option( 'psupsellmaster_bp_analytics_orders_last_run' );

	// Check if the last run is not empty.
	if ( ! empty( $last_run ) ) {
		// Get the run tasks.
		$run_tasks = psupsellmaster_should_run_tasks( $last_run, $interval );

		// Check the run tasks.
		if ( $run_tasks ) {
			// Add the background process to the list.
			array_push( $should_run, array( 'key' => 'analytics_orders' ) );
		}
	}

	// ------------------------------------------
	// Analytics - Upsells.
	// ------------------------------------------

	// Get the last run date.
	$last_run = get_option( 'psupsellmaster_bp_analytics_upsells_last_run' );

	// Check if the last run is not empty.
	if ( ! empty( $last_run ) ) {
		// Get the run tasks.
		$run_tasks = psupsellmaster_should_run_tasks( $last_run, $interval );

		// Check the run tasks.
		if ( $run_tasks ) {
			// Add the background process to the list.
			array_push( $should_run, array( 'key' => 'analytics_upsells' ) );
		}
	}

	// ------------------------------------------
	// Scores.
	// ------------------------------------------

	// Get the last run date.
	$last_run = get_option( 'psupsellmaster_bp_scores_last_run' );

	// Check if the last run is not empty.
	if ( ! empty( $last_run ) ) {
		// Check the should run - if analytics will run, then the scores also need to run.
		if ( ! empty( $should_run['analytics_orders'] ) || ! empty( $should_run['analytics_upsells'] ) ) {
			// Add the background process to the list.
			array_push( $should_run, array( 'key' => 'scores' ) );

			// Otherwise, check the last run.
		} else {
			// Get the run tasks.
			$run_tasks = psupsellmaster_should_run_tasks( $last_run, $interval );

			// Check the run tasks.
			if ( $run_tasks ) {
				// Check if the Easy Digital Downloads plugin is enabled.
				if ( psupsellmaster_is_plugin_active( 'edd' ) ) {
					// Add the background process to the list.
					array_push( $should_run, array( 'key' => 'edd_prices' ) );
				}

				array_push( $should_run, array( 'key' => 'scores' ) );
			}
		}
	}

	// Check if the should run is not empty.
	if ( ! empty( $should_run ) ) {
		// Get the queue.
		$queue = get_option( 'psupsellmaster_bp_queue', array() );
		$queue = is_array( $queue ) ? $queue : array();

		// Set the queue.
		$queue = array_merge( $queue, $should_run );

		// Update the option.
		update_option( 'psupsellmaster_bp_queue', $queue, false );
	}
}
add_action( 'psupsellmaster_wp_cron_daily', 'psupsellmaster_wp_cron_daily_maybe_run_bps' );

/**
 * Runs when the WP Cron daily hook is triggered.
 * It deletes old records from the database in case they exist.
 */
function psupsellmaster_wp_cron_maybe_delete_old_records() {
	// Delete old interests.
	psupsellmaster_maybe_delete_old_interests();

	// Delete old visitors.
	psupsellmaster_maybe_delete_old_visitors();

	// Delete old results.
	psupsellmaster_maybe_delete_old_results();
}
add_action( 'psupsellmaster_wp_cron_daily', 'psupsellmaster_wp_cron_maybe_delete_old_records' );
