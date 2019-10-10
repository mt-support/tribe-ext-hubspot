<?php

namespace Tribe\HubSpot\Admin;

use SevenShores\Hubspot\Http\Client;
use SevenShores\Hubspot\Resources\OAuth2;
use SevenShores\Hubspot\Factory;
use Tribe__Settings_Manager;

/**
 * Do the Settings.
 */
class Settings {

	/**
	 * The Settings Helper class.
	 *
	 * @var Settings_Helper
	 */
	protected $settings_helper;

	/**
	 * The prefix for our settings keys.
	 *
	 * Gets set automatically from the Text Domain or can be set manually.
	 * The prefix should not end with underscore `_`.
	 *
	 * @var string
	 */
	protected $opts_prefix = 'tribe_hubspot';

	/**
	 * Settings constructor.
	 *
	 * @since 1.0
	 *
	 */
	public function __construct( $opts_prefix ) {
		$this->settings_helper = new Settings_Helper();

		$this->set_options_prefix( $opts_prefix );

		// Add settings specific to OSM
		add_action( 'admin_init', [ $this, 'add_settings' ] );
	}

	/**
	 * Set the options prefix to be used for this extension's settings.
	 *
	 * Always has ends with a single underscore.
	 *
	 * @since 1.0
	 *
	 * @param string $opts_prefix
	 */
	private function set_options_prefix( $opts_prefix ) {

		$opts_prefix = $opts_prefix . '_';

		$this->opts_prefix = str_replace( '__', '_', $opts_prefix );
	}

	/**
	 * Given an option key, get this extension's option value.
	 *
	 * This automatically prepends this extension's option prefix so you can just do `$this->get_option( 'a_setting' )`.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 * @see tribe_get_option()
	 *
	 */
	public function get_option( $key = '', $default = '' ) {
		$key = $this->sanitize_option_key( $key );

		return tribe_get_option( $key, $default );
	}

	/**
	 * Get an option key after ensuring it is appropriately prefixed.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function sanitize_option_key( $key = '' ) {
		$prefix = $this->get_options_prefix();

		if ( 0 === strpos( $key, $prefix ) ) {
			$prefix = '';
		}

		return $prefix . $key;
	}

	/**
	 * Get this extension's options prefix.
	 *
	 * @return string
	 */
	public function get_options_prefix() {
		if ( empty( $this->opts_prefix ) ) {
			$this->set_options_prefix();
		}

		return $this->opts_prefix;
	}

	/**
	 * Get an array of all of this extension's options without array keys having the redundant prefix.
	 *
	 * @return array
	 */
	public function get_all_options() {
		$raw_options = $this->get_all_raw_options();

		$result = [];

		$prefix = $this->get_options_prefix();

		foreach ( $raw_options as $key => $value ) {
			$abbr_key            = str_replace( $prefix, '', $key );
			$result[ $abbr_key ] = $value;
		}

		return $result;
	}

