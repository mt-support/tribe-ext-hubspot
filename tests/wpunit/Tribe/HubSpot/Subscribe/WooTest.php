<?php

namespace Tribe\HubSpot\Subscribe;

use Tribe\Events\Test\Factories\Event;
use Tribe\Tickets_Plus\Test\Commerce\WooCommerce\Order_Maker;
use Tribe\Tickets_Plus\Test\Commerce\WooCommerce\Ticket_Maker;
use Tribe__Tickets_Plus__Commerce__WooCommerce__Main as Main_Woo;
use Tribe\HubSpot\Subscribe\Woo as Woo_Subscribe;
use Tribe\HubSpot\Properties\Event_Data as Data;

class WooTest extends \Codeception\TestCase\WPTestCase {

	use Ticket_Maker;
	use Order_Maker;

	public function setUp() {
		// before
		parent::setUp();

		$this->factory()->event = new Event();
	}

	public function tearDown() {
		// your tear down methods here
		// then
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function it_should_return_initial_properties_array_from_attendee_data() {
		$woo_subscribe = new Woo_Subscribe();

		$event_id = $this->factory()->event->create();
		$ticket_id   = $this->create_woocommerce_ticket( $event_id, 5 );
		$order_id = $this->create_woocommerce_order( $ticket_id, 2, [ 'status' => 'completed' ] );
		$order    = new \WC_Order( $order_id );

		// Getting Initial Properties Array that is sent to HubSpot
		$attendee_data = $woo_subscribe->get_woo_contact_data_from_order( $order );
		$properties    = $woo_subscribe->get_initial_properties_array( $attendee_data );

		self::assertEquals( 'firstname', $properties[0]['property'] );
		self::assertEquals( $attendee_data['first_name'], $properties[0]['value'] );
		self::assertEquals( 'lastname', $properties[1]['property'] );
		self::assertEquals( $attendee_data['last_name'], $properties[1]['value'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_order_data_array_from_attendee_data() {
		$data          = new Data();
		$woo_subscribe = new Woo_Subscribe();

		$event_id = $this->factory()->event->create();
		$ticket_id   = $this->create_woocommerce_ticket( $event_id, 5 );
		$order_id = $this->create_woocommerce_order( $ticket_id, 2, [ 'status' => 'completed' ] );
		$order    = new \WC_Order( $order_id );

		// Return Array of Order Data that is used to send to HubSpot
		$qty           = $data->get_woo_order_quantities( $order );
		$attendee_data = $woo_subscribe->get_woo_contact_data_from_order( $order );
		$order_data    = $woo_subscribe->get_order_data_array( $attendee_data, $qty, 'register' );

		self::assertEquals( $attendee_data['date'], $order_data['order_date'] );
		self::assertEquals( $attendee_data['total'], $order_data['order_total'] );
		self::assertEquals( $qty['total'], $order_data['order_ticket_quantity'] );
		self::assertEquals( count( $qty['tickets'] ), $order_data['order_ticket_type_quantity'] );
		self::assertEquals( $qty['events_per_order'], $order_data['events_per_order'] );
		self::assertEquals( 'register', $order_data['aggregate_type'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_extra_data() {
		$woo_subscribe = new Woo_Subscribe();

		$event_id = $this->factory()->event->create();
		$ticket_id   = $this->create_woocommerce_ticket( $event_id, 5 );
		$order_id = $this->create_woocommerce_order( $ticket_id, 2, [ 'status' => 'completed' ] );
		$order    = new \WC_Order( $order_id );

		// Get the array of Extra Data for HubSpot Timeline Events
		$attendee_data = $woo_subscribe->get_woo_contact_data_from_order( $order );
		$attendee_id   = $woo_subscribe->get_first_attendee_id_from_order( $order_id, 'woo' );
		$extra_data    = $woo_subscribe->get_extra_data( $event_id, $ticket_id, $attendee_id, 'woo', $attendee_data['name'] );

		self::assertEquals( $event_id, $extra_data['event']['event_id'] );
		self::assertEquals( get_the_title( $event_id ), $extra_data['event']['event_name'] );
		self::assertEquals( $ticket_id, $extra_data['ticket']['ticket_type_id'] );
		self::assertEquals( get_the_title( $ticket_id ), $extra_data['ticket']['ticket_type'] );
		self::assertEquals( $attendee_id, $extra_data['ticket']['ticket_attendee_id'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_first_attendee_id_from_order() {
		$main_woo      = new Main_Woo();
		$woo_subscribe = new Woo_Subscribe();

		$event_id = $this->factory()->event->create();
		$ticket_id   = $this->create_woocommerce_ticket( $event_id, 5 );
		$order_id = $this->create_woocommerce_order( $ticket_id, 2, [ 'status' => 'completed' ] );

		// Get the First Attendee
		$test_attendees = $main_woo->get_attendees_array( $event_id );
		$test_attendee  = current( $test_attendees );
		$attendee_id    = $woo_subscribe->get_first_attendee_id_from_order( $order_id, 'woo' );


		self::assertEquals( $test_attendee['attendee_id'], $attendee_id );
	}

	/**
	 * @test
	 */
	public function it_should_return_related_order_data_with_attendee_id() {
		$main_woo      = new Main_Woo();
		$woo_subscribe = new Woo_Subscribe();

		$event_id = $this->factory()->event->create();
		$ticket_id   = $this->create_woocommerce_ticket( $event_id, 5 );
		$order_id = $this->create_woocommerce_order( $ticket_id, 2, [ 'status' => 'completed' ] );
		$order    = new \WC_Order( $order_id );

		// Get Related Data by Attendee ID
		$test_attendees = $main_woo->get_attendees_array( $event_id );
		$test_attendee  = current( $test_attendees );
		$related_data   = $woo_subscribe->get_woo_related_data_by_attendee_id( $test_attendee['attendee_id'] );

		self::assertEquals( $order_id, $related_data['order_id'] );
		self::assertEquals( $order, $related_data['order'] );
		self::assertEquals( $event_id, $related_data['post_id'] );
		self::assertEquals( $ticket_id, $related_data['ticket_id'] );
	}
}
