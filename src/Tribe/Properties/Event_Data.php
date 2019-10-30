<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class Event_Data
 *
 * @package Tribe\HubSpot\Properties
 */
class Event_Data extends Base {

	/**
	 * @var string
	 */
	protected $properties_grouping_name = 'event_data';

	/**
	 * @var array
	 */
	protected $properties = [];

	public function __construct() {

		$this->set_properties();
	}

	/**
	 * Set the Individual Properties for this Grouping
	 *
	 * @since 1.0
	 *
	 */
	public function set_properties() {

		$this->properties = [
			'total_registered_events'   => [
				'label'     => __( 'Total Registered Events', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'total_number_of_orders'    => [
				'label'     => __( 'Total Number of Orders', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'average_tickets_per_order' => [
				'label'     => __( 'Average Tickets Per Order', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'average_events_per_order'  => [
				'label'     => __( 'Average Events Per Order', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'total_attended_events'     => [
				'label'     => __( 'Total Attended Events', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'total_no_shows'            => [
				'label'     => __( 'Total No Shows', 'tribe-ext-hubspot' ),
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}