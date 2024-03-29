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

		$event = tribe_get_event( $post_id );
		$is_event = $this->is_tribe_event( $event->post_type );

		$event_data = [
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
				'value'    => implode( ', ', $event->organizer_names->all() ),
			],
			[
				'property' => $prefix . 'event_is_featured',
				'value'    => $event->featured,
			],
			[
				'property' => $prefix . 'event_cost',
				'value'    => html_entity_decode( $event->cost ),
			],
			[
				'property' => $prefix . 'event_start_date_utc',
				'value'    => $is_event ? ms_timestamp( strtotime( 'midnight', ( \Tribe__Date_Utils::wp_strtotime( $event->start_date_utc ) ) ) ) : null,
			],
			[
				'property' => $prefix . 'event_start_datetime_utc',
				'value'    => $is_event ? ms_timestamp( date( 'U', strtotime( $event->start_date_utc ) ) ) : null,
			],
			[
				'property' => $prefix . 'event_timezone',
				'value'    => $is_event ? $event->timezone : null,
			],
			[
				'property' => $prefix . 'event_duration',
				'value'    => $is_event ? $event->duration : null,
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
				'value'    => isset( $venue->post_title ) ? $venue->post_title : null,
			],
			[
				'property' => $prefix . 'event_venue_address',
				'value'    => isset( $venue->address ) ? $venue->address : null,
			],
			[
				'property' => $prefix . 'event_venue_city',
				'value'    => isset( $venue->city ) ? $venue->city : null,
			],
			[
				'property' => $prefix . 'event_venue_state_province',
				'value'    => isset( $venue->state_province ) ? $venue->state_province : null,
			],
			[
				'property' => $prefix . 'event_venue_postal_code',
				'value'    => isset( $venue->zip ) ? $venue->zip : null,
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
				'value'    => ms_timestamp( $date )
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
	 * @param int       $ticket_id     The ticket product id.
	 * @param int       $attendee_id   the ID of an attendee.
	 * @param string    $commerce      The commerce key for ET and ET+ (woo,edd,tpp,rsvp).
	 * @param string    $name          The name of the Attendee.
	 * @param null|bool $rsvp_is_going 1|0 whether an Attendee is going or not.
	 *
	 * @return array
	 */
	public function get_ticket_values( $ticket_id, $attendee_id, $commerce, $name, $rsvp_is_going = null ) {

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
				'value'    => $rsvp_is_going,
			],
		];

		return $ticket_data;

	}

	/**
	 * Determine whether a post type is a TEC event.
	 *
	 * @param string $post_type The post type name.
	 *
	 * @return bool Whether a post type is a TEC event.
	 */
	protected function is_tribe_event( $post_type ) {

		 if ( 'tribe_events' === $post_type ) {
		 	return true;
         }

         return false;
	}

	/**
	 * Get Order Quantities for WooCommerce Orders
	 *
	 * @since 1.0
	 *
	 * @param object|\WC_Order $order WooCommerce order object.
	 *
	 * @return array An array of data for total tickets, total number of events, and total types of tickets.
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

			if ( isset( $valid_order_items['tickets'][ $ticket_id ] ) ) {
				$valid_order_items['tickets'][ $ticket_id ] += $quantities;
			} else {
				$valid_order_items['tickets'][ $ticket_id ] = $quantities;
			}
		}

		$valid_order_items['events_per_order'] = count( wp_parse_id_list( $valid_order_items['events_per_order'] ) );

		return $valid_order_items;

	}

	/**
	 * Get Order Quantities for EDD Orders
	 *
	 * @since 1.0
	 *
	 * @param \EDD_Payment $order EDD order object.
	 *
	 * @return array An array of data for total tickets, total number of events, and total types of tickets.
	 */
	public function get_edd_order_quantities( $order ) {

		$valid_order_items = [
			'total'   => 0,
			'tickets' => []
		];

		/** @var $commerce_edd \Tribe__Tickets_Plus__Commerce__EDD__Main */
		$commerce_edd = tribe( 'tickets-plus.commerce.edd' );

		$event_key = $commerce_edd->event_key;
		foreach ( $order->cart_details as $item ) {
			$ticket_id       = $item['id'];
			$ticket_event_id = absint( get_post_meta( $ticket_id, $event_key, true ) );

			// If not a ticket product then do not count
			if ( empty( $ticket_event_id ) ) {
				continue;
			}

			$quantities = empty( $item['quantity'] ) ? 0 : intval( $item['quantity'] );

			$valid_order_items['total']                 += $quantities;
			$valid_order_items['events_per_order'][]    = $ticket_event_id;

			if ( isset( $valid_order_items['tickets'][ $ticket_id ] ) ) {
				$valid_order_items['tickets'][ $ticket_id ] += $quantities;
			} else {
				$valid_order_items['tickets'][ $ticket_id ] = $quantities;
			}
		}

		$valid_order_items['events_per_order'] = count( wp_parse_id_list( $valid_order_items['events_per_order'] ) );

		return $valid_order_items;
	}

	/**
	 * Get Order Quantities for RSVP Orders
	 *
	 * @since 1.0
	 *
	 * @param array $order Array of Attendees from RSVP Order.
	 *
	 * @return array An array of data for total tickets, total number of events, and total types of tickets.
	 */
	public function get_rsvp_order_quantities( $order ) {

		$valid_order_items = [
			'total'            => 0,
			'tickets'          => [],
			'events_per_order' => 1,
		];

		foreach ( $order as $item ) {
			$ticket_id  = $item['product_id'];
			$quantities = 1;

			$valid_order_items['total']                 += $quantities;

			if ( isset( $valid_order_items['tickets'][ $ticket_id ] ) ) {
				$valid_order_items['tickets'][ $ticket_id ] += $quantities;
			} else {
				$valid_order_items['tickets'][ $ticket_id ] = $quantities;
			}
		}

		return $valid_order_items;
	}

	/**
	 * Get Order Quantities for TPP Orders
	 *
	 * @since 1.0
	 *
	 * @param \Tribe__Tickets__Commerce__PayPal__Order $order An order object for TPP.
	 *
	 * @return array An array of data for total tickets, total number of events, and total types of tickets.
	 */
	public function get_tpp_order_quantities( $order ) {

		$valid_order_items = [
			'total'            => 0,
			'tickets'          => [],
			'events_per_order' => 1,
		];

		foreach ( $order->get_attendees() as $item ) {
			$ticket_id  = $item['product_id'];
			$quantities = 1;

			$valid_order_items['total']                 += $quantities;

			if ( isset( $valid_order_items['tickets'][ $ticket_id ] ) ) {
				$valid_order_items['tickets'][ $ticket_id ] += $quantities;
			} else {
				$valid_order_items['tickets'][ $ticket_id ] = $quantities;
			}
		}

		return $valid_order_items;
	}
}
