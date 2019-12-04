<?php

namespace Tribe\HubSpot\Process;

use Tribe__Process__Queue;

class Setup_Queue extends Tribe__Process__Queue {

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
		return 'hubspot_setup_queue';
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
		if ( 'group_name_setup' === $hubspot_data['type'] ) {
			$response = $this->group_name_update( $hubspot_data );
		} elseif ( 'custom_properties_setup' === $hubspot_data['type'] ) {
			$response = $this->properties_update( $hubspot_data );
		}   elseif ( 'timeline_event_types_setup' === $hubspot_data['type'] ) {
			$response = $this->timeline_event_types_update( $hubspot_data );
		}

		return $response;
	}

	/**
	 * Update Group Name for Custom Properties with HubSpot
	 *
	 * @since 1.0
	 *
	 * @param array $hubspot_data An array of data to send to HubSpot
	 *
	 * @return bool
	 */
	protected function group_name_update( $hubspot_data ) {

		/** @var \Tribe\HubSpot\API\Contact_Property_Group $hubspot_api */
		$hubspot_contact = tribe( 'tickets.hubspot.contact.property.group' );

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Group Name';

		$logger->log_debug( "(ID: {$this->identifier}) - start setup group name with HubSpot", $log_src );

		// Connect to HubSpot and Update.
		$hubspot_response = $hubspot_contact->create();

		if ( false === $hubspot_response ) {
			do_action(
				'tribe_log',
				'error',
				$this->identifier,
				[
					'action'     => 'setup-group-name',
				]
			);
			$logger->log_debug( "(ID: {$this->identifier}) - could not setup group name with HubSpot", $log_src );

			return false;
		}

		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'setup-group-name',
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - complete setup group name with HubSpot.", $log_src );

		return $hubspot_response;
	}

	/**
	 * Update Custom Properties with HubSpot
	 *
	 * @since 1.0
	 *
	 * @param array $hubspot_data An array of data to send to HubSpot
	 *
	 * @return bool
	 */
	protected function properties_update( $hubspot_data ) {

		/** @var \Tribe\HubSpot\API\Contact_Properties $hubspot_api */
		$hubspot_contact = tribe( 'tickets.hubspot.contact.properties' );

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Custom Properties';

		$logger->log_debug( "(ID: {$this->identifier}) - start setup custom properties with HubSpot", $log_src );

		// Connect to HubSpot and Update.
		$hubspot_response = $hubspot_contact->setup_properties();

		if ( false === $hubspot_response ) {
			do_action(
				'tribe_log',
				'error',
				$this->identifier,
				[
					'action'     => 'setup-custom-properties',
				]
			);
			$logger->log_debug( "(ID: {$this->identifier}) - could not setup custom properties with HubSpot", $log_src );

			return false;
		}

		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'setup-custom-properties',
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - complete setup custom properties with HubSpot.", $log_src );

		return $hubspot_response;
	}

	/**
	 * Update Timeline Event Types with HubSpot
	 *
	 * @since 1.0
	 *
	 * @param array $hubspot_data An array of data to send to HubSpot
	 *
	 * @return bool
	 */
	protected function timeline_event_types_update( $hubspot_data ) {

		/** @var \Tribe\HubSpot\API\Timeline $hubspot_api */
		$hubspot_timeline = tribe( 'tickets.hubspot.timeline' );

		/** @var \Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Timeline';

		$logger->log_debug( "(ID: {$this->identifier}) - start setup timeline event types with HubSpot", $log_src );

		// Connect to HubSpot and Update.
		$hubspot_response = $hubspot_timeline->create_event_types();

		if ( false === $hubspot_response ) {
			do_action(
				'tribe_log',
				'error',
				$this->identifier,
				[
					'action'     => 'timeline-event-types-setup',
				]
			);
			$logger->log_debug( "(ID: {$this->identifier}) - could not update timeline event types with HubSpot", $log_src );

			return false;
		}

		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'update-timeline-event-types',
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - complete setup timeline event types with HubSpot.", $log_src );

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
		$log_src = 'HubSpot Setup';

		do_action(
			'tribe_log',
			'debug',
			$this->identifier,
			[
				'action'     => 'hubspot-setup',
				'type' => $this->type,
				'data' => $this->data,
			]
		);

		$logger->log_debug( "(ID: {$this->identifier}) - setup {$this->type}.", $log_src );

		parent::complete();
	}
}