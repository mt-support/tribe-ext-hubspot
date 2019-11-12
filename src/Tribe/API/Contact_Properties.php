<?php

namespace Tribe\HubSpot\API;

use SevenShores\Hubspot\Factory;
use Tribe\HubSpot\Process\Delivery_Queue;

/**
 * Class Contact_Properties
 *
 * @package Tribe\HubSpot\API
 */
class Contact_Properties {

	protected $properties = [];

	/**
	 * Setup Hooks for Contact_Property
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'tribe_hubspot_authorize_site', [ $this, 'queue_properties' ], 30 );

		$this->properties['last_registered_event']  = tribe( 'tickets.hubspot.properties.last_registered_event' );
		$this->properties['last_attended_event']    = tribe( 'tickets.hubspot.properties.last_attended_event' );
		$this->properties['first_order']            = tribe( 'tickets.hubspot.properties.first_order' );
		$this->properties['last_order']             = tribe( 'tickets.hubspot.properties.last_order' );
		$this->properties['last_registered_ticket'] = tribe( 'tickets.hubspot.properties.last_registered_ticket' );
		$this->properties['aggregate_data']         = tribe( 'tickets.hubspot.properties.aggregate_data' );
	}

	/**
	 * Queue the Creation of the Custom Properties
	 *
	 * @since 1.0
	 *
	 */
	public function queue_properties() {

		$hubspot_data = [
			'type' => 'update_properties',
		];

		$queue = new Delivery_Queue();
		$queue->push_to_queue( $hubspot_data );
		$queue->save();
		$queue->dispatch();

	}

	/**
	 * Setup Custom Properties with HubSpot
	 *
	 * @since 1.0
	 *
	 */
	public function setup_properties() {

		/** @var \Tribe\HubSpot\API\Contact_Property_Group $hubspot_api_group */
		$hubspot_api_group = tribe( 'tickets.hubspot.contact.property.group' );

		if ( ! $hubspot_api_group->has_group() ) {
			return;
		}

		// Get all properties created for our group name.
		$created_fields = $this->get_created_properties();

		// Create or Update Properties
		$this->create_all_properties( $created_fields );

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

		try {
			$hubspot  = Factory::createWithToken( $access_token, $hubspot_api->client );
			$response = $hubspot->contactProperties()->all();
		} catch ( \Exception $e ) {
			$message = sprintf( 'Could not access custom properties: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Properties' );

			return [];
		}

		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not access custom properties: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Properties' );

			return [];
		}

		$created_fields = wp_filter_object_list( $response->data, [ 'groupName' => 'event_tickets' ], 'and', 'name' );

		return array_flip( $created_fields );
	}

	/**
	 * Create All Properties by Grouping
	 *
	 * @since 1.0
	 *
	 * @param array $created_fields An array of created properties from HubSpot.
	 */
	public function create_all_properties( $created_fields ) {

		foreach ( $this->properties as $properties ) {

			$this->create_properties_for_grouping( $properties->get_properties(), $created_fields );

		}
	}

	/**
	 * Create or Update Properties for a Grouping
	 *
	 * @since 1.0
	 *
	 * @param array $properties     An array of properties defined by their class.
	 * @param array $created_fields An array of created properties from HubSpot.
	 */
	public function create_properties_for_grouping( $properties, $created_fields ) {

		foreach ( $properties as $name => $property ) {

			if ( isset( $created_fields[ $name ] ) ) {
				$this->create_property( $name, $property, true );

				continue;
			}

			$this->create_property( $name, $property );

		}
	}

	/**
	 * Create or Update Property with HubSpot
	 *
	 * @since 1.0
	 *
	 * @param string $name     The name of the property,used as the ID in HubSpot.
	 * @param array  $property An array of attributes defined for the property.
	 * @param bool   $update   Whether this is an update or created a new property in HubSpot.
	 */
	public function create_property( $name, $property, $update = false ) {

		if ( empty( $name ) ) {
			return;
		}

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return;
		}

		try {
			$hubspot = Factory::createWithToken( $access_token, $hubspot_api->client );

			if ( $update ) {
				$response = $hubspot->contactProperties()->update( $name, $property );
			} else {
				$property['name'] = $name;
				$response         = $hubspot->contactProperties()->create( $property );
			}

		} catch ( \Exception $e ) {
			$message = sprintf( 'Could not create custom contact property ' . esc_html( $name ) . ', error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property' );

			return;
		}

		// Additional Safety Check to Verify Status Code.
		if ( 200 !== $response->getStatusCode() ) {
			$message = sprintf( 'Could not create custom contact property ' . esc_html( $name ) . ', error code: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property' );

			return;
		}

	}

	/**
	 * Update Contact
	 *
	 * @since 1.0
	 *
	 * @param string $email      An email used to update a contact in HubSpot.
	 * @param array  $properties An array of fields and custom fields to update for a contact.
	 *
	 * @return bool
	 */
	public function update( $email, $properties ) {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return false;
		}

		$client = $hubspot_api->client;

		//todo aggregate data will be calculated right here

		try {
			$hubspot  = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->contacts()->createOrUpdate( $email, $properties );
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact' );

			return false;
		}

		// Additional Safety Check to Verify Status Code.
		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact' );

			return false;
		}


		return true;

	}
}
