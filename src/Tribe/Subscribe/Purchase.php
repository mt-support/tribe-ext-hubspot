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
	 * Setup Hooks to SubScribe to Purchases
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'event_ticket_woo_attendee_created', [ $this, 'woo_subscribe' ], 10, 4 );
		// Timeline Events Should be Added Second to the Queue so the Contact Can Be Created.
		add_action( 'event_ticket_woo_attendee_created', [ $this, 'woo_timeline' ], 100, 4 );
	}

	/**
	 * Connect to Creation of an Attendee for WooCommerce
	 *
	 * @since 1.0
	 *
	 * @param int    $attendee_id ID of attendee ticket.
	 * @param int    $post_id     ID of event.
	 * @param object $order       WooCommerce order object \WC_Order.
	 * @param int    $product_id  WooCommerce product ID.
	 */
	public function woo_subscribe( $attendee_id, $post_id, $order, $product_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		$attendee_data = $this->get_woo_contact_data_from_order( $order );
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
	 * Create Timeline Event
	 *
	 * @since 1.0
	 *
	 * @param int    $attendee_id ID of attendee ticket.
	 * @param int    $post_id     ID of event.
	 * @param object $order       WooCommerce order object /WC_Order.
	 * @param int    $product_id  WooCommerce product ID.
	 */
	public function woo_timeline( $attendee_id, $post_id, $order, $product_id ) {

		$attendee_data = $this->get_woo_contact_data_from_order( $order );
		$extra_data    = $this->get_extra_data( $post_id, $product_id, $attendee_id, 'woo', $attendee_data['name'] );

		$this->maybe_push_to_timeline_queue( $attendee_data, 'timeline_event_registration_id', $post_id, $attendee_id, $extra_data );
	}
}