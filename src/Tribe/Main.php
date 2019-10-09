<?php

namespace Tribe\HubSpot;

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
	private $opts_prefix = 'tribe_hubspot_';

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


	}

	public function init() {


	}

}

