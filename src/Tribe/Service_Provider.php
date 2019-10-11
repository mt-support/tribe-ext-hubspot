<?php

namespace Tribe\HubSpot;

/**
 * Class Tribe\HubSpot\Service_Provider
 *
 * Provides the Event Tickets HubSpot Integration service.
 *
 * This class should handle implementation binding, builder functions and hooking for any first-level hook and be
 * devoid of business logic.
 *
 * @since TBD
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since TBD
	 *
	 */
	public function register() {

		$this->container->singleton( 'tickets.hubspot', Main::instance() );

		$this->container->singleton( 'tickets.hubspot.admin.settings', Admin\Settings::class );
		$this->container->singleton( 'tickets.hubspot.admin.notices', Admin\Notices::class, array( 'hook' ) );

		$this->container->singleton( 'tickets.hubspot.api', API\Connection::class );
		$this->container->singleton( 'tickets.hubspot.oauth', API\Oauth::class, array( 'hook' ) );

		$this->hook();
	}

	/**
	 * Any hooking for any class needs happen here.
	 *
	 * In place of delegating the hooking responsibility to the single classes they are all hooked here.
	 *
	 * @since TBD
	 *
	 */
	protected function hook() {

		tribe( 'tickets.hubspot.api' );
		tribe( 'tickets.hubspot.oauth' );

		if ( is_admin() ) {
			tribe( 'tickets.hubspot.admin.settings' );
			tribe( 'tickets.hubspot.admin.notices' );
		}

	}
}
