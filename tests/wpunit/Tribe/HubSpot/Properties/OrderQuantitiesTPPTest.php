<?php

namespace Tribe\HubSpot\Properties;

use Codeception\TestCase\WPTestCase;
use ReflectionClass;
use Tribe\Events\Test\Factories\Event;
use Tribe\Tickets\Test\Commerce\PayPal\Ticket_Maker as Ticket_Maker;
use Tribe\Tickets\Test\Commerce\PayPal\Order_Maker as Order_Maker;
use Tribe__Tickets__Commerce__PayPal__Main as Main_TPP;
use Tribe__Tickets__Commerce__PayPal__Order;
use Tribe__Tickets__Commerce__PayPal__Tickets_View as Tickets_View;
use Tribe\HubSpot\Properties\Event_Data;

class OrderQuantitiesTPPTest extends WPTestCase {

	use Ticket_Maker;
	use Order_Maker;

	public function setUp() {
		// before
		parent::setUp();

		$this->factory()->event = new Event();

		$this->event_data = new Event_Data();

		// your set up methods here
		$this->tickets_view = new Tickets_View();

		// let's avoid die()s
		add_filter( 'tribe_exit', function () {
			return [ $this, 'dont_die' ];
		} );

		// Enable Tribe Commerce.
		add_filter( 'tribe_tickets_commerce_paypal_is_active', '__return_true' );
		add_filter( 'tribe_tickets_get_modules', function ( $modules ) {
			$modules['Tribe__Tickets__Commerce__PayPal__Main'] = tribe( 'tickets.commerce.paypal' )->plugin_name;

			return $modules;
		} );
	}

	public function tearDown() {
		// your tear down methods here
		// then
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function it_should_return_tpp_ticket_quantities_from_order() {
		$event_id  = $this->factory()->event->create();
		$ticket_id = $this->create_paypal_ticket( $event_id, 1, [
			'meta_input' => [
				'total_sales' => 0,
				'_stock'      => 10,
				'_capacity'   => 10,
			],
		] );
		$generated = $this->create_paypal_orders( $event_id, $ticket_id, 2, 1 );
		$order     = Tribe__Tickets__Commerce__PayPal__Order::from_order_id( $generated[0]['Order ID'] );

		// Get Order Quantities
		$valid_order_items = $this->event_data->get_tpp_order_quantities( $order );

		self::assertEquals( 2, $valid_order_items['total'] );
		self::assertEquals( 2, $valid_order_items['tickets'][ $ticket_id ] );
		self::assertCount( 1, $valid_order_items['tickets'] );
		self::assertEquals( 1, $valid_order_items['events_per_order'] );
	}

	private function make_instance() {
		/** @var Main_TPP $instance */
		$instance = ( new ReflectionClass( Main_TPP::class ) )->newInstanceWithoutConstructor();
		$instance->set_tickets_view( $this->tickets_view );

		return $instance;
	}

}
