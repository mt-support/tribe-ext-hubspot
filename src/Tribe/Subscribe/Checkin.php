<?php

namespace Tribe\HubSpot\Subscribe;

use Tribe\HubSpot\Process\Delivery_Queue;

/**
 * Class Checkin
 *
 * @package Tribe\HubSpot\API
 */
class Checkin {

	/**
	 * Setup Hooks to SubScribe to Check
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'wootickets_checkin', [ $this, 'woo_subscribe' ] );
		add_action( 'wootickets_checkin', [ $this, 'woo_timeline' ] );
	}

	/**
	 * Connect to Checkin of an Attendee for WooCommerce
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id The id of the attendee who was checked in.
	 */
	public function woo_subscribe( $attendee_id ) {

		/** @var $commerce_woo \Tribe__Tickets_Plus__Commerce__WooCommerce__Main */
		$commerce_woo = tribe( 'tickets-plus.commerce.woo' );

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data  = tribe( 'tickets.hubspot.properties.event_data' );

		$order_id = get_post_meta( $attendee_id, $commerce_woo->attendee_order_key, true );
		$post_id = get_post_meta( $attendee_id, $commerce_woo->attendee_event_key, true );
		$order = new \WC_Order( $order_id );
		$email = $order->get_billing_email();
		$total = $order->get_total();
		$date  = $order->get_date_created()->getTimestamp();
		$qty   = $data->get_woo_order_quantities( $order );

		$groups   = [];
		$groups[] = $data->get_event_values( 'last_attended_', $post_id );

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
			'aggregate_type'             => 'checkin',
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
	 * Connect to Checkin of an Attendee for WooCommerce
	 *
	 * @since 1.0
	 *
	 * @param int $attendee_id The id of the attendee who was checked in.
	 */
	public function woo_timeline( $attendee_id ) {

		/** @var $commerce_woo \Tribe__Tickets_Plus__Commerce__WooCommerce__Main */
		$commerce_woo = tribe( 'tickets-plus.commerce.woo' );

		$order_id = get_post_meta( $attendee_id, $commerce_woo->attendee_order_key, true );
		$post_id = get_post_meta( $attendee_id, $commerce_woo->attendee_event_key, true );
		$order = new \WC_Order( $order_id );
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
				'event_type'        => 'timeline_event_checkin_id',
				'timeline_event_id' => "event-checkin:{$post_id}:{$attendee_id}",
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