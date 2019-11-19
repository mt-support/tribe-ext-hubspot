<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class Event_Data
 *
 * @package Tribe\HubSpot\Properties
 */
class Event_Data {

	/**
	 *  Get all the Event Values for the Custom Properties
	 *
	 * @since 1.0
	 *
	 * @param int $post_id A post ID.
	 *
	 * @return array An array of attributes with values
	 */
	public function get_event_values( $prefix, $post_id ) {

		$event      = tribe_get_event( $post_id );

		$event_data = [
			//todo number formatting is 16,108 in HubSpot
			[
				'property' => $prefix . 'event_id',
				'value'    => $event->ID,
			],
			[
				'property' => $prefix . 'event_name',
				'value'    => $event->post_title,
			],
			[
				'property' => $prefix . 'event_organizer',
				'value'    => implode( ', ', $event->organizers->all() ),
			],
			[
				'property' => $prefix . 'event_is_featured',
				'value'    => $event->featured,
			],
			//todo includes html
			[
				'property' => $prefix . 'event_cost',
				'value'    => $event->cost,
			],
			//todo must be midnight utc in milliseconds sending as 1576800000000 by hubspot uses 1576800000
			[
				'property' => $prefix . 'event_start_datetime_utc',
				'value'    => date( 'U', strtotime( 'midnight', ( \Tribe__Date_Utils::wp_strtotime( $event->start_date_utc ) ) ) ) * 1000,
			],
			//todo it does not show as a time only date
			[
				'property' => $prefix . 'event_start_time_utc',
				'value'    => date( 'U', strtotime( $event->start_date_utc ) ),
			],
			[
				'property' => $prefix . 'event_timezone',
				'value'    => $event->timezone,
			],
			[
				'property' => $prefix . 'event_duration',
				'value'    => $event->duration,
			],
		];

		$venue_fields = $this->get_venue_values( $prefix, $event->venues->all() );

		$event_data = array_merge( $event_data, $venue_fields );

		return $event_data;
	}

	/**
	 *  Get all the Event Values for the Custom Properties
	 *
	 * @since 1.0
	 *
	 * @param array $venue An array of WP_Post objects
	 *
	 * @return array An array of attributes with values
	 */
	public function get_venue_values( $prefix, $venue ) {

		// We only support one venue, but we get an array so get the first value
		$venue      = reset( $venue );
		$venue_data = [
			[
				'property' => $prefix . 'event_venue',
				'value'    => $venue->post_title,
			],
			[
				'property' => $prefix . 'event_venue_address',
				'value'    => $venue->address,
			],
			[
				'property' => $prefix . 'event_venue_city',
				'value'    => $venue->city,
			],
			[
				'property' => $prefix . 'event_venue_state_province',
				'value'    => $venue->state_province,
			],
			[
				'property' => $prefix . 'event_venue_postal_code',
				'value'    => $venue->zip,
			],
		];

		return $venue_data;
	}

	/**
	 * Get Order Custom Properties with Values
	 *
	 * @since 1.0
	 *
	 * @param int $date The date in unix timestamp in seconds
	 * @param int $total The total for an order of Tickets.
	 * @param int $quantity The total quantity of tickets purchased.
	 * @param int $type_quantity The different amount of ticket types in an Order.
	 *
	 * @return array
	 */
	public function get_order_values( $prefix, $date, $total, $quantity, $type_quantity ) {

		$order_data = [
			[
				'property' => $prefix . 'date_utc',
				'value'    => $date * 1000, //convert to milliseconds for HubSpot
			],
			[
				'property' => $prefix . 'total',
				'value'    => $total,
			],
			[
				'property' => $prefix . 'ticket_quantity',
				'value'    => $quantity,
			],
			[
				'property' => $prefix . 'ticket_type_quantity',
				'value'    => $type_quantity,
			],
		];

		return $order_data;

	}

	/**
	 * Get Ticket Custom Properties with Values
	 *
	 * @since 1.0
	 *
	 * @param int    $ticket_id   The ticket product id
	 * @param int    $attendee_id the ID of an attendee.
	 * @param string $commerce    The commerce key for ET and ET+ (woo,edd,tpp,rsvp).
	 * @param string $name        The name of the Attendee.
	 *
	 * @return array
	 */
	public function get_ticket_values( $ticket_id, $attendee_id, $commerce, $name ) {

		$ticket_data = [
			[
				'property' => 'last_registered_ticket_type_id',
				'value'    => $ticket_id,
			],
			[
				'property' => 'last_registered_ticket_type',
				'value'    => get_the_title( $ticket_id ),
			],
			[
				'property' => 'last_registered_ticket_commerce',
				'value'    => $commerce,
			],
			[
				'property' => 'last_registered_ticket_attendee_id',
				'value'    => $attendee_id,
			],
			[
				'property' => 'last_registered_ticket_attendee_name',
				'value'    => $name,
			],
			[
				'property' => 'last_registered_ticket_rsvp_is_going',
				'value'    => '', //todo when connecting in the rsvp add the value here
			],
		];

		return $ticket_data;

	}

	/**
	 * Get Order Quantities for WooCommerce Orders
	 *
	 * @since 1.0
	 *
	 * @param object $order WooCommerce order object \WC_Order.
	 *
	 * @return array An array of data for total tickets, total number of events, and total types of tickets
	 */
	public function get_woo_order_quantities( $order ) {

		$valid_order_items = [
			'total'   => 0,
			'tickets' => []
		];

		$order_items = $order->get_items();

		/** @var $commerce_woo \Tribe__Tickets_Plus__Commerce__WooCommerce__Main */
		$commerce_woo = tribe( 'tickets-plus.commerce.woo' );

		$event_key = $commerce_woo->event_key;
		foreach ( (array) $order_items as $item_id => $item ) {
			$ticket_id = $item['product_id'];

			$ticket_event_id = absint( get_post_meta( $ticket_id, $event_key, true ) );

			// If not a ticket product then do not count
			if ( empty( $ticket_event_id ) ) {
				continue;
			}

			$quantities = empty( $item['qty'] ) ? 0 : intval( $item['qty'] );

			$valid_order_items['total']                 += $quantities;
			$valid_order_items['events_per_order'][]    = $ticket_event_id;
			$valid_order_items['tickets'][ $ticket_id ] = $quantities;
		}

		$valid_order_items['events_per_order'] = count( wp_parse_id_list( $valid_order_items['events_per_order'] ) );

		return $valid_order_items;

	}

}
