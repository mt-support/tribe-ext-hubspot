<?php

namespace Tribe\HubSpot\Subscribe;

use Tribe\HubSpot\Process\Delivery_Queue;

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
	 * @param object $order       WooCommerce order object /WC_Order.
	 * @param int    $product_id  WooCommerce product ID.
	 */
	public function woo_subscribe( $attendee_id, $post_id, $order, $product_id ) {

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data  = tribe( 'tickets.hubspot.properties.event_data' );
		$email = $order->get_billing_email();
		$name  = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$total = $order->get_total();
		$date  = $order->get_date_created()->getTimestamp();
		$qty   = $data->get_woo_order_quantities( $order );

		$groups   = [];
		$groups[] = $data->get_event_values( 'last_registered_', $post_id );
		$groups[] = $data->get_order_values( 'last_order_', $date, $total, $qty['total'], count( $qty['tickets'] ) );
		$groups[] = $data->get_ticket_values( $product_id, $attendee_id, 'woo', $name );

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

		$order_data = [
			'order_date'                 => $date,
			'order_total'                => $total,
			'order_ticket_quantity'      => $qty['total'],
			'order_ticket_type_quantity' => count( $qty['tickets'] ),
			'events_per_order'           => $qty['events_per_order'],
		];

		// Send to Queue Process.
		if ( ! empty( $email ) ) {

			$hubspot_data = [
				'type'       => 'contact',
				'email'      => $email,
				'properties' => $properties,
				'order_data' => $order_data,
			];

			$queue = new Delivery_Queue();
			$queue->push_to_queue( $hubspot_data );
			$queue->save();
			$queue->dispatch();
		}

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

		$email  = $order->get_billing_email();
		$event = tribe_get_event( $post_id );
		$extra_data = [
			'event' => [
				'ID' => $event->ID,
				'post_title' => $event->post_title,
			]
		];

		// Send to Queue Process.
		if ( ! empty( $email ) ) {

			$hubspot_data = [
				'type'              => 'timeline',
				'event_type'        => 'timeline_event_registration_id',
				'timeline_event_id' => "event-register:{$post_id}:{$attendee_id}",
				'email'             => $email,
				'extra_data'        => $extra_data,
			];

			$queue = new Delivery_Queue();
			$queue->push_to_queue( $hubspot_data );
			$queue->save();
			$queue->dispatch();
		}

	}
}