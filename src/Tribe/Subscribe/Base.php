<?php

namespace Tribe\HubSpot\Subscribe;

use Tribe\HubSpot\Process\Delivery_Queue;

/**
 * Class Base
 *
 * @package Tribe\HubSpot\API
 */


/**
 * Class Base
 *
 * @package Tribe\HubSpot\Subscribe
 */
abstract class Base {

	/**
	 * Get a Standard Array of the Properties to Send to HubSpot.
	 *
	 * @since 1.0
	 *
	 * @param array $attendee_data An array of contact information for the attendee.
	 *
	 * @return array An array of initial properties to send to HubSpot.
	 */
	public function get_initial_properties_array( $attendee_data ) {

		$properties = [
			[
				'property' => 'firstname',
				'value'    => $attendee_data['first_name'],
			],
			[
				'property' => 'lastname',
				'value'    => $attendee_data['last_name'],
			],
		];

		return $properties;
	}

	/**
	 * Get a Standard Array of Order Data.
	 *
	 * @since 1.0
	 *
	 * @param array  $attendee_data  An array of contact information for the attendee.
	 * @param array  $qty            An array of data for total tickets, total number of events, and total types of tickets
	 * @param string $aggregate_type The type of fields to calculate aggregate data for.
	 *
	 * @return array An array of order date to use to calculate aggregate data and first order custom properties.
	 */
	public function get_order_data_array( $attendee_data, $qty, $aggregate_type ) {

		$order_data = [
			'order_date'                 => $attendee_data['date'],
			'order_total'                => $attendee_data['total'],
			'order_ticket_quantity'      => $qty['total'],
			'order_ticket_type_quantity' => count( $qty['tickets'] ),
			'events_per_order'           => $qty['events_per_order'],
			'aggregate_type'             => $aggregate_type,
		];

		return $order_data;
	}

	/**
	 * Get the Extra Data for HubSpot Timeline Events.
	 *
	 * @param int    $post_id     the ID of an event.
	 * @param int    $ticket_id   The ticket product id
	 * @param int    $attendee_id the ID of an attendee.
	 * @param string $commerce    The commerce key for ET and ET+ (woo,edd,tpp,rsvp).
	 * @param string $name        The name of the Attendee.
	 * @param null   $rsvp        The optional RSVP status of going (1) and not going (0).
	 *
	 * @return array An array of data for HubSpot extra data.
	 */
	public function get_extra_data( $post_id, $ticket_id, $attendee_id, $commerce, $name, $rsvp = null ) {

		$event = tribe_get_event( $post_id );
		$venue = $event->venues->all();
		$venue = reset( $venue );

		$extra_data = [
			'event'  => [
				'event_id'                   => $event->ID,
				'event_name'                 => $event->post_title,
				'event_venue'                => $venue->post_title,
				'event_venue_address'        => $venue->event_venue_address,
				'event_venue_city'           => $venue->event_venue_city,
				'event_venue_state_province' => $venue->event_venue_state_province,
				'event_venue_postal_code'    => $venue->event_venue_postal_code,
				'event_organizer'            => implode( ', ', $event->organizers->all() ),
				'event_is_featured'          => $event->featured,
				'event_cost'                 => $event->cost,
				'event_start_datetime_utc'   => date( 'U', strtotime( 'midnight', ( \Tribe__Date_Utils::wp_strtotime( $event->start_date_utc ) ) ) ) * 1000,
				'event_start_time_utc'       => date( 'U', strtotime( $event->start_date_utc ) ),
				'event_timezone'             => $event->timezone,
				'event_duration'             => $event->duration,
			],
			'ticket' => [
				'ticket_type_id'       => $ticket_id,
				'ticket_type'          => get_the_title( $ticket_id ),
				'ticket_commerce'      => $commerce,
				'ticket_attendee_id'   => $attendee_id,
				'ticket_attendee_name' => $name,
				'ticket_rsvp_is_going' => $rsvp,
			],
		];

		return $extra_data;
	}

