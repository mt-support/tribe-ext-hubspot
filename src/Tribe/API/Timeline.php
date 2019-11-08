<?php

namespace Tribe\HubSpot\API;

use SevenShores\Hubspot\Factory;

/**
 * Class Timeline
 *
 * @package Tribe\HubSpot\API
 */
class Timeline {

	protected $properties = [];

	/**
	 * Setup Hooks for Contact_Property
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'tribe_hubspot_authorize_site', [ $this, 'create_event_type' ], 30 );

	}

	/**
	 * Create Timeline Event Type
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function create_event_type() {
		//todo if the appid changes then this ids should be deleted

		/** @var \Tribe\HubSpot\Admin\Settings $hubspot_options */
		$hubspot_options = tribe( 'tickets.hubspot.admin.settings' );
		$options         = $hubspot_options->get_all_options();

		if ( ! empty( $options['event_registration_id'] ) ) {
			return false;
		}

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return false;
		}

		$client = $hubspot_api->client;
		$appId  = isset( $options['app_id'] ) ? $options['app_id'] : '';

		//todo setup all 3 types in another class and generate each one here
		$name           = 'eventRegistration';
		$headerTemplate = 'Ticket purchase occurred at {{#formatDate timestamp}}{{/formatDate}} from the Event Tickets HubSpot Integration app';
		$detailTemplate = 'Purchased ticket for {{extraData.event.post_title}} (# {{extraData.event.ID}} )';

		try {
			$hubspot  = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->Timeline()->createEventType( $appId, $name, $headerTemplate, $detailTemplate );
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Timeline Event Type' );

			return false;
		}

		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not create timeline event type: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Timeline Event Type' );

			return false;
		}

		$hubspot_options->update_option( 'event_registration_id', $response->data->id );
	}

	/**
	 * Get Timeline Event Type for the APP ID
	 *
	 * @since 1.0
	 *
	 * @return array An array of event types for an APP ID.
	 */
	public function get_event_types() {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return [];
		}

		$hubspot_options = tribe( 'tickets.hubspot' )->get_all_options();
		$client          = $hubspot_api->client;
		$appId           = isset( $hubspot_options['app_id'] ) ? $hubspot_options['app_id'] : '';;

		try {
			$hubspot  = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->Timeline()->getEventTypes( $appId );
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not get timeline event types from HubSpot, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Timeline Event Type' );

			return [];
		}


		if ( $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not get timeline event types: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Timeline Event Type' );

			return [];
		}

		return $response->data;
	}

	/**
	 * Create Timeline Event
	 *
	 * @since 1.0
	 *
	 * @param string $id         The custom id for the event in HubSpot.
	 * @param string $type       The name of the type of event to create ( Registration, Update, Check-In ).
	 * @param string $email      The email address of the account to Update.
	 * @param array  $extra_data An array of event and ticket data to include with the Timeline Event.
	 */
	public function create( $id, $type, $email, $extra_data ) {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api     = tribe( 'tickets.hubspot.api' );
		$hubspot_options = tribe( 'tickets.hubspot' )->get_all_options();

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return;
		}

		$client = $hubspot_api->client;
		$app_id = isset( $hubspot_options['app_id'] ) ? $hubspot_options['app_id'] : '';;
		$event_type_id = isset( $hubspot_options[ $type ] ) ? $hubspot_options[ $type ] : '';;

		try {
			$hubspot  = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->Timeline()->createOrUpdate( $app_id, $event_type_id, $id, null, $email, null, $extra_data );
		} catch ( Exception $e ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Timeline Event Type' );

			return;
		}

		// Additional Safety Check to Verify Status Code.
		if ( $response->getStatusCode() !== 204 && $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Timeline Event Type' );

			return;
		}

	}
}
