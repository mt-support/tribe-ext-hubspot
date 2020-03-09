<?php

namespace Tribe\HubSpot\Properties;

use Codeception\TestCase\WPTestCase;
use Tribe\Events\Test\Factories\Event;
use Tribe\Tickets_Plus\Test\Commerce\WooCommerce\Order_Maker;
use Tribe\Tickets_Plus\Test\Commerce\WooCommerce\Ticket_Maker;
use Tribe\HubSpot\Properties\Event_Data;
use WC_Order;

class OrderQuantitiesWooTest extends WPTestCase {

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
	public function it_should_return_woo_ticket_quantities_from_order() {
		$event_id  = $this->factory()->event->create();
		$ticket_id = $this->create_woocommerce_ticket( $event_id, 5 );
		$order_id  = $this->create_woocommerce_order( $ticket_id, 2, [ 'status' => 'completed' ] );
		$order     = new WC_Order( $order_id );

		// Get Order Quantities
		$valid_order_items = $this->event_data->get_woo_order_quantities( $order );

		self::assertEquals( 2, $valid_order_items['total'] );
		self::assertEquals( 2, $valid_order_items['tickets'][ $ticket_id ] );
		self::assertCount( 1, $valid_order_items['tickets'] );
		self::assertEquals( 1, $valid_order_items['events_per_order'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_woo_ticket_quantities_from_order_with_multiple_events() {
		$event_id    = $this->factory()->event->create();
		$event_id_2  = $this->factory()->event->create();
		$ticket_id   = $this->create_woocommerce_ticket( $event_id, 5 );
		$ticket_id_2 = $this->create_woocommerce_ticket( $event_id_2, 2 );
		$order_id    = $this->create_woocommerce_order( $ticket_id, 4, [ 'products' => [ [ 'product_id' => $ticket_id_2, 'quantity' => 3, ] ], 'status' => 'completed' ] );
		$order       = new WC_Order( $order_id );

		// Get Order Quantities
		$valid_order_items = $this->event_data->get_woo_order_quantities( $order );

		self::assertEquals( 7, $valid_order_items['total'] );
		self::assertEquals( 4, $valid_order_items['tickets'][ $ticket_id ] );
		self::assertEquals( 3, $valid_order_items['tickets'][ $ticket_id_2 ] );
		self::assertCount( 2, $valid_order_items['tickets'] );
		self::assertEquals( 2, $valid_order_items['events_per_order'] );
	}

}
