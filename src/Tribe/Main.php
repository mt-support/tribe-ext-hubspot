<?php

namespace Tribe\HubSpot;

use Tribe\HubSpot\Admin\Settings;
use Tribe\HubSpot\Service_Provider;

class Main {

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * Custom options prefix (without trailing underscore).
	 *
	 * Should leave blank unless you want to set it to something custom, such as if migrated from old extension.
	 */
	private $opts_prefix = 'tribe_hubspot';

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

		$this->get_settings();

		// Intialize the Service Provider for Event Tickets HubSpot Integration
		//tribe_register_provider( Service_Provider::class );
	}

	/**
	 * Get Settings instance.
	 *
	 * @return Settings
	 */
	private function get_settings() {
		if ( empty( $this->settings ) ) {
			$this->settings = new Settings( $this->opts_prefix );
		}

		return $this->settings;
	}

	/**
	 * Get all of this extension's options.
	 *
	 * @return array
	 */
	public function get_all_options() {
		$settings = $this->get_settings();

		return $settings->get_all_options();
	}

}

