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

		// Stop if we are missing the OAuth code.
		if ( empty( $_GET['code'] ) ) {
			die();
		}

		// Stop if the nonce is missing or is not valid.
		if (
			! isset( $_GET['hubspot-oauth-nonce'] ) ||
			! wp_verify_nonce( $_GET['hubspot-oauth-nonce'], 'hubspot-oauth-action' )
		) {
			die();
		}

		/**
		 * Hook to Save Data During Authorization of Site
		 *
		 * @since 1.0
		 *
		 */
		do_action( 'tribe_hubspot_authorize_site' );

		wp_safe_redirect( \Tribe__Settings::instance()->get_url( [ 'tab' => 'addons' ] ) );

		tribe_exit();
	}

}