<?php

namespace Tribe\HubSpot\API;

use Tribe\HubSpot\Admin\Settings;
use Tribe\HubSpot\Process\Setup_Queue;

/**
 * Class Setup
 *
 * @since 1.0
 *
 * @package Tribe\HubSpot\API
 */
class Setup {

	/**
	 * @var Settings
	 */
	protected $settings;

	/**
	 * @var /Tribe__Cache
	 */
	protected $cache;

	/**
	 * Number of seconds before setup transient (when enabled) should be invalidated.
	 *
	 * @var int
	 */
	protected $cache_expiration;

	/**
	 * @var string
	 */
	protected $delay_transient_name = 'hubspot_setup_delay';

	/**
	 * @var array
	 */
	protected $setup_types = [
		'group_name_setup',
		'custom_properties_setup',
		'timeline_event_types_setup',
	];

	/**
	 * @var array
	 */
	protected $secondary_setup = [
		'custom_properties_setup',
		'timeline_event_types_setup',
	];

	/**
	 * @var int
	 */
	protected $max_setup_tries = 5;

	/**
	 * @var int
	 */
	protected $delay = 10;

	/**
	 * Static Singleton Holder
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @since 1.0
	 *
	 * @return self
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Class constructor
	 *
	 * @since 1.0
	 *
	 */
	protected function __construct() {

		/** @var \Tribe\HubSpot\Admin\Settings $settings */
		$this->settings = tribe( 'tickets.hubspot.admin.settings' );

		/** @var \Tribe__Cache $cache */
		$this->cache            = new \Tribe__Cache();
		$this->cache_expiration = HOUR_IN_SECONDS;

		$this->hook();
	}

	/**
	 * HubSpot API Setup Hook.
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {
		add_action( 'admin_footer', [ $this, 'setup_check' ] );
	}

	/**
	 * Check on the HubSpot API Setup Processes and Maybe Run.
	 *
	 * @since 1.0
	 *
	 */
	public function setup_check() {

		$options = $this->settings->get_all_options();
		$delay   = $this->cache->get_transient( $this->delay_transient_name );

		foreach ( $this->setup_types as $type ) {

			if ( empty( $options[ $type ] ) ) {
				continue;
			}

			if ( 'complete' === $options[ $type ] || 'failed' === $options[ $type ] ) {
				continue;
			}

			// If greater then the max tries then mark as failed.
			if ( $options[ $type ] >= $this->max_setup_tries ) {
				$this->set_status_value_by_name( $type, 'failed' );
				continue;
			}

			if ( time() < $delay ) {
				continue;
			}

			// Send Setup to Queue.
			$this->send_to_queue( $type );
		}
	}

	/**
	 * Set the Status by Name and Transient for one of the Setup Types.
	 *
	 * @since 1.0
	 *
	 * @param string  $type              The name of the status to set a value.
	 * @param mixed   $value             The value to update the status type to.
	 * @param boolean $trigger_secondary Whether to trigger setup of secondary values.
	 */
	public function set_status_value_by_name( $type, $value, $trigger_secondary = false ) {

		$this->settings->update_option( $type, $value );

		$this->cache->set_transient( $this->delay_transient_name, $this->get_delay_timestamp(), $this->cache_expiration );

		if ( ! $trigger_secondary ) {
			return;
		}

		// Setup the Secondary Settings that Rely on the Group Name Being Setup.
		foreach ( $this->secondary_setup as $type ) {
			// Send Setup to Queue.
			$this->send_to_queue( $type );
		}
	}

	/**
	 * Get the Status Value by Name and Increase Value if Numeric
	 *
	 * @since 1.0
	 *
	 * @param string $type The name of the status to get a value.
	 *
	 * @return int|mixed|string The value of the Status Type.
	 */
	public function get_status_value_by_name( $type ) {

		$value = $this->settings->get_option( $type );

		if ( empty( $value ) || 'pending' === $value ) {

			return 1;
		}

		if ( is_numeric( $value ) ) {
			$value ++;

			return $value;
		}

		return $value;
	}

	/**
	 * Get the timestamp with delay added.
	 *
	 * @since 1.0
	 *
	 * @return int The timestamp with delay added.
	 */
	public function get_delay_timestamp() {

		$delay = time() + $this->delay . ' secs';

		/**
		 * Filter the delay to try to setup settings in HubSpot.
		 *
		 * @since 1.0
		 *
		 * @param int $delay The time in seconds to delay the next try to setup HubSpot, default is 5 seconds.
		 *
		 */
		return apply_filters( 'tribe_hubspot_setup_delay', $delay );
	}

	/**
	 * Dispatch and Setup Type to the Setup Queue.
	 *
	 * @since 1.0
	 *
	 * @param string $type The name of the status to dispatch to the setup queue.
	 */
	public function send_to_queue( $type ) {

		$hubspot_data = [
			'type' => $type,
		];

		$queue = new Setup_Queue();
		$queue->push_to_queue( $hubspot_data );
		$queue->save();
		$queue->dispatch();
	}

	/**
	 * Set the Setup Statuses to Pending
	 *
	 * @since 1.0
	 *
	 */
	public function set_setup_to_pending() {

		foreach ( $this->setup_types as $type ) {
			$this->set_status_value_by_name( $type, 'pending' );
		}
	}

	/**
	 * Clear the Setup Statuses.
	 *
	 * @since 1.0
	 *
	 */
	public function clear_setup() {

		foreach ( $this->setup_types as $type ) {
			$this->set_status_value_by_name( $type, false );
		}
	}

	/**
	 * Get the array of Setup Status Types.
	 *
	 * @since 1.0
	 *
	 * @return array An array of setup status types.
	 */
	public function get_setup_types() {
		return $this->setup_types;
	}

}
