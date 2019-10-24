<?php

namespace Tribe\HubSpot\Properties;

/**
 * Class Event_Data
 *
 * @package Tribe\HubSpot\Properties
 */
class Event_Data {

	public function __construct() {


		//add_action( 'admin_footer', [ $this, 'get_event_values' ] );
	}


	public function get_event_values( $post_id ) {

		$event      = tribe_get_event( $post_id );
		$event_data = [
			'event_id'                 => [
				'names' => [ 'last_registered_event_id', 'last_attended_event_id' ],
				'value' => $event->ID,
			],
			'event_name'               => [
				'names' => [ 'last_registered_event_name', 'last_attended_event_name' ],
				'value' => $event->post_title,
			],
			'event_organizer'          => [
				'names' => [ 'last_registered_event_organizer', 'last_attended_event_organizer' ],
				'value' => implode( ', ', $event->organizers->all() ),
			],
			'event_is_featured'        => [
				'names' => [ 'last_registered_event_is_featured', 'last_attended_event_is_featured' ],
				'value' => $event->featured,
			],
			'event_cost'               => [
				'names' => [ 'last_registered_event_cost', 'last_attended_event_cost' ],
				'value' => $event->cost,
			],
			'event_start_datetime_utc' => [
				'names' => [ 'last_registered_event_start_datetime_utc', 'last_attended_event_start_datetime_utc' ],
				'value' => $event->start_date,
			],
			'event_start_time_utc'     => [
				'names' => [ 'last_registered_event_start_time_utc', 'last_attended_event_start_time_utc' ],
				'value' => $event->start_date_utc,
			],
			'event_timezone'           => [
				'names' => [ 'last_registered_event_timezone', 'last_attended_event_timezone' ],
				'value' => $event->timezone,
			],
			'event_duration'           => [
				'names' => [ 'last_registered_event_duration', 'last_attended_event_duration' ],
				'value' => $event->duration,
			],
		];

		$venue_fields = $this->get_venue_values( $event->venues->all() );

		$event_data = array_merge( $event_data, $venue_fields );

		return $event_data;
	}

	public function get_venue_values( $venue ) {

		// We only support one venue, but we get an array so get the first value
		$venue      = reset( $venue );
		$venue_data = [
			'event_venue'                => [
				'names' => [ 'last_registered_event_venue', 'last_attended_event_venue' ],
				'value' => $venue->post_title,
			],
			'event_venue_address'        => [
				'names' => [ 'last_registered_event_venue_address', 'last_attended_event_venue_address' ],
				'value' => $venue->address,
			],
			'event_venue_cit'            => [
				'names' => [ 'last_registered_event_venue_city', 'last_attended_event_venue_city' ],
				'value' => $venue->city,
			],
			'event_venue_state_province' => [
				'names' => [ 'last_registered_event_venue_state_province', 'last_attended_event_venue_state_province' ],
				'value' => $venue->state_province,
			],
			'event_venue_postal_code'    => [
				'names' => [ 'last_registered_event_venue_postal_code', 'last_attended_event_venue_postal_code' ],
				'value' => $venue->zip,
			],
		];

		return $venue_data;
	}

	public function get_order_values( $order ) {

		$order_data = [
			'order_date_utc'             => [
				'names' => [ 'first_order_date_utc', 'last_order_date_utc' ],
				'value' => '',
			],
			'order_total'                => [
				'names' => [ 'first_order_total', 'last_order_total' ],
				'value' => '',
			],
			'order_ticket_quantity'      => [
				'names' => [ 'first_order_ticket_quantity', 'last_order_ticket_quantity' ],
				'value' => '',
			],
			'order_ticket_type_quantity' => [
				'names' => [ 'first_order_ticket_type_quantity', 'last_order_ticket_type_quantity' ],
				'value' => '',
			],
		];

		return $order_data;

	}

	public function get_ticket_values( $ticket_id, $attendee_id, $commerce, $name ) {

		$ticket_data = [
			'ticket_type_id'       => [
				'names' => [ 'last_registered_ticket_type_id' ],
				'value' => $ticket_id,
			],
			'ticket_type'          => [
				'names' => [ 'last_registered_ticket_type' ],
				'value' => get_the_title( $ticket_id ),
			],
			'ticket_commerce'      => [
				'names' => [ 'last_registered_ticket_commerce' ],
				'value' => $commerce,
			],
			'ticket_attendee_id'   => [
				'names' => [ 'last_registered_ticket_attendee_id' ],
				'value' => $attendee_id,
			],
			'ticket_attendee_name' => [
				'names' => [ 'last_registered_ticket_attendee_name' ],
				'value' => $name,
			],
			'rsvp_is_going'        => [
				'names' => [ 'last_registered_ticket_rsvp_is_going' ],
				'value' => '', //todo when connecting in the rsvp add the value here
			],
		];

		return $ticket_data;

	}

	public function get_woo_order_quantities( $order ) {

		$valid_order_items = [
			'total'   => 0,
			'tickets' => []
		];

		$order_items = $order->get_items();

		foreach ( (array) $order_items as $item_id => $item ) {
			$ticket_id = $item['product_id'];

			$ticket_event_id = absint(
				get_post_meta( $ticket_id, \Tribe__Tickets_Plus__Commerce__WooCommerce__Main::get_instance()->event_key, true )
			);

			// If not a ticket product then do not count
			if ( empty( $ticket_event_id ) ) {
				continue;
			}

			$quantities = empty( $item['qty'] ) ? 0 : intval( $item['qty'] );

			$valid_order_items['total']                 += $quantities;
			$valid_order_items['tickets'][ $ticket_id ] = $quantities;
		}

		return $valid_order_items;

	}

}