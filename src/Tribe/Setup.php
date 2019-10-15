<?php

namespace Tribe\HubSpot;

use Tribe\HubSpot\Main;
use Tribe__Autoloader;
use Tribe__Extension;

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

				$message .= sprintf( __( '%1$s requires PHP version %2$s or newer to work. Please contact your website host and inquire about updating PHP.', 'tribe-ext-hubspot' ), $this->get_name(), $php_required_version );

				$message .= sprintf( ' <a href="%1$s">%1$s</a>', 'https://wordpress.org/about/requirements/' );

				$message .= '</p>';

				tribe_notice( 'tribe-ext-hubspot' . '-php-version', $message, [ 'type' => 'error' ] );
			}

			return false;
		}

		return true;
	}

} // end class
