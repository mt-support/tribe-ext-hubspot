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

		//log_me($attendee_id);
		//log_me($post_id);
		//log_me($order);
		//log_me($product_id);

		if ( ! $access_token = tribe( 'tickets.hubspot.api' )->is_ready() ) {
			return;
		}

		$client = tribe( 'tickets.hubspot.api' )->client;
		$attendee = tribe( 'tickets-plus.commerce.woo' )->get_attendee( $attendee_id, $post_id );

		//log_me($attendee);
		$contact =  $attendee['purchaser_email'];


		//todo get
		$properties = [
			[
				'property' => 'firstname',
				'value'    => $attendee['holder_name'],
			],
			[
				'property' => 'lastname',
				'value'    => $attendee['holder_name'],
			],
		];

		// todo add try/exception for invalid email - tribeTest@email
		$hubspot = Factory::createWithToken( $access_token, $client );
		//$response = $hubspot->contacts()->createOrUpdate( $contact, $properties );

		//log_me($response);

	}

	//todo remove test code
	public function sample_connection() {

		if ( ! tribe( 'tickets.hubspot.api' )->is_authorized() ) {
			return;
		}

		$access_token = tribe( 'tickets.hubspot.api' )->maybe_refresh( tribe( 'tickets.hubspot.api' )->access_token );

		tribe( 'tickets.hubspot.api' )->client->key    = $access_token;
		tribe( 'tickets.hubspot.api' )->client->oauth2 = true;

		$hubspot = Factory::createWithToken( tribe( 'tickets.hubspot.api' )->access_token, tribe( 'tickets.hubspot.api' )->client );

		// test Factory
		$response = $hubspot->contacts()->all( [
			'count'    => 10,
			'property' => [ 'firstname', 'lastname' ],
		] );

		foreach ( $response->contacts as $contact ) {
			log_me( sprintf( "Contact name is %s %s." . PHP_EOL, $contact->properties->firstname->value, $contact->properties->lastname->value ) );
		}

		$properties = [
			[
				'property' => 'firstname',
				'value'    => 'HubSpot',
			],
			[
				'property' => 'lastname',
				'value'    => 'test',
			],
		];

		// todo add try/exception for invalid email - tribeTest@email
		// todo make the first subscription
		//$response = $hubspot->contacts()->createOrUpdate( 'tribeTest@tri.be', $properties );

		//log_me($response);

	}
}