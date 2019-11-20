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

		add_action( 'event_tickets_woocommerce_tickets_generated_for_product', [ $this, 'woo_subscribe' ], 10, 4 );
		add_action( 'event_tickets_plus_edd_send_ticket_emails', [ $this, 'edd_subscribe' ], 10 );
		add_action( 'event_tickets_rsvp_tickets_generated', [ $this, 'rsvp_subscribe' ], 10, 3 );
		add_action( 'event_tickets_tpp_tickets_generated', [ $this, 'tpp_subscribe' ], 10, 2 );

		// Timeline Events Should be Added Second to the Queue so the Contact Can Be Created.
		//add_action( 'event_tickets_woocommerce_tickets_generated_for_product', [ $this, 'woo_timeline' ], 100, 4 );
	}

	/**
	 * Maybe Update HubSpot Contact
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

		$groups   = [];
		$groups[] = $data->get_event_values( 'last_registered_', $post_id );
		$groups[] = $data->get_order_values( 'last_order_', $attendee_data['date'], $attendee_data['total'], $qty['total'], count( $qty['tickets'] ) );
		$groups[] = $data->get_ticket_values( $product_id, $attendee_id, $provider_key, $attendee_data['name'] );

		$properties = $this->get_initial_properties_array( $attendee_data );
		$properties = array_merge( $properties, ...$groups );

		$order_data = $this->get_order_data_array( $attendee_data, $qty, 'register' );

		$this->maybe_push_to_contact_queue( $attendee_data, $properties, $order_data );
	}

	/**
	 * Update HubSpot on Creation of a EDD Order.
	 *
	 * @since 1.0
	 *
	 * @param int $order_id The EDD Order ID,
	 */
	public function edd_subscribe( $order_id ) {

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

		$this->maybe_register_contact( 'edd', $post_id, $product_id, $attendee_id, $attendee_data, $qty, $data );
	}

	/**
	 * Update HubSpot on Creation of a RSVP Order.
	 *
	 * @since 1.0
	 *
	 * @param string $order_id              The RSVP Order Key
	 * @param int    $post_id               The ID of the event.
	 * @param string $attendee_order_status The status of the attendee, either yes or no.
	 */
	public function rsvp_subscribe( $order_id, $post_id, $attendee_order_status ) {

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


		$this->maybe_register_contact( 'rsvp', $post_id, $product_id, $attendee_id, $attendee_data, $qty, $data );
	}

	/**
	 * Update HubSpot on Creation of a TPP Order.
	 *
	 * @since 1.0
	 *
	 * @param int $order_id The TPP Order ID,
	 * @param int $post_id  The ID of the event.
	 */
	public function tpp_subscribe( $order_id, $post_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$order = \Tribe__Tickets__Commerce__PayPal__Order::from_order_id( $order_id );

		$attendee_data = $this->get_tpp_contact_data_from_order( $order );
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'tpp' );
		$product_id    = reset( $order->get_ticket_ids() );
		$qty           = $data->get_tpp_order_quantities( $order );

		$this->maybe_register_contact( 'tpp', $post_id, $product_id, $attendee_id, $attendee_data, $qty, $data );
	}

	/**
	 * Update HubSpot on Creation of a WooCommerce Order.
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
		$attendee_id   = $this->get_first_attendee_id_from_order( $order_id, 'woo' );
		$qty           = $data->get_woo_order_quantities( $order );

		$this->maybe_register_contact( 'woo', $post_id, $product_id, $attendee_id, $attendee_data, $qty, $data );
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