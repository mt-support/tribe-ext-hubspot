<?php

namespace Tribe\HubSpot\Subscribe;

/**
 * Class EDD
 *
 * @package Tribe\HubSpot\Subscribe
 */
class EDD extends Base {

	/**
	 * Setup Hooks
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'event_tickets_plus_edd_send_ticket_emails', [ $this, 'register' ] );
		add_action( 'event_tickets_plus_attendee_meta_update', [ $this, 'update' ], 10, 3 );
		add_action( 'eddtickets_checkin', [ $this, 'checkin' ] );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Creation of a EDD Order.
	 *
	 * @since 1.0
	 *
	 * @param int $order_id The EDD Order ID,
	 */
	public function register( $order_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		/** @var $commerce_edd \Tribe__Tickets_Plus__Commerce__EDD__Main */
		$commerce_edd = tribe( 'tickets-plus.commerce.edd' );

		$attendees   = $commerce_edd->get_attendees_by_id( $order_id );
		$attendee    = reset( $attendees );
		$attendee_id = $attendee['attendee_id'];
		$post_id     = $attendee['event_id'];
		$product_id  = $attendee['product_id'];

		$order         = edd_get_payment( $order_id );
		$attendee_data = $this->get_edd_contact_data_from_order( $order, $order_id );
		$qty           = $data->get_edd_order_quantities( $order );

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_register_contact( 'edd', $post_id, $product_id, $attendee_id, $attendee_data, $qty, $data );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $post_id, $product_id, $attendee_id, 'edd', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_registration_id', "event-register:{$post_id}:{$attendee_id}", $extra_data );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Update of EDD Attendee.
	 *
	 * @since 1.0
	 *
	 * @param array $data        An array of information that was saved for the attendee.
	 * @param int   $attendee_id The ID of an attendee.
	 * @param int   $post_id     The ID of an event.
	 */
	public function update( $data, $attendee_id, $post_id ) {

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

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_update_contact( $attendee_data, $qty );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'edd', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_attendee_update_id', "attendee-update:{$related_data['post_id']}:{$attendee_id}", $extra_data );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Update of EDD Attendee.
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id The id of the attendee who was checked in.
	 */
	public function checkin( $attendee_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_edd_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_edd_contact_data_from_order( $related_data['order'], $related_data['order_id'] );
		$qty           = $data->get_edd_order_quantities( $related_data['order'] );

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_checkin_contact( $related_data, $attendee_data, $qty, $data );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'edd', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_checkin_id', "event-checkin:{$related_data['post_id']}:{$attendee_id}", $extra_data );
	}

	/**
	 * Connect to Creation of an Attendee for EDD.
	 *
	 * @since 1.0
	 *
	 * @param object $order EDD order object \EDD_Payment.
	 *
	 * @return array An array of attendee data from a EDD Order.
	 */
	public function get_edd_contact_data_from_order( $order, $order_id ) {

		$user_info = edd_get_payment_meta_user_info( $order_id );

		$attendee_data['email']      = $user_info['email'];
		$attendee_data['name']       = $user_info['first_name'] . ' ' . $user_info['last_name'];
		$attendee_data['first_name'] = $user_info['first_name'];
		$attendee_data['last_name']  = $user_info['last_name'];
		$attendee_data['total']      = $order->total;
		$attendee_data['date']       = \Tribe__Date_Utils::wp_strtotime( $order->date );

		return $attendee_data;
	}

	/**
	 * Get EDD Order Related Data by Attendee ID.
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id the ID of an attendee.
	 *
	 * @return array An array of related data ( Order ID, Order Object, Post ID, and Product ID ).
	 */
	public function get_edd_related_data_by_attendee_id( $attendee_id ) {

		/** @var $commerce_edd \Tribe__Tickets_Plus__Commerce__EDD__Main */
		$commerce_edd = tribe( 'tickets-plus.commerce.edd' );

		$related_data['order_id']  = get_post_meta( $attendee_id, $commerce_edd->attendee_order_key, true );
		$related_data['order']     = edd_get_payment( $related_data['order_id'] );
		$related_data['post_id']   = get_post_meta( $attendee_id, $commerce_edd->attendee_event_key, true );
		$related_data['ticket_id'] = get_post_meta( $attendee_id, $commerce_edd->attendee_product_key, true );

		return $related_data;
	}
}