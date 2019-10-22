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
 * @since 1.0
 *
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0
	 *
	 */
	public function register() {

		$this->container->singleton( 'tickets.hubspot', Main::instance() );

		$this->container->singleton( 'tickets.hubspot.admin.settings', Admin\Settings::class );
		$this->container->singleton( 'tickets.hubspot.admin.notices', Admin\Notices::class, array( 'hook' ) );

		$this->container->singleton( 'tickets.hubspot.process.setup', Process\Setup::class, array( 'hook' ) );

		$this->container->singleton( 'tickets.hubspot.api', API\Connection::class );
		$this->container->singleton( 'tickets.hubspot.oauth', API\Oauth::class, array( 'hook' ) );

		$this->container->singleton( 'tickets.hubspot.properties.last_registered_event', new Properties\Last_Registered_Event() );
		$this->container->singleton( 'tickets.hubspot.properties.last_attended_event', new Properties\Last_Attended_Event() );
		$this->container->singleton( 'tickets.hubspot.properties.first_order', new Properties\First_Order() );
		$this->container->singleton( 'tickets.hubspot.properties.last_order', new Properties\Last_Order() );
		$this->container->singleton( 'tickets.hubspot.properties.last_registered_ticket', new Properties\Last_Registered_Ticket() );
		$this->container->singleton( 'tickets.hubspot.properties.event_data', new Properties\Event_Data() );

		$this->container->singleton( 'tickets.hubspot.contact.property', API\Contact_Property::class, array( 'hook' ) );
		$this->container->singleton( 'tickets.hubspot.contact.property.group', API\Contact_Property_Group::class, array( 'hook' ) );

		$this->container->singleton( 'tickets.hubspot.subscribe.purchase', Subscribe\Purchase::class, array( 'hook' ) );

		$this->hook();
	}

	/**
	 * Any hooking for any class needs happen here.
	 *
	 * In place of delegating the hooking responsibility to the single classes they are all hooked here.
	 *
	 * @since 1.0
	 *
	 */
	protected function hook() {

		tribe( 'tickets.hubspot.api' );
		tribe( 'tickets.hubspot.oauth' );
		add_action( 'tribe_hubspot_authorize_site', tribe_callback( 'tickets.hubspot.api', 'save_access_token' ) );

		tribe( 'tickets.hubspot.contact.property.group' );
		tribe( 'tickets.hubspot.contact.property' );

		tribe( 'tickets.hubspot.process.setup' );
		tribe( 'tickets.hubspot.subscribe.purchase' );

		if ( is_admin() ) {
			tribe( 'tickets.hubspot.admin.settings' );
			tribe( 'tickets.hubspot.admin.notices' );
		}

	}
}
