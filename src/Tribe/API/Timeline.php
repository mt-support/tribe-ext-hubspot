<?php

namespace Tribe\HubSpot\API;

use SevenShores\Hubspot\Factory;
use Tribe\HubSpot\Process\Delivery_Queue;

/**
 * Class Timeline
 *
 * @package Tribe\HubSpot\API
 */
class Timeline {

	/**
	 * @var array
	 */
	protected $timeline_event_types = [
		'eventRegistration'     => [
			'site_option'    => 'timeline_event_registration_id',
			'headerTemplate' => 'Ticket purchase occurred at {{#formatDate timestamp}}{{/formatDate}} from the Event Tickets HubSpot Integration app',
			'detailTemplate' => 'Purchased ticket for {{extraData.event.post_title}} (# {{extraData.event.ID}} )',
		],
		'eventRegistrationRSVP' => [
			'site_option'    => 'timeline_event_registration_rsvp_id',
			'headerTemplate' => 'RSVP occurred at {{#formatDate timestamp}}{{/formatDate}} from the Event Tickets HubSpot Integration app',
			'detailTemplate' => 'RSVP\'d for {{extraData.event.post_title}} (# {{extraData.event.ID}} )',
		],
		'eventAttendeeUpdate'   => [
			'site_option'    => 'timeline_event_attendee_update_id',
			'headerTemplate' => 'Attendee Information update occurred at {{#formatDate timestamp}}{{/formatDate}} from the Event Tickets HubSpot Integration app',
			'detailTemplate' => 'Updated Attendee Information for {{extraData.event.post_title}} (# {{extraData.event.ID}} )',
		],
		'eventCheckin'          => [
			'site_option'    => 'timeline_event_checkin_id',
			'headerTemplate' => 'Contact was successfully checked in at {{#formatDate timestamp}}{{/formatDate}}',
			'detailTemplate' => 'Attendee was checked into {{extraData.event.post_title}} (# {{extraData.event.ID}} )',
		],
	];

	/**
	 * Setup Hooks for Contact_Property
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		add_action( 'tribe_hubspot_authorize_site', [ $this, 'queue_timeline_event_types' ], 30 );
	}

	/**
	 * Queue the Creation of Timeline Event Types
	 *
	 * @since 1.0
	 *
	 */
	public function queue_timeline_event_types() {

		$hubspot_data = [
			'type' => 'update_timeline_event_types',
		];

		$queue = new Delivery_Queue();
		$queue->push_to_queue( $hubspot_data );
		$queue->save();
		$queue->dispatch();

	}

	/**
	 * Create all Timeline Event Types.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function create_event_types() {

		/** @var \Tribe\HubSpot\Admin\Settings $hubspot_options */
		$hubspot_options = tribe( 'tickets.hubspot.admin.settings' );
		$options         = $hubspot_options->get_all_options();

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api = tribe( 'tickets.hubspot.api' );

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return false;
		}

		$client          = $hubspot_api->client;
		$app_id          = isset( $options['app_id'] ) ? (int) $options['app_id'] : '';
		$timeline_events = $this->get_event_types();

		foreach ( $this->timeline_event_types as $name => $event_type ) {

			$matching_event_types = wp_filter_object_list( $timeline_events, [ 'name' => $name ], 'and' );
			$matching_event_type  = reset( $matching_event_types );

			// If the match is empty then there is no event type and create it.
			if ( empty( $matching_event_type->applicationId ) ) {
				$this->create_event_type( $hubspot_options, $access_token, $client, $app_id, $name, $event_type );
				continue;
			}

			// If the match and the app id does not match then create it.
			if ( $app_id !== (int) $matching_event_type->applicationId ) {
				$this->create_event_type( $hubspot_options, $access_token, $client, $app_id, $name, $event_type );
				continue;
			}

			// If there is an id then update the site option with it
			if ( ! empty( $matching_event_type->id ) ) {
				$hubspot_options->update_option( $event_type['site_option'], $matching_event_type->id );
				continue;
			}

		}
	}

	/**
	 * Create Timeline Event Type
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function create_event_type( $hubspot_options, $access_token, $client, $app_id, $name, $event_type ) {

		try {
			$hubspot  = Factory::createWithToken( $access_token, $client );
			$response = $hubspot->Timeline()->createEventType( $app_id, $name, $event_type['headerTemplate'], $event_type['detailTemplate'] );
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

		$hubspot_options->update_option( $event_type['site_option'], $response->data->id );

		return true;
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
	 *
	 * @return bool
	 */
	public function create( $id, $type, $email, $extra_data ) {

		/** @var \Tribe\HubSpot\API\Connection $hubspot_api */
		$hubspot_api     = tribe( 'tickets.hubspot.api' );
		$hubspot_options = tribe( 'tickets.hubspot' )->get_all_options();

		if ( ! $access_token = $hubspot_api->is_ready() ) {
			return false;
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

			return false;
		}

		// Additional Safety Check to Verify Status Code.
		if ( $response->getStatusCode() !== 204 && $response->getStatusCode() !== 200 ) {
			$message = sprintf( 'Could not update or create a contact with HubSpot, error code: %s', $response->getStatusCode() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Timeline Event Type' );

			return false;
		}

		return true;
	}
}
