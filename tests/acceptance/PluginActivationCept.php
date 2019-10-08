<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'activate Event Tickets HubSpot Integration on a fresh WordPress installation and deactivate it' );

// set the `active_plugins` in the database to an empty array to make sure no plugin is active
// by default the database dump has Event Tickets active
$I->haveOptionInDatabase( 'active_plugins', [] );

$I->loginAsAdmin();
$I->amOnPluginsPage();
$I->seePluginDeactivated( 'event-tickets' );
$I->seePluginDeactivated( 'event-tickets-plus' );
$I->seePluginDeactivated( 'tribe-ext-hubspot' );

$I->activatePlugin( [ 'event-tickets', 'event-tickets-plus', 'tribe-ext-hubspot' ] );

// to get back to the plugins page if redirected after the plugin activation
$I->amOnPluginsPage();

$I->seePluginActivated( 'event-tickets' );
$I->seePluginActivated( 'event-tickets-plus' );
$I->seePluginActivated( 'tribe-ext-hubspot' );

$I->deactivatePlugin( 'tribe-ext-hubspot' );

// to get back to the plugins page if redirected after the plugin activation
$I->amOnPluginsPage();

$I->seePluginDeactivated( 'tribe-ext-hubspot' );

// and we stop here: verifying Event Tickets and Event Tickets Plus plugin deactivation should be handled in
// their respective tests
