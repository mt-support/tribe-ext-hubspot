<?php

namespace Tribe\HubSpot\Subscribe;

/**
 * Class Checkin
 *
 * @package Tribe\HubSpot\Subscribe
 */
class Checkin extends Base {

	/**
	 * Setup Hooks to SubScribe to Check
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'wootickets_checkin', [ $this, 'woo_subscribe' ] );
		// Timeline Events Should be Added Second to the Queue so the Contact Can Be Created.
		add_action( 'wootickets_checkin', [ $this, 'woo_timeline' ], 100 );
	}

	/**
	 * Connect to Checkin of an Attendee for WooCommerce
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id The id of the attendee who was checked in.
	 */
	public function woo_subscribe( $attendee_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_woo_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $related_data['order'] );
		$qty           = $data->get_woo_order_quantities( $related_data['order'] );

		$groups   = [];
		$groups[] = $data->get_event_values( 'last_attended_', $related_data['post_id'] );

		$properties = $this->get_initial_properties_array( $attendee_data );
		$properties = array_merge( $properties, ...$groups );

		$order_data = $this->get_order_data_array( $attendee_data, $qty, 'checkin' );

		$this->maybe_push_to_contact_queue( $attendee_data, $properties, $order_data );
	}

	/**
	 * Connect to Checkin of an Attendee for WooCommerce
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id The id of the attendee who was checked in.
	 */
	public function woo_timeline( $attendee_id ) {

		$related_data  = $this->get_woo_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $related_data['order'] );
		$extra_data    = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'woo', $attendee_data['name'] );

		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_checkin_id', $related_data['post_id'], $attendee_id, $extra_data );
	}
}