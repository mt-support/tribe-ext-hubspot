<?php

namespace Tribe\HubSpot\Subscribe;

use Tribe\Events\Test\Factories\Event;
use Tribe\Tickets_Plus\Test\Commerce\EDD\Ticket_Maker as EDD_Ticket_Maker;
use Tribe\Tickets_Plus\Test\Commerce\EDD\Order_Maker as EDD_Order_Maker;
use Tribe__Tickets_Plus__Commerce__EDD__Main as Main_EDD;
use Tribe\HubSpot\Subscribe\EDD as EDD_Subscribe;
use Tribe\HubSpot\Properties\Event_Data as Data;

class EDDTest extends \Codeception\TestCase\WPTestCase {

	use EDD_Ticket_Maker;
	use EDD_Order_Maker;

	public function setUp() {
		// before
		parent::setUp();

		$this->factory()->event = new Event();
		
		$this->edd_subscribe = new EDD_Subscribe();
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
		$base_data = $this->make_base_data();
		$order_id  = $base_data['order_id'];
		$order     = $base_data['order'];

		// Getting Initial Properties Array that is sent to HubSpot
		$attendee_data = $this->edd_subscribe->get_edd_contact_data_from_order( $order, $order_id );
		$properties    = $this->edd_subscribe->get_initial_properties_array( $attendee_data );

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
		
		$base_data = $this->make_base_data();
		$order_id  = $base_data['order_id'];
		$order     = $base_data['order'];

		// Return Array of Order Data that is used to send to HubSpot
		$qty           = $data->get_edd_order_quantities( $order );
		$attendee_data = $this->edd_subscribe->get_edd_contact_data_from_order( $order, $order_id );
		$order_data    = $this->edd_subscribe->get_order_data_array( $attendee_data, $qty, 'register' );

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
		$base_data = $this->make_base_data();
		$event_id  = $base_data['event_id'];
		$ticket_id = $base_data['ticket_id'];
		$order_id  = $base_data['order_id'];
		$order     = $base_data['order'];

		// Get the array of Extra Data for HubSpot Timeline Events
		$attendee_data = $this->edd_subscribe->get_edd_contact_data_from_order( $order, $order_id );
		$attendee_id   = $this->edd_subscribe->get_first_attendee_id_from_order( $order_id, 'edd' );
		$extra_data    = $this->edd_subscribe->get_extra_data( $event_id, $ticket_id, $attendee_id, 'edd', $attendee_data['name'] );

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
		$Main_EDD      = new Main_EDD();
		
		$base_data = $this->make_base_data();
		$event_id  = $base_data['event_id'];
		$order_id  = $base_data['order_id'];

		// Get the First Attendee
		$test_attendees = $Main_EDD->get_attendees_array( $event_id );
		$test_attendee  = current( $test_attendees );
		$attendee_id    = $this->edd_subscribe->get_first_attendee_id_from_order( $order_id, 'edd' );


		self::assertEquals( $test_attendee['attendee_id'], $attendee_id );
	}

	/**
	 * @test
	 */
	public function it_should_return_related_order_data_with_attendee_id() {
		$Main_EDD      = new Main_EDD();
		
		$base_data = $this->make_base_data();
		$event_id  = $base_data['event_id'];
		$ticket_id = $base_data['ticket_id'];
		$order_id  = $base_data['order_id'];
		$order     = $base_data['order'];

		// Get Related Data by Attendee ID
		$test_attendees = $Main_EDD->get_attendees_array( $event_id );
		$test_attendee  = current( $test_attendees );
		$related_data   = $this->edd_subscribe->get_edd_related_data_by_attendee_id( $test_attendee['attendee_id'] );

		self::assertEquals( $order_id, $related_data['order_id'] );
		self::assertEquals( $order, $related_data['order'] );
		self::assertEquals( $event_id, $related_data['post_id'] );
		self::assertEquals( $ticket_id, $related_data['ticket_id'] );
	}
	
	protected function make_base_data() {
		$event_id = $this->factory()->event->create();
		$ticket_id   = $this->create_edd_ticket( $event_id, 5 );
		$order_id = $this->create_edd_order( $ticket_id, 2, [ 'status' => 'completed' ] );
		$order    = edd_get_payment( $order_id );

		return [
			'event_id'  => $event_id,
			'ticket_id' => $ticket_id,
			'order_id'  => $order_id,
			'order'     => $order,
		];
	}
}
