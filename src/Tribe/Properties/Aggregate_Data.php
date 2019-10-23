<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class Event_Data
 *
 * @package Tribe\HubSpot\Properties
 */
class Aggregate_Data extends Base {

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
				'label'     => 'Total Registered Events',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'total_number_of_orders'    => [
				'label'     => 'Total Number of Orders',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'average_tickets_per_order' => [
				'label'     => 'Average Tickets Per Order',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'average_events_per_order'  => [
				'label'     => 'Average Events Per Order',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'total_attended_events'     => [
				'label'     => 'Total Attended Events',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
			'total_no_shows'            => [
				'label'     => 'Total No Shows',
				'groupName' => $this->group_name,
				'type'      => 'number',
				'fieldType' => 'number',
			],
		];
	}
}