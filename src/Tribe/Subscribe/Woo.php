<?php

namespace Tribe\HubSpot\Subscribe;

/**
 * Class Woo
 *
 * @package Tribe\HubSpot\Subscribe
 */
class Woo extends Base {

	/**
	 * Setup Hooks
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'event_tickets_woocommerce_tickets_generated_for_product', [ $this, 'register' ], 10, 4 );
		add_action( 'event_tickets_plus_attendee_meta_update', [ $this, 'update' ], 10, 3 );
		add_action( 'wootickets_checkin', [ $this, 'checkin' ] );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Creation of a WooCommerce Order.
	 *
	 * @since 1.0
	 *
	 * @param int $product_id WooCommerce product ID.
	 * @param int $order_id   ID of the WooCommerce Order.
	 * @param int $quantity   The total Quantity of items in Order.
	 * @param int $post_id    ID of event.
	 */
	public function register( $product_id, $order_id, $quantity, $post_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$order         = new \WC_Order( $order_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $order );
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'woo' );
		$qty           = $data->get_woo_order_quantities( $order );

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_register_contact( 'woo', $post_id, $product_id, $attendee_id, $attendee_data, $qty, $data );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $post_id, $product_id, $attendee_id, 'woo', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_registration_id', "event-register:{$post_id}:{$attendee_id}", $extra_data );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Update of WooCommerce Attendee.
	 *
	 * @since 1.0
	 *
	 * @param array $data        An array of information that was saved for the attendee.
	 * @param int   $attendee_id The ID of an attendee.
	 * @param int   $post_id     The ID of an event.
	 */
	public function update( $data, $attendee_id, $post_id ) {

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

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_update_contact( $attendee_data, $qty );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'woo', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_attendee_update_id', "attendee-update:{$related_data['post_id']}:{$attendee_id}", $extra_data );
	}

	/**
	 * Update HubSpot Contact and Timeline Event on Update of Checkin Attendee.
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id The id of the attendee who was checked in.
	 */
	public function checkin( $attendee_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$related_data  = $this->get_woo_related_data_by_attendee_id( $attendee_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $related_data['order'] );
		$qty           = $data->get_woo_order_quantities( $related_data['order'] );

		// Maybe Queue Updating the Contact in HubSpot.
		$this->maybe_checkin_contact( $related_data, $attendee_data, $qty, $data );

		// Extra Data for Timeline Event
		$extra_data = $this->get_extra_data( $related_data['post_id'], $related_data['ticket_id'], $attendee_id, 'woo', $attendee_data['name'] );

		// Maybe Queue Creating a Timeline Event in HubSpot.
		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_checkin_id', "event-checkin:{$related_data['post_id']}:{$attendee_id}", $extra_data );
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

	/**
	 * Get WooCommerce Order Related Data by Attendee ID.
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id the ID of an attendee.
	 *
	 * @return array An array of relatåed data ( Order ID, Order Object, Post ID, and Product ID ).
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
}