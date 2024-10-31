<?php
/**
 * Class - Background Process.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PsUpsellMaster_Background_Process class.
 */
class PsUpsellMaster_Background_Process extends PsUpsellMaster_WP_Background_Process {

	/**
	 * Batch callback.
	 *
	 * @var callable
	 * @access protected
	 */
	protected $batch_callback;

	/**
	 * Constructor.
	 *
	 * @param string   $action The action.
	 * @param callable $batch_callback The batch callback.
	 */
	public function __construct( $action, $batch_callback ) {
		// Set the prefix.
		$this->prefix = 'psupsellmaster';

		// Set the action.
		$this->action = $action;

		// Set the batch callback.
		$this->batch_callback = $batch_callback;

		// Call the parent constructor.
		parent::__construct();
	}

	/**
	 * Code to execute for each item in the queue.
	 *
	 * @param string $item Queue item to iterate over.
	 * @return bool
	 */
	protected function task( $item ) {
		// Check if the item is empty.
		if ( empty( $item ) ) {
			return false;
		}

		// Get the status.
		$status = isset( $item['status'] ) ? $item['status'] : false;

		// Check the status.
		if ( 'starting' === $status ) {
			// Set the status.
			$item['status'] = 'running';
		}

		// Check if the batch callback is callable.
		if ( is_callable( $this->batch_callback ) ) {
			// Run the batch callback.
			$count = call_user_func( $this->batch_callback );
		}

		// Check if the count is empty.
		if ( empty( $count ) ) {
			// Set the status.
			$item['status'] = 'stopping';

			// Return false - it is done.
			return false;
		}

		// Return the item - it needs to run again.
		return $item;
	}

	/**
	 * Is queue empty.
	 *
	 * @return bool
	 */
	public function is_queue_empty() {
		$table  = PsUpsellMaster_Database::get_table_name( 'options' );
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = PsUpsellMaster_Database::get_table_name( 'sitemeta' );
			$column = 'meta_key';
		}

		$key = PsUpsellMaster_Database::esc_like( $this->identifier . '_batch_' ) . '%';

		$count = PsUpsellMaster_Database::get_var( PsUpsellMaster_Database::prepare( "SELECT COUNT(*) FROM {$table} WHERE {$column} LIKE %s", $key ) );

		return ! ( $count > 0 );
	}

	/**
	 * Get batch.
	 *
	 * @return stdClass Return the first batch from the queue.
	 */
	protected function get_batch() {
		$table        = PsUpsellMaster_Database::get_table_name( 'options' );
		$column       = 'option_name';
		$key_column   = 'option_id';
		$value_column = 'option_value';

		if ( is_multisite() ) {
			$table        = PsUpsellMaster_Database::get_table_name( 'sitemeta' );
			$column       = 'meta_key';
			$key_column   = 'meta_id';
			$value_column = 'meta_value';
		}

		$key = PsUpsellMaster_Database::esc_like( $this->identifier . '_batch_' ) . '%';

		$query = PsUpsellMaster_Database::get_row( PsUpsellMaster_Database::prepare( "SELECT * FROM {$table} WHERE {$column} LIKE %s ORDER BY {$key_column} ASC LIMIT 1", $key ) );

		$batch       = new stdClass();
		$batch->key  = $query->$column;
		$batch->data = array_filter( (array) maybe_unserialize( $query->$value_column ) );

		return $batch;
	}

	/**
	 * See if the batch limit has been exceeded.
	 *
	 * @return bool
	 */
	protected function batch_limit_exceeded() {
		return $this->time_exceeded() || $this->memory_exceeded();
	}

	/**
	 * Handle.
	 *
	 * Pass each queue item to the task handler, while remaining
	 * within server memory and time limit constraints.
	 */
	protected function handle() {
		$this->lock_process();

		do {
			$batch = $this->get_batch();

			foreach ( $batch->data as $key => $value ) {
				$task = $this->task( $value );

				if ( false !== $task ) {
					$batch->data[ $key ] = $task;
				} else {
					unset( $batch->data[ $key ] );
				}

				if ( $this->batch_limit_exceeded() ) {
					// Batch limits reached.
					break;
				}
			}

			// Update or delete current batch.
			if ( ! empty( $batch->data ) && ! $this->is_queue_empty() ) {
				$this->update( $batch->key, $batch->data );
			} else {
				$this->delete( $batch->key );
			}
		} while ( ! $this->batch_limit_exceeded() && ! $this->is_queue_empty() );

		$this->unlock_process();

		// Start next batch or complete process.
		if ( ! $this->is_queue_empty() ) {
			$this->dispatch();
		} else {
			$this->complete();
		}
	}

	/**
	 * Get memory limit.
	 *
	 * @return int
	 */
	protected function get_memory_limit() {

		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || -1 === intval( $memory_limit ) ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32G';
		}

		return wp_convert_hr_to_bytes( $memory_limit );
	}

	/**
	 * Schedule cron healthcheck.
	 *
	 * @param array $schedules Schedules.
	 * @return array
	 */
	public function schedule_cron_healthcheck( $schedules ) {
		$interval = apply_filters( $this->identifier . '_cron_interval', 5 );

		// Adds every 5 minutes to the existing schedules.
		$schedules[ $this->identifier . '_cron_interval' ] = array(
			'interval' => MINUTE_IN_SECONDS * $interval,
			/* translators: %d: interval */
			'display'  => sprintf( __( 'Every %d minutes', 'psupsellmaster' ), $interval ),
		);

		return $schedules;
	}

	/**
	 * Delete all batches.
	 *
	 * @return PsUpsellMaster_Background_Process
	 */
	public function delete_all_batches() {
		$table  = PsUpsellMaster_Database::get_table_name( 'options' );
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = PsUpsellMaster_Database::get_table_name( 'sitemeta' );
			$column = 'meta_key';
		}

		$key = PsUpsellMaster_Database::esc_like( $this->identifier . '_batch_' ) . '%';

		PsUpsellMaster_Database::query( PsUpsellMaster_Database::prepare( "DELETE FROM {$table} WHERE {$column} LIKE %s", $key ) );

		return $this;
	}

	/**
	 * Complete.
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		// Unschedule the cron healthcheck.
		$this->clear_scheduled_event();

		do_action( $this->identifier . '_complete' );
	}

	/**
	 * Kill process.
	 *
	 * Stop processing queue items, clear cronjob and delete all batches.
	 */
	public function kill_process() {

		if ( ! $this->is_queue_empty() ) {
			$this->delete_all_batches();
			wp_clear_scheduled_hook( $this->cron_hook_identifier );
		}

		do_action( $this->identifier . '_kill_process' );
	}
}
