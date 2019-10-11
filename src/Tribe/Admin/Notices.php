<?php

namespace Tribe\HubSpot\Admin;

use Tribe__Admin__Notices;

class Notices {

	/**
	 * Hooks the class method to relevant filters and actions.
	 *
	 * @since 1.0
	 *
	 */
	public function hook() {

		tribe_notice(
			$this->slug( 'missing-application-credentials' ),
			array( $this, 'render_missing_application_credentials_notice' ),
			array(
				'type'    => 'warning',
				'dismiss' => 1,
				'wrap'    => false,
			),
			array( $this, 'should_render_missing_application_credentials_notice' )
		);

		tribe_notice(
			$this->slug( 'missing-access-token' ),
			array( $this, 'render_missing_access_token_notice' ),
			array(
				'type'    => 'warning',
				'dismiss' => 1,
				'wrap'    => false,
			),
			array( $this, 'should_render_missing_access_token_notice' )
		);
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
		set_transient( $this->slug( 'show-missing-application-credentials' ), '1', DAY_IN_SECONDS );
	}

	/**
	 * Renders (echoes) the missing application credentials admin notice.
	 *
	 * @since 1.0
	 *
	 */
	public function render_missing_application_credentials_notice() {
		Tribe__Admin__Notices::instance()->render_paragraph(
			$this->slug( 'missing-application-credentials' ),
			sprintf( '%s, <a href="%s" target="_blank">%s</a>.',
				esc_html__( 'HubSpot is missing the Application Credentials necessary to authorize your application. Please', 'tribe-ext-hubspot' ),
				esc_url( admin_url() . '?page=tribe-common&tab=addons#tribe-hubspot-application-credentials' ),
				esc_html__( 'set it in the settings.', 'tribe-ext-hubspot' )
			)
		);
	}

	/**
	 * Whether the missing application credentials token notice should be rendered or not.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function should_render_missing_application_credentials_notice() {
		$transient      = get_transient( $this->slug( 'show-missing-application-credentials' ) );
		$missing_credentials = tribe( 'tickets.hubspot.api' )->has_required_fields();

		return ! empty( $transient ) && empty( $missing_credentials );
	}


	/**
	 * Triggers the display of the missing access token notice.
	 *
	 * @since 1.0
	 */
	public function show_missing_access_token_notice() {
		set_transient( $this->slug( 'show-missing-access-token' ), '1', DAY_IN_SECONDS );
	}

	/**
	 * Renders (echoes) the missing access token admin notice.
	 *
	 * @since 1.0
	 *
	 */
	public function render_missing_access_token_notice() {
		Tribe__Admin__Notices::instance()->render_paragraph(
			$this->slug( 'missing-access-token' ),
			sprintf( '%s, <a href="%s" target="_blank">%s</a>.',
				esc_html__( 'HubSpot is not authorized and data is unable to be sent.', 'tribe-ext-hubspot' ),
				esc_url( admin_url() . '?page=tribe-common&tab=addons#tribe-hubspot-application-credentials' ),
				esc_html__( 'Please authorize or refresh your token.', 'tribe-ext-hubspot' )
			)
		);
	}

	/**
	 * Whether the missing access token notice should be rendered or not.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function should_render_missing_access_token_notice() {

		$transient      = get_transient( $this->slug( 'show-missing-access-token' ) );
		$access_token =  tribe( 'tickets.hubspot.api' )->is_authorized();

		return ! empty( $transient ) && empty( $access_token );
	}

}
