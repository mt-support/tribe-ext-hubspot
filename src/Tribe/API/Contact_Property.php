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
		//add_action( 'admin_init', [ $this, 'setup_properties' ], 30 );
		add_action( 'tribe_hubspot_authorize_site', [ $this, 'setup_properties' ], 30 );

		$this->properties[ 'last_registered_event' ] = tribe( 'tickets.hubspot.properties.last_registered_event' );
		$this->properties[ 'last_attended_event' ] = tribe( 'tickets.hubspot.properties.last_attended_event' );
	}

	public function setup_properties() {
		log_me('setup_properties');

		//todo check here for group added

		// Get all properties created for our group name.
		$created_fields = $this->get_created_properties();

		// Create or Update Properties
		$this->create_all_properties( $created_fields );

		return;

	}

	/**
	 * Get Names of All Created Properties in HubSpot with Event Tickets Group Name
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_created_properties() {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return [];
		}

		$client = $hubspot_api->client;

		try {
			$hubspot = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->contactProperties()->all();
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not access custom properties: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Properties' );

			return [];
		}

		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not access custom properties: %s', $response->getStatusCode());
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Properties' );

			return [];
		}

		$created_fields = wp_filter_object_list(
			$response->data,
			[ 'groupName' => 'event_tickets' ],
			'and',
			'name'
		 );

		return array_flip( $created_fields );

	}

	public function create_all_properties( $created_fields ) {

		//todo get rid of double foreach?
		foreach( $this->properties as $properties ) {

			foreach( $properties->get_properties() as $name => $property ) {

				log_me( $name );
				if ( isset( $created_fields[$name] ) ) {
					log_me('created');
					//todo add call to update
					$this->create_property( $property, true );

					continue;
				} else {
					log_me('not');
				}

				// add name from key for HubSpot
				//todo maybe because the name is required as a seperate field for update I leave it out and end it in the create_property method
				$property[ 'name' ] = $name;
				$this->create_property( $property );

			}
		}

	}


	public function create_property( $property, $update = false ) {

		if ( empty( $property['name'] ) ) {
			return;
		}

		return;
		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return;
		}

		$client = $hubspot_api->client;

/*		$properties = [
			'name'        => "newcustomproperty",
			'label' => "A New Custom Property",
			'description' => "A new property for you", //optional
			'groupName' => 'event_tickets',
			'type' => "string",
			'fieldType' => "text",
			'formField' => false, //optional
			'displayOrder' => 6, //optional
			'options' => [] //optional
		];*/

		try {
			$hubspot = Factory::createWithToken( $access_token, $client );

			if ( $update ) {
				$response = $hubspot->contactProperties()->update( $property['name'], $property );
			} else {
				$response = $hubspot->contactProperties()->create( $property );
			}
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not create a contact property, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property' );

			return;
		}

		// Additional Safety Check to Verify Status Code.
		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not create a contact property, error code: %s', $response->getStatusCode());
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property' );

			return;
		}

	}
}