	/**
	 * Connect to Creation of an Attendee for WooCommerce.
	 *
	 * @since 1.0
	 *
	 * @param array $attendee_data An array of contact information for the attendee.
	 * @param array $properties    An array of custom HubSpot Properties with name and value per property.
	 * @param array $order_data    An array of order information for the attendee.
	 */
	public function maybe_push_to_contact_queue( $attendee_data, $properties, $order_data ) {

		if ( ! empty( $attendee_data['email'] ) ) {

			$hubspot_data = [
				'type'       => 'contact',
				'email'      => $attendee_data['email'],
				'properties' => $properties,
				'order_data' => $order_data,
			];

			$queue = new Delivery_Queue();
			$queue->push_to_queue( $hubspot_data );
			$queue->save();
			$queue->dispatch();
		}

	}

	/**
	 * Connect to Creation of an Attendee for WooCommerce.
	 *
	 * @since 1.0
	 *
	 * @param array  $attendee_data An array of contact information for the attendee.
	 * @param string $type_site_id  The name of the site option field of event to create ( Registration, Update, Check-In ).
	 * @param int    $post_id       the ID of an event.
	 * @param int    $attendee_id   the ID of an attendee.
	 * @param array  $extra_data    An array of event and ticket data to include with the Timeline Event.
	 */
	public function maybe_push_to_timeline_queue( $attendee_data, $type_site_id, $post_id, $attendee_id, $extra_data ) {

		if ( ! empty( $attendee_data['email'] ) ) {

			$hubspot_data = [
				'type'              => 'timeline',
				'event_type'        => $type_site_id,
				'timeline_event_id' => "event-checkin:{$post_id}:{$attendee_id}",
				'email'             => $attendee_data['email'],
				'extra_data'        => $extra_data,
			];

			$queue = new Delivery_Queue();
			$queue->push_to_queue( $hubspot_data );
			$queue->save();
			$queue->dispatch();
		}
	}

	/**
	 * Get WooCommerce Order Related Data by Attendee ID.
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id the ID of an attendee.
	 *
	 * @return array An array of related data ( Order ID, Order Object, Post ID, and Product ID )
	 */
	public function get_woo_related_data_by_attendee_id( $attendee_id ) {

		/** @var $commerce_woo \Tribe__Tickets_Plus__Commerce__WooCommerce__Main */
		$commerce_woo = tribe( 'tickets-plus.commerce.woo' );

		$related_data['order_id']  = get_post_meta( $attendee_id, $commerce_woo->attendee_order_key, true );
		$related_data['order']     = new \WC_Order( $related_data['order_id'] );
		$related_data['post_id']   = get_post_meta( $attendee_id, $commerce_woo->attendee_event_key, true );
		$related_data['ticket_id'] = get_post_meta( $attendee_id, $commerce_woo->attendee_product_key, true );

		return $related_data;
	}

	/**
	 * Get the First Attendee in an Order.
	 *
	 * @since 1.0
	 *
	 * @param int $order_id ID of the WooCommerce Order.
	 *
	 * @return int An attendee Id
	 */
	public function get_woo_first_attendee_id_from_order( $order_id ) {

		/** @var $commerce_woo \Tribe__Tickets_Plus__Commerce__WooCommerce__Main */
		$commerce_woo = tribe( 'tickets-plus.commerce.woo' );

		$attendees = $commerce_woo->get_attendees_by_id( $order_id );

		$attendee = reset( $attendees );

		return $attendee[ 'attendee_id' ];
	}

	/**
	 * Connect to Creation of an Attendee for WooCommerce.
	 *
	 * @since 1.0
	 *
	 * @param object $order WooCommerce order object \WC_Order.
	 *
	 * @return array An array of attendee data from a WooCommerce Order.
	 */
	public function get_woo_contact_data_from_order( $order ) {

		$attendee_data['email']      = $order->get_billing_email();
		$attendee_data['name']       = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$attendee_data['first_name'] = $order->get_billing_first_name();
		$attendee_data['last_name']  = $order->get_billing_last_name();
		$attendee_data['total']      = $order->get_total();
		$attendee_data['date']       = $order->get_date_created()->getTimestamp();

		return $attendee_data;
	}
}