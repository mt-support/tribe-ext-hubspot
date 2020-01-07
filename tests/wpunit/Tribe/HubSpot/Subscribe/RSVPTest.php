<?php

namespace Tribe\HubSpot\Subscribe;

use Codeception\TestCase\WPTestCase;
use ReflectionClass;
use Tribe\Tickets\Test\Commerce\Attendee_Maker;
use Tribe\Tickets\Test\Commerce\RSVP\Ticket_Maker as RSVP_Ticket_Maker;
use Tribe__Tickets__RSVP as Main_RSVP;
use Tribe__Tickets__Tickets_View as Tickets_View;
use Tribe\HubSpot\Subscribe\RSVP as RSVP_Subscribe;
use Tribe\HubSpot\Properties\Event_Data as Data;

class RsvpTest extends WPTestCase {

	use Attendee_Maker;
	use RSVP_Ticket_Maker;

	public function setUp() {
		// before
		parent::setUp();

		$this->tickets_view = new Tickets_View();

		$this->rsvp_subscribe = new RSVP_Subscribe();

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
	public function it_should_return_initial_properties_array_from_attendee_data() {
		$base_data     = $this->make_base_data();
		$test_attendee = $base_data['test_attendee'];

		// Getting Initial Properties Array that is sent to HubSpot
		$related_data  = $this->rsvp_subscribe->get_rsvp_related_data_by_attendee_id( $test_attendee['attendee_id'] );
		$attendee_data = $this->rsvp_subscribe->get_rsvp_contact_data_from_order( $related_data['order'] );
		$properties    = $this->rsvp_subscribe->get_initial_properties_array( $attendee_data );

		self::assertEquals( 'firstname', $properties[0]['property'] );
		self::assertEquals( $attendee_data['first_name'], $properties[0]['value'] );
		self::assertEquals( 'lastname', $properties[1]['property'] );
		self::assertEquals( $attendee_data['last_name'], $properties[1]['value'] );
	}

	protected function make_base_data() {
		$post_id   = $this->factory->post->create();
		$ticket_id = $this->create_rsvp_ticket( $post_id );

		$sut = $this->make_instance();
		$sut->generate_tickets_for( $ticket_id, 1, $this->fake_attendee_details( [ 'order_status' => 'yes' ] ) );
		$test_attendees = $sut->get_attendees_array( $post_id );
		$test_attendee  = current( $test_attendees );

		return [
			'post_id'       => $post_id,
			'ticket_id'     => $ticket_id,
			'test_attendee' => $test_attendee,
		];
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

	/**
	 * @test
	 */
	public function it_should_return_order_data_array_from_attendee_data() {
		$data = new Data();

		$base_data     = $this->make_base_data();
		$test_attendee = $base_data['test_attendee'];

		// Return Array of Order Data that is used to send to HubSpot
		$related_data  = $this->rsvp_subscribe->get_rsvp_related_data_by_attendee_id( $test_attendee['attendee_id'] );
		$qty           = $data->get_rsvp_order_quantities( $related_data['order'] );
		$attendee_data = $this->rsvp_subscribe->get_rsvp_contact_data_from_order( $related_data['order'] );
		$order_data    = $this->rsvp_subscribe->get_order_data_array( $attendee_data, $qty, 'register' );

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
		$base_data     = $this->make_base_data();
		$post_id       = $base_data['post_id'];
		$ticket_id     = $base_data['ticket_id'];
		$test_attendee = $base_data['test_attendee'];

		// Get the array of Extra Data for HubSpot Timeline Events
		$related_data  = $this->rsvp_subscribe->get_rsvp_related_data_by_attendee_id( $test_attendee['attendee_id'] );
		$attendee_data = $this->rsvp_subscribe->get_rsvp_contact_data_from_order( $related_data['order'] );
		$attendee_id   = $this->rsvp_subscribe->get_first_attendee_id_from_order( $related_data['order_id'], 'rsvp' );
		$extra_data    = $this->rsvp_subscribe->get_extra_data( $post_id, $ticket_id, $attendee_id, 'rsvp', $attendee_data['name'] );

		self::assertEquals( $post_id, $extra_data['event']['event_id'] );
		self::assertEquals( get_the_title( $post_id ), $extra_data['event']['event_name'] );
		self::assertEquals( $ticket_id, $extra_data['ticket']['ticket_type_id'] );
		self::assertEquals( get_the_title( $ticket_id ), $extra_data['ticket']['ticket_type'] );
		self::assertEquals( $attendee_id, $extra_data['ticket']['ticket_attendee_id'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_first_attendee_id_from_order() {
		$Main_RSVP = new Main_RSVP();

		$base_data     = $this->make_base_data();
		$post_id       = $base_data['post_id'];
		$test_attendee = $base_data['test_attendee'];

		// Get the First Attendee
		$related_data   = $this->rsvp_subscribe->get_rsvp_related_data_by_attendee_id( $test_attendee['attendee_id'] );
		$test_attendees = $Main_RSVP->get_attendees_array( $post_id );
		$test_attendee  = current( $test_attendees );
		$attendee_id    = $this->rsvp_subscribe->get_first_attendee_id_from_order( $related_data['order_id'], 'rsvp' );


		self::assertEquals( $test_attendee['attendee_id'], $attendee_id );
	}

	/**
	 * @test
	 */
	public function it_should_spilt_full_name() {

		// split_name( $string )
		$full_name_1 = 'HubSpot McHubspotty';
		$full_name_2 = 'HubSpot Tribe McHubspotty';

		$names_1 = $this->rsvp_subscribe->split_name( $full_name_1 );
		$names_2 = $this->rsvp_subscribe->split_name( $full_name_2 );

		self::assertEquals( 'HubSpot', $names_1['first_name'] );
		self::assertEquals( 'McHubspotty', $names_1['last_name'] );
		self::assertEquals( '', $names_1['middle_name'] );
		self::assertEquals( 'HubSpot', $names_2['first_name'] );
		self::assertEquals( 'McHubspotty', $names_2['last_name'] );
		self::assertEquals( 'Tribe', $names_2['middle_name'] );
	}

	/**
	 * @test
	 */
	public function it_should_return_related_order_data_with_attendee_id() {
		$base_data     = $this->make_base_data();
		$post_id       = $base_data['post_id'];
		$ticket_id     = $base_data['ticket_id'];
		$test_attendee = $base_data['test_attendee'];

		// Get Related Data by Attendee ID
		$related_data = $this->rsvp_subscribe->get_rsvp_related_data_by_attendee_id( $test_attendee['attendee_id'] );

		self::assertEquals( $post_id, $related_data['post_id'] );
		self::assertEquals( $ticket_id, $related_data['ticket_id'] );
	}
}
