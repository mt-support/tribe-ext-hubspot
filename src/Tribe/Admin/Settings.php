<?php

namespace Tribe\HubSpot\Admin;

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

			$this->opts_prefix . 'HubSpot' => [
				'type' => 'html',
				'html' => $this->get_example_intro_text(),
			],

			$this->opts_prefix . 'hapi_key'       => [
				'type'            => 'text',
				'label'           => esc_html__( 'Developer HAPIkey', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'Enter your Developer HAPIkey', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'string',
			],
			$this->opts_prefix . 'application_id' => [
				'type'            => 'text',
				'label'           => esc_html__( 'ID of the OAuth app', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'string',
			],
			$this->opts_prefix . 'access_token'   => [
				'type'            => 'text',
				'label'           => esc_html__( 'Access Token', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'string',
			],
			$this->opts_prefix . 'refresh_token'  => [
				'type'            => 'text',
				'label'           => esc_html__( 'Refresh Token', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'string',
			],
			$this->opts_prefix . 'token_expires'  => [
				'type'            => 'text',
				'label'           => esc_html__( 'ID of the OAuth app', 'tribe-ext-hubspot' ),
				'tooltip'         => sprintf( esc_html__( 'DESCRIPTION', 'tribe-ext-hubspot' ) ),
				'validation_type' => 'number',
			]
		];

		$this->settings_helper->add_fields( $fields, 'addons', 'tribeEventsMiscellaneousTitle', true );
	}

	/**
	 * Here is an example of getting some HTML for the Settings Header.
	 *
	 * @return string
	 */
	private function get_example_intro_text() {
		$result = '<h3>' . esc_html_x( 'HubSpot', 'API connection header', 'tribe-ext-hubspot' ) . '</h3>';
		$result .= '<div style="margin-left: 20px;">';
		$result .= '<p>';
		$result .= esc_html_x( 'You need to connect to your HubSpot account to be able to subscribe to actions.', 'Settings', 'tribe-ext-hubspot' );
		$result .= '</p>';
		$result .= '</div>';

		return $result;
	}

}
