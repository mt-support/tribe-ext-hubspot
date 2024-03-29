<?php

namespace Tribe\HubSpot\API;

use SevenShores\Hubspot\Http\Client;
use SevenShores\Hubspot\Resources\OAuth2;

/**
 * Class Connection
 *
 * @package Tribe\HubSpot\API
 */
class Connection {

	/**
	 * @var string
	 */
	protected $callback = '';

	/**
	 * @var array
	 */
	protected $scope = [ 'crm.schemas.contacts.read', 'crm.schemas.contacts.write', 'timeline' ];

	/**
	 * @var int
	 */
	protected $app_id = '';

	/**
	 * @var string
	 */
	protected $client_id = '';

	/**
	 * @var string
	 */
	protected $client_secret = '';

	/**
	 * @var int
	 */
	protected $user_id = '';

	/**
	 * @var string
	 */
	protected $hapi_key = '';

	/**
	 * @var string
	 */
	protected $access_token = '';

	/**
	 * @var string
	 */
	protected $refresh_token = '';

	/**
	 * @var string
	 */
	protected $token_expires = '';

	/**
	 * Connection constructor.
	 *
	 * @since 1.0
	 * @since 1.0.3 - Modify the callback url to remove the nonce query string.
	 */
	public function __construct() {
		$this->callback                   = get_home_url( null, '/tribe-hubspot/' );
		$this->options                    = tribe( 'tickets.hubspot' )->get_all_options();
		$this->opts_prefix                = tribe( 'tickets.hubspot.admin.settings' )->get_options_prefix();
		$this->app_id                     = isset( $this->options['app_id'] ) ? trim( $this->options['app_id'] ) : '';
		$this->client_id                  = isset( $this->options['client_id'] ) ? trim( $this->options['client_id'] ) : '';
		$this->client_secret              = isset( $this->options['client_secret'] ) ? trim( $this->options['client_secret'] ) : '';
		$this->user_id                    = isset( $this->options['user_id'] ) ? trim( $this->options['user_id'] ) : '';
		$this->hapi_key                   = isset( $this->options['hapi_key'] ) ? trim( $this->options['hapi_key'] ) : '';
		$this->access_token               = isset( $this->options['access_token'] ) ? $this->options['access_token'] : '';
		$this->refresh_token              = isset( $this->options['refresh_token'] ) ? $this->options['refresh_token'] : '';
		$this->token_expires              = isset( $this->options['token_expires'] ) ? $this->options['token_expires'] : '';
		$this->group_name_setup           = isset( $this->options['group_name_setup'] ) ? $this->options['group_name_setup'] : '';
		$this->custom_properties_setup    = isset( $this->options['custom_properties_setup'] ) ? $this->options['custom_properties_setup'] : '';
		$this->timeline_event_types_setup = isset( $this->options['timeline_event_types_setup'] ) ? $this->options['timeline_event_types_setup'] : '';

		if ( ! $this->has_required_fields() ) {
			return;
		}

		$this->client = new Client( [ 'key' => $this->client_secret ] );
		$this->oauth2 = new OAuth2( $this->client );

	}

	/**
	 * Get the User ID.
	 *
	 * @since 1.0
	 *
	 * @return int The User ID.
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * Get the Application ID.
	 *
	 * @since 1.0
	 *
	 * @return int The application ID.
	 */
	public function get_app_id() {
		return $this->app_id;
	}

	/**
	 * Get the HAPI Key.
	 *
	 * @since 1.0
	 *
	 * @return string The HAPI key.
	 */
	public function get_hapi_key() {
		return $this->hapi_key;
	}

	/**
	 * Check if the Required Fields Are Available to Authorize
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function has_required_fields() {

		if (
			empty( $this->client_id ) ||
			empty( $this->client_secret ) ||
			empty( $this->user_id ) ||
			empty( $this->hapi_key )
		) {

			return false;
		}

		return true;

	}

	/**
	 * Check if HubSpot is Authorized
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function is_authorized() {

		// If missing any of these fields then the site is Not Authorized.
		if (
			empty( $this->client_id ) ||
			empty( $this->client_secret ) ||
			empty( $this->user_id ) ||
			empty( $this->hapi_key ) ||
			empty( $this->access_token ) ||
			empty( $this->refresh_token ) ||
			empty( $this->token_expires )
		) {

			return false;
		}

		return true;

	}

	/**
	 * Get the HubSpot Authorization URL
	 *
	 * @since 1.0
	 *
	 * @return \SevenShores\Hubspot\Http\Response|void
	 */
	public function get_authorized_url() {

		// If No Client ID then Return Empty to Prevent Errors.
		if ( empty( $this->client_id ) ) {
			return;
		}

		return $this->oauth2->getAuthUrl( $this->client_id, $this->callback, $this->scope );
	}

