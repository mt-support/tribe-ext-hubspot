<?php

namespace Tribe\HubSpot\Admin;

use Tribe__Admin__Notices;

class Notices {

	/**
	 * Hooks the actions and filters used by the class.
	 *
	 * Too late to use 'plugins_loaded' or 'tribe_plugins_loaded'
	 * and must be before 'admin_notices' to use tribe_notice().
	 *
	 * @since 1.0
	 */
	public function hook() {
		add_action( 'admin_init', [ $this, 'show_missing_application_credentials_notice' ] );
		add_action( 'admin_init', [ $this, 'show_missing_access_token_notice' ], 100 );
	}

	/**
	 * Builds a slug used by the class.
	 *
	 * @since 1.0
	 *
	 * @param $string
	 *
	 * @return string
	 */
	protected function slug( $string ) {
		return 'tickets-hubspot-' . $string;
	}

	/**
	 * Triggers the display of the missing application credentials notice.
	 *
	 * @since 1.0
	 */
	public function show_missing_application_credentials_notice() {

		/** @var \Tribe__Settings $settings */
		$settings = tribe( 'settings' );

		// Bail if user cannot change settings.
		if ( ! current_user_can( $settings->requiredCap ) ) {
			return;
		}

		// Bail if previously dismissed this notice.
		if ( Tribe__Admin__Notices::instance()->has_user_dimissed( $this->slug( 'missing-application-credentials' ) ) ) {
			return;
		}

		// Bail if the Application Credentials are Saved.
		$options       = tribe( 'tickets.hubspot' )->get_all_options();
		if (
			! empty( $options[ 'user_id' ] ) &&
			! empty( $options[ 'app_id' ] ) &&
			! empty( $options[ 'hapi_key' ] ) &&
			! empty( $options[ 'client_id' ] ) &&
			! empty( $options[ 'client_secret' ] )
		) {
			return;
		}

		// Bail if already at wp-admin > Events > Settings > APIs tab to avoid confusion.
		if (
			'tribe-common' === tribe_get_request_var( 'page' )
			&& 'addons' === tribe_get_request_var( 'tab' )
		) {
			return;
		}

		tribe_notice(
			$this->slug( 'missing-application-credentials' ),
			$this->render_missing_application_credentials_notice( $settings ),
			[
				'type'    => 'warning',
				'dismiss' => true,
			]
		);

		return;
	}

	/**
	 * Renders (echoes) the missing application credentials admin notice.
	 *
	 * @since 1.0
	 *
	 */
	public function render_missing_application_credentials_notice( $settings ) {

		// Get link to APIs Tab.
		$url = $settings->get_url( [
			'page' => 'tribe-common',
			'tab'  => 'addons',
		] );

		$message = sprintf( '<div><p>%s, <a href="%s" target="_blank">%s</a>.</p></div>',
			esc_html_x( 'HubSpot is missing the Credentials necessary to authorize your application. Please', 'first part of notice there is no settings saved for HubSpot.', 'tribe-ext-hubspot' ),
			esc_url( $url ),
			esc_html_x( 'set it in the settings', 'link text of notice there is no settings saved for HubSpot.' , 'tribe-ext-hubspot' )
		);

		return $message;
	}

	/**
	 * Triggers the display of the missing access token notice.
	 *
	 * @since 1.0
	 */
	public function show_missing_access_token_notice() {

		/** @var \Tribe__Settings $settings */
		$settings = tribe( 'settings' );

		// Bail if user cannot change settings.
		if ( ! current_user_can( $settings->requiredCap ) ) {
			return;
		}

		// Bail if previously dismissed this notice.
		if ( Tribe__Admin__Notices::instance()->has_user_dimissed( $this->slug( 'missing-access-token' ) ) ) {
			return;
		}

		/** @var \Tribe\HubSpot\Main $options */
		$options       = tribe( 'tickets.hubspot' )->get_all_options();

		// Bail if the Application Credentials are Empty.
		if (
			empty( $options[ 'user_id' ] ) ||
			empty( $options[ 'app_id' ] ) ||
			empty( $options[ 'hapi_key' ] ) ||
			empty( $options[ 'client_id' ] ) ||
			empty( $options[ 'client_secret' ] )
		) {
			return;
		}


		// Bail if the Application Credientials are Empty.
		if (
			! empty( $options[ 'access_token' ] ) &&
			! empty( $options[ 'refresh_token' ] ) &&
			! empty( $options[ 'token_expires' ] )
		) {
			return;
		}

		// Bail if already at wp-admin > Events > Settings > APIs tab to avoid confusion.
		if (
			'tribe-common' === tribe_get_request_var( 'page' )
			&& 'addons' === tribe_get_request_var( 'tab' )
		) {
			return;
		}

		tribe_notice(
			$this->slug( 'missing-access-token' ),
			$this->render_missing_access_token_notice( $settings ),
			[
				'type'    => 'warning',
				'dismiss' => 1,
			]
		);

		return;
	}

	/**
	 * Renders (echoes) the missing access token admin notice.
	 *
	 * @since 1.0
	 *
	 */
	public function render_missing_access_token_notice( $settings ) {

		// Get link to APIs Tab.
		$url = $settings->get_url( [
			'page' => 'tribe-common',
			'tab'  => 'addons',
		] );


		$message = sprintf( '<div><p>%s, <a href="%s" target="_blank">%s</a>.</p></div>',
			esc_html_x( 'HubSpot is not authorized', 'first part of notice there is no connection with HubSpot.', 'tribe-ext-hubspot' ),
			esc_url( $url ),
			esc_html_x( 'Please authorize or refresh your token', 'link text of notice there is no connection with HubSpot.','tribe-ext-hubspot' )
		);

		return $message;
	}
}
