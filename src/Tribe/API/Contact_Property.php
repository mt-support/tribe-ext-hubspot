<?php

namespace Tribe\HubSpot\API;

use SevenShores\Hubspot\Factory;

/**
 * Class Contact_Property
 *
 * @package Tribe\HubSpot\API
 */
class Contact_Property {

	protected $properties = [];

	/**
	 * Setup Hooks for Contact_Property
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {
		//add_action( 'admin_init', [ $this, 'create' ], 10, 0 );

		$this->last_registered_event = tribe( 'tickets.hubspot.properties.last_registered_event' );
		$this->last_attended_event = tribe( 'tickets.hubspot.properties.last_attended_event' );
	}

	public function create() {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return;
		}

		$client = $hubspot_api->client;

		$properties = [
			'name'        => "newcustomproperty",
			'label' => "A New Custom Property",
			'description' => "A new property for you", //optional
			'groupName' => 'event_tickets',
			'type' => "string",
			'fieldType' => "text",
			'formField' => false, //optional
			'displayOrder' => 6, //optional
			'options' => [] //optional
		];

		try {
			$hubspot = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->contactProperties()->create( $properties );
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not create a contact property group, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return;
		}

		//log_me($response);

		//todo handle 409 Conflict, field already created

		// Additional Safety Check to Verify Status Code.
		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not create a contact property group, error code: %s', $response->getStatusCode());
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return;
		}

	}
}