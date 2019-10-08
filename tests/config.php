<?php
global $wp_tests_options;
$wp_tests_options['installation_filters'] = [
	'remove' => [
		/**
		 * EDD will try to run this during installation causing a DB-related warning
		 * so we remove it during the installation phase.
		 */
		[ 'user_register', 'edd_connect_existing_customer_to_new_user', 10, 1 ],
	]
];

/**
 * Spoof the checks that EDD makes during installation to avoid more DB-related noise.
 */
$wp_tests_options['edd_use_php_sessions'] = true;
$wp_tests_options['edd_settings'] = array( 'some-key' => 'some-value' );
