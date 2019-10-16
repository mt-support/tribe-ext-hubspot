<?php

namespace Tribe\HubSpot\Process;

class Queue {

	/**
	 * Which action will be triggered as an ongoing scheduled cron event.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $scheduled_key = 'tribe_hubspot_process_subscription';

	/**
	 * Number of items to be processed in a single batch.
	 *
	 * @var int
	 */
	protected $batch_size = 500;

	/**
	 * Number of items in the current batch processed so far.
	 *
	 * @var int
	 */
	protected $processed = 0;

	/**
	 * Current Post ID being Processed.
	 *
	 * @var int
	 */
	protected $current_id = 0;

	/**
	 * Queue Hooks.
	 *
	 * @since 1.0
	 */
	public function hooks() {
		// @todo Add custom HubSpot deactivation code based on TEC.
		add_action( 'tribe_events_blog_deactivate', [ $this, 'clear_scheduled_task' ] );

		// Register the Required Cron Schedules
		add_filter( 'cron_schedules', array( $this, 'filter_add_cron_schedules' ) );

		add_action( $this->scheduled_key, [ $this, 'process_queue' ], 20, 0 );
	}

	/**
	 * Adds the Frequency to WP cron schedules of 20 minutes
	 *
	 * @since 1.0
	 *
	 * @param array $schedules
	 *
	 * @return array
	 */
	public function filter_add_cron_schedules( array $schedules ) {
		// Adds the Min frequency to WordPress cron schedules
		$schedules['tribe-every20mins'] = array(
			'interval' => MINUTE_IN_SECONDS * 20,
			'display'  => esc_html_x( 'Every 20 minutes', 'hubsput subscription process schedule frequency', 'tribe-ext-hubspot' ),
		);

		return (array) $schedules;
	}

	/**
	 * Handle things that need to happen during the init action.
	 *
	 * @since 1.0
	 */
	public function action_init() {
		$this->manage_scheduled_task();
	}

	/**
	 * Configures a scheduled task to handle "background processing" for the queue.
	 *
	 * @since 1.0
	 */
	protected function manage_scheduled_task() {
		$this->register_scheduled_task();
	}

	/**
	 * Register scheduled task to be used for processing batches on plugin activation.
	 *
	 * @since 1.0
	 */
	protected function register_scheduled_task() {
		if ( wp_next_scheduled( $this->scheduled_key ) ) {
			return;
		}

		/**
		 * Filter the interval at which to process queue.
		 *
		 * By default the interval "hourly" is specified, however other intervals such as "daily"
		 * and "twicedaily" can normally be substituted.
		 *
		 * @since 1.0
		 *
		 * @see   wp_schedule_event()
		 * @see   'cron_schedules'
		 */
		$interval = apply_filters( 'tribe_hubspot_process_subscriptions_interval', 'tribe-every20mins' );

		wp_schedule_event( time(), $interval, $this->scheduled_key );
	}

	/**
	 * Clear the scheduled task on plugin deactivation.
	 *
	 * @since 1.0
	 */
	public function clear_scheduled_task() {
		wp_clear_scheduled_hook( $this->scheduled_key );
	}

	/**
	 * Processes the next item in queue
	 *
	 * @since 1.0
	 *
	 * @param int $batch_size
	 */
	public function process_queue( $batch_size = null ) {
		if ( null === $batch_size ) {
			/**
			 * Filter the amount of post objects to process
			 *
			 * @param int $default_batch_size
			 */
			$this->batch_size = (int) apply_filters( $this->scheduled_key . '_batch_size', 100 );
		} else {
			$this->batch_size = (int) $batch_size;
		}

		while ( $this->next_waiting_item() ) {
			if ( ! $this->do_processing() ) {
				break;
			}
		}
	}

	/**
	 * Obtains the post ID of the next item
	 *
	 * @since 1.0
	 *
	 */
	protected function next_waiting_item() {

		// TODO add query to get next waiting item set current id and return true or falsex

		return false;
	}

	/**
	 * Process the Current Item
	 *
	 * @since 1.0
	 *
	 */
	protected function do_processing() {

		if ( $this->batch_complete() ) {
			return false;
		}

		// TODO add processing code here


		$this->processed ++;

		return true;

	}

	/**
	 * Determines if the batch job is complete.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	protected function batch_complete() {
		return ( $this->processed >= $this->batch_size );
	}
}
