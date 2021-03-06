<?php

namespace Tribe\HubSpot\Process;

use Tribe__Process__Queue;

class Delivery_Queue extends Tribe__Process__Queue {

	/**
	 * @var string The type of action to take to update HubSpot
	 */
	protected $type;

	/**
	 * @var array The array of data to send to HubSpot
	 */
	protected $data;

	/**
	 * Returns the queue process action name.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function action() {
		return 'hubspot_delivery_queue';
	}

	/**
	 * Task to send the Data to HubSpot based on the Type Sent
	 *
	 * @since 1.0
	 *
	 * @param mixed $hubspot_data An array of data to send to HubSpot
	 *
	 * @return bool
	 */
	protected function task( $hubspot_data ) {

		if ( empty( $hubspot_data['type'] ) ) {
			return false;
		}

		$this->type = $hubspot_data['type'];
		$this->data = $hubspot_data;

		$response = false;
		if ( 'contact' === $hubspot_data['type'] ) {
			$response = $this->contact_update( $hubspot_data );
		} elseif ( 'timeline' === $hubspot_data['type'] ) {
			$response = $this->timeline_create( $hubspot_data );
		}

		return $response;
	}

	/**
	 * Update Contact with HubSpot
	 *
	 * @since 1.0
	 *
	 * @param array $hubspot_data An array of data to send to HubSpot
	 *
	 * @return bool
	 */
	protected function contact_update( $hubspot_data ) {

		/** @var \Tribe\HubSpot\API\Contact_Property $hubspot_api */
		$hubspot_contact = tribe( 'tickets.hubspot.contact.properties' );

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Subscriptions';

		$logger->log_debug( "(ID: {$this->identifier}) - handling request.", $log_src );

		if ( ! isset( $hubspot_data['email'], $hubspot_data['properties'] ) ) {
			do_action( 'tribe_log', 'error', $this->identifier, [ 'data' => $hubspot_data, ] );

			return false;
		}

		/** @var \Tribe\HubSpot\API\Setup $hubspot_api */
		$setup = tribe( 'tickets.hubspot.setup' );

		// Check if Contact is Setup before Continuing.
		if ( ! $setup->is_contact_setup() ) {
			return false;
		}

		$email      = filter_var( $hubspot_data['email'], FILTER_SANITIZE_EMAIL );
		$properties = \Tribe__Utils__Array::escape_multidimensional_array( $hubspot_data['properties'] );
		$order_data = \Tribe__Utils__Array::escape_multidimensional_array( $hubspot_data['order_data'] );

		$logger->log_debug( "(ID: {$this->identifier}) - updating contact {$email}", $log_src );

		// Connect to HubSpot and Update.
		$hubspot_response = $hubspot_contact->update( $email, $properties, $order_data );

		if ( false === $hubspot_response ) {
			do_action(
				'tribe_log',
				'error',
				$this->identifier,
				[
					'action'     => 'update-contact',
					'email'      => $email,
					'properties' => $properties,
				]
			);
			$logger->log_debug( "(ID: {$this->identifier}) - could not update contact {$email}", $log_src );

			return false;
		}


		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'update-contact',
				'email'      => $email,
				'properties' => $properties,
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - updated {$email}.", $log_src );

		return $hubspot_response;
	}

	/**
	 * Create Timeline Event with HubSpot
	 *
	 * @since 1.0
	 *
	 * @param array $hubspot_data An array of data to send to HubSpot
	 *
	 * @return bool
	 */
	protected function timeline_create( $hubspot_data ) {

		/** @var \Tribe\HubSpot\API\Timeline $hubspot_api */
		$hubspot_timeline = tribe( 'tickets.hubspot.timeline' );

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Timeline';

		$logger->log_debug( "(ID: {$this->identifier}) - handling request.", $log_src );

		if ( ! isset( $hubspot_data['email'], $hubspot_data['timeline_event_id'], $hubspot_data['event_type'] ) ) {
			do_action( 'tribe_log', 'error', $this->identifier, [ 'data' => $hubspot_data, ] );

			return false;
		}

		/** @var \Tribe\HubSpot\API\Setup $hubspot_api */
		$setup = tribe( 'tickets.hubspot.setup' );

		// Check if Contact is Setup before Continuing.
		if ( ! $setup->is_timeline_setup() ) {
			return false;
		}

		$email             = filter_var( $hubspot_data['email'], FILTER_SANITIZE_EMAIL );
		$timeline_event_id = filter_var( $hubspot_data['timeline_event_id'], FILTER_SANITIZE_STRING );
		$event_type        = filter_var( $hubspot_data['event_type'], FILTER_SANITIZE_STRING );
		$extra_data        = \Tribe__Utils__Array::escape_multidimensional_array( $hubspot_data['extra_data'] );

		$logger->log_debug( "(ID: {$this->identifier}) - updating timeline {$email}", $log_src );

		// Connect to HubSpot and Update.
		$hubspot_response = $hubspot_timeline->create( $timeline_event_id, $event_type, $email, $extra_data );

		if ( false === $hubspot_response ) {
			do_action(
				'tribe_log',
				'error',
				$this->identifier,
				[
					'action'     => 'create-timeline',
					'email'      => $email,
				]
			);
			$logger->log_debug( "(ID: {$this->identifier}) - could not update timeline {$email}", $log_src );

			return false;
		}


		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'create-timeline',
				'email'      => $email,
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - updated timeline for {$email}.", $log_src );

		return $hubspot_response;

	}

	/**
	 * HubSpot Connection Complete
	 *
	 * @since 1.0
	 *
	 */
	protected function complete() {

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Updates';

		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'hubspot',
				'type' => $this->type,
				'data' => $this->data,
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - updated {$this->type}.", $log_src );

		parent::complete();
	}
}