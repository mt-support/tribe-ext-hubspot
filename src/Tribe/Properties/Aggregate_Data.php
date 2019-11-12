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
				'label'          => __( 'Total Registered Events', 'tribe-ext-hubspot' ),
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'register',
			],
			'total_number_of_orders'    => [
				'label'          => __( 'Total Number of Orders', 'tribe-ext-hubspot' ),
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'register',
			],
			'average_tickets_per_order' => [
				'label'          => __( 'Average Tickets Per Order', 'tribe-ext-hubspot' ),
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'register',
			],
			'average_events_per_order'  => [
				'label'          => __( 'Average Events Per Order', 'tribe-ext-hubspot' ),
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'register',
			],
			'total_attended_events'     => [
				'label'          => __( 'Total Attended Events', 'tribe-ext-hubspot' ),
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'checkin',
			],
		];
	}

	public function get_values( $subscribe_type, $current_properties, $tickets, $events ) {
		$agg_data            = [];
		$matching_properties = wp_filter_object_list( $this->properties, [ 'subscribe_type' => $subscribe_type ], 'and' );

		foreach ( $matching_properties as $key => $property ) {

			log_me( $key );
			log_me( isset( $current_properties->{$key} ) );

			$value = 1;
			if ( isset( $current_properties->{$key} ) ) {
				$value = (int) $current_properties->{$key}->value ++;
			}

			//todo handle average_tickets_per_order and average_events_per_order
			//todo each ticket updates data in hubspot so how do I make it so these two get updated once per order?
			if ( 'average_tickets_per_order' === $key ) {
				$value = $tickets;
			}
			if ( 'average_events_per_order' === $key ) {
				$value = $events;
			}

			$agg_data[] = [
				'property' => $key,
				'value'    => $value,
			];

		}

		return $agg_data;
	}
}