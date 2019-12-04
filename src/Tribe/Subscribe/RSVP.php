<?php

namespace Tribe\HubSpot\Subscribe;

/**
 * Class RSVP
 *
 * @package Tribe\HubSpot\Subscribe
 */
class RSVP extends Base {

	/**
	 * Setup Hooks
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'event_tickets_rsvp_tickets_generated', [ $this, 'register' ], 10, 3 );
		add_action( 'event_tickets_rsvp_after_attendee_update', [ $this, 'update' ], 10, 3 );
		add_action( 'rsvp_checkin', [ $this, 'checkin' ] );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Creation of a RSVP 'Order'.
	 *
	 * @since 1.0
	 *
	 * @param string $order_id              The RSVP Order Key
	 * @param int    $post_id               The ID of the event.
	 * @param string $attendee_order_status The status of the attendee, either yes or no.
	 */
	public function register( $order_id, $post_id, $attendee_order_status ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		/** @var $rsvp \Tribe__Tickets__RSVP */
		$rsvp       = tribe( 'tickets.rsvp' );
		$order      = $rsvp->get_attendees_by_id( $order_id );
		$attendees  = $order;
		$attendee   = reset( $attendees );
		$product_id = $attendee['product_id'];

		$attendee_data = $this->get_rsvp_contact_data_from_order( $order );
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'rsvp' );
		$qty           = $data->get_rsvp_order_quantities( $order );

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_register_contact( 'rsvp', $post_id, $product_id, $attendee_id, $attendee_data, $qty, $data );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $post_id, $product_id, $attendee_id, 'rsvp', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_registration_rsvp_id', "event-register:{$post_id}:{$attendee_id}", $extra_data );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Update of RSVP Attendee.
	 *
	 * @since 1.0
	 *
	 * @param int    $order_id              Refers to the attendee or ticket ID per this methods $order_id parameter.
	 * @param int    $event_id              The ID of an event.
	 * @param string $attendee_order_status The status of the attendee, either yes or no.
	 */
	public function update( $order_id, $event_id, $attendee_order_status ) {

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

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_update_contact( $attendee_data, $qty );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $order_id, 'rsvp', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_attendee_update_id', "attendee-update:{$related_data['post_id']}:{$order_id}", $extra_data );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Update of RSVP Attendee.
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id The id of the attendee who was checked in.
	 */
	public function checkin( $attendee_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_rsvp_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_rsvp_contact_data_from_order( $related_data['order'] );
		$qty           = $data->get_rsvp_order_quantities( $related_data['order'] );

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_checkin_contact( $related_data, $attendee_data, $qty, $data );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'rsvp', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_checkin_id', "event-checkin:{$related_data['post_id']}:{$attendee_id}", $extra_data );
	}

	/**
	 * Connect to Creation of an Attendee for RSVP.
	 *
	 * @since 1.0
	 *
	 * @param array $order Array of Attendees from RSVP Order.
	 *
	 * @return array An array of attendee data from a RSVP Order.
	 */
	public function get_rsvp_contact_data_from_order( $order ) {

		// Use the First Attendee for the Order Information.
		$attendee = reset( $order );
		$names    = $this->split_name( $attendee['holder_name'] );

		$attendee_data['email']      = $attendee['holder_email'];
		$attendee_data['name']       = $attendee['holder_name'];
		$attendee_data['first_name'] = $names['first_name'];
		$attendee_data['last_name']  = $names['last_name'];
		$attendee_data['total']      = 0;
		$attendee_data['date']       = get_the_date( 'U', $attendee['attendee_id'] );
		$attendee_data['status']     = 'Going' === $attendee['order_status_label'] ? 1 : 0;

		return $attendee_data;
	}

	/**
	 * Get RSVP Order Related Data by Attendee ID.
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id the ID of an attendee.
	 *
	 * @return array An array of related data ( Order ID, Order Object, Post ID, and Product ID ).
	 */
	public function get_rsvp_related_data_by_attendee_id( $attendee_id ) {

		/** @var $rsvp \Tribe__Tickets__RSVP */
		$rsvp = tribe( 'tickets.rsvp' );

		$related_data['order_id']  = get_post_meta( $attendee_id, $rsvp->order_key, true );
		$related_data['order']     = $rsvp->get_attendees_by_id( $related_data['order_id'] );
		$related_data['post_id']   = get_post_meta( $attendee_id, $rsvp::ATTENDEE_EVENT_KEY, true );
		$related_data['ticket_id'] = get_post_meta( $attendee_id, $rsvp::ATTENDEE_PRODUCT_KEY, true );

		return $related_data;
	}
}