	/**
	 * Update the Access Token Options
	 *
	 * @since 1.0
	 *
	 * @param object $access_tokens A data object with access token, refresh token, and expiration duration
	 */
	protected function update_tokens( $access_tokens ) {

		tribe_update_option( $this->opts_prefix . 'access_token', sanitize_text_field( $access_tokens->data->access_token ) );
		tribe_update_option( $this->opts_prefix . 'refresh_token', sanitize_text_field( $access_tokens->data->refresh_token ) );
		tribe_update_option( $this->opts_prefix . 'token_expires', sanitize_text_field( current_time( 'timestamp' ) + $access_tokens->data->expires_in ) );

	}

	/**
	 * Save The HubSpot Code Token Sent during the Authorization Process
	 *
	 * @since 1.0
	 *
	 */
	public function save_access_token() {

		// Sanitize GET Before Use.
		$safe_get   = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
		$token_code = $safe_get['code'];

		// Get Access Tokens from HubSpot.
		try {
			$access_tokens = $this->oauth2->getTokensByCode( $this->client_id, $this->client_secret, $this->callback, $token_code );

		} catch ( \Exception $e ) {
			$message = sprintf( 'Could not complete authorization with HubSpot, error message %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Authorization Tokens' );

			return;
		}

		// Additional Safety Check to Verify Status Code.
		if ( $access_tokens->getStatusCode() !== 200 ) {

			$message = sprintf( 'Could not complete authorization with HubSpot, error code %s', $access_tokens->getStatusCode());
			tribe( 'logger' )->log_error( $message, 'HubSpot Authorization Tokens' );

			return;
		}

		$this ->update_tokens( $access_tokens );
	}

	/**
	 * Maybe Refresh the Token if Expired or within a Minute of Expiring
	 *
	 * @since 1.0
	 *
	 */
	protected function maybe_refresh( $access_token ) {

		// If there is no refresh token then there is nothing to do.
		if ( empty( $this->refresh_token ) ) {
			return $access_token;
		}

		// Refresh Any Token that is Expired or within a Minute of Expiring.
		if ( current_time( 'timestamp' ) + 60 <= $this->token_expires ) {
			return $access_token;
		}

		try {
			$access_tokens = $this->oauth2->getTokensByRefresh( $this->client_id, $this->client_secret, $this->refresh_token );

		} catch ( \Exception $e ) {
			$message = sprintf( 'Could not complete refresh with HubSpot, error message %s', $e->getMessage() );
			tribe( 'logger' )->log_error( $message, 'HubSpot Refresh Tokens' );

			return $access_token;
		}

		// Additional Safety Check to Verify Status Code.
		if ( $access_tokens->getStatusCode() !== 200 ) {

			$message = sprintf( 'Could not complete refresh with HubSpot, error code %s', $access_tokens->getStatusCode());
			tribe( 'logger' )->log_error( $message, 'HubSpot Refresh Tokens' );

			return $access_token;
		}


		$this ->update_tokens( $access_tokens );

		return sanitize_text_field( $access_tokens->data->access_token );
	}

	/**
	 * Determine if the API has Valid Access Token and Setup is complete with HubSpot
	 *
	 * @since 1.0
	 *
	 * @return string|false Refreshed access token or false if not ready.
	 */
	public function is_ready() {

		if ( ! $this->is_authorized() ) {
			return false;
		}

		$access_token = $this->maybe_refresh( $this->access_token );

		if ( ! $access_token ) {
			return false;
		}

		if (
			'complete' !== $this->group_name_setup ||
			'complete' !== $this->custom_properties_setup ||
			'complete' !== $this->timeline_event_types_setup
		) {
			return false;
		}

		$this->client->key    = $access_token;
		$this->client->oauth2 = true;

		return $access_token;
	}

	/**
	 * Determine if the API has Valid Access Token and Ready to Attempt Setup
	 *
	 * @since 1.0
	 *
	 * @return string|false Refreshed access token or false if not ready.
	 */
	public function is_ready_for_setup() {

		if ( ! $this->is_authorized() ) {
			return false;
		}

		$access_token = $this->maybe_refresh( $this->access_token );

		if ( ! $access_token ) {
			return false;
		}

		$this->client->key    = $access_token;
		$this->client->oauth2 = true;

		return $access_token;
	}
}