<?php

namespace Tribe\HubSpot;

use Tribe\HubSpot\Admin\Settings;

class Main {

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * Static Singleton Holder
	 * @var self
	 */
	protected static $instance;

	/**
	 * Get (and instantiate, if necessary) the instance of the class
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
	 */
	protected function __construct() {

		$this->init();

	}

	public function init() {

		// Setup bootstrap on earliest hook available to the extension.
		add_action( 'tribe_plugins_loaded', [ $this, 'bootstrap' ], 11 );

	}

	/**
	 * Bootstrap Plugin
	 *
	 * @since TBD
	 *
	 */
	public function bootstrap() {

		// Intialize the Service Provider for Event Tickets HubSpot Integration.
		tribe_register_provider( Service_Provider::class );

	}

	/**
	 * Get all of this extension's options.
	 *
	 * @return array
	 */
	public function get_all_options() {

		/** @var \Tribe\HubSpot\Admin\Settings $hubspot_options */
		$settings = tribe( 'tickets.hubspot.admin.settings' );

		return $settings->get_all_options();
	}

}

