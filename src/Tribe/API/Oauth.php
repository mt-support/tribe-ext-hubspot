<?php

namespace Tribe\HubSpot\API;

/**
 * Class Oauth
 *
 * @package Tribe\HubSpot\API
 */
class Oauth {

	/**
	 * Setup Hooks for OAuth
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {
		add_action( 'init', [ $this, 'add_endpoint' ], 10, 0 );
		add_action( 'parse_request', [ $this, 'handle_request' ], 10, 1 );
	}

	/**
	 * Add HubSpot OAuth Endpoint to Root of Site
	 *
	 * @since 1.0
	 *
	 */
	public function add_endpoint() {
		add_rewrite_endpoint( 'tribe-hubspot', EP_ROOT );
	}

	/**
	 * Detect if HubSpot Endpoint and Save Access Token then Redirect to Settings
	 *
	 * @since 1.0
	 *
	 * @param object $wp An object of the WordPress Query.
	 */
	public function handle_request( $wp ) {

		if ( ! isset( $wp->query_vars['tribe-hubspot'] ) ) {
			return;
		}

		//If missing essential fields then stop here
		if ( empty( $_GET['code'] ) ) {
			die();
		}

		tribe( 'tickets.hubspot.api' )->save_access_token();

		wp_safe_redirect( \Tribe__Settings::instance()->get_url( [ 'tab' => 'addons' ] ) );

		tribe_exit();
	}

}