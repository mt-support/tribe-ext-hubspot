<?php

namespace Tribe\HubSpot\Subscribe;

use Tribe\HubSpot\Process\Delivery_Queue;

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
		// Timeline Events Should be Added Second to the Queue so the Contact Can Be Created.
		add_action( 'event_tickets_woocommerce_tickets_generated_for_product', [ $this, 'woo_timeline' ], 100, 4 );
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
		$attendee_id   = $this->get_woo_first_attendee_id_from_order( $order_id );
		$qty           = $data->get_woo_order_quantities( $order );

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
		$attendee_id   = $this->get_woo_first_attendee_id_from_order( $order_id );
		$extra_data    = $this->get_extra_data( $post_id, $product_id, $attendee_id, 'woo', $attendee_data['name'] );

		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_registration_id', $post_id, $attendee_id, $extra_data );
	}
}