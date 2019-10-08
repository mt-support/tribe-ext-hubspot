<?php
/**
 * Plugin Name:       Event Tickets HubSpot Integration
 * Plugin URI:        https://theeventscalendar.com/extensions/---the-extension-article-url---/
 * GitHub Plugin URI: https://github.com/mt-support/tribe-ext-hubspot
 * Description:       Event Tickets and Event Tickets Plus HubSpot Integration
 * Version:           1.0.0
 * Extension Class:   Tribe\Extensions\HubSpot\Setup
 * Author:            Modern Tribe, Inc.
 * Author URI:        http://m.tri.be/1971
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

namespace Tribe\Extensions\HubSpot;

use Tribe\HubSpot\Main;
use Tribe__Autoloader;
use Tribe__Extension;

/**
 * Define Constants
 */
if ( ! defined( __NAMESPACE__ . '\NS' ) ) {
	define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );
}

if ( ! defined( NS . 'PLUGIN_TEXT_DOMAIN' ) ) {
	// `Tribe\Extensions\HubSpot\PLUGIN_TEXT_DOMAIN` is defined
	define( NS . 'PLUGIN_TEXT_DOMAIN', 'tribe-ext-hubspot' );
}

// Do not load unless Tribe Common is fully loaded and our class does not yet exist.
if (
	! class_exists( 'Tribe__Extension' )
	|| class_exists( NS . 'Main' )
) {
	return;
}

define( 'EVENT_TICKETS_HUBSPOT_DIR', dirname( __FILE__ ) );
define( 'EVENT_TICKETS_HUBSPOT_FILE', __FILE__ );

// Load the Composer autoload file.
require_once dirname( EVENT_TICKETS_HUBSPOT_FILE ) . '/vendor/autoload.php';

/**
 * Extension main class, class begins loading on init() function.
 *
 * @since 1.0
 *
 */
class Setup extends Tribe__Extension {

	/**
	 * @var Tribe__Autoloader
	 */
	private $class_loader;

	/**
	 * Setup the Extension's properties.
	 *
	 * This always executes even if the required plugins are not present.
	 *
	 * @since 1.0
	 *
	 */
	public function construct() {

		$this->add_required_plugin( 'Tribe__Tickets__Main', '4.10' );
		// $this->add_required_plugin( 'Tribe__Tickets_Plus__Main', '4.3.3' );

	}

	/**
	 * Extension initialization and hooks.
	 *
	 * @since 1.0
	 *
	 */
	public function init() {
		// Load plugin textdomain
		// Don't forget to generate the 'languages/tribe-ext-extension-template.pot' file
		load_plugin_textdomain( 'tribe-ext-hubspot', false, basename( dirname( __FILE__ ) ) . '/languages/' );

		if ( ! $this->php_version_check() ) {
			return;
		}

		// Start Main Class.
		Main::instance();

	}

	/**
	 * Check if we have a sufficient version of PHP. Admin notice if we don't and user should see it.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	private function php_version_check() {
		$php_required_version = '5.6';

		if ( version_compare( PHP_VERSION, $php_required_version, '<' ) ) {
			if (
				is_admin()
				&& current_user_can( 'activate_plugins' )
			) {
				$message = '<p>';

				$message .= sprintf( __( '%s requires PHP version %s or newer to work. Please contact your website host and inquire about updating PHP.', PLUGIN_TEXT_DOMAIN ), $this->get_name(), $php_required_version );

				$message .= sprintf( ' <a href="%1$s">%1$s</a>', 'https://wordpress.org/about/requirements/' );

				$message .= '</p>';

				tribe_notice( PLUGIN_TEXT_DOMAIN . '-php-version', $message, [ 'type' => 'error' ] );
			}

			return false;
		}

		return true;
	}

} // end class
