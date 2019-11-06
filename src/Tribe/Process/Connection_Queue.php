<?php

namespace Tribe\HubSpot\Process;

use Tribe__Process__Queue;

class Connection_Queue extends Tribe__Process__Queue {

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
		return 'hubspot_connection_queue';
	}


	protected function task( $hubspot_data ) {

		if ( empty( $hubspot_data['type'] ) ) {
			return false;
		}

		$this->type = $hubspot_data['type'];
		$this->data = $hubspot_data;

		$response = '';

		if ( 'contact' === $hubspot_data['type'] ) {
			$response = $this->contact_update( $hubspot_data );
		} elseif ( 'timeline' === $hubspot_data['type'] ) {
			$response = $this->timeline_update( $hubspot_data );
		} elseif ( 'update_properties' === $hubspot_data['type'] ) {
			$response = $this->properties_update( $hubspot_data );
		}



		return $response;
	}

	protected function contact_update( $hubspot_data ) {

		/** @var \Tribe\HubSpot\API\Contact_Property $hubspot_api */
		$hubspot_contact = tribe( 'tickets.hubspot.contact.property' );

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Subscriptions';

		$logger->log_debug( "(ID: {$this->identifier}) - handling request.", $log_src );

		if ( ! isset( $hubspot_data['email'], $hubspot_data['properties'] ) ) {
			do_action( 'tribe_log', 'error', $this->identifier, [ 'data' => $hubspot_data, ] );

			return 0;
		}

		$email      = filter_var( $hubspot_data['email'], FILTER_SANITIZE_EMAIL );
		$properties = \Tribe__Utils__Array::escape_multidimensional_array( $hubspot_data['properties'] );

		$logger->log_debug( "(ID: {$this->identifier}) - updating contact {$email}", $log_src );

		// Connect to HubSpot and Update.
		$hubspot_response = $hubspot_contact->update( $email, $properties );

		if ( false === $hubspot_response ) {
			do_action(
				'tribe_log',
				'error',
				$this->identifier,
				[
					'action'     => 'fetch',
					'email'      => $email,
					'properties' => $properties,
				]
			);
			$logger->log_debug( "(ID: {$this->identifier}) - could not update contact {$email}", $log_src );

			return 0;
		}


		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'set',
				'email'      => $email,
				'properties' => $properties,
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - updated {$email}.", $log_src );

		return $hubspot_response;

	}

	protected function timeline_update( $hubspot_data ) {

		/** @var \Tribe\HubSpot\API\Timeline $hubspot_api */
		$hubspot_timeline = tribe( 'tickets.hubspot.timeline' );

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Timeline';

		$logger->log_debug( "(ID: {$this->identifier}) - handling request.", $log_src );

		if ( ! isset( $hubspot_data['email'], $hubspot_data['properties'] ) ) {
			do_action( 'tribe_log', 'error', $this->identifier, [ 'data' => $hubspot_data, ] );

			return 0;
		}

		$email      = filter_var( $hubspot_data['email'], FILTER_SANITIZE_EMAIL );
		$id         = filter_var( $hubspot_data['event_id'], FILTER_SANITIZE_STRING );
		$type       = filter_var( $hubspot_data['event_type'], FILTER_SANITIZE_STRING );
		$extra_data = \Tribe__Utils__Array::escape_multidimensional_array( $hubspot_data['properties'] );

		$logger->log_debug( "(ID: {$this->identifier}) - updating timeline {$email}", $log_src );

		// Connect to HubSpot and Update.
		$hubspot_response = $hubspot_timeline->create( $id, $type, $email, $extra_data );

		if ( false === $hubspot_response ) {
			do_action(
				'tribe_log',
				'error',
				$this->identifier,
				[
					'action'     => 'fetch',
					'email'      => $email,
				]
			);
			$logger->log_debug( "(ID: {$this->identifier}) - could not update timeline {$email}", $log_src );

			return 0;
		}


		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'set',
				'email'      => $email,
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - updated timeline for {$email}.", $log_src );

		return $hubspot_response;

	}

	protected function properties_update( $hubspot_data ) {

		/** @var \Tribe\HubSpot\API\Properties $hubspot_api */
		$hubspot_contact = tribe( 'tickets.hubspot.contact.property' );

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Custom Properties';

		$logger->log_debug( "(ID: {$this->identifier}) - updating custom properties with HubSpot", $log_src );

		// Connect to HubSpot and Update.
		$hubspot_response = $hubspot_contact->setup_properties();

		if ( false === $hubspot_response ) {
			do_action(
				'tribe_log',
				'error',
				$this->identifier,
				[
					'action'     => 'update',
				]
			);
			$logger->log_debug( "(ID: {$this->identifier}) - could not update custom properties", $log_src );

			return 0;
		}


		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'set',
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - updated custom properties with HubSpot.", $log_src );

		return $hubspot_response;

	}

	protected function complete() {

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Updates';

		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'set',
				'type' => $this->type,
				'data' => $this->data,
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - updated {$this->type}.", $log_src );


		parent::complete();
	}
}