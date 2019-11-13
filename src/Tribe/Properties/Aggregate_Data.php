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
				'label'          => 'Total Registered Events',
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'register',
			],
			'total_number_of_orders'    => [
				'label'          => 'Total Number of Orders',
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'register',
			],
			'average_tickets_per_order' => [
				'label'          => 'Average Tickets Per Order',
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'register',
			],
			'average_tickets_per_order_list' => [
				'label'          => 'Average Tickets Per Order List',
				'groupName'      => $this->group_name,
				'type'           => 'string',
				'fieldType'      => 'text',
			],
			'average_events_per_order'  => [
				'label'          => 'Average Events Per Order',
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'register',
			],
			'average_events_per_order_list'  => [
				'label'          => 'Average Events Per Order List',
				'groupName'      => $this->group_name,
				'type'           => 'string',
				'fieldType'      => 'text',
			],
			'total_attended_events'     => [
				'label'          => 'Total Attended Events',
				'groupName'      => $this->group_name,
				'type'           => 'number',
				'fieldType'      => 'number',
				'subscribe_type' => 'checkin',
			],
		];
	}

	/**
	 * Get Aggregate Values and Format for HubSpot
	 *
	 * @since 1.0
	 *
	 * @param string $subscribe_type     The name of the properties to setup values for.
	 * @param object $current_properties An object of custom properties with values from HubSpot.
	 * @param int    $tickets_qty        The total number of tickets in this order.
	 * @param int    $events             The amount of different events in this order.
	 *
	 * @return array An array of properties with values to update the contact in HubSpot.
	 */
	public function get_values( $subscribe_type, $current_properties, $tickets_qty, $events_per_order ) {
		$agg_data            = [];
		$matching_properties = wp_filter_object_list( $this->properties, [ 'subscribe_type' => $subscribe_type ], 'and' );

		foreach ( $matching_properties as $key => $property ) {

			$value = 1;
			if ( isset( $current_properties->{$key} ) ) {
				$current_properties->{$key}->value ++;
				$value = $current_properties->{$key}->value;
			}

			if ( 'total_registered_events' === $key ) {
				$agg_data['total_registered_events'] = $value;
			}

			// Handle Average Tickets Per Order
			if ( 'average_tickets_per_order' === $key ) {
				$values               = $this->get_average_from_list( $current_properties, 'average_tickets_per_order_list', $tickets_qty );
				$value                = $values['current_value'];
				$agg_data['values'][] = [
					'property' => 'average_tickets_per_order_list',
					'value'    => $values['list'],
				];
			}

			// Handle Average Events Per Order
			if ( 'average_events_per_order' === $key ) {
				$values               = $this->get_average_from_list( $current_properties, 'average_events_per_order_list', $events_per_order );
				$value                = $values['current_value'];
				$agg_data['values'][] = [
					'property' => 'average_events_per_order_list',
					'value'    => $values['list'],
				];
			}

			$agg_data['values'][] = [
				'property' => $key,
				'value'    => $value,
			];

		}

		return $agg_data;
	}

	/**
	 * Get the average values from a comma separated list of values in HubSpot with the provided key.
	 *
	 * @since 1.0
	 *
	 * @param object $current_properties An object of custom properties with values from HubSpot.
	 * @param string $list_key           The key to get values from the current properties.
	 * @param int    $qty                The new value to use to calculate the average.
	 *
	 * @return array An array of the values with the current and average value.
	 */
	public function get_average_from_list( $current_properties, $list_key, $qty ) {

		if ( ! isset( $current_properties->{$list_key} ) ) {
			$values = [
				'current_value' => $qty,
				'list'          => $qty,
			];

			return $values;
		}

		$list = $current_properties->{$list_key}->value . ',' . $qty;

		$array = explode( ',', $list );

		// Removes empty values including 0, which is acceptable as orders should have at least 1 ticket and 1 event.
		$array = array_filter( $array );

		// Prevent Dividing by 0.
		if ( count( $array ) ) {
			$average = array_sum( $array ) / count( $array );
		} else {
			$average = $qty;
		}

		$values = [
			'current_value' => $average,
			'list'          => $list,
		];

		return $values;
	}
}