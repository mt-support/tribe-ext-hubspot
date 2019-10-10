<?php

namespace Tribe\HubSpot\API;

use SevenShores\Hubspot\Factory;
use SevenShores\Hubspot\Http\Client;
use SevenShores\Hubspot\Resources\OAuth2;

/**
 * todo HUBSPOT Connection BUILD
 * Initial Connection - Callback, Get Code, Save Code
 * Refresh - Hook into Automatically Refresh
 * Add Admin Notice if Connection Not Set or Connection Not Authorized
 * Goal|Last Part - Ready to get data from a common class
 *
 * Maybe
 * Disconnect
 */
class Connection {

	/**
	 * @var string
	 */
	protected $callback = '';

	/**
	 * @var array
	 */
	protected $scope = [ 'contacts', 'timeline' ];

	/**
	 * @var string
	 */
	protected $client_id = '';

	/**
	 * @var string
	 */
	protected $client_secret = '';

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

	public function __construct() {

		//todo add a callback handler on a specific url that then redirects to settings page
		$this->callback      = \Tribe__Settings::instance()->get_url( [ 'tab' => 'addons' ] );
		$this->options       = tribe( 'tickets.hubspot' )->get_all_options();
		$this->opts_prefix   = tribe( 'tickets.hubspot.settings' )->get_options_prefix();
		$this->access_token  = $this->options['access_token'];
		$this->client_id     = $this->options['client_id'];
		$this->client_secret = $this->options['client_secret'];
		$this->refresh_token = $this->options['refresh_token'];
		$this->token_expires = $this->options['token_expires'];

		//todo add handler for options that do not exist to prevent undefinded notices
		//$this->hapi_key = $this->options['hapi_key'];

		$this->client = new Client( [ 'key' => $this->client_secret ] );
		$this->oauth2 = new OAuth2( $this->client );

	}

	public function is_authorized() {

		if (
			empty( $this->client_id ) ||
			empty( $this->client_secret ) ||
			empty( $this->access_token ) ||
			empty( $this->refresh_token ) ||
			empty( $this->token_expires )
		) {
			return false;
		}

		return true;

	}

	public function get_authorized_url() {

		return $this->oauth2->getAuthUrl( $this->client_id, $this->callback, $this->scope );
	}


	protected function update_tokens( $access_tokens ) {

		tribe_update_option( $this->opts_prefix . 'access_token', sanitize_text_field( $access_tokens->data->access_token ) );
		tribe_update_option( $this->opts_prefix . 'refresh_token', sanitize_text_field( $access_tokens->data->refresh_token ) );
		tribe_update_option( $this->opts_prefix . 'token_expires', sanitize_text_field( current_time( 'timestamp' ) + $access_tokens->data->expires_in ) );
	}

	public function save_access_token() {

		// Sanitize GET Before Use.
		$safe_get   = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
		$token_code = $safe_get['code'];
		log_me( '$safe_get' );
		log_me( $safe_get );
		log_me( $this->client_id );
		log_me( $this->client_secret );
		log_me( $this->callback );

		// Get Access Tokens from HubSpot.
		//todo add try exception here
		$access_tokens = $this->oauth2->getTokensByCode( $this->client_id, $this->client_secret, $this->callback, $token_code );

		if ( $access_tokens->getStatusCode() !== 200 ) {
			log_me( "Could not refresh access! Please re-connect." );
		} // http_code => 400 means the user disconnected from HS platform | all refresh tokens will be revoked | You will still have access until the access token expires.

		$this ->update_tokens( $access_tokens );
	}

	protected function maybe_refresh() {

		// If there is no refresh token then there is nothing to do.
		if ( empty( $this->refresh_token ) ) {
			return;
		}

		// Refresh Any Token that is Expired or within a Minute of Expiring.
		if ( current_time( 'timestamp' ) + 60 <= $this->token_expires ) {
			return;
		}

		$access_tokens = $this->oauth2->getTokensByRefresh( $this->client_id, $this->client_secret, $this->refresh_token );
		if ( $access_tokens->getStatusCode() !== 200 ) {
			log_me( "Could not refresh access! Please re-connect." );
		}

		$this ->update_tokens( $access_tokens );

	}

	public function test() {

		$this->maybe_refresh();

		$this->client->key    = $this->access_token;
		$this->client->oauth2 = true;

		$hubspot = Factory::createWithToken( $this->access_token, $this->client );

		// test Factory
		$response = $hubspot->contacts()->all( [
			'count'    => 10,
			'property' => [ 'firstname', 'lastname' ],
		] );

		foreach ( $response->contacts as $contact ) {
			log_me( sprintf( "Contact name is %s %s." . PHP_EOL, $contact->properties->firstname->value, $contact->properties->lastname->value ) );
		}

	}
}