	/**
	 * Get an array of all of this extension's raw options (i.e. the ones starting with its prefix).
	 *
	 * @return array
	 */
	public function get_all_raw_options() {
		$tribe_options = Tribe__Settings_Manager::get_options();

		if ( ! is_array( $tribe_options ) ) {
			return [];
		}

		$result = [];

		foreach ( $tribe_options as $key => $value ) {
			if ( 0 === strpos( $key, $this->get_options_prefix() ) ) {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	/**
	 * Given an option key, delete this extension's option value.
	 *
	 * This automatically prepends this extension's option prefix so you can just do `$this->delete_option( 'a_setting' )`.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function delete_option( $key = '' ) {
		$key = $this->sanitize_option_key( $key );

		$options = Tribe__Settings_Manager::get_options();

		unset( $options[ $key ] );

		return Tribe__Settings_Manager::set_options( $options );
	}

	/**
	 * Adds a new section of fields to Events > Settings > General tab, appearing after the "Map Settings" section and
	 * before the "Miscellaneous Settings" section.
	 */
	public function add_settings() {

		$fields = [

			$this->opts_prefix . 'hubspot_header' => [
				'type' => 'html',
				'html' => $this->get_example_intro_text(),
			],

			$this->opts_prefix . 'hubspot_authorize' => [
				'type' => 'html',
				'html' => $this->get_authorize_fields(),
			],

			$this->opts_prefix . 'hapi_key'       => [
				'type'            => 'text',
				'label'           => esc_html__( 'Developer HAPIkey', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'Enter your Developer HAPIkey', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'alpha_numeric_with_dashes_and_underscores',
			],
			$this->opts_prefix . 'application_id' => [
				'type'            => 'text',
				'label'           => esc_html__( 'ID of the OAuth app', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'alpha_numeric_with_dashes_and_underscores',
			],
			$this->opts_prefix . 'client_id'      => [
				'type'            => 'text',
				'label'           => esc_html__( 'Client ID of the OAuth app', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'alpha_numeric_with_dashes_and_underscores',
			],
			$this->opts_prefix . 'client_secret'  => [
				'type'            => 'text',
				'label'           => esc_html__( 'Client Secret of the OAuth app', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'alpha_numeric_with_dashes_and_underscores',
			],
			$this->opts_prefix . 'access_token'   => [
				'type'            => 'text',
				'label'           => esc_html__( 'Access Token', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'alpha_numeric_with_dashes_and_underscores',
			],
			$this->opts_prefix . 'refresh_token'  => [
				'type'            => 'text',
				'label'           => esc_html__( 'Refresh Token', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'alpha_numeric_with_dashes_and_underscores',
			],
			$this->opts_prefix . 'token_expires'  => [
				'type'            => 'text',
				'label'           => esc_html__( 'Expires', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'alpha_numeric_with_dashes_and_underscores',
			]
		];

		$this->settings_helper->add_fields( $fields, 'addons', 'tribeEventsMiscellaneousTitle', true );
	}

	/**
	 * Here is an example of getting some HTML for the Settings Header.
	 *
	 * @return string
	 */
	 // todo clean this up and move to a admin-views directory?
	private function get_example_intro_text() {
		$result = '<h3>' . esc_html_x( 'HubSpot', 'API connection header', 'tribe-ext-hubspot' ) . '</h3>';
		$result .= '<div style="margin-left: 20px;">';
		$result .= '<p>';
		$result .= esc_html_x( 'You need to connect to your HubSpot account to be able to subscribe to actions.', 'Settings', 'tribe-ext-hubspot' );
		$result .= '</p>';
		$result .= '</div>';


		return $result;
	}

	private function get_authorize_fields() {

		$options = $this->get_all_options();

		$callback      = \Tribe__Settings::instance()->get_url( [ 'tab' => 'addons' ] );
		$client_id     = $options['client_id'];
		$client_secret = $options['client_secret'];
		$access_token  = $options['access_token'];

		$missing_hubspot_credentials = false;

		if ( empty( $access_token ) ) {
			$missing_hubspot_credentials = true;
		}


		// Scope. Use what you need here check:
		// https://developers.hubspot.com/docs/methods/oauth2/initiate-oauth-integration#scopes
		$scope = [ 'contacts', 'timeline' ];

		$my_client = new Client( [ 'key' => $client_secret ] );
		$my_oauth2 = new OAuth2( $my_client );

		//todo remove outside div
		ob_start();
		?>
		<div>
			<fieldset id="tribe-field-hubspot_token" class="tribe-field tribe-field-text tribe-size-medium">
				<legend class="tribe-field-label"><?php esc_html_e( 'HubSpot Token', 'tribe-ext-hubspot' ) ?></legend>
				<div class="tribe-field-wrap">
					<?php
					if ( $missing_hubspot_credentials ) {
						echo '<p>' . esc_html__( 'You need to connect to HubSpot.' ) . '</p>';
						$hubspot_button_label = __( 'Connect to HubSpot', 'tribe-ext-hubspot' );
						$authorizeLink        = $my_oauth2->getAuthUrl( $client_id, $callback, $scope );
					} else {
						$hubspot_button_label     = __( 'Refresh your connection to HubSpot', 'tribe-ext-hubspot' );
						$hubspot_disconnect_label = __( 'Disconnect', 'tribe-ext-hubspot' );
						$hubspot_disconnect_url   = \Tribe__Settings::instance()->get_url( [ 'tab' => 'addons' ] );
					}
					?>
					<a target="_blank" class="tribe-ea-eventbrite-button" href="<?php echo esc_url( $authorizeLink ); ?>"><?php esc_html_e( $hubspot_button_label ); ?></a>
					<!--					<?php /*if ( ! $missing_eb_credentials ) : */ ?>
					<a href="<?php /*echo esc_url( $hubspot_disconnect_url ); */ ?>" class="tribe-ea-hubspot-disconnect"><?php /*echo esc_html( $hubspot_disconnect_label ); */ ?></a>
				--><?php /*endif; */ ?>
				</div>
			</fieldset>
		</div>
		<?php


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

		//todo remove test coding
		$this->get_data();

		return ob_get_clean();
	}

	private function get_data() {

		$options       = $this->get_all_options();
		$access_token  = $options['access_token'];
		$client_id = $options['client_id'];
		$client_secret = $options['client_secret'];
		$refresh_token = $options['refresh_token'];
		$token_expires = $options['token_expires'];

		if ( empty( $access_token ) ) {
			return;
		}

		$my_client = new Client( [ 'key' => $client_secret ] );
		$my_oauth2 = new OAuth2( $my_client );

		if ( current_time( 'timestamp' ) + 60 >= $token_expires ) {

			$refreshToken = $options['refresh_token'];

			$access_tokens =  $my_oauth2->getTokensByRefresh($client_id, $client_secret, $refresh_token);
			if ( $access_tokens->getStatusCode() !== 200 ) {
				log_me( "Could not refresh access! Please re-connect." );
			} // http_code => 400 means the user disconnected from HS platform | all refresh tokens will be revoked | You will still have access until the access token expires.

			tribe_update_option( $this->opts_prefix . 'access_token', $access_tokens->data->access_token );
			tribe_update_option( $this->opts_prefix . 'refresh_token', $access_tokens->data->refresh_token );
			tribe_update_option( $this->opts_prefix . 'token_expires', current_time( 'timestamp' ) + $access_tokens->data->expires_in );

			// Use New Access Token
			$access_token = $access_tokens->data->access_token;

		}

		// update my_client to use oauth2
		$my_client->key    = $access_token;
		$my_client->oauth2 = true;

		// create Factory using createWithToken and my_client as client
		$hubspot = Factory::createWithToken( $access_token, $my_client );

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
