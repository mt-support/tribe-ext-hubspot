<?php

namespace Tribe\HubSpot\Subscribe;

/**
 * Class Update
 *
 * @package Tribe\HubSpot\Subscribe
 */
class Update extends Base {

	/**
	 * Setup Hooks to SubScribe to Check.
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'event_tickets_plus_attendee_meta_update', [ $this, 'woo_subscribe' ], 10, 3 );
		// Timeline Events Should be Added Second to the Queue so the Contact Can Be Created.
		add_action( 'event_tickets_plus_attendee_meta_update', [ $this, 'woo_timeline' ], 100, 3 );
	}

	/**
	 * Connect to Update of an Attendee for WooCommerce.
	 *
	 * @since 1.0
	 *
	 * @param array $data        An array of information that was saved for the attendee.
	 * @param int   $attendee_id The ID of an attendee.
	 * @param int   $post_id     The ID of an event.
	 */
	public function woo_subscribe( $data, $attendee_id, $post_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_woo_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $related_data['order'] );
		$qty           = $data->get_woo_order_quantities( $related_data['order'] );
		$properties    = $this->get_initial_properties_array( $attendee_data );
		$order_data    = $this->get_order_data_array( $attendee_data, $qty, '' );

		$this->maybe_push_to_contact_queue( $attendee_data, $properties, $order_data );
	}

	/**
	 * Connect to Update of an Attendee for WooCommerce.
	 *
	 * @since 1.0
	 *
	 * @param array $data        An array of information that was saved for the attendee.
	 * @param int   $attendee_id The ID of an attendee.
	 * @param int   $post_id     The ID of an event.
	 */
	public function woo_timeline( $data, $attendee_id, $post_id ) {

		$related_data  = $this->get_woo_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $related_data['order'] );
		$extra_data    = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'woo', $attendee_data['name'] );

		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_attendee_update_id', $post_id, $attendee_id, $extra_data );
	}
}
