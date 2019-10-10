<?php

namespace Tribe\HubSpot;

use Tribe\HubSpot\Main;

/**
 * todo HUBSPOT Connection BUILD
 * Initial Connection - Try-Exception Error Handling
 * Refresh Connection
 * Disconnect
 * Manual Refresh
 * Add Admin Notice if Connection Not Set or Connection Not Authorized
 * Goal|Last Part - Ready to get data from a common class
 *
 */
class Connection {

	/**
	 * @var string
	 */
	protected $callback = '';

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

		$this->callback      = \Tribe__Settings::instance()->get_url( [ 'tab' => 'addons' ] );

		$this->options       = Main::instance()->get_all_options();
		$this->access_token  = $this->options['access_token'];
		$this->client_id = $this->options['client_id'];
		$this->client_secret = $this->options['client_secret'];
		$this->refresh_token = $this->options['refresh_token'];
		$this->token_expires = $this->options['token_expires'];

	}

	public function oauth() {

		if ( isset( $_GET['code'] ) ) {

			// sanitize GET
			$safeGet   = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
			$tokenCode = $safeGet['code'];

			// get access tokens
			$access_tokens = $my_oauth2->getTokensByCode( $client_id, $client_secret, $callback, $tokenCode );

			tribe_update_option( $this->opts_prefix . 'access_token', $access_tokens->data->access_token );
			tribe_update_option( $this->opts_prefix . 'refresh_token', $access_tokens->data->refresh_token );
			tribe_update_option( $this->opts_prefix . 'token_expires', current_time( 'timestamp' ) + $access_tokens->data->expires_in );

		}

	}
}