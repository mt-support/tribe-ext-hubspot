<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class Base
 *
 * @package Tribe\HubSpot\Properties
 */
Abstract class Base {

	/**
	 * @var string
	 */
	protected $group_name = 'event_tickets';

	/**
	 * @var string
	 */
	protected $properties_grouping_name = '';

	/**
	 * @var array
	 */
	protected $properties = [];

	/**
	 * Set the Individual Properties for a Grouping
	 *
	 * @since 1.0
	 *
	 */
	abstract function set_properties();

	/**
	 * Get Properties Grouping Name
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_properties_grouping_name() {

		return $this->properties_grouping_name;
	}

	/**
	 * Get an Array of Properties
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_properties() {

		/**
		 * Filter the properties created and updated with HubSpot
		 *
		 * @since 1.0
		 *
		 * @param array $properties List of properties that are created and updated with HubSpot.
		 *
		 */
		return apply_filters( 'tribe_hubspot_properties_' . $this->properties_grouping_name, $this->properties );

	}
}