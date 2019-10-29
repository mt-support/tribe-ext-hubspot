<?php

namespace Tribe\HubSpot\Process;

use Tribe__Process__Handler;

class Async extends Tribe__Process__Handler {

	/**
	 * @var string The email of the contact to update in HubSpot
	 */
	protected $email;

	/**
	 * @var array The array of properties to update in HubSpot
	 */
	protected $properties;

	/**
	 * {@inheritdoc}
	 */
	public static function action() {
		return 'hubspot_subscriptions';
	}

	/**
	 * {@inheritdoc}
	 */
	public function dispatch() {

		if ( ! isset( $this->email ) ) {
			// since this is a developer error we are not localizing this error string
			throw new InvalidArgumentException( 'The contact email should be set before trying to dispatch.' );
		}

		if ( ! isset( $this->properties ) ) {
			// since this is a developer error we are not localizing this error string
			throw new InvalidArgumentException( 'The properties should be set before trying to dispatch.' );
		}

		$data = [
			'email'        => $this->email,
			'properties'        => $this->properties,
		];

		$this->data( $data );

		do_action( 'tribe_log', 'debug', __CLASS__, $data );

		return parent::dispatch();
	}

	/**
	 *
	 *
	 * @since 1.0
	 *
	 * @param string $email
	 */
	public function set_email( $email ) {
		$this->email = $email;
	}

	/**
	 *
	 *
	 * @since 1.0
	 *
	 * @param array $properties
	 */
	public function set_properties( $properties ) {
		$this->properties = $properties;
	}

	/**
	 *
	 *
	 * @since 1.0
	 *
	 * @see   ::sync_handle()
	 *
	 * @param array|null $data_source An optional source of data.
	 */
	protected function handle( array $data_source = null ) {
		$this->sync_handle( $data_source );
	}

	/**
	 * {@inheritdoc}
	 */
	public function sync_handle( array $data_source = null ) {

		/** @var \Tribe\HubSpot\API\Contact_Property $hubspot_api */
		$hubspot_contact = tribe( 'tickets.hubspot.contact.property' );

			/** @var Tribe__Log $logger */
		$logger  = tribe( 'logger' );
		$log_src = 'HubSpot Subscriptions';

		$logger->log_debug( "(ID: {$this->identifier}) - handling request.", $log_src );

		$data_source = isset( $data_source ) ? $data_source : $_POST;

		if ( ! isset( $data_source['email'], $data_source['properties'] ) ) {
			do_action( 'tribe_log', 'error', $this->identifier, [ 'data' => $data_source, ] );

			return 0;
		}

		$email             = filter_var( $data_source['email'], FILTER_SANITIZE_EMAIL );
		$properties             = $data_source['properties'];

		$logger->log_debug( "(ID: {$this->identifier}) - updating contact {$email}", $log_src );

		//todo this is where to connect
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
}
