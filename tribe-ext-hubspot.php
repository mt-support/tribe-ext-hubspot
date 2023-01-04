<?php
/**
 * Plugin Name:       Event Tickets HubSpot Integration
 * Plugin URI:        http://m.tri.be/hubspot
 * GitHub Plugin URI: https://github.com/mt-support/tribe-ext-hubspot
 * Description:       Event Tickets and Event Tickets Plus HubSpot Integration
 * Version:           1.0.3
 * Extension Class:   Tribe\HubSpot\Setup
 * Author:            The Events Calendar
 * Author URI:        https://evnt.is/1aor
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tribe-ext-hubspot
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

// Do not load unless Tribe Common is fully loaded and our class does not yet exist.
if (
	! class_exists( 'Tribe__Extension' )
	|| class_exists( 'Tribe\HubSpot\Setup' )
) {
	return;
}

define( 'EVENT_TICKETS_HUBSPOT_DIR', dirname( __FILE__ ) );
define( 'EVENT_TICKETS_HUBSPOT_FILE', __FILE__ );

// Load the Composer autoload file.
require_once dirname( EVENT_TICKETS_HUBSPOT_FILE ) . '/vendor/autoload.php';

Tribe\HubSpot\Setup::instance();