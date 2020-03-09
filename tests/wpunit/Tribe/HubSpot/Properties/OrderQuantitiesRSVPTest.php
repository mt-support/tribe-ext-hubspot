<?php

namespace Tribe\HubSpot\Properties;

use Codeception\TestCase\WPTestCase;
use ReflectionClass;
use Tribe\Events\Test\Factories\Event;
use Tribe\HubSpot\Subscribe\RSVP as RSVP_Subscribe;
use Tribe\Tickets\Test\Commerce\Attendee_Maker;
use Tribe\Tickets\Test\Commerce\RSVP\Ticket_Maker as Ticket_Maker;
use Tribe__Tickets__RSVP as Main_RSVP;
use Tribe__Tickets__Tickets_View as Tickets_View;
use Tribe\HubSpot\Properties\Event_Data;

class OrderQuantitiesRSVPTest extends WPTestCase {

	use Attendee_Maker;
	use Ticket_Maker;

	public function setUp() {
		// before
		parent::setUp();

		$this->tickets_view = new Tickets_View();

		$this->factory()->event = new Event();

		$this->rsvp_subscribe = new RSVP_Subscribe();

		$this->event_data = new Event_Data();

		// let's avoid die()s
		add_filter( 'tribe_exit', function () {
			return [ $this, 'dont_die' ];
		} );

		// let's avoid confirmation emails
		add_filter( 'tribe_tickets_rsvp_send_mail', '__return_false' );
	}

	public function tearDown() {
		// your tear down methods here
		// then
		parent::tearDown();
	}

	/**
	 * @test
	 */
	public function it_should_return_rsvp_ticket_quantities_from_order() {
		$event_id  = $this->factory()->event->create();
		$ticket_id = $this->create_rsvp_ticket( $event_id );
		$sut       = $this->make_instance();
		$sut->generate_tickets_for( $ticket_id, 3, $this->fake_attendee_details( [ 'order_status' => 'yes' ] ) );
		$test_attendees = $sut->get_attendees_array( $event_id );
		$test_attendee  = current( $test_attendees );
		$related_data   = $this->rsvp_subscribe->get_rsvp_related_data_by_attendee_id( $test_attendee['attendee_id'] );

		// Get Order Quantities
		$valid_order_items = $this->event_data->get_rsvp_order_quantities( $related_data['order'] );

		var_dump( $related_data['order'] );

		self::assertEquals( 3, $valid_order_items['total'] );
		self::assertEquals( 3, $valid_order_items['tickets'][ $ticket_id ] );
		self::assertCount( 1, $valid_order_items['tickets'] );
		self::assertEquals( 1, $valid_order_items['events_per_order'] );
	}

	private function make_instance() {
		/** @var Main_RSVP $instance */
		$instance = ( new ReflectionClass( Main_RSVP::class ) )->newInstanceWithoutConstructor();
		$instance->set_tickets_view( $this->tickets_view );

		return $instance;
	}

	protected function fake_attendee_details( array $overrides = [] ) {
		return array_merge( [
			'full_name'    => 'Jane Doe',
			'email'        => 'jane@doe.com',
			'order_status' => 'yes',
			'optout'       => 'no',
			'order_id'     => Main_RSVP::generate_order_id(),
		], $overrides );
	}
}
