<?php

namespace Tribe\HubSpot\API;

use SevenShores\Hubspot\Factory;

/**
 * Class Contact_Property_Group
 *
 * @package Tribe\HubSpot\API
 */
class Contact_Property_Group {

	/**
	 * @var string
	 */
	protected $display_name = 'Event Tickets';

	/**
	 * @var string
	 */
	public $group_name = 'event_tickets';

	/**
	 * Setup Hooks for Contact_Property_Group
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'tribe_hubspot_authorize_site', [ $this, 'create' ], 20 );
	}

	/**
	 * Create HubSpot Group if not Already Created
	 *
	 * @since 1.0
	 *
	 */
	public function create() {

		if ( $hubspot_api_group = $this->has_group() ) {
			return;
		}

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );
		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return false;
		}

		$properties = [
			'name'        => $this->group_name,
			'displayName' => $this->display_name,
		];

		try {
			$hubspot  = Factory::createWithToken( $access_token, $hubspot_api->client );
			$response = $hubspot->contactProperties()->createGroup( $properties );

		} catch ( \Exception $e ) {
			$message = sprintf( 'Could not create a contact property group, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return;
		}

		// Additional Safety Check to Verify Status Code.
		if ( 200 !== $response->getStatusCode() ) {
			$message = sprintf( 'Could not create a contact property group, error code: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return;
		}

	}

	/**
	 * Check if HubSpot Group Created
	 *
	 * @since 1.0
	 *
	 * @return bool true|false if group is created
	 */
	public function has_group() {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );
		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return false;
		}

		try {
			$hubspot  = Factory::createWithToken( $access_token, $hubspot_api->client );
			$response = $hubspot->contactProperties()->getGroups( true );

		} catch ( \Exception $e ) {
			$message = sprintf( 'Could not determine if the Event Tickets Contact Property Group is Created in HubSpot, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return false;
		}

		// Additional Safety Check to Verify Status Code.
		if ( 200 !== $response->getStatusCode() ) {
			$message = sprintf( 'Could not determine if the Event Tickets Contact Property Group is Created in HubSpot, error code: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return false;
		}

		$event_group = wp_filter_object_list( $response->data, [ 'name' => $this->group_name ], 'and', 'name' );

		return ! empty( $event_group );
	}
}
