<?php

namespace Tribe\HubSpot\Subscribe;

use SevenShores\Hubspot\Factory;

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
		add_action( 'event_ticket_woo_attendee_created', [ $this, 'connect' ], 10, 4 );
	}

	/**
	 * Connect to Creation of an Attendee.
	 *
	 * @since TBD
	 *
	 * @param int    $attendee_id ID of attendee ticket.
	 * @param int    $post_id     ID of event.
	 * @param object $order       WooCommerce order.
	 * @param int    $product_id  WooCommerce product ID.
	 */
	public function connect( $attendee_id, $post_id, $order, $product_id ) {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return;
		}

		$client = $hubspot_api->client;
		$contact = $order->get_billing_email();

		$properties = [
			[
				'property' => 'firstname',
				'value'    => $order->get_billing_first_name(),
			],
			[
				'property' => 'lastname',
				'value'    => $order->get_billing_last_name(),
			],
			[
				'property' => 'newcustomproperty',
				'value'    => 'Custom Property Test',
			],
		];

		try {
			$hubspot = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->contacts()->createOrUpdate( $contact, $properties );
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact' );

			return;
		}

		// Additional Safety Check to Verify Status Code.
		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $response->getStatusCode());
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact' );

			return;
		}

	}
}