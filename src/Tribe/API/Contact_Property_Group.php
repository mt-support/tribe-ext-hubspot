<?php

namespace Tribe\HubSpot\API;

use SevenShores\Hubspot\Factory;
use Tribe\HubSpot\Process\Setup_Queue;

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
	 * @var string
	 */
	public $setup_name = 'group_name_setup';

	/**
	 * Setup Hooks for Contact_Property_Group
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'tribe_hubspot_authorize_site', [ $this, 'queue_group_name' ], 20 );
	}

	/**
	 * Queue the Creation of the Custom Properties
	 *
	 * @since 1.0
	 *
	 * @param mixed $setup_status The current setup try number or status message.
	 */
	public function queue_group_name( $setup_status = 1 ) {

		/** @var \Tribe\HubSpot\API\Setup $setup */
		$setup = tribe( 'tickets.hubspot.setup' );
		// Clear the setup try for custom properties and timeline event types.
		$setup->set_setup_to_pending();

		$hubspot_data = [
			'type' => $this->setup_name,
		];

		$queue = new Setup_Queue();
		$queue->push_to_queue( $hubspot_data );
		$queue->save();
		$queue->dispatch();
	}

	/**
	 * Create HubSpot Group if not Already Created
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function create() {

		/** @var \Tribe\HubSpot\API\Setup $setup */
		$setup = tribe( 'tickets.hubspot.setup' );
		$setup_status = $setup->get_status_value_by_name( $this->setup_name );

		if ( 'failed' === $setup_status ) {
			return false;
		}

		if ( 'complete' === $setup_status ) {
			return true;
		}

		$setup->set_status_value_by_name( $this->setup_name, $setup_status );

		if ( $hubspot_api_group = $this->has_group() ) {
			// The group is setup in HubSpot, set status as complete.
			$setup->set_status_value_by_name( $this->setup_name, 'complete', true );

			return true;
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
			$hubspot  = Factory::createWithOAuth2Token( $access_token, $hubspot_api->client );
			$response = $hubspot->contactProperties()->createGroup( $properties );

		} catch ( \Exception $e ) {
			$message = sprintf( 'Could not create a contact property group, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return false;
		}

		// Additional Safety Check to Verify Status Code.
		if ( 200 !== $response->getStatusCode() ) {
			$message = sprintf( 'Could not create a contact property group, error code: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Contact Property Group' );

			return false;
		}

		// The group is setup in HubSpot, set status as complete.
		$setup->set_status_value_by_name( $this->setup_name, 'complete', true );

		return true;
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
			$hubspot  = Factory::createWithOAuth2Token( $access_token, $hubspot_api->client );
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
