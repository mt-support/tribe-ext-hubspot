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

		/** @var \Tribe\HubSpot\Properties\Event_Data $data */
		$data = tribe( 'tickets.hubspot.properties.event_data' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return;
		}

		$client = $hubspot_api->client;
		$email  = $order->get_billing_email();
		$total = $order->get_total();
		$date = $order->get_date_created()->getTimestamp();
		$qty = $data->get_woo_order_quantities( $order );

		//$this->get_order_values( 16110, 16112 );
		//$this->get_ticket_values( 16110, 16112, 'woo', 'Brian Jessee' );

		//todo get all data to send to hubspot in correct format
		//verify sending to hubspot works
		//add filter to either immediately send or to add to queue
		//add coding to use queue
		//setup queue to process

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

		try {
			$hubspot  = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->contacts()->createOrUpdate( $email, $properties );
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact' );

			return;
		}

		// Additional Safety Check to Verify Status Code.
		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact' );

			return;
		}

	}
}