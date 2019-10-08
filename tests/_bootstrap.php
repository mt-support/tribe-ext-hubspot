<?php
/**
 * @file Global bootstrap for all codeception tests
 */

$tec_tests_folder = __DIR__ . '/../../the-events-calendar/tests';
$et_tests_folder = __DIR__ . '/../../event-tickets/tests';
$etplus_tests_folder = __DIR__ . '/../../event-tickets-plus/tests';

/**
 * We'll also use a number of support classes defined in ET tests
 */
Codeception\Util\Autoload::addNamespace( 'Tribe__Events__WP_UnitTestCase', $et_tests_folder . '/_support' );
Codeception\Util\Autoload::addNamespace( '\Tribe\Events\Test', $tec_tests_folder . '/_support' );
Codeception\Util\Autoload::addNamespace( '\Tribe\Tickets\Test', $et_tests_folder . '/_support' );

Codeception\Util\Autoload::addNamespace( 'Tribe\Tickets_Plus\Test', $etplus_tests_folder . '/_support' );

// Let's make sure, in the context of any test, any async system will be used in synchronous mode.
define( 'TRIBE_NO_ASYNC', true );
// Let's make sure any deletion is actually a real deletion and not just a trashing.
define( 'EMPTY_TRASH_DAYS', 0 );
