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

		$this->container->singleton( 'tickets.hubspot.properties.last_registered_event', Properties\Last_Registered_Event::class );
		$this->container->singleton( 'tickets.hubspot.properties.last_attended_event', Properties\Last_Attended_Event::class );
		$this->container->singleton( 'tickets.hubspot.properties.first_order', Properties\First_Order::class );
		$this->container->singleton( 'tickets.hubspot.properties.last_order', Properties\Last_Order::class );
		$this->container->singleton( 'tickets.hubspot.properties.last_registered_ticket', Properties\Last_Registered_Ticket::class );
		$this->container->singleton( 'tickets.hubspot.properties.aggregate_data', Properties\Aggregate_Data::class );
		$this->container->singleton( 'tickets.hubspot.properties.event_data', Properties\Event_Data::class );

		$this->container->singleton( 'tickets.hubspot.contact.property.group', API\Contact_Property_Group::class, array( 'hook' ) );
		$this->container->singleton( 'tickets.hubspot.contact.property', API\Properties::class, array( 'hook' ) );
		$this->container->singleton( 'tickets.hubspot.timeline', API\Timeline::class, array( 'hook' ) );

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

		//todo change these to only load when required during Sprint 4
		tribe( 'tickets.hubspot.api' );
		tribe( 'tickets.hubspot.oauth' );
		tribe( 'tickets.hubspot.timeline' );
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
