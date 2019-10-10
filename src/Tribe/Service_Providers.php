<?php


namespace Tribe\HubSpot;

use Tribe\HubSpot\Main;

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
	 */
	public function register() {

		$this->container->singleton( 'tickets.hubspot', Main::instance() );
		$this->container->singleton( 'tickets.hubspot', 'Tribe__Tickets__Tickets_Handler' );

		$this->hook();
	}

	/**
	 * Any hooking for any class needs happen here.
	 *
	 * In place of delegating the hooking responsibility to the single classes they are all hooked here.
	 *
	 * @since TBD
	 */
	protected function hook() {

	}
}
