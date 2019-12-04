<?php

namespace Tribe\HubSpot\Subscribe;

use Tribe\HubSpot\Process\Delivery_Queue;

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
	 * @param array  $qty            An array of data for total tickets, total number of events, and total types of tickets.
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
	 * @param int    $post_id     The ID of an event.
	 * @param int    $ticket_id   The ticket product id.
	 * @param int    $attendee_id The ID of an attendee.
	 * @param string $commerce    The commerce key for ET and ET+ (woo, edd, tpp, rsvp).
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
				'event_venue'                => isset( $venue->post_title ) ? $venue->post_title : null,
				'event_venue_address'        => isset( $venue->event_venue_address ) ? $venue->event_venue_address : null,
				'event_venue_city'           => isset( $venue->event_venue_city ) ? $venue->event_venue_city : null,
				'event_venue_state_province' => isset( $venue->event_venue_state_province ) ? $venue->event_venue_state_province : null,
				'event_venue_postal_code'    => isset( $venue->event_venue_postal_code ) ? $venue->event_venue_postal_code : null,
				'event_organizer'            => implode( ', ', $event->organizers->all() ),
				'event_is_featured'          => $event->featured,
				'event_cost'                 => html_entity_decode( $event->cost ),
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
	 * Maybe Update HubSpot Contact.
	 *
	 * @since 1.0
	 *
	 * @param string                               $provider_key  The key for the provider.
	 * @param int                                  $post_id       The ID of the event.
	 * @param int                                  $product_id    The ID of the product(ticket).
	 * @param int                                  $attendee_id   The ID of an attendee.
	 * @param array                                $attendee_data An array of attendee data.
	 * @param array                                $qty           An array of data for total tickets, total number of events, and total types of tickets.
	 * @param \Tribe\HubSpot\Properties\Event_Data $data          An event data object used to get and organize values for HubSpot.
	 */
	public function maybe_register_contact( $provider_key, $post_id, $product_id, $attendee_id, $attendee_data, $qty, $data ) {

		$rsvp_status = null;
		if ( 'rsvp' === $provider_key ) {
			$rsvp_status = $attendee_data['status'];
		}

		$groups   = [];
		$groups[] = $data->get_event_values( 'last_registered_', $post_id );
		$groups[] = $data->get_order_values( 'last_order_', $attendee_data['date'], $attendee_data['total'], $qty['total'], count( $qty['tickets'] ) );
		$groups[] = $data->get_ticket_values( $product_id, $attendee_id, $provider_key, $attendee_data['name'], $rsvp_status );

		$properties = $this->get_initial_properties_array( $attendee_data );
		$properties = array_merge( $properties, ...$groups );

		$order_data = $this->get_order_data_array( $attendee_data, $qty, 'register' );

		$this->maybe_push_to_contact_queue( $attendee_data, $properties, $order_data );
	}

	/**
	 * Maybe Update HubSpot Contact.
	 *
	 * @since 1.0
	 *
	 * @param array $attendee_data An array of attendee data.
	 * @param array $qty           An array of data for total tickets, total number of events, and total types of tickets.
	 */
	public function maybe_update_contact( $attendee_data, $qty ) {

		$properties = $this->get_initial_properties_array( $attendee_data );
		$order_data = $this->get_order_data_array( $attendee_data, $qty, '' );

		$this->maybe_push_to_contact_queue( $attendee_data, $properties, $order_data );
	}

	/**
	 * Maybe Update HubSpot Contact
	 *
	 * @since 1.0
	 *
	 * @param array                                $related_data  An array of order information for an Attendee.
	 * @param array                                $attendee_data An array of attendee data.
	 * @param array                                $qty           An array of data for total tickets, total number of events, and total types of tickets.
	 * @param \Tribe\HubSpot\Properties\Event_Data $data          An event data object used to get and organize values for HubSpot.
	 */
	public function maybe_checkin_contact( $related_data, $attendee_data, $qty, $data ) {

		$groups   = [];
		$groups[] = $data->get_event_values( 'last_attended_', $related_data['post_id'] );

		$properties = $this->get_initial_properties_array( $attendee_data );
		$properties = array_merge( $properties, ...$groups );

		$order_data = $this->get_order_data_array( $attendee_data, $qty, 'checkin' );

		$this->maybe_push_to_contact_queue( $attendee_data, $properties, $order_data );
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
	 * @param int    $post_id       The ID of an event.
	 * @param int    $attendee_id   The ID of an attendee.
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
	 * Get the First Attendee in an Order.
	 *
	 * @since 1.0
	 *
	 * @param int    $order_id     ID of an Order.
	 * @param string $provider_key Key for Provider.
	 *
	 * @return int An attendee ID.
	 */
	public function get_first_attendee_id_from_order( $order_id, $provider_key ) {

		/** @var $provider \Tribe__Tickets__RSVP */
		$provider = tribe( 'tickets.rsvp' );
		if ( 'woo' === $provider_key ) {
			/** @var $provider \Tribe__Tickets_Plus__Commerce__WooCommerce__Main */
			$provider = tribe( 'tickets-plus.commerce.woo' );
		} elseif ( 'edd' === $provider_key ) {
			/** @var $provider \Tribe__Tickets_Plus__Commerce__EDD__Main */
			$provider = tribe( 'tickets-plus.commerce.edd' );
		} elseif ( 'tpp' === $provider_key ) {
			/** @var $provider \Tribe__Tickets__Commerce__PayPal__Main */
			$provider = tribe( 'tickets.commerce.paypal' );
		}

		$attendees = $provider->get_attendees_by_id( $order_id );

		$attendee = reset( $attendees );

		return $attendee['attendee_id'];
	}

	/**
	 * Spilt Full Name into First, Middle, and Last.
	 * https://stackoverflow.com/a/31330346
	 *
	 * @since 1.0
	 *
	 * @param string $string The Name to parse.
	 *
	 * @return array|bool The First, Middle, and Last Name or False if to many names provided.
	 */
	public function split_name( $string ) {
		$arr        = explode( ' ', $string );
		$num        = count( $arr );
		$first_name = $middle_name = $last_name = null;

		if ( $num == 2 ) {
			list( $first_name, $last_name ) = $arr;
		} else {
			list( $first_name, $middle_name, $last_name ) = $arr;
		}

		return ( empty( $first_name ) || $num > 3 ) ? false : compact( 'first_name', 'middle_name', 'last_name' );
	}
}