<?php

namespace Tribe\HubSpot\Subscribe;

/**
 * Class Update
 *
 * @package Tribe\HubSpot\Subscribe
 */
class Update extends Base {

	/**
	 * Setup Hooks to SubScribe to Check
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'event_tickets_plus_attendee_meta_update', [ $this, 'woo_subscribe' ], 10, 3 );
		add_action( 'event_tickets_plus_attendee_meta_update', [ $this, 'edd_subscribe' ], 10, 3 );
		add_action( 'event_tickets_rsvp_after_attendee_update', [ $this, 'rsvp_subscribe' ], 10, 3 );
		add_action( 'event_tickets_plus_attendee_meta_update', [ $this, 'tpp_subscribe' ], 10, 3 );

		// Timeline Events Should be Added Second to the Queue so the Contact Can Be Created.
		//add_action( 'event_tickets_plus_attendee_meta_update', [ $this, 'woo_timeline' ], 100, 3 );
	}

	/**
	 * Maybe Update HubSpot Contact
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
	 * Connect to Update of an Attendee for WooCommerce.
	 *
	 * @since 1.0
	 *
	 * @param array $data        An array of information that was saved for the attendee.
	 * @param int   $attendee_id The ID of an attendee.
	 * @param int   $post_id     The ID of an event.
	 */
	public function woo_subscribe( $data, $attendee_id, $post_id ) {

		// Detect if this an WooCommerce Attendee.
		$provider = tribe( 'tickets.data_api' )->get_ticket_provider( $attendee_id );
		if ( 'woo' !== ( $provider->orm_provider ) ) {
			return;
		}

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_woo_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $related_data['order'] );
		$qty           = $data->get_woo_order_quantities( $related_data['order'] );

		$this->maybe_update_contact( $attendee_data, $qty );
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
	public function edd_subscribe( $data, $attendee_id, $post_id ) {

		// Detect if this an EDD Attendee.
		$provider = tribe( 'tickets.data_api' )->get_ticket_provider( $attendee_id );
		if ( 'edd' !== ( $provider->orm_provider ) ) {
			return;
		}

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_edd_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_edd_contact_data_from_order( $related_data['order'], $related_data['order_id'] );
		$qty           = $data->get_edd_order_quantities( $related_data['order'] );

		$this->maybe_update_contact( $attendee_data, $qty );
	}

	/**
	 * Connect to Update of an Attendee for RSVP.
	 *
	 * @since 1.0
	 *
	 * @param int    $order_id              Refers to the attendee or ticket ID per this methods $order_id parameter.
	 * @param int    $event_id              The ID of an event.
	 * @param string $attendee_order_status The status of the attendee, either yes or no.
	 */
	public function rsvp_subscribe( $order_id, $event_id, $attendee_order_status ) {

		// Detect if this an RSVP Attendee.
		$provider = tribe( 'tickets.data_api' )->get_ticket_provider( $order_id );
		if ( 'rsvp' !== ( $provider->orm_provider ) ) {
			return;
		}

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_rsvp_related_data_by_attendee_id( $order_id );
		$attendee_data = $this->get_rsvp_contact_data_from_order( $related_data['order'] );
		$qty           = $data->get_rsvp_order_quantities( $related_data['order'] );

		$this->maybe_update_contact( $attendee_data, $qty );
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
	public function tpp_subscribe( $data, $attendee_id, $post_id ) {

		// Detect if this an TPP Attendee.
		$provider = tribe( 'tickets.data_api' )->get_ticket_provider( $attendee_id );
		if ( 'tribe-commerce' !== ( $provider->orm_provider ) ) {
			return;
		}

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_tpp_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_tpp_contact_data_from_order( $related_data['order'] );
		$qty           = $data->get_tpp_order_quantities( $related_data['order'] );

		$this->maybe_update_contact( $attendee_data, $qty );
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

		// Detect if this an WooCommerce Attendee.
		$provider = tribe( 'tickets.data_api' )->get_ticket_provider( $attendee_id );
		if ( 'woo' !== ( $provider->orm_provider ) ) {
			return;
		}

		$related_data  = $this->get_woo_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $related_data['order'] );
		$extra_data    = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'woo', $attendee_data['name'] );

		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_attendee_update_id', $post_id, $attendee_id, $extra_data );
	}
}