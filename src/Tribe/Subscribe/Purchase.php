<?php

namespace Tribe\HubSpot\Subscribe;

use SevenShores\Hubspot\Factory;
use Tribe\HubSpot\Process\Async as Async;

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
		//add_action( 'admin_init', [ $this, 'createEventType' ], 10 );
		//add_action( 'admin_init', [ $this, 'createOrUpdate' ], 10 );
		//add_action( 'admin_init', [ $this, 'get_event_types' ], 10 );
		add_action( 'event_ticket_woo_attendee_created', [ $this, 'createOrUpdate' ], 10, 4 );
		//add_action( 'event_ticket_woo_attendee_created', [ $this, 'woo_subscribe' ], 10, 4 );
	}

	public function createEventType() {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return false;
		}

		$client = $hubspot_api->client;

		$appId          = 203977;
		$name           = 'eventRegistration';
		$headerTemplate = 'Purchased ticket for <event title> (#<event ID>)';
		$detailTemplate = 'Ticket purchase occurred at {{#formatDate timestamp}}{{/formatDate}} from the <app name> app';

		//todo looks like it will create it multiple times so add a check that is already exists like the contact properties
		//todo save the id so we can use that to check if the field is created without an id check
		$hubspot  = Factory::createWithToken( $access_token, $client );
		$response = $hubspot->Timeline()->createEventType( $appId, $name, $headerTemplate, $detailTemplate );


		return;
	}

	public function get_event_types() {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return false;
		}

		$client = $hubspot_api->client;

		$appId          = 203977;

		$hubspot  = Factory::createWithToken( $access_token, $client );
		$response = $hubspot->Timeline()->getEventTypes( $appId );


		return;
	}

	public function createOrUpdate( $attendee_id, $post_id, $order, $product_id ) {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return false;
		}

		$client = $hubspot_api->client;
		$appId       = 203977;
		$eventTypeId = 394718;
		$id          = "event-register:{$post_id}:{$attendee_id}";
		$email  = $order->get_billing_email();
		$extraData   = [];

		$hubspot  = Factory::createWithToken( $access_token, $client );
		$response = $hubspot->Timeline()->createOrUpdate( $appId, $eventTypeId, $id, null, $email,  null, $extraData );

		log_me( $response );

		return;
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

		//todo add check if first order already exists and if it does then
		//$groups[]  = $data->get_order_values( 'first_order_', $date, $total, $qty['total'], count( $qty['tickets'] ) );

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

		foreach ( $groups as $group ) {
			$properties = array_merge( $properties, $group );
		}

		// Send to Async Process.
		if ( ! empty( $email ) ) {
			$hubspot_process = new Async();
			$hubspot_process->set_email( $email );
			$hubspot_process->set_properties( $properties );
			$hubspot_process->dispatch();
		}

	}

}