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
		add_action( 'tribe_hubspot_authorize_site', [ $this, 'setup_group' ], 20 );
	}

	public function setup_group() {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );
		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return;
		}

		$client = $hubspot_api->client;

		$properties = [
			'name'        => $this->group_name,
			'displayName' => $this->display_name,
		];

		try {
			$hubspot = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->contactProperties()->createGroup( $properties );
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not create a contact property group, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return;
		}

		//todo handle 409 Conflict, field already created

		// Additional Safety Check to Verify Status Code.
		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not create a contact property group, error code: %s', $response->getStatusCode());
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return;
		}

	}
}