<?php

namespace Tribe\HubSpot\Subscribe;

use Tribe\HubSpot\Process\Async as Process_Async;

/**
 * Class Connection
 *
 * @package Tribe\HubSpot\API
 */
class Purchase {

	/**
	 * Setup Hooks for OAuth
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {
		add_action( 'event_ticket_woo_attendee_created', [ $this, 'woo_subscribe' ], 10, 4 );
	}
	
	/**
	 * Connect to Creation of an Attendee for WooCommerce
	 *
	 * @since 1.0
	 *
	 * @param int    $attendee_id ID of attendee ticket.
	 * @param int    $post_id     ID of event.
	 * @param object $order       WooCommerce order object /WC_Order.
	 * @param int    $product_id  WooCommerce product ID.
	 */
	public function woo_subscribe( $attendee_id, $post_id, $order, $product_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );
		$email  = $order->get_billing_email();
		$name  = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$total = $order->get_total();
		$date = $order->get_date_created()->getTimestamp();
		$qty = $data->get_woo_order_quantities( $order );

		$groups = [];
		$groups[]  = $data->get_event_values( 'last_registered_', $post_id );
		$groups[]  = $data->get_order_values( 'last_order_', $date, $total, $qty['total'], count( $qty['tickets'] ) );
		$groups[]  = $data->get_ticket_values( $product_id, $attendee_id, 'woo', $name );

		$properties = [
			[
				'property' => 'firstname',
				'value'    => $order->get_billing_first_name(),
			],
			[
				'property' => 'lastname',
				'value'    => $order->get_billing_last_name(),
			],
		];

		$properties = array_merge( $properties, ...$groups );

		// Send to Async Process.
		if ( ! empty( $email ) ) {
			$hubspot_process = new Process_Async();
			$hubspot_process->set_email( $email );
			$hubspot_process->set_properties( $properties );
			$hubspot_process->dispatch();
		}

	}

}