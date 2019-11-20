<?php

namespace Tribe\HubSpot\Subscribe;

/**
 * Class Purchase
 *
 * @package Tribe\HubSpot\Subscribe
 */
class Purchase extends Base {

	/**
	 * Setup Hooks to Subscribe to Purchases.
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		//add_action( 'event_tickets_woocommerce_tickets_generated_for_product', [ $this, 'woo_subscribe' ], 10, 4 );
		//add_action( 'admin_footer', [ $this, 'tpp_subscribe' ], 10 );
		//add_action( 'event_tickets_plus_edd_send_ticket_emails', [ $this, 'edd_subscribe' ], 10 );
		//add_action( 'event_tickets_rsvp_tickets_generated', [ $this, 'rsvp_subscribe' ], 10, 3 );
		add_action( 'event_tickets_tpp_tickets_generated', [ $this, 'tpp_subscribe' ], 10, 2 );

		// Timeline Events Should be Added Second to the Queue so the Contact Can Be Created.
		//add_action( 'event_tickets_woocommerce_tickets_generated_for_product', [ $this, 'woo_timeline' ], 100, 4 );
	}
	public function edd_subscribe( $order_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$order         = edd_get_payment( $order_id );
		$attendee_data = $this->get_edd_contact_data_from_order( $order, $order_id );
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'edd' );
		$qty = $data->get_edd_order_quantities( $order );
	}

	public function rsvp_subscribe( $order_id, $post_id, $attendee_order_status ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		/** @var $rsvp \Tribe__Tickets__RSVP */
		$rsvp = tribe( 'tickets.rsvp' );
		$order = $rsvp->get_attendees_by_id( $order_id );

		$attendee_data = $this->get_rsvp_contact_data_from_order( $order );
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'rsvp' );
		$qty = $data->get_rsvp_order_quantities( $order );
	}

	public function tpp_subscribe( $order_id, $post_id ) {
	//public function tpp_subscribe() {

		log_me($order_id );
		log_me($post_id );

		return;
		$order_id = '';
		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		/** @var $rsvp \Tribe__Tickets__Commerce__PayPal__Main */
		$rsvp = tribe( 'tickets.commerce.paypal' );
		$order = $rsvp->get_attendees_by_id( $order_id );

		$attendee_data = $this->get_rsvp_contact_data_from_order( $order );
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'rsvp' );
		$qty = $data->get_rsvp_order_quantities( $order );

		log_me( $attendee_data );
		log_me( $attendee_id );
		log_me( $qty );

	}
	/**
	 * Update HubSpot on Creation of a WooCommerce Attendee.
	 *
	 * @since 1.0
	 *
	 * @param int $product_id WooCommerce product ID.
	 * @param int $order_id   ID of the WooCommerce Order.
	 * @param int $quantity   The total Quantity of items in Order.
	 * @param int $post_id    ID of event.
	 */
	public function woo_subscribe( $product_id, $order_id, $quantity, $post_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$order         = new \WC_Order( $order_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $order );
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'woo');
		$qty           = $data->get_woo_order_quantities( $order );

		//todo make below a common method and pass the commerce
		$groups   = [];
		$groups[] = $data->get_event_values( 'last_registered_', $post_id );
		$groups[] = $data->get_order_values( 'last_order_', $attendee_data['date'], $attendee_data['total'], $qty['total'], count( $qty['tickets'] ) );
		$groups[] = $data->get_ticket_values( $product_id, $attendee_id, 'woo', $attendee_data['name'] );

		$properties = $this->get_initial_properties_array( $attendee_data );
		$properties = array_merge( $properties, ...$groups );

		$order_data = $this->get_order_data_array( $attendee_data, $qty, 'register' );

		$this->maybe_push_to_contact_queue( $attendee_data, $properties, $order_data );
	}

	/**
	 * Create Timeline Event for WooCommerce Attendee.
	 *
	 * @since 1.0
	 *
	 * @param int $product_id WooCommerce product ID.
	 * @param int $order_id   ID of the WooCommerce Order.
	 * @param int $quantity   The total Quantity of items in Order.
	 * @param int $post_id    ID of event.
	 */
	public function woo_timeline( $product_id, $order_id, $quantity, $post_id ) {

		$order         = new \WC_Order( $order_id );
		$attendee_data = $this->get_woo_contact_data_from_order( $order );
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'woo' );
		$extra_data    = $this->get_extra_data( $post_id, $product_id, $attendee_id, 'woo', $attendee_data['name'] );

		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_registration_id', $post_id, $attendee_id, $extra_data );
	}
}