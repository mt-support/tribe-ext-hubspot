<?php

namespace Tribe\HubSpot\API;

use Tribe\Events\Admin\Settings as TEC_Settings;
use Tribe\HubSpot\Process\Setup_Queue;

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
	 * @since 1.0.3 - Remove the check for the nonce as query string is not supported with Hubspot redirect url.
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

		// Clear queue in case a queued setup item is causing an issue.
		$queue = new Setup_Queue();
		$queue->delete_all_queues( 'hubspot_setup_queue' );

		/**
		 * Hook to Save Data During Authorization of Site
		 *
		 * @since 1.0
		 */
		do_action( 'tribe_hubspot_authorize_site' );

		wp_safe_redirect( tribe( TEC_Settings::class )->get_url( [ 'tab' => 'addons' ] ) );

		tribe_exit();
	}

}
