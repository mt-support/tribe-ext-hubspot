<?php

namespace Tribe\HubSpot\Subscribe;

/**
 * Class TPP
 *
 * @package Tribe\HubSpot\Subscribe
 */
class TPP extends Base {

	/**
	 * Setup Hooks
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'event_tickets_tpp_tickets_generated', [ $this, 'register' ], 10, 2 );
		add_action( 'event_tickets_plus_attendee_meta_update', [ $this, 'update' ], 10, 3 );
		add_action( 'event_tickets_checkin', [ $this, 'checkin' ] );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Creation of a TPP Order.
	 *
	 * @since 1.0
	 *
	 * @param int $order_id The TPP Order ID,
	 * @param int $post_id  The ID of the event.
	 */
	public function register( $order_id, $post_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$order = \Tribe__Tickets__Commerce__PayPal__Order::from_order_id( $order_id );

		$attendee_data = $this->get_tpp_contact_data_from_order( $order );
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'tpp' );
		$product_id    = reset( $order->get_ticket_ids() );
		$qty           = $data->get_tpp_order_quantities( $order );

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_register_contact( 'tpp', $post_id, $product_id, $attendee_id, $attendee_data, $qty, $data );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $post_id, $product_id, $attendee_id, 'tpp', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_registration_id', $post_id, $attendee_id, $extra_data );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Update of TPP Attendee.
	 *
	 * @since 1.0
	 *
	 * @param array $data        An array of information that was saved for the attendee.
	 * @param int   $attendee_id The ID of an attendee.
	 * @param int   $post_id     The ID of an event.
	 */
	public function update( $data, $attendee_id, $post_id ) {

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

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_update_contact( $attendee_data, $qty );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'tpp', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_attendee_update_id', $post_id, $attendee_id, $extra_data );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Checkin of TPP Attendee.
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id The id of the attendee who was checked in.
	 */
	public function checkin( $attendee_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_tpp_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_tpp_contact_data_from_order( $related_data['order'] );
		$qty           = $data->get_tpp_order_quantities( $related_data['order'] );

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_checkin_contact( $related_data, $attendee_data, $qty, $data );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'tpp', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_checkin_id', $related_data['post_id'], $attendee_id, $extra_data );
	}

	/**
	 * Connect to Creation of an Attendee for TPP.
	 *
	 * @since 1.0
	 *
	 * @param \Tribe__Tickets__Commerce__PayPal__Order $order An order object for TPP.
	 *
	 * @return array An array of attendee data from a RSVP Order.
	 */
	public function get_tpp_contact_data_from_order( $order ) {

		$names = $this->split_name( $order->get_meta( 'address_name' ) );

		$attendee_data['email']      = $order->get_meta( 'payer_email' );
		$attendee_data['name']       = $order->get_meta( 'address_name' );
		$attendee_data['first_name'] = $names['first_name'];
		$attendee_data['last_name']  = $names['last_name'];
		$attendee_data['total']      = $order->get_line_total();
		$attendee_data['date']       = \Tribe__Date_Utils::wp_strtotime( $order->get_creation_date() );

		return $attendee_data;
	}

	/**
	 * Get TPP Order Related Data by Attendee ID.
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id the ID of an attendee.
	 *
	 * @return array An array of related data ( Order ID, Order Object, Post ID, and Product ID ).
	 */
	public function get_tpp_related_data_by_attendee_id( $attendee_id ) {

		/** @var $commerce_tpp \Tribe__Tickets__Commerce__PayPal__Main */
		$commerce_tpp = tribe( 'tickets.commerce.paypal' );

		$related_data['order_id']  = get_post_meta( $attendee_id, $commerce_tpp->order_key, true );
		$related_data['order']     = \Tribe__Tickets__Commerce__PayPal__Order::from_order_id( $related_data['order_id'] );
		$related_data['post_id']   = get_post_meta( $attendee_id, $commerce_tpp->attendee_event_key, true );
		$related_data['ticket_id'] = get_post_meta( $attendee_id, $commerce_tpp->attendee_product_key, true );

		return $related_data;
	}
}