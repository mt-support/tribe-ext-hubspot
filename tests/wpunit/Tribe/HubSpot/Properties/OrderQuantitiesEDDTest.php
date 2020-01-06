<?php

namespace Tribe\HubSpot\Properties;

use Codeception\TestCase\WPTestCase;
use Tribe\Events\Test\Factories\Event;
use Tribe\Tickets_Plus\Test\Commerce\EDD\Ticket_Maker as Ticket_Maker;
use Tribe\Tickets_Plus\Test\Commerce\EDD\Order_Maker as Order_Maker;
use Tribe\HubSpot\Properties\Event_Data;

class OrderQuantitiesEDDTest extends WPTestCase {

	use Ticket_Maker;
	use Order_Maker;

	public function setUp() {
		// before
		parent::setUp();

		$this->factory()->event = new Event();

		$this->event_data = new Event_Data();
	}

	public function tearDown() {
		// your tear down methods here
		// then
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function it_should_return_edd_ticket_quantities_from_order() {
		$event_id  = $this->factory()->event->create();
		$ticket_id = $this->create_edd_ticket( $event_id, 5 );
		$order_id  = $this->create_edd_order( $ticket_id, 6, [ 'status' => 'completed' ] );
		$order     = edd_get_payment( $order_id );

		// Get Order Quantities
		$valid_order_items = $this->event_data->get_edd_order_quantities( $order );

		self::assertEquals( 6, $valid_order_items['total'] );
		self::assertEquals( 6, $valid_order_items['tickets'][ $ticket_id ] );
		self::assertCount( 1, $valid_order_items['tickets'] );
		self::assertEquals( 1, $valid_order_items['events_per_order'] );
	}